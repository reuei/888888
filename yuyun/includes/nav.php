<?php $current = basename($_SERVER['PHP_SELF']); ?>
<header class="site-header" id="header">
    <div class="container nav-inner">
        <a href="<?php echo YUYUN_URL ?>/index.php" class="logo">
            <?php if (setting('site_logo')): ?>
                <img src="<?php echo e(setting('site_logo')) ?>" alt="<?php echo e(setting('site_name')) ?>">
            <?php else: ?>
                <span class="logo-text"><i class="fa-solid fa-cloud"></i> <?php echo e(setting('site_short','语云')) ?></span>
            <?php endif; ?>
        </a>
        <nav class="main-nav" id="mainNav">
            <a href="<?php echo YUYUN_URL ?>/index.php" class="<?php echo $current==='index.php'?'active':'' ?>">首页</a>
            <a href="<?php echo YUYUN_URL ?>/about.php" class="<?php echo $current==='about.php'?'active':'' ?>">关于我们</a>
            <a href="<?php echo YUYUN_URL ?>/company.php" class="<?php echo $current==='company.php'?'active':'' ?>">公司简介</a>
            <a href="<?php echo YUYUN_URL ?>/products.php" class="<?php echo $current==='products.php'?'active':'' ?>">产品介绍</a>
            <a href="<?php echo YUYUN_URL ?>/partners.php" class="<?php echo $current==='partners.php'?'active':'' ?>">合作伙伴</a>
            <a href="<?php echo YUYUN_URL ?>/contact.php" class="<?php echo $current==='contact.php'?'active':'' ?>">联系我们</a>
            <a href="<?php echo e(setting('international_url','https://cloud.loveym.cloud')) ?>" target="_blank">国际版官网</a>
            <?php if (is_logged_in()): ?>
                <a href="<?php echo YUYUN_URL ?>/user/index.php">用户中心</a>
                <?php if (is_admin()): ?>
                    <a href="<?php echo YUYUN_URL ?>/admin/index.php">后台</a>
                <?php endif; ?>
                <a href="<?php echo YUYUN_URL ?>/logout.php">退出</a>
            <?php else: ?>
                <a href="<?php echo YUYUN_URL ?>/login.php">登录</a>
            <?php endif; ?>
        </nav>
        <button class="hamburger" id="hamburger" aria-label="菜单"><span></span><span></span><span></span></button>
    </div>
</header>
<div class="header-spacer"></div>
