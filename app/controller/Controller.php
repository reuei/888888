<?php
/**
 * 兼容层：原主站 Controller 基类
 * 继承 ThinkPHP BaseController，并保留 assign/fetch/layout 等旧方法
 */

namespace app\controller;

use think\App;
use app\BaseController as ThinkBaseController;

class Controller extends ThinkBaseController
{
    protected $viewData = [];
    protected $layout = 'layout/main';
    protected $layoutEnabled = true;

    public function __construct(?App $app = null)
    {
        parent::__construct($app ?? app());
    }

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
            throw new \Exception('模板文件不存在：' . $viewFile);
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
        return null;
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
        $ns = 'app\\controller\\';
        if (strpos($class, $ns) === 0) {
            $relative = substr($class, strlen($ns));
            $parts = explode('\\', $relative);
        } else {
            $parts = [$class];
        }
        $parts[] = $this->request->action() ?: 'index';
        return strtolower(implode(DIRECTORY_SEPARATOR, $parts));
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
