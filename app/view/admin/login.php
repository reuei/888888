<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台 - QEEFG授权站</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #1a1a1a; color: #333; line-height: 1.6; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: #fff; padding: 50px; width: 100%; max-width: 400px; }
        .login-title { font-size: 24px; text-align: center; margin-bottom: 30px; color: #1a1a1a; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; color: #333; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 16px; font-size: 14px; border: 1px solid #d9d9d9; background: #fff; }
        .form-control:focus { outline: none; border-color: #1890ff; }
        .btn { display: block; width: 100%; padding: 12px; font-size: 16px; border: none; cursor: pointer; background: #1890ff; color: #fff; }
        .btn:hover { background: #40a9ff; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #666; font-size: 14px; }
        .back-link a:hover { color: #1890ff; }
        .error-msg { background: #fff2f0; border: 1px solid #ffccc7; color: #ff4d4f; padding: 10px; margin-bottom: 20px; display: none; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1 class="login-title">管理后台</h1>
        <div class="error-msg" id="error-msg"></div>
        <form id="loginForm" onsubmit="return handleSubmit(event)">
            <div class="form-group">
                <label>管理员账号</label>
                <input type="text" class="form-control" name="username" placeholder="请输入管理员账号" required>
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" class="form-control" name="password" placeholder="请输入密码" required>
            </div>
            <button type="submit" class="btn">登录</button>
        </form>
        <div class="back-link">
            <a href="/">返回首页</a>
        </div>
    </div>

    <script>
        function handleSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('loginForm');
            const formData = new FormData(form);

            fetch('/admin/dologin', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    window.location.href = data.data.redirect || '/admin/dashboard';
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