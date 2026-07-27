<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>产品管理 - QEEFG授权站</title>
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
        .table-box { background: #fff; border: 1px solid #e5e5e5; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #e5e5e5; }
        .table th { background: #fafafa; font-weight: 500; color: #666; }
        .table tr:hover { background: #f5f7fa; }
        .status-active { color: #52c41a; }
        .status-inactive { color: #ff4d4f; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
        .pagination a { padding: 8px 16px; background: #fff; border: 1px solid #e5e5e5; color: #333; }
        .pagination a:hover { border-color: #1890ff; color: #1890ff; }
        .pagination a.active { background: #1890ff; color: #fff; border-color: #1890ff; }
        .empty-msg { text-align: center; color: #999; padding: 60px; }
        .footer { padding: 20px; background: #1a1a1a; color: #fff; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; height: auto; }
            .table-box { overflow-x: auto; }
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
            <a href="/admin/products" class="active">产品管理</a>
            <a href="/admin/settings">系统设置</a>
        </aside>

        <main class="content">
            <h1 class="page-title">产品管理</h1>

            <div class="table-box">
                <?php if (!empty($products)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>产品名称</th>
                            <th>价格</th>
                            <th>状态</th>
                            <th>排序</th>
                            <th>创建时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id'] ?? ''; ?></td>
                            <td><?php echo htmlspecialchars($product['name'] ?? ''); ?></td>
                            <td>¥<?php echo number_format($product['price'] ?? 0, 2); ?></td>
                            <td><span class="<?php echo ($product['status'] ?? 0) == 1 ? 'status-active' : 'status-inactive'; ?>"><?php echo ($product['status'] ?? 0) == 1 ? '上架' : '下架'; ?></span></td>
                            <td><?php echo $product['sort'] ?? 0; ?></td>
                            <td><?php echo $product['created_at'] ?? ''; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-msg">暂无产品数据</div>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> QEEFG授权站 All Rights Reserved.</p>
    </footer>
</body>
</html>