<?php
/**
 * 语云科技 - 后台管理仪表盘
 * 显示系统概览、统计数据、最近工单等
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_admin();

$config = get_config();

// 获取统计数据
$users = get_content('users') ?: [];
$tickets = get_content('tickets') ?: [];
$products = get_content('products') ?: [];
$banners = get_content('banners') ?: [];
$partners = get_content('partners') ?: [];
$staffList = get_content('staff') ?: [];

// 统计计算
$totalUsers = count($users);
$totalTickets = count($tickets);
$totalProducts = count($products);
$totalBanners = count($banners);

// 工单状态统计
$openTickets = array_filter($tickets, fn($t) => $t['status'] === 'open' || $t['status'] === 'processing');
$closedTickets = array_filter($tickets, fn($t) => $t['status'] === 'closed');
$urgentTickets = array_filter($tickets, fn($t) => $t['priority'] === 'urgent' && $t['status'] !== 'closed');

// 最近工单（取最新5条）
usort($tickets, function($a, $b) {
    return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
});
$recentTickets = array_slice($tickets, 0, 5);

// 系统信息
$phpVersion = PHP_VERSION;
$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
$mysqlStatus = extension_loaded('mysqli') || extension_loaded('pdo_mysql') ? '已安装' : '未安装';
$maxExecutionTime = ini_get('max_execution_time') . '秒';
$memoryLimit = ini_get('memory_limit');
$uploadMaxSize = ini_get('upload_max_filesize');
$postMaxSize = ini_get('post_max_size');

// 操作系统信息
$os = PHP_OS;
$diskTotalSpace = function_exists('disk_total_space') ? round(disk_total_space('/') / (1024*1024*1024), 2) : 0;
$diskFreeSpace = function_exists('disk_free_space') ? round(disk_free_space('/') / (1024*1024*1024), 2) : 0;

// 当前时间
$currentDate = date('Y年m月d日');
$currentTime = date('H:i:s');
$weekDays = ['日', '一', '二', '三', '四', '五', '六'];
$weekday = '星期' . $weekDays[date('w')];

// 管理员信息
$adminName = $_SESSION['admin_name'] ?? '管理员';
$adminEmail = $_SESSION['admin_email'] ?? '';
$loginTime = date('Y-m-d H:i:s', $_SESSION['admin_login_time'] ?? time());
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>控制面板 - 语云科技后台管理</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- 侧边栏 -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('collapsed')">
            <i class="fas fa-angle-double-left"></i>
        </div>

        <div class="sidebar-header">
            <div class="sidebar-logo">语</div>
            <div class="sidebar-title">
                <h2>语云科技</h2>
                <span>管理中心 v1.0</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">主导航</div>
                <a href="dashboard.php" class="nav-item active" data-href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>仪表盘</span>
                </a>
                <a href="settings.php" class="nav-item" data-href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>站点设置</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">内容管理</div>
                <a href="content.php" class="nav-item" data-href="content.php">
                    <i class="fas fa-th-large"></i>
                    <span>内容管理</span>
                </a>
                <a href="products.php" class="nav-item" data-href="products.php">
                    <i class="fas fa-box"></i>
                    <span>产品管理</span>
                </a>
                <a href="templates.php" class="nav-item" data-href="templates.php">
                    <i class="fas fa-palette"></i>
                    <span>模板管理</span>
                </a>
                <a href="staff.php" class="nav-item" data-href="staff.php">
                    <i class="fas fa-users"></i>
                    <span>员工管理</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">运营管理</div>
                <a href="users.php" class="nav-item" data-href="users.php">
                    <i class="fas fa-user-friends"></i>
                    <span>用户管理</span>
                </a>
                <a href="tickets.php" class="nav-item" data-href="tickets.php">
                    <i class="fas fa-ticket-alt"></i>
                    <span>工单管理</span>
                    <?php if (count($openTickets) > 0): ?>
                        <span class="badge"><?php echo count($openTickets); ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">系统</div>
                <a href="system.php" class="nav-item" data-href="system.php">
                    <i class="fas fa-server"></i>
                    <span>系统管理</span>
                </a>
                <a href="../api/auth.php?action=logout" class="nav-item" onclick="return confirm('确定要退出登录吗？');">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>退出登录</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- 移动端遮罩 -->
    <div class="sidebar-overlay" onclick="document.getElementById('sidebar').classList.remove('mobile-show')"></div>

    <!-- 顶部导航栏 -->
    <header class="header">
        <div class="header-left">
            <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.add('mobile-show'); document.querySelector('.sidebar-overlay').classList.add('show');">
                <i class="fas fa-bars"></i>
            </button>
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-separator">/</span>
                <span class="page-title">仪表盘</span>
            </div>
        </div>
        <div class="header-right">
            <a href="tickets.php" class="header-action" title="工单通知">
                <i class="fas fa-bell"></i>
                <?php if (count($openTickets) > 0): ?>
                    <span class="notification-dot"></span>
                <?php endif; ?>
            </a>
            <div class="user-dropdown">
                <div class="user-avatar"><?php echo mb_substr($adminName, 0, 1); ?></div>
                <div class="user-info">
                    <div class="name"><?php echo e($adminName); ?></div>
                    <div class="role">超级管理员</div>
                </div>
            </div>
        </div>
    </header>

    <!-- 主内容区 -->
    <main class="main-content">
        <!-- 欢迎横幅 -->
        <div style="
            background: linear-gradient(135deg, rgba(0, 102, 204, 0.15), rgba(255, 107, 0, 0.08));
            border: 1px solid rgba(0, 102, 204, 0.2);
            border-radius: 12px;
            padding: 28px 32px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        ">
            <div>
                <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 6px;">
                    欢迎回来，<?php echo e($adminName); ?>！
                </h2>
                <p style="color: var(--text-secondary); font-size: 14px;">
                    <i class="far fa-calendar-alt"></i> <?php echo $currentDate; ?> <?php echo $weekday; ?>
                    &nbsp;&nbsp;
                    <i class="far fa-clock"></i> <?php echo $currentTime; ?>
                </p>
            </div>
            <div style="text-align: right;">
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">上次登录</p>
                <p style="font-size: 14px; color: var(--text-secondary);"><?php echo $loginTime; ?></p>
            </div>
        </div>

        <!-- 统计卡片 -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h4><?php echo $totalUsers; ?></h4>
                    <p>注册用户</p>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i> 较上月 +12%
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <h4><?php echo $totalTickets; ?></h4>
                    <p>总工单数</p>
                    <div class="stat-trend <?php echo count($openTickets) > 5 ? 'up' : ''; ?>">
                        <?php echo count($openTickets); ?> 个待处理
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stat-info">
                    <h4><?php echo $totalProducts; ?></h4>
                    <p>产品服务</p>
                    <div class="stat-trend up">
                        <i class="fas fa-check-circle"></i> 全部在线
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-images"></i>
                </div>
                <div class="stat-info">
                    <h4><?php echo $totalBanners; ?></h4>
                    <p>轮播图</p>
                    <div class="stat-trend">
                        <i class="fas fa-check"></i> 正常展示
                    </div>
                </div>
            </div>
        </div>

        <!-- 主要内容区：两列布局 -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            <!-- 左侧：最近工单 -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-clock"></i> 最近工单</h3>
                    <a href="tickets.php" class="btn btn-outline btn-sm">
                        查看全部 <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentTickets)): ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>工单号</th>
                                        <th>标题</th>
                                        <th>提交人</th>
                                        <th>优先级</th>
                                        <th>状态</th>
                                        <th>时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentTickets as $ticket):
                                        $priorityColors = [
                                            'low' => 'badge-secondary',
                                            'normal' => 'badge-primary',
                                            'high' => 'badge-warning',
                                            'urgent' => 'badge-danger'
                                        ];
                                        $priorityNames = [
                                            'low' => '低',
                                            'normal' => '普通',
                                            'high' => '高',
                                            'urgent' => '紧急'
                                        ];
                                        $statusColors = [
                                            'open' => 'badge-warning',
                                            'processing' => 'badge-info',
                                            'closed' => 'badge-success'
                                        ];
                                        $statusNames = [
                                            'open' => '待处理',
                                            'processing' => '处理中',
                                            'closed' => '已关闭'
                                        ];
                                    ?>
                                    <tr>
                                        <td><code><?php echo e($ticket['ticket_no'] ?? ''); ?></code></td>
                                        <td><strong><?php echo e(mb_substr($ticket['subject'] ?? '', 0, 20)); ?></strong></td>
                                        <td><?php echo e($ticket['user_name'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge <?php echo $priorityColors[$ticket['priority']] ?? 'badge-secondary'; ?>">
                                                <?php echo $priorityNames[$ticket['priority']] ?? '普通'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $statusColors[$ticket['status']] ?? 'badge-secondary'; ?>">
                                                <?php echo $statusNames[$ticket['status']] ?? $ticket['status']; ?>
                                            </span>
                                        </td>
                                        <td style="white-space: nowrap;"><?php echo format_date($ticket['created_at'], 'm-d H:i'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h3>暂无工单</h3>
                            <p>目前还没有任何工单记录</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 右侧：快捷操作 + 系统信息 -->
            <div>
                <!-- 快捷操作 -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-bolt"></i> 快捷操作</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <a href="settings.php#basic" class="btn btn-outline" style="justify-content: flex-start;">
                                <i class="fas fa-edit"></i> 编辑设置
                            </a>
                            <a href="content.php?tab=banners" class="btn btn-outline" style="justify-content: flex-start;">
                                <i class="fas fa-image"></i> 轮播图
                            </a>
                            <a href="products.php" class="btn btn-outline" style="justify-content: flex-start;">
                                <i class="fas fa-plus-circle"></i> 新增产品
                            </a>
                            <a href="users.php" class="btn btn-outline" style="justify-content: flex-start;">
                                <i class="fas fa-user-plus"></i> 用户管理
                            </a>
                            <a href="templates.php" class="btn btn-outline" style="justify-content: flex-start;">
                                <i class="fas fa-palette"></i> 切换模板
                            </a>
                            <a href="system.php" class="btn btn-outline" style="justify-content: flex-start;">
                                <i class="fas fa-download"></i> 数据备份
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 系统状态 -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-server"></i> 系统状态</h3>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; flex-direction: column; gap: 14px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--text-secondary);">
                                    <i class="fab fa-php" style="color: #777BB4;"></i> PHP版本
                                </span>
                                <strong><?php echo $phpVersion; ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--text-secondary);">
                                    <i class="fas fa-database" style="color: var(--info-color);"></i> MySQL
                                </span>
                                <span class="badge badge-success"><?php echo $mysqlStatus; ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--text-secondary);">
                                    <i class="fab fa-linux" style="color: #FCC624;"></i> 系统
                                </span>
                                <strong><?php echo $os; ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--text-secondary);">
                                    <i class="fas fa-hdd" style="color: var(--accent-color);"></i> 磁盘空间
                                </span>
                                <strong><?php echo $diskFreeSpace; ?>G / <?php echo $diskTotalSpace; ?>G</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: var(--text-secondary);">
                                    <i class="fas fa-memory" style="color: var(--primary-color);"></i> 内存限制
                                </span>
                                <strong><?php echo $memoryLimit; ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
