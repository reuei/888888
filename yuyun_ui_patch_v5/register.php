<?php
require __DIR__ . '/includes/config.php';
if (template_include('register.php')) exit;
$pageTitle = __('page_register');
ensure_user_columns();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $nickname = trim($_POST['nickname'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) flash('error', __('email_invalid'));
    elseif (strlen($password) < 6) flash('error', __('password_short'));
    elseif ($password !== $password2) flash('error', __('password_mismatch'));
    else {
        $db = getDb();
        $check = $db->prepare('SELECT id FROM users WHERE email = :e');
        $check->execute([':e' => $email]);
        if ($check->fetch()) {
            flash('error', __('email_exists'));
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $now = date('Y-m-d H:i:s');
            $emailVerify = setting('email_verify_enabled','0') === '1';
            $verified = $emailVerify ? 0 : 1;
            $stmt = $db->prepare('INSERT INTO users (email, password, nickname, email_verified, created_at) VALUES (:e,:p,:n,:v,:t)');
            $stmt->execute([':e'=>$email,':p'=>$hash,':n'=>$nickname,':v'=>$verified,':t'=>$now]);
            if ($emailVerify) {
                flash('success', __('register_success_verify'));
                redirect(YUYUN_URL . '/verify.php');
            }
            $_SESSION['user_id'] = $db->lastInsertId();
            flash('success', __('register_success'));
            redirect(YUYUN_URL . '/user/index.php');
        }
    }
    redirect(YUYUN_URL . '/register.php');
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <div class="auth-box">
        <div class="text-center">
            <div class="ip-illustration" style="width:90px;height:90px;margin-bottom:12px"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="24" cy="16" r="9"/><path d="M6 44c0-12 9-18 18-18s18 6 18 18"/><path d="M36 8h8M40 4v8"/></svg></div>
        </div>
        <h2><?php echo __('register_account') ?></h2>
        <p><?php echo __('register_subtitle') ?></p>
        <?php echo render_flash() ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <div class="form-group"><label><i class="iconfont icon-envelope"></i> <?php echo __('email') ?></label><input type="email" name="email" class="form-control" required></div>
            <div class="form-group"><label><i class="iconfont icon-user"></i> <?php echo __('nickname') ?></label><input type="text" name="nickname" class="form-control" required></div>
            <div class="form-row">
                <div class="form-group"><label><i class="iconfont icon-lock"></i> <?php echo __('password') ?></label><input type="password" name="password" class="form-control" required minlength="6"></div>
                <div class="form-group"><label><i class="iconfont icon-lock"></i> <?php echo __('confirm_password') ?></label><input type="password" name="password2" class="form-control" required minlength="6"></div>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="width:100%"><i class="iconfont icon-user"></i> <?php echo __('register') ?></button>
        </form>
        <p style="text-align:center;margin-top:18px;font-size:14px;color:var(--text-2)">
            <?php echo __('has_account') ?><a href="<?php echo YUYUN_URL ?>/login.php" style="color:var(--brand)"><?php echo __('login_now') ?></a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
