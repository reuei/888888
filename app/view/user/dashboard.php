<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户中心 - QEEFG授权站</title>
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
        .logout-btn:hover { background: #ff7875; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; display: flex; gap: 20px; }
        .sidebar { width: 200px; background: #fff; border: 1px solid #e5e5e5; padding: 20px 0; height: fit-content; }
        .sidebar a { display: block; padding: 12px 20px; color: #333; font-size: 14px; }
        .sidebar a:hover { background: #f5f7fa; }
        .sidebar a.active { background: #e6f7ff; color: #1890ff; border-left: 3px solid #1890ff; }
        .content { flex: 1; }
        .page-title { font-size: 24px; margin-bottom: 20px; color: #1a1a1a; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 24px; border: 1px solid #e5e5e5; }
        .stat-value { font-size: 32px; font-weight: 600; color: #1890ff; }
        .stat-label { font-size: 14px; color: #666; margin-top: 8px; }
        .welcome-box { background: #fff; padding: 24px; border: 1px solid #e5e5e5; margin-bottom: 20px; }
        .welcome-box h2 { font-size: 18px; margin-bottom: 10px; }
        .welcome-box p { color: #666; font-size: 14px; }
        .footer { padding: 20px; background: #1a1a1a; color: #fff; text-align: center; margin-top: 40px; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; }
            .stats-grid { grid-template-columns: 1fr; }
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
            <a href="/user/dashboard" class="active">用户中心</a>
            <a href="/user/workplace">工作台</a>
            <a href="/user/products">产品中心</a>
            <a href="/user/my-products">我的产品</a>
            <a href="/user/balance">余额管理</a>
            <a href="/user/settings">账户设置</a>
        </aside>

        <main class="content">
            <h1 class="page-title">用户中心</h1>

            <div class="welcome-box">
                <h2>欢迎回来，<?php echo htmlspecialchars($user['username'] ?? ''); ?>！</h2>
                <p>这里是您的个人中心，您可以管理您的授权产品、查看余额等。</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">¥<?php echo number_format($user['balance'] ?? 0, 2); ?></div>
                    <div class="stat-label">账户余额</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['products'] ?? 0; ?></div>
                    <div class="stat-label">授权产品</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['orders'] ?? 0; ?></div>
                    <div class="stat-label">订单数量</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['login_count'] ?? 0; ?></div>
                    <div class="stat-label">登录次数</div>
                </div>
            </div>

            <div class="welcome-box">
                <h2>快捷操作</h2>
                <p style="margin-top: 15px;">
                    <a href="/user/products" style="background: #1890ff; color: #fff; padding: 10px 20px; display: inline-block;">购买产品</a>
                    <a href="/user/my-products" style="background: #fff; color: #1890ff; border: 1px solid #1890ff; padding: 10px 20px; display: inline-block; margin-left: 10px;">我的授权</a>
                </p>
            </div>
        </main>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> QEEFG授权站 All Rights Reserved.</p>
    </footer>
</body>
</html>