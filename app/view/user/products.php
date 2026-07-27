<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>产品中心 - QEEFG授权站</title>
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
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; display: flex; gap: 20px; }
        .sidebar { width: 200px; background: #fff; border: 1px solid #e5e5e5; padding: 20px 0; height: fit-content; }
        .sidebar a { display: block; padding: 12px 20px; color: #333; font-size: 14px; }
        .sidebar a:hover { background: #f5f7fa; }
        .sidebar a.active { background: #e6f7ff; color: #1890ff; border-left: 3px solid #1890ff; }
        .content { flex: 1; }
        .page-title { font-size: 24px; margin-bottom: 20px; color: #1a1a1a; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .product-card { background: #fff; border: 1px solid #e5e5e5; padding: 24px; }
        .product-card:hover { border-color: #1890ff; }
        .product-name { font-size: 18px; font-weight: 500; margin-bottom: 12px; }
        .product-desc { font-size: 14px; color: #666; margin-bottom: 16px; min-height: 40px; }
        .product-price { font-size: 24px; color: #ff4d4f; font-weight: 600; margin-bottom: 16px; }
        .product-price span { font-size: 12px; color: #999; }
        .buy-btn { display: block; text-align: center; background: #1890ff; color: #fff; padding: 10px 0; }
        .buy-btn:hover { background: #40a9ff; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 30px; }
        .pagination a { padding: 8px 16px; background: #fff; border: 1px solid #e5e5e5; color: #333; }
        .pagination a:hover { border-color: #1890ff; color: #1890ff; }
        .pagination a.active { background: #1890ff; color: #fff; border-color: #1890ff; }
        .empty-msg { text-align: center; color: #999; padding: 60px; background: #fff; border: 1px solid #e5e5e5; }
        .footer { padding: 20px; background: #1a1a1a; color: #fff; text-align: center; margin-top: 40px; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; }
            .product-grid { grid-template-columns: 1fr; }
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
            <a href="/user/dashboard">用户中心</a>
            <a href="/user/workplace">工作台</a>
            <a href="/user/products" class="active">产品中心</a>
            <a href="/user/my-products">我的产品</a>
            <a href="/user/balance">余额管理</a>
            <a href="/user/settings">账户设置</a>
        </aside>

        <main class="content">
            <h1 class="page-title">产品中心</h1>

            <?php if (!empty($products)): ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-name"><?php echo htmlspecialchars($product['name'] ?? ''); ?></div>
                    <div class="product-desc"><?php echo htmlspecialchars($product['description'] ?? '暂无描述'); ?></div>
                    <div class="product-price">¥<?php echo number_format($product['price'] ?? 0, 2); ?><span>/永久</span></div>
                    <a href="#" class="buy-btn">立即购买</a>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div class="empty-msg">暂无可用产品</div>
            <?php endif; ?>
        </main>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> QEEFG授权站 All Rights Reserved.</p>
    </footer>
</body>
</html>