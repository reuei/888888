<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? '仪表盘') ?> - 授权站后台 v1.1.1</title>
    <link rel="stylesheet" href="/static/css/app.css?v=1.0.5">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <span class="brand-mark"></span>
            <span class="brand-name">授权站后台</span>
        </div>
        <nav class="sidebar-menu">
            <a href="/license/admin/dashboard" class="menu-group">仪表盘</a>
            <a href="/license/admin/licenses" class="menu-item">授权管理</a>
            <a href="/license/admin/domains" class="menu-item">域名管理</a>
            <a href="/license/admin/logs" class="menu-item">调用日志</a>
            <a href="/license/admin/logout" class="menu-item">退出登录</a>
        </nav>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-left">
                <h1 class="topbar-title"><?= h($pageTitle ?? '授权站后台') ?></h1>
            </div>
            <div class="topbar-right">
                <span class="topbar-version">授权站 v1.1.1</span>
            </div>
        </header>
        <main class="admin-content">
            <?= $__content__ ?? '' ?>
        </main>
    </div>
    <script src="/static/js/app.js?v=1.0.5"></script>
</body>
</html>
