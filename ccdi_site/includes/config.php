<?php
/**
 * 网站核心配置文件
 * 中央纪委国家监委网站风格 CMS 系统
 */

// 防止直接访问
if (!defined('SYSTEM_INIT')) {
    die('未经授权的访问');
}

// 网站基本信息
define('SITE_NAME', '中央纪委国家监委网站');
define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);
// 计算站点子目录路径（相对于文档根目录）
$site_root = realpath(dirname(__DIR__));
$doc_root = realpath($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__));
$site_path = '';
if ($site_root && $doc_root && $site_root !== $doc_root && strpos($site_root, $doc_root) === 0) {
    $site_path = str_replace('\\', '/', substr($site_root, strlen($doc_root)));
}
define('SITE_PATH', $site_path);
define('SITE_FULL_URL', SITE_URL . SITE_PATH);

// 路径定义
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('DATA_PATH', ROOT_PATH . 'data/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('ADMIN_PATH', ROOT_PATH . 'admin/');
define('TEMPLATES_PATH', ROOT_PATH . 'templates/');

// 数据库配置 (SQLite)
define('DB_PATH', DATA_PATH . 'ccdi_site.db');
define('DB_DSN', 'sqlite:' . DB_PATH);

// 会话配置
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}
session_start();

// 时区设置
date_default_timezone_set('Asia/Shanghai');

// 错误报告（生产环境关闭）
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// 上传限制
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,webp,mp4,webm,pdf,doc,docx,xls,xlsx');
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// 分页设置
define('ITEMS_PER_PAGE', 15);
define('ADMIN_ITEMS_PER_PAGE', 20);

// 缓存设置
define('CACHE_ENABLED', false);
define('CACHE_PATH', DATA_PATH . 'cache/');

// 安全密钥（安装时自动生成）
define('SECURITY_KEY', get_site_config('security_key', ''));
define('CSRF_TOKEN_NAME', 'csrf_token');

// 版本号
define('CMS_VERSION', '8.0.0');
define('CMS_BUILD', '20260805');

// 数据库连接函数
function get_site_config($key, $default = '') {
    static $config_cache = null;
    if ($config_cache === null) {
        if (file_exists(DB_PATH)) {
            try {
                $db = new PDO(DB_DSN);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $db->query("SELECT config_key, config_value FROM site_config");
                $config_cache = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $config_cache[$row['config_key']] = $row['config_value'];
                }
            } catch (Exception $e) {
                $config_cache = [];
            }
        } else {
            $config_cache = [];
        }
    }
    return isset($config_cache[$key]) ? $config_cache[$key] : $default;
}