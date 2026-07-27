<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的产品 - QEEFG授权站</title>
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
        .table-box { background: #fff; border: 1px solid #e5e5e5; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #e5e5e5; }
        .table th { background: #fafafa; font-weight: 500; color: #666; }
        .table tr:hover { background: #f5f7fa; }
        .status-active { color: #52c41a; }
        .status-inactive { color: #ff4d4f; }
        .license-key { font-family: monospace; background: #f5f5f5; padding: 4px 8px; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
        .pagination a { padding: 8px 16px; background: #fff; border: 1px solid #e5e5e5; color: #333; }
        .pagination a:hover { border-color: #1890ff; color: #1890ff; }
        .pagination a.active { background: #1890ff; color: #fff; border-color: #1890ff; }
        .empty-msg { text-align: center; color: #999; padding: 60px; }
        .footer { padding: 20px; background: #1a1a1a; color: #fff; text-align: center; margin-top: 40px; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; }
            .table-box { overflow-x: auto; }
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
            <a href="/user/products">产品中心</a>
            <a href="/user/my-products" class="active">我的产品</a>
            <a href="/user/balance">余额管理</a>
            <a href="/user/settings">账户设置</a>
        </aside>

        <main class="content">
            <h1 class="page-title">我的产品</h1>

            <div class="table-box">
                <?php if (!empty($licenses)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>授权码</th>
                            <th>产品名称</th>
                            <th>绑定域名</th>
                            <th>状态</th>
                            <th>到期时间</th>
                            <th>创建时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($licenses as $license): ?>
                        <tr>
                            <td><span class="license-key"><?php echo htmlspecialchars($license['license_key'] ?? ''); ?></span></td>
                            <td><?php echo htmlspecialchars($license['product_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($license['domain'] ?? '未绑定'); ?></td>
                            <td><span class="<?php echo ($license['status'] ?? 0) == 1 ? 'status-active' : 'status-inactive'; ?>"><?php echo ($license['status'] ?? 0) == 1 ? '有效' : '无效'; ?></span></td>
                            <td><?php echo $license['expire_time'] ?? '永久'; ?></td>
                            <td><?php echo $license['created_at'] ?? ''; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-msg">暂无授权产品，<a href="/user/products" style="color: #1890ff;">立即购买</a></div>
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