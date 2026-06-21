<?php
/**
 * 通用头部组件
 */
require_once dirname(__FILE__, 2) . '/config.php';
$currentPage = $currentPage ?? 'home';
$siteName = $site_data['site']['name'] ?? '语云科技';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="语云科技 - 全球领先的云服务提供商，为您提供安全、稳定、高效的云计算解决方案。">
<meta name="keywords" content="云服务器,云计算,CDN,云数据库,SSL证书,语云科技">
<meta name="author" content="语云科技">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?><?php echo htmlspecialchars($siteName); ?></title>
<link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- 顶部通知条 -->
<div class="top-bar">
    <div class="container">
        <div class="top-bar-content">
            <div class="top-bar-left">
                <a href="contact.php"><i class="fas fa-phone-alt"></i> 销售热线 400-800-8541</a>
                <a href="contact.php"><i class="fas fa-envelope"></i> sales@yuyun-tech.com</a>
            </div>
            <div class="top-bar-right">
                <a href="admin/">后台管理</a>
                <a href="https://cloud.loveym.cloud" target="_blank"><i class="fas fa-globe"></i> 国际版</a>
                <a href="#" onclick="toggleTheme();return false;"><i class="fas fa-moon"></i> 深色模式</a>
            </div>
        </div>
    </div>
</div>

<!-- 头部导航 -->
<header class="header">
    <div class="container">
        <div class="header-inner">
            <a href="index.php" class="logo">
                <span class="logo-icon">Y</span>
                <div class="logo-text">
                    <?php echo htmlspecialchars($siteName); ?>
                    <small>YUYUN TECH</small>
                </div>
            </a>

            <nav class="main-nav">
                <a href="index.php" class="<?php echo $currentPage==='home'?'active':''; ?>"><i class="fas fa-home"></i> 首页</a>
                <a href="products.php" class="<?php echo $currentPage==='products'?'active':''; ?>"><i class="fas fa-cube"></i> 产品介绍</a>
                <a href="about.php" class="<?php echo $currentPage==='about'?'active':''; ?>"><i class="fas fa-building"></i> 关于我们</a>
                <a href="company.php" class="<?php echo $currentPage==='company'?'active':''; ?>"><i class="fas fa-info-circle"></i> 公司简介</a>
                <a href="partners.php" class="<?php echo $currentPage==='partners'?'active':''; ?>"><i class="fas fa-handshake"></i> 合作伙伴</a>
                <a href="contact.php" class="<?php echo $currentPage==='contact'?'active':''; ?>"><i class="fas fa-envelope"></i> 联系我们</a>
                <a href="https://cloud.loveym.cloud" target="_blank"><i class="fas fa-globe-asia"></i> 国际版</a>
            </nav>

            <div class="nav-actions">
                <a href="contact.php" class="btn btn-primary btn-sm">免费咨询</a>
                <button class="hamburger" aria-label="菜单">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- 移动端菜单 -->
<div class="menu-overlay"></div>
<aside class="mobile-menu">
    <button class="mobile-menu-close" aria-label="关闭">&times;</button>
    <div class="mobile-menu-title">
        <span class="logo-icon" style="display:inline-flex;width:36px;height:36px;font-size:16px;vertical-align:middle;margin-right:8px;">Y</span>
        语云科技
    </div>
    <nav>
        <a href="index.php" class="<?php echo $currentPage==='home'?'active':''; ?>"><i class="fas fa-home"></i> 首页</a>
        <a href="products.php" class="<?php echo $currentPage==='products'?'active':''; ?>"><i class="fas fa-cube"></i> 产品介绍</a>
        <a href="about.php" class="<?php echo $currentPage==='about'?'active':''; ?>"><i class="fas fa-building"></i> 关于我们</a>
        <a href="company.php" class="<?php echo $currentPage==='company'?'active':''; ?>"><i class="fas fa-info-circle"></i> 公司简介</a>
        <a href="partners.php" class="<?php echo $currentPage==='partners'?'active':''; ?>"><i class="fas fa-handshake"></i> 合作伙伴</a>
        <a href="contact.php" class="<?php echo $currentPage==='contact'?'active':''; ?>"><i class="fas fa-envelope"></i> 联系我们</a>
        <a href="https://cloud.loveym.cloud" target="_blank"><i class="fas fa-globe-asia"></i> 国际版官网</a>
        <a href="admin/"><i class="fas fa-cog"></i> 后台管理</a>
    </nav>
</aside>
