<?php
/**
 * QEEFG 授权站全局辅助函数
 */

function url($path = '', $params = [])
{
    $base = '/';
    if ($path) {
        $base .= ltrim($path, '/');
    }
    if (!empty($params)) {
        $base .= '?' . http_build_query($params);
    }
    return $base;
}

function base_url($path = '')
{
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = $scheme . '://' . $host;
    return $base . '/' . ltrim($path, '/');
}

function current_url()
{
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return $scheme . '://' . $host . $uri;
}

function input($key = null, $default = null)
{
    if ($key === null) {
        return $_REQUEST;
    }
    return $_REQUEST[$key] ?? $default;
}

function session($key = null, $value = null)
{
    if ($key === null) {
        return $_SESSION;
    }
    if ($value === null) {
        return $_SESSION[$key] ?? null;
    }
    $_SESSION[$key] = $value;
}

function json_success($msg = '操作成功', $data = [], $code = 0)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]);
    exit;
}

function json_error($msg = '操作失败', $code = 1, $data = [])
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]);
    exit;
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function password_hash_custom($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function password_verify_custom($password, $hash)
{
    return password_verify($password, $hash);
}

function generate_token($length = 32)
{
    return bin2hex(random_bytes($length / 2));
}

function admin_log($action, $content = '')
{
    try {
        $admin = session('admin_user') ?? [];
        Db::insert('qef_admin_log', [
            'admin_id' => $admin['id'] ?? 0,
            'admin_name' => $admin['username'] ?? '',
            'action' => $action,
            'content' => is_array($content) || is_object($content) ? json_encode($content, JSON_UNESCAPED_UNICODE) : (string) $content,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'create_time' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $e) {
    }
}

function get_admin_user()
{
    return session('admin_user');
}

function require_admin_login()
{
    if (empty(session('admin_user'))) {
        redirect(url('admin/admin'));
    }
}

function require_user_login()
{
    if (empty(session('user'))) {
        redirect(url('login'));
    }
}

function get_user()
{
    return session('user');
}

function site_config($key = null, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = [];
        try {
            $rows = Db::query("SELECT cfg_key, cfg_value FROM qef_config WHERE cfg_group = 'base'");
            foreach ($rows as $row) {
                $config[$row['cfg_key']] = $row['cfg_value'];
            }
        } catch (Exception $e) {
        }
    }

    if ($key === null) {
        return $config;
    }

    $defaults = [
        'site_name' => 'QEEFG 寄售系统售卖网站',
        'currency_unit' => '¥',
        'copyright' => 'QEEFG v1.0.0',
    ];

    if (!isset($config[$key]) && isset($defaults[$key])) {
        return $defaults[$key];
    }

    return $config[$key] ?? $default;
}

function pagination($total, $page, $pageSize, $urlTemplate)
{
    $total = max(0, (int) $total);
    $page = max(1, (int) $page);
    $pageSize = max(1, (int) $pageSize);
    $totalPages = max(1, (int) ceil($total / $pageSize));
    $page = min($page, $totalPages);

    $html = '<div class="pagination">';
    if ($page > 1) {
        $html .= '<a href="' . str_replace('{page}', $page - 1, $urlTemplate) . '" class="btn btn-sm btn-outline">上一页</a>';
    }
    $html .= '<span class="page-info">第 ' . $page . ' / ' . $totalPages . ' 页，共 ' . $total . ' 条</span>';
    if ($page < $totalPages) {
        $html .= '<a href="' . str_replace('{page}', $page + 1, $urlTemplate) . '" class="btn btn-sm btn-outline">下一页</a>';
    }
    $html .= '</div>';
    return $html;
}

function get_client_ip()
{
    $keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(strtok($_SERVER[$key], ','));
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
            return $ip;
        }
    }
    return '0.0.0.0';
}

function upload_file($field, $subDir = '', $allowedExt = ['jpg', 'jpeg', 'png', 'gif'])
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return ['code' => 1, 'msg' => '文件上传失败'];
    }

    $file = $_FILES[$field];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return ['code' => 1, 'msg' => '不支持的文件格式'];
    }

    $maxSize = 20 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['code' => 1, 'msg' => '文件大小不能超过20MB'];
    }

    $safeSubDir = '';
    if ($subDir) {
        $parts = explode('/', trim(str_replace('\\', '/', $subDir), '/'));
        $safeParts = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '' || $part === '.' || $part === '..') {
                continue;
            }
            $safeParts[] = $part;
        }
        $safeSubDir = implode(DIRECTORY_SEPARATOR, $safeParts);
    }

    $uploadRoot = ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
    $targetDir = $uploadRoot . ($safeSubDir ? $safeSubDir . DIRECTORY_SEPARATOR : '');
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }

    $filename = date('Ymd') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $targetPath = $targetDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['code' => 1, 'msg' => '文件保存失败'];
    }

    $relative = 'uploads/' . ($safeSubDir ? str_replace('\\', '/', $safeSubDir) . '/' : '') . $filename;
    return ['code' => 0, 'path' => $relative, 'url' => base_url($relative)];
}

function upload_zip($field, $subDir = '')
{
    return upload_file($field, $subDir, ['zip']);
}

function generate_order_no()
{
    return 'QEF' . date('YmdHis') . random_int(1000, 9999);
}

function generate_auth_code()
{
    return strtoupper(substr(bin2hex(random_bytes(16)), 0, 24));
}

function api_key()
{
    return site_config('api_key', '');
}

function api_sign(array $params, $key = null)
{
    unset($params['sign']);
    ksort($params);
    $str = http_build_query($params);
    return hash_hmac('sha256', $str, $key ?: api_key());
}

function verify_api_sign(array $params, $key = null)
{
    $sign = $params['sign'] ?? '';
    $expected = api_sign($params, $key);
    return hash_equals(strtolower($expected), strtolower($sign));
}

function format_price($price)
{
    return site_config('currency_unit', '¥') . number_format((float) $price, 2);
}

function status_text($status, $map)
{
    return $map[$status] ?? '未知';
}

function format_size($size)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $unitIndex = 0;
    $size = max(0, (int) $size);
    while ($size >= 1024 && $unitIndex < count($units) - 1) {
        $size /= 1024;
        $unitIndex++;
    }
    return round($size, 2) . ' ' . $units[$unitIndex];
}

function download_token($file, $expires = 3600)
{
    $expiresAt = time() + $expires;
    $data = $file . '|' . $expiresAt;
    $sign = hash_hmac('sha256', $data, api_key());
    return base64_encode($data . '|' . $sign);
}

function verify_download_token($token)
{
    $raw = base64_decode($token, true);
    if (!$raw || substr_count($raw, '|') !== 2) {
        return false;
    }
    [$file, $expiresAt, $sign] = explode('|', $raw);
    $expected = hash_hmac('sha256', $file . '|' . $expiresAt, api_key());
    if (!hash_equals($expected, $sign)) {
        return false;
    }
    if ((int) $expiresAt < time()) {
        return false;
    }
    return $file;
}
