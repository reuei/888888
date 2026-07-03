<?php
$admin = session('admin_user') ?? [];
$currentPath = parse_url(trim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH);
$currentPath = preg_replace('#^public/#', '', $currentPath);

$menu = [
    ['name' => '仪表盘', 'icon' => 'dashboard', 'url' => 'admin/dashboard', 'children' => []],
    ['name' => '分站管理', 'icon' => 'subsite', 'url' => '#', 'roles' => ['super'], 'children' => [
        ['name' => '分站列表', 'url' => 'admin/subsite'],
        ['name' => '新建分站', 'url' => 'admin/subsite/create'],
        ['name' => '分站监控', 'url' => 'admin/subsite/monitor'],
    ]],
    ['name' => '商户管理', 'icon' => 'merchant', 'url' => '#', 'children' => [
        ['name' => '商户列表', 'url' => 'admin/merchant'],
        ['name' => '入驻审核', 'url' => 'admin/merchant/audit'],
        ['name' => '邀请码', 'url' => 'admin/merchant/invite', 'roles' => ['super', 'admin']],
    ]],
    ['name' => '商品管理', 'icon' => 'goods', 'url' => '#', 'children' => [
        ['name' => '全平台商品', 'url' => 'admin/goods'],
        ['name' => '分类管理', 'url' => 'admin/goods/category'],
        ['name' => '禁售目录', 'url' => 'admin/goods/ban'],
    ]],
    ['name' => '订单管理', 'icon' => 'order', 'url' => '#', 'children' => [
        ['name' => '订单列表', 'url' => 'admin/order'],
        ['name' => '投诉管理', 'url' => 'admin/order/complaint'],
    ]],
    ['name' => '用户管理', 'icon' => 'user', 'url' => '#', 'children' => [
        ['name' => '用户列表', 'url' => 'admin/user'],
        ['name' => '等级分组', 'url' => 'admin/user/group'],
    ]],
    ['name' => '代理分销', 'icon' => 'agent', 'url' => '#', 'roles' => ['super', 'admin'], 'children' => [
        ['name' => '代理商品', 'url' => 'admin/agent/goods'],
        ['name' => '代理树', 'url' => 'admin/agent/tree'],
        ['name' => '佣金记录', 'url' => 'admin/agent/commission'],
        ['name' => '佣金结算', 'url' => 'admin/agent/settle'],
    ]],
    ['name' => '财务结算', 'icon' => 'finance', 'url' => '#', 'roles' => ['super', 'admin'], 'children' => [
        ['name' => '资金流水', 'url' => 'admin/finance/flow'],
        ['name' => '费率分组', 'url' => 'admin/finance/rate'],
        ['name' => '结算打款', 'url' => 'admin/finance/settle'],
    ]],
    ['name' => '支付网关', 'icon' => 'payment', 'url' => '#', 'roles' => ['super', 'admin'], 'children' => [
        ['name' => '渠道配置', 'url' => 'admin/payment/channel'],
        ['name' => '风控策略', 'url' => 'admin/payment/risk'],
    ]],
    ['name' => '模板前端', 'icon' => 'template', 'url' => '#', 'roles' => ['super', 'admin'], 'children' => [
        ['name' => '首页模板', 'url' => 'admin/template/home'],
        ['name' => '购卡页模板', 'url' => 'admin/template/goods'],
    ]],
    ['name' => '文章公告', 'icon' => 'article', 'url' => '#', 'children' => [
        ['name' => '公告列表', 'url' => 'admin/article'],
        ['name' => '发布公告', 'url' => 'admin/article/create'],
    ]],
    ['name' => '广告位', 'icon' => 'ad', 'url' => 'admin/ad', 'children' => []],
    ['name' => '优惠券', 'icon' => 'coupon', 'url' => 'admin/coupon', 'roles' => ['super', 'admin'], 'children' => []],
    ['name' => '积分商城', 'icon' => 'points', 'url' => '#', 'roles' => ['super', 'admin'], 'children' => [
        ['name' => '积分规则', 'url' => 'admin/points'],
        ['name' => '积分商品', 'url' => 'admin/points/goods'],
        ['name' => '兑换订单', 'url' => 'admin/points/order'],
        ['name' => '积分流水', 'url' => 'admin/points/log'],
    ]],
    ['name' => '数据统计', 'icon' => 'stat', 'url' => '#', 'children' => [
        ['name' => '经营报表', 'url' => 'admin/stat/report'],
        ['name' => '操作日志', 'url' => 'admin/stat/log'],
    ]],
    ['name' => '在线更新', 'icon' => 'update', 'url' => 'admin/update', 'roles' => ['super', 'admin'], 'children' => []],
    ['name' => '系统设置', 'icon' => 'setting', 'url' => '#', 'roles' => ['super', 'admin'], 'children' => [
        ['name' => '站点基础', 'url' => 'admin/setting'],
        ['name' => '邮件系统', 'url' => 'admin/setting/email'],
        ['name' => '短信通知', 'url' => 'admin/setting/sms'],
        ['name' => '消息模板', 'url' => 'admin/message'],
        ['name' => '发送日志', 'url' => 'admin/message/log'],
        ['name' => '文件存储', 'url' => 'admin/setting/storage'],
        ['name' => '安全防护', 'url' => 'admin/setting/security'],
        ['name' => '数据备份', 'url' => 'admin/backup', 'roles' => ['super', 'admin']],
        ['name' => '管理员账号', 'url' => 'admin/admin', 'roles' => ['super']],
    ]]
];

