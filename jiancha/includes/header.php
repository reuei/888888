<?php
if (!isset($currentSlug)) $currentSlug = '';
if (!isset($pageTitle)) $pageTitle = '';
$u = currentUser();
$cats = getCategories(true);
?><!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?php echo $pageTitle ? e($pageTitle) . ' - ' : ''; ?><?php echo e(siteName()); ?></title>
<link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body>
<nav class="nav" id="nav">
<div class="container nav-inner">
<a href="<?php echo SITE_URL; ?>index.php" class="logo">
<span class="logo-mark">检</span>
<span class="logo-text"><?php echo e(siteName()); ?></span>
</a>
<button class="nav-toggle" id="navToggle" aria-label="菜单"><span></span><span></span><span></span></button>
<div class="nav-links" id="navLinks">
<a href="<?php echo SITE_URL; ?>index.php" class="nav-link<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? ' active' : ''; ?>">首页</a>
<?php foreach ($cats as $c): ?>
<a href="<?php echo SITE_URL; ?>category.php?slug=<?php echo urlencode($c['slug']); ?>" class="nav-link<?php echo $currentSlug === $c['slug'] ? ' active' : ''; ?>"><?php echo e($c['name']); ?></a>
<?php endforeach; ?>
<?php if ($u): ?>
<a href="<?php echo SITE_URL; ?>user.php" class="nav-link"><?php echo e($u['nickname'] ?: $u['username']); ?></a>
<a href="<?php echo SITE_URL; ?>logout.php" class="nav-link">退出</a>
<?php else: ?>
<a href="<?php echo SITE_URL; ?>login.php" class="nav-link">登录</a>
<?php endif; ?>
<a href="<?php echo SITE_URL; ?>search.php" class="nav-link nav-search">&#9906;</a>
</div>
</div>
</nav>
<div class="nav-spacer"></div>
