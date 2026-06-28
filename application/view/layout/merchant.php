<?php
$merchant = session('merchant_user') ?? [];
$currentPath = trim($_SERVER['REQUEST_URI'], '/');
$currentPath = preg_replace('#^public/#', '', $currentPath);

$menu = [
    ['name' => '仪表盘', 'icon' => '◇', 'url' => 'merchant/dashboard', 'children' => []],
    ['name' => '商品管理', 'icon' => '□', 'url' => '#', 'children' => [
        ['name' => '商品列表', 'url' => 'merchant/goods'],
        ['name' => '新增商品', 'url' => 'merchant/goods/create'],
        ['name' => '卡密管理', 'url' => 'merchant/goods/card'],
        ['name' => '批量导入', 'url' => 'merchant/goods/import'],
        ['name' => '货源广场', 'url' => 'merchant/goods/source'],
        ['name' => '代理商品', 'url' => 'merchant/goods/agent'],
    ]],
    ['name' => '订单管理', 'icon' => '≡', 'url' => '#', 'children' => [
        ['name' => '订单列表', 'url' => 'merchant/order'],
        ['name' => '投诉处理', 'url' => 'merchant/order/complaint'],
        ['name' => '查单', 'url' => 'merchant/order/query'],
    ]],
    ['name' => '客服管理', 'icon' => '☎', 'url' => '#', 'children' => [
        ['name' => '咨询列表', 'url' => 'merchant/chat'],
        ['name' => '回复会话', 'url' => 'merchant/chat/session'],
    ]],
    ['name' => '资金管理', 'icon' => '¥', 'url' => '#', 'children' => [
        ['name' => '资金概览', 'url' => 'merchant/finance'],
        ['name' => '资金流水', 'url' => 'merchant/finance/flow'],
        ['name' => '结算提现', 'url' => 'merchant/finance/settle'],
    ]],
    ['name' => '店铺设置', 'icon' => '⚙', 'url' => '#', 'children' => [
        ['name' => '店铺信息', 'url' => 'merchant/setting/shop'],
        ['name' => '第三方登录', 'url' => 'merchant/setting/oauth'],
        ['name' => '实名认证', 'url' => 'merchant/setting/auth'],
        ['name' => '自定义支付', 'url' => 'merchant/setting/payment'],
        ['name' => '引导页', 'url' => 'merchant/setting/guide'],
        ['name' => '子域名', 'url' => 'merchant/setting/domain'],
    ]],
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? '后台'); ?> - 鲸商城 Pro B端</title>
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
        .logo { font-size: 18px; font-weight: 600; color: #10B981; }
        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .topbar-right a { color: #475569; font-size: 14px; }
        .topbar-right a:hover { color: #10B981; }
        .user-menu { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: #10B981; color: #fff; display: flex; align-items: center; justify-content: center;
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
        .menu-link:hover { background: #F1F5F9; color: #10B981; }
        .menu-link.active { background: #ECFDF5; color: #10B981; border-right: 3px solid #10B981; }
        .menu-icon { width: 24px; text-align: center; margin-right: 12px; font-size: 16px; }
        .sidebar.collapsed .menu-text { display: none; }
        .sidebar.collapsed .menu-icon { margin-right: 0; }
        .submenu { display: none; background: #F8FAFC; }
        .submenu.show { display: block; }
        .submenu a {
            display: block; padding: 10px 20px 10px 56px;
            color: #64748B; font-size: 13px;
        }
        .submenu a:hover, .submenu a.active { color: #10B981; background: #ECFDF5; }
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
            background: #10B981; color: #fff;
            border: 1px solid #10B981; border-radius: 6px;
            font-size: 14px; cursor: pointer; text-decoration: none;
        }
        .btn-outline { background: #FFFFFF; color: #10B981; }
        .btn-primary { background: #2563EB; border-color: #2563EB; }
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
            <div class="logo">鲸商城 Pro · B端</div>
        </div>
        <div class="topbar-right">
            <a href="#">消息</a>
            <a href="#">帮助</a>
            <div class="user-menu">
                <div class="avatar"><?php echo mb_substr($merchant['shop_name'] ?? 'M', 0, 1); ?></div>
                <span><?php echo h($merchant['shop_name'] ?? '商户'); ?></span>
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
            鲸商城 Pro v1.0.0 | 操作手册 | 客服入口
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
