<?php
require __DIR__ . '/../includes/config.php';
require_login();
$pageTitle = __('profile');
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
            flash('error', __('password_error'));
        } elseif (strlen($newPassword) < 6) {
            flash('error', __('password_short'));
        } else {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $db->prepare('UPDATE users SET nickname=:n, phone=:p, password=:pw WHERE id=:id')->execute([':n'=>$nickname,':p'=>$phone,':pw'=>$hash,':id'=>$user['id']]);
            flash('success', __('profile_password_updated'));
        }
    } else {
        $db->prepare('UPDATE users SET nickname=:n, phone=:p WHERE id=:id')->execute([':n'=>$nickname,':p'=>$phone,':id'=>$user['id']]);
        flash('success', __('profile_updated'));
    }
    redirect(YUYUN_URL . '/user/profile.php');
}
require __DIR__ . '/../includes/header.php';
?>
<section class="section bg-white">
    <div class="container">
        <div class="user-layout">
            <div class="user-sidebar">
                <a href="<?php echo YUYUN_URL ?>/user/index.php"><i class="iconfont icon-gauge"></i> <?php echo __('welcome') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/notifications.php"><i class="iconfont icon-bell"></i> <?php echo __('notifications') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/tickets.php"><i class="iconfont icon-ticket"></i> <?php echo __('my_tickets') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/feedback.php"><i class="iconfont icon-edit"></i> <?php echo __('feedback') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/profile.php" class="active"><i class="iconfont icon-user"></i> <?php echo __('profile') ?></a>
            </div>
            <div class="user-content">
                <h2 style="margin-bottom:20px"><?php echo __('profile') ?></h2>
                <div class="admin-card" style="max-width:600px">
                    <?php echo render_flash() ?>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                        <div class="form-group"><label><?php echo __('email') ?></label><input type="email" class="form-control" value="<?php echo e($user['email']) ?>" disabled></div>
                        <div class="form-row">
                            <div class="form-group"><label><?php echo __('nickname') ?></label><input type="text" name="nickname" class="form-control" value="<?php echo e($user['nickname']) ?>"></div>
                            <div class="form-group"><label><?php echo __('phone_label') ?></label><input type="text" name="phone" class="form-control" value="<?php echo e($user['phone']) ?>"></div>
                        </div>
                        <hr style="border:none;border-top:1px solid #f0f0f0;margin:24px 0">
                        <div class="form-row">
                            <div class="form-group"><label><?php echo __('current_password') ?></label><input type="password" name="password" class="form-control"></div>
                            <div class="form-group"><label><?php echo __('new_password') ?></label><input type="password" name="new_password" class="form-control" minlength="6"></div>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo __('save') ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
