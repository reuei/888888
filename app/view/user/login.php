<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户登录 - QEEFG授权站</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #f5f7fa; color: #333; line-height: 1.6; min-height: 100vh; display: flex; flex-direction: column; }
        a { text-decoration: none; color: inherit; }
        .header { background: #fff; border-bottom: 1px solid #e5e5e5; padding: 0 20px; }
        .header-inner { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; height: 60px; }
        .logo { font-size: 20px; font-weight: 600; color: #1a1a1a; }
        .nav { display: flex; gap: 20px; }
        .nav a { color: #666; font-size: 14px; padding: 8px 0; }
        .nav a:hover { color: #1890ff; }
        .main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .login-box { background: #fff; padding: 40px; border: 1px solid #e5e5e5; width: 100%; max-width: 400px; }
        .login-title { font-size: 24px; text-align: center; margin-bottom: 30px; color: #1a1a1a; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; color: #333; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 16px; font-size: 14px; border: 1px solid #d9d9d9; background: #fff; }
        .form-control:focus { outline: none; border-color: #1890ff; }
        .btn { display: block; width: 100%; padding: 12px; font-size: 16px; border: none; cursor: pointer; background: #1890ff; color: #fff; }
        .btn:hover { background: #40a9ff; }
        .login-links { display: flex; justify-content: space-between; margin-top: 20px; font-size: 14px; }
        .login-links a { color: #1890ff; }
        .login-links a:hover { text-decoration: underline; }
        .error-msg { background: #fff2f0; border: 1px solid #ffccc7; color: #ff4d4f; padding: 10px; margin-bottom: 20px; display: none; }
        .footer { padding: 20px; background: #1a1a1a; color: #fff; text-align: center; }
        .footer a { color: #1890ff; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <a href="/" class="logo">QEEFG授权站</a>
            <nav class="nav">
                <a href="/">首页</a>
                <a href="/documents">文档中心</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="login-box">
            <h1 class="login-title">用户登录</h1>
            <div class="error-msg" id="error-msg"></div>
            <form id="loginForm" onsubmit="return handleSubmit(event)">
                <div class="form-group">
                    <label>用户名/邮箱</label>
                    <input type="text" class="form-control" name="username" placeholder="请输入用户名或邮箱" required>
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" class="form-control" name="password" placeholder="请输入密码" required>
                </div>
                <button type="submit" class="btn">登录</button>
            </form>
            <div class="login-links">
                <a href="/register">注册账号</a>
                <a href="/">返回首页</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> QEEFG授权站 All Rights Reserved.</p>
    </footer>

    <script>
        function handleSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('loginForm');
            const formData = new FormData(form);

            fetch('/user/dologin', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    window.location.href = data.data.redirect || '/user/dashboard';
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
        }
    </script>
</body>
</html>