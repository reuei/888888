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
            <a href="<?php echo YUYUN_URL ?>/index.php" class="<?php echo $current==='index.php'?'active':'' ?>"><i class="iconfont icon-home"></i> <span><?php echo __('home') ?></span></a>
            <a href="<?php echo YUYUN_URL ?>/about.php" class="<?php echo $current==='about.php'?'active':'' ?>"><i class="iconfont icon-shield"></i> <span><?php echo __('about') ?></span></a>
            <a href="<?php echo YUYUN_URL ?>/company.php" class="<?php echo $current==='company.php'?'active':'' ?>"><i class="iconfont icon-building"></i> <span><?php echo __('company') ?></span></a>
            <a href="<?php echo YUYUN_URL ?>/products.php" class="<?php echo $current==='products.php'?'active':'' ?>"><i class="iconfont icon-cubes"></i> <span><?php echo __('products') ?></span></a>
            <a href="<?php echo YUYUN_URL ?>/partners.php" class="<?php echo $current==='partners.php'?'active':'' ?>"><i class="iconfont icon-handshake"></i> <span><?php echo __('partners') ?></span></a>
            <a href="<?php echo YUYUN_URL ?>/contact.php" class="<?php echo $current==='contact.php'?'active':'' ?>"><i class="iconfont icon-envelope"></i> <span><?php echo __('contact') ?></span></a>
            <a href="<?php echo e(setting('international_url','https://cloud.loveym.cloud')) ?>" target="_blank"><i class="iconfont icon-globe"></i> <span><?php echo __('intl') ?></span></a>
            <?php if (is_logged_in()): ?>
                <a href="<?php echo YUYUN_URL ?>/user/index.php"><i class="iconfont icon-user"></i> <span><?php echo __('user_center') ?></span></a>
                <?php if (is_admin()): ?>
                    <a href="<?php echo YUYUN_URL ?>/admin/index.php"><i class="iconfont icon-gauge"></i> <span><?php echo __('admin') ?></span></a>
                <?php endif; ?>
                <a href="<?php echo YUYUN_URL ?>/logout.php"><i class="iconfont icon-logout"></i> <span><?php echo __('logout') ?></span></a>
            <?php else: ?>
                <a href="<?php echo YUYUN_URL ?>/login.php"><i class="iconfont icon-lock"></i> <span><?php echo __('login') ?></span></a>
            <?php endif; ?>
        </nav>
        <div class="header-actions">
            <button class="header-icon-btn" id="themeToggle" title="<?php echo __('theme_dark') ?> / <?php echo __('theme_light') ?>">
                <i class="iconfont icon-sun theme-icon-light"></i>
                <i class="iconfont icon-moon theme-icon-dark"></i>
            </button>
            <div class="lang-switcher-wrap">
                <button class="header-icon-btn" id="langSwitcherBtn" title="<?php echo __('language') ?>">
                    <i class="iconfont icon-translate"></i>
                </button>
                <div class="lang-popup" id="langPopup">
                    <div class="lang-popup-title"><?php echo __('select_language') ?></div>
                    <?php foreach ($availableLanguages as $code => $info): ?>
                    <a href="<?php echo langUrl($code) ?>" class="lang-option <?php echo $currentLang===$code?'current':'' ?>">
                        <span class="lang-flag"><?php echo $info['flag'] ?></span>
                        <span class="lang-name"><?php echo e($info['name']) ?></span>
                        <?php if ($currentLang===$code): ?><i class="iconfont icon-check lang-check"></i><?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="hamburger" id="hamburger" aria-label="菜单"><i class="iconfont icon-menu"></i></button>
        </div>
    </div>
</header>
<div class="header-spacer"></div>
