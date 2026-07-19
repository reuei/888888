<?php
/**
 * 后台管理头部模板 v4.0.0
 * 中央纪委国家监委网站 CMS 系统
 */
if (!defined('SYSTEM_INIT')) { require_once __DIR__ . '/../includes/init.php'; }
require_admin();

$admin_page = basename($_SERVER['SCRIPT_NAME'], '.php');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css?v=4.0.0'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --sidebar-bg: #1e1e2d;
        --sidebar-hover: #27273a;
        --sidebar-active: #c62828;
        --sidebar-text: #a2a3b7;
        --sidebar-text-hover: #fff;
        --sidebar-width: 250px;
        --sidebar-collapsed-width: 0px;
        --header-height: 60px;
        --accent: #c62828;
        --accent-light: #ef5350;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    html, body { height: 100%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Microsoft YaHei', sans-serif; background: #f1f3f5; color: #333; }
    a { text-decoration: none; color: inherit; }
    ul { list-style: none; }

    .admin-wrapper { display: flex; min-height: 100vh; }

    /* Sidebar */
    .admin-sidebar {
        width: var(--sidebar-width);
        min-width: var(--sidebar-width);
        background: var(--sidebar-bg);
        color: var(--sidebar-text);
        display: flex;
        flex-direction: column;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1000;
        transition: transform 0.3s ease;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .admin-sidebar.collapsed { transform: translateX(-100%); }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 22px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        color: #fff;
        font-size: 16px;
        font-weight: 700;
    }
    .sidebar-brand i {
        font-size: 24px;
        color: var(--accent);
    }

    .sidebar-nav { flex: 1; padding: 12px 0; }
    .sidebar-nav ul li { margin: 2px 0; }
    .sidebar-nav ul li a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 22px;
        color: var(--sidebar-text);
        font-size: 14px;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    .sidebar-nav ul li a:hover {
        background: var(--sidebar-hover);
        color: var(--sidebar-text-hover);
    }
    .sidebar-nav ul li a i {
        width: 20px;
        text-align: center;
        font-size: 15px;
    }
    .sidebar-nav ul li.active a {
        background: rgba(198,40,40,0.15);
        color: var(--accent-light);
        border-left-color: var(--accent);
        font-weight: 600;
    }

    .sidebar-footer {
        padding: 16px 22px;
        border-top: 1px solid rgba(255,255,255,0.06);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .sidebar-footer a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        color: var(--sidebar-text);
        font-size: 13px;
        transition: color 0.2s;
    }
    .sidebar-footer a:hover { color: #fff; }
    .sidebar-footer a i { width: 20px; text-align: center; }

    /* Main Content */
    .admin-main {
        margin-left: var(--sidebar-width);
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        transition: margin-left 0.3s ease;
    }
    .admin-main.expanded { margin-left: 0; }

    /* Admin Header */
    .admin-header {
        height: var(--header-height);
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        position: sticky;
        top: 0;
        z-index: 999;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .admin-header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .sidebar-toggle {
        background: none;
        border: none;
        font-size: 20px;
        color: #6b7280;
        cursor: pointer;
        padding: 6px;
        border-radius: 6px;
        transition: background 0.2s, color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
    }
    .sidebar-toggle:hover { background: #f3f4f6; color: #333; }
    .admin-greeting {
        font-size: 14px;
        color: #374151;
        font-weight: 500;
    }
    .admin-greeting strong { color: var(--accent); }

    .admin-header-right {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .admin-time {
        font-size: 13px;
        color: #9ca3af;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    /* Admin Content */
    .admin-content {
        flex: 1;
        padding: 24px;
    }

    /* Mobile Overlay */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 999;
    }
    .sidebar-overlay.show { display: block; }

    /* Responsive */
    @media (max-width: 768px) {
        .admin-sidebar { transform: translateX(-100%); }
        .admin-sidebar.mobile-open { transform: translateX(0); }
        .admin-main { margin-left: 0; }
        .admin-content { padding: 16px; }
    }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <i class="fas fa-shield-haltered"></i>
            <span>后台管理</span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li class="<?php echo $admin_page === 'index' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url(); ?>"><i class="fas fa-tachometer-alt"></i> 仪表盘</a>
                </li>
                <li class="<?php echo $admin_page === 'articles' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('articles.php'); ?>"><i class="fas fa-newspaper"></i> 文章管理</a>
                </li>
                <li class="<?php echo $admin_page === 'categories' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('categories.php'); ?>"><i class="fas fa-folder"></i> 分类管理</a>
                </li>
                <li class="<?php echo $admin_page === 'carousel' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('carousel.php'); ?>"><i class="fas fa-images"></i> 轮播图管理</a>
                </li>
                <li class="<?php echo $admin_page === 'popups' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('popups.php'); ?>"><i class="fas fa-window-maximize"></i> 弹窗管理</a>
                </li>
                <li class="<?php echo $admin_page === 'nav' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('nav.php'); ?>"><i class="fas fa-bars"></i> 导航管理</a>
                </li>
                <li class="<?php echo $admin_page === 'users' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('users.php'); ?>"><i class="fas fa-users"></i> 用户管理</a>
                </li>
                <li class="<?php echo $admin_page === 'messages' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('messages.php'); ?>"><i class="fas fa-envelope"></i> 留言管理</a>
                </li>
                <li class="<?php echo $admin_page === 'reports' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('reports.php'); ?>"><i class="fas fa-flag"></i> 举报管理</a>
                </li>
                <li class="<?php echo $admin_page === 'settings' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('settings.php'); ?>"><i class="fas fa-cog"></i> 系统设置</a>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?php echo site_url(); ?>" target="_blank"><i class="fas fa-external-link-alt"></i> 访问网站</a>
            <a href="<?php echo site_url('logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> 退出</a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="admin-main" id="adminMain">
        <header class="admin-header">
            <div class="admin-header-left">
                <button class="sidebar-toggle" id="sidebarToggle" title="切换侧边栏">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="admin-greeting">欢迎，<strong><?php echo htmlspecialchars(current_username()); ?></strong></span>
            </div>
            <div class="admin-header-right">
                <span class="admin-time" id="adminTime"><?php echo date('Y-m-d H:i'); ?></span>
            </div>
        </header>

        <div class="admin-content">

<script>
(function(){
    var sidebar = document.getElementById('adminSidebar'),
        main = document.getElementById('adminMain'),
        overlay = document.getElementById('sidebarOverlay'),
        toggle = document.getElementById('sidebarToggle'),
        isMobile = window.innerWidth <= 768,
        sidebarOpen = !isMobile;

    function openSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('mobile-open');
            overlay.classList.add('show');
        } else {
            sidebar.classList.remove('collapsed');
            main.classList.remove('expanded');
        }
        sidebarOpen = true;
    }

    function closeSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        } else {
            sidebar.classList.add('collapsed');
            main.classList.add('expanded');
        }
        sidebarOpen = false;
    }

    toggle.addEventListener('click', function() {
        sidebarOpen ? closeSidebar() : openSidebar();
    });

    overlay.addEventListener('click', closeSidebar);

    window.addEventListener('resize', function() {
        var wasMobile = isMobile;
        isMobile = window.innerWidth <= 768;
        if (wasMobile !== isMobile) {
            sidebar.classList.remove('collapsed', 'mobile-open');
            main.classList.remove('expanded');
            overlay.classList.remove('show');
            sidebarOpen = !isMobile;
        }
    });

    // 实时时钟
    function updateTime() {
        var now = new Date();
        var str = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0') + ' ' +
            String(now.getHours()).padStart(2, '0') + ':' +
            String(now.getMinutes()).padStart(2, '0');
        var el = document.getElementById('adminTime');
        if (el) el.textContent = str;
    }
    setInterval(updateTime, 30000);
})();
</script>