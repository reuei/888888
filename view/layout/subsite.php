<?php
$admin = session('admin_user') ?? [];
$currentPath = parse_url(trim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH);
$currentPath = preg_replace('#^public/#', '', $currentPath);

$menu = [
    ['name' => '仪表盘', 'icon' => 'dashboard', 'url' => 'subsite/dashboard', 'children' => []],
    ['name' => '商户管理', 'icon' => 'merchant', 'url' => '#', 'children' => [
        ['name' => '商户列表', 'url' => 'subsite/merchant'],
        ['name' => '入驻审核', 'url' => 'subsite/merchant/audit'],
    ]],
    ['name' => '商品管理', 'icon' => 'goods', 'url' => '#', 'children' => [
        ['name' => '分站商品', 'url' => 'subsite/goods'],
        ['name' => '库存监控', 'url' => 'subsite/goods/stock'],
    ]],
    ['name' => '订单管理', 'icon' => 'order', 'url' => '#', 'children' => [
        ['name' => '订单列表', 'url' => 'subsite/order'],
        ['name' => '投诉管理', 'url' => 'subsite/order/complaint'],
    ]],
    ['name' => '财务结算', 'icon' => 'finance', 'url' => '#', 'children' => [
        ['name' => '资金流水', 'url' => 'subsite/finance/flow'],
        ['name' => '结算管理', 'url' => 'subsite/finance/settle'],
    ]],
];
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="subsite">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? '后台'); ?> - <?php echo h(site_config('site_name', '鲸商城 Pro')); ?> 分站</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body class="admin-body">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle" aria-label="折叠侧边栏">
                <svg class="icon" aria-hidden="true"><use href="#icon-menu"></use></svg>
            </button>
            <a href="<?php echo url('subsite/dashboard'); ?>" class="logo">
                <span class="logo-mark"><svg class="icon" aria-hidden="true"><use href="#icon-subsite"></use></svg></span>
                <?php echo h(site_config('site_name', '鲸商城 Pro')); ?> · 分站
            </a>
        </div>
        <div class="topbar-right">
            <span style="color: var(--text-muted); font-size: 13px; padding: 6px 12px; background: var(--primary-50); border-radius: var(--radius-full); display: inline-flex; align-items: center; gap: 6px;">
                <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-subsite"></use></svg>
                <?php echo h($admin['subsite_name'] ?? '分站后台'); ?>
            </span>
            <a href="#" data-action="fullscreen" title="全屏">
                <svg class="icon" aria-hidden="true"><use href="#icon-fullscreen"></use></svg>
            </a>
            <a href="#" title="消息">
                <svg class="icon" aria-hidden="true"><use href="#icon-bell"></use></svg>
            </a>
            <div class="user-menu">
                <div class="avatar"><?php echo h(mb_substr($admin['username'] ?? 'S', 0, 1)); ?></div>
                <span><?php echo h($admin['username'] ?? '分站超管'); ?></span>
            </div>
            <a href="<?php echo url('login/logout'); ?>" title="退出">
                <svg class="icon" aria-hidden="true"><use href="#icon-logout"></use></svg>
            </a>
        </div>
    </header>

    <aside class="sidebar" id="sidebar">
        <ul class="menu">
            <?php foreach ($menu as $item): ?>
            <li class="menu-item">
                <a href="<?php echo $item['children'] ? '#' : url($item['url']); ?>" class="menu-link <?php echo strpos($currentPath, $item['url']) === 0 ? 'active' : ''; ?>" data-has-submenu="<?php echo $item['children'] ? '1' : '0'; ?>">
                    <span class="menu-icon"><svg class="icon" aria-hidden="true"><use href="#icon-<?php echo h($item['icon']); ?>"></use></svg></span>
                    <span class="menu-text"><?php echo h($item['name']); ?></span>
                    <?php if ($item['children']): ?>
                    <span class="arrow"><svg class="icon icon-sm" aria-hidden="true"><use href="#icon-chevron-right"></use></svg></span>
                    <?php endif; ?>
                </a>
                <?php if ($item['children']): ?>
                <div class="submenu">
                    <?php foreach ($item['children'] as $sub): ?>
                    <a href="<?php echo url($sub['url']); ?>" class="<?php echo strpos($currentPath, $sub['url']) === 0 ? 'active' : ''; ?>"><?php echo h($sub['name']); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <div class="main">
        <main class="content">
            <?php echo $__content__ ?? ''; ?>
        </main>
        <footer class="footer">
            <?php echo h(site_config('copyright', '鲸商城 Pro v1.0.0')); ?> | 分站管理系统
        </footer>
    </div>

    <script src="/static/js/app.js"></script>
</body>
</html>
