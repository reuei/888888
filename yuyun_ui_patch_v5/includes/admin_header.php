<?php
require __DIR__ . '/config.php';
require_admin();
$pageTitle = ($pageTitle ?? __('admin_dashboard')) . ' - ' . __('admin_title');
$adminMenu = [
    ['index.php','icon-gauge','admin_dashboard'],
    ['settings.php','icon-sliders','admin_settings'],
    ['slides.php','icon-images','admin_slides'],
    ['products.php','icon-cubes','admin_products'],
    ['partners.php','icon-handshake','admin_partners'],
    ['staff.php','icon-users','admin_staff'],
    ['users.php','icon-user-shield','admin_users'],
    ['tickets.php','icon-ticket','admin_tickets'],
    ['feedback.php','icon-edit','admin_feedback'],
    ['notifications.php','icon-message','admin_notifications'],
    ['templates.php','icon-paint-brush','admin_templates'],
    ['update.php','icon-cloud-arrow-up','admin_update'],
];
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="<?php echo e($currentLang) ?>" class="<?php echo $currentLang==='en'?'lang-en':'' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?php echo YUYUN_URL ?>/assets/css/iconfont.css">
    <link rel="stylesheet" href="<?php echo YUYUN_URL ?>/assets/css/style.css">
    <script>
    (function(){
        try {
            var theme = localStorage.getItem('yy_theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        } catch(e){}
    })();
    </script>
</head>
<body class="admin-body">
<aside class="admin-sidebar" id="adminSidebar">
    <div class="brand"><i class="iconfont icon-cloud"></i> <?php echo e(setting('site_short','语云')) ?> <?php echo __('admin_title') ?></div>
    <?php foreach ($adminMenu as $m): ?>
        <a href="<?php echo YUYUN_URL ?>/admin/<?php echo $m[0] ?>" class="<?php echo $current===$m[0]?'active':'' ?>"><i class="iconfont <?php echo $m[1] ?>"></i> <?php echo __($m[2]) ?></a>
    <?php endforeach; ?>
    <a href="<?php echo YUYUN_URL ?>/index.php" target="_blank"><i class="iconfont icon-eye"></i> <?php echo __('admin_view_site') ?></a>
    <a href="<?php echo YUYUN_URL ?>/logout.php"><i class="iconfont icon-logout"></i> <?php echo __('admin_logout') ?></a>
</aside>
<main class="admin-main">
    <div class="admin-topbar">
        <button class="hamburger admin-hamburger" id="adminMenuToggle" aria-label="菜单"><i class="iconfont icon-menu"></i></button>
        <h1><?php echo e($pageTitle) ?></h1>
        <div class="admin-actions">
            <div class="lang-switcher-wrap">
                <button class="header-icon-btn" id="adminLangSwitcherBtn" title="<?php echo __('language') ?>"><i class="iconfont icon-translate"></i></button>
                <div class="lang-popup" id="adminLangPopup">
                    <div class="lang-popup-title"><?php echo __('select_language') ?></div>
                    <?php foreach ($availableLanguages as $code => $info): ?>
                    <a href="<?php echo langUrl($code) ?>" class="lang-option <?php echo $currentLang===$code?'current':'' ?>">
                        <span class="lang-flag"><?php echo $info['flag'] ?></span>
                        <span class="lang-name"><?php echo e($info['name']) ?></span>
                        <?php if ($currentLang===$code): ?><i class="iconfont icon-check lang-check"></i><?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="header-icon-btn" id="adminThemeToggle" title="<?php echo __('theme_dark') ?> / <?php echo __('theme_light') ?>"><i class="iconfont icon-sun theme-icon-light"></i><i class="iconfont icon-moon theme-icon-dark"></i></button>
            <a href="<?php echo YUYUN_URL ?>/user/index.php" class="btn btn-sm btn-outline"><?php echo __('user_center') ?></a>
            <a href="<?php echo YUYUN_URL ?>/logout.php" class="btn btn-sm btn-dark"><?php echo __('logout') ?></a>
        </div>
    </div>
