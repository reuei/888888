<div class="license-admin">
    <aside class="la-sidebar">
        <div class="sidebar-brand">
            <span class="brand-mark"></span>
            <span class="brand-name">授权站后台</span>
        </div>
        <nav class="sidebar-menu">
            <a href="/license/admin/dashboard" class="menu-item">仪表盘</a>
            <a href="/license/admin/licenses" class="menu-item">授权管理</a>
            <a href="/license/admin/domains" class="menu-item">域名管理</a>
            <a href="/license/admin/logs" class="menu-item">调用日志</a>
            <a href="/license/admin/logout" class="menu-item">退出登录</a>
        </nav>
    </aside>
    <main class="la-content">
        <header class="la-topbar">
            <h1><?= h($pageTitle ?? '仪表盘') ?></h1>
            <span class="badge badge-info">v1.1.1</span>
        </header>
        <div class="la-body">
            <?= $__content__ ?? '' ?>
        </div>
    </main>
</div>
