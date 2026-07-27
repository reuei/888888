<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>余额管理 - QEEFG授权站</title>
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
        .balance-card { background: #fff; padding: 30px; border: 1px solid #e5e5e5; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .balance-info h2 { font-size: 14px; color: #666; margin-bottom: 8px; }
        .balance-amount { font-size: 36px; font-weight: 600; color: #1890ff; }
        .recharge-btn { background: #1890ff; color: #fff; padding: 12px 32px; border: none; cursor: pointer; font-size: 16px; }
        .recharge-btn:hover { background: #40a9ff; }
        .section-title { font-size: 18px; margin-bottom: 16px; }
        .table-box { background: #fff; border: 1px solid #e5e5e5; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 14px 16px; text-align: left; border-bottom: 1px solid #e5e5e5; }
        .table th { background: #fafafa; font-weight: 500; color: #666; }
        .type-income { color: #52c41a; }
        .type-expense { color: #ff4d4f; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
        .pagination a { padding: 8px 16px; background: #fff; border: 1px solid #e5e5e5; color: #333; }
        .pagination a.active { background: #1890ff; color: #fff; border-color: #1890ff; }
        .empty-msg { text-align: center; color: #999; padding: 40px; }
        .footer { padding: 20px; background: #1a1a1a; color: #fff; text-align: center; margin-top: 40px; }
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar { width: 100%; }
            .balance-card { flex-direction: column; gap: 20px; text-align: center; }
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
            <a href="/user/my-products">我的产品</a>
            <a href="/user/balance" class="active">余额管理</a>
            <a href="/user/settings">账户设置</a>
        </aside>

        <main class="content">
            <h1 class="page-title">余额管理</h1>

            <div class="balance-card">
                <div class="balance-info">
                    <h2>当前余额</h2>
                    <div class="balance-amount">¥<?php echo number_format($user['balance'] ?? 0, 2); ?></div>
                </div>
                <button class="recharge-btn">充值</button>
            </div>

            <h2 class="section-title">余额明细</h2>
            <div class="table-box">
                <?php if (!empty($logs)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>类型</th>
                            <th>金额</th>
                            <th>说明</th>
                            <th>时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><span class="<?php echo ($log['type'] ?? '') == 'income' ? 'type-income' : 'type-expense'; ?>"><?php echo ($log['type'] ?? '') == 'income' ? '收入' : '支出'; ?></span></td>
                            <td class="<?php echo ($log['type'] ?? '') == 'income' ? 'type-income' : 'type-expense'; ?>"><?php echo ($log['type'] ?? '') == 'income' ? '+' : '-'; ?>¥<?php echo number_format(abs($log['amount'] ?? 0), 2); ?></td>
                            <td><?php echo htmlspecialchars($log['description'] ?? ''); ?></td>
                            <td><?php echo $log['created_at'] ?? ''; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-msg">暂无余额记录</div>
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