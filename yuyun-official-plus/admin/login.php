<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';

if (!defined('INSTALLED') || !INSTALLED) {
    header('Location: ../install.php');
    exit;
}

require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';

if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) {
        $error = '请填写账号和密码';
    } elseif (!adminLogin($username, $password)) {
        $error = '账号或密码错误';
    } else {
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录 - <?php echo yy_e(getSetting('site_title', '语云科技')); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="login-body">
<div class="login-box">
    <h1><i class="fa-solid fa-cloud"></i> 语云后台登录</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger" style="margin-bottom:16px;"><?php echo yy_e($error); ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="form-group">
            <label>管理员账号</label>
            <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="form-group">
            <label>管理员密码</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">登录</button>
    </form>
</div>
</body>
</html>
