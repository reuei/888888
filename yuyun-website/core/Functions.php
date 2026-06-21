<?php
/**
 * 语云科技企业官网 - 核心函数库
 * 提供全局公共函数
 */

if (!defined('YUYUN_ROOT')) {
    define('YUYUN_ROOT', dirname(__DIR__));
}

// JSON数据存储路径
define('DATA_PATH', YUYUN_ROOT . '/data/');
define('UPLOADS_PATH', YUYUN_ROOT . '/uploads/');
define('TEMPLATES_PATH', YUYUN_ROOT . '/templates/');

/**
 * 安全输出HTML
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * 读取JSON配置文件
 */
function get_config($key = null) {
    $file = DATA_PATH . 'config.json';
    $config = [];
    if (file_exists($file)) {
        $config = json_decode(file_get_contents($file), true) ?: [];
    }
    if ($key !== null) {
        return $config[$key] ?? null;
    }
    return $config;
}

/**
 * 写入JSON配置文件
 */
function set_config($key, $value) {
    $file = DATA_PATH . 'config.json';
    $config = get_config();
    $config[$key] = $value;
    return file_put_contents($file, json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

/**
 * 获取内容数据
 */
function get_content($type) {
    $file = DATA_PATH . $type . '.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true) ?: [];
    }
    return [];
}

/**
 * 保存内容数据
 */
function save_content($type, $data) {
    $file = DATA_PATH . $type . '.json';
    return file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

/**
 * 生成CSRF Token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * 验证CSRF Token
 */
function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * JSON响应
 */
function json_response($code, $message = '', $data = null) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'code' => $code,
        'message' => $message,
        'data' => $data,
        'timestamp' => time()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 成功响应
 */
function success($data = null, $message = '操作成功') {
    json_response(200, $message, $data);
}

/**
 * 错误响应
 */
function error($message = '操作失败', $code = 400) {
    json_response($code, $message);
}

/**
 * 重定向
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * 检查是否已安装
 */
function is_installed() {
    return file_exists(DATA_PATH . 'config.json') && get_config('installed') === true;
}

/**
 * 检查是否登录(前台用户)
 */
function is_logged_in() {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_email']);
}

/**
 * 检查是否管理员
 */
function is_admin() {
    return !empty($_SESSION['admin_id']) && $_SESSION['admin_role'] === 'admin';
}

/**
 * 要求登录
 */
function require_login() {
    if (!is_logged_in()) {
        if (is_ajax()) {
            error('请先登录', 401);
        }
        redirect('user/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

/**
 * 要求管理员权限
 */
function require_admin() {
    if (!is_admin()) {
        if (is_ajax()) {
            error('无权限访问', 403);
        }
        redirect('admin/index.php');
    }
}

/**
 * 判断AJAX请求
 */
function is_ajax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * 生成随机验证码
 */
function generate_code($length = 6) {
    return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

/**
 * 格式化日期
 */
function format_date($date, $format = 'Y-m-d H:i:s') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

/**
 * 截取字符串
 */
function truncate($str, $length = 100, $suffix = '...') {
    if (mb_strlen($str) > $length) {
        return mb_substr($str, 0, $length) . $suffix;
    }
    return $str;
}

/**
 * 获取客户端IP
 */
function get_client_ip() {
    $ip = '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/**
 * 上传文件基础处理
 */
function handle_upload($file, $allowed_types = ['jpg','jpeg','png','gif','svg','webp'], $max_size = 5242880) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => '上传失败: ' . $file['error']];
    }

    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => '文件大小超出限制(最大5MB)'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) {
        return ['success' => false, 'message' => '不支持的文件类型'];
    }

    $filename = uniqid('img_') . '.' . $ext;
    $relative_path = 'uploads/images/' . $filename;
    $full_path = YUYUN_ROOT . '/' . $relative_path;

    if (!is_dir(dirname($full_path))) {
        mkdir(dirname($full_path), 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $full_path)) {
        return ['success' => true, 'path' => '/' . $relative_path, 'filename' => $filename];
    }

    return ['success' => false, 'message' => '文件保存失败'];
}

/**
 * 获取当前模板路径
 */
function get_current_template() {
    $template = get_config('template') ?: 'default';
    $templatePath = TEMPLATES_PATH . $template;
    if (!is_dir($templatePath)) {
        $templatePath = TEMPLATES_PATH . 'default';
    }
    return $templatePath;
}

/**
 * 获取模板URL前缀
 */
function get_template_url() {
    $template = get_config('template') ?: 'default';
    return '/templates/' . $template . '/';
}

/**
 * 分页处理
 */
function paginate($items, $page = 1, $per_page = 15) {
    $total = count($items);
    $total_pages = ceil($total / $per_page);
    $page = max(1, min($page, $total_pages));
    $offset = ($page - 1) * $per_page;

    return [
        'items' => array_slice($items, $offset, $per_page),
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_items' => $total,
        'per_page' => $per_page
    ];
}

/**
 * 记录日志
 */
function log_message($message, $level = 'info') {
    $log_file = YUYUN_ROOT . '/data/logs/' . date('Y-m-d') . '.log';
    $log_dir = dirname($log_file);
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $entry = "[" . date('Y-m-d H:i:s') "] [$level] $message" . PHP_EOL;
    file_put_contents($log_file, $entry, FILE_APPEND | LOCK_EX);
}
