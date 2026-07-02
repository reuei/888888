<?php
/**
 * PHP 内置开发服务器路由文件
 *
 * 使用方式：php -S 127.0.0.1:8080 router.php
 * 该文件仅在开发测试时使用，生产环境请使用 Apache/Nginx。
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$publicPath = __DIR__ . $uri;

// 静态资源直接访问
if ($uri !== '/' && file_exists($publicPath) && is_file($publicPath)) {
    return false;
}

// 其他请求交给 ThinkPHP
require __DIR__ . '/index.php';
