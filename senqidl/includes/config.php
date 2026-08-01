<?php
// 森企动力 - 站点配置文件
define('SITE_NAME', '森企动力');
define('SITE_TITLE', '森企动力 - 品牌数字化解决方案专家');
define('SITE_DESCRIPTION', '森企动力 - 品牌数字化解决方案专家，提供智能外贸数字营销服务，助力中国品牌全球化');
define('SITE_KEYWORDS', '森企动力,品牌数字化,外贸营销,SEO,数字营销');
define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

// 主题颜色 - 蓝色系
define('THEME_PRIMARY', '#1a5fff');
define('THEME_PRIMARY_DARK', '#0d47c2');
define('THEME_PRIMARY_LIGHT', '#3d7aff');
define('THEME_ACCENT', '#00d4ff');
define('THEME_BG', '#f5f7fa');
define('THEME_TEXT', '#1a1a1a');
define('THEME_TEXT_LIGHT', '#666666');
define('THEME_WHITE', '#ffffff');

// 路径
define('ROOT_PATH', dirname(__DIR__));
define('DATA_PATH', ROOT_PATH . '/data');
define('ADMIN_PATH', ROOT_PATH . '/admin');

// 时区
date_default_timezone_set('Asia/Shanghai');

// 错误显示
error_reporting(E_ALL);
ini_set('display_errors', 0);

// 自动加载
spl_autoload_register(function ($class) {
    $file = ROOT_PATH . '/includes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
