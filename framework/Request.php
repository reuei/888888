<?php
namespace Framework;

class Request
{
    public $method;
    public $path;
    public $query = [];
    public $post = [];
    public $files = [];
    public $cookies = [];
    public $server = [];
    public $headers = [];

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = $this->parsePath();
        $this->query = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->cookies = $_COOKIE ?? [];
        $this->server = $_SERVER ?? [];
        $this->headers = $this->parseHeaders();
    }

    protected function parsePath()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = strtok($uri, '?');
        $uri = '/' . trim($uri, '/');
        return $uri === '' ? '/' : $uri;
    }

    protected function parseHeaders()
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    public function input($key, $default = null)
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function get($key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function post($key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    public function isGet()
    {
        return $this->method === 'GET';
    }

    public function isPost()
    {
        return $this->method === 'POST';
    }

    public function isAjax()
    {
        return ($this->headers['x-requested-with'] ?? '') === 'XMLHttpRequest';
    }

    public function ip()
    {
        foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $key) {
            if (!empty($this->server[$key])) {
                $ip = explode(',', $this->server[$key])[0];
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    public function userAgent()
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }
}
