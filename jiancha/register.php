<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
if (isLoggedIn()) redirect('user.php');
$error = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf()) { $error = '非法请求'; }
    else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $email = trim($_POST['email'] ?? '');
        if (!$username || !$password || !$confirm) { $error = '请填写必填字段'; }
        elseif (strlen($username) < 3 || strlen($username) > 20) { $error = '用户名3-20个字符'; }
        elseif (!preg_match('/^[\w\x{4e00}-\x{9fa5}]+$/u', $username)) { $error = '用户名含非法字符'; }
        elseif (strlen($password) < 6) { $error = '密码至少6位'; }
        elseif (preg_match('/^\d+$/', $password)) { $error = '密码不能纯数字'; }
        elseif ($password !== $confirm) { $error = '两次密码不一致'; }
        elseif ($email && !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) { $error = '邮箱格式错误'; }
        elseif (DB::fetchOne("SELECT id FROM users WHERE username=?", [$username])) { $error = '用户名已存在'; }
        elseif ($email && DB::fetchOne("SELECT id FROM users WHERE email=?", [$email])) { $error = '邮箱已被注册'; }
        else {
            DB::insert('users', ['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT), 'email' => $email, 'nickname' => $username, 'role' => 'user', 'create_time' => date('Y-m-d H:i:s')]);
            $ok = '注册成功，请登录';
        }
    }
}
$pageTitle = '注册';
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo e($pageTitle); ?> - <?php echo e(siteName()); ?></title>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body>
<div class="auth-page">
<div class="auth-card">
<div class="auth-card-head"><h2>用户注册</h2></div>
<div class="auth-card-body">
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
<?php if ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?> <a href="login.php">去登录</a></div><?php endif; ?>
<form method="post">
<?php echo csrfField(); ?>
<div class="auth-field"><label>用户名 <span class="req">*</span></label><input type="text" name="username" data-v="username" required></div>
<div class="auth-field"><label>密码 <span class="req">*</span></label><input type="password" name="password" data-v="password" required></div>
<div class="auth-field"><label>确认密码 <span class="req">*</span></label><input type="password" name="confirm_password" data-v="confirm_password" required></div>
<div class="auth-field"><label>邮箱</label><input type="email" name="email" data-v="email"></div>
<div class="auth-field"><button type="submit" class="btn btn-primary btn-block">注 册</button></div>
</form>
<p style="text-align:center;font-size:13px;color:var(--text2)">已有账号？<a href="login.php">立即登录</a></p>
</div></div></div>
<script src="<?php echo SITE_URL; ?>assets/js/main.js"></script>
</body>
</html>