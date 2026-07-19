<?php
/**
 * 后台管理头部模板
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
    <link rel="stylesheet" href="<?php echo site_url('assets/css/admin.css?v=' . CMS_BUILD); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-shield-haltered"></i>
            <span>后台管理</span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li class="<?php echo $admin_page == 'index' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url(); ?>"><i class="fas fa-tachometer-alt"></i> 仪表盘</a>
                </li>
                <li class="<?php echo $admin_page == 'articles' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('articles.php'); ?>"><i class="fas fa-newspaper"></i> 文章管理</a>
                </li>
                <li class="<?php echo $admin_page == 'categories' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('categories.php'); ?>"><i class="fas fa-folder"></i> 分类管理</a>
                </li>
                <li class="<?php echo $admin_page == 'carousel' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('carousel.php'); ?>"><i class="fas fa-images"></i> 轮播图管理</a>
                </li>
                <li class="<?php echo $admin_page == 'popups' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('popups.php'); ?>"><i class="fas fa-window-maximize"></i> 弹窗管理</a>
                </li>
                <li class="<?php echo $admin_page == 'nav' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('nav.php'); ?>"><i class="fas fa-bars"></i> 导航管理</a>
                </li>
                <li class="<?php echo $admin_page == 'users' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('users.php'); ?>"><i class="fas fa-users"></i> 用户管理</a>
                </li>
                <li class="<?php echo $admin_page == 'messages' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('messages.php'); ?>"><i class="fas fa-envelope"></i> 留言管理</a>
                </li>
                <li class="<?php echo $admin_page == 'reports' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('reports.php'); ?>"><i class="fas fa-flag"></i> 举报管理</a>
                </li>
                <li class="<?php echo $admin_page == 'settings' ? 'active' : ''; ?>">
                    <a href="<?php echo admin_url('settings.php'); ?>"><i class="fas fa-cog"></i> 系统设置</a>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="<?php echo site_url(); ?>" target="_blank"><i class="fas fa-external-link-alt"></i> 访问网站</a>
            <a href="<?php echo site_url('logout.php'); ?>"><i class="fas fa-sign-out-alt"></i> 退出</a>
        </div>
    </aside>
    
    <div class="admin-main">
        <header class="admin-header">
            <div class="admin-header-left">
                <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <span class="admin-greeting">欢迎，<?php echo htmlspecialchars(current_username()); ?></span>
            </div>
            <div class="admin-header-right">
                <span class="admin-time"><?php echo date('Y-m-d H:i'); ?></span>
            </div>
        </header>
        
        <div class="admin-content"><?php