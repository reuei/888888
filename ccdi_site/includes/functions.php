<?php
/**
 * 通用函数库
 */
if (!defined('SYSTEM_INIT')) { die('未经授权的访问'); }

/**
 * 安全过滤输入
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * 获取 POST 数据
 */
function post($key, $default = '') {
    return isset($_POST[$key]) ? sanitize($_POST[$key]) : $default;
}

/**
 * 获取 GET 数据
 */
function get($key, $default = '') {
    return isset($_GET[$key]) ? sanitize($_GET[$key]) : $default;
}

/**
 * 生成 CSRF Token
 */
function csrf_token() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * 验证 CSRF Token
 */
function csrf_verify($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * 生成 CSRF 隐藏域
 */
function csrf_field() {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . csrf_token() . '">';
}

/**
 * 重定向
 */
function redirect($url) {
    if (!headers_sent()) {
        header('Location: ' . $url);
    } else {
        echo '<script>window.location.href="' . $url . '";</script>';
    }
    exit;
}

/**
 * 获取当前URL
 */
function current_url() {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * 生成URL
 */
function site_url($path = '') {
    return SITE_FULL_URL . '/' . ltrim($path, '/');
}

/**
 * 生成管理员URL
 */
function admin_url($path = '') {
    return SITE_FULL_URL . '/admin/' . ltrim($path, '/');
}

/**
 * 格式化时间
 */
function format_time($time, $format = 'Y-m-d H:i') {
    if (empty($time)) return '';
    $timestamp = is_numeric($time) ? $time : strtotime($time);
    return date($format, $timestamp);
}

/**
 * 格式化友好的时间
 */
function time_ago($time) {
    $timestamp = is_numeric($time) ? $time : strtotime($time);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return '刚刚';
    if ($diff < 3600) return floor($diff / 60) . '分钟前';
    if ($diff < 86400) return floor($diff / 3600) . '小时前';
    if ($diff < 2592000) return floor($diff / 86400) . '天前';
    if ($diff < 31536000) return floor($diff / 2592000) . '个月前';
    return floor($diff / 31536000) . '年前';
}

/**
 * 截取字符串
 */
function str_cut($str, $len = 100, $suffix = '...') {
    $str = strip_tags($str);
    if (mb_strlen($str, 'UTF-8') <= $len) return $str;
    return mb_substr($str, 0, $len, 'UTF-8') . $suffix;
}

/**
 * 生成分页HTML
 */
function pagination($total, $current, $base_url, $per_page = ITEMS_PER_PAGE) {
    $total_pages = ceil($total / $per_page);
    if ($total_pages <= 1) return '';
    
    $current = max(1, min($current, $total_pages));
    $html = '<nav class="pagination"><ul>';
    
    // 上一页
    if ($current > 1) {
        $html .= '<li><a href="' . $base_url . 'page=' . ($current - 1) . '">&laquo; 上一页</a></li>';
    }
    
    // 页码
    $start = max(1, $current - 2);
    $end = min($total_pages, $current + 2);
    
    if ($start > 1) {
        $html .= '<li><a href="' . $base_url . 'page=1">1</a></li>';
        if ($start > 2) $html .= '<li><span>...</span></li>';
    }
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current) {
            $html .= '<li class="active"><span>' . $i . '</span></li>';
        } else {
            $html .= '<li><a href="' . $base_url . 'page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) $html .= '<li><span>...</span></li>';
        $html .= '<li><a href="' . $base_url . 'page=' . $total_pages . '">' . $total_pages . '</a></li>';
    }
    
    // 下一页
    if ($current < $total_pages) {
        $html .= '<li><a href="' . $base_url . 'page=' . ($current + 1) . '">下一页 &raquo;</a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

/**
 * 上传文件
 */
function upload_file($file, $subdir = '') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['error' => '没有选择文件'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => '上传错误代码：' . $file['error']];
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['error' => '文件大小超过限制（最大' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB）'];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = explode(',', ALLOWED_EXTENSIONS);
    if (!in_array($ext, $allowed)) {
        return ['error' => '不支持的文件类型：.' . $ext];
    }
    
    $upload_dir = UPLOADS_PATH . ($subdir ? trim($subdir, '/') . '/' : '');
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $new_name = date('Ymd') . '_' . substr(md5(uniqid()), 0, 10) . '.' . $ext;
    $dest = $upload_dir . $new_name;
    
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return [
            'success' => true,
            'filename' => $new_name,
            'path' => ($subdir ? trim($subdir, '/') . '/' : '') . $new_name,
            'size' => $file['size'],
            'ext' => $ext
        ];
    }
    
    return ['error' => '文件移动失败'];
}

/**
 * 获取网站配置
 */
function site_config($key, $default = '') {
    return get_site_config($key, $default);
}

/**
 * 获取分类列表
 */
function get_categories($parent_id = 0) {
    return db_fetch_all("SELECT * FROM categories WHERE parent_id = ? AND status = 1 ORDER BY sort_order ASC, id ASC", [$parent_id]);
}

/**
 * 获取文章列表
 */
function get_articles($category_id = 0, $limit = 10, $offset = 0, $is_top = null) {
    $where = "status = 'publish'";
    $params = [];
    
    if ($category_id > 0) {
        $where .= " AND category_id = ?";
        $params[] = $category_id;
    }
    
    if ($is_top !== null) {
        $where .= " AND is_top = ?";
        $params[] = $is_top ? 1 : 0;
    }
    
    $sql = "SELECT * FROM articles WHERE {$where} ORDER BY is_top DESC, publish_time DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return db_fetch_all($sql, $params);
}

/**
 * 获取文章详情
 */
function get_article($id) {
    if (is_numeric($id)) {
        return db_fetch("SELECT * FROM articles WHERE id = ? AND status = 'publish'", [$id]);
    }
    return db_fetch("SELECT * FROM articles WHERE slug = ? AND status = 'publish'", [$id]);
}

/**
 * 获取轮播图列表（支持类型筛选）
 */
function get_carousel($type = '') {
    if ($type) {
        return db_fetch_all("SELECT * FROM carousel WHERE status = 1 AND type = ? ORDER BY sort_order ASC, id DESC", [$type]);
    }
    return db_fetch_all("SELECT * FROM carousel WHERE status = 1 ORDER BY sort_order ASC, id DESC");
}

/**
 * 获取B2弹窗
 */
function get_popup() {
    return db_fetch("SELECT * FROM popups WHERE status = 1 AND (start_time IS NULL OR start_time <= datetime('now','localtime')) AND (end_time IS NULL OR end_time >= datetime('now','localtime')) ORDER BY id DESC LIMIT 1");
}

/**
 * 记录日志
 */
function add_log($action, $description = '') {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    db_insert('system_logs', [
        'user_id' => $user_id,
        'action' => $action,
        'description' => $description,
        'ip_address' => $ip,
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

/**
 * JSON 响应
 */
function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 验证邮箱格式
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * 验证用户名
 */
function is_valid_username($username) {
    return preg_match('/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]{3,20}$/u', $username);
}

/**
 * 验证密码强度
 */
function is_strong_password($password) {
    return strlen($password) >= 6 && strlen($password) <= 50;
}

/**
 * 获取预载图片
 */
function get_preloader_image() {
    $img = site_config('preloader_image', '');
    if ($img && file_exists(UPLOADS_PATH . $img)) {
        return SITE_FULL_URL . '/uploads/' . $img;
    }
    return '';
}

/**
 * 获取页脚机关图片
 */
function get_footer_image() {
    $img = site_config('footer_image', '');
    if ($img && file_exists(UPLOADS_PATH . $img)) {
        return SITE_FULL_URL . '/uploads/' . $img;
    }
    return '';
}

/**
 * 获取横幅图片
 */
function get_banner_image() {
    $img = site_config('banner_image', '');
    if ($img && file_exists(UPLOADS_PATH . $img)) {
        return SITE_FULL_URL . '/uploads/' . $img;
    }
    return '';
}

/**
 * 获取页脚轮播图列表
 */
function get_footer_carousel() {
    return db_fetch_all("SELECT * FROM footer_carousel WHERE status = 1 ORDER BY sort_order ASC, id DESC");
}

/**
 * 获取视频列表
 */
function get_videos($limit = 12, $category_id = 0) {
    $sql = "SELECT * FROM videos WHERE status = 1";
    $params = [];
    if ($category_id > 0) {
        $sql .= " AND category_id = ?";
        $params[] = $category_id;
    }
    $sql .= " ORDER BY sort_order ASC, id DESC LIMIT ?";
    $params[] = $limit;
    return db_fetch_all($sql, $params);
}

/**
 * 获取工作人员列表
 */
function get_staff($limit = 20) {
    return db_fetch_all("SELECT * FROM staff WHERE status = 1 ORDER BY sort_order ASC, id ASC LIMIT ?", [$limit]);
}

