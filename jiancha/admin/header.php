<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) redirect(SITE_URL . 'login.php');
$admin = currentUser();
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>后台管理 - <?php echo e(siteName()); ?></title>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body>
<div class="admin">
<aside class="admin-side" id="adminSide">
<div class="admin-side-head"><h2><?php echo e(siteName()); ?></h2></div>
<ul class="admin-side-nav">
<li class="group">内容管理</li>
<li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'on' : ''; ?>">首页概览</a></li>
<li><a href="articles.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'articles.php' || basename($_SERVER['PHP_SELF']) == 'article_edit.php' ? 'on' : ''; ?>">文章管理</a></li>
<li><a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'on' : ''; ?>">栏目管理</a></li>
<li><a href="pages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'pages.php' ? 'on' : ''; ?>">单页管理</a></li>
<li><a href="slides.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'slides.php' ? 'on' : ''; ?>">轮播图</a></li>
<li class="group">互动管理</li>
<li><a href="messages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'on' : ''; ?>">留言管理</a></li>
<li class="group">系统设置</li>
<li><a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'on' : ''; ?>">系统设置</a></li>
<li><a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'on' : ''; ?>">用户管理</a></li>
<?php if (isSuperAdmin()): ?>
<li><a href="admins.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admins.php' ? 'on' : ''; ?>">管理员</a></li>
<?php endif; ?>
<li class="group">快速导航</li>
<li><a href="<?php echo SITE_URL; ?>index.php" target="_blank">网站首页</a></li>
<li><a href="<?php echo SITE_URL; ?>logout.php">退出登录</a></li>
</ul>
</aside>
<div class="admin-main">
<div class="admin-top">
<button class="admin-btn" id="adminMenuBtn">&#9776;</button>
<h3><?php echo $adminTitle ?? '后台管理'; ?></h3>
<div class="right"><span><?php echo e($admin['nickname'] ?: $admin['username']); ?></span><a href="<?php echo SITE_URL; ?>logout.php">退出</a></div>
</div>
<div class="admin-body">