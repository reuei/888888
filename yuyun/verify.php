<?php
require __DIR__ . '/includes/config.php';
if (template_include('verify.php')) exit;
$pageTitle = L('auth.verify_title', '邮箱验证码登录');
$mode = $_GET['mode'] ?? '';
$emailVerifyMode = ($mode === 'verify_email');
$step = empty($_GET['code']) ? 'send' : 'verify';

function setUserVerified(PDO $db, int $uid): void {
    $db->prepare('UPDATE users SET email_verified=1, verify_code=NULL, code_expire=NULL WHERE id=:id')->execute([':id'=>$uid]);
}

function doLogin(array $user): void {
    $_SESSION['user_id'] = $user['id'];
    flash('success', '登录成功');
    redirect($user['is_admin'] ? YUYUN_URL . '/admin/index.php' : YUYUN_URL . '/user/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $db = getDb();
    if (isset($_POST['send'])) {
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', '邮箱格式不正确');
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
            if ($emailVerifyMode) {
                send_verify_email($email, $code);
            } else {
                send_code($email, $code);
            }
            flash('success', '验证码已发送，请查收邮箱');
            redirect(YUYUN_URL . '/verify.php?code=1&email=' . urlencode($email) . ($emailVerifyMode ? '&mode=verify_email' : ''));
        }
    } elseif (isset($_POST['verify_email'])) {
        $email = trim($_POST['email'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :e LIMIT 1');
        $stmt->execute([':e'=>$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $user['verify_code'] === $code && $user['code_expire'] > time()) {
            setUserVerified($db, $user['id']);
            doLogin($user);
        } else {
            flash('error', '验证码错误或已过期');
            redirect(YUYUN_URL . '/verify.php?mode=verify_email&email=' . urlencode($email));
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $stmt = $db->prepare('SELECT * FROM users WHERE email = :e LIMIT 1');
        $stmt->execute([':e'=>$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $user['verify_code'] === $code && $user['code_expire'] > time()) {
            setUserVerified($db, $user['id']);
            doLogin($user);
        } else {
            flash('error', '验证码错误或已过期');
            redirect(YUYUN_URL . '/verify.php?code=1&email=' . urlencode($email));
        }
    }
    redirect(YUYUN_URL . '/verify.php' . ($emailVerifyMode ? '?mode=verify_email' : ''));
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
        <h2><?php echo $emailVerifyMode ? '邮箱验证' : L('auth.verify_title', '邮箱验证码登录') ?></h2>
        <p><?php echo $emailVerifyMode ? '请输入邮箱收到的 6 位验证码完成验证' : L('auth.verify_subtitle', '无需密码，验证码 5 分钟内有效') ?></p>
        <?php if (!$emailVerifyMode): ?>
        <div class="auth-tabs">
            <a href="<?php echo YUYUN_URL ?>/login.php"><?php echo L('auth.password_login', '密码登录') ?></a>
            <a href="<?php echo YUYUN_URL ?>/verify.php" class="active"><?php echo L('auth.code_login', '邮箱验证码登录') ?></a>
        </div>
        <?php endif; ?>
        <?php echo render_flash() ?>
        <?php if ($step === 'send'): ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <input type="hidden" name="send" value="1">
            <?php if ($emailVerifyMode): ?><input type="hidden" name="mode" value="verify_email"><?php endif; ?>
            <div class="form-group"><label><i class="iconfont icon-envelope"></i> <?php echo L('auth.email', '邮箱') ?></label><input type="email" name="email" class="form-control" value="<?php echo e($_GET['email'] ?? '') ?>" required></div>
            <button type="submit" class="btn btn-primary btn-block" style="width:100%"><i class="iconfont icon-send"></i> <?php echo L('btn.send', '发送验证码') ?></button>
        </form>
        <?php else: ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <input type="hidden" name="email" value="<?php echo e($_GET['email'] ?? '') ?>">
            <div class="form-group"><label><i class="iconfont icon-envelope"></i> <?php echo L('auth.email', '邮箱') ?></label><input type="email" class="form-control" value="<?php echo e($_GET['email'] ?? '') ?>" disabled></div>
            <div class="form-group"><label><i class="iconfont icon-certificate"></i> <?php echo L('auth.code', '验证码') ?></label><input type="text" name="code" class="form-control" required maxlength="6"></div>
            <button type="submit" name="<?php echo $emailVerifyMode ? 'verify_email' : 'verify' ?>" class="btn btn-primary btn-block" style="width:100%"><i class="iconfont icon-user"></i> <?php echo L('btn.login', '登录') ?></button>
        </form>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>