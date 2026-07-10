<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? '') ?> - 玄武后台</title>
    <link rel="stylesheet" href="/static/css/app.css?v=1.0.5">
</head>
<body class="admin-body <?= !empty($isScreen) ? 'screen-body' : '' ?>">
    <?php if (empty($isScreen)): ?>
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <span class="brand-mark"></span>
            <span class="brand-name">玄武后台</span>
        </div>
        <nav class="sidebar-menu">
            <a href="/admin" class="menu-group <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                <span class="menu-icon icon-home"></span>总概览
            </a>
            <a href="/admin/screen" class="menu-group <?= ($activeMenu ?? '') === 'screen' ? 'active' : '' ?>">
                <span class="menu-icon icon-screen"></span>数据大屏
            </a>
            <div class="menu-section">店铺管理</div>
            <a href="/admin/shop/users" class="menu-item <?= ($activeMenu ?? '') === 'shop_users' ? 'active' : '' ?>">用户管理</a>
            <a href="/admin/shop/realname" class="menu-item <?= ($activeMenu ?? '') === 'shop_realname' ? 'active' : '' ?>">实名管理</a>
            <a href="/admin/shop/qualification" class="menu-item <?= ($activeMenu ?? '') === 'shop_qualification' ? 'active' : '' ?>">资质管理</a>
            <a href="/admin/shop/certification" class="menu-item <?= ($activeMenu ?? '') === 'shop_certification' ? 'active' : '' ?>">认证管理</a>
            <a href="/admin/shop/risk" class="menu-item <?= ($activeMenu ?? '') === 'shop_risk' ? 'active' : '' ?>">风控管理</a>
            <a href="/admin/shop/service" class="menu-item <?= ($activeMenu ?? '') === 'shop_service' ? 'active' : '' ?>">客服管理</a>
            <div class="menu-section">消息管理</div>
            <a href="/admin/message/publish" class="menu-item <?= ($activeMenu ?? '') === 'message_publish' ? 'active' : '' ?>">发布消息</a>
            <a href="/admin/message/list" class="menu-item <?= ($activeMenu ?? '') === 'message_list' ? 'active' : '' ?>">消息管理</a>
            <div class="menu-section">公告管理</div>
            <a href="/admin/notice/publish" class="menu-item <?= ($activeMenu ?? '') === 'notice_publish' ? 'active' : '' ?>">发布公告</a>
            <a href="/admin/notice/list" class="menu-item <?= ($activeMenu ?? '') === 'notice_list' ? 'active' : '' ?>">公告管理</a>
            <div class="menu-section">系统配置</div>
            <a href="/admin/system/site" class="menu-item <?= ($activeMenu ?? '') === 'system_site' ? 'active' : '' ?>">站点配置</a>
            <a href="/admin/system/update" class="menu-item <?= ($activeMenu ?? '') === 'system_update' ? 'active' : '' ?>">系统更新</a>
            <a href="/admin/system/withdraw" class="menu-item <?= ($activeMenu ?? '') === 'system_withdraw' ? 'active' : '' ?>">提现管理</a>
            <a href="/admin/system/channel" class="menu-item <?= ($activeMenu ?? '') === 'system_channel' ? 'active' : '' ?>">通道管理</a>
            <div class="menu-section">数据管理</div>
            <a href="/admin/data/log" class="menu-item <?= ($activeMenu ?? '') === 'data_log' ? 'active' : '' ?>">日志管理</a>
            <a href="/admin/data/server" class="menu-item <?= ($activeMenu ?? '') === 'data_server' ? 'active' : '' ?>">服务器日志</a>
            <a href="/admin/data/database" class="menu-item <?= ($activeMenu ?? '') === 'data_database' ? 'active' : '' ?>">数据库日志</a>
            <a href="/admin/data/login" class="menu-item <?= ($activeMenu ?? '') === 'data_login' ? 'active' : '' ?>">登录日志</a>
        </nav>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-left">
                <h1 class="topbar-title"><?= h($pageTitle ?? '玄武后台') ?></h1>
            </div>
            <div class="topbar-right">
                <span class="topbar-version">v1.0.5 · 授权v1.1.1</span>
                <a href="/admin/logout" class="btn btn-line btn-sm">退出登录</a>
            </div>
        </header>
        <main class="admin-content">
            <?= $__content__ ?? '' ?>
        </main>
    </div>
    <?php else: ?>
    <main class="screen-content">
        <?= $__content__ ?? '' ?>
    </main>
    <?php endif; ?>
    <script src="/static/js/app.js?v=1.0.5"></script>
</body>
</html>
