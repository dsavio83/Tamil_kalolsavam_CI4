<?php

namespace App\Libraries;

use Config\ModelMap;

/**
 * Loader facade
 *
 * Mimics the CI3 $this->load->... API used by the application controllers and
 * models: model(), library(), helper(), view(), config(), database(), dbforge().
 */
class CiLoader
{
    protected $controller;

    /** @var array<string, object> loaded models by lowercase short name */
    protected $models = [];

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function model($model, $name = null, $db_conn = false)
    {
        if (is_array($model)) {
            $this->model($model[0], isset($model[1]) ? $model[1] : null);
            return $this;
        }

        $fqcn = $this->resolveModel($model);
        if ($fqcn === null) {
            throw new \RuntimeException("Model not mapped: {$model}");
        }

        $short = $name ?: basename(str_replace('\\', '/', (string) $model));

        if (!isset($this->models[strtolower($short)])) {
            $instance = new $fqcn();
            $this->models[strtolower($short)] = $instance;
            $this->controller->{$short} = $instance;
        } else {
            $this->controller->{$short} = $this->models[strtolower($short)];
        }

        return $this;
    }

    public function get_model($short)
    {
        return $this->models[strtolower($short)] ?? null;
    }

    protected function resolveModel($model)
    {
        $key = strtolower(str_replace('\\', '/', (string) $model));
        ModelMap::init();
        if (isset(ModelMap::$byPath[$key])) {
            return ModelMap::$byPath[$key];
        }
        // trim dirs to basename
        $base = strtolower(basename($key));
        if (isset(ModelMap::$byName[$base])) {
            return ModelMap::$byName[$base];
        }
        // try direct autoload of basename without namespace dirs
        if (class_exists('App\\Models\\' . $key, false)) {
            return 'App\\Models\\' . $key;
        }
        return null;
    }

    public function library($library, $params = null, $object_name = null)
    {
        if (is_array($library)) {
            foreach ($library as $lib) {
                $this->library($lib, $params, $object_name);
            }
            return $this;
        }

        $name = strtolower((string) $library);

        // Native CI4 services / special cases
        switch ($name) {
            case 'session':
                $this->controller->session = session();
                return $this;
            case 'form_validation':
            case 'validation':
                $this->controller->form_validation = service('validation');
                return $this;
            case 'database':
            case 'db':
                $this->controller->db = \Config\Database::connect();
                return $this;
            case 'template':
                $this->controller->template = \Config\Services::template();
                return $this;
            case 'email':
                $this->controller->email = new \CodeIgniter\Email\Email();
                return $this;
            case 'table':
                $this->controller->table = new \CodeIgniter\View\Table();
                return $this;
            default:
                break;
        }

        // Map to a class
        $map = $this->libraryMap();
        $base = basename(str_replace('\\', '/', (string) $library));
        $short = $object_name ?: $base;

        if (isset($map[$name])) {
            // Language-specific global-class libraries
            if (isset($map[$name]['global']) && $map[$name]['global']) {
                if (class_exists($map[$name]['class'])) {
                    $this->controller->{$short} = new $map[$name]['class']();
                    return $this;
                }
                // fall through to require via classmap
            } else {
                $class = $map[$name]['class'];
                $this->controller->{$short} = new $class();
                return $this;
            }
        }

        // Try autoload: App\Libraries\<Name>
        $candidate = 'App\\Libraries\\' . ucfirst($base);
        if (class_exists($candidate)) {
            $this->controller->{$short} = new $candidate($params);
            return $this;
        }

        // Legacy libraries that are used only for their global class side-effects
        // (HTML2PDF, FPDF, etc.). Ensure the class is loadable via the classmap.
        if (in_array($name, ['html2pdf', 'fpdf', '_fpdf/fpdf', 'csvreader', 'excel_reader', 'timezone'], true)) {
            $global = $this->globalClassName($name);
            if ($global !== null && class_exists($global)) {
                if (in_array($name, ['fpdf', '_fpdf/fpdf'], true)) {
                    $this->controller->{$short} = new $global();
                }
                return $this;
            }
            return $this;
        }

        return $this;
    }

    protected function globalClassName($name): ?string
    {
        switch ($name) {
            case 'html2pdf':
            case '_html2pdf':
                return 'HTML2PDF';
            case 'fpdf':
            case '_fpdf/fpdf':
                return 'Fpdf';
            case 'csvreader':
                return 'Csvreader';
            case 'excel_reader':
                return 'Excel_reader';
            case 'timezone':
                return 'CI_timezone';
        }
        return null;
    }

    protected function libraryMap(): array
    {
        return [
            'csvreader'  => ['class' => \Csvreader::class, 'global' => true],
            'upload'     => ['class' => \App\Libraries\Upload::class],
            'image_lib'  => ['class' => \App\Libraries\Image_lib::class],
            'pagination' => ['class' => \App\Libraries\Pagination::class],
            'zip'        => ['class' => \App\Libraries\Zip::class],
        ];
    }

    public function helper($helpers)
    {
        if (is_array($helpers)) {
            foreach ($helpers as $helper) {
                $this->helper($helper);
            }
            return $this;
        }

        $name = strtolower((string) $helpers);
        // normalize CI3 paths e.g. 'csv/csv' or 'csv_helper/csv' -> 'csv'
        $base = basename(str_replace('\\', '/', (string) $helpers));
        $base = strtolower($base);

        if (function_exists('helper') && $base !== 'url') {
            helper($base);
            return $this;
        }

        return $this;
    }

    public function view($view, $vars = null, $return = false, $layout = null)
    {
        $data = is_array($vars) ? $vars : [];

        if (function_exists('view')) {
            $rendered = view($view, $data, ['saveData' => false]);
        } else {
            $rendered = '';
        }

        if ($return) {
            return $rendered;
        }
        echo $rendered;
        return $this;
    }

    public function config($file = '')
    {
        // CI3 returns config object; controllers often call $this->load->config(...)
        // or $this->config->item('key'). Provide the App config via 'config()' helper.
        $this->controller->config = config(\Config\App::class);
        return $this;
    }

    public function database($params = '', $return = false, $query_builder = null)
    {
        $this->controller->db = \Config\Database::connect();
        if ($return) {
            return $this->controller->db;
        }
        return $this;
    }

    public function db($params = '', $active_record = null)
    {
        $this->controller->db = \Config\Database::connect();
        return $this;
    }

    public function dbforge()
    {
        $this->controller->dbforge = \Config\Database::forge();
        return $this;
    }

    public function driver($library, $params = null, $object_name = null)
    {
        return $this->library($library, $params, $object_name);
    }

    public function language($file = '')
    {
        return $this;
    }

    public function spark($class)
    {
        return $this;
    }
}
