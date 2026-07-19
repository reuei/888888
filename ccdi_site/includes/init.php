<?php
/**
 * 系统初始化文件
 * 所有页面均需引入此文件
 */
define('SYSTEM_INIT', true);

// 加载核心文件
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

// 检查是否已安装
if (!file_exists(DB_PATH) && !defined('SKIP_INSTALL_CHECK')) {
    // 检查是否在安装页面
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    if ($current_script !== 'index.php' || dirname($_SERVER['SCRIPT_NAME']) !== '/install') {
        $install_path = SITE_PATH . '/install/index.php';
        if (!file_exists(ROOT_PATH . 'install/index.php')) {
            $install_path = SITE_PATH . '/install/';
        }
        header('Location: ' . $install_path);
        exit;
    }
}

// 自动登录
if (file_exists(DB_PATH)) {
    auto_login();
}

// 设置全局变量
$current_page = basename($_SERVER['SCRIPT_NAME'], '.php');
$site_title = SITE_NAME;
$site_description = site_config('site_description', '中央纪委国家监委网站——中国共产党中央纪律检查委员会、中华人民共和国国家监察委员会官方网站');
$site_keywords = site_config('site_keywords', '中央纪委,国家监委,反腐败,纪检监察,巡视巡察,党风廉政');

// 安全头
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}