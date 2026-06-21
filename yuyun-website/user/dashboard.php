<?php
/**
 * 语云科技 - 用户中心仪表盘
 */
session_start();
require_once __DIR__ . '/../core/Functions.php';

// 要求登录
require_login();

$page_title = '用户中心 - ' . (get_config('site_name') ?: '语云科技');
$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_username'] ?? '用户';
$email = $_SESSION['user_email'] ?? '';
$avatar = $_SESSION['user_avatar'] ?? '';

// 获取用户统计数据
$users_data = get_content('users');
$current_user = null;
foreach ($users_data as $u) {
    if (($u['id'] ?? '') == $user_id) {
        $current_user = $u;
        break;
    }
}

// 获取工单数据
$tickets_data = get_content('tickets');
$my_tickets = array_filter($tickets_data, function($t) use ($user_id) {
    return ($t['user_id'] ?? '') == $user_id;
});

// 统计数据
$total_tickets = count($my_tickets);
$open_tickets = count(array_filter($my_tickets, function($t) {
    return in_array($t['status'] ?? '', ['open', 'replying']);
}));
$closed_tickets = count(array_filter($my_tickets, function($t) {
    return in_array($t['status'] ?? '', ['closed', 'resolved']);
}));

// 获取反馈数据
$feedbacks_data = get_content('feedbacks');
$my_feedbacks = array_filter($feedbacks_data, function($f) use ($user_id) {
    return ($f['user_id'] ?? '') == $user_id;
});
$total_feedbacks = count($my_feedbacks);

// 最近动态（模拟数据，实际应从日志或活动记录获取）
$recent_activities = [
    ['icon' => '&#128172;', 'text' => '欢迎加入语云科技', 'time' => date('Y-m-d H:i'), 'type' => 'info'],
];

// 添加最近的工单动态
$ticket_list = array_values($my_tickets);
usort($ticket_list, function($a, $b) {
    return strtotime($b['created_at'] ?? 0) - strtotime($a['created_at'] ?? 0);
});

foreach (array_slice($ticket_list, 0, 3) as $ticket) {
    $status_text = [
        'open' => '新建',
        'replying' => '回复中',
        'closed' => '已关闭',
        'resolved' => '已解决'
    ];
    $recent_activities[] = [
        'icon' => '&#128221;',
        'text' => '工单 "' . e(truncate($ticket['subject'] ?? '', 30)) . '" 状态: ' . ($status_text[$ticket['status']] ?? $ticket['status']),
        'time' => format_date($ticket['created_at'] ?? '', 'm-d H:i'),
        'type' => $ticket['status'] === 'resolved' ? 'success' : 'primary'
    ];
}

