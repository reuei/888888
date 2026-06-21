<?php
require __DIR__ . '/includes/config.php';
if (template_include('login.php')) exit;
$pageTitle = '用户登录';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = :e LIMIT 1');
    $stmt->execute([':e' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['password'] && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        flash('success', '登录成功');
        redirect($user['is_admin'] ? YUYUN_URL . '/admin/index.php' : YUYUN_URL . '/user/index.php');
    } else {
        flash('error', '邮箱或密码错误');
        redirect(YUYUN_URL . '/login.php');
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <div class="auth-box" style="position:relative;overflow:hidden">
        <div class="text-center">
            <div class="illustration-3d" style="width:90px;height:90px;margin-bottom:12px">
                <div class="cube" style="width:40px;height:40px;left:25px;top:25px"><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div></div>
            </div>
        </div>
        <h2>欢迎回来</h2>
        <p>登录语云科技用户中心</p>
        <div class="auth-tabs">
            <a href="<?php echo YUYUN_URL ?>/login.php" class="active">密码登录</a>
            <a href="<?php echo YUYUN_URL ?>/verify.php">邮箱验证码登录</a>
        </div>
        <?php echo render_flash() ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <div class="form-group"><label><i class="iconfont icon-envelope"></i> 邮箱</label><input type="email" name="email" class="form-control" required></div>
            <div class="form-group"><label><i class="iconfont icon-lock"></i> 密码</label><input type="password" name="password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary btn-block" style="width:100%"><i class="iconfont icon-user"></i> 登录</button>
        </form>
        <p style="text-align:center;margin-top:18px;font-size:14px;color:var(--text-2)">
            还没有账号？<a href="<?php echo YUYUN_URL ?>/register.php" style="color:var(--brand)">立即注册</a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
