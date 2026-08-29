<?php

namespace App\Libraries;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Input Proxy
 *
 * Mimics the CI3 $this->input->... API surface using CI4's request/response.
 */
class Input
{
    protected RequestInterface $request;
    protected ResponseInterface $response;

    public function __construct()
    {
        $this->request  = service('request');
        $this->response = service('response');
    }

    public function post($index = null, $xssClean = null)
    {
        return $this->fetch('post', $index);
    }

    public function get($index = null, $xssClean = null)
    {
        return $this->fetch('get', $index);
    }

    public function get_post($index = null, $xssClean = null)
    {
        $v = $this->fetch('post', $index);
        if ($v === null && $index !== null) {
            $v = $this->fetch('get', $index);
        }
        return $v;
    }

    public function post_get($index = null, $xssClean = null)
    {
        $v = $this->fetch('get', $index);
        if ($v === null && $index !== null) {
            $v = $this->fetch('post', $index);
        }
        return $v;
    }

    protected function fetch($method, $index)
    {
        $raw = ($method === 'post')
            ? $this->request->getPost()
            : $this->request->getGet();

        if ($index === null) {
            return $raw;
        }

        $parts = explode('.', (string) $index);
        if (count($parts) === 2) {
            // CI3 supports post('arr[prop]') via dot not used; handle simple arrays
            if (isset($raw[$parts[0]]) && is_array($raw[$parts[0]])) {
                return $raw[$parts[0]][$parts[1]] ?? null;
            }
        }

        return $raw[$index] ?? null;
    }

    public function server($index = null)
    {
        $server = $this->request->getServer();
        $index  = strtoupper((string) $index);
        return $index === '' ? $server : ($server[$index] ?? null);
    }

    public function ip_address()
    {
        return $this->request->getIPAddress();
    }

    public function is_ajax_request()
    {
        return $this->request->isAJAX();
    }

    public function valid_ip($ip, $which = '')
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    public function request_headers()
    {
        return $this->request->headers();
    }

    public function get_request_header($index)
    {
        return $this->request->getHeaderLine($index);
    }

    public function user_agent()
    {
        return $this->request->getUserAgent() ? $this->request->getUserAgent()->getAgentString() : null;
    }

    public function cookie($index = null)
    {
        $cookies = $this->request->getCookie();
        if ($index === null) {
            return $cookies;
        }
        return $cookies[$index] ?? null;
    }

    public function set_cookie($name, $value = '', $expire = 0, $domain = '', $path = '/', $prefix = '', $secure = false, $httponly = false)
    {
        if (is_array($name)) {
            extract($name);
        }
        $this->response->setCookie($name, $value, [
            'expires'  => $expire ? time() + $expire : 0,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly,
        ]);
    }
}
