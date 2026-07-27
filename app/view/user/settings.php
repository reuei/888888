<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>账户设置 - QEEFG授权站</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #f5f7fa; color: #333; line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        .header { background: #fff; border-bottom: 1px solid #e5e5e5; padding: 0 20px; }
        .header-inner { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; height: 60px; }
        .logo { font-size: 20px; font-weight: 600; color: #1a1a1a; }
        .user-info { display: flex; align-items: center; gap: 20px; }
        .user-info span { color: #666; font-size: 14px; }
        .logout-btn { background: #ff4d4f; color: #fff; padding: 6px 16px; border: none; cursor: pointer; font-size: 14px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; display: flex; gap: 20px; }
        .sidebar { width: 200px; background: #fff; border: 1px solid #e5e5e5; padding: 20px 0; height: fit-content; }
        .sidebar a { display: block; padding: 12px 20px; color: #333; font-size: 14px; }
        .sidebar a:hover { background: #f5f7fa; }
        .sidebar a.active { background: #e6f7ff; color: #1890ff; border-left: 3px solid #1890ff; }
        .content { flex: 1; }
        .page-title { font-size: 24px; margin-bottom: 20px; color: #1a1a1a; }
        .settings-box { background: #fff; padding: 30px; border: 1px solid #e5e5e5; max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; color: #333; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 16px; font-size: 14px; border: 1px solid #d9d9d9; background: #fff; }
        .form-control:focus { outline: none; border-color: #1890ff; }
        .form-control:disabled { background: #f5f5f5; }
        .help-text { font-size: 12px; color: #999; margin-top: 4px; }
        .divider { border-top: 1px solid #e5e5e5; margin: 30px 0; padding-top: 20px; }
        .divider-title { font-size: 16px; margin-bottom: 16px; color: #666; }
        .btn { display: inline-block; padding: 12px 32px; font-size: 16px; border: none; cursor: pointer; background: #1890ff; color: #fff; }
        .btn:hover { background: #40a9ff; }
        .error-msg { background: #fff2f0; border: 1px solid #ffccc7; color: #ff4d4f; padding: 10px; margin-bottom: 20px; display: none; }
        .success-msg { background: #f6ffed; border: 1px solid #b7eb8f; color: #52c41a; padding: 10px; margin-bottom: 20px; display: none; }
        .footer { padding: 20px; background: #1a1a1a; color: #fff; text-align: center; margin-top: 40px; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; }
            .settings-box { padding: 20px; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <a href="/" class="logo">QEEFG授权站</a>
            <div class="user-info">
                <span>欢迎，<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
                <a href="/user/logout" class="logout-btn">退出</a>
            </div>
        </div>
    </header>

    <div class="container">
        <aside class="sidebar">
            <a href="/user/dashboard">用户中心</a>
            <a href="/user/workplace">工作台</a>
            <a href="/user/products">产品中心</a>
            <a href="/user/my-products">我的产品</a>
            <a href="/user/balance">余额管理</a>
            <a href="/user/settings" class="active">账户设置</a>
        </aside>

        <main class="content">
            <h1 class="page-title">账户设置</h1>

            <div class="settings-box">
                <div class="error-msg" id="error-msg"></div>
                <div class="success-msg" id="success-msg"></div>
                <form id="settingsForm" onsubmit="return handleSubmit(event)">
                    <div class="form-group">
                        <label>用户名</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" disabled>
                        <div class="help-text">用户名不可修改</div>
                    </div>
                    <div class="form-group">
                        <label>邮箱</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>QQ号</label>
                        <input type="text" class="form-control" name="qq" value="<?php echo htmlspecialchars($user['qq'] ?? ''); ?>" placeholder="选填">
                    </div>
                    <div class="form-group">
                        <label>手机号</label>
                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="选填">
                    </div>

                    <div class="divider">
                        <div class="divider-title">修改密码（不修改请留空）</div>
                        <div class="form-group">
                            <label>原密码</label>
                            <input type="password" class="form-control" name="old_password" placeholder="如需修改密码请输入原密码">
                        </div>
                        <div class="form-group">
                            <label>新密码</label>
                            <input type="password" class="form-control" name="password" placeholder="请输入新密码（至少6位）">
                        </div>
                    </div>

                    <button type="submit" class="btn">保存设置</button>
                </form>
            </div>
        </main>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> QEEFG授权站 All Rights Reserved.</p>
    </footer>

    <script>
        function handleSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('settingsForm');
            const formData = new FormData(form);

            fetch('/user/updateSettings', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    showSuccess(data.msg);
                } else {
                    showError(data.msg);
                }
            })
            .catch(error => {
                showError('网络错误，请稍后重试');
            });

            return false;
        }

        function showError(msg) {
            const errorDiv = document.getElementById('error-msg');
            errorDiv.textContent = msg;
            errorDiv.style.display = 'block';
            document.getElementById('success-msg').style.display = 'none';
        }

        function showSuccess(msg) {
            const successDiv = document.getElementById('success-msg');
            successDiv.textContent = msg;
            successDiv.style.display = 'block';
            document.getElementById('error-msg').style.display = 'none';
        }
    </script>
</body>
</html>