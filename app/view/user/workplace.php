<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>工作台 - QEEFG授权站</title>
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
            <li><a href="/user/workplace" class="active">📋 工作台</a></li>
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
            <h1>工作台</h1>
            <p>管理您的授权和订单</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>我的授权</h3>
            </div>
            
            <?php if ($licenses): ?>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php foreach ($licenses as $license): ?>
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
                        <span>硬件ID: <?php echo $license['hardware_id'] ?: '未绑定'; ?></span>
                        <span>到期: <?php echo $license['expire_date'] ?: '永久'; ?></span>
                        <span>创建: <?php echo date('Y-m-d H:i', strtotime($license['created_at'])); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #64748b;">
                暂无授权记录
            </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>最近订单</h3>
            </div>
            
            <?php if ($orders): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>订单号</th>
                        <th>金额</th>
                        <th>状态</th>
                        <th>创建时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['order_no']; ?></td>
                        <td>¥<?php echo $order['amount']; ?></td>
                        <td>
                            <?php if ($order['status'] == 1): ?>
                                <span class="status-badge status-active">已完成</span>
                            <?php else: ?>
                                <span class="status-badge status-pending">待支付</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #64748b;">
                暂无订单记录
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="/static/js/main.js"></script>
</body>
</html>
