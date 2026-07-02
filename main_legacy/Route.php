<?php
/**
 * 简单路由解析器
 * URL 格式：/模块/控制器/操作?参数
 * 模块目录为小写，控制器类名为首字母大写
 */
class Route
{
    private static $controller = 'Index';
    private static $action = 'index';
    private static $module = '';

    public static function dispatch()
    {
        $path = $_SERVER['REQUEST_URI'];
        $path = parse_url($path, PHP_URL_PATH);
        $path = trim($path, '/');

        // 去掉 public 前缀
        $path = preg_replace('#^public/#', '', $path);

        // 去掉 install 路径直接处理
        if (strpos($path, 'install') === 0) {
            return;
        }

        if ($path) {
            $segments = explode('/', $path);
            $segments = array_filter($segments);
            $segments = array_values($segments);

            if (count($segments) >= 3) {
                self::$module = strtolower($segments[0]);
                self::$controller = ucfirst($segments[1]);
                self::$action = $segments[2];
            } elseif (count($segments) === 2) {
                $moduleDir = APP_PATH . 'controller' . DIRECTORY_SEPARATOR . strtolower($segments[0]);
                // 如果存在模块目录，则按模块控制器处理，默认 index 操作
                if (is_dir($moduleDir)) {
                    self::$module = strtolower($segments[0]);
                    self::$controller = ucfirst($segments[1]);
                    self::$action = 'index';
                } else {
                    self::$controller = ucfirst($segments[0]);
                    self::$action = $segments[1];
                }
            } elseif (count($segments) === 1) {
                $moduleDir = APP_PATH . 'controller' . DIRECTORY_SEPARATOR . strtolower($segments[0]);
                if (is_dir($moduleDir) && is_file($moduleDir . DIRECTORY_SEPARATOR . 'Index.php')) {
                    self::$module = strtolower($segments[0]);
                    self::$controller = 'Index';
                    self::$action = 'index';
                } else {
                    self::$controller = ucfirst($segments[0]);
                }
            }
        }

        if (self::$module) {
            $class = ucfirst(self::$module) . '_' . self::$controller;
            $file = APP_PATH . 'controller' . DIRECTORY_SEPARATOR . self::$module . DIRECTORY_SEPARATOR . self::$controller . '.php';
        } else {
            $class = self::$controller;
            $file = APP_PATH . 'controller' . DIRECTORY_SEPARATOR . self::$controller . '.php';
        }

        // 模块级登录权限拦截
        self::moduleAuthCheck();

        if (!is_file($file)) {
            throw new Exception('控制器不存在：' . $file);
        }

        require $file;

        if (!class_exists($class)) {
            throw new Exception('控制器类不存在：' . $class);
        }

        $instance = new $class();
        $action = self::$action;

        if (!method_exists($instance, $action)) {
            throw new Exception('操作方法不存在：' . $action);
        }

        call_user_func([$instance, $action]);
    }

    /**
     * 模块级登录与身份拦截
     */
    private static function moduleAuthCheck()
    {
        $module = self::$module;

        if ($module === 'admin') {
            $admin = session('admin_user');
            if (empty($admin)) {
                redirect(url('login') . '?type=admin');
            }
            // 分站管理员不允许进入总站后台
            if (in_array($admin['role'] ?? '', ['subsite_super', 'subsite_admin'], true)) {
                redirect(url('subsite/dashboard'));
            }
        }

        if ($module === 'merchant') {
            if (empty(session('merchant_user'))) {
                redirect(url('login') . '?type=merchant');
            }
        }

        if ($module === 'subsite') {
            $admin = session('admin_user');
            if (empty($admin) || $admin['subsite_id'] <= 0 || !in_array($admin['role'] ?? '', ['subsite_super', 'subsite_admin'], true)) {
                redirect(url('login') . '?type=admin');
            }
        }
    }

    public static function getController()
    {
        return self::$controller;
    }

    public static function getAction()
    {
        return self::$action;
    }

    public static function getModule()
    {
        return self::$module ? ucfirst(self::$module) : '';
    }
}
