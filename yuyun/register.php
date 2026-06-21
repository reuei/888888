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
        $check = $db->prepare('SELECT id, email_verified FROM users WHERE email = :e');
        $check->execute([':e' => $email]);
        $existing = $check->fetch(PDO::FETCH_ASSOC);
        if ($existing && !empty($existing['email_verified'])) {
            flash('error', '该邮箱已注册');
            redirect(YUYUN_URL . '/register.php');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $now = date('Y-m-d H:i:s');
        $emailVerify = setting('site_email_verify');
        if ($emailVerify) {
            $code = generate_code();
            $expire = time() + 300;
            if ($existing) {
                $db->prepare('UPDATE users SET password=:p, nickname=:n, verify_code=:c, code_expire=:ex WHERE id=:id')->execute([':p'=>$hash,':n'=>$nickname,':c'=>$code,':ex'=>$expire,':id'=>$existing['id']]);
            } else {
                $stmt = $db->prepare('INSERT INTO users (email, password, nickname, email_verified, verify_code, code_expire, created_at) VALUES (:e,:p,:n,0,:c,:ex,:t)');
                $stmt->execute([':e'=>$email,':p'=>$hash,':n'=>$nickname,':c'=>$code,':ex'=>$expire,':t'=>$now]);
            }
            send_verify_email($email, $code);
            flash('success', '验证码已发送至邮箱，请完成验证');
            redirect(YUYUN_URL . '/verify.php?mode=verify_email&email=' . urlencode($email));
        } else {
            if ($existing) {
                $db->prepare('UPDATE users SET password=:p, nickname=:n, email_verified=1, verify_code=NULL, code_expire=NULL WHERE id=:id')->execute([':p'=>$hash,':n'=>$nickname,':id'=>$existing['id']]);
                $_SESSION['user_id'] = $existing['id'];
            } else {
                $stmt = $db->prepare('INSERT INTO users (email, password, nickname, email_verified, created_at) VALUES (:e,:p,:n,1,:t)');
                $stmt->execute([':e'=>$email,':p'=>$hash,':n'=>$nickname,':t'=>$now]);
                $_SESSION['user_id'] = $db->lastInsertId();
            }
            flash('success', '注册成功');
            redirect(YUYUN_URL . '/user/index.php');
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <div class="auth-box">
        <div class="text-center">
            <div class="illustration-3d" style="width:90px;height:90px;margin-bottom:12px">
                <div class="cube" style="width:40px;height:40px;left:25px;top:25px"><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div></div>
            </div>
        </div>
        <h2>创建账号</h2>
        <p>注册后即可提交工单与建议</p>
        <?php echo render_flash() ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <div class="form-group"><label><i class="iconfont icon-envelope"></i> 邮箱</label><input type="email" name="email" class="form-control" required></div>
            <div class="form-group"><label><i class="iconfont icon-user"></i> 昵称</label><input type="text" name="nickname" class="form-control" required></div>
            <div class="form-row">
                <div class="form-group"><label><i class="iconfont icon-lock"></i> 密码</label><input type="password" name="password" class="form-control" required minlength="6"></div>
                <div class="form-group"><label><i class="iconfont icon-lock"></i> 确认密码</label><input type="password" name="password2" class="form-control" required minlength="6"></div>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="width:100%"><i class="iconfont icon-user"></i> 注册</button>
        </form>
        <p style="text-align:center;margin-top:18px;font-size:14px;color:var(--text-2)">
            已有账号？<a href="<?php echo YUYUN_URL ?>/login.php" style="color:var(--brand)">立即登录</a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
