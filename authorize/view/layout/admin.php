<?php
$admin = session('admin_user') ?? [];
$currentPath = parse_url(trim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH);
$currentPath = preg_replace('#^public/#', '', $currentPath);

$menu = [
    ['name' => '仪表盘', 'icon' => 'dashboard', 'url' => 'admin/dashboard'],
    ['name' => '用户管理', 'icon' => 'user', 'url' => 'admin/user'],
    ['name' => '授权产品', 'icon' => 'product', 'url' => 'admin/product'],
    ['name' => '订单管理', 'icon' => 'order', 'url' => 'admin/order'],
    ['name' => '授权码管理', 'icon' => 'license', 'url' => 'admin/license'],
    ['name' => '插件市场', 'icon' => 'plugin', 'url' => 'admin/plugin'],
    ['name' => '版本更新包', 'icon' => 'version', 'url' => 'admin/version'],
    ['name' => '充值管理', 'icon' => 'recharge', 'url' => 'admin/recharge'],
    ['name' => '系统设置', 'icon' => 'setting', 'url' => 'admin/setting'],
];
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
            <div class="admin-logo">
                <span class="logo-badge">Q</span>
                <?php echo h(site_config('site_name')); ?>
            </div>
        </div>
        <div class="admin-topbar-right">
            <span class="admin-user">
                <span class="admin-user-avatar">
                    <i data-icon="user" class="svg-icon-sm"></i>
                </span>
                <?php echo h($admin['username'] ?? '管理员'); ?>
            </span>
            <a href="<?php echo url('/'); ?>" target="_blank">
                <i data-icon="home" class="svg-icon-sm"></i><span>前台</span>
            </a>
            <a href="<?php echo url('admin/admin/logout'); ?>">
                <i data-icon="logout" class="svg-icon-sm"></i><span>退出</span>
            </a>
        </div>
    </header>

    <!-- 侧边栏 -->
    <aside class="sidebar" id="sidebar">
        <ul class="menu">
            <?php foreach ($menu as $item): ?>
            <li class="menu-item">
                <a href="<?php echo url($item['url']); ?>" class="menu-link <?php echo strpos($currentPath, $item['url']) === 0 ? 'active' : ''; ?>">
                    <span class="menu-icon"><i data-icon="<?php echo h($item['icon']); ?>"></i></span>
                    <span class="menu-text"><?php echo h($item['name']); ?></span>
                </a>
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

    <script src="/static/js/app.js"></script>
</body>
</html>
