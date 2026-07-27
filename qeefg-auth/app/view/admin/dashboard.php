<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台首页 - QEEFG授权站</title>
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
            <li><a href="/admin/dashboard" class="active">🏠 首页</a></li>
            <li><a href="/admin/users">👥 用户管理</a></li>
            <li><a href="/admin/products">📦 产品管理</a></li>
            <li><a href="/admin/licenses">🔑 授权管理</a></li>
            <li><a href="/admin/orders">💳 订单管理</a></li>
            <li><a href="/admin/settings">⚙️ 系统设置</a></li>
            <li><a href="/admin/logout">🚪 退出登录</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-title">
            <h1>后台管理中心</h1>
            <p>查看系统概览和统计数据</p>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $userCount; ?></div>
                <div class="stat-label">用户总数</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $productCount; ?></div>
                <div class="stat-label">产品数量</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $licenseCount; ?></div>
                <div class="stat-label">授权数量</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">¥<?php echo $revenue ?: '0.00'; ?></div>
                <div class="stat-label">总收入</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div class="card">
                <div class="card-header">
                    <h3>最近用户</h3>
                    <a href="/admin/users" class="btn btn-sm btn-secondary">查看全部</a>
                </div>
                
                <?php if ($recentUsers): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>用户名</th>
                            <th>邮箱</th>
                            <th>注册时间</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $user): ?>
                        <tr>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #64748b;">暂无用户</div>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>最近订单</h3>
                    <a href="/admin/orders" class="btn btn-sm btn-secondary">查看全部</a>
                </div>
                
                <?php if ($recentOrders): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>订单号</th>
                            <th>用户</th>
                            <th>金额</th>
                            <th>状态</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?php echo $order['order_no']; ?></td>
                            <td><?php echo $order['user_name']; ?></td>
                            <td>¥<?php echo $order['amount']; ?></td>
                            <td>
                                <?php if ($order['status'] == 1): ?>
                                    <span class="status-badge status-active">已完成</span>
                                <?php else: ?>
                                    <span class="status-badge status-pending">待支付</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #64748b;">暂无订单</div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="/static/js/main.js"></script>
</body>
</html>
