<?php
/**
 * 玄武发卡 v1.0.5 系统入口
 * 自研轻量MVC框架
 */

define('XUANWU_VERSION', '1.0.5');
define('XUANWU_LICENSE_VERSION', '1.1.1');
define('ROOT_PATH', __DIR__);
define('APP_PATH', __DIR__ . '/app');
define('FRAMEWORK_PATH', __DIR__ . '/framework');
define('RUNTIME_PATH', __DIR__ . '/runtime');
define('PUBLIC_PATH', __DIR__ . '/public');

date_default_timezone_set('Asia/Shanghai');

require FRAMEWORK_PATH . '/helpers.php';
require FRAMEWORK_PATH . '/App.php';
require FRAMEWORK_PATH . '/Request.php';
require FRAMEWORK_PATH . '/Response.php';
require FRAMEWORK_PATH . '/Router.php';
require FRAMEWORK_PATH . '/Controller.php';
require FRAMEWORK_PATH . '/Session.php';
require FRAMEWORK_PATH . '/Cache/Cache.php';
require FRAMEWORK_PATH . '/Database/Database.php';
require FRAMEWORK_PATH . '/Database/QueryBuilder.php';

spl_autoload_register(function ($class) {
    $prefix = 'Framework\\';
    $base = FRAMEWORK_PATH . '/';
    if (strpos($class, $prefix) === 0) {
        $relative = substr($class, strlen($prefix));
        $file = $base . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }

    $appPrefixes = [
        'Home\\' => APP_PATH . '/Home/',
        'User\\' => APP_PATH . '/User/',
        'Admin\\' => APP_PATH . '/Admin/',
        'License\\' => APP_PATH . '/License/',
        'Install\\' => APP_PATH . '/Install/',
    ];
    foreach ($appPrefixes as $prefix => $base) {
        if (strpos($class, $prefix) === 0) {
            $relative = substr($class, strlen($prefix));
            $file = $base . str_replace('\\', '/', $relative) . '.php';
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

$app = new \Framework\App();
\Framework\Session::start();

$request = new \Framework\Request();
$router = new \Framework\Router();

$routesFile = ROOT_PATH . '/routes.php';
if (file_exists($routesFile)) {
    require $routesFile;
}

$app->setRouter($router);
$app->run();
