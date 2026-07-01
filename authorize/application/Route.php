<?php
/**
 * QEEFG 授权站简单路由解析器
 * URL 格式：/模块/控制器/操作?参数
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

        $path = preg_replace('#^public/#', '', $path);

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
                if (is_dir($moduleDir) && is_file($moduleDir . DIRECTORY_SEPARATOR . 'Admin.php')) {
                    self::$module = strtolower($segments[0]);
                    self::$controller = 'Dashboard';
                    self::$action = 'index';
                } else {
                    self::$controller = ucfirst($segments[0]);
                }
            }
        }

        self::moduleAuthCheck();

        if (self::$module) {
            $class = ucfirst(self::$module) . '_' . self::$controller;
            $file = APP_PATH . 'controller' . DIRECTORY_SEPARATOR . self::$module . DIRECTORY_SEPARATOR . self::$controller . '.php';
        } else {
            $class = self::$controller;
            $file = APP_PATH . 'controller' . DIRECTORY_SEPARATOR . self::$controller . '.php';
        }

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

    private static function moduleAuthCheck()
    {
        if (self::$module === 'admin') {
            if (self::$controller === 'Admin' && in_array(self::$action, ['index', 'doLogin'], true)) {
                return;
            }
            $admin = session('admin_user');
            if (empty($admin)) {
                redirect(url('admin/admin'));
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
