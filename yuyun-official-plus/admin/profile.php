<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';
require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';
requireAdminLogin();

$db = YuyunDB::getInstance();
$admin = currentAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('danger', '安全验证失败，请刷新页面重试');
        header('Location: profile.php');
        exit;
    }

    $oldPass = $_POST['old_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $newPass2 = $_POST['new_password2'] ?? '';

    // 验证旧密码
    $stored = null;
    if ($db->getType() === 'json') {
        $admins = $db->jsonAll('admins', 'id', 'ASC');
        foreach ($admins as $a) {
            if ($a['id'] == ($admin['id'] ?? 0)) {
                $stored = $a;
                break;
            }
        }
    } else {
        $stored = $db->queryOne("SELECT * FROM admins WHERE id = ?", [($admin['id'] ?? 0)]);
    }

    if (!$stored || !password_verify($oldPass, $stored['password'] ?? '')) {
        setFlash('danger', '当前密码不正确');
    } elseif (strlen($newPass) < 6) {
        setFlash('warning', '新密码长度不能少于 6 位');
    } elseif ($newPass !== $newPass2) {
        setFlash('warning', '两次输入的新密码不一致');
    } else {
        dbUpdate('admins', $stored['id'], ['password' => password_hash($newPass, PASSWORD_DEFAULT)]);
        addLog('修改管理员密码', '管理员：' . $stored['username']);
        setFlash('success', '密码已修改，请使用新密码重新登录');
        session_destroy();
        header('Location: login.php');
        exit;
    }
    header('Location: profile.php');
    exit;
}

$pageTitle = '修改密码';
include __DIR__ . '/includes/header.php';
?>

<div class="card">
    <div class="card-header"><h2>修改管理员密码</h2></div>
    <div class="card-body">
        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <div class="form-group">
                <label>当前密码</label>
                <input type="password" name="old_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>新密码</label>
                <input type="password" name="new_password" class="form-control" required minlength="6">
            </div>
            <div class="form-group">
                <label>确认新密码</label>
                <input type="password" name="new_password2" class="form-control" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary">保存修改</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
