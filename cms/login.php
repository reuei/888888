<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$error = '';
$success = '';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        $user = DB::fetchOne("SELECT * FROM users WHERE username=? AND status=1", [$username]);
        if (!$user || !password_verify($password, $user['password'])) {
            $error = '用户名或密码错误';
        } else {
            $_SESSION['user_id'] = $user['id'];
            DB::update('users', ['last_login' => date('Y-m-d H:i:s')], 'id=?', [$user['id']]);

            if ($remember) {
                setcookie('remember_user', $user['username'], time() + 86400 * 30, '/');
            }

            if ($redirect) {
                redirect($redirect);
            }
            redirect('index.php');
        }
    }
}

$pageTitle = '用户登录';
include __DIR__ . '/includes/header.php';
?>

    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>登录</span>
        </div>
    </div>

    <div class="form-box">
        <h2>用户登录</h2>
        <?php if ($error): ?>
        <div style="background:#fff1f0; color:#f5222d; padding:10px 15px; border-radius:4px; margin-bottom:20px; border:1px solid #ffa39e;">
            <?php echo e($error); ?>
        </div>
        <?php endif; ?>
        <form method="post" data-toast-form>
            <div class="form-group">
                <label>用户名</label>
                <input type="text" name="username" value="<?php echo e($_POST['username'] ?? $_COOKIE['remember_user'] ?? ''); ?>" required placeholder="请输入用户名" data-validate="username">
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" required placeholder="请输入密码" data-validate="password" data-password-strength>
            </div>
            <div class="form-group" style="display:flex; align-items:center; justify-content:space-between;">
                <label style="display:flex; align-items:center; cursor:pointer;">
                    <input type="checkbox" name="remember" style="width:auto; margin-right:5px;"> 记住我
                </label>
                <a href="register.php" style="color:#c20000; font-size:13px;">还没有账号？立即注册</a>
            </div>
            <button type="submit" class="btn btn-block">登录</button>
        </form>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
