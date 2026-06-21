<?php
if (!defined('YUYUN_ROOT')) require __DIR__ . '/config.php';
$pageTitle = $pageTitle ?? setting('site_name','语云科技');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle) ?> - <?php echo e(setting('site_slogan','企业与开发者信赖的云计算与数字化服务伙伴')) ?></title>
    <meta name="description" content="<?php echo e(setting('site_slogan')) ?>">
    <link rel="icon" href="<?php echo e(setting('site_favicon', YUYUN_URL . '/assets/img/favicon.ico')) ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo YUYUN_URL ?>/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/nav.php'; ?>
<div class="main-wrap">
