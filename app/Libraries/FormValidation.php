<?php

namespace App\Libraries;

use CodeIgniter\Validation\Validation;

/**
 * Form Validation proxy
 *
 * Exposes the CI3 form_validation API (set_rules, run, error, ...) backed by
 * CodeIgniter 4's Validation service.
 */
class FormValidation
{
    protected Validation $validation;

    public function __construct()
    {
        $this->validation = service('validation');
    }

    public function set_rules($field, $label = '', $rules = [], $errors = [])
    {
        if (is_array($field)) {
            $this->validation->setRules($field);
            return $this;
        }
        $this->validation->setRule($field, $label, $rules, $errors);
        return $this;
    }

    public function set_rules_array(array $rules)
    {
        $this->validation->setRules($rules);
        return $this;
    }

    public function set_data($data = [])
    {
        $this->validation->setData($data);
        return $this;
    }

    public function run($group = null, $db_group = null)
    {
        // If no explicit data was set, validate against the combined request input.
        $request = service('request');
        $rules   = $this->validation->getRules();

        if ($group === null && is_array($rules) && $rules !== []) {
            return $this->validation->withRequest($request)->run();
        }

        return $this->validation->withRequest($request)->run($group);
    }

    public function error($field = '', $prefix = '', $suffix = '')
    {
        $msg = $this->validation->getError($field);
        if ($msg === null || $msg === '') {
            return '';
        }
        return $prefix . $msg . $suffix;
    }

    public function error_string($prefix = '', $suffix = '')
    {
        $errors = $this->validation->getErrors();
        if ($errors === []) {
            return '';
        }
        $str = $prefix;
        foreach ($errors as $e) {
            $str .= $e . $suffix;
        }
        return $str;
    }

    public function set_message($lang, $val = '')
    {
        if (is_array($lang)) {
            $this->validation->setRule("__msg", '', 'required'); // no-op; custom messages handled per-rule
            $this->validation->setRuleMessages($lang);
        }
        return $this;
    }

    public function set_error_delimiters($prefix = '<p>', $suffix = '</p>')
    {
        $this->validation->setErrorDelimiters($prefix, $suffix);
        return $this;
    }

    public function __call($method, $args)
    {
        if (method_exists($this->validation, $method)) {
            return $this->validation->{$method}(...$args);
        }
        throw new \BadMethodCallException('Undefined method FormValidation::' . $method);
    }
}
