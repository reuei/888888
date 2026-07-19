<?php
session_start();
ini_set('display_errors', '0');
error_reporting(0);
date_default_timezone_set('Asia/Shanghai');
header('Content-Type: text/html; charset=utf-8');

$basePath = dirname(__DIR__) . '/';
$baseUrl = '/' . basename(dirname(__DIR__)) . '/';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $baseUrl;

if (PHP_SAPI === 'cli') {
    $baseUrl = str_replace('\\', '/', dirname(dirname($_SERVER['PHP_SELF']))) . '/';
    $baseUrl = rtrim($baseUrl, '/') . '/';
}

if (!defined('SITE_NAME')) define('SITE_NAME', '人民检察');
if (!defined('DB_PATH')) define('DB_PATH', $basePath . 'data/jiancha.db');
if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', $basePath . 'uploads/');
if (!defined('UPLOAD_URL')) define('UPLOAD_URL', 'uploads/');
if (!defined('BASE_PATH')) define('BASE_PATH', $basePath);
if (!defined('SITE_URL')) define('SITE_URL', $baseUrl);

if (!file_exists(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);
if (!file_exists(dirname(DB_PATH))) @mkdir(dirname(DB_PATH), 0755, true);
