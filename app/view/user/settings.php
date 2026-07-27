<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>账户设置 - QEEFG授权站</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="/" class="logo">
                <div class="logo-icon">Q</div>
                <div class="logo-text">QEEFG授权站</div>
            </a>
            <nav class="nav">
                <ul class="nav-links">
                    <li><a href="/">首页</a></li>
                    <li><a href="/license-query">授权查询</a></li>
                    <li><a href="/documents">文档中心</a></li>
                </ul>
                <div class="nav-actions">
                    <button class="icon-btn theme-toggle" id="theme-toggle">☀️</button>
                    <button class="icon-btn lang-toggle" id="lang-toggle">CN</button>
                    <div class="burger-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="/user/dashboard">🏠 首页</a></li>
            <li><a href="/user/workplace">📋 工作台</a></li>
            <li><a href="/user/products">🛒 产品列表</a></li>
            <li><a href="/user/my-products">💼 我的产品</a></li>
            <li><a href="/user/balance">💰 余额管理</a></li>
            <li><a href="/user/settings" class="active">⚙️ 账户设置</a></li>
            <li><a href="/user/feedback">📝 意见反馈</a></li>
            <li><a href="/logout">🚪 退出登录</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-title">
            <h1>账户设置</h1>
            <p>管理您的账户信息</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>基本信息</h3>
            </div>
            
            <form method="post" action="/user/updateSettings" id="settings-form">
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" value="<?php echo $user['username']; ?>" disabled>
                </div>
                <div class="form-group">
                    <label>邮箱</label>
                    <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label>QQ</label>
                    <input type="text" name="qq" value="<?php echo $user['qq'] ?: ''; ?>" placeholder="选填">
                </div>
                <div class="form-group">
                    <label>手机号</label>
                    <input type="text" name="phone" value="<?php echo $user['phone'] ?: ''; ?>" placeholder="选填">
                </div>
                <button type="submit" class="btn btn-primary">保存信息</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>修改密码</h3>
            </div>
            
            <form method="post" action="/user/updateSettings" id="password-form">
                <div class="form-group">
                    <label>原密码</label>
                    <input type="password" name="old_password" placeholder="请输入原密码">
                </div>
                <div class="form-group">
                    <label>新密码</label>
                    <input type="password" name="new_password" placeholder="至少6位">
                </div>
                <div class="form-group">
                    <label>确认新密码</label>
                    <input type="password" name="confirm_password" placeholder="请再次输入新密码">
                </div>
                <button type="submit" class="btn btn-secondary">修改密码</button>
            </form>
        </div>
    </main>

    <script src="/static/js/main.js"></script>
    <script>
        document.getElementById('settings-form').addEventListener('submit', function(e) {
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
                showToast('更新失败', 'error');
            });
        });

        document.getElementById('password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const oldPassword = this.querySelector('input[name="old_password"]').value;
            const newPassword = this.querySelector('input[name="new_password"]').value;
            const confirmPassword = this.querySelector('input[name="confirm_password"]').value;

            if (!oldPassword || !newPassword || !confirmPassword) {
                showToast('请填写所有密码字段', 'error');
                return;
            }

            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    showToast(data.msg, 'success');
                    this.reset();
                } else {
                    showToast(data.msg, 'error');
                }
            })
            .catch(err => {
                showToast('修改失败', 'error');
            });
        });
    </script>
</body>
</html>
