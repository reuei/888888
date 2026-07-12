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
    <title>后台管理 - 清廉在线</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #f0f2f5; }
        .admin-login-wrap { max-width: 420px; margin: 100px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.1); overflow: hidden; }
        .admin-login-header { background: #c20000; color: #fff; padding: 30px; text-align: center; }
        .admin-login-header h1 { font-size: 22px; font-weight: 500; }
        .admin-login-header p { font-size: 13px; opacity: 0.8; margin-top: 5px; }
        .admin-login-body { padding: 30px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-size: 14px; }
        .form-group input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-group input:focus { outline: none; border-color: #c20000; }
        .btn { width: 100%; padding: 12px; background: #c20000; color: #fff; border: 0; border-radius: 4px; cursor: pointer; font-size: 15px; }
        .btn:hover { background: #a80000; }
        .error { background: #fff1f0; color: #f5222d; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ffa39e; font-size: 13px; }
        .back-link { text-align: center; margin-top: 15px; }
        .back-link a { color: #999; font-size: 13px; text-decoration: none; }
        .back-link a:hover { color: #c20000; }
    </style>
</head>
<body>
    <div class="admin-login-wrap">
        <div class="admin-login-header">
            <h1>清廉在线管理系统</h1>
            <p>后台管理登录</p>
        </div>
        <div class="admin-login-body">
            <?php if ($error): ?>
                <div class="error"><?php echo e($error); ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn">登 录</button>
            </form>
            <div class="back-link">
                <a href="../index.php">&laquo; 返回首页</a>
            </div>
        </div>
    </div>
</body>
</html>
