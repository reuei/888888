<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户中心 - QEEFG授权站</title>
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
            <li><a href="/user/dashboard" class="active">🏠 首页</a></li>
            <li><a href="/user/workplace">📋 工作台</a></li>
            <li><a href="/user/products">🛒 产品列表</a></li>
            <li><a href="/user/my-products">💼 我的产品</a></li>
            <li><a href="/user/balance">💰 余额管理</a></li>
            <li><a href="/user/settings">⚙️ 账户设置</a></li>
            <li><a href="/user/feedback">📝 意见反馈</a></li>
            <li><a href="/logout">🚪 退出登录</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-title">
            <h1>欢迎回来, <?php echo $user['username']; ?></h1>
            <p>查看您的账户概览</p>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-value">¥<?php echo $user['balance']; ?></div>
                <div class="stat-label">账户余额</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $licenseCount; ?></div>
                <div class="stat-label">有效授权</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $orderCount; ?></div>
                <div class="stat-label">订单数量</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>最近授权</h3>
                <a href="/user/my-products" class="btn btn-sm btn-secondary">查看全部</a>
            </div>
            
            <?php if ($recentLicenses): ?>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php foreach ($recentLicenses as $license): ?>
                <div class="license-item">
                    <div class="license-header">
                        <h4><?php echo $license['product_name']; ?></h4>
                        <?php if ($license['status'] == 1): ?>
                            <span class="status-badge status-active">有效</span>
                        <?php else: ?>
                            <span class="status-badge status-inactive">已禁用</span>
                        <?php endif; ?>
                    </div>
                    <div class="license-key"><?php echo $license['license_key']; ?></div>
                    <div class="license-meta">
                        <span>到期: <?php echo $license['expire_date'] ?: '永久'; ?></span>
                        <span>创建: <?php echo date('Y-m-d', strtotime($license['created_at'])); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #64748b;">
                暂无授权记录，去 <a href="/user/products">购买产品</a> 获取授权
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="/static/js/main.js"></script>
</body>
</html>