// 根据当前管理员角色过滤菜单
$currentRole = $admin['role'] ?? '';
$filteredMenu = [];
foreach ($menu as $item) {
    $itemRoles = $item['roles'] ?? ['super', 'admin', 'operator'];
    if (!in_array($currentRole, $itemRoles, true)) {
        continue;
    }
    if (!empty($item['children'])) {
        $children = [];
        foreach ($item['children'] as $sub) {
            $subRoles = $sub['roles'] ?? ['super', 'admin', 'operator'];
            if (in_array($currentRole, $subRoles, true)) {
                $children[] = $sub;
            }
        }
        if (empty($children)) {
            continue;
        }
        $item['children'] = $children;
    }
    $filteredMenu[] = $item;
}
$menu = $filteredMenu;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? '后台'); ?> - <?php echo h(site_config('site_name', '鲸商城 Pro')); ?> S端</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body class="admin-body">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle" aria-label="折叠侧边栏">
                <svg class="icon" aria-hidden="true"><use href="#icon-menu"></use></svg>
            </button>
            <a href="<?php echo url('admin/dashboard'); ?>" class="logo">
                <span class="logo-mark"><svg class="icon" aria-hidden="true"><use href="#icon-admin"></use></svg></span>
                <?php echo h(site_config('site_name', '鲸商城 Pro')); ?> · S端
            </a>
        </div>
        <div class="topbar-right">
            <a href="#" data-action="fullscreen" title="全屏">
                <svg class="icon" aria-hidden="true"><use href="#icon-fullscreen"></use></svg>
            </a>
            <a href="#" title="消息">
                <svg class="icon" aria-hidden="true"><use href="#icon-bell"></use></svg>
                <span class="badge">3</span>
            </a>
            <div class="user-menu">
                <div class="avatar"><?php echo h(mb_substr($admin['username'] ?? 'A', 0, 1)); ?></div>
                <span><?php echo h($admin['username'] ?? '管理员'); ?></span>
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
                    <?php if (($sub['url'] ?? '') === 'admin/admin' && ($admin['role'] ?? '') !== 'super') continue; ?>
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
            <?php echo h(site_config('copyright', '鲸商城 Pro v1.0.0')); ?><?php if (site_config('icp')): ?> | <?php echo h(site_config('icp')); ?><?php endif; ?> | 客服：<?php echo h(site_config('contact') ?: '-'); ?>
        </footer>
    </div>

    <script src="/static/js/app.js"></script>
</body>
</html>
