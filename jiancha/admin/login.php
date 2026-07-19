<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (isLoggedIn()) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf()) { $error = '非法请求'; }
    else {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';
        $user = DB::fetchOne("SELECT * FROM users WHERE username=? OR email=?", [$u, $u]);
        if ($user && password_verify($p, $user['password']) && in_array($user['role'], ['admin', 'super_admin'])) {
            $_SESSION['user_id'] = $user['id'];
            redirect('index.php');
        } else { $error = '用户名或密码错误，或不是管理员'; }
    }
}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>后台登录 - <?php echo e(siteName()); ?></title>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body>
<div class="auth-page">
<div class="auth-card">
<div class="auth-card-head"><h2>后台管理</h2></div>
<div class="auth-card-body">
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
<form method="post">
<?php echo csrfField(); ?>
<div class="auth-field"><label>管理员账号</label><input type="text" name="username" required autofocus></div>
<div class="auth-field"><label>密码</label><input type="password" name="password" required></div>
<div class="auth-field"><button type="submit" class="btn btn-primary btn-block">登 录</button></div>
</form>
</div></div></div>
<script src="<?php echo SITE_URL; ?>assets/js/main.js"></script>
</body>
</html>