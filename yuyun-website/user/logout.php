<?php
/**
 * 语云科技 - 用户退出登录
 */
session_start();
require_once __DIR__ . '/../core/Functions.php';

// 清除所有Session变量
$_SESSION = array();

// 销毁Session
if (session_id()) {
    session_destroy();
}

// 删除Session Cookie（如果存在）
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// 跳转到首页或登录页
$redirect_url = $_GET['redirect'] ?? '../index.php';
header('Location: ' . $redirect_url);
exit;
?>
