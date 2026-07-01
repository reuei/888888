<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? site_config('site_name')); ?></title>
    <link rel="stylesheet" href="<?php echo base_url('css/style.css'); ?>">
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <a href="<?php echo url('/'); ?>" class="logo"><?php echo h(site_config('site_name')); ?></a>
        </div>
        <div class="topbar-links">
            <a href="<?php echo url('/'); ?>">首页</a>
            <a href="<?php echo url('product'); ?>">授权产品</a>
            <a href="<?php echo url('plugin'); ?>">插件市场</a>
            <?php if (get_user()): ?>
            <a href="<?php echo url('user'); ?>">个人中心</a>
            <a href="<?php echo url('login/logout'); ?>">退出</a>
            <?php else: ?>
            <a href="<?php echo url('login'); ?>">登录</a>
            <a href="<?php echo url('login/register'); ?>">注册</a>
            <?php endif; ?>
            <a href="<?php echo url('admin/dashboard'); ?>">后台</a>
        </div>
        <div class="mobile-menu-btn" id="mobileMenuBtn">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="mobile-drawer" id="mobileDrawer">
        <a href="<?php echo url('/'); ?>">首页</a>
        <a href="<?php echo url('product'); ?>">授权产品</a>
        <a href="<?php echo url('plugin'); ?>">插件市场</a>
        <?php if (get_user()): ?>
        <a href="<?php echo url('user'); ?>">个人中心</a>
        <a href="<?php echo url('login/logout'); ?>">退出</a>
        <?php else: ?>
        <a href="<?php echo url('login'); ?>">登录</a>
        <a href="<?php echo url('login/register'); ?>">注册</a>
        <?php endif; ?>
        <a href="<?php echo url('admin/dashboard'); ?>">后台</a>
    </div>
    <div class="container">
        <?php echo $__content__ ?? ''; ?>
    </div>
    <div class="footer">
        <?php echo h(site_config('copyright', 'QEEFG v1.0.0')); ?>
    </div>
    <script>
    (function() {
        const btn = document.getElementById('mobileMenuBtn');
        const drawer = document.getElementById('mobileDrawer');
        if (!btn || !drawer) return;
        btn.addEventListener('click', () => drawer.classList.toggle('open'));
        document.addEventListener('click', (e) => {
            if (!btn.contains(e.target) && !drawer.contains(e.target)) {
                drawer.classList.remove('open');
            }
        });
    })();
    </script>
</body>
</html>
