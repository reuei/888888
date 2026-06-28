<?php
/**
 * 全局辅助函数
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

/**
 * 记录管理员操作日志
 */
function admin_log($action, $content = '')
{
    try {
        $admin = session('admin_user') ?? [];
        Db::insert('jz_admin_log', [
            'admin_id' => $admin['id'] ?? 0,
            'admin_name' => $admin['username'] ?? '',
            'action' => $action,
            'content' => is_array($content) || is_object($content) ? json_encode($content, JSON_UNESCAPED_UNICODE) : (string) $content,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'create_time' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $e) {
        // 忽略日志写入失败，避免影响主流程
    }
}

/**
 * 读取站点配置
 */
function site_config($key = null, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = [];
        try {
            $rows = Db::query("SELECT cfg_key, cfg_value FROM jz_config WHERE cfg_group = 'base'");
            foreach ($rows as $row) {
                $shortKey = substr($row['cfg_key'], 5);
                $config[$shortKey] = $row['cfg_value'];
            }
        } catch (Exception $e) {
            // 安装前或未初始化时忽略
        }
    }

    if ($key === null) {
        return $config;
    }

    $defaults = [
        'site_name' => '鲸商城 Pro',
        'copyright' => '鲸商城 Pro v1.0.0',
    ];

    if (!isset($config[$key]) && isset($defaults[$key])) {
        return $defaults[$key];
    }

    return $config[$key] ?? $default;
}
