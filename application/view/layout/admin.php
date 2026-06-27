<?php
$admin = session('admin_user') ?? [];
$currentPath = trim($_SERVER['REQUEST_URI'], '/');
$currentPath = preg_replace('#^public/#', '', $currentPath);

$menu = [
    ['name' => '仪表盘', 'icon' => '◇', 'url' => 'admin/dashboard', 'children' => []],
    ['name' => '分站管理', 'icon' => '◈', 'url' => '#', 'children' => [
        ['name' => '分站列表', 'url' => 'admin/subsite'],
        ['name' => '新建分站', 'url' => 'admin/subsite/create'],
        ['name' => '分站监控', 'url' => 'admin/subsite/monitor'],
    ]],
    ['name' => '商户管理', 'icon' => '◎', 'url' => '#', 'children' => [
        ['name' => '商户列表', 'url' => 'admin/merchant'],
        ['name' => '入驻审核', 'url' => 'admin/merchant/audit'],
        ['name' => '邀请码', 'url' => 'admin/merchant/invite'],
    ]],
    ['name' => '商品管理', 'icon' => '□', 'url' => '#', 'children' => [
        ['name' => '全平台商品', 'url' => 'admin/goods'],
        ['name' => '分类管理', 'url' => 'admin/category'],
        ['name' => '禁售目录', 'url' => 'admin/goods/ban'],
    ]],
    ['name' => '订单管理', 'icon' => '≡', 'url' => '#', 'children' => [
        ['name' => '订单列表', 'url' => 'admin/order'],
        ['name' => '投诉管理', 'url' => 'admin/order/complaint'],
    ]],
    ['name' => '用户管理', 'icon' => '○', 'url' => '#', 'children' => [
        ['name' => '用户列表', 'url' => 'admin/user'],
        ['name' => '等级分组', 'url' => 'admin/user/group'],
    ]],
    ['name' => '代理分销', 'icon' => '⧉', 'url' => '#', 'children' => [
        ['name' => '代理商品', 'url' => 'admin/agent/goods'],
        ['name' => '代理树', 'url' => 'admin/agent/tree'],
        ['name' => '佣金记录', 'url' => 'admin/agent/commission'],
        ['name' => '佣金结算', 'url' => 'admin/agent/settle'],
    ]],
    ['name' => '财务结算', 'icon' => '¥', 'url' => '#', 'children' => [
        ['name' => '资金流水', 'url' => 'admin/finance/flow'],
        ['name' => '费率分组', 'url' => 'admin/finance/rate'],
        ['name' => '结算打款', 'url' => 'admin/finance/settle'],
    ]],
    ['name' => '支付网关', 'icon' => '₿', 'url' => '#', 'children' => [
        ['name' => '渠道配置', 'url' => 'admin/payment/channel'],
        ['name' => '风控策略', 'url' => 'admin/payment/risk'],
    ]],
    ['name' => '模板前端', 'icon' => '◐', 'url' => '#', 'children' => [
        ['name' => '首页模板', 'url' => 'admin/template/home'],
        ['name' => '购卡页模板', 'url' => 'admin/template/goods'],
    ]],
    ['name' => '文章公告', 'icon' => '✎', 'url' => '#', 'children' => [
        ['name' => '公告列表', 'url' => 'admin/article'],
        ['name' => '发布公告', 'url' => 'admin/article/create'],
    ]],
    ['name' => '广告位', 'icon' => '▣', 'url' => 'admin/ad', 'children' => []],
    ['name' => '优惠券', 'icon' => '✂', 'url' => 'admin/coupon', 'children' => []],
    ['name' => '数据统计', 'icon' => '▦', 'url' => '#', 'children' => [
        ['name' => '经营报表', 'url' => 'admin/stat/report'],
        ['name' => '操作日志', 'url' => 'admin/stat/log'],
    ]],
    ['name' => '系统设置', 'icon' => '⚙', 'url' => '#', 'children' => [
        ['name' => '站点基础', 'url' => 'admin/setting'],
        ['name' => '邮件系统', 'url' => 'admin/setting/email'],
        ['name' => '短信通知', 'url' => 'admin/setting/sms'],
        ['name' => '文件存储', 'url' => 'admin/setting/storage'],
        ['name' => '二次认证', 'url' => 'admin/setting/security'],
    ]]
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? '后台'); ?> - <?php echo h(site_config('site_name', '鲸商城 Pro')); ?> S端</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #F8FAFC;
            color: #1F2937;
            font-size: 14px;
            line-height: 1.5;
        }
        a { text-decoration: none; color: inherit; }
        ul, ol { list-style: none; }
        .topbar {
            height: 56px;
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }
        .topbar-left { display: flex; align-items: center; }
        .menu-toggle {
            width: 32px; height: 32px; border: 1px solid #E2E8F0; border-radius: 6px;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            margin-right: 12px; background: #fff;
        }
        .logo { font-size: 18px; font-weight: 600; color: #2563EB; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-right a { color: #475569; font-size: 14px; }
        .topbar-right a:hover { color: #2563EB; }
        .user-menu { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: #2563EB; color: #fff; display: flex; align-items: center; justify-content: center;
            font-size: 12px;
        }
        .sidebar {
            width: 220px;
            background: #FFFFFF;
            border-right: 1px solid #E2E8F0;
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            z-index: 99;
            transition: width 0.2s;
        }
        .sidebar.collapsed { width: 64px; }
        .menu { padding: 12px 0; }
        .menu-item { position: relative; }
        .menu-link {
            display: flex; align-items: center;
            padding: 0 20px; height: 48px;
            color: #475569; font-size: 14px;
            cursor: pointer;
        }
        .menu-link:hover { background: #F1F5F9; color: #2563EB; }
        .menu-link.active { background: #EFF6FF; color: #2563EB; border-right: 3px solid #2563EB; }
        .menu-icon { width: 24px; text-align: center; margin-right: 12px; font-size: 16px; }
        .sidebar.collapsed .menu-text { display: none; }
        .sidebar.collapsed .menu-icon { margin-right: 0; }
        .submenu { display: none; background: #F8FAFC; }
        .submenu.show { display: block; }
        .submenu a {
            display: block; padding: 10px 20px 10px 56px;
            color: #64748B; font-size: 13px;
        }
        .submenu a:hover, .submenu a.active { color: #2563EB; background: #EFF6FF; }
        .arrow { margin-left: auto; font-size: 12px; transition: transform 0.2s; }
        .arrow.rotate { transform: rotate(90deg); }
        .main {
            margin-left: 220px;
            margin-top: 56px;
            min-height: calc(100vh - 56px);
            transition: margin-left 0.2s;
        }
        .sidebar.collapsed ~ .main { margin-left: 64px; }
        .content { padding: 24px; }
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .page-header h2 { font-size: 18px; font-weight: 600; }
        .breadcrumb { color: #64748B; font-size: 13px; margin-bottom: 8px; }
        .card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
        }
        .btn {
            display: inline-block; padding: 8px 16px;
            background: #2563EB; color: #fff;
            border: 1px solid #2563EB; border-radius: 6px;
            font-size: 14px; cursor: pointer; text-decoration: none;
        }
        .btn-outline { background: #FFFFFF; color: #2563EB; }
        .btn-success { background: #10B981; border-color: #10B981; }
        .btn-warning { background: #F59E0B; border-color: #F59E0B; }
        .btn-danger { background: #EF4444; border-color: #EF4444; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid #E2E8F0; }
        th { background: #F8FAFC; font-weight: 600; color: #475569; }
        tr:hover { background: #F8FAFC; }
        .tag {
            display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px;
        }
        .tag-blue { background: #EFF6FF; color: #2563EB; }
        .tag-green { background: #ECFDF5; color: #059669; }
        .tag-orange { background: #FFFBEB; color: #D97706; }
        .tag-red { background: #FEF2F2; color: #DC2626; }
        .search-bar {
            display: flex; gap: 12px; margin-bottom: 16px;
        }
        .search-bar input {
            flex: 1; max-width: 320px; padding: 8px 12px;
            border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;
        }
        .footer {
            text-align: center; padding: 16px;
            color: #64748B; font-size: 12px; border-top: 1px solid #E2E8F0;
        }
        @media (max-width: 768px) {
            .sidebar { width: 64px; }
            .main { margin-left: 64px; }
            .menu-text { display: none; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <div class="menu-toggle" id="menuToggle">☰</div>
            <div class="logo"><?php echo h(site_config('site_name', '鲸商城 Pro')); ?> · S端</div>
        </div>
        <div class="topbar-right">
            <a href="#">消息</a>
            <a href="#">全屏</a>
            <div class="user-menu">
                <div class="avatar"><?php echo mb_substr($admin['username'] ?? 'A', 0, 1); ?></div>
                <span><?php echo h($admin['username'] ?? '管理员'); ?></span>
                <a href="<?php echo url('login/logout'); ?>">退出</a>
            </div>
        </div>
    </div>

    <div class="sidebar" id="sidebar">
        <ul class="menu">
            <?php foreach ($menu as $item): ?>
            <li class="menu-item">
                <a href="<?php echo $item['children'] ? '#' : url($item['url']); ?>" class="menu-link <?php echo strpos($currentPath, $item['url']) === 0 ? 'active' : ''; ?>" data-has-submenu="<?php echo $item['children'] ? '1' : '0'; ?>">
                    <span class="menu-icon"><?php echo $item['icon']; ?></span>
                    <span class="menu-text"><?php echo $item['name']; ?></span>
                    <?php if ($item['children']): ?>
                    <span class="arrow">›</span>
                    <?php endif; ?>
                </a>
                <?php if ($item['children']): ?>
                <div class="submenu">
                    <?php foreach ($item['children'] as $sub): ?>
                    <a href="<?php echo url($sub['url']); ?>" class="<?php echo strpos($currentPath, $sub['url']) === 0 ? 'active' : ''; ?>"><?php echo $sub['name']; ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="main">
        <div class="content">
            <?php echo $__content__ ?? ''; ?>
        </div>
        <div class="footer">
            <?php echo h(site_config('copyright', '鲸商城 Pro v1.0.0')); ?> | <?php echo h(site_config('icp') ?: ''); ?><?php echo site_config('icp') ? ' | ' : ''; ?>客服：<?php echo h(site_config('contact') ?: '-'); ?>
        </div>
    </div>

    <script>
        document.getElementById('menuToggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });

        document.querySelectorAll('.menu-link[data-has-submenu="1"]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const submenu = link.nextElementSibling;
                submenu.classList.toggle('show');
                link.querySelector('.arrow').classList.toggle('rotate');
            });
        });
    </script>
</body>
</html>
