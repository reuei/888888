<?php $current = basename($_SERVER['PHP_SELF']); ?>
<header class="site-header" id="header">
    <div class="container nav-inner">
        <a href="<?php echo YUYUN_URL ?>/index.php" class="logo">
            <?php if (setting('site_logo')): ?>
                <img src="<?php echo e(setting('site_logo')) ?>" alt="<?php echo e(setting('site_name')) ?>">
            <?php else: ?>
                <span class="logo-text"><i class="iconfont icon-cloud"></i> <?php echo e(setting('site_short', L('nav.home', '语云'))) ?></span>
            <?php endif; ?>
        </a>
        <nav class="main-nav" id="mainNav">
            <a href="<?php echo YUYUN_URL ?>/index.php" class="<?php echo $current==='index.php'?'active':'' ?>"><?php echo L('nav.home', '首页') ?></a>
            <a href="<?php echo YUYUN_URL ?>/about.php" class="<?php echo $current==='about.php'?'active':'' ?>"><?php echo L('nav.about', '关于我们') ?></a>
            <a href="<?php echo YUYUN_URL ?>/company.php" class="<?php echo $current==='company.php'?'active':'' ?>"><?php echo L('nav.company', '公司简介') ?></a>
            <a href="<?php echo YUYUN_URL ?>/products.php" class="<?php echo $current==='products.php'?'active':'' ?>"><?php echo L('nav.products', '产品介绍') ?></a>
            <a href="<?php echo YUYUN_URL ?>/partners.php" class="<?php echo $current==='partners.php'?'active':'' ?>"><?php echo L('nav.partners', '合作伙伴') ?></a>
            <a href="<?php echo YUYUN_URL ?>/contact.php" class="<?php echo $current==='contact.php'?'active':'' ?>"><?php echo L('nav.contact', '联系我们') ?></a>
            <a href="<?php echo e(setting('international_url','https://cloud.loveym.cloud')) ?>" target="_blank"><?php echo L('nav.international', '国际版官网') ?></a>
            <?php if (is_logged_in()): ?>
                <a href="<?php echo YUYUN_URL ?>/user/index.php"><?php echo L('nav.user', '用户中心') ?></a>
                <?php if (is_admin()): ?>
                    <a href="<?php echo YUYUN_URL ?>/admin/index.php"><?php echo L('nav.admin', '后台') ?></a>
                <?php endif; ?>
                <a href="<?php echo YUYUN_URL ?>/logout.php"><?php echo L('nav.logout', '退出') ?></a>
            <?php else: ?>
                <a href="<?php echo YUYUN_URL ?>/login.php"><?php echo L('nav.login', '登录') ?></a>
            <?php endif; ?>
        </nav>
        <div class="header-tools">
            <button class="tool-btn" id="themeToggle" title="<?php echo L('btn.save', '切换主题') ?>" aria-label="theme"><i class="iconfont icon-moon"></i></button>
            <button class="tool-btn" id="langToggle" title="<?php echo L('btn.save', '切换语言') ?>" aria-label="language"><i class="iconfont icon-translate"></i></button>
        </div>
        <button class="hamburger" id="hamburger" aria-label="<?php echo L('nav.home', '菜单') ?>"><i class="iconfont icon-menu"></i></button>
    </div>
</header>
<div class="header-spacer"></div>
