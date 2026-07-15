<?php
define('IN_ADMIN', true);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        $user = DB::fetchOne("SELECT * FROM users WHERE username=? AND status=1", [$username]);
        if (!$user || !password_verify($password, $user['password'])) {
            $error = '用户名或密码错误';
        } elseif (!in_array($user['role'], ['admin', 'super_admin'])) {
            $error = '您没有管理权限';
        } else {
            $_SESSION['user_id'] = $user['id'];
            DB::update('users', ['last_login' => date('Y-m-d H:i:s')], 'id=?', [$user['id']]);
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台 - 人民检察</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: linear-gradient(135deg, #0a2540 0%, #1e3a5f 100%); min-height: 100vh; }
    </style>
</head>
<body>
    <div class="install-bg">
        <div class="install-card" style="max-width:440px;">
            <div class="install-head">
                <div style="display:flex; justify-content:center; margin-bottom:14px;">
                    <svg viewBox="0 0 100 100" width="48" height="48">
                        <path d="M50 12 L58 24 L72 22 L70 36 L82 42 L72 50 L74 64 L60 62 L50 74 L40 62 L26 64 L28 50 L18 42 L30 36 L28 22 L42 24 Z" fill="#c9a227"/>
                        <text x="50" y="58" text-anchor="middle" fill="#0a2540" font-size="20" font-weight="bold" font-family="serif">检</text>
                    </svg>
                </div>
                <h1>管理后台</h1>
                <p>人民检察 · 管理系统登录</p>
            </div>

            <div class="install-body">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo e($error); ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="form-row">
                        <label>管理员账号</label>
                        <input type="text" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required autofocus placeholder="请输入用户名" data-validate="username">
                        <div class="form-tip"></div>
                    </div>
                    <div class="form-row">
                        <label>登录密码</label>
                        <input type="password" name="password" required placeholder="请输入密码" data-validate="password">
                        <div class="form-tip"></div>
                    </div>
                    <button type="submit" class="btn btn-block">登 录 系 统</button>
                </form>

                <div style="text-align:center; margin-top:18px;">
                    <a href="../index.php" style="color:var(--pk-gray-500); font-size:13px;">&laquo; 返回网站首页</a>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>