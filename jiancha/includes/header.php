<?php
$cats = getCategories();
$currentSlug = $currentSlug ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?><?php echo e(siteName()); ?></title>
<meta name="description" content="人民检察院法律监督信息公开平台">
<link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body>

<nav class="nav">
  <div class="container">
    <a href="<?php echo SITE_URL; ?>index.php" class="nav-logo">
      <div class="logo">检</div>
      <?php echo e(siteName()); ?>
    </a>
    <ul class="nav-links">
      <li><a href="<?php echo SITE_URL; ?>index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">首页</a></li>
      <?php foreach ($cats as $c): ?>
      <li><a href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $c['slug']; ?>" class="<?php echo $currentSlug == $c['slug'] ? 'active' : ''; ?>"><?php echo e($c['name']); ?></a></li>
      <?php endforeach; ?>
      <li><a href="<?php echo SITE_URL; ?>report.php">举报</a></li>
      <?php if (isLoggedIn()): ?>
        <li><a href="<?php echo SITE_URL; ?>user.php"><?php echo e(currentUser()['nickname'] ?: currentUser()['username']); ?></a></li>
        <li><a href="<?php echo SITE_URL; ?>logout.php">退出</a></li>
        <?php if (isAdmin()): ?>
        <li><a href="<?php echo SITE_URL; ?>admin/index.php" class="btn-admin">管理</a></li>
        <?php endif; ?>
      <?php else: ?>
        <li><a href="<?php echo SITE_URL; ?>login.php">登录</a></li>
      <?php endif; ?>
    </ul>
    <button class="nav-btn" id="menuBtn">&#9776;</button>
  </div>
</nav>

<aside class="mobile-menu" id="mobileMenu">
  <div class="mobile-menu-panel">
    <button class="mobile-close" id="mobileClose">&times;</button>
    <div style="margin-top:40px">
      <a href="<?php echo SITE_URL; ?>index.php">首页</a>
      <?php foreach ($cats as $c): ?>
      <a href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $c['slug']; ?>"><?php echo e($c['name']); ?></a>
      <?php endforeach; ?>
      <a href="<?php echo SITE_URL; ?>report.php">信访举报</a>
      <?php if (isLoggedIn()): ?>
        <a href="<?php echo SITE_URL; ?>user.php">个人中心</a>
        <a href="<?php echo SITE_URL; ?>logout.php">退出</a>
        <?php if (isAdmin()): ?>
        <a href="<?php echo SITE_URL; ?>admin/index.php">管理后台</a>
        <?php endif; ?>
      <?php else: ?>
        <a href="<?php echo SITE_URL; ?>login.php">登录</a>
        <a href="<?php echo SITE_URL; ?>register.php">注册</a>
      <?php endif; ?>
    </div>
  </div>
</aside>

<script src="<?php echo SITE_URL; ?>assets/js/main.js"></script>