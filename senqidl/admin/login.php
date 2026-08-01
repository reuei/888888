<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/Auth.php';

if (Auth::isLoggedIn()) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        if (Auth::login($username, $password)) {
            redirect(SITE_URL . '/admin/dashboard.php');
        } else {
            $error = '用户名或密码错误';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录 - <?php echo h(DB::getSettingValue('site_name', SITE_NAME)); ?></title>
    <link rel="icon" href="<?php echo h(DB::getSettingValue('favicon', '/assets/images/favicon.ico')); ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/admin/assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <a href="<?php echo siteUrl(); ?>" class="logo">
                <span class="logo-icon">森</span>
                <span class="logo-text"><?php echo h(DB::getSettingValue('site_name', SITE_NAME)); ?></span>
            </a>
            <h1>管理后台</h1>
            <p>请登录以继续</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="alert-icon">⚠</i>
            <?php echo h($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="username">用户名</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="请输入用户名" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label" for="password">密码</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="请输入密码" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">登 录</button>
        </form>

        <div class="login-footer">
            <a href="<?php echo siteUrl(); ?>">← 返回首页</a>
        </div>
    </div>
</body>
</html>