<?php
$siteName = getSetting('site_name', SITE_NAME);
$siteTitle = getSetting('site_title', SITE_TITLE);
$navCategories = DB::fetchAll("SELECT * FROM categories WHERE parent_id=0 AND show_in_menu=1 ORDER BY sort_order ASC, id ASC");

// 品牌Logo SVG（检察机关徽章风格 - 天秤+麦穗+盾形）
$brandLogo = '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
        <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#e8c97c"/>
            <stop offset="100%" stop-color="#a68419"/>
        </linearGradient>
    </defs>
    <path d="M50 12 L58 24 L72 22 L70 36 L82 42 L72 50 L74 64 L60 62 L50 74 L40 62 L26 64 L28 50 L18 42 L30 36 L28 22 L42 24 Z" fill="url(#goldGrad)"/>
    <line x1="50" y1="28" x2="50" y2="38" stroke="#0a2540" stroke-width="1.5"/>
    <line x1="50" y1="38" x2="36" y2="46" stroke="#0a2540" stroke-width="1.5"/>
    <line x1="50" y1="38" x2="64" y2="46" stroke="#0a2540" stroke-width="1.5"/>
    <circle cx="36" cy="46" r="4" fill="#0a2540"/>
    <circle cx="64" cy="46" r="4" fill="#0a2540"/>
    <text x="50" y="62" text-anchor="middle" fill="#0a2540" font-size="11" font-weight="bold" font-family="serif">检察</text>
</svg>';

// 导航栏图标（线性简约）
$navIcons = [
    'home'   => '<svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12L12 3l9 9"/><path d="M5 10v10h14V10"/></svg>',
    'yaowen' => '<svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="1"/><line x1="7" y1="9" x2="17" y2="9"/><line x1="7" y1="13" x2="17" y2="13"/><line x1="7" y1="17" x2="13" y2="17"/></svg>',
    'shencha'=> '<svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="16" y1="16" x2="21" y2="21"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>',
    'xunshi' => '<svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v4"/><path d="M12 18v4"/><path d="M4.93 4.93l2.83 2.83"/><path d="M16.24 16.24l2.83 2.83"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="M4.93 19.07l2.83-2.83"/><path d="M16.24 7.76l2.83-2.83"/></svg>',
    'fagui'  => '<svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/><path d="M9 12l2 2 4-4"/></svg>',
    'jubao'  => '<svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z"/></svg>',
    'video'  => '<svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="5 3 19 12 5 21 5 3"/></svg>',
    'wenhua' => '<svg class="nav-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?><?php echo e($siteTitle); ?></title>
    <meta name="keywords" content="<?php echo e(getSetting('site_keywords', SITE_KEYWORDS)); ?>">
    <meta name="description" content="<?php echo e(getSetting('site_description', SITE_DESCRIPTION)); ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/iconfont.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <div class="topbar">
        <div class="container">
            <div class="topbar-date"><?php echo date('Y年m月d日'); ?> <?php echo ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'][date('w')]; ?></div>
            <div>
                <?php if (isLoggedIn()): ?>
                    <span>您好，<?php echo e(currentUser()['nickname'] ?: currentUser()['username']); ?></span>
                    <a href="<?php echo BASE_URL; ?>user.php">个人中心</a>
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

    <header class="masthead">
        <div class="container">
            <div class="brand">
                <div class="brand-mark"><?php echo $brandLogo; ?></div>
                <div class="brand-text">
                    <h1><?php echo e($siteName); ?></h1>
                    <div class="tagline">PEOPLE'S PROCURATORATE</div>
                </div>
            </div>
            <form class="search-mini" action="<?php echo BASE_URL; ?>search.php" method="get">
                <input type="text" name="q" placeholder="检索本站信息..." value="<?php echo e($_GET['q'] ?? ''); ?>">
                <button type="submit" aria-label="搜索">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="16" y1="16" x2="21" y2="21"/></svg>
                </button>
            </form>
        </div>
    </header>

    <nav class="mainnav">
        <div class="container">
            <ul class="mainnav-list">
                <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <a href="<?php echo BASE_URL; ?>index.php"><?php echo $navIcons['home']; ?>首页</a>
                </li>
                <?php foreach ($navCategories as $cat): ?>
                    <?php $subCats = getChildCategories($cat['id']); ?>
                    <li class="<?php echo isset($currentCatId) && $currentCatId == $cat['id'] ? 'active' : ''; ?>">
                        <a href="<?php echo $cat['type'] == 'page' ? BASE_URL . 'report.php' : BASE_URL . 'category.php?slug=' . $cat['slug']; ?>">
                            <?php echo isset($navIcons[$cat['slug']]) ? $navIcons[$cat['slug']] : $navIcons['yaowen']; ?>
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
            <button class="nav-trigger" id="navTrigger">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                导航菜单
            </button>
        </div>
    </nav>

    <div class="mobile-overlay" id="mobileOverlay"></div>
    <aside class="mobile-drawer" id="mobileDrawer">
        <div class="mobile-drawer-head">
            <h3>导航目录</h3>
            <button class="mobile-drawer-close" id="drawerClose" aria-label="关闭">×</button>
        </div>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>index.php"><?php echo $navIcons['home']; ?>首&nbsp;&nbsp;页</a></li>
            <?php foreach ($navCategories as $cat): ?>
            <li><a href="<?php echo $cat['type'] == 'page' ? BASE_URL . 'report.php' : BASE_URL . 'category.php?slug=' . $cat['slug']; ?>">
                <?php echo isset($navIcons[$cat['slug']]) ? $navIcons[$cat['slug']] : $navIcons['yaowen']; ?>
                <?php echo e($cat['name']); ?>
            </a></li>
            <?php endforeach; ?>
            <li><a href="<?php echo BASE_URL; ?>report.php"><?php echo $navIcons['jubao']; ?>信访举报</a></li>
        </ul>
    </aside>

    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>