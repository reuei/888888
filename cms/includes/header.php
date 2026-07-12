<?php
$siteName = getSetting('site_name', SITE_NAME);
$siteTitle = getSetting('site_title', SITE_TITLE);
$navCategories = DB::fetchAll("SELECT * FROM categories WHERE parent_id=0 AND show_in_menu=1 ORDER BY sort_order ASC, id ASC");
$currentUrl = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?><?php echo e($siteTitle); ?></title>
    <meta name="keywords" content="<?php echo e(getSetting('site_keywords', SITE_KEYWORDS)); ?>">
    <meta name="description" content="<?php echo e(getSetting('site_description', SITE_DESCRIPTION)); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <div class="top-bar">
        <div class="container">
            <div class="top-left">
                <span>欢迎访问 <?php echo e($siteName); ?></span>
            </div>
            <div class="top-right">
                <?php if (isLoggedIn()): ?>
                    <span>您好，<?php echo e(currentUser()['nickname'] ?: currentUser()['username']); ?></span>
                    <a href="<?php echo BASE_URL; ?>user.php">用户中心</a>
                    <a href="<?php echo BASE_URL; ?>logout.php">退出</a>
                    <?php if (isAdmin()): ?>
                        <a href="<?php echo BASE_URL; ?>admin/index.php">管理后台</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login.php">登录</a>
                    <a href="<?php echo BASE_URL; ?>register.php">注册</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <header class="site-header">
        <div class="container">
            <div class="header-main">
                <div class="site-logo">
                    <div class="logo-icon">廉</div>
                    <div class="logo-text">
                        <h1><?php echo e($siteName); ?></h1>
                        <p>党风廉政建设门户网站</p>
                    </div>
                </div>
                <div class="header-right">
                    <form class="search-box" action="<?php echo BASE_URL; ?>search.php" method="get">
                        <input type="text" name="q" placeholder="请输入关键词搜索" value="<?php echo e($_GET['q'] ?? ''); ?>">
                        <button type="submit">搜索</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <nav class="main-nav">
        <div class="container">
            <ul>
                <li class="<?php echo ($_SERVER['PHP_SELF'] == '/index.php' || basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>index.php">首页</a>
                </li>
                <?php foreach ($navCategories as $cat): ?>
                    <?php $subCats = getChildCategories($cat['id']); ?>
                    <li class="<?php echo isset($currentCatId) && ($currentCatId == $cat['id'] || ($cat['parent_id'] && $currentCatId == $cat['parent_id'])) ? 'active' : ''; ?>">
                        <a href="<?php echo $cat['type'] == 'page' ? BASE_URL . 'report.php' : BASE_URL . 'category.php?slug=' . $cat['slug']; ?>">
                            <?php echo e($cat['name']); ?>
                        </a>
                        <?php if ($subCats): ?>
                            <ul class="submenu">
                                <?php foreach ($subCats as $sub): ?>
                                    <li><a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($sub['slug']); ?>"><?php echo e($sub['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button class="mobile-toggle" id="mobileToggle">☰</button>
        </div>
    </nav>

    <div class="mobile-overlay" id="mobileOverlay"></div>
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <h3>导航菜单</h3>
            <button class="mobile-menu-close" id="mobileClose">✕</button>
        </div>
        <div class="mobile-search">
            <form action="<?php echo BASE_URL; ?>search.php" method="get">
                <input type="text" name="q" placeholder="搜索...">
            </form>
        </div>
        <nav class="mobile-nav">
            <ul>
                <li><a href="<?php echo BASE_URL; ?>index.php">首页</a></li>
                <?php foreach ($navCategories as $cat): ?>
                    <?php $subCats = getChildCategories($cat['id']); ?>
                    <li class="<?php echo $subCats ? 'has-submenu' : ''; ?>">
                        <a href="<?php echo $cat['type'] == 'page' ? BASE_URL . 'report.php' : BASE_URL . 'category.php?slug=' . $cat['slug']; ?>">
                            <?php echo e($cat['name']); ?>
                            <?php if ($subCats): ?><span class="menu-toggle">▾</span><?php endif; ?>
                        </a>
                        <?php if ($subCats): ?>
                            <ul class="submenu" style="display:none;">
                                <?php foreach ($subCats as $sub): ?>
                                    <li><a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($sub['slug']); ?>"><?php echo e($sub['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                <li><a href="<?php echo BASE_URL; ?>report.php">监督举报</a></li>
            </ul>
        </nav>
    </div>

    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
