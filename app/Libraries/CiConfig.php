<?php

namespace App\Libraries;

/**
 * CiConfig - provides CI3-style $this->config->item('key') access backed by
 * the CodeIgniter 4 App config plus a few computed legacy entries.
 */
class CiConfig
{
    protected $app;
    protected $items;

    public function __construct()
    {
        $this->app = config(\Config\App::class);
        $this->items = [
            'base_path' => ROOTPATH,
            'base_url'  => rtrim($this->app->baseURL, '/') . '/',
            'index_page' => '',
        ];
    }

    public function item($key = '')
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }
        // fall back to an App config public property
        if (property_exists($this->app, $key)) {
            return $this->app->{$key};
        }
        return null;
    }

    public function set_item($key, $value)
    {
        $this->items[$key] = $value;
    }

    public function __get($key)
    {
        return $this->item($key);
    }
}
