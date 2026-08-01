<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h(DB::getSettingValue('site_title', SITE_TITLE)); ?></title>
    <meta name="description" content="<?php echo h(DB::getSettingValue('site_description', SITE_DESCRIPTION)); ?>">
    <meta name="keywords" content="<?php echo h(DB::getSettingValue('site_keywords', SITE_KEYWORDS)); ?>">
    <link rel="icon" href="<?php echo h(DB::getSettingValue('favicon', '/assets/images/favicon.ico')); ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
</head>
<body class="page-<?php echo $currentPage; ?>">

<!-- Top Navigation -->
<header class="site-header" id="siteHeader">
    <div class="header-inner">
        <div class="container">
            <div class="header-row">
                <!-- Logo -->
                <a href="<?php echo siteUrl(); ?>" class="logo">
                    <img src="<?php echo h(DB::getSettingValue('logo', '/assets/images/logo.png')); ?>" alt="<?php echo h(SITE_NAME); ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <span class="logo-text" style="display:none;"><?php echo h(SITE_NAME); ?></span>
                </a>
                
                <!-- Main Navigation -->
                <nav class="main-nav" id="mainNav">
                    <ul>
                        <li><a href="<?php echo siteUrl(); ?>" class="<?php echo activeNav('home'); ?>">首页</a></li>
                        <li class="has-dropdown">
                            <a href="<?php echo siteUrl('about.php'); ?>" class="<?php echo activeNav('about'); ?>">关于我们</a>
                            <ul class="dropdown">
                                <li><a href="<?php echo siteUrl('about.php'); ?>">公司介绍</a></li>
                                <li><a href="<?php echo siteUrl('about.php#team'); ?>">团队介绍</a></li>
                                <li><a href="<?php echo siteUrl('about.php#culture'); ?>">企业文化</a></li>
                            </ul>
                        </li>
                        <li class="has-dropdown">
                            <a href="<?php echo siteUrl('service.php'); ?>" class="<?php echo activeNav('service'); ?>">服务项目</a>
                            <ul class="dropdown">
                                <?php
                                $services = DB::getList('services', ['status' => 1], 'sort', 'ASC');
                                foreach ($services as $s):
                                ?>
                                <li><a href="<?php echo siteUrl('service.php'); ?>"><?php echo h($s['title']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="has-dropdown">
                            <a href="<?php echo siteUrl('case.php'); ?>" class="<?php echo activeNav('case'); ?>">客户案例</a>
                            <ul class="dropdown">
                                <li><a href="<?php echo siteUrl('case.php'); ?>">全部案例</a></li>
                                <li><a href="<?php echo siteUrl('case.php?cat=网站建设'); ?>">网站建设</a></li>
                                <li><a href="<?php echo siteUrl('case.php?cat=SEO优化'); ?>">SEO优化</a></li>
                                <li><a href="<?php echo siteUrl('case.php?cat=外贸营销'); ?>">外贸营销</a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo siteUrl('news.php'); ?>" class="<?php echo activeNav('news'); ?>">新闻资讯</a></li>
                        <li><a href="<?php echo siteUrl('contact.php'); ?>" class="<?php echo activeNav('contact'); ?>">联系我们</a></li>
                    </ul>
                </nav>
                
                <!-- Header Actions -->
                <div class="header-actions">
                    <a href="<?php echo siteUrl('contact.php'); ?>" class="btn btn-primary btn-sm">免费咨询</a>
                    <button class="hamburger" id="hamburger" aria-label="菜单">
                        <span></span><span></span><span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-inner">
        <nav>
            <ul>
                <li><a href="<?php echo siteUrl(); ?>">首页</a></li>
                <li><a href="<?php echo siteUrl('about.php'); ?>">关于我们</a></li>
                <li><a href="<?php echo siteUrl('service.php'); ?>">服务项目</a></li>
                <li><a href="<?php echo siteUrl('case.php'); ?>">客户案例</a></li>
                <li><a href="<?php echo siteUrl('news.php'); ?>">新闻资讯</a></li>
                <li><a href="<?php echo siteUrl('contact.php'); ?>">联系我们</a></li>
            </ul>
        </nav>
        <div class="mobile-menu-footer">
            <p>电话: <?php echo h(DB::getSettingValue('contact_phone', '')); ?></p>
            <p>邮箱: <?php echo h(DB::getSettingValue('contact_email', '')); ?></p>
        </div>
    </div>
</div>
<div class="mobile-menu-backdrop" id="mobileMenuBackdrop"></div>

<main class="site-main">
