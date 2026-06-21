<?php $current = basename($_SERVER['PHP_SELF']); ?>
<header class="site-header" id="header">
    <div class="container nav-inner">
        <a href="<?php echo YUYUN_URL ?>/index.php" class="logo">
            <?php if (setting('site_logo')): ?>
                <img src="<?php echo e(setting('site_logo')) ?>" alt="<?php echo e(setting('site_name')) ?>">
            <?php else: ?>
                <span class="logo-text"><i class="iconfont icon-cloud"></i> <?php echo e(setting('site_short','语云')) ?></span>
            <?php endif; ?>
        </a>
        <nav class="main-nav" id="mainNav">
            <a href="<?php echo YUYUN_URL ?>/index.php" class="<?php echo $current==='index.php'?'active':'' ?>"><i class="iconfont icon-home"></i> <?php echo __('home') ?></a>
            <a href="<?php echo YUYUN_URL ?>/about.php" class="<?php echo $current==='about.php'?'active':'' ?>"><i class="iconfont icon-shield"></i> <?php echo __('about') ?></a>
            <a href="<?php echo YUYUN_URL ?>/company.php" class="<?php echo $current==='company.php'?'active':'' ?>"><i class="iconfont icon-building"></i> <?php echo __('company') ?></a>
            <a href="<?php echo YUYUN_URL ?>/products.php" class="<?php echo $current==='products.php'?'active':'' ?>"><i class="iconfont icon-cubes"></i> <?php echo __('products') ?></a>
            <a href="<?php echo YUYUN_URL ?>/partners.php" class="<?php echo $current==='partners.php'?'active':'' ?>"><i class="iconfont icon-handshake"></i> <?php echo __('partners') ?></a>
            <a href="<?php echo YUYUN_URL ?>/contact.php" class="<?php echo $current==='contact.php'?'active':'' ?>"><i class="iconfont icon-envelope"></i> <?php echo __('contact') ?></a>
            <a href="<?php echo e(setting('international_url','https://cloud.loveym.cloud')) ?>" target="_blank"><i class="iconfont icon-globe"></i> <?php echo __('intl') ?></a>
            <?php if (is_logged_in()): ?>
                <a href="<?php echo YUYUN_URL ?>/user/index.php"><i class="iconfont icon-user"></i> <?php echo __('user_center') ?></a>
                <?php if (is_admin()): ?>
                    <a href="<?php echo YUYUN_URL ?>/admin/index.php"><i class="iconfont icon-gauge"></i> <?php echo __('admin') ?></a>
                <?php endif; ?>
                <a href="<?php echo YUYUN_URL ?>/logout.php"><i class="iconfont icon-logout"></i> <?php echo __('logout') ?></a>
            <?php else: ?>
                <a href="<?php echo YUYUN_URL ?>/login.php"><i class="iconfont icon-lock"></i> <?php echo __('login') ?></a>
            <?php endif; ?>
        </nav>
        <div class="header-actions">
            <button class="header-icon-btn" id="themeToggle" title="<?php echo __('theme_dark') ?> / <?php echo __('theme_light') ?>">
                <i class="iconfont icon-sun theme-icon-light"></i>
                <i class="iconfont icon-moon theme-icon-dark"></i>
            </button>
            <a href="<?php echo langUrl($currentLang === 'zh' ? 'en' : 'zh') ?>" class="header-icon-btn" title="<?php echo __('language') ?>">
                <i class="iconfont icon-translate"></i>
            </a>
            <button class="hamburger" id="hamburger" aria-label="菜单"><i class="iconfont icon-menu"></i></button>
        </div>
    </div>
</header>
<div class="header-spacer"></div>
