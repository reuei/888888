<?php
$admin = session('admin_user') ?? [];
$currentPath = parse_url(trim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH);
$currentPath = preg_replace('#^public/#', '', $currentPath);

/**
 * 菜单数组结构保持与原版一致：name / icon / url
 * 新增 group 字段用于侧边栏分组渲染（仅 UI 展示用途，不影响角色过滤逻辑）
 */
$menu = [
    ['name' => '仪表盘',       'icon' => 'dashboard', 'url' => 'admin/dashboard', 'group' => '总览'],
    ['name' => '授权产品',     'icon' => 'product',   'url' => 'admin/product',   'group' => '商品管理'],
    ['name' => '插件市场',     'icon' => 'plugin',    'url' => 'admin/plugin',    'group' => '商品管理'],
    ['name' => '版本更新包',   'icon' => 'version',   'url' => 'admin/version',   'group' => '商品管理'],
    ['name' => '订单管理',     'icon' => 'order',     'url' => 'admin/order',     'group' => '订单管理'],
    ['name' => '充值管理',     'icon' => 'recharge',  'url' => 'admin/recharge',  'group' => '订单管理'],
    ['name' => '用户管理',     'icon' => 'user',      'url' => 'admin/user',      'group' => '用户管理'],
    ['name' => '授权码管理',   'icon' => 'license',   'url' => 'admin/license',    'group' => '用户管理'],
    ['name' => '系统设置',     'icon' => 'setting',   'url' => 'admin/setting',   'group' => '系统'],
];

// 按分组聚合（保留原数组结构 + 角色过滤逻辑入口，便于后续按 role 扩展）
$adminRole   = $admin['role'] ?? '';
$menuGroups  = [];
foreach ($menu as $item) {
    // 角色过滤占位：未来可在此处按 $adminRole 进行权限收窄，当前保持全量展示
    $menuGroups[$item['group']][] = $item;
}

