<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>授权管理 - QEEFG授权站</title>
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
            <li><a href="/admin/licenses" class="active">🔑 授权管理</a></li>
            <li><a href="/admin/orders">💳 订单管理</a></li>
            <li><a href="/admin/settings">⚙️ 系统设置</a></li>
            <li><a href="/admin/logout">🚪 退出登录</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-title">
            <h1>授权管理</h1>
            <p>管理系统授权</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>授权列表</h3>
                <div style="display: flex; gap: 12px;">
                    <button class="btn btn-sm btn-primary" onclick="showToast('功能开发中', 'info')">生成授权</button>
                    <span style="color: #64748b; line-height: 36px;">共 <?php echo $total; ?> 条记录</span>
                </div>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>授权密钥</th>
                        <th>产品</th>
                        <th>用户</th>
                        <th>硬件ID</th>
                        <th>到期时间</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($licenses as $license): ?>
                    <tr>
                        <td><?php echo $license['id']; ?></td>
                        <td style="font-family: monospace; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo $license['license_key']; ?></td>
                        <td><?php echo $license['product_name']; ?></td>
                        <td><?php echo $license['user_name']; ?></td>
                        <td><?php echo $license['hardware_id'] ?: '-'; ?></td>
                        <td><?php echo $license['expire_date'] ?: '永久'; ?></td>
                        <td>
                            <?php if ($license['status'] == 1): ?>
                                <span class="status-badge status-active">有效</span>
                            <?php else: ?>
                                <span class="status-badge status-inactive">已禁用</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($license['created_at'])); ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-secondary" onclick="showToast('功能开发中', 'info')">编辑</a>
                            <?php if ($license['status'] == 1): ?>
                                <button class="btn btn-sm btn-danger" onclick="showToast('功能开发中', 'info')">禁用</button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-success" onclick="showToast('功能开发中', 'info')">启用</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="/admin/licenses?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="/static/js/main.js"></script>
</body>
</html>
