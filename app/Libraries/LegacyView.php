<?php

namespace App\Libraries;

use CodeIgniter\View\View;
use Config\View as ViewConfig;
use Config\ModelMap;
use CodeIgniter\Autoloader\FileLocator;
use Psr\Log\LoggerInterface;

/**
 * LegacyView
 *
 * Extends the CodeIgniter 4 View renderer to also expose CI3-style instance
 * properties ($this->session, $this->input, $this->load) and lazy model access
 * ($this->ModelName) inside view templates, which the ported application views
 * rely on.
 */
class LegacyView extends View
{
    public $session;
    public $input;
    public $load;

    /** @var array models loaded by short name */
    protected $loaded_models = [];

    public function __construct(
        ViewConfig $config,
        ?string $viewPath = null,
        ?FileLocator $loader = null,
        ?bool $debug = null,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($config, $viewPath, $loader, $debug, $logger);

        $this->session = session();
        $this->input   = new Input();
        $this->load    = new CiLoader($this);
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
            if (isset($this->loaded_models[$lower])) {
                return $this->loaded_models[$lower];
            }
            $fqcn = ModelMap::$byName[$lower];
            $instance = new $fqcn();
            $this->loaded_models[$lower] = $instance;
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