$adminName    = $admin['username'] ?? '管理员';
$adminAvatar = mb_substr($adminName, 0, 1);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? '后台'); ?> - <?php echo h(site_config('site_name')); ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="admin-body">
    <!-- 页面加载动画 -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
        <div class="loader-logo">后台管理</div>
    </div>

    <!-- 顶部栏 -->
    <header class="admin-topbar">
        <div class="admin-topbar-left">
            <button class="menu-toggle" id="menuToggle" aria-label="折叠菜单">
                <i data-icon="menu"></i>
            </button>
            <a href="<?php echo url('admin/dashboard'); ?>" class="admin-logo">
                <span class="logo-badge">Q</span>
                <span class="admin-logo-text"><?php echo h(site_config('site_name')); ?></span>
            </a>
        </div>
        <div class="admin-topbar-right">
            <button class="admin-icon-btn" id="fullscreenBtn" type="button" aria-label="全屏" title="全屏">
                <i data-icon="dashboard"></i>
            </button>
            <a href="<?php echo url('/'); ?>" class="admin-icon-btn" target="_blank" rel="noopener" aria-label="前台" title="前台">
                <i data-icon="home"></i>
            </a>
            <a href="<?php echo url('admin/recharge'); ?>" class="admin-icon-btn admin-bell" aria-label="消息" title="待处理">
                <i data-icon="bell"></i>
                <span class="admin-bell-badge" id="adminBellBadge">0</span>
            </a>
            <div class="admin-user-menu" id="adminUserMenu">
                <button type="button" class="admin-user" aria-label="用户菜单">
                    <span class="admin-user-avatar"><?php echo h($adminAvatar); ?></span>
                    <span class="admin-user-name"><?php echo h($adminName); ?></span>
                    <i data-icon="chevron-down" class="svg-icon-sm admin-user-caret"></i>
                </button>
                <div class="admin-user-dropdown" id="adminUserDropdown">
                    <div class="admin-user-head">
                        <div class="admin-user-avatar lg"><?php echo h($adminAvatar); ?></div>
                        <div>
                            <div class="admin-user-pop-name"><?php echo h($adminName); ?></div>
                            <div class="admin-user-pop-role"><?php echo $adminRole ? h($adminRole) : '超级管理员'; ?></div>
                        </div>
                    </div>
                    <a href="<?php echo url('admin/setting'); ?>">
                        <i data-icon="setting" class="svg-icon-sm"></i><span>系统设置</span>
                    </a>
                    <a href="<?php echo url('/'); ?>" target="_blank" rel="noopener">
                        <i data-icon="home" class="svg-icon-sm"></i><span>前台首页</span>
                    </a>
                    <a href="<?php echo url('admin/admin/logout'); ?>" class="admin-logout">
                        <i data-icon="logout" class="svg-icon-sm"></i><span>退出登录</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- 侧边栏 -->
    <aside class="sidebar" id="sidebar">
        <ul class="menu">
            <?php foreach ($menuGroups as $groupName => $items): ?>
            <li class="menu-group">
                <div class="menu-group-title"><?php echo h($groupName); ?></div>
                <ul class="menu-sub">
                    <?php foreach ($items as $item): ?>
                    <li class="menu-item">
                        <a href="<?php echo url($item['url']); ?>" class="menu-link <?php echo strpos($currentPath, $item['url']) === 0 ? 'active' : ''; ?>">
                            <span class="menu-icon"><i data-icon="<?php echo h($item['icon']); ?>"></i></span>
                            <span class="menu-text"><?php echo h($item['name']); ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- 主内容 -->
    <main class="admin-main">
        <div class="admin-content">
            <?php echo $__content__ ?? ''; ?>
        </div>
        <footer class="admin-footer">
            <?php echo h(site_config('copyright', 'QEEFG v1.0.0')); ?>
        </footer>
    </main>

    <style>
    .admin-logo { display: inline-flex; align-items: center; gap: 10px; }
    .admin-logo-text { font-size: 16px; font-weight: 700; color: var(--color-text); max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .admin-icon-btn {
        width: 38px; height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-sm);
        color: var(--color-text-secondary);
        border: 1px solid transparent;
        transition: all var(--transition);
        position: relative;
    }
    .admin-icon-btn:hover { color: var(--color-primary); background: var(--color-primary-lighter); border-color: var(--color-primary-light); }
    .admin-bell { position: relative; }
    .admin-bell-badge {
        position: absolute;
        top: 4px; right: 4px;
        min-width: 16px; height: 16px;
        padding: 0 4px;
        background: var(--color-danger);
        color: #fff;
        border-radius: var(--radius-pill);
        font-size: 10px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    .admin-bell-badge:empty, .admin-bell-badge[data-count="0"] { display: none; }

    .admin-user-menu { position: relative; }
    .admin-user {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 10px 4px 4px;
        border-radius: var(--radius-pill);
        border: 1px solid var(--color-border-light);
        background: #fff;
        transition: all var(--transition);
    }
    .admin-user:hover { border-color: var(--color-primary-light); box-shadow: var(--shadow-sm); }
    .admin-user-avatar {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--gradient-primary);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .admin-user-avatar.lg { width: 44px; height: 44px; font-size: 18px; }
    .admin-user-name { font-size: 13px; font-weight: 600; color: var(--color-text); max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .admin-user-caret { color: var(--color-text-tertiary); transition: transform var(--transition); }
    .admin-user-menu.open .admin-user-caret { transform: rotate(180deg); }

    .admin-user-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 220px;
        background: #fff;
        border: 1px solid var(--color-border-light);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all var(--transition);
        z-index: 100;
    }
    .admin-user-menu.open .admin-user-dropdown { opacity: 1; visibility: visible; transform: translateY(0); }
    .admin-user-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px 14px;
        border-bottom: 1px solid var(--color-border-light);
        margin-bottom: 6px;
    }
    .admin-user-pop-name { font-size: 14px; font-weight: 700; color: var(--color-text); }
    .admin-user-pop-role { font-size: 12px; color: var(--color-text-tertiary); margin-top: 2px; }
    .admin-user-dropdown a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        color: var(--color-text-secondary);
        transition: all var(--transition);
    }
    .admin-user-dropdown a:hover { background: var(--color-primary-lighter); color: var(--color-primary); }
    .admin-user-dropdown .admin-logout { color: var(--color-danger); }
    .admin-user-dropdown .admin-logout:hover { background: #fee2e2; color: var(--color-danger); }

    /* 菜单分组 */
    .menu-group { padding: 0; list-style: none; }
    .menu-sub { list-style: none; padding: 0; margin: 0; }
    .menu-group-title {
        font-size: 11px;
        font-weight: 700;
        color: var(--color-text-tertiary);
        letter-spacing: 1px;
        padding: 14px 16px 6px;
        text-transform: uppercase;
    }
    .menu-group:first-child .menu-group-title { padding-top: 4px; }
    .sidebar.collapsed .menu-group-title { display: none; }
    </style>

    <script src="/static/js/app.js"></script>
    <script>
    (function () {
        var sidebar = document.getElementById('sidebar');
        var toggle = document.getElementById('menuToggle');
        if (toggle && sidebar) {
            toggle.addEventListener('click', function () { sidebar.classList.toggle('collapsed'); });
        }
        var fsBtn = document.getElementById('fullscreenBtn');
        if (fsBtn) {
            fsBtn.addEventListener('click', function () {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen && document.documentElement.requestFullscreen();
                } else {
                    document.exitFullscreen && document.exitFullscreen();
                }
            });
        }
        var userMenu = document.getElementById('adminUserMenu');
        if (userMenu) {
            var trigger = userMenu.querySelector('.admin-user');
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                userMenu.classList.toggle('open');
            });
            document.addEventListener('click', function () { userMenu.classList.remove('open'); });
        }
    })();
    </script>
</body>
</html>
