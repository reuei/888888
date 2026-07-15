<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$user = currentUser();
$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'profile') {
        $nickname = trim($_POST['nickname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        DB::update('users', [
            'nickname' => $nickname,
            'email' => $email,
        ], 'id=?', [$user['id']]);
        $msg = '资料修改成功';
        $msgType = 'success';
        $user = currentUser();
    } elseif ($action == 'password') {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($oldPassword, $user['password'])) {
            $msg = '原密码错误';
            $msgType = 'error';
        } elseif (strlen($newPassword) < 6) {
            $msg = '新密码长度不能少于6位';
            $msgType = 'error';
        } elseif ($newPassword !== $confirmPassword) {
            $msg = '两次输入的新密码不一致';
            $msgType = 'error';
        } else {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            DB::update('users', ['password' => $hashed], 'id=?', [$user['id']]);
            $msg = '密码修改成功';
            $msgType = 'success';
        }
    }
}

$pageTitle = '用户中心';
include __DIR__ . '/includes/header.php';
?>

    <div class="container">
        <div class="crums">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>用户中心</span>
        </div>
    </div>

    <div class="">
        <div class="container">
            <div class="two-col">
                <div class="">
                    <div class="section">
                        <div class="block-head">
                            <h3>修改资料</h3>
                        </div>
                        <div class="block-body">
                            <?php if ($msg && $_POST['action'] == 'profile'): ?>
                            <div style="padding:10px 15px; border-radius:4px; margin-bottom:20px; <?php echo $msgType == 'success' ? 'background:#f6ffed; color:#52c41a; border:1px solid #b7eb8f;' : 'background:#fff1f0; color:#f5222d; border:1px solid #ffa39e;'; ?>">
                                <?php echo e($msg); ?>
                            </div>
                            <?php endif; ?>
                            <form method="post" style="max-width:400px;">
                                <input type="hidden" name="action" value="profile">
                                <div class="form-group">
                                    <label>用户名</label>
                                    <input type="text" value="<?php echo e($user['username']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label>昵称</label>
                                    <input type="text" name="nickname" value="<?php echo e($user['nickname']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>邮箱</label>
                                    <input type="email" name="email" value="<?php echo e($user['email']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>注册时间</label>
                                    <input type="text" value="<?php echo e($user['reg_time']); ?>" disabled>
                                </div>
                                <button type="submit" class="btn">保存修改</button>
                            </form>
                        </div>
                    </div>

                    <div class="section">
                        <div class="block-head">
                            <h3>修改密码</h3>
                        </div>
                        <div class="block-body">
                            <?php if ($msg && $_POST['action'] == 'password'): ?>
                            <div style="padding:10px 15px; border-radius:4px; margin-bottom:20px; <?php echo $msgType == 'success' ? 'background:#f6ffed; color:#52c41a; border:1px solid #b7eb8f;' : 'background:#fff1f0; color:#f5222d; border:1px solid #ffa39e;'; ?>">
                                <?php echo e($msg); ?>
                            </div>
                            <?php endif; ?>
                            <form method="post" style="max-width:400px;">
                                <input type="hidden" name="action" value="password">
                                <div class="form-group">
                                    <label>原密码</label>
                                    <input type="password" name="old_password" required>
                                </div>
                                <div class="form-group">
                                    <label>新密码</label>
                                    <input type="password" name="new_password" required placeholder="至少6位">
                                </div>
                                <div class="form-group">
                                    <label>确认新密码</label>
                                    <input type="password" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn">修改密码</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="">
                    <div class="block">
                        <div class="block-title">个人信息</div>
                        <div class="block-body" style="text-align:center; padding:20px 15px;">
                            <div style="width:80px; height:80px; background:#c20000; border-radius:50%; margin:0 auto 15px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:32px;">
                                <?php echo e(mb_substr($user['nickname'] ?: $user['username'], 0, 1)); ?>
                            </div>
                            <p style="font-size:16px; font-weight:500; margin-bottom:5px;"><?php echo e($user['nickname'] ?: $user['username']); ?></p>
                            <p style="font-size:12px; color:#999;"><?php echo e($user['email'] ?: '未设置邮箱'); ?></p>
                            <p style="margin-top:10px; font-size:13px;">
                                角色：
                                <?php
                                $roleMap = ['super_admin' => '超级管理员', 'admin' => '管理员', 'subscriber' => '普通用户'];
                                echo e($roleMap[$user['role']] ?? $user['role']);
                                ?>
                            </p>
                            <?php if (isAdmin()): ?>
                            <a href="<?php echo BASE_URL; ?>admin/index.php" class="btn" style="margin-top:15px; display:inline-block; font-size:13px; padding:6px 15px;">进入后台</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
