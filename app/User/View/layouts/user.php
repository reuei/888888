<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? '') ?> - 用户中心</title>
    <link rel="stylesheet" href="/static/css/app.css?v=1.0.5">
</head>
<body class="user-body">
    <header class="user-topbar">
        <div class="user-topbar-inner">
            <a href="/" class="brand">
                <span class="brand-mark"></span>
                <span class="brand-name">玄武发卡</span>
            </a>
            <div class="user-topbar-right">
                <a href="/" class="topbar-link">返回前台</a>
                <span class="topbar-divider">·</span>
                <span class="topbar-user"><?= h($user['nickname'] ?? $user['username']) ?></span>
                <a href="/logout" class="topbar-link">退出</a>
            </div>
        </div>
    </header>
    <div class="user-layout">
        <aside class="user-sidebar">
            <div class="user-card">
                <div class="user-avatar"></div>
                <div class="user-info">
                    <div class="user-name"><?= h($user['nickname'] ?? $user['username']) ?></div>
                    <div class="user-id">ID: <?= (int) ($user['id'] ?? 0) ?></div>
                </div>
            </div>
            <nav class="user-menu">
                <a href="/user" class="menu-item <?= ($activeMenu ?? '') === 'home' ? 'active' : '' ?>"><span class="menu-icon icon-home"></span>首页</a>
                <a href="/user/profile" class="menu-item <?= ($activeMenu ?? '') === 'profile' ? 'active' : '' ?>"><span class="menu-icon icon-user"></span>个人资料</a>
                <a href="/user/orders" class="menu-item <?= ($activeMenu ?? '') === 'orders' ? 'active' : '' ?>"><span class="menu-icon icon-order"></span>我的订单</a>
                <a href="/user/recharge" class="menu-item <?= ($activeMenu ?? '') === 'recharge' ? 'active' : '' ?>"><span class="menu-icon icon-money"></span>账户充值</a>
                <a href="/user/messages" class="menu-item <?= ($activeMenu ?? '') === 'messages' ? 'active' : '' ?>"><span class="menu-icon icon-bell"></span>消息中心</a>
            </nav>
        </aside>
        <main class="user-content">
            <?= $__content__ ?? '' ?>
        </main>
    </div>
    <script src="/static/js/app.js?v=1.0.5"></script>
</body>
</html>
