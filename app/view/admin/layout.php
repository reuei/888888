<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '管理后台') ?> - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-home" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></symbol>
            <symbol id="i-platform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></symbol>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
            <symbol id="i-box" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></symbol>
            <symbol id="i-orders" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></symbol>
            <symbol id="i-settings" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></symbol>
            <symbol id="i-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></symbol>
            <symbol id="i-logout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></symbol>
            <symbol id="i-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></symbol>
            <symbol id="i-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></symbol>
            <symbol id="i-trash" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></symbol>
            <symbol id="i-download" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-wallet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></symbol>
        </defs>
    </svg>

    <header class="site-header" style="background: #1a1a2e; border-bottom: 1px solid #2a2a3e;">
        <div class="header-inner container">
            <a href="/" class="logo" style="color: #fff;"><span class="logo-mark">☁</span><span>熵云</span><span style="font-size: 13px; color: #687690; margin-left: 8px;">管理后台</span></a>
            <div class="auth-links" style="display: flex; align-items: center; gap: 16px;">
                <span style="color: #a0a8c0; font-size: 13px;">管理员：<?= htmlspecialchars($admin['username'] ?? '') ?></span>
                <a href="/" class="btn btn-ghost btn-sm" style="color: #a0a8c0;">前台</a>
                <a href="/admin/logout" class="btn btn-sm" style="background: #ff4d4f; color: #fff;">退出</a>
            </div>
        </div>
    </header>

    <div class="admin-layout" style="display: flex; min-height: calc(100vh - 60px);">
        <aside class="admin-sidebar">
            <div class="icon-rail">
                <div class="menu-item">
                    <a href="/admin/dashboard" class="menu-link<?= ($activeMenu ?? '') === 'dashboard' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-home"/></svg>
                        <span>控制台</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/admin/users" class="menu-link<?= ($activeMenu ?? '') === 'users' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-user"/></svg>
                        <span>用户管理</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/admin/products" class="menu-link<?= ($activeMenu ?? '') === 'products' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-box"/></svg>
                        <span>产品管理</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/admin/licenses" class="menu-link<?= ($activeMenu ?? '') === 'licenses' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-key"/></svg>
                        <span>授权管理</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/admin/orders" class="menu-link<?= ($activeMenu ?? '') === 'orders' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-orders"/></svg>
                        <span>订单管理</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/admin/settings" class="menu-link<?= ($activeMenu ?? '') === 'settings' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-settings"/></svg>
                        <span>系统设置</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/admin/documents" class="menu-link<?= ($activeMenu ?? '') === 'documents' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-doc"/></svg>
                        <span>文档管理</span>
                    </a>
                </div>
            </div>
        </aside>

        <main class="admin-content">
            <?php if (isset($toast)): ?>
            <div class="toast toast-<?= $toast['type'] ?? 'success' ?>" style="position: fixed; top: 80px; right: 20px; z-index: 9999; padding: 12px 24px; color: #fff; font-size: 14px; background: <?= ($toast['type'] ?? 'success') === 'success' ? '#52c41a' : '#ff4d4f' ?>;">
                <?= htmlspecialchars($toast['message'] ?? '') ?>
            </div>
            <?php endif; ?>
            <?= $content ?>
        </main>
    </div>

    <div class="toast-center" id="toastContainer"></div>

    <script src="/static/js/main.js"></script>
</body>
</html>