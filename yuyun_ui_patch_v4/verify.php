<?php
require __DIR__ . '/includes/config.php';
if (template_include('verify.php')) exit;
$pageTitle = __('page_verify');
ensure_user_columns();
$step = empty($_GET['code']) ? 'send' : 'verify';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $db = getDb();
    if (isset($_POST['send'])) {
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', __('email_invalid'));
        } else {
            $code = generate_code();
            $expire = time() + 300;
            $check = $db->prepare('SELECT id FROM users WHERE email=:e');
            $check->execute([':e'=>$email]);
            if ($check->fetch()) {
                $db->prepare('UPDATE users SET verify_code=:c, code_expire=:ex WHERE email=:e')->execute([':e'=>$email,':c'=>$code,':ex'=>$expire]);
            } else {
                $now = date('Y-m-d H:i:s');
                $db->prepare('INSERT INTO users (email, password, nickname, verify_code, code_expire, created_at) VALUES (:e, "", "", :c, :ex, :t)')->execute([':e'=>$email,':c'=>$code,':ex'=>$expire,':t'=>$now]);
            }
            send_code($email, $code);
            flash('success', __('code_sent'));
            redirect(YUYUN_URL . '/verify.php?code=1&email=' . urlencode($email));
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :e LIMIT 1');
        $stmt->execute([':e'=>$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $user['verify_code'] === $code && $user['code_expire'] > time()) {
            $db->prepare('UPDATE users SET email_verified=1, verify_code=NULL, code_expire=NULL WHERE id=:id')->execute([':id'=>$user['id']]);
            $_SESSION['user_id'] = $user['id'];
            flash('success', __('login_success'));
            redirect($user['is_admin'] ? YUYUN_URL . '/admin/index.php' : YUYUN_URL . '/user/index.php');
        } else {
            flash('error', __('code_error'));
            redirect(YUYUN_URL . '/verify.php?code=1&email=' . urlencode($email));
        }
    }
    redirect(YUYUN_URL . '/verify.php');
}
require __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <div class="auth-box">
        <div class="text-center">
            <div class="ip-illustration" style="width:90px;height:90px;margin-bottom:12px"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="10" width="36" height="26" rx="2"/><path d="M6 14l18 14 18-14"/><circle cx="38" cy="16" r="2" fill="currentColor" stroke="none"/></svg></div>
        </div>
        <h2><?php echo __('verify_login') ?></h2>
        <p><?php echo __('verify_subtitle') ?></p>
        <div class="auth-tabs">
            <a href="<?php echo YUYUN_URL ?>/login.php"><?php echo __('password_login') ?></a>
            <a href="<?php echo YUYUN_URL ?>/verify.php" class="active"><?php echo __('code_login') ?></a>
        </div>
        <?php echo render_flash() ?>
        <?php if ($step === 'send'): ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <input type="hidden" name="send" value="1">
            <div class="form-group"><label><i class="iconfont icon-envelope"></i> <?php echo __('email') ?></label><input type="email" name="email" class="form-control" required></div>
            <button type="submit" class="btn btn-primary btn-block" style="width:100%"><i class="iconfont icon-send"></i> <?php echo __('send_code') ?></button>
        </form>
        <?php else: ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <div class="form-group"><label><i class="iconfont icon-envelope"></i> <?php echo __('email') ?></label><input type="email" name="email" class="form-control" value="<?php echo e($_GET['email'] ?? '') ?>" required></div>
            <div class="form-group"><label><i class="iconfont icon-certificate"></i> <?php echo __('verify_code') ?></label><input type="text" name="code" class="form-control" required maxlength="6"></div>
            <button type="submit" class="btn btn-primary btn-block" style="width:100%"><i class="iconfont icon-user"></i> <?php echo __('login') ?></button>
        </form>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
