<?php
/**
 * 控制器基类
 */
class Controller
{
    protected $viewData = [];
    protected $layout = 'layout/main';
    protected $layoutEnabled = true;

    public function assign($key, $value = null)
    {
        if (is_array($key)) {
            $this->viewData = array_merge($this->viewData, $key);
        } else {
            $this->viewData[$key] = $value;
        }
        return $this;
    }

    public function fetch($template = '', $vars = [])
    {
        $this->viewData = array_merge($this->viewData, $vars);

        if (!$template) {
            $template = $this->getDefaultTemplate();
        }

        $viewFile = APP_PATH . 'view' . DIRECTORY_SEPARATOR . $template . '.php';
        if (!is_file($viewFile)) {
            throw new Exception('模板文件不存在：' . $viewFile);
        }

        $content = $this->parseTemplate($viewFile, $this->viewData);

        if ($this->layoutEnabled && $this->layout) {
            $layoutFile = APP_PATH . 'view' . DIRECTORY_SEPARATOR . $this->layout . '.php';
            if (is_file($layoutFile)) {
                $this->viewData['__content__'] = $content;
                $content = $this->parseTemplate($layoutFile, $this->viewData);
            }
        }

        echo $content;
    }

    protected function parseTemplate($file, $vars)
    {
        extract($vars);
        ob_start();
        include $file;
        return ob_get_clean();
    }

    protected function getDefaultTemplate()
    {
        $class = get_class($this);
        $parts = explode('_', $class);
        $action = Route::getAction();
        if (count($parts) > 1) {
            $module = strtolower($parts[0]);
            $controller = strtolower($parts[1]);
            return $module . DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR . $action;
        }
        return strtolower($class) . DIRECTORY_SEPARATOR . $action;
    }

    public function disableLayout()
    {
        $this->layoutEnabled = false;
        return $this;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }
}
