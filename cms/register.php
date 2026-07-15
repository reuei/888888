<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $nickname = trim($_POST['nickname'] ?? '');

    if (empty($username) || empty($password)) {
        $error = '用户名和密码不能为空';
    } elseif (strlen($username) < 3 || strlen($username) > 20) {
        $error = '用户名长度应为3-20个字符';
    } elseif (!preg_match('/^[a-zA-Z0-9_\x7f-\xff]+$/', $username)) {
        $error = '用户名只能包含字母、数字、下划线和中文';
    } elseif (strlen($password) < 6) {
        $error = '密码长度不能少于6位';
    } elseif ($password !== $password2) {
        $error = '两次输入的密码不一致';
    } else {
        $exists = DB::fetchOne("SELECT id FROM users WHERE username=?", [$username]);
        if ($exists) {
            $error = '该用户名已被注册';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            DB::insert('users', [
                'username' => $username,
                'password' => $hashed,
                'nickname' => $nickname,
                'email' => $email,
                'role' => 'subscriber',
                'status' => 1,
            ]);
            $success = '注册成功！请登录';
        }
    }
}

$pageTitle = '用户注册';
include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="crumbs">
        <a href="<?php echo BASE_URL; ?>index.php">首页</a>
        <span class="sep">/</span>
        <span>用户注册</span>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <div class="form-card">
        <div class="form-card-head">
            <h2>用户注册</h2>
            <p>USER REGISTER</p>
        </div>
        <div class="form-card-body">
            <?php if ($error): ?>
            <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo e($success); ?> <a href="login.php" style="color:var(--pk-success); margin-left:10px; font-weight:600;">去登录 &raquo;</a></div>
            <?php endif; ?>

            <form method="post" data-toast-form>
                <div class="form-row">
                    <label>用户名 <span class="req">*</span></label>
                    <input type="text" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required placeholder="3-20个字符" data-validate="username">
                    <div class="form-tip"></div>
                </div>
                <div class="form-row">
                    <label>昵称</label>
                    <input type="text" name="nickname" value="<?php echo e($_POST['nickname'] ?? ''); ?>" placeholder="选填">
                </div>
                <div class="form-row">
                    <label>邮箱</label>
                    <input type="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" placeholder="选填" data-validate="email">
                    <div class="form-tip"></div>
                </div>
                <div class="form-row">
                    <label>密码 <span class="req">*</span></label>
                    <input type="password" name="password" required placeholder="至少6位字符" data-validate="password">
                    <div class="form-tip"></div>
                </div>
                <div class="form-row">
                    <label>确认密码 <span class="req">*</span></label>
                    <input type="password" name="password2" required placeholder="请再次输入密码" data-validate="confirm_password">
                    <div class="form-tip"></div>
                </div>
                <button type="submit" class="btn btn-block">注 册</button>
                <p style="text-align:center; margin-top:18px; font-size:13px; color:var(--pk-gray-500);">
                    已有账号？<a href="login.php" style="color:var(--pk-gold-deep); font-weight:600;">立即登录</a>
                </p>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>