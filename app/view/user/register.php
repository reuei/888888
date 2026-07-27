<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户注册 - QEEFG授权站</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <a href="/" class="logo" style="justify-content: center; margin-bottom: 30px;">
                <div class="logo-icon">Q</div>
                <div class="logo-text">QEEFG授权站</div>
            </a>
            
            <h2>用户注册</h2>
            
            <form method="post" action="/user/doregister" id="register-form">
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" name="username" placeholder="3-20位字母、数字或下划线" required>
                </div>
                <div class="form-group">
                    <label>邮箱</label>
                    <input type="email" name="email" placeholder="请输入邮箱地址" required>
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" name="password" placeholder="至少6位" required>
                </div>
                <div class="form-group">
                    <label>确认密码</label>
                    <input type="password" name="confirm_password" placeholder="请再次输入密码" required>
                </div>
                <div class="form-options">
                    <label>
                        <input type="checkbox" name="agree" required> 我已阅读并同意 <a href="#">服务协议</a>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">注册</button>
            </form>
            
            <div class="form-actions">
                <p>已有账号? <a href="/login">立即登录</a></p>
            </div>
        </div>
    </div>

    <script src="/static/js/main.js"></script>
    <script>
        document.getElementById('register-form').addEventListener('submit', function(e) {
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
                showToast('注册失败', 'error');
            });
        });
    </script>
</body>
</html>
