<?php
/**
 * QEEFG授权站系统
 * 入口文件
 */

// 错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 检查PHP版本
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    header('Content-Type: text/html; charset=utf-8');
    die('系统需要 PHP >= 8.1，当前版本：' . PHP_VERSION);
}

// 定义常量
define('ROOT_PATH', dirname(__DIR__) . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('PUBLIC_PATH', __DIR__ . '/');
define('STORAGE_PATH', ROOT_PATH . 'storage/');

// 自动加载
spl_autoload_register(function ($class) {
    $file = APP_PATH . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// 加载 .env
if (file_exists(ROOT_PATH . '.env')) {
    $lines = file(ROOT_PATH . '.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// 获取URI
$uri = $_SERVER['REQUEST_URI'];
$uri = parse_url($uri, PHP_URL_PATH);
$uri = str_replace('/index.php', '', $uri);
$uri = rtrim($uri, '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

// 路由映射表
$routes = [
    'GET' => [
        '/' => 'Index@index',
        '/platform' => 'Index@platform',
        '/license-query' => 'Index@licenseQuery',
        '/documents' => 'Index@documents',
        '/announcement' => 'Index@announcement',
        '/switch-lang' => 'Index@switchLang',
        '/login' => 'User@login',
        '/register' => 'User@register',
        '/logout' => 'User@logout',
        '/user/login' => 'User@login',
        '/user/register' => 'User@register',
        '/user/logout' => 'User@logout',
        '/dashboard' => 'User@dashboard',
        '/workplace' => 'User@workplace',
        '/user/products' => 'User@products',
        '/user/my-products' => 'User@myProducts',
        '/user/orders' => 'User@orders',
        '/user/balance' => 'User@balance',
        '/user/logs' => 'User@logs',
        '/user/settings' => 'User@settings',
        '/user/feedback' => 'User@feedback',
        '/user/messages' => 'User@messages',
        '/user/plugin-market' => 'User@pluginMarket',
        '/user/developer' => 'User@developer',
        '/user/buy' => 'User@buyPage',
        '/user/balance-logs' => 'User@balanceLogs',
        '/user/login-logs' => 'User@loginLogs',
        '/user/operation-logs' => 'User@operationLogs',
        '/user/rebind' => 'User@rebind',
        '/download' => 'User@download',
        '/admin/login' => 'Admin@login',
        '/admin/logout' => 'Admin@logout',
        '/admin/dashboard' => 'Admin@dashboard',
        '/admin/users' => 'Admin@users',
        '/admin/products' => 'Admin@products',
        '/admin/licenses' => 'Admin@licenses',
        '/admin/orders' => 'Admin@orders',
        '/admin/settings' => 'Admin@settings',
        '/admin/documents' => 'Admin@documents',
        '/admin/messages' => 'Admin@messages',
        '/admin/developers' => 'Admin@developers',
        '/admin/plugins' => 'Admin@plugins',
        '/admin/feedback' => 'Admin@feedbackList',
        '/admin/emailPool' => 'Admin@emailPool',
        '/admin/emailTemplates' => 'Admin@emailTemplates',
        '/admin/paymentChannels' => 'Admin@paymentChannels',
        '/admin/uploadFiles' => 'Admin@uploadFiles',
        '/captcha' => 'Captcha@generate',
    ],
    'POST' => [
        '/user/dologin' => 'User@dologin',
        '/user/doregister' => 'User@doregister',
        '/user/updateSettings' => 'User@updateSettings',
        '/user/updatePassword' => 'User@updatePassword',
        '/user/submitFeedback' => 'User@submitFeedback',
        '/user/buyProduct' => 'User@buyProduct',
        '/user/applyDeveloper' => 'User@applyDeveloper',
        '/user/submitPlugin' => 'User@submitPlugin',
        '/user/rebindEmail' => 'User@rebindEmail',
        '/user/rebindPhone' => 'User@rebindPhone',
        '/user/sendVerifyCode' => 'User@sendVerifyCode',
        '/user/readMessage' => 'User@readMessage',
        '/user/readAllMessages' => 'User@readAllMessages',
        '/admin/dologin' => 'Admin@dologin',
        '/admin/addProduct' => 'Admin@addProduct',
        '/admin/editProduct' => 'Admin@editProduct',
        '/admin/deleteProduct' => 'Admin@deleteProduct',
        '/admin/uploadProductFile' => 'Admin@uploadProductFile',
        '/admin/deleteProductFile' => 'Admin@deleteProductFile',
        '/admin/saveSettings' => 'Admin@saveSettings',
        '/admin/saveDocument' => 'Admin@saveDocument',
        '/admin/deleteDocument' => 'Admin@deleteDocument',
        '/admin/createFeatureCode' => 'Admin@createFeatureCode',
        '/admin/updateFeatureCodeStatus' => 'Admin@updateFeatureCodeStatus',
        '/admin/sendMessage' => 'Admin@sendMessage',
        '/admin/editMessage' => 'Admin@editMessage',
        '/admin/deleteMessage' => 'Admin@deleteMessage',
        '/admin/reviewDeveloper' => 'Admin@reviewDeveloper',
        '/admin/reviewPlugin' => 'Admin@reviewPlugin',
        '/admin/editPlugin' => 'Admin@editPlugin',
        '/admin/deletePlugin' => 'Admin@deletePlugin',
        '/admin/replyFeedback' => 'Admin@replyFeedback',
        '/admin/processFeedback' => 'Admin@processFeedback',
        '/admin/rejectFeedback' => 'Admin@rejectFeedback',
        '/admin/addEmailPool' => 'Admin@addEmailPool',
        '/admin/editEmailPool' => 'Admin@editEmailPool',
        '/admin/deleteEmailPool' => 'Admin@deleteEmailPool',
        '/admin/testEmail' => 'Admin@testEmail',
        '/admin/saveEmailTemplate' => 'Admin@saveEmailTemplate',
        '/admin/savePaymentChannel' => 'Admin@savePaymentChannel',
        '/admin/deletePaymentChannel' => 'Admin@deletePaymentChannel',
        '/admin/uploadSiteLogo' => 'Admin@uploadSiteLogo',
        '/admin/uploadSiteFavicon' => 'Admin@uploadSiteFavicon',
        '/admin/saveEmailConfig' => 'Admin@saveEmailConfig',
    ],
];

// 匹配路由
$route = $routes[$method][$uri] ?? null;

if ($route) {
    list($controller, $action) = explode('@', $route);
    $controllerClass = "app\\controller\\{$controller}";

    try {
        if (class_exists($controllerClass)) {
            $instance = new $controllerClass();
            if (method_exists($instance, $action)) {
                echo $instance->$action();
            } else {
                http_response_code(404);
                echo '方法不存在';
            }
        } else {
            http_response_code(404);
            echo '控制器不存在';
        }
    } catch (\Exception $e) {
        http_response_code(500);
        if ($method === 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['code' => 500, 'msg' => '服务器错误: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        } else {
            echo '服务器错误: ' . htmlspecialchars($e->getMessage());
        }
    }
} else {
    http_response_code(404);
    if ($method === 'POST') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['code' => 404, 'msg' => '路由不存在'], JSON_UNESCAPED_UNICODE);
    } else {
        echo '页面不存在';
    }
}