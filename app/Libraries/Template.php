<?php

namespace App\Libraries;

/**
 * Template Library (ported from CI3 Colin Williams' CI_Template)
 *
 * Preserves the same public API used by the application controllers:
 * write(), write_view(), load(), render(), add_js(), add_css(), set_template()...
 */

class Template
{
    protected $regions = [
        '_scripts' => [],
        '_styles'  => [],
    ];

    protected $config;
    protected $template;
    protected $master;
    protected $output;
    protected $js = [];
    protected $css = [];
    protected $parser = 'parser';
    protected $parser_method = 'parse';
    protected $parse_template = false;

    public function __construct()
    {
        $configFile = APPPATH . 'Config/Template.php';
        if (is_file($configFile)) {
            $config = require $configFile;
        } else {
            // Default template configuration matching the CI3 template.php
            $config = [
                'active_template' => 'default',
                'default'         => [
                    'template'       => 'template',
                    'regions'        => [
                        'header', 'title', 'content', 'sidebar', 'footer', 'login_area',
                        'menu', 'error', 'message', 'on_load', 'left_panel',
                    ],
                    'parser'         => 'parser',
                    'parser_method'  => 'parse',
                    'parse_template' => false,
                ],
            ];
        }

        $this->config = $config;
        $this->set_template($config['active_template'] ?? 'default');
    }

    public function set_template($group)
    {
        if (isset($this->config[$group])) {
            $this->template = $this->config[$group];

            // Master template must exist
            $templateFile = APPVIEWPATH . ($this->template['template'] ?? 'template') . '.php';
            if (is_file($templateFile)) {
                $this->master = $this->template['template'];
            } else {
                $templateFileNoExt = APPVIEWPATH . ($this->template['template'] ?? 'template');
                if (is_file($templateFileNoExt)) {
                    $this->master = $this->template['template'];
                }
            }

            if (isset($this->template['regions'])) {
                $this->set_regions($this->template['regions']);
            }
        } else {
            $this->master = 'template';
        }
    }

    public function set_regions($regions)
    {
        if (count($regions)) {
            $this->regions = [
                '_scripts' => [],
                '_styles'  => [],
            ];
            foreach ($regions as $key => $region) {
                if (!is_array($region)) {
                    $this->add_region($region);
                } else {
                    $this->add_region($key, $region);
                }
            }
        }
    }

    public function add_region($name, $props = [])
    {
        if (!is_array($props)) {
            $props = [];
        }
        if (!isset($this->regions[$name])) {
            $this->regions[$name] = $props;
        }
    }

    public function write($region, $content, $overwrite = false)
    {
        if (isset($this->regions[$region])) {
            // Singleton layout components must never be duplicated into the DOM
            if ($overwrite === true || in_array($region, ['menu', 'left_panel', 'header'], true)) {
                $this->regions[$region]['content'] = [$content];
            } else {
                $this->regions[$region]['content'][] = $content;
            }
            return;
        }
        // Keep silent when region is undefined (matches loose app usage)
    }

    public function write_view($region, $view, $data = null, $overwrite = false)
    {
        $args = func_get_args();
        unset($args[0], $args[2], $args[3]);
        if (count($args) > 1) {
            foreach ($args as $suggestion) {
                if (is_string($suggestion) && $this->viewExists($suggestion)) {
                    $view = $suggestion;
                    break;
                }
            }
        }
        $viewData = is_array($data) ? $data : [];
        $content  = view($view, $viewData, ['saveData' => true]);
        $this->write($region, $content, $overwrite);
    }

    public function parse_view($region, $view, $data = null, $overwrite = false)
    {
        $viewData = is_array($data) ? $data : [];
        $content  = view($view, $viewData, ['saveData' => true], true);
        $this->write($region, $content, $overwrite);
    }

    public function add_js($script, $type = 'import', $defer = false)
    {
        $js = null;
        $realPath = (defined('FCPATH') ? FCPATH : '') . ltrim($script, '/\\');
        $v = (is_string($realPath) && is_file($realPath)) ? filemtime($realPath) : '20260905a';
        $filepath = base_url() . ltrim($script, '/\\') . '?v=' . $v;

        if ($type === 'import') {
            $js = '<script type="text/javascript" src="' . $filepath . '"';
            if ($defer) {
                $js .= ' defer="defer"';
            }
            $js .= "></script>";
        } elseif ($type === 'embed') {
            $js = '<script type="text/javascript"';
            if ($defer) {
                $js .= ' defer="defer"';
            }
            $js .= ">";
            $js .= $script;
            $js .= '</script>';
        }

        if ($js !== null && !in_array($js, $this->js, true)) {
            $this->js[] = $js;
            $this->write('_scripts', $js);
        }
        return $js !== null;
    }

    public function add_css($style, $type = 'link', $media = false)
    {
        $css = null;
        $realPath = (defined('FCPATH') ? FCPATH : '') . ltrim($style, '/\\');
        $v = (is_string($realPath) && is_file($realPath)) ? filemtime($realPath) : '20260905a';
        $filepath = base_url() . ltrim($style, '/\\') . '?v=' . $v;

        if ($type === 'link') {
            $css = '<link type="text/css" rel="stylesheet" href="' . $filepath . '"';
            if ($media) {
                $css .= ' media="' . $media . '"';
            }
            $css .= ' />';
        } elseif ($type === 'import') {
            $css = '<style type="text/css">@import url(' . $filepath . ');</style>';
        } elseif ($type === 'embed') {
            $css = '<style type="text/css">';
            $css .= $style;
            $css .= '</style>';
        }

        if ($css !== null && !in_array($css, $this->css, true)) {
            $this->css[] = $css;
            $this->write('_styles', $css);
        }
        return $css !== null;
    }

    public function render($region = null, $buffer = false, $parse = false)
    {
        if ($region) {
            if (isset($this->regions[$region])) {
                return $this->buildContent($this->regions[$region]);
            }
            return '';
        }

        foreach ($this->regions as $name => $regionArr) {
            $built = $this->buildContent($regionArr);
            $this->output[$name] = ($built === false) ? '' : $built;
        }

        $output = view($this->master, $this->output, ['saveData' => true]);

        if ($buffer === false) {
            echo $output;
            return;
        }
        return $output;
    }

    public function load($region = null, $buffer = false)
    {
        $region = null;
        $this->render($region, $buffer);
    }

    protected function buildContent($region)
    {
        $output = null;

        if (!isset($region['content']) or !count($region['content'])) {
            return false;
        }

        if (isset($region['wrapper'])) {
            $wrapper = $region['wrapper'];
            $output .= substr($wrapper, 0, strlen($wrapper) - 1);
            if (isset($region['attributes']) && is_array($region['attributes'])) {
                foreach ($region['attributes'] as $name => $value) {
                    $output .= " $name=\"$value\"";
                }
            }
            $output .= ">";
        }

        foreach ($region['content'] as $content) {
            $output .= $content;
        }

        if (isset($region['wrapper'])) {
            $output .= str_replace('<', '</', $region['wrapper']) . "\n";
        }

        return $output;
    }

    protected function viewExists($view)
    {
        $candidates = [
            APPVIEWPATH . $view . '.php',
            APPVIEWPATH . $view,
        ];
        foreach ($candidates as $c) {
            if (is_file($c)) {
                return true;
            }
        }
        return false;
    }
}
