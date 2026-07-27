<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户管理 - QEEFG授权站</title>
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
            <li><a href="/admin/users" class="active">👥 用户管理</a></li>
            <li><a href="/admin/products">📦 产品管理</a></li>
            <li><a href="/admin/licenses">🔑 授权管理</a></li>
            <li><a href="/admin/orders">💳 订单管理</a></li>
            <li><a href="/admin/settings">⚙️ 系统设置</a></li>
            <li><a href="/admin/logout">🚪 退出登录</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-title">
            <h1>用户管理</h1>
            <p>管理系统用户</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>用户列表</h3>
                <span style="color: #64748b;">共 <?php echo $total; ?> 条记录</span>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>用户名</th>
                        <th>邮箱</th>
                        <th>QQ</th>
                        <th>手机号</th>
                        <th>余额</th>
                        <th>状态</th>
                        <th>注册时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['qq'] ?: '-'; ?></td>
                        <td><?php echo $user['phone'] ?: '-'; ?></td>
                        <td>¥<?php echo $user['balance']; ?></td>
                        <td>
                            <?php if ($user['status'] == 1): ?>
                                <span class="status-badge status-active">启用</span>
                            <?php else: ?>
                                <span class="status-badge status-inactive">禁用</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-secondary" onclick="showToast('功能开发中', 'info')">编辑</a>
                            <?php if ($user['status'] == 1): ?>
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
                <a href="/admin/users?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="/static/js/main.js"></script>
</body>
</html>
