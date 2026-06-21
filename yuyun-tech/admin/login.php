<?php
/**
 * 后台登录页
 */
require_once 'config.php';
handle_login();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>后台管理 - 语云科技</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { background: linear-gradient(135deg, #0f1b3d 0%, #1a2d5c 100%); min-height: 100vh; }
</style>
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="login-logo">
            <div class="logo-icon" style="width:70px;height:70px;font-size:28px;">Y</div>
            <h2>语云科技后台管理</h2>
            <p>请输入您的账号信息</p>
        </div>

        <?php if (!empty($GLOBALS['login_error'])): ?>
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#e74c3c;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px;">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($GLOBALS['login_error']); ?>
        </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label class="form-label">用户名</label>
                <input type="text" class="form-input" name="username" placeholder="请输入用户名" value="admin" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">密码</label>
                <input type="password" class="form-input" name="password" placeholder="请输入密码" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
                <i class="fas fa-sign-in-alt"></i> 登录
            </button>
        </form>

        <div style="margin-top:24px;padding:16px;background:#f0f7ff;border-radius:8px;font-size:13px;color:#4a5568;line-height:1.7;">
            <div><i class="fas fa-info-circle" style="color:#1a73e8;"></i> 默认账号：<strong style="color:#1a73e8;">admin</strong> / <strong style="color:#1a73e8;">admin123</strong></div>
            <div style="margin-top:4px;"><a href="../index.php" style="color:#1a73e8;">&larr; 返回网站首页</a></div>
        </div>
    </div>
</div>
</body>
</html>
