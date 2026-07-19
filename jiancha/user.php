<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
if (!isLoggedIn()) redirect('login.php');
$user = currentUser();
$error = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf()) { $error = '非法请求'; }
    else {
        $nickname = trim($_POST['nickname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $oldPwd = $_POST['old_password'] ?? '';
        $newPwd = $_POST['new_password'] ?? '';
        $data = ['nickname' => $nickname ?: $user['username'], 'email' => $email];
        if ($oldPwd && $newPwd) {
            if (!password_verify($oldPwd, $user['password'])) { $error = '原密码错误'; }
            elseif (strlen($newPwd) < 6) { $error = '新密码至少6位'; }
            else { $data['password'] = password_hash($newPwd, PASSWORD_DEFAULT); }
        }
        if (!$error) {
            DB::update('users', $data, 'id=?', [$user['id']]);
            $ok = '信息更新成功';
            $user = currentUser();
        }
    }
}
$pageTitle = '个人中心';
include __DIR__ . '/includes/header.php';
?>
<div class="page-hero"><h1>个人中心</h1><p>欢迎，<?php echo e($user['nickname'] ?: $user['username']); ?></p></div>
<div class="section"><div class="container" style="max-width:700px">
<?php if ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
<div class="card"><div class="card-body">
<form method="post">
<?php echo csrfField(); ?>
<div class="auth-field"><label>用户名</label><input type="text" value="<?php echo e($user['username']); ?>" disabled style="opacity:.5"></div>
<div class="auth-field"><label>昵称</label><input type="text" name="nickname" value="<?php echo e($user['nickname'] ?: ''); ?>" placeholder="显示名称"></div>
<div class="auth-field"><label>邮箱</label><input type="email" name="email" value="<?php echo e($user['email'] ?: ''); ?>" data-v="email"></div>
<div class="auth-field"><label>原密码（修改密码时填写）</label><input type="password" name="old_password" data-v="password"></div>
<div class="auth-field"><label>新密码</label><input type="password" name="new_password" data-v="password"></div>
<div class="auth-field"><button type="submit" class="btn btn-primary">保存修改</button></div>
</form>
</div></div>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>