// 最近登录时间
$last_login = $current_user['last_login'] ?? date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        /* 用户中心布局 */
        .user-layout {
            display: flex;
            min-height: calc(100vh - var(--nav-height));
            background: var(--gray-50);
        }

        /* 侧边栏 */
        .user-sidebar {
            width: 260px;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            padding: 24px 0;
            position: fixed;
            top: var(--nav-height);
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 100;
            transition: transform var(--transition-normal);
        }

        .sidebar-user-info {
            padding: 0 20px 24px;
            border-bottom: 1px solid var(--gray-100);
            margin-bottom: 16px;
        }

        .sidebar-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-bg);
            margin-bottom: 12px;
        }

        .sidebar-username {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .sidebar-email {
            font-size: 13px;
            color: var(--gray-500);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 12px;
        }

        .sidebar-menu li {
            margin-bottom: 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-600);
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
            text-decoration: none;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: var(--primary-bg);
            color: var(--primary);
        }

        .sidebar-link.active {
            font-weight: 700;
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar-divider {
            height: 1px;
            background: var(--gray-100);
            margin: 16px 20px;
        }

        /* 主内容区 */
        .user-main {
            flex: 1;
            margin-left: 260px;
            padding: 32px;
            min-width: 0;
        }

        /* 欢迎横幅 */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: var(--radius-lg);
            padding: 32px 36px;
            color: white;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .welcome-banner h1 {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 8px;
            position: relative;
        }

        .welcome-banner p {
            font-size: 15px;
            opacity: 0.9;
            position: relative;
        }

        .welcome-time {
            font-size: 13px;
            opacity: 0.75;
            margin-top: 12px;
            position: relative;
        }

        /* 快捷操作卡片 */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        .action-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 24px;
            text-decoration: none;
            transition: all var(--transition-normal);
            cursor: pointer;
            text-align: center;
        }

        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .action-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 24px;
        }

        .action-icon.blue { background: var(--primary-bg); color: var(--primary); }
        .action-icon.orange { background: var(--accent-light); color: var(--accent); }
        .action-icon.green { background: #F0FDF4; color: var(--success); }
        .action-icon.purple { background: #F3E8FF; color: #9333EA; }

        .action-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .action-desc {
            font-size: 13px;
            color: var(--gray-500);
        }

        /* 统计概览 */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 24px;
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--gray-500);
            font-weight: 500;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: var(--gray-900);
        }

        .stat-trend {
            font-size: 13px;
            color: var(--success);
            font-weight: 600;
        }

        /* 最近动态 */
        .activity-section {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 24px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .activity-icon.info { background: #EFF6FF; color: var(--info); }
        .activity-icon.primary { background: var(--primary-bg); color: var(--primary); }
        .activity-icon.success { background: #F0FDF4; color: var(--success); }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-text {
            font-size: 14px;
            color: var(--gray-700);
            line-height: 1.5;
        }

        .activity-time {
            font-size: 12px;
            color: var(--gray-400);
            margin-top: 4px;
        }

        /* 移动端适配 */
        @media (max-width: 1024px) {
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }

            .stats-overview {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .user-sidebar {
                transform: translateX(-100%);
            }

            .user-sidebar.show {
                transform: translateX(0);
            }

            .user-main {
                margin-left: 0;
                padding: 20px 16px;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }

            .stats-overview {
                grid-template-columns: 1fr;
            }

            .mobile-menu-btn {
                display: block !important;
            }

            .welcome-banner {
                padding: 24px;
            }

            .welcome-banner h1 {
                font-size: 22px;
            }
        }

        .mobile-menu-btn {
            display: none;
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 56px;
            height: 56px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            box-shadow: var(--shadow-lg);
            z-index: 101;
            font-size: 24px;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body>
    <!-- 移动端菜单按钮 -->
    <button class="mobile-menu-btn" onclick="toggleSidebar()">&#9776;</button>

    <div class="user-layout">
        <!-- 侧边栏 -->
        <aside class="user-sidebar" id="userSidebar">
            <div class="sidebar-user-info">
                <?php if ($avatar): ?>
                <img src="<?php echo e($avatar); ?>" alt="头像" class="sidebar-avatar">
                <?php else: ?>
                <div class="sidebar-avatar" style="background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:white;font-size:24px;font-weight:700;">
                    <?php echo mb_substr($username, 0, 1); ?>
                </div>
                <?php endif; ?>
                <div class="sidebar-username"><?php echo e($username); ?></div>
                <div class="sidebar-email"><?php echo e($email); ?></div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="dashboard.php" class="sidebar-link active">
                        <i>&#127968;</i> 仪表盘
                    </a>
                </li>
                <li>
                    <a href="profile.php" class="sidebar-link">
                        <i>&#128100;</i> 个人资料
                    </a>
                </li>
                <li>
                    <a href="tickets.php" class="sidebar-link">
                        <i>&#128221;</i> 我的工单
                        <?php if ($open_tickets > 0): ?>
                        <span style="margin-left:auto;background:var(--error);color:white;padding:2px 8px;border-radius:10px;font-size:11px;"><?php echo $open_tickets; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="feedback.php" class="sidebar-link">
                        <i>&#128172;</i> 建议举报
                    </a>
                </li>
                <li class="sidebar-divider"></li>
                <li>
                    <a href="logout.php" class="sidebar-link" style="color:var(--error);">
                        <i>&#128682;</i> 退出登录
                    </a>
                </li>
            </ul>
        </aside>

        <!-- 主内容区 -->
        <main class="user-main">
            <!-- 欢迎横幅 -->
            <div class="welcome-banner">
                <h1>欢迎回来，<?php echo e($username); ?> &#128075;</h1>
                <p>今天是 <?php echo date('Y年n月j日'); ?>，祝您工作顺利！</p>
                <div class="welcome-time">上次登录：<?php echo format_date($last_login, 'Y-m-d H:i'); ?></div>
            </div>

            <!-- 快捷操作 -->
            <div class="quick-actions">
                <a href="tickets.php" class="action-card">
                    <div class="action-icon blue">&#128221;</div>
                    <div class="action-title">我的工单</div>
                    <div class="action-desc"><?php echo $total_tickets; ?> 个工单</div>
                </a>
                <a href="feedback.php" class="action-card">
                    <div class="action-icon orange">&#128172;</div>
                    <div class="action-title">提交建议</div>
                    <div class="action-desc">反馈您的想法</div>
                </a>
                <a href="profile.php" class="action-card">
                    <div class="action-icon green">&#128100;</div>
                    <div class="action-title">个人信息</div>
                    <div class="action-desc">管理账户设置</div>
                </a>
                <a href="profile.php#password" class="action-card">
                    <div class="action-icon purple">&#128274;</div>
                    <div class="action-title">修改密码</div>
                    <div class="action-desc">保障账户安全</div>
                </a>
            </div>

            <!-- 统计概览 -->
            <div class="stats-overview">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <span class="stat-label">总工单数</span>
                        <span style="font-size:24px;color:var(--primary);">&#128221;</span>
                    </div>
                    <div class="stat-value"><?php echo $total_tickets; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <span class="stat-label">进行中</span>
                        <span style="font-size:24px;color:var(--warning);">&#9888;</span>
                    </div>
                    <div class="stat-value"><?php echo $open_tickets; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <span class="stat-label">反馈建议</span>
                        <span style="font-size:24px;color:var(--success);">&#128172;</span>
                    </div>
                    <div class="stat-value"><?php echo $total_feedbacks; ?></div>
                </div>
            </div>

            <!-- 最近动态 -->
            <div class="activity-section">
                <div class="section-header">
                    <h2 class="section-title">最近动态</h2>
                    <a href="tickets.php" style="font-size:14px;">查看全部 &rarr;</a>
                </div>
                <ul class="activity-list">
                    <?php foreach (array_slice($recent_activities, 0, 5) as $activity): ?>
                    <li class="activity-item">
                        <div class="activity-icon <?php echo $activity['type']; ?>">
                            <?php echo $activity['icon']; ?>
                        </div>
                        <div class="activity-content">
                            <div class="activity-text"><?php echo $activity['text']; ?></div>
                            <div class="activity-time"><?php echo $activity['time']; ?></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($recent_activities)): ?>
                    <li class="activity-item">
                        <div class="activity-content" style="text-align:center;padding:20px;">
                            <div style="color:var(--gray-400);">暂无动态</div>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/modal.js"></script>
    <script>
        // 移动端侧边栏切换
        function toggleSidebar() {
            document.getElementById('userSidebar').classList.toggle('show');
        }

        // 点击外部关闭侧边栏
        document.addEventListener('click', function(e) {
            var sidebar = document.getElementById('userSidebar');
            var btn = document.querySelector('.mobile-menu-btn');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(e.target) &&
                !btn.contains(e.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>
</html>
