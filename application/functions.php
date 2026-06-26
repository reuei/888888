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
