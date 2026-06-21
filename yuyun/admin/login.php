<?php
require __DIR__ . '/../includes/config.php';
if (is_admin()) redirect(YUYUN_URL . '/admin/index.php');
$pageTitle = '后台登录';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = :e LIMIT 1');
    $stmt->execute([':e'=>$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['is_admin'] && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        redirect(YUYUN_URL . '/admin/index.php');
    } else {
        flash('error', '账号或密码错误，或无管理员权限');
        redirect(YUYUN_URL . '/admin/login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台登录 - 语云科技</title>
    <link rel="stylesheet" href="<?php echo YUYUN_URL ?>/assets/css/iconfont.css">
    <link rel="stylesheet" href="<?php echo YUYUN_URL ?>/assets/css/style.css">
</head>
<body>
<section class="auth-page" style="background:linear-gradient(135deg,#0f172a,#1e293b);min-height:100vh">
    <div class="auth-box" style="background:rgba(255,255,255,.98);backdrop-filter:blur(10px)">
        <div class="text-center">
            <div class="illustration-3d" style="width:90px;height:90px;margin-bottom:12px">
                <div class="cube" style="width:40px;height:40px;left:25px;top:25px"><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div></div>
            </div>
        </div>
        <h2 style="text-align:center"><i class="iconfont icon-cloud" style="color:var(--brand)"></i> 语云后台</h2>
        <p style="text-align:center">管理员登录</p>
        <?php echo render_flash() ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <div class="form-group"><label><i class="iconfont icon-envelope"></i> 管理员邮箱</label><input type="email" name="email" class="form-control" required></div>
            <div class="form-group"><label><i class="iconfont icon-lock"></i> 密码</label><input type="password" name="password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary btn-block" style="width:100%"><i class="iconfont icon-user"></i> 登录</button>
        </form>
        <p style="text-align:center;margin-top:18px;font-size:14px;color:var(--text-2)">
            <a href="<?php echo YUYUN_URL ?>/index.php" style="color:var(--brand)">返回首页</a>
        </p>
    </div>
</section>
</body>
</html>
