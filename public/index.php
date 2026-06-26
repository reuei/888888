<?php
/**
 * 入口文件
 * 兼容 ThinkPHP 风格目录结构，适合虚拟主机部署
 */

// 定义根目录
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT_PATH . 'application' . DIRECTORY_SEPARATOR);
define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DIRECTORY_SEPARATOR);

// 错误显示（生产环境建议关闭）
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 检查是否已安装
if (!file_exists(APP_PATH . 'config/database.php')) {
    if (is_dir(ROOT_PATH . 'install')) {
        header('Location: /install/');
        exit;
    }
    exit('系统未安装，且未找到 install 目录');
}

// 加载启动文件
require APP_PATH . 'bootstrap.php';

$app = new App();
$app->run();
