<?php
require __DIR__ . '/config.php';
require_admin();
$pageTitle = ($pageTitle ?? '后台管理') . ' - 语云科技后台';
$adminMenu = [
    ['index.php','fa-gauge','概览'],
    ['settings.php','fa-sliders','站点配置'],
    ['slides.php','fa-images','轮播管理'],
    ['products.php','fa-cubes','产品管理'],
    ['partners.php','fa-handshake','合作伙伴'],
    ['staff.php','fa-users','员工卡片'],
    ['users.php','fa-user-shield','用户管理'],
    ['tickets.php','fa-ticket','工单管理'],
    ['templates.php','fa-paint-brush','模板管理'],
    ['update.php','fa-cloud-arrow-up','代码更新'],
];
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo YUYUN_URL ?>/assets/css/style.css">
</head>
<body class="admin-body">
<aside class="admin-sidebar" id="adminSidebar">
    <div class="brand"><i class="fa-solid fa-cloud"></i> 语云后台</div>
    <?php foreach ($adminMenu as $m): ?>
        <a href="<?php echo YUYUN_URL ?>/admin/<?php echo $m[0] ?>" class="<?php echo $current===$m[0]?'active':'' ?>"><i class="fa-solid <?php echo $m[1] ?>"></i> <?php echo $m[2] ?></a>
    <?php endforeach; ?>
    <a href="<?php echo YUYUN_URL ?>/index.php" target="_blank"><i class="fa-solid fa-eye"></i> 查看前台</a>
    <a href="<?php echo YUYUN_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> 退出登录</a>
</aside>
<main class="admin-main">
    <div class="admin-topbar">
        <h1><?php echo e($pageTitle) ?></h1>
        <button class="hamburger" id="adminMenuToggle" style="display:none"><span></span><span></span><span></span></button>
    </div>
