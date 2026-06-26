<?php
if (!defined('YUYUN_ROOT')) require __DIR__ . '/config.php';
$pageTitle = $pageTitle ?? setting('site_name','语云科技');
$bannerEnabled = setting('banner_enabled','1') === '1';
$bannerText = setting('banner_text', __('banner_default'));
$bannerBg = setting('banner_bg_color','#0a0a0a');
$bannerIcon = setting('banner_icon','megaphone');
?>
<!DOCTYPE html>
<html lang="<?php echo e($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle) ?> - <?php echo e(setting('site_slogan','企业与开发者信赖的云计算与数字化服务伙伴')) ?></title>
    <meta name="description" content="<?php echo e(setting('site_slogan')) ?>">
    <link rel="icon" href="<?php echo e(setting('site_favicon', YUYUN_URL . '/assets/img/favicon.svg')) ?>">
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
<body>
<?php if ($bannerEnabled && trim($bannerText) !== ''): ?>
<div class="site-banner" id="siteBanner" style="background:<?php echo e($bannerBg) ?>">
    <div class="container banner-inner">
        <div class="banner-icon-left"><i class="iconfont icon-<?php echo e($bannerIcon) ?>"></i></div>
        <div class="banner-marquee">
            <span class="banner-text"><?php echo e($bannerText) ?></span>
            <span class="banner-text" aria-hidden="true"><?php echo e($bannerText) ?></span>
        </div>
        <button class="banner-close" id="bannerClose" aria-label="关闭公告"><i class="iconfont icon-close"></i></button>
    </div>
</div>
<?php endif; ?>
<?php require __DIR__ . '/nav.php'; ?>
<div id="globalNotifier" class="global-notifier" aria-live="polite"></div>
<div class="main-wrap">
