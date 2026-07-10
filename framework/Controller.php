<?php
namespace Framework;

use Framework\Request;
use Framework\Response;

class Controller
{
    protected $viewPath;
    protected $layout = false;
    protected $data = [];

    public function __construct()
    {
        $this->viewPath = App::getInstance()->getConfig('app_path') . '/';
    }

    protected function view($name, $data = [])
    {
        $this->data = array_merge($this->data, $data);
        $content = $this->renderView($name, $this->data);
        if ($this->layout) {
            $content = $this->renderView('layouts/' . $this->layout, array_merge($this->data, ['__content__' => $content]));
        }
        Response::html($content);
    }

    protected function renderView($name, $data)
    {
        $file = $this->viewPath . str_replace('.', '/', $name) . '.php';
        if (!file_exists($file)) {
            throw new \Exception("View not found: $name ($file)");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        include $file;
        return ob_get_clean();
    }

    protected function assign($key, $value)
    {
        $this->data[$key] = $value;
    }

    protected function json($data, $code = 0, $msg = 'success')
    {
        Response::json($data, $code, $msg);
    }

    protected function success($msg = 'success', $data = null)
    {
        Response::success($msg, $data);
    }

    protected function error($msg = 'error', $code = 1, $status = 200)
    {
        Response::error($msg, $code, null, $status);
    }
}
