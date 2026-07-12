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
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?>管理后台 - 清廉在线</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #f0f2f5; }
        .admin-layout { display: flex; min-height: 100vh; }
        .admin-sidebar { width: 220px; background: #001529; color: #fff; flex-shrink: 0; }
        .admin-logo { padding: 20px; background: #c20000; text-align: center; font-size: 16px; font-weight: bold; }
        .admin-menu { padding: 10px 0; }
        .admin-menu a { display: block; padding: 12px 25px; color: rgba(255,255,255,0.75); text-decoration: none; font-size: 14px; }
        .admin-menu a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .admin-menu a.active { background: #c20000; color: #fff; }
        .admin-menu .menu-group { color: rgba(255,255,255,0.45); font-size: 12px; padding: 15px 25px 8px; text-transform: uppercase; }
        .admin-main { flex: 1; display: flex; flex-direction: column; }
        .admin-header { background: #fff; padding: 0 25px; height: 55px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .admin-header h2 { font-size: 18px; font-weight: 500; }
        .admin-header-right { display: flex; align-items: center; gap: 15px; font-size: 13px; }
        .admin-header-right a { color: #666; text-decoration: none; }
        .admin-header-right a:hover { color: #c20000; }
        .admin-content { flex: 1; padding: 20px; overflow-y: auto; }
        .admin-card { background: #fff; border-radius: 6px; padding: 20px; margin-bottom: 20px; }
        .admin-card h3 { font-size: 16px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #f0f0f0; }

        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 20px; }
        .stat-card { background: #fff; border-radius: 6px; padding: 20px; }
        .stat-card .stat-num { font-size: 28px; font-weight: bold; color: #c20000; }
        .stat-card .stat-label { font-size: 13px; color: #999; margin-top: 5px; }

        table { width: 100%; border-collapse: collapse; }
        table th, table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        table th { background: #fafafa; font-weight: 600; color: #555; }
        table tr:hover td { background: #fafafa; }
        .btn-small { display: inline-block; padding: 4px 12px; font-size: 12px; border-radius: 3px; text-decoration: none; }
        .btn-primary { background: #c20000; color: #fff; }
        .btn-primary:hover { background: #a80000; color: #fff; }
        .btn-default { background: #fff; color: #666; border: 1px solid #ddd; }
        .btn-default:hover { border-color: #c20000; color: #c20000; }
        .btn-danger { background: #ff4d4f; color: #fff; }
        .btn-danger:hover { background: #d9363e; color: #fff; }

        .form-row { display: flex; gap: 20px; margin-bottom: 15px; }
        .form-item { flex: 1; }
        .form-item label { display: block; margin-bottom: 6px; font-size: 13px; color: #555; }
        .form-item input, .form-item select, .form-item textarea { width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; }
        .form-item input:focus, .form-item select:focus, .form-item textarea:focus { outline: none; border-color: #c20000; }
        .form-item textarea { resize: vertical; min-height: 80px; }

        .alert { padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; font-size: 13px; }
        .alert-success { background: #f6ffed; color: #52c41a; border: 1px solid #b7eb8f; }
        .alert-error { background: #fff1f0; color: #f5222d; border: 1px solid #ffa39e; }

        .pagination { margin-top: 15px; text-align: right; }
        .pagination a, .pagination .current { display: inline-block; padding: 4px 10px; margin-left: 5px; border: 1px solid #ddd; border-radius: 3px; font-size: 13px; text-decoration: none; color: #666; }
        .pagination a:hover { border-color: #c20000; color: #c20000; }
        .pagination .current { background: #c20000; color: #fff; border-color: #c20000; }

        .badge { display: inline-block; padding: 2px 8px; font-size: 11px; border-radius: 10px; }
        .badge-success { background: #f6ffed; color: #52c41a; border: 1px solid #b7eb8f; }
        .badge-warning { background: #fffbe6; color: #d4b106; border: 1px solid #ffe58f; }
        .badge-danger { background: #fff1f0; color: #f5222d; border: 1px solid #ffa39e; }
        .badge-info { background: #e6f7ff; color: #1890ff; border: 1px solid #91d5ff; }

        @media (max-width: 768px) {
            .admin-sidebar { width: 60px; }
            .admin-logo { font-size: 12px; padding: 15px 5px; }
            .admin-menu a { padding: 12px 10px; text-align: center; font-size: 12px; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-logo">清廉在线</div>
            <nav class="admin-menu">
                <div class="menu-group">概览</div>
                <a href="index.php" class="<?php echo $activeMenu == 'dashboard' ? 'active' : ''; ?>">📊 仪表盘</a>

                <div class="menu-group">内容管理</div>
                <a href="articles.php" class="<?php echo $activeMenu == 'articles' ? 'active' : ''; ?>">📝 文章管理</a>
                <a href="categories.php" class="<?php echo $activeMenu == 'categories' ? 'active' : ''; ?>">📁 栏目管理</a>
                <a href="pages.php" class="<?php echo $activeMenu == 'pages' ? 'active' : ''; ?>">📄 单页管理</a>

                <div class="menu-group">用户与互动</div>
                <a href="users.php" class="<?php echo $activeMenu == 'users' ? 'active' : ''; ?>">👥 用户管理</a>
                <a href="messages.php" class="<?php echo $activeMenu == 'messages' ? 'active' : ''; ?>">💬 留言举报</a>

                <div class="menu-group">系统设置</div>
                <a href="settings.php" class="<?php echo $activeMenu == 'settings' ? 'active' : ''; ?>">⚙️ 系统设置</a>
                <?php if (isSuperAdmin()): ?>
                <a href="admins.php" class="<?php echo $activeMenu == 'admins' ? 'active' : ''; ?>">🔐 管理员管理</a>
                <?php endif; ?>
            </nav>
        </aside>
        <div class="admin-main">
            <header class="admin-header">
                <h2><?php echo isset($pageTitle) ? e($pageTitle) : '管理后台'; ?></h2>
                <div class="admin-header-right">
                    <span>欢迎，<?php echo e($currentUser['nickname'] ?: $currentUser['username']); ?></span>
                    <a href="../index.php" target="_blank">前台首页</a>
                    <a href="../logout.php">退出</a>
                </div>
            </header>
            <div class="admin-content">
