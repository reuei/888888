<?php
$admin = session('admin_user') ?? [];
$currentPath = parse_url(trim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH);
$currentPath = preg_replace('#^public/#', '', $currentPath);

$menu = [
    ['name' => '仪表盘', 'icon' => '◇', 'url' => 'admin/dashboard'],
    ['name' => '用户管理', 'icon' => '○', 'url' => 'admin/user'],
    ['name' => '授权产品', 'icon' => '□', 'url' => 'admin/product'],
    ['name' => '订单管理', 'icon' => '≡', 'url' => 'admin/order'],
    ['name' => '授权码管理', 'icon' => '◎', 'url' => 'admin/license'],
    ['name' => '插件市场', 'icon' => '◈', 'url' => 'admin/plugin'],
    ['name' => '版本更新包', 'icon' => '⇧', 'url' => 'admin/version'],
    ['name' => '系统设置', 'icon' => '⚙', 'url' => 'admin/setting'],
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? '后台'); ?> - <?php echo h(site_config('site_name')); ?></title>
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
            top: 0; left: 0; right: 0;
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
        .sidebar {
            width: 220px;
            background: #FFFFFF;
            border-right: 1px solid #E2E8F0;
            position: fixed;
            top: 56px; bottom: 0; left: 0;
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
        }
        .menu-link:hover { background: #F1F5F9; color: #2563EB; }
        .menu-link.active { background: #EFF6FF; color: #2563EB; border-right: 3px solid #2563EB; }
        .menu-icon { width: 24px; text-align: center; margin-right: 12px; font-size: 16px; }
        .sidebar.collapsed .menu-text { display: none; }
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
        .search-bar input, .search-bar select {
            padding: 8px 12px;
            border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;
        }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 13px; color: #64748B; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 10px 12px;
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
            <div class="logo"><?php echo h(site_config('site_name')); ?> · 后台</div>
        </div>
        <div class="topbar-right">
            <span><?php echo h($admin['username'] ?? '管理员'); ?></span>
            <a href="<?php echo url('/'); ?>" target="_blank">前台</a>
            <a href="<?php echo url('admin/admin/logout'); ?>">退出</a>
        </div>
    </div>

    <div class="sidebar" id="sidebar">
        <ul class="menu">
            <?php foreach ($menu as $item): ?>
            <li class="menu-item">
                <a href="<?php echo url($item['url']); ?>" class="menu-link <?php echo strpos($currentPath, $item['url']) === 0 ? 'active' : ''; ?>">
                    <span class="menu-icon"><?php echo $item['icon']; ?></span>
                    <span class="menu-text"><?php echo $item['name']; ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="main">
        <div class="content">
            <?php echo $__content__ ?? ''; ?>
        </div>
        <div class="footer">
            <?php echo h(site_config('copyright', 'QEEFG v1.0.0')); ?>
        </div>
    </div>

    <script>
        document.getElementById('menuToggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
    </script>
</body>
</html>
