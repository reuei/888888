<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订单管理 - QEEFG授权站</title>
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
                    <li><a href="/">前台首页</a></li>
                </ul>
                <div class="nav-actions">
                    <span style="color: #64748b; margin-right: 16px;">欢迎, <?php echo $admin['username']; ?></span>
                    <a href="/admin/logout" class="btn btn-sm btn-secondary">退出</a>
                    <button class="icon-btn theme-toggle" id="theme-toggle">☀️</button>
                    <button class="icon-btn lang-toggle" id="lang-toggle">CN</button>
                </div>
            </nav>
        </div>
    </header>

    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="/admin/dashboard">🏠 首页</a></li>
            <li><a href="/admin/users">👥 用户管理</a></li>
            <li><a href="/admin/products">📦 产品管理</a></li>
            <li><a href="/admin/licenses">🔑 授权管理</a></li>
            <li><a href="/admin/orders" class="active">💳 订单管理</a></li>
            <li><a href="/admin/settings">⚙️ 系统设置</a></li>
            <li><a href="/admin/logout">🚪 退出登录</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-title">
            <h1>订单管理</h1>
            <p>管理系统订单</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>订单列表</h3>
                <span style="color: #64748b;">共 <?php echo $total; ?> 条记录</span>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>订单号</th>
                        <th>用户</th>
                        <th>产品</th>
                        <th>金额</th>
                        <th>支付方式</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['order_no']; ?></td>
                        <td><?php echo $order['user_name']; ?></td>
                        <td><?php echo $order['product_name']; ?></td>
                        <td>¥<?php echo $order['amount']; ?></td>
                        <td><?php echo $order['payment_method'] ?: '-'; ?></td>
                        <td>
                            <?php if ($order['status'] == 1): ?>
                                <span class="status-badge status-active">已完成</span>
                            <?php else: ?>
                                <span class="status-badge status-pending">待支付</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-secondary" onclick="showToast('功能开发中', 'info')">详情</a>
                            <?php if ($order['status'] == 0): ?>
                                <button class="btn btn-sm btn-success" onclick="showToast('功能开发中', 'info')">确认支付</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="/admin/orders?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="/static/js/main.js"></script>
</body>
</html>
