<?php

namespace App\Models;

use Config\ModelMap;
use App\Libraries\CiLoader;
use App\Libraries\Input;
use App\Libraries\CiDatabase;
use App\Libraries\FormValidation;

/**
 * Base Model
 *
 * Recreates the CI3 model environment ($this->db, $this->session, $this->load,
 * $this->config, $this->input) for the ported application models.
 */
class BaseModel
{
    /** @var \App\Libraries\CiDatabase */
    protected $db;

    /** @var \App\Libraries\CiDatabase Alias of $db (second connection) */
    protected $db1;

    /** @var \CodeIgniter\Session\Session */
    protected $session;

    /** @var object */
    protected $config;

    /** @var \App\Libraries\CiLoader */
    protected $load;

    /** @var \App\Libraries\Input */
    protected $input;

    /** @var \CodeIgniter\Validation\Validation */
    protected $form_validation;

    public function __construct()
    {
        $connection             = \Config\Database::connect();
        $this->db               = new CiDatabase($connection);
        $this->db1              = $this->db;
        $this->session          = session();
        $this->config           = config(\Config\App::class);
        $this->load             = new CiLoader($this);
        $this->input            = new Input();
        $this->form_validation  = new FormValidation();
    }

    public function __get($name)
    {
        $lower = strtolower($name);

        if ($this->load instanceof CiLoader) {
            $m = $this->load->get_model($lower);
            if ($m !== null) {
                return $m;
            }
        }

        ModelMap::init();
        if (isset(ModelMap::$byName[$lower])) {
            $fqcn = ModelMap::$byName[$lower];
            $instance = new $fqcn();
            if ($this->load instanceof CiLoader) {
                $this->load->model($fqcn);
            }
            $this->{$name} = $instance;
            return $instance;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property: ' . static::class . '::$' . $name .
            ' in ' . ($trace[0]['file'] ?? '') . ' on line ' . ($trace[0]['line'] ?? '') .
            ' (use $this->load->model() to load it)',
            E_USER_NOTICE
        );
        return null;
    }
}
