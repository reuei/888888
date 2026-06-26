<?php
require __DIR__ . '/config.php';
require_admin();
$pageTitle = ($pageTitle ?? '后台管理') . ' - 语云科技后台';
$adminMenu = [
    ['index.php','icon-gauge','概览'],
    ['settings.php','icon-sliders','站点配置'],
    ['slides.php','icon-images','轮播管理'],
    ['products.php','icon-cubes','产品管理'],
    ['partners.php','icon-handshake','合作伙伴'],
    ['staff.php','icon-users','员工卡片'],
    ['users.php','icon-user-shield','用户管理'],
    ['tickets.php','icon-ticket','工单管理'],
    ['feedback.php','icon-edit','反馈管理'],
    ['notifications.php','icon-message','消息通知'],
    ['templates.php','icon-paint-brush','模板管理'],
    ['update.php','icon-cloud-arrow-up','代码更新'],
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
    <div class="brand"><i class="iconfont icon-cloud"></i> 语云后台</div>
    <?php foreach ($adminMenu as $m): ?>
        <a href="<?php echo YUYUN_URL ?>/admin/<?php echo $m[0] ?>" class="<?php echo $current===$m[0]?'active':'' ?>"><i class="iconfont <?php echo $m[1] ?>"></i> <?php echo e($m[2]) ?></a>
    <?php endforeach; ?>
    <a href="<?php echo YUYUN_URL ?>/index.php" target="_blank"><i class="iconfont icon-eye"></i> 查看前台</a>
    <a href="<?php echo YUYUN_URL ?>/logout.php"><i class="iconfont icon-logout"></i> 退出登录</a>
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
            <button class="header-icon-btn" id="adminThemeToggle" title="切换主题"><i class="iconfont icon-sun theme-icon-light"></i><i class="iconfont icon-moon theme-icon-dark"></i></button>
            <a href="<?php echo YUYUN_URL ?>/user/index.php" class="btn btn-sm btn-outline">用户中心</a>
            <a href="<?php echo YUYUN_URL ?>/logout.php" class="btn btn-sm btn-dark">退出</a>
        </div>
    </div>
