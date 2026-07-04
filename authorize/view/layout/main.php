<?php
$currentUser = get_user();
$userName    = $currentUser ? ($currentUser['nickname'] ?: $currentUser['username']) : '';
$userAvatar  = $userName ? mb_substr($userName, 0, 1) : '';
?>
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

        <div class="topbar-center">
            <form class="topbar-search" action="<?php echo url('product'); ?>" method="get" id="topbarSearchForm">
                <i data-icon="search" class="svg-icon-sm"></i>
                <input type="text" name="keyword" id="topbarSearchInput" placeholder="搜索授权产品 / 插件..." autocomplete="off">
                <button type="submit" class="topbar-search-btn" aria-label="搜索">
                    <i data-icon="search" class="svg-icon-sm"></i>
                </button>
            </form>
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
            <a href="<?php echo url('/'); ?>#articles">
                <i data-icon="bell" class="svg-icon-sm"></i><span>公告</span>
            </a>
            <?php if ($currentUser): ?>
            <a href="<?php echo url('user/license'); ?>">
                <i data-icon="license" class="svg-icon-sm"></i><span>我的授权</span>
            </a>
            <?php endif; ?>
            <?php if ($currentUser): ?>
            <div class="topbar-user-menu" id="topbarUserMenu">
                <button type="button" class="topbar-user" aria-label="用户菜单">
                    <span class="topbar-user-avatar"><?php echo h($userAvatar); ?></span>
                    <span class="topbar-user-name"><?php echo h($userName); ?></span>
                    <i data-icon="chevron-down" class="svg-icon-sm topbar-user-caret"></i>
                </button>
                <div class="topbar-user-dropdown" id="topbarUserDropdown">
                    <a href="<?php echo url('user'); ?>">
                        <i data-icon="dashboard" class="svg-icon-sm"></i><span>个人中心</span>
                    </a>
                    <a href="<?php echo url('user/license'); ?>">
                        <i data-icon="license" class="svg-icon-sm"></i><span>我的授权</span>
                    </a>
                    <a href="<?php echo url('user/order'); ?>">
                        <i data-icon="order" class="svg-icon-sm"></i><span>订单记录</span>
                    </a>
                    <a href="<?php echo url('user/recharge'); ?>">
                        <i data-icon="recharge" class="svg-icon-sm"></i><span>余额充值</span>
                    </a>
                    <a href="<?php echo url('user/password'); ?>">
                        <i data-icon="key" class="svg-icon-sm"></i><span>修改密码</span>
                    </a>
                    <a href="<?php echo url('login/logout'); ?>" class="topbar-logout">
                        <i data-icon="logout" class="svg-icon-sm"></i><span>退出登录</span>
                    </a>
                </div>
            </div>
            <?php else: ?>
            <a href="<?php echo url('login'); ?>">
                <i data-icon="user" class="svg-icon-sm"></i><span>登录</span>
            </a>
            <a href="<?php echo url('login/register'); ?>" class="nav-cta">
                <i data-icon="key" class="svg-icon-sm"></i><span>注册</span>
            </a>
            <?php endif; ?>
        </div>

        <!-- 三线变 X 汉堡菜单（移动端） -->
        <div class="mobile-menu-btn" id="mobileMenuBtn" aria-label="菜单">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>

    <!-- 抽屉遮罩 -->
    <div class="drawer-overlay" id="drawerOverlay"></div>

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
        <a href="<?php echo url('/'); ?>#articles">
            <i data-icon="bell"></i><span>公告</span>
        </a>
        <?php if ($currentUser): ?>
        <a href="<?php echo url('user/license'); ?>">
            <i data-icon="license"></i><span>我的授权</span>
        </a>
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

    <!-- 浮动客服按钮 -->
    <a href="<?php echo url('/'); ?>#articles" class="float-service" id="floatService" aria-label="联系客服" title="联系客服">
        <i data-icon="bell"></i>
        <span class="float-service-pulse"></span>
        <span class="float-service-tooltip">联系客服</span>
    </a>

    <!-- 返回顶部 -->
    <button type="button" class="back-to-top" id="backToTop" aria-label="返回顶部" title="返回顶部">
        <i data-icon="chevron-right" class="svg-icon-sm"></i>
    </button>

    <!-- 页脚 -->
    <footer class="footer">
        <div class="footer-links">
            <a href="<?php echo url('/'); ?>">首页</a>
            <a href="<?php echo url('product'); ?>">授权产品</a>
            <a href="<?php echo url('plugin'); ?>">插件市场</a>
            <a href="<?php echo url('/'); ?>#articles">公告</a>
            <?php if ($currentUser): ?>
            <a href="<?php echo url('user'); ?>">个人中心</a>
            <?php else: ?>
            <a href="<?php echo url('login/register'); ?>">注册</a>
            <?php endif; ?>
        </div>
        <div>
            <span class="footer-brand"><?php echo h(site_config('site_name')); ?></span>
            · <?php echo h(site_config('copyright', 'QEEFG v1.0.0')); ?>
        </div>
    </footer>

    <style>
    /* 中部搜索框 */
    .topbar-center { flex: 1; max-width: 420px; margin: 0 24px; }
    .topbar-search {
        position: relative;
        display: flex;
        align-items: center;
        height: 40px;
        background: rgba(124, 58, 237, 0.06);
        border: 1px solid rgba(124, 58, 237, 0.12);
        border-radius: var(--radius-pill);
        padding: 0 12px;
        transition: all var(--transition);
    }
    .topbar-search:focus-within {
        background: #fff;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.12);
    }
    .topbar-search > .svg-icon-sm { color: var(--color-text-tertiary); flex-shrink: 0; }
    .topbar-search input {
        flex: 1;
        border: none;
        background: transparent;
        outline: none;
        padding: 0 10px;
        font-size: 13px;
        color: var(--color-text);
        min-width: 0;
    }
    .topbar-search input::placeholder { color: var(--color-text-tertiary); }
    .topbar-search-btn {
        width: 28px; height: 28px;
        border: none;
        background: var(--gradient-primary);
        color: #fff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform var(--transition);
    }
    .topbar-search-btn:hover { transform: scale(1.08); }

    /* 顶栏用户菜单 */
    .topbar-user-menu { position: relative; }
    .topbar-user {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 10px 4px 4px;
        border-radius: var(--radius-pill);
        border: 1px solid transparent;
        background: transparent;
        transition: all var(--transition);
    }
    .topbar-user:hover { background: var(--color-primary-lighter); }
    .topbar-user-avatar {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: var(--gradient-primary);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .topbar-user-name { font-size: 13px; font-weight: 600; color: var(--color-text); max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .topbar-user-caret { color: var(--color-text-tertiary); transition: transform var(--transition); }
    .topbar-user-menu.open .topbar-user-caret { transform: rotate(180deg); }
    .topbar-user-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 200px;
        background: #fff;
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all var(--transition);
        z-index: 1001;
    }
    .topbar-user-menu.open .topbar-user-dropdown { opacity: 1; visibility: visible; transform: translateY(0); }
    .topbar-user-dropdown a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        color: var(--color-text-secondary);
        transition: all var(--transition);
    }
    .topbar-user-dropdown a:hover { background: var(--color-primary-lighter); color: var(--color-primary); }
    .topbar-user-dropdown .topbar-logout { color: var(--color-danger); }
    .topbar-user-dropdown .topbar-logout:hover { background: #fee2e2; color: var(--color-danger); }

    /* 浮动客服按钮 */
    .float-service {
        position: fixed;
        right: 24px;
        bottom: 32px;
        width: 56px; height: 56px;
        background: var(--gradient-primary);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 32px rgba(124, 58, 237, 0.4);
        z-index: 900;
        transition: transform var(--transition);
    }
    .float-service .svg-icon, .float-service i { width: 24px; height: 24px; }
    .float-service:hover { transform: scale(1.08) rotate(-6deg); }
    .float-service-pulse {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: var(--color-primary);
        opacity: 0.6;
        animation: floatPulse 2s var(--ease-out) infinite;
        z-index: -1;
    }
    @keyframes floatPulse {
        0% { transform: scale(1); opacity: 0.6; }
        100% { transform: scale(1.8); opacity: 0; }
    }
    .float-service-tooltip {
        position: absolute;
        right: 70px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--color-text);
        color: #fff;
        padding: 6px 12px;
        border-radius: var(--radius-sm);
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all var(--transition);
        pointer-events: none;
    }
    .float-service:hover .float-service-tooltip { opacity: 1; visibility: visible; right: 74px; }

    /* 返回顶部 */
    .back-to-top {
        position: fixed;
        right: 24px;
        bottom: 100px;
        width: 44px; height: 44px;
        background: #fff;
        color: var(--color-primary);
        border: 1px solid var(--color-border-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-md);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px) rotate(-90deg);
        transition: all var(--transition);
        z-index: 900;
        cursor: pointer;
    }
    .back-to-top.show { opacity: 1; visibility: visible; transform: translateY(0) rotate(-90deg); }
    .back-to-top:hover { background: var(--color-primary); color: #fff; border-color: var(--color-primary); }

    @media (max-width: 1024px) {
        .topbar-center { max-width: 280px; margin: 0 16px; }
    }
    @media (max-width: 768px) {
        .topbar-center { display: none; }
        .topbar-links .topbar-user-menu { display: none; }
        .float-service { right: 16px; bottom: 24px; width: 50px; height: 50px; }
        .back-to-top { right: 16px; bottom: 88px; }
    }
    </style>

    <script src="/static/js/app.js"></script>
    <script>
    (function () {
        // 三线变 X 汉堡菜单 + 抽屉
        var btn = document.getElementById('mobileMenuBtn');
        var drawer = document.getElementById('mobileDrawer');
        var overlay = document.getElementById('drawerOverlay');
        function toggleDrawer(state) {
            var open = typeof state === 'boolean' ? state : !btn.classList.contains('open');
            btn.classList.toggle('open', open);
            drawer.classList.toggle('open', open);
            overlay.classList.toggle('open', open);
            document.body.style.overflow = open ? 'hidden' : '';
        }
        if (btn) btn.addEventListener('click', function () { toggleDrawer(); });
        if (overlay) overlay.addEventListener('click', function () { toggleDrawer(false); });
        if (drawer) drawer.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () { toggleDrawer(false); });
        });

        // 顶栏用户菜单
        var userMenu = document.getElementById('topbarUserMenu');
        if (userMenu) {
            var trigger = userMenu.querySelector('.topbar-user');
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                userMenu.classList.toggle('open');
            });
            document.addEventListener('click', function () { userMenu.classList.remove('open'); });
        }

        // 返回顶部
        var backTop = document.getElementById('backToTop');
        if (backTop) {
            window.addEventListener('scroll', function () {
                backTop.classList.toggle('show', window.scrollY > 400);
            });
            backTop.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // 搜索框：回车跳转到 product?keyword=
        var searchForm = document.getElementById('topbarSearchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', function (e) {
                var input = document.getElementById('topbarSearchInput');
                if (input && !input.value.trim()) {
                    e.preventDefault();
                    if (typeof Toast !== 'undefined') Toast.error('请输入搜索关键词');
                }
            });
        }
    })();
    </script>
</body>
</html>
