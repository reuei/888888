<?php
if (!defined('YUYUN_ROOT')) {
    define('YUYUN_ROOT', dirname(__DIR__));
}
if (!defined('YUYUN_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = dirname($_SERVER['SCRIPT_NAME']);
    $base = rtrim(str_replace(['/install','/admin','/user'], '', $script), '/');
    define('YUYUN_URL', $protocol . '://' . $host . $base);
}

$installed = file_exists(YUYUN_ROOT . '/data/installed.lock');
$isInstall = strpos($_SERVER['PHP_SELF'] ?? '', '/install/') !== false;

if (!$installed && !$isInstall) {
    header('Location: ' . YUYUN_URL . '/install/');
    exit;
}

require __DIR__ . '/db.php';
require __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
