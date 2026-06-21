<?php
require __DIR__ . '/includes/config.php';
if (template_include('register.php')) exit;
$pageTitle = '用户注册';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $nickname = trim($_POST['nickname'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) flash('error', '邮箱格式不正确');
    elseif (strlen($password) < 6) flash('error', '密码至少 6 位');
    elseif ($password !== $password2) flash('error', '两次密码不一致');
    else {
        $db = getDb();
        $check = $db->prepare('SELECT id FROM users WHERE email = :e');
        $check->execute([':e' => $email]);
        if ($check->fetch()) {
            flash('error', '该邮箱已注册');
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $now = date('Y-m-d H:i:s');
            $stmt = $db->prepare('INSERT INTO users (email, password, nickname, email_verified, created_at) VALUES (:e,:p,:n,1,:t)');
            $stmt->execute([':e'=>$email,':p'=>$hash,':n'=>$nickname,':t'=>$now]);
            $_SESSION['user_id'] = $db->lastInsertId();
            flash('success', '注册成功');
            redirect(YUYUN_URL . '/user/index.php');
        }
    }
    redirect(YUYUN_URL . '/register.php');
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <div class="auth-box">
        <h2>创建账号</h2>
        <p>注册后即可提交工单与建议</p>
        <?php echo render_flash() ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <div class="form-group"><label>邮箱</label><input type="email" name="email" class="form-control" required></div>
            <div class="form-group"><label>昵称</label><input type="text" name="nickname" class="form-control" required></div>
            <div class="form-row">
                <div class="form-group"><label>密码</label><input type="password" name="password" class="form-control" required minlength="6"></div>
                <div class="form-group"><label>确认密码</label><input type="password" name="password2" class="form-control" required minlength="6"></div>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="width:100%">注册</button>
        </form>
        <p style="text-align:center;margin-top:18px;font-size:14px;color:var(--text-2)">
            已有账号？<a href="<?php echo YUYUN_URL ?>/login.php" style="color:var(--brand)">立即登录</a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
