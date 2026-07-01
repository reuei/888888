<?php
/**
 * QEEFG 寄售系统售卖网站入口文件
 */

define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT_PATH . 'application' . DIRECTORY_SEPARATOR);
define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DIRECTORY_SEPARATOR);

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
if (!is_dir(RUNTIME_PATH)) {
    @mkdir(RUNTIME_PATH, 0755, true);
}
ini_set('error_log', RUNTIME_PATH . 'error.log');

if (!file_exists(APP_PATH . 'config/database.php')) {
    if (is_dir(ROOT_PATH . 'install')) {
        header('Location: /install/');
        exit;
    }
    exit('系统未安装，且未找到 install 目录');
}

require APP_PATH . 'bootstrap.php';

$app = new App();
$app->run();
