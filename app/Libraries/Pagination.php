<?php

namespace App\Libraries;

/**
 * Pagination - CI3-style pagination link generator backed by CI4 Pager reading.
 */
class Pagination
{
    protected $config = [
        'base_url'              => '',
        'per_page'              => 10,
        'total_rows'            => 0,
        'uri_segment'           => 3,
        'num_links'             => 2,
        'full_tag_open'         => '<ul class="pagination">',
        'full_tag_close'        => '</ul>',
        'first_link'            => '&lsaquo; First',
        'first_tag_open'        => '<li>',
        'first_tag_close'       => '</li>',
        'last_link'             => 'Last &rsaquo;',
        'last_tag_open'         => '<li>',
        'last_tag_close'        => '</li>',
        'next_link'             => '&gt;',
        'next_tag_open'         => '<li>',
        'next_tag_close'        => '</li>',
        'prev_link'             => '&lt;',
        'prev_tag_open'         => '<li>',
        'prev_tag_close'        => '</li>',
        'cur_tag_open'          => '<li class="active"><a href="#">',
        'cur_tag_close'         => '</a></li>',
        'num_tag_open'          => '<li>',
        'num_tag_close'         => '</li>',
    ];

    protected $cur_page = 1;

    public function initialize(array $params = [])
    {
        foreach ($params as $key => $val) {
            if (isset($this->config[$key])) {
                $this->config[$key] = $val;
            }
        }
        $this->config['per_page'] = (int) $this->config['per_page'];
        $this->config['total_rows'] = (int) $this->config['total_rows'];
        $this->config['num_links'] = (int) (isset($params['num_links']) ? $params['num_links'] : $this->config['num_links']);
        $this->cur_page = $this->determineCurrentPage();
        return $this;
    }

    protected function determineCurrentPage()
    {
        $request = service('request');
        $get = $request->getGet('page');
        if ($get !== null && is_numeric($get)) {
            return max(1, (int) $get);
        }
        return 1;
    }

    public function create_links()
    {
        if ($this->config['total_rows'] == 0 || $this->config['per_page'] == 0) {
            return '';
        }

        $num_pages = (int) ceil($this->config['total_rows'] / $this->config['per_page']);
        if ($num_pages <= 1) {
            return '';
        }

        $cur = min($this->cur_page, $num_pages);
        if ($cur <= 0) {
            $cur = 1;
        }

        $baseUrl = $this->config['base_url'];
        $pageUrl = function ($page) use ($baseUrl) {
            $sep = strpos($baseUrl, '?') !== false ? '&amp;' : (strrpos($baseUrl, '/') === strlen($baseUrl) - 1 ? '' : '/');
            return $baseUrl . $sep . $page;
        };

        $out = '';
        $out .= $this->config['full_tag_open'];

        // First link
        if ($cur > 1 && !empty($this->config['first_link'])) {
            $out .= $this->config['first_tag_open'] . '<a href="' . $pageUrl(1) . '">' . $this->config['first_link'] . '</a>' . $this->config['first_tag_close'];
        }

        // Prev link
        if ($cur > 1 && !empty($this->config['prev_link'])) {
            $out .= $this->config['prev_tag_open'] . '<a href="' . $pageUrl($cur - 1) . '">' . $this->config['prev_link'] . '</a>' . $this->config['prev_tag_close'];
        }

        // Numbers
        $start = max(1, $cur - $this->config['num_links']);
        $end = min($num_pages, $cur + $this->config['num_links']);
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $cur) {
                $out .= $this->config['cur_tag_open'] . $i . $this->config['cur_tag_close'];
            } else {
                $out .= $this->config['num_tag_open'] . '<a href="' . $pageUrl($i) . '">' . $i . '</a>' . $this->config['num_tag_close'];
            }
        }

        // Next link
        if ($cur < $num_pages && !empty($this->config['next_link'])) {
            $out .= $this->config['next_tag_open'] . '<a href="' . $pageUrl($cur + 1) . '">' . $this->config['next_link'] . '</a>' . $this->config['next_tag_close'];
        }

        // Last link
        if ($cur < $num_pages && !empty($this->config['last_link'])) {
            $out .= $this->config['last_tag_open'] . '<a href="' . $pageUrl($num_pages) . '">' . $this->config['last_link'] . '</a>' . $this->config['last_tag_close'];
        }

        $out .= $this->config['full_tag_close'];
        return $out;
    }

    public function __get($key)
    {
        return $this->config[$key] ?? null;
    }
}
