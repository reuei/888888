<?php
$adminNav = [
    ['url' => 'index.php', 'label' => '控制台', 'icon' => 'fa-gauge'],
    ['url' => 'settings.php', 'label' => '站点配置', 'icon' => 'fa-gears'],
    ['url' => 'slides.php', 'label' => '轮播图', 'icon' => 'fa-images'],
    ['url' => 'products.php', 'label' => '产品管理', 'icon' => 'fa-cubes'],
    ['url' => 'partners.php', 'label' => '合作伙伴', 'icon' => 'fa-handshake'],
    ['url' => 'links.php', 'label' => '友情链接', 'icon' => 'fa-link'],
    ['url' => 'certs.php', 'label' => '证书管理', 'icon' => 'fa-certificate'],
    ['url' => 'messages.php', 'label' => '留言管理', 'icon' => 'fa-envelope'],
    ['url' => 'backup.php', 'label' => '备份恢复', 'icon' => 'fa-database'],
];
$currentAdminUrl = basename($_SERVER['PHP_SELF']);
$admin = currentAdmin();
$siteTitle = getSetting('site_title', '语云科技');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo yy_e($pageTitle ?? '管理后台'); ?> - <?php echo yy_e($siteTitle); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-cloud" style="font-size:24px;color:var(--primary);"></i>
            <span>语云后台</span>
        </div>
        <nav class="nav-menu">
            <?php foreach ($adminNav as $nav): ?>
                <a href="<?php echo yy_e($nav['url']); ?>" class="nav-item <?php echo $currentAdminUrl === $nav['url'] ? 'active' : ''; ?>">
                    <i class="fa-solid <?php echo yy_e($nav['icon']); ?>"></i>
                    <?php echo yy_e($nav['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sidebar-footer">
            <div>当前用户：<?php echo yy_e($admin['username'] ?? ''); ?></div>
            <div style="margin-top:8px;"><a href="../" target="_blank">访问前台</a> | <a href="logout.php">退出登录</a></div>
        </div>
    </aside>
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-title"><?php echo yy_e($pageTitle ?? '管理后台'); ?></div>
            <div class="topbar-actions">
                <a href="../" target="_blank"><i class="fa-solid fa-globe"></i> 访问前台</a>
                <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> 退出</a>
            </div>
        </header>
        <div class="content">
            <?php $flash = getFlash(); if ($flash): ?>
                <div class="alert alert-<?php echo yy_e($flash['type']); ?>"><?php echo yy_e($flash['message']); ?></div>
            <?php endif; ?>
