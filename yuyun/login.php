<?php
require __DIR__ . '/includes/config.php';
if (template_include('login.php')) exit;
$pageTitle = __('page_login');
ensure_user_columns();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = :e LIMIT 1');
    $stmt->execute([':e' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['password'] && password_verify($password, $user['password'])) {
        if (setting('email_verify_enabled','0') === '1' && empty($user['email_verified'])) {
            flash('error', __('email_not_verified'));
            redirect(YUYUN_URL . '/verify.php');
        }
        $_SESSION['user_id'] = $user['id'];
        flash('success', __('login_success'));
        redirect($user['is_admin'] ? YUYUN_URL . '/admin/index.php' : YUYUN_URL . '/user/index.php');
    } else {
        flash('error', __('email_or_password_error'));
        redirect(YUYUN_URL . '/login.php');
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <div class="auth-box" style="position:relative;overflow:hidden">
        <div class="text-center">
            <div class="ip-illustration" style="width:90px;height:90px;margin-bottom:12px"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="24" cy="16" r="9"/><path d="M6 44c0-12 9-18 18-18s18 6 18 18"/></svg></div>
        </div>
        <h2><?php echo __('login_welcome') ?></h2>
        <p><?php echo __('login_subtitle') ?></p>
        <div class="auth-tabs">
            <a href="<?php echo YUYUN_URL ?>/login.php" class="active"><?php echo __('password_login') ?></a>
            <a href="<?php echo YUYUN_URL ?>/verify.php"><?php echo __('code_login') ?></a>
        </div>
        <?php echo render_flash() ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <div class="form-group"><label><i class="iconfont icon-envelope"></i> <?php echo __('email') ?></label><input type="email" name="email" class="form-control" required></div>
            <div class="form-group"><label><i class="iconfont icon-lock"></i> <?php echo __('password') ?></label><input type="password" name="password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary btn-block" style="width:100%"><i class="iconfont icon-user"></i> <?php echo __('login') ?></button>
        </form>
        <p style="text-align:center;margin-top:18px;font-size:14px;color:var(--text-2)">
            <?php echo __('no_account') ?><a href="<?php echo YUYUN_URL ?>/register.php" style="color:var(--brand)"><?php echo __('register_now') ?></a>
        </p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
