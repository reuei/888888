<?php
if (!defined('IN_ADMIN')) {
    define('IN_ADMIN', true);
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/icons.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: login.php');
    exit;
}

$currentUser = currentUser();
$activeMenu = $activeMenu ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?>管理后台 - 清廉在线</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #f0f2f5; font-family: "Microsoft YaHei", "PingFang SC", sans-serif; }
        .admin-layout { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 230px; background: linear-gradient(180deg, #001529 0%, #002140 100%); color: #fff; flex-shrink: 0; position: sticky; top: 0; height: 100vh; overflow-y: auto; box-shadow: 2px 0 8px rgba(0,0,0,0.15); }
        .admin-logo { padding: 22px 20px; background: linear-gradient(135deg, #b80000, #8b0000); text-align: center; font-size: 17px; font-weight: 700; letter-spacing: 2px; position: relative; }
        .admin-logo::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, #d4a017, #f0c040, #d4a017); }
        .admin-menu { padding: 8px 0; }
        .admin-menu a { display: flex; align-items: center; gap: 8px; padding: 12px 24px; color: rgba(255,255,255,0.65); text-decoration: none; font-size: 14px; transition: all 0.3s; border-left: 3px solid transparent; }
        .admin-menu a:hover { background: rgba(255,255,255,0.06); color: #fff; border-left-color: #d4a017; }
        .admin-menu a.active { background: linear-gradient(90deg, rgba(184,0,0,0.3), transparent); color: #fff; border-left-color: #b80000; }
        .admin-menu .menu-group { color: rgba(255,255,255,0.35); font-size: 11px; padding: 18px 24px 8px; text-transform: uppercase; letter-spacing: 1px; }
        .admin-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .admin-header { background: #fff; padding: 0 28px; height: 60px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 4px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100; }
        .admin-header h2 { font-size: 19px; font-weight: 600; color: #1a1a2e; }
        .admin-header-right { display: flex; align-items: center; gap: 16px; font-size: 13px; }
        .admin-header-right span { color: #5f6368; }
        .admin-header-right a { color: #5f6368; text-decoration: none; padding: 6px 14px; border-radius: 6px; transition: all 0.3s; }
        .admin-header-right a:hover { color: #b80000; background: #fff5f5; }
        .admin-content { flex: 1; padding: 24px; overflow-y: auto; }
        .admin-card { background: #fff; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: box-shadow 0.3s; }
        .admin-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .admin-card h3 { font-size: 16px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #f0f2f5; display: flex; align-items: center; gap: 8px; }
        .admin-card h3::before { content: ''; width: 4px; height: 18px; background: linear-gradient(to bottom, #b80000, #d4a017); border-radius: 2px; }

        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 22px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: all 0.3s; position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(to bottom, #b80000, #d4a017); }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
        .stat-card .stat-num { font-size: 30px; font-weight: 800; background: linear-gradient(135deg, #b80000, #d4a017); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .stat-card .stat-label { font-size: 13px; color: #9aa0a6; margin-top: 6px; }

        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #f0f2f5; font-size: 13px; }
        table th { background: #fafbfc; font-weight: 600; color: #5f6368; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        table tr { transition: all 0.2s; }
        table tr:hover td { background: #fafbfc; }
        .btn-small { display: inline-flex; align-items: center; padding: 5px 12px; font-size: 12px; border-radius: 6px; text-decoration: none; transition: all 0.3s; font-weight: 500; }
        .btn-primary { background: linear-gradient(135deg, #b80000, #8b0000); color: #fff; box-shadow: 0 2px 6px rgba(184,0,0,0.2); }
        .btn-primary:hover { background: linear-gradient(135deg, #d63333, #a80000); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(184,0,0,0.3); }
        .btn-default { background: #fff; color: #5f6368; border: 1px solid #dadce0; }
        .btn-default:hover { border-color: #b80000; color: #b80000; background: #fff5f5; }
        .btn-danger { background: #ff4d4f; color: #fff; }
        .btn-danger:hover { background: #d9363e; color: #fff; }

        .form-row { display: flex; gap: 20px; margin-bottom: 16px; }
        .form-item { flex: 1; }
        .form-item label { display: block; margin-bottom: 7px; font-size: 13px; color: #5f6368; font-weight: 500; }
        .form-item input, .form-item select, .form-item textarea { width: 100%; padding: 9px 12px; border: 2px solid #e8eaed; border-radius: 8px; font-size: 13px; transition: all 0.3s; background: #f8f9fa; }
        .form-item input:focus, .form-item select:focus, .form-item textarea:focus { outline: none; border-color: #b80000; background: #fff; box-shadow: 0 0 0 3px rgba(184,0,0,0.08); }
        .form-item textarea { resize: vertical; min-height: 80px; }

        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #f6ffed; color: #52c41a; border: 1px solid #b7eb8f; }
        .alert-error { background: #fff1f0; color: #f5222d; border: 1px solid #ffa39e; }

        .pagination { margin-top: 16px; text-align: right; }
        .pagination a, .pagination .current { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; margin-left: 4px; border: 1px solid #dadce0; border-radius: 6px; font-size: 13px; text-decoration: none; color: #5f6368; transition: all 0.3s; }
        .pagination a:hover { border-color: #b80000; color: #b80000; }
        .pagination .current { background: linear-gradient(135deg, #b80000, #8b0000); color: #fff; border-color: #b80000; }

        .badge { display: inline-flex; align-items: center; padding: 3px 10px; font-size: 11px; border-radius: 12px; font-weight: 500; }
        .badge-success { background: #f6ffed; color: #52c41a; border: 1px solid #b7eb8f; }
        .badge-warning { background: #fffbe6; color: #d48806; border: 1px solid #ffe58f; }
        .badge-danger { background: #fff1f0; color: #f5222d; border: 1px solid #ffa39e; }
        .badge-info { background: #e6f7ff; color: #1890ff; border: 1px solid #91d5ff; }

        /* 后台手机端汉堡菜单 */
        .admin-mobile-toggle {
            display: none;
            background: transparent;
            border: none;
            color: #1a1a2e;
            font-size: 22px;
            cursor: pointer;
            padding: 8px 10px;
            margin-right: 12px;
            border-radius: 4px;
        }
        .admin-mobile-toggle:hover { background: rgba(184,0,0,0.08); color: #b80000; }
        .admin-mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 90;
        }
        .admin-mobile-overlay.active { display: block; }
        .admin-sidebar.mobile-open {
            transform: translateX(0);
        }

        @media (max-width: 768px) {
            .admin-mobile-toggle { display: inline-flex; align-items: center; justify-content: center; }
            .admin-sidebar {
                position: fixed;
                top: 0; left: 0;
                height: 100vh;
                width: 230px;
                z-index: 100;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .admin-main { margin-left: 0; }
            .admin-menu a { padding: 12px 24px; justify-content: flex-start; }
            .admin-menu a span { display: inline; }
            .admin-menu .menu-group { display: block; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
            .form-row { flex-direction: column; gap: 12px; }
            .admin-header-right a { padding: 4px 8px; font-size: 12px; }
            .admin-header-right span { display: none; }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo">清廉在线</div>
            <nav class="admin-menu">
                <div class="menu-group">概览</div>
                <a href="index.php" class="<?php echo $activeMenu == 'dashboard' ? 'active' : ''; ?>"><?php echo icon('dashboard'); ?> 仪表盘</a>

                <div class="menu-group">内容管理</div>
                <a href="articles.php" class="<?php echo $activeMenu == 'articles' ? 'active' : ''; ?>"><?php echo icon('article'); ?> 文章管理</a>
                <a href="categories.php" class="<?php echo $activeMenu == 'categories' ? 'active' : ''; ?>"><?php echo icon('folder'); ?> 栏目管理</a>
                <a href="pages.php" class="<?php echo $activeMenu == 'pages' ? 'active' : ''; ?>"><?php echo icon('page'); ?> 单页管理</a>
                <a href="slides.php" class="<?php echo $activeMenu == 'slides' ? 'active' : ''; ?>"><?php echo icon('image'); ?> 轮播图管理</a>

                <div class="menu-group">用户与互动</div>
                <a href="users.php" class="<?php echo $activeMenu == 'users' ? 'active' : ''; ?>"><?php echo icon('user'); ?> 用户管理</a>
                <a href="messages.php" class="<?php echo $activeMenu == 'messages' ? 'active' : ''; ?>"><?php echo icon('message'); ?> 留言举报</a>

                <div class="menu-group">系统设置</div>
                <a href="settings.php" class="<?php echo $activeMenu == 'settings' ? 'active' : ''; ?>"><?php echo icon('setting'); ?> 系统设置</a>
                <?php if (isSuperAdmin()): ?>
                <a href="admins.php" class="<?php echo $activeMenu == 'admins' ? 'active' : ''; ?>"><?php echo icon('admin'); ?> 管理员管理</a>
                <?php endif; ?>
            </nav>
        </aside>
        <div class="admin-main">
            <div class="admin-mobile-overlay" id="adminMobileOverlay"></div>
            <header class="admin-header">
                <div style="display:flex; align-items:center;">
                    <button class="admin-mobile-toggle" id="adminMobileToggle"><?php echo icon('menu'); ?></button>
                    <h2><?php echo isset($pageTitle) ? e($pageTitle) : '管理后台'; ?></h2>
                </div>
                <div class="admin-header-right">
                    <span>欢迎，<?php echo e($currentUser['nickname'] ?: $currentUser['username']); ?></span>
                    <a href="../index.php" target="_blank">前台首页</a>
                    <a href="../logout.php">退出</a>
                </div>
            </header>
            <div class="admin-content">
            <script>
            (function(){
                var toggle = document.getElementById('adminMobileToggle');
                var overlay = document.getElementById('adminMobileOverlay');
                var sidebar = document.querySelector('.admin-sidebar');
                if(toggle && sidebar){
                    toggle.onclick = function(){
                        sidebar.classList.toggle('mobile-open');
                        if(overlay) overlay.classList.toggle('active');
                    };
                }
                if(overlay && sidebar){
                    overlay.onclick = function(){
                        sidebar.classList.remove('mobile-open');
                        overlay.classList.remove('active');
                    };
                }
            })();
            </script>

