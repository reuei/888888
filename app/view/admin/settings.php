<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统设置 - QEEFG授权站</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #f5f7fa; color: #333; line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        .header { background: #1a1a1a; padding: 0 20px; }
        .header-inner { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; height: 60px; }
        .logo { font-size: 18px; font-weight: 600; color: #fff; }
        .admin-info { display: flex; align-items: center; gap: 20px; }
        .admin-info span { color: #fff; font-size: 14px; }
        .logout-btn { background: #ff4d4f; color: #fff; padding: 6px 16px; border: none; cursor: pointer; font-size: 14px; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; display: flex; gap: 20px; }
        .sidebar { width: 200px; background: #fff; border: 1px solid #e5e5e5; padding: 20px 0; height: calc(100vh - 100px); }
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
        .help-text { font-size: 12px; color: #999; margin-top: 4px; }
        .btn { display: inline-block; padding: 12px 32px; font-size: 16px; border: none; cursor: pointer; background: #1890ff; color: #fff; }
        .btn:hover { background: #40a9ff; }
        .footer { padding: 20px; background: #1a1a1a; color: #fff; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; height: auto; }
            .settings-box { padding: 20px; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <span class="logo">QEEFG授权站 - 管理后台</span>
            <div class="admin-info">
                <span>管理员：<?php echo htmlspecialchars($_SESSION['admin_username'] ?? ''); ?></span>
                <a href="/admin/login?logout=1" class="logout-btn" onclick="return confirm('确定要退出吗？')">退出</a>
            </div>
        </div>
    </header>

    <div class="container">
        <aside class="sidebar">
            <a href="/admin/dashboard">后台首页</a>
            <a href="/admin/users">用户管理</a>
            <a href="/admin/products">产品管理</a>
            <a href="/admin/settings" class="active">系统设置</a>
        </aside>

        <main class="content">
            <h1 class="page-title">系统设置</h1>

            <div class="settings-box">
                <form id="settingsForm">
                    <div class="form-group">
                        <label>网站名称</label>
                        <input type="text" class="form-control" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'QEEFG授权站'); ?>">
                    </div>
                    <div class="form-group">
                        <label>网站描述</label>
                        <input type="text" class="form-control" name="site_desc" value="<?php echo htmlspecialchars($settings['site_desc'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>联系邮箱</label>
                        <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>客服QQ</label>
                        <input type="text" class="form-control" name="qq" value="<?php echo htmlspecialchars($settings['qq'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>备案号</label>
                        <input type="text" class="form-control" name="icp" value="<?php echo htmlspecialchars($settings['icp'] ?? ''); ?>">
                        <div class="help-text">显示在页面底部</div>
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
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('设置已保存（功能待完善）');
        });
    </script>
</body>
</html>