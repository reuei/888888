<?php
if (!defined('YUYUN_ROOT')) require __DIR__ . '/config.php';
$pageTitle = $pageTitle ?? setting('site_name', L('nav.home', '语云科技'));
$lang = current_lang();
$theme = setting('site_default_theme', 'light');
?>
<!DOCTYPE html>
<html lang="<?php echo $lang === 'en' ? 'en' : 'zh-CN' ?>" data-theme="<?php echo e($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle) ?> - <?php echo e(setting('site_slogan', L('home.welcome_subtitle', '企业与开发者信赖的云计算与数字化服务伙伴'))) ?></title>
    <meta name="description" content="<?php echo e(setting('site_slogan')) ?>">
    <link rel="icon" href="<?php echo e(setting('site_favicon', YUYUN_URL . '/assets/img/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?php echo YUYUN_URL ?>/assets/css/iconfont.css">
    <link rel="stylesheet" href="<?php echo YUYUN_URL ?>/assets/css/iconpark.css">
    <link rel="stylesheet" href="<?php echo YUYUN_URL ?>/assets/css/style.css">
    <script>
    (function(){
        var t = localStorage.getItem('yy_theme') || '<?php echo e($theme) ?>';
        document.documentElement.setAttribute('data-theme', t);
    })();
    </script>
</head>
<body class="<?php echo setting('top_banner_enabled') ? 'has-banner' : '' ?>">
<?php if (setting('top_banner_enabled')): ?>
<div class="top-banner" style="background:<?php echo e(setting('top_banner_bg', '#ff6a00')) ?>">
    <div class="top-banner-inner">
        <i class="iconfont icon-<?php echo e(setting('top_banner_icon', 'bell')) ?>"></i>
        <div class="top-banner-scroll"><span><?php echo e(setting('top_banner_text', '')) ?></span></div>
    </div>
</div>
<?php endif; ?>
<?php require __DIR__ . '/nav.php'; ?>
<div class="top-notice-container" id="topNoticeContainer"></div>
<div class="main-wrap">
