<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统设置 - QEEFG授权站</title>
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
                    <li><a href="/">前台首页</a></li>
                </ul>
                <div class="nav-actions">
                    <span style="color: #64748b; margin-right: 16px;">欢迎, <?php echo $admin['username']; ?></span>
                    <a href="/admin/logout" class="btn btn-sm btn-secondary">退出</a>
                    <button class="icon-btn theme-toggle" id="theme-toggle">☀️</button>
                    <button class="icon-btn lang-toggle" id="lang-toggle">CN</button>
                </div>
            </nav>
        </div>
    </header>

    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="/admin/dashboard">🏠 首页</a></li>
            <li><a href="/admin/users">👥 用户管理</a></li>
            <li><a href="/admin/products">📦 产品管理</a></li>
            <li><a href="/admin/licenses">🔑 授权管理</a></li>
            <li><a href="/admin/orders">💳 订单管理</a></li>
            <li><a href="/admin/settings" class="active">⚙️ 系统设置</a></li>
            <li><a href="/admin/logout">🚪 退出登录</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-title">
            <h1>系统设置</h1>
            <p>管理系统配置</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>基本设置</h3>
            </div>
            
            <form method="post" action="/admin/saveSettings" id="settings-form">
                <div class="form-group">
                    <label>网站名称</label>
                    <input type="text" name="site_name" value="<?php echo $settings['site_name'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>网站标题</label>
                    <input type="text" name="site_title" value="<?php echo $settings['site_title'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>网站描述</label>
                    <textarea name="site_description" rows="3"><?php echo $settings['site_description'] ?? ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label>网站关键词</label>
                    <input type="text" name="site_keywords" value="<?php echo $settings['site_keywords'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>网站URL</label>
                    <input type="text" name="site_url" value="<?php echo $settings['site_url'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>联系邮箱</label>
                    <input type="email" name="site_email" value="<?php echo $settings['site_email'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>联系QQ</label>
                    <input type="text" name="site_qq" value="<?php echo $settings['site_qq'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>主题颜色</label>
                    <input type="color" name="theme_color" value="<?php echo $settings['theme_color'] ?? '#667eea'; ?>">
                </div>
                <button type="submit" class="btn btn-primary">保存设置</button>
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
                showToast('保存失败', 'error');
            });
        });
    </script>
</body>
</html>
