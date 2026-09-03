<?php

if (!function_exists('base_path')) {
    function base_path()
    {
        return ROOTPATH;
    }
}

if (!function_exists('validation_errors')) {
    function validation_errors($prefix = '', $suffix = '')
    {
        $validation = service('validation');
        $errors = $validation->getErrors();
        if ($errors === []) {
            return '';
        }
        $str = $prefix;
        foreach ($errors as $error) {
            $str .= $error . $suffix;
        }
        return $str;
    }
}

if (!function_exists('form_error')) {
    function form_error($field = '', $prefix = '', $suffix = '')
    {
        $validation = service('validation');
        $error = $validation->getError($field);
        if ($error === null || $error === '') {
            return '';
        }
        return $prefix . $error . $suffix;
    }
}

if (!function_exists('set_value')) {
    function set_value($field = '', $default = '')
    {
        $request = service('request');
        $value = $request->getPost($field);
        if ($value === null) {
            $value = $request->getGet($field);
        }
        return $value ?? $default;
    }
}

if (!function_exists('set_select')) {
    function set_select($field = '', $value = '', $default = false)
    {
        if (set_value($field, (string) ($default ? $value : '')) === (string) $value) {
            return ' selected="selected"';
        }
        return '';
    }
}

if (!function_exists('set_checkbox')) {
    function set_checkbox($field = '', $value = '', $default = false)
    {
        if (set_value($field, (string) ($default ? $value : '')) === (string) $value) {
            return ' checked="checked"';
        }
        return '';
    }
}

if (!function_exists('set_radio')) {
    function set_radio($field = '', $value = '', $default = false)
    {
        if (set_value($field, (string) ($default ? $value : '')) === (string) $value) {
            return ' checked="checked"';
        }
        return '';
    }
}

if (!function_exists('image_url')) {
    function image_url($path = '')
    {
        if ($path === false || $path === null) {
            $path = '';
        }

        if ($path === '') {
            return base_url('images') . '/';
        }

        return base_url('images/' . ltrim($path, '/'));
    }
}

if (!function_exists('base_url')) {
    function base_url($relativePath = '', ?string $scheme = null): string
    {
        if ($relativePath === false || $relativePath === null) {
            $relativePath = '';
        }
        $currentURI = service('request')->getUri();
        if ($currentURI instanceof \CodeIgniter\HTTP\SiteURI) {
            return $currentURI->baseUrl($relativePath, $scheme);
        }
        return (string) $currentURI;
    }
}

