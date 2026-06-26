<?php
require __DIR__ . '/../includes/config.php';
require_login();
$pageTitle = '个人资料';
$db = getDb();
$user = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nickname = trim($_POST['nickname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    if ($password && $newPassword) {
        if (!password_verify($password, $user['password'])) {
            flash('error', '当前密码错误');
        } elseif (strlen($newPassword) < 6) {
            flash('error', '新密码至少 6 位');
        } else {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $db->prepare('UPDATE users SET nickname=:n, phone=:p, password=:pw WHERE id=:id')->execute([':n'=>$nickname,':p'=>$phone,':pw'=>$hash,':id'=>$user['id']]);
            flash('success', '资料与密码已更新');
        }
    } else {
        $db->prepare('UPDATE users SET nickname=:n, phone=:p WHERE id=:id')->execute([':n'=>$nickname,':p'=>$phone,':id'=>$user['id']]);
        flash('success', '资料已更新');
    }
    redirect(YUYUN_URL . '/user/profile.php');
}
require __DIR__ . '/../includes/header.php';
?>
<section class="section bg-white">
    <div class="container">
        <div class="user-layout">
            <?php require __DIR__ . '/../includes/user_sidebar.php'; ?>
            <div>
                <h2 style="margin-bottom:20px">个人资料</h2>
                <div class="admin-card" style="max-width:600px">
                    <?php echo render_flash() ?>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                        <div class="form-group"><label>邮箱</label><input type="email" class="form-control" value="<?php echo e($user['email']) ?>" disabled></div>
                        <div class="form-row">
                            <div class="form-group"><label>昵称</label><input type="text" name="nickname" class="form-control" value="<?php echo e($user['nickname']) ?>"></div>
                            <div class="form-group"><label>手机</label><input type="text" name="phone" class="form-control" value="<?php echo e($user['phone']) ?>"></div>
                        </div>
                        <hr style="border:none;border-top:1px solid #f0f0f0;margin:24px 0">
                        <div class="form-row">
                            <div class="form-group"><label>当前密码（修改密码时填写）</label><input type="password" name="password" class="form-control"></div>
                            <div class="form-group"><label>新密码</label><input type="password" name="new_password" class="form-control" minlength="6"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">保存</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
