<?php
if (!defined('YUYUN_ROOT')) {
    define('YUYUN_ROOT', dirname(__DIR__));
}

$pdo = null;
$installed = file_exists(YUYUN_ROOT . '/data/installed.lock');

if ($installed && file_exists(YUYUN_ROOT . '/data/config.php')) {
    require YUYUN_ROOT . '/data/config.php';
    try {
        $pdo = new PDO(DB_DSN, defined('DB_USER') ? DB_USER : null, defined('DB_PASS') ? DB_PASS : null);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if (DB_TYPE === 'sqlite') {
            $pdo->exec('PRAGMA foreign_keys = ON;');
        }
    } catch (PDOException $e) {
        die('数据库连接失败：' . $e->getMessage());
    }
}

function getDb(): PDO {
    global $pdo;
    if (!$pdo) {
        throw new Exception('数据库未初始化');
    }
    return $pdo;
}
