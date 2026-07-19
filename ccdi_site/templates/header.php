<?php
/**
 * 网站头部模板 v3.0.0
 */
if (!defined('SYSTEM_INIT')) { require_once __DIR__ . '/../includes/init.php'; }

$nav_menu = db_fetch_all("SELECT * FROM nav_menu WHERE parent_id = 0 AND status = 1 ORDER BY sort_order ASC");
$show_popup = site_config('popup_enabled', '0') === '1';
$popup = $show_popup ? get_popup() : null;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $site_description; ?>">
    <meta name="keywords" content="<?php echo $site_keywords; ?>">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo site_url('assets/css/style.css?v=3.0.0'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://at.alicdn.com/t/c/font_4346459_iconfont.css">
</head>
<body>

<!-- 加载动画：红色圆柱跳动 -->
<div class="page-loader" id="pageLoader">
    <div class="loader-cylinders">
        <div class="cyl"></div>
        <div class="cyl"></div>
        <div class="cyl"></div>
        <div class="cyl"></div>
        <div class="cyl"></div>
    </div>
</div>

<!-- Toast 全局提示容器 -->
<div class="toast-container" id="toastContainer"></div>

<!-- 顶部工具栏 -->
<div class="top-bar">
    <div class="container">
        <div class="top-bar-left">
            <span><?php echo date('Y年m月d日'); ?> 星期<?php echo ['日','一','二','三','四','五','六'][date('w')]; ?></span>
        </div>
        <div class="top-bar-right">
            <?php if (is_logged_in()): ?>
                <span><?php echo htmlspecialchars(current_username()); ?></span>
                <?php if (is_admin()): ?><a href="<?php echo admin_url(); ?>">后台</a><?php endif; ?>
                <a href="<?php echo site_url('logout.php'); ?>">退出</a>
            <?php else: ?>
                <a href="<?php echo site_url('login.php'); ?>">登录</a>
                <a href="<?php echo site_url('register.php'); ?>">注册</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 头部 -->
<header class="site-header">
    <div class="container">
        <div class="header-main">
            <div class="logo-section">
                <div class="logo-icon"><i class="fas fa-landmark"></i></div>
                <div class="logo-text">
                    <h1 class="site-title"><?php echo htmlspecialchars(site_config('site_name', SITE_NAME)); ?></h1>
                    <p class="site-subtitle">中共中央纪律检查委员会 中华人民共和国国家监察委员会</p>
                </div>
            </div>
            <div class="header-search">
                <form action="<?php echo site_url('search.php'); ?>" method="get" class="search-form">
                    <input type="text" name="q" placeholder="站内搜索" value="<?php echo htmlspecialchars(get('q')); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</header>

<!-- 主导航 -->
<nav class="main-nav" id="mainNav">
    <div class="container">
        <button class="hamburger" id="hamburgerBtn" aria-label="菜单">
            <span></span><span></span><span></span>
        </button>
        <ul class="nav-list" id="navList">
            <li><a href="<?php echo site_url(); ?>" class="<?php echo $current_page == 'index' ? 'active' : ''; ?>">首页</a></li>
            <?php foreach ($nav_menu as $item): ?>
            <li><a href="<?php echo !empty($item['url']) ? (strpos($item['url'], 'http') === 0 ? $item['url'] : site_url($item['url'])) : site_url('category.php?slug=' . $item['slug']); ?>"><?php echo htmlspecialchars($item['name']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<!-- 移动端遮罩 -->
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- 移动端侧边导航 -->
<div class="mobile-sidebar" id="mobileSidebar">
    <div class="mobile-sidebar-header">
        <span>导航菜单</span>
        <button class="mobile-sidebar-close" id="mobileSidebarClose">&times;</button>
    </div>
    <ul class="mobile-nav-list">
        <li><a href="<?php echo site_url(); ?>">首页</a></li>
        <?php foreach ($nav_menu as $item): ?>
        <li><a href="<?php echo !empty($item['url']) ? (strpos($item['url'], 'http') === 0 ? $item['url'] : site_url($item['url'])) : site_url('category.php?slug=' . $item['slug']); ?>"><?php echo htmlspecialchars($item['name']); ?></a></li>
        <?php endforeach; ?>
        <li class="nav-divider"></li>
        <?php if (is_logged_in()): ?>
            <li><a href="<?php echo admin_url(); ?>">后台管理</a></li>
            <li><a href="<?php echo site_url('logout.php'); ?>">退出登录</a></li>
        <?php else: ?>
            <li><a href="<?php echo site_url('login.php'); ?>">登录</a></li>
            <li><a href="<?php echo site_url('register.php'); ?>">注册</a></li>
        <?php endif; ?>
    </ul>
</div>

<!-- B2弹窗 -->
<?php if ($popup): ?>
<div class="popup-overlay" id="popupOverlay">
    <div class="popup-box">
        <button class="popup-close" id="popupClose">&times;</button>
        <?php if ($popup['title']): ?><h3 class="popup-title"><?php echo htmlspecialchars($popup['title']); ?></h3><?php endif; ?>
        <?php if ($popup['image']): ?><img src="<?php echo site_url('uploads/' . $popup['image']); ?>" alt="" class="popup-image"><?php endif; ?>
        <div class="popup-content"><?php echo nl2br(htmlspecialchars($popup['content'])); ?></div>
        <?php if ($popup['link']): ?><a href="<?php echo htmlspecialchars($popup['link']); ?>" class="popup-link">查看详情</a><?php endif; ?>
    </div>
</div>
<?php endif; ?>

<main class="site-main">
    <div class="container"><?php