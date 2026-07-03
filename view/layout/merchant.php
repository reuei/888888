<?php
$merchant = session('merchant_user') ?? [];
$currentPath = parse_url(trim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH);
$currentPath = preg_replace('#^public/#', '', $currentPath);

$menu = [
    ['name' => '仪表盘', 'icon' => 'dashboard', 'url' => 'merchant/dashboard', 'children' => []],
    ['name' => '商品管理', 'icon' => 'goods', 'url' => '#', 'children' => [
        ['name' => '商品列表', 'url' => 'merchant/goods'],
        ['name' => '新增商品', 'url' => 'merchant/goods/create'],
        ['name' => '卡密管理', 'url' => 'merchant/goods/card'],
        ['name' => '批量导入', 'url' => 'merchant/goods/import'],
        ['name' => '货源广场', 'url' => 'merchant/goods/source'],
    ]],
    ['name' => '订单管理', 'icon' => 'order', 'url' => '#', 'children' => [
        ['name' => '订单列表', 'url' => 'merchant/order'],
        ['name' => '投诉处理', 'url' => 'merchant/order/complaint'],
    ]],
    ['name' => '客服管理', 'icon' => 'chat', 'url' => '#', 'children' => [
        ['name' => '咨询列表', 'url' => 'merchant/chat'],
        ['name' => '回复会话', 'url' => 'merchant/chat/session'],
    ]],
    ['name' => '资金管理', 'icon' => 'finance', 'url' => '#', 'children' => [
        ['name' => '资金概览', 'url' => 'merchant/finance'],
        ['name' => '资金流水', 'url' => 'merchant/finance/flow'],
        ['name' => '结算提现', 'url' => 'merchant/finance/settle'],
    ]],
    ['name' => '店铺设置', 'icon' => 'setting', 'url' => '#', 'children' => [
        ['name' => '店铺信息', 'url' => 'merchant/setting'],
        ['name' => '修改密码', 'url' => 'merchant/setting/password'],
        ['name' => '实名认证', 'url' => 'merchant/setting/auth'],
        ['name' => '自定义支付', 'url' => 'merchant/setting/payment'],
        ['name' => '引导页', 'url' => 'merchant/setting/guide'],
        ['name' => '子域名', 'url' => 'merchant/setting/domain'],
    ]],
];
?>
<!DOCTYPE html>
<html lang="zh-CN" data-theme="merchant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? '后台'); ?> - 鲸商城 Pro B端</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body class="admin-body">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle" aria-label="折叠侧边栏">
                <svg class="icon" aria-hidden="true"><use href="#icon-menu"></use></svg>
            </button>
            <a href="<?php echo url('merchant/dashboard'); ?>" class="logo">
                <span class="logo-mark"><svg class="icon" aria-hidden="true"><use href="#icon-merchant"></use></svg></span>
                鲸商城 Pro · B端
            </a>
        </div>
        <div class="topbar-right">
            <a href="#" data-action="fullscreen" title="全屏">
                <svg class="icon" aria-hidden="true"><use href="#icon-fullscreen"></use></svg>
            </a>
            <a href="#" title="消息">
                <svg class="icon" aria-hidden="true"><use href="#icon-bell"></use></svg>
            </a>
            <a href="#" title="帮助">
                <svg class="icon" aria-hidden="true"><use href="#icon-help"></use></svg>
            </a>
            <div class="user-menu">
                <div class="avatar"><?php echo h(mb_substr($merchant['shop_name'] ?? 'M', 0, 1)); ?></div>
                <span><?php echo h($merchant['shop_name'] ?? '商户'); ?></span>
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
            鲸商城 Pro v1.0.0 | 操作手册 | 客服入口
        </footer>
    </div>

    <script src="/static/js/app.js"></script>
</body>
</html>
