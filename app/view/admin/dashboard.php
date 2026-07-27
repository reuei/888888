<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台 - QEEFG授权站</title>
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
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 24px; border: 1px solid #e5e5e5; }
        .stat-value { font-size: 32px; font-weight: 600; color: #1890ff; }
        .stat-label { font-size: 14px; color: #666; margin-top: 8px; }
        .footer { padding: 20px; background: #1a1a1a; color: #fff; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; height: auto; }
            .stats-grid { grid-template-columns: 1fr; }
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
            <a href="/admin/dashboard" class="active">后台首页</a>
            <a href="/admin/users">用户管理</a>
            <a href="/admin/products">产品管理</a>
            <a href="/admin/settings">系统设置</a>
        </aside>

        <main class="content">
            <h1 class="page-title">后台首页</h1>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['users'] ?? 0; ?></div>
                    <div class="stat-label">注册用户</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['products'] ?? 0; ?></div>
                    <div class="stat-label">产品数量</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['licenses'] ?? 0; ?></div>
                    <div class="stat-label">授权总数</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['orders'] ?? 0; ?></div>
                    <div class="stat-label">订单总数</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">¥<?php echo number_format($stats['revenue'] ?? 0, 2); ?></div>
                    <div class="stat-label">总收入</div>
                </div>
            </div>
        </main>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> QEEFG授权站 All Rights Reserved.</p>
    </footer>
</body>
</html>