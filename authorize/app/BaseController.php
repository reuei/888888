<?php

declare (strict_types = 1);

namespace app;

use think\App;
use think\facade\View;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 布局模板
     * @var string
     */
    protected $layout = 'layout/main';

    /**
     * 是否启用布局
     * @var bool
     */
    protected $layoutEnabled = true;

    /**
     * 构造方法
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 模板变量赋值
     */
    protected function assign($key, $value = null)
    {
        if (is_array($key)) {
            View::assign($key);
        } else {
            View::assign($key, $value);
        }
        return $this;
    }

    /**
     * 渲染模板
     */
    protected function fetch($template = '', $vars = [])
    {
        if (!empty($vars)) {
            View::assign($vars);
        }

        if (!$template) {
            $template = $this->getDefaultTemplate();
        }

        $content = View::fetch($template);

        if ($this->layoutEnabled && $this->layout) {
            View::assign('__content__', $content);
            $content = View::fetch($this->layout);
        }

        echo $content;
    }

    /**
     * 获取默认模板
     */
    protected function getDefaultTemplate()
    {
        $class = get_class($this);
        $parts = explode('\\', $class);
        $controller = strtolower(end($parts));
        $action = strtolower($this->request->action());

        // 去掉命名空间中的 app\controller 前缀
        $module = '';
        $controllerIndex = array_search('controller', $parts);
        if ($controllerIndex !== false && isset($parts[$controllerIndex + 1])) {
            $subParts = array_slice($parts, $controllerIndex + 1);
            $controller = strtolower(array_pop($subParts));
            if (!empty($subParts)) {
                $module = implode('/', array_map('strtolower', $subParts));
            }
        }

        return $module ? $module . '/' . $controller . '/' . $action : $controller . '/' . $action;
    }

    /**
     * 禁用布局
     */
    protected function disableLayout()
    {
        $this->layoutEnabled = false;
        return $this;
    }

    /**
     * 设置布局
     */
    protected function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * 验证数据
     */
    protected function validate(array $data, string|array $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }
}
