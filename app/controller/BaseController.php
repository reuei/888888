<?php
namespace app\controller;

use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基类
 */
abstract class BaseController
{
    /**
     * 应用实例
     * @var App
     */
    protected $app;

    /**
     * 请求实例
     */
    protected $request;

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
     * 构造方法
     * @access public
     * @param  App  $app  应用实例
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize()
    {}

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v     = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    /**
     * 返回成功响应
     */
    protected function success($data = [], $msg = '操作成功', $code = 200)
    {
        return json([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ]);
    }

    /**
     * 返回失败响应
     */
    protected function error($msg = '操作失败', $code = 400)
    {
        return json([
            'code' => $code,
            'msg'  => $msg,
            'data' => []
        ]);
    }

    /**
     * 渲染模板
     */
    protected function fetch(string $template = '', array $vars = [])
    {
        if (!$template) {
            $template = $this->request->controller() . '/' . $this->request->action();
        }

        return view($template, $vars);
    }
}