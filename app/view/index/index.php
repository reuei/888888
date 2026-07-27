<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QEEFG授权站 - 首页</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #fff; color: #333; line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        .header { background: #fff; border-bottom: 1px solid #e5e5e5; padding: 0 20px; }
        .header-inner { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; height: 60px; }
        .logo { font-size: 20px; font-weight: 600; color: #1a1a1a; }
        .nav { display: flex; gap: 30px; }
        .nav a { color: #666; font-size: 14px; padding: 8px 0; }
        .nav a:hover { color: #1890ff; }
        .nav-btn { background: #1890ff; color: #fff; padding: 8px 20px; border: none; cursor: pointer; }
        .nav-btn:hover { background: #40a9ff; }
        .hero { padding: 80px 20px; text-align: center; background: linear-gradient(135deg, #f5f7fa 0%, #fff 100%); }
        .hero h1 { font-size: 42px; color: #1a1a1a; margin-bottom: 20px; }
        .hero p { font-size: 18px; color: #666; margin-bottom: 40px; }
        .hero-btns { display: flex; gap: 20px; justify-content: center; }
        .btn { display: inline-block; padding: 12px 32px; font-size: 16px; border: none; cursor: pointer; }
        .btn-primary { background: #1890ff; color: #fff; }
        .btn-primary:hover { background: #40a9ff; }
        .btn-outline { background: #fff; color: #1890ff; border: 1px solid #1890ff; }
        .btn-outline:hover { background: #f0f8ff; }
        .stats { display: flex; justify-content: center; gap: 60px; padding: 60px 20px; background: #fafafa; }
        .stat-item { text-align: center; }
        .stat-num { font-size: 36px; font-weight: 600; color: #1890ff; }
        .stat-label { font-size: 14px; color: #666; margin-top: 8px; }
        .products { padding: 60px 20px; max-width: 1200px; margin: 0 auto; }
        .section-title { font-size: 28px; text-align: center; margin-bottom: 40px; color: #1a1a1a; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }
        .product-card { background: #fff; border: 1px solid #e5e5e5; padding: 24px; }
        .product-card:hover { border-color: #1890ff; }
        .product-name { font-size: 18px; font-weight: 500; margin-bottom: 12px; }
        .product-desc { font-size: 14px; color: #666; margin-bottom: 16px; }
        .product-price { font-size: 20px; color: #ff4d4f; font-weight: 600; }
        .product-price span { font-size: 12px; color: #999; }
        .features { padding: 60px 20px; background: #fafafa; }
        .features-inner { max-width: 1200px; margin: 0 auto; }
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 40px; }
        .feature-item { text-align: center; padding: 20px; }
        .feature-icon { font-size: 40px; margin-bottom: 16px; }
        .feature-title { font-size: 18px; margin-bottom: 12px; }
        .feature-desc { font-size: 14px; color: #666; }
        .footer { padding: 40px 20px; background: #1a1a1a; color: #fff; text-align: center; }
        .footer a { color: #1890ff; }
        @media (max-width: 768px) {
            .header-inner { flex-direction: column; height: auto; padding: 15px 0; }
            .nav { margin-top: 15px; flex-wrap: wrap; justify-content: center; gap: 15px; }
            .hero { padding: 40px 15px; }
            .hero h1 { font-size: 28px; }
            .hero p { font-size: 14px; }
            .hero-btns { flex-direction: column; align-items: center; }
            .stats { flex-direction: column; gap: 30px; }
            .product-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <a href="/" class="logo">QEEFG授权站</a>
            <nav class="nav">
                <a href="/">首页</a>
                <a href="/license-query">授权查询</a>
                <a href="/documents">文档中心</a>
                <a href="/login" class="nav-btn">登录</a>
                <a href="/register" class="nav-btn btn-outline">注册</a>
            </nav>
        </div>
    </header>

    <section class="hero">
        <h1>专业的软件授权管理平台</h1>
        <p>安全、稳定、高效的软件授权解决方案，为您的产品保驾护航</p>
        <div class="hero-btns">
            <a href="/register" class="btn btn-primary">立即注册</a>
            <a href="/login" class="btn btn-outline">用户登录</a>
        </div>
    </section>

    <section class="stats">
        <div class="stat-item">
            <div class="stat-num"><?php echo $stats['products'] ?? 0; ?></div>
            <div class="stat-label">产品数量</div>
        </div>
        <div class="stat-item">
            <div class="stat-num"><?php echo $stats['users'] ?? 0; ?></div>
            <div class="stat-label">注册用户</div>
        </div>
        <div class="stat-item">
            <div class="stat-num"><?php echo $stats['licenses'] ?? 0; ?></div>
            <div class="stat-label">授权总数</div>
        </div>
    </section>

    <section class="products">
        <h2 class="section-title">热门产品</h2>
        <div class="product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-name"><?php echo htmlspecialchars($product['name'] ?? ''); ?></div>
                    <div class="product-desc"><?php echo htmlspecialchars($product['description'] ?? ''); ?></div>
                    <div class="product-price">¥<?php echo number_format($product['price'] ?? 0, 2); ?><span>/永久</span></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; color: #999; padding: 40px;">暂无产品</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="features">
        <div class="features-inner">
            <h2 class="section-title">平台优势</h2>
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="feature-icon">🔒</div>
                    <div class="feature-title">安全可靠</div>
                    <div class="feature-desc">采用高强度加密算法，确保授权数据安全</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <div class="feature-title">快速稳定</div>
                    <div class="feature-desc">毫秒级授权验证，高并发架构支撑</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <div class="feature-title">数据统计</div>
                    <div class="feature-desc">完善的统计分析，实时掌握授权动态</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🛠️</div>
                    <div class="feature-title">易于集成</div>
                    <div class="feature-desc">提供标准API接口，快速接入您的产品</div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> QEEFG授权站 All Rights Reserved. <a href="/admin/login">管理后台</a></p>
    </footer>
</body>
</html>