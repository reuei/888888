<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>产品列表 - QEEFG授权站</title>
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
            <li><a href="/user/products" class="active">🛒 产品列表</a></li>
            <li><a href="/user/my-products">💼 我的产品</a></li>
            <li><a href="/user/balance">💰 余额管理</a></li>
            <li><a href="/user/settings">⚙️ 账户设置</a></li>
            <li><a href="/user/feedback">📝 意见反馈</a></li>
            <li><a href="/logout">🚪 退出登录</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-title">
            <h1>产品列表</h1>
            <p>选择适合您的授权方案</p>
        </div>

        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if ($product['icon']): ?>
                        <img src="<?php echo $product['icon']; ?>" alt="<?php echo $product['name']; ?>">
                    <?php else: ?>
                        📦
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3><?php echo $product['name']; ?></h3>
                    <p><?php echo $product['description']; ?></p>
                    <div class="product-price">
                        <span>¥<?php echo $product['price']; ?></span>
                        <button class="btn btn-primary btn-sm" onclick="showToast('功能开发中', 'info')">购买</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script src="/static/js/main.js"></script>
</body>
</html>
