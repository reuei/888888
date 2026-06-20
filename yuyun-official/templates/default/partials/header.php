<?php
$navItems = [
    ['page' => 'home', 'label' => '首页', 'url' => './'],
    ['page' => 'about', 'label' => '关于我们', 'url' => '?page=about'],
    ['page' => 'company', 'label' => '公司简介', 'url' => '?page=company'],
    ['page' => 'products', 'label' => '产品介绍', 'url' => '?page=products'],
    ['page' => 'contact', 'label' => '联系我们', 'url' => '?page=contact'],
    ['page' => 'partners', 'label' => '合作伙伴', 'url' => '?page=partners'],
];
$currentPage = $currentPage ?? 'home';
$logo = getSetting('site_logo');
$siteTitle = getSetting('site_title', '语云科技');
$phone = getSetting('sales_phone', '400-800-8451');
?>
<header class="header">
    <div class="header-inner">
        <a href="./" class="logo">
            <?php if ($logo && file_exists(YUYUN_ROOT . '/' . $logo)): ?>
                <img src="<?php echo yy_e($logo); ?>" alt="<?php echo yy_e($siteTitle); ?>">
            <?php else: ?>
                <i class="fa-solid fa-cloud"></i>
            <?php endif; ?>
            <span><?php echo yy_e($siteTitle); ?></span>
        </a>
        <nav class="nav">
            <?php foreach ($navItems as $item): ?>
                <a href="<?php echo yy_e($item['url']); ?>" class="<?php echo $currentPage === $item['page'] ? 'active' : ''; ?>"><?php echo yy_e($item['label']); ?></a>
            <?php endforeach; ?>
            <a href="<?php echo yy_e(getSetting('international_url', 'https://cloud.loveym.cloud')); ?>" target="_blank">国际版</a>
        </nav>
        <div class="header-actions">
            <a href="tel:<?php echo yy_e($phone); ?>" class="btn btn-primary"><i class="fa-solid fa-phone"></i> <?php echo yy_e($phone); ?></a>
            <a href="?page=contact" class="btn btn-outline">联系我们</a>
        </div>
        <div class="hamburger">
            <span></span><span></span><span></span>
        </div>
    </div>
</header>
<div class="mobile-nav">
    <?php foreach ($navItems as $item): ?>
        <a href="<?php echo yy_e($item['url']); ?>"><?php echo yy_e($item['label']); ?></a>
    <?php endforeach; ?>
    <a href="<?php echo yy_e(getSetting('international_url', 'https://cloud.loveym.cloud')); ?>" target="_blank">国际版</a>
    <a href="tel:<?php echo yy_e($phone); ?>" class="btn btn-primary"><i class="fa-solid fa-phone"></i> 拨打电话</a>
</div>
