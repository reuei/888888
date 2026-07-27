<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台登录 - QEEFG授权站</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <a href="/" class="logo" style="justify-content: center; margin-bottom: 30px;">
                <div class="logo-icon">Q</div>
                <div class="logo-text">QEEFG授权站</div>
            </a>
            
            <h2>后台管理登录</h2>
            
            <form method="post" action="/admin/dologin" id="admin-login-form">
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" name="username" placeholder="请输入用户名" required>
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" name="password" placeholder="请输入密码" required>
                </div>
                <div class="form-options">
                    <label>
                        <input type="checkbox" name="remember"> 记住我
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">登录</button>
            </form>
            
            <div class="form-actions">
                <p><a href="/">返回首页</a></p>
            </div>
        </div>
    </div>

    <script src="/static/js/main.js"></script>
    <script>
        document.getElementById('admin-login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    showToast(data.msg, 'success');
                    setTimeout(() => {
                        window.location.href = data.data.redirect;
                    }, 1000);
                } else {
                    showToast(data.msg, 'error');
                }
            })
            .catch(err => {
                showToast('登录失败', 'error');
            });
        });
    </script>
</body>
</html>
