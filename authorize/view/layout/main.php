<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? site_config('site_name')); ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- 页面加载动画 -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
        <div class="loader-logo"><?php echo h(site_config('site_name')); ?></div>
    </div>

    <!-- 顶部导航栏 - 玻璃拟态 -->
    <nav class="topbar">
        <div class="topbar-left">
            <a href="<?php echo url('/'); ?>" class="logo">
                <span class="logo-mark">
                    <svg class="svg-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                </span>
                <?php echo h(site_config('site_name')); ?>
            </a>
        </div>
        <div class="topbar-links">
            <a href="<?php echo url('/'); ?>">
                <i data-icon="home" class="svg-icon-sm"></i><span>首页</span>
            </a>
            <a href="<?php echo url('product'); ?>">
                <i data-icon="product" class="svg-icon-sm"></i><span>授权产品</span>
            </a>
            <a href="<?php echo url('plugin'); ?>">
                <i data-icon="plugin" class="svg-icon-sm"></i><span>插件市场</span>
            </a>
            <?php if (get_user()): ?>
            <a href="<?php echo url('user'); ?>">
                <i data-icon="user" class="svg-icon-sm"></i><span>个人中心</span>
            </a>
            <a href="<?php echo url('login/logout'); ?>">
                <i data-icon="logout" class="svg-icon-sm"></i><span>退出</span>
            </a>
            <?php else: ?>
            <a href="<?php echo url('login'); ?>">
                <i data-icon="user" class="svg-icon-sm"></i><span>登录</span>
            </a>
            <a href="<?php echo url('login/register'); ?>" class="nav-cta">
                <i data-icon="key" class="svg-icon-sm"></i><span>注册</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo url('admin/dashboard'); ?>">
                <i data-icon="dashboard" class="svg-icon-sm"></i><span>后台</span>
            </a>
        </div>
        <!-- 汉堡菜单（移动端） -->
        <div class="mobile-menu-btn" id="mobileMenuBtn" aria-label="菜单">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- 移动端抽屉菜单 -->
    <div class="mobile-drawer" id="mobileDrawer">
        <a href="<?php echo url('/'); ?>">
            <i data-icon="home"></i><span>首页</span>
        </a>
        <a href="<?php echo url('product'); ?>">
            <i data-icon="product"></i><span>授权产品</span>
        </a>
        <a href="<?php echo url('plugin'); ?>">
            <i data-icon="plugin"></i><span>插件市场</span>
        </a>
        <?php if (get_user()): ?>
        <a href="<?php echo url('user'); ?>">
            <i data-icon="user"></i><span>个人中心</span>
        </a>
        <a href="<?php echo url('login/logout'); ?>">
            <i data-icon="logout"></i><span>退出</span>
        </a>
        <?php else: ?>
        <a href="<?php echo url('login'); ?>">
            <i data-icon="user"></i><span>登录</span>
        </a>
        <a href="<?php echo url('login/register'); ?>" class="drawer-cta">
            <i data-icon="key"></i><span>立即注册</span>
        </a>
        <?php endif; ?>
        <a href="<?php echo url('admin/dashboard'); ?>">
            <i data-icon="dashboard"></i><span>后台</span>
        </a>
    </div>

    <!-- 主容器 -->
    <div class="container">
        <?php echo $__content__ ?? ''; ?>
    </div>

    <!-- 页脚 -->
    <footer class="footer">
        <div class="footer-links">
            <a href="<?php echo url('/'); ?>">首页</a>
            <a href="<?php echo url('product'); ?>">授权产品</a>
            <a href="<?php echo url('plugin'); ?>">插件市场</a>
            <a href="<?php echo url('user'); ?>">个人中心</a>
        </div>
        <div>
            <span class="footer-brand"><?php echo h(site_config('site_name')); ?></span>
            · <?php echo h(site_config('copyright', 'QEEFG v1.0.0')); ?>
        </div>
    </footer>

    <script src="/static/js/app.js"></script>
</body>
</html>
