<?php
/**
 * 语云科技 - 用户认证API
 * 支持登录、注册、验证码登录、管理员登录等操作
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_once YUYUN_ROOT . '/core/Auth.php';

// 设置响应头
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// 预检请求处理
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$auth = new Auth();

switch ($action) {
    case 'login':
        // 密码登录
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            error('请填写邮箱和密码');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error('邮箱格式不正确');
        }

        $result = $auth->login($email, $password);
        if ($result['success']) {
            success($result['user'], $result['message']);
        } else {
            error($result['message']);
        }
        break;

    case 'register':
        // 用户注册
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $name = trim($_POST['name'] ?? '');

        if (empty($email) || empty($password)) {
            error('请填写邮箱和密码');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error('邮箱格式不正确');
        }

        if (strlen($password) < 6) {
            error('密码长度至少6位');
        }

        // CSRF验证（注册需要）
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            error('CSRF验证失败', 403);
        }

        $result = $auth->register($email, $password, $name);
        if ($result['success']) {
            success(['user_id' => $result['user_id']], $result['message']);
        } else {
            error($result['message']);
        }
        break;

    case 'email-code':
        // 发送验证码
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            error('请填写邮箱地址');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error('邮箱格式不正确');
        }

        $result = $auth->sendEmailCode($email);
        if ($result['success']) {
            success(null, $result['message']);
        } else {
            error($result['message']);
        }
        break;

    case 'email-login':
        // 验证码登录
        $email = trim($_POST['email'] ?? '');
        $code = trim($_POST['code'] ?? '');

        if (empty($email) || empty($code)) {
            error('请填写邮箱和验证码');
        }

        $result = $auth->emailCodeLogin($email, $code);
        if ($result['success']) {
            success($result['user'], $result['message']);
        } else {
            error($result['message']);
        }
        break;

    case 'admin-login':
        // 管理员登录
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            error('请填写用户名和密码');
        }

        $result = $auth->adminLogin($username, $password);
        if ($result['success']) {
            success([
                'redirect' => 'dashboard.php'
            ], $result['message']);
        } else {
            error($result['message']);
        }
        break;

    case 'logout':
        // 退出登录
        $auth->logout();
        success(null, '已成功退出登录');
        break;

    default:
        error('未知操作类型');
}
