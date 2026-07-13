<?php
$siteName = getSetting('site_name', SITE_NAME);
$siteTitle = getSetting('site_title', SITE_TITLE);
$navCategories = DB::fetchAll("SELECT * FROM categories WHERE parent_id=0 AND show_in_menu=1 ORDER BY sort_order ASC, id ASC");
$currentUrl = $_SERVER['REQUEST_URI'];

if (!function_exists('resolve_nav_icon')) {
    function resolve_nav_icon($cat) {
        $slug = isset($cat['slug']) ? $cat['slug'] : '';
        $name = isset($cat['name']) ? $cat['name'] : '';
        $icons = array(
            'news'      => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h13a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H6a2 2 0 0 1-2-2V4z"/><path d="M18 8h2v11a2 2 0 0 1-2 2"/><line x1="8" y1="9" x2="14" y2="9"/><line x1="8" y1="13" x2="14" y2="13"/><line x1="8" y1="17" x2="12" y2="17"/></svg>',
            'scale'     => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="3" x2="12" y2="21"/><line x1="7" y1="21" x2="17" y2="21"/><line x1="5" y1="7" x2="19" y2="7"/><path d="M5 7L2 13a3 3 0 0 0 6 0L5 7z"/><path d="M19 7l-3 6a3 3 0 0 0 6 0l-3-6z"/></svg>',
            'search'    => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="11"/><line x1="11" y1="11" x2="13" y2="11"/></svg>',
            'book'      => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="9" y1="12" x2="15" y2="12"/></svg>',
            'megaphone' => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10v4l11 5V5L3 10z"/><path d="M14 8a5 5 0 0 1 0 8"/><line x1="6" y1="13" x2="6" y2="17"/></svg>',
            'video'     => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="6" width="14" height="12" rx="1"/><path d="M16 10l6-3v10l-6-3z"/></svg>',
            'palette'   => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2a10 10 0 0 0 0 20c1.1 0 2-.9 2-2 0-.5-.2-1-.5-1.3-.3-.4-.5-.8-.5-1.2 0-1 .9-1.5 2-1.5h2.5A4 4 0 0 0 22 12 10 10 0 0 0 12 2z"/><circle cx="7.5" cy="10.5" r="1"/><circle cx="12" cy="7.5" r="1"/><circle cx="16.5" cy="10.5" r="1"/></svg>',
            'default'   => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"/></svg>',
        );
        $map = array(
            'yaowen'  => 'news',
            'shencha' => 'scale',
            'xunshi'  => 'search',
            'fagui'   => 'book',
            'shipin'  => 'video',
            'wenhua'  => 'palette',
            'jubao'   => 'megaphone',
        );
        $key = isset($map[$slug]) ? $map[$slug] : '';
        if (!$key) {
            if (mb_strpos($name, '要闻') !== false || mb_strpos($name, '新闻') !== false) $key = 'news';
            elseif (mb_strpos($name, '审查') !== false) $key = 'scale';
            elseif (mb_strpos($name, '巡视') !== false) $key = 'search';
            elseif (mb_strpos($name, '法规') !== false || mb_strpos($name, '党纪') !== false) $key = 'book';
            elseif (mb_strpos($name, '视频') !== false) $key = 'video';
            elseif (mb_strpos($name, '文化') !== false) $key = 'palette';
            elseif (mb_strpos($name, '举报') !== false || mb_strpos($name, '监督') !== false) $key = 'megaphone';
            else $key = 'default';
        }
        return isset($icons[$key]) ? $icons[$key] : $icons['default'];
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?><?php echo e($siteTitle); ?></title>
    <meta name="keywords" content="<?php echo e(getSetting('site_keywords', SITE_KEYWORDS)); ?>">
    <meta name="description" content="<?php echo e(getSetting('site_description', SITE_DESCRIPTION)); ?>">
    <link rel="stylesheet" href="//at.alicdn.com/t/font_3171436_xc6n6a4nd8r.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        :root{
            --gov-red:#9a0006;
            --gov-red-mid:#7a0000;
            --gov-red-dark:#4a0000;
            --gov-red-deep:#2e0000;
            --gov-gold:#d4af37;
            --gov-gold-light:#f0d878;
            --gov-gold-deep:#b8941f;
        }
        .top-bar{
            background:linear-gradient(90deg,var(--gov-red-deep) 0%,var(--gov-red-dark) 100%);
            color:var(--gov-gold-light);
            border-bottom:1px solid rgba(212,175,55,.35);
        }
        .top-bar a{color:var(--gov-gold-light);}
        .top-bar a:hover{color:#fff;}
        .top-left span::before{
            content:"";
            display:inline-block;
            width:14px;height:14px;
            margin-right:6px;
            vertical-align:-2px;
            background:linear-gradient(135deg,var(--gov-gold-light),var(--gov-gold-deep));
            -webkit-mask:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path d='M12 2l2.4 6.9H22l-6 4.4 2.3 7.1L12 16.2 5.7 20.4 8 13.3 2 8.9h7.6z'/></svg>") center/contain no-repeat;
                    mask:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><path d='M12 2l2.4 6.9H22l-6 4.4 2.3 7.1L12 16.2 5.7 20.4 8 13.3 2 8.9h7.6z'/></svg>") center/contain no-repeat;
        }
        .site-header{
            background:
                radial-gradient(circle at 90% -20%,rgba(212,175,55,.18),transparent 55%),
                linear-gradient(135deg,var(--gov-red) 0%,var(--gov-red-dark) 55%,var(--gov-red-deep) 100%);
            border-bottom:3px solid var(--gov-gold);
            box-shadow:0 4px 14px rgba(46,0,0,.28);
            position:relative;
        }
        .site-header::after{
            content:"";
            position:absolute;left:0;right:0;bottom:-3px;height:3px;
            background:repeating-linear-gradient(90deg,var(--gov-gold) 0 18px,transparent 18px 30px);
            opacity:.5;
        }
        .site-logo{align-items:center;}
        .site-logo .logo-icon{
            width:56px;height:56px;
            display:flex;align-items:center;justify-content:center;
            font-size:30px;font-weight:700;
            color:var(--gov-red-dark);
            background:linear-gradient(135deg,var(--gov-gold-light),var(--gov-gold-deep));
            border:2px solid var(--gov-gold-light);
            border-radius:6px;
            box-shadow:0 0 14px rgba(212,175,55,.55),inset 0 0 8px rgba(255,255,255,.4);
            text-shadow:0 1px 0 rgba(255,255,255,.5);
        }
        .site-logo .logo-text h1{
            color:#fff;
            font-size:26px;letter-spacing:2px;
            text-shadow:0 2px 4px rgba(0,0,0,.45);
        }
        .site-logo .logo-text p{
            color:var(--gov-gold-light);
            font-size:13px;letter-spacing:4px;
            margin-top:2px;
        }
        .header-right .search-box{
            background:rgba(0,0,0,.22);
            border:1px solid rgba(212,175,55,.5);
            border-radius:4px;
            overflow:hidden;
        }
        .header-right .search-box input{
            background:transparent;
            color:#fff;border:none;outline:none;
            padding:8px 12px;width:200px;
        }
        .header-right .search-box input::placeholder{color:rgba(240,216,120,.7);}
        .header-right .search-box button{
            background:linear-gradient(135deg,var(--gov-gold-light),var(--gov-gold-deep));
            color:var(--gov-red-deep);
            font-weight:700;border:none;
            padding:8px 16px;cursor:pointer;
        }
        .main-nav{
            background:linear-gradient(180deg,#a8000a 0%,#7a0000 100%);
            border-top:1px solid rgba(212,175,55,.45);
            border-bottom:2px solid var(--gov-gold);
            box-shadow:0 3px 10px rgba(122,0,0,.35);
        }
        .main-nav > .container > ul{display:flex;flex-wrap:wrap;}
        .main-nav > .container > ul > li{position:relative;}
        .main-nav > .container > ul > li > a{
            color:#fff;
            display:flex;align-items:center;gap:6px;
            padding:14px 18px;
            font-size:16px;font-weight:500;
            border-right:1px solid rgba(212,175,55,.18);
            transition:background .2s,color .2s;
        }
        .main-nav > .container > ul > li > a:hover{
            background:rgba(212,175,55,.16);
            color:var(--gov-gold-light);
        }
        .main-nav > .container > ul > li.active > a{
            background:var(--gov-gold);
            color:var(--gov-red-deep);
            font-weight:700;
        }
        .nav-icon{
            width:18px;height:18px;flex-shrink:0;
            display:inline-block;vertical-align:middle;
        }
        .main-nav .submenu{
            background:#fff;
            border:1px solid rgba(212,175,55,.4);
            border-top:3px solid var(--gov-gold);
            box-shadow:0 6px 16px rgba(0,0,0,.18);
            min-width:180px;
        }
        .main-nav .submenu a{
            color:var(--gov-red-dark);
            padding:10px 16px;
            border-bottom:1px dashed rgba(212,175,55,.4);
            display:flex;align-items:center;gap:6px;
        }
        .main-nav .submenu a:hover{
            background:var(--gov-gold-light);
            color:var(--gov-red-deep);
        }
        .mobile-toggle{
            background:var(--gov-gold);
            color:var(--gov-red-deep);
            border:none;font-size:22px;
            padding:6px 12px;border-radius:4px;cursor:pointer;
        }
        .mobile-menu{background:#fff;}
        .mobile-menu-header{
            background:linear-gradient(135deg,var(--gov-red),var(--gov-red-dark));
            color:var(--gov-gold-light);
            border-bottom:2px solid var(--gov-gold);
        }
        .mobile-menu-header h3{color:#fff;letter-spacing:2px;}
        .mobile-nav ul li a{
            color:var(--gov-red-dark);
            display:flex;align-items:center;gap:8px;
            border-bottom:1px solid rgba(212,175,55,.3);
        }
        .mobile-nav ul li a:hover{background:var(--gov-gold-light);color:var(--gov-red-deep);}
        .mobile-nav .nav-icon{color:var(--gov-red);}
        @media(max-width:768px){
            .header-right .search-box input{width:140px;}
            .site-logo .logo-text h1{font-size:20px;}
        }
    </style>
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
                    <a href="<?php echo BASE_URL; ?>index.php">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5"/><path d="M9 21v-7h6v7"/></svg>
                        首页
                    </a>
                </li>
                <?php foreach ($navCategories as $cat): ?>
                    <?php $subCats = getChildCategories($cat['id']); ?>
                    <li class="<?php echo isset($currentCatId) && ($currentCatId == $cat['id'] || ($cat['parent_id'] && $currentCatId == $cat['parent_id'])) ? 'active' : ''; ?>">
                        <a href="<?php echo $cat['type'] == 'page' ? BASE_URL . 'report.php' : BASE_URL . 'category.php?slug=' . $cat['slug']; ?>">
                            <?php echo resolve_nav_icon($cat); ?>
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
                <li><a href="<?php echo BASE_URL; ?>index.php">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5"/><path d="M9 21v-7h6v7"/></svg>
                    首页
                </a></li>
                <?php foreach ($navCategories as $cat): ?>
                    <?php $subCats = getChildCategories($cat['id']); ?>
                    <li class="<?php echo $subCats ? 'has-submenu' : ''; ?>">
                        <a href="<?php echo $cat['type'] == 'page' ? BASE_URL . 'report.php' : BASE_URL . 'category.php?slug=' . $cat['slug']; ?>">
                            <?php echo resolve_nav_icon($cat); ?>
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
                <li><a href="<?php echo BASE_URL; ?>report.php">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10v4l11 5V5L3 10z"/><path d="M14 8a5 5 0 0 1 0 8"/><line x1="6" y1="13" x2="6" y2="17"/></svg>
                    监督举报
                </a></li>
            </ul>
        </nav>
    </div>

    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
