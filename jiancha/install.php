<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (file_exists(DB_PATH)) {
    $error = '系统已安装。如需重新安装，请删除 data/jiancha.db 文件。';
    $step = 0;
} else {
    $step = intval($_GET['step'] ?? 1);
    $error = $ok = '';
}

if ($step === 2 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (!$username || !$password) { $error = '请填写用户名和密码'; }
    elseif (strlen($password) < 6) { $error = '密码至少6位'; }
    elseif ($password !== $confirm) { $error = '两次密码不一致'; }
    else {
        try {
            $sql = file_get_contents(__DIR__ . '/install.sql');
            $statements = explode(';', $sql);
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if ($stmt) DB::exec($stmt);
            }
            DB::insert('users', ['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT), 'nickname' => '超级管理员', 'role' => 'super_admin', 'create_time' => date('Y-m-d H:i:s')]);
            $ok = true;
        } catch (Exception $e) {
            $error = '安装失败：' . $e->getMessage();
        }
    }
}
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>安装向导 - <?php echo SITE_NAME; ?></title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">
<div class="auth-card">
<div class="auth-card-head"><h2>安装向导</h2></div>
<div class="auth-card-body">
<?php if ($ok): ?>
<div class="alert alert-success">安装成功！</div>
<p style="line-height:2;font-size:14px;color:var(--text2)">系统已安装完成。<br>请点击下方按钮进入网站首页。</p>
<div style="margin-top:16px"><a href="index.php" class="btn btn-primary btn-block">进入网站</a> <a href="admin/login.php" class="btn btn-gold btn-block" style="margin-top:8px">进入后台</a></div>
<?php elseif ($step === 0 && $error): ?>
<div class="alert alert-warn"><?php echo $error; ?></div>
<p style="text-align:center;margin-top:16px"><a href="index.php" class="btn btn-primary">进入网站</a></p>
<?php elseif ($step === 1): ?>
<h4 style="margin-bottom:16px;text-align:center;color:var(--text1)">环境检查</h4>
<table style="width:100%;font-size:13px;border-collapse:collapse">
<?php
$checks = [
    ['PHP版本 >= 7.0', PHP_VERSION_ID >= 70000, phpversion()],
    ['PDO 扩展', class_exists('PDO'), '已安装'],
    ['SQLite 扩展', extension_loaded('pdo_sqlite'), extension_loaded('pdo_sqlite') ? '已安装' : '未安装'],
    ['GD 扩展', extension_loaded('gd'), extension_loaded('gd') ? '已安装' : '未安装'],
    ['data/ 目录可写', is_writable(__DIR__ . '/data') || !file_exists(__DIR__ . '/data'), is_writable(__DIR__ . '/data') ? '可写' : '不可写'],
    ['uploads/ 目录可写', is_writable(__DIR__ . '/uploads') || !file_exists(__DIR__ . '/uploads'), is_writable(__DIR__ . '/uploads') ? '可写' : '不可写'],
];
$allOk = true;
foreach ($checks as $c):
    $allOk = $allOk && $c[1];
?>
<tr><td style="padding:6px 0;color:var(--text1)"><?php echo $c[0]; ?></td><td style="color:<?php echo $c[1] ? 'var(--green)' : 'var(--red)'; ?>"><?php echo $c[2]; ?></td></tr>
<?php endforeach; ?>
</table>
<?php if ($allOk): ?>
<div style="margin-top:20px;text-align:center"><a href="?step=2" class="btn btn-primary btn-block">下一步</a></div>
<?php else: ?>
<div class="alert alert-error" style="margin-top:16px">请修复上述问题后重试。</div>
<?php endif; ?>
<?php elseif ($step === 2): ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
<h4 style="margin-bottom:16px;text-align:center;color:var(--text1)">创建管理员账号</h4>
<form method="post">
<div class="auth-field"><label>用户名</label><input type="text" name="username" required></div>
<div class="auth-field"><label>密码</label><input type="password" name="password" required></div>
<div class="auth-field"><label>确认密码</label><input type="password" name="confirm_password" required></div>
<div class="auth-field"><button type="submit" class="btn btn-primary btn-block">完成安装</button></div>
</form>
<?php endif; ?>
</div></div></div>
</body>
</html>
