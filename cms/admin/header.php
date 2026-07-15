<?php
if (!defined('IN_ADMIN')) {
    define('IN_ADMIN', true);
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$currentUser = currentUser();
$activeMenu = $activeMenu ?? 'dashboard';

// 后台管理菜单
$adminMenu = [
    ['group' => '概览'],
    ['key' => 'dashboard', 'name' => '仪表盘', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>', 'url' => 'index.php'],
    ['group' => '内容管理'],
    ['key' => 'articles', 'name' => '文章管理', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>', 'url' => 'articles.php'],
    ['key' => 'categories', 'name' => '栏目管理', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>', 'url' => 'categories.php'],
    ['key' => 'pages', 'name' => '单页管理', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>', 'url' => 'pages.php'],
    ['key' => 'slides', 'name' => '轮播图', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2"/><line x1="2" y1="8" x2="22" y2="8"/><line x1="9" y1="22" x2="9" y2="8"/></svg>', 'url' => 'slides.php'],
    ['group' => '用户互动'],
    ['key' => 'users', 'name' => '用户管理', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>', 'url' => 'users.php'],
    ['key' => 'messages', 'name' => '留言举报', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z"/></svg>', 'url' => 'messages.php'],
    ['group' => '系统设置'],
    ['key' => 'settings', 'name' => '系统设置', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>', 'url' => 'settings.php'],
];
if (isSuperAdmin()) {
    $adminMenu[] = ['key' => 'admins', 'name' => '管理员', 'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', 'url' => 'admins.php'];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' · ' : ''; ?>管理后台 - 人民检察</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: var(--pk-gray-100); }
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
        .stat-card { background: var(--pk-paper); padding: 20px; border: 1px solid var(--pk-gray-200); border-radius: var(--pk-radius-lg); position: relative; overflow: hidden; transition: all 0.2s; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 3px; height: 100%; background: var(--pk-blue); }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--pk-shadow); }
        .stat-card .stat-num { font-size: 28px; font-weight: 700; color: var(--pk-blue); }
        .stat-card .stat-label { font-size: 12px; color: var(--pk-gray-500); margin-top: 6px; letter-spacing: 1px; }
        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid var(--pk-gray-100); font-size: 13px; }
        table th { background: var(--pk-gray-50); font-weight: 600; color: var(--pk-gray-700); }
        table tr:hover td { background: var(--pk-gray-50); }
        .btn-primary { background: var(--pk-blue); color: #fff; padding: 6px 16px; border-radius: var(--pk-radius); text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
        .btn-primary:hover { background: var(--pk-blue-mid); color: #fff; }
        .btn-default { background: #fff; color: var(--pk-gray-700); border: 1px solid var(--pk-gray-300); padding: 5px 14px; border-radius: var(--pk-radius); text-decoration: none; display: inline-flex; align-items: center; }
        .btn-default:hover { border-color: var(--pk-blue); color: var(--pk-blue); }
        .btn-danger { background: var(--pk-error); color: #fff; padding: 5px 12px; border-radius: var(--pk-radius); text-decoration: none; }
        .btn-danger:hover { background: #a02b3d; color: #fff; }
        .alert-success { background: #f0f9f3; color: var(--pk-success); border-left: 3px solid var(--pk-success); padding: 10px 14px; border-radius: var(--pk-radius); margin-bottom: 18px; font-size: 13px; }
        .alert-error { background: #fdf3f5; color: var(--pk-error); border-left: 3px solid var(--pk-error); padding: 10px 14px; border-radius: var(--pk-radius); margin-bottom: 18px; font-size: 13px; }
        .pagination { margin-top: 16px; text-align: right; }
        .pagination a, .pagination .current { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; margin-left: 4px; border: 1px solid var(--pk-gray-200); border-radius: var(--pk-radius); font-size: 13px; text-decoration: none; color: var(--pk-gray-600); }
        .pagination a:hover { border-color: var(--pk-blue); color: var(--pk-blue); }
        .pagination .current { background: var(--pk-blue); color: #fff; border-color: var(--pk-blue); }
        .badge { display: inline-block; padding: 2px 8px; font-size: 11px; border-radius: 2px; }
        .badge-success { background: #f0f9f3; color: var(--pk-success); }
        .badge-warning { background: #fef8e7; color: var(--pk-warning); }
        .badge-danger { background: #fdf3f5; color: var(--pk-error); }
        .badge-info { background: var(--pk-blue-pale); color: var(--pk-blue); }
        .admin-mobile-toggle {
            display: none;
            background: transparent;
            border: none;
            color: var(--pk-blue);
            font-size: 20px;
            cursor: pointer;
            padding: 8px 10px;
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .admin-mobile-toggle { display: inline-flex; align-items: center; }
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-side" id="adminSide">
            <div class="admin-side-head">
                <div style="display:flex; justify-content:center; margin-bottom:8px;">
                    <svg viewBox="0 0 100 100" width="40" height="40">
                        <path d="M50 12 L58 24 L72 22 L70 36 L82 42 L72 50 L74 64 L60 62 L50 74 L40 62 L26 64 L28 50 L18 42 L30 36 L28 22 L42 24 Z" fill="#c9a227"/>
                        <text x="50" y="58" text-anchor="middle" fill="#0a2540" font-size="18" font-weight="bold" font-family="serif">检</text>
                    </svg>
                </div>
                <h1>人民检察</h1>
                <div class="sub">ADMIN CENTER</div>
            </div>
            <ul class="admin-nav">
                <?php foreach ($adminMenu as $m): ?>
                    <?php if (isset($m['group'])): ?>
                        <li class="group"><?php echo $m['group']; ?></li>
                    <?php else: ?>
                        <li><a href="<?php echo $m['url']; ?>" class="<?php echo $activeMenu == $m['key'] ? 'on' : ''; ?>">
                            <?php echo $m['icon']; ?>
                            <?php echo $m['name']; ?>
                        </a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </aside>
        <div class="admin-main">
            <header class="admin-head">
                <div style="display:flex; align-items:center;">
                    <button class="admin-mobile-toggle" id="adminMobileToggle" aria-label="菜单">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    <h2><?php echo isset($pageTitle) ? e($pageTitle) : '管理后台'; ?></h2>
                </div>
                <div class="act">
                    <span>欢迎，<?php echo e($currentUser['nickname'] ?: $currentUser['username']); ?></span>
                    <a href="../index.php" target="_blank">前台首页</a>
                    <a href="../logout.php">退出</a>
                </div>
            </header>
            <div class="admin-body">
            <script>
            (function(){
                var t = document.getElementById('adminMobileToggle');
                var s = document.getElementById('adminSide');
                if(t && s){
                    t.onclick = function(){
                        s.classList.toggle('open');
                    };
                    document.addEventListener('click', function(e){
                        if(s.classList.contains('open') && !s.contains(e.target) && e.target !== t && !t.contains(e.target)){
                            s.classList.remove('open');
                        }
                    });
                }
            })();
            </script>