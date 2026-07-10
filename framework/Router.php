<?php
namespace Framework;

use Framework\Response;

class Router
{
    protected $routes = [];
    protected $groups = [];

    public function get($path, $action)
    {
        return $this->addRoute('GET', $path, $action);
    }

    public function post($path, $action)
    {
        return $this->addRoute('POST', $path, $action);
    }

    public function any($path, $action)
    {
        return $this->addRoute(['GET', 'POST'], $path, $action);
    }

    public function group($prefix, $callback)
    {
        $this->groups[] = $prefix;
        $callback($this);
        array_pop($this->groups);
    }

    protected function addRoute($methods, $path, $action)
    {
        $prefix = implode('', $this->groups);
        $fullPath = $prefix . $path;
        $methods = is_array($methods) ? $methods : [$methods];
        $this->routes[] = [
            'methods' => $methods,
            'path' => $this->normalize($fullPath),
            'pattern' => $this->compile($fullPath),
            'action' => $action,
        ];
        return $this;
    }

    protected function normalize($path)
    {
        return '/' . trim($path, '/');
    }

    protected function compile($path)
    {
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $this->normalize($path));
        return '#^' . $regex . '$#';
    }

    public function dispatch(Request $request)
    {
        $path = $request->path;

        foreach ($this->routes as $route) {
            if (!in_array($request->method, $route['methods'])) {
                continue;
            }
            if (preg_match($route['pattern'], $path, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[$key] = $value;
                    }
                }
                return $this->invoke($route['action'], $params, $request);
            }
        }

        Response::notFound();
    }

    protected function invoke($action, $params, $request)
    {
        if (is_array($action)) {
            [$class, $method] = $action;
            $controller = new $class();
            return $controller->$method($request, $params);
        }
        if (is_callable($action)) {
            return call_user_func($action, $request, $params);
        }
    }
}
