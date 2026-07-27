<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>工作台 - QEEFG授权站</title>
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
        .section { background: #fff; padding: 24px; border: 1px solid #e5e5e5; margin-bottom: 20px; }
        .section-title { font-size: 18px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e5e5e5; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e5e5; }
        .table th { background: #fafafa; font-weight: 500; color: #666; }
        .status-active { color: #52c41a; }
        .status-inactive { color: #ff4d4f; }
        .empty-msg { text-align: center; color: #999; padding: 40px; }
        .footer { padding: 20px; background: #1a1a1a; color: #fff; text-align: center; margin-top: 40px; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; }
            .table { display: block; overflow-x: auto; }
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
            <a href="/user/workplace" class="active">工作台</a>
            <a href="/user/products">产品中心</a>
            <a href="/user/my-products">我的产品</a>
            <a href="/user/balance">余额管理</a>
            <a href="/user/settings">账户设置</a>
        </aside>

        <main class="content">
            <h1 class="page-title">工作台</h1>

            <div class="section">
                <h2 class="section-title">最近授权</h2>
                <?php if (!empty($licenses)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>授权码</th>
                            <th>产品名称</th>
                            <th>状态</th>
                            <th>创建时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($licenses as $license): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($license['license_key'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($license['product_name'] ?? ''); ?></td>
                            <td><span class="<?php echo ($license['status'] ?? 0) == 1 ? 'status-active' : 'status-inactive'; ?>"><?php echo ($license['status'] ?? 0) == 1 ? '有效' : '无效'; ?></span></td>
                            <td><?php echo $license['created_at'] ?? ''; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-msg">暂无授权记录</div>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2 class="section-title">最近订单</h2>
                <?php if (!empty($orders)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>订单号</th>
                            <th>产品名称</th>
                            <th>金额</th>
                            <th>状态</th>
                            <th>创建时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_no'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($order['product_name'] ?? ''); ?></td>
                            <td>¥<?php echo number_format($order['amount'] ?? 0, 2); ?></td>
                            <td><span class="<?php echo ($order['payment_status'] ?? 0) == 1 ? 'status-active' : 'status-inactive'; ?>"><?php echo ($order['payment_status'] ?? 0) == 1 ? '已支付' : '待支付'; ?></span></td>
                            <td><?php echo $order['created_at'] ?? ''; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-msg">暂无订单记录</div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> QEEFG授权站 All Rights Reserved.</p>
    </footer>
</body>
</html>