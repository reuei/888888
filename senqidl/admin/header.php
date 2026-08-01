<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/Auth.php';
Auth::requireLogin();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$adminUser = Auth::user();
$siteName = DB::getSettingValue('site_name', SITE_NAME);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($pageTitle ?? '管理后台'); ?> - <?php echo h($siteName); ?></title>
    <link rel="icon" href="<?php echo h(DB::getSettingValue('favicon', '/assets/images/favicon.ico')); ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/admin/assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <div class="logo-icon"><?php echo h(mb_substr($siteName, 0, 1)); ?></div>
                <div>
                    <div class="logo-text"><?php echo h($siteName); ?></div>
                    <div class="logo-sub">管理后台</div>
                </div>
            </div>
            <nav class="admin-sidebar-menu">
                <div class="menu-section">主菜单</div>
                <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <span class="menu-icon">🏠</span> 仪表盘
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/settings.php" class="<?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
                    <span class="menu-icon">⚙️</span> 站点设置
                </a>

                <div class="menu-section">内容管理</div>
                <a href="<?php echo SITE_URL; ?>/admin/pages/slides.php" class="<?php echo $currentPage === 'slides' ? 'active' : ''; ?>">
                    <span class="menu-icon">🖼️</span> 幻灯片管理
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/pages/services.php" class="<?php echo $currentPage === 'services' ? 'active' : ''; ?>">
                    <span class="menu-icon">🛠️</span> 服务管理
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/pages/cases.php" class="<?php echo $currentPage === 'cases' ? 'active' : ''; ?>">
                    <span class="menu-icon">📁</span> 案例管理
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/pages/news.php" class="<?php echo $currentPage === 'news' ? 'active' : ''; ?>">
                    <span class="menu-icon">📰</span> 新闻管理
                </a>

                <div class="menu-section">系统</div>
                <a href="<?php echo SITE_URL; ?>/admin/pages/users.php" class="<?php echo $currentPage === 'users' ? 'active' : ''; ?>">
                    <span class="menu-icon">👥</span> 管理员
                </a>
                <a href="<?php echo siteUrl(); ?>" target="_blank">
                    <span class="menu-icon">🌐</span> 查看站点
                </a>
                <a href="<?php echo SITE_URL; ?>/admin/pages/logout.php" class="menu-link-logout">
                    <span class="menu-icon">🚪</span> 退出登录
                </a>
            </nav>
        </aside>

        <div class="admin-sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button class="admin-topbar-menu-toggle" id="menuToggle" aria-label="菜单">☰</button>
                    <h1><?php echo h($pageTitle ?? '管理后台'); ?></h1>
                </div>
                <div class="admin-topbar-right">
                    <div class="admin-user-info">
                        <div class="admin-avatar"><?php echo h(mb_substr($adminUser['username'] ?? 'A', 0, 1)); ?></div>
                        <div class="admin-user-details">
                            <span class="admin-user-name"><?php echo h($adminUser['username'] ?? 'admin'); ?></span>
                            <span class="admin-user-role"><?php echo h($adminUser['role'] ?? 'superadmin'); ?></span>
                        </div>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/logout.php" class="admin-topbar-btn danger" title="退出登录">⏻</a>
                </div>
            </header>
            <main class="admin-content">