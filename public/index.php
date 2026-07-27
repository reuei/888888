<?php
/**
 * QEEFG授权站系统 - 独立版
 * 不依赖框架，直接运行
 */

// 错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 检查PHP版本
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    die('系统需要 PHP >= 8.1，当前版本：' . PHP_VERSION);
}

// 定义常量
define('ROOT_PATH', dirname(__DIR__) . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');

// 自动加载类
spl_autoload_register(function ($class) {
    $file = APP_PATH . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// 加载配置
$config = [];
if (file_exists(CONFIG_PATH . 'database.php')) {
    $config['database'] = include CONFIG_PATH . 'database.php';
}

// 简单路由
$uri = $_SERVER['REQUEST_URI'];
$uri = parse_url($uri, PHP_URL_PATH);
$uri = str_replace('/index.php', '', $uri);
$uri = rtrim($uri, '/') ?: '/';

// 路由映射
$routes = [
    '/' => 'Index@index',
    '/index' => 'Index@index',
    '/login' => 'User@login',
    '/register' => 'User@register',
    '/user/dashboard' => 'User@dashboard',
    '/user/workplace' => 'User@workplace',
    '/user/products' => 'User@products',
    '/user/my-products' => 'User@myProducts',
    '/user/balance' => 'User@balance',
    '/user/settings' => 'User@settings',
    '/user/logout' => 'User@logout',
    '/admin/login' => 'Admin@login',
    '/admin/dashboard' => 'Admin@dashboard',
    '/admin/users' => 'Admin@users',
    '/admin/products' => 'Admin@products',
    '/admin/settings' => 'Admin@settings',
    '/license-query' => 'Index@licenseQuery',
    '/documents' => 'Index@documents',
];

// 匹配路由
if (isset($routes[$uri])) {
    list($controller, $action) = explode('@', $routes[$uri]);
    $controllerClass = "app\\controller\\{$controller}";
    
    if (class_exists($controllerClass)) {
        $instance = new $controllerClass();
        if (method_exists($instance, $action)) {
            echo $instance->$action();
        } else {
            http_response_code(404);
            echo '页面不存在';
        }
    } else {
        http_response_code(404);
        echo '控制器不存在';
    }
} else {
    // 处理POST请求
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postRoutes = [
            '/user/dologin' => 'User@dologin',
            '/user/doregister' => 'User@doregister',
            '/user/updateSettings' => 'User@updateSettings',
            '/admin/dologin' => 'Admin@dologin',
        ];
        
        if (isset($postRoutes[$uri])) {
            list($controller, $action) = explode('@', $postRoutes[$uri]);
            $controllerClass = "app\\controller\\{$controller}";
            
            if (class_exists($controllerClass)) {
                $instance = new $controllerClass();
                if (method_exists($instance, $action)) {
                    echo $instance->$action();
                } else {
                    http_response_code(404);
                    echo json_encode(['code' => 404, 'msg' => '方法不存在']);
                }
            }
        } else {
            http_response_code(404);
            echo json_encode(['code' => 404, 'msg' => '路由不存在']);
        }
    } else {
        http_response_code(404);
        echo '页面不存在';
    }
}