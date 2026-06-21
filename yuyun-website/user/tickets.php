<?php
/**
 * 语云科技 - 我的工单页面
 */
session_start();
require_once __DIR__ . '/../core/Functions.php';

// 要求登录
require_login();

$page_title = '我的工单 - ' . (get_config('site_name') ?: '语云科技');
$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_username'] ?? '用户';
$email = $_SESSION['user_email'] ?? '';
$avatar = $_SESSION['user_avatar'] ?? '';

$error = '';
$success = '';

// 获取工单数据
$tickets_data = get_content('tickets');

// 处理新建工单
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = '安全验证失败';
    } else {
        $subject = trim($_POST['subject'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $priority = $_POST['priority'] ?? 'normal';

        if (empty($subject) || empty($content)) {
            $error = '请填写工单主题和内容';
        } else {
            // 创建新工单
            $new_ticket = [
                'id' => uniqid('ticket_'),
                'user_id' => $user_id,
                'username' => $username,
                'subject' => $subject,
                'content' => $content,
                'priority' => $priority,
                'status' => 'open',
                'replies' => [],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $tickets_data[] = $new_ticket;
            save_content('tickets', $tickets_data);
            $success = '工单创建成功！';

            // 刷新页面显示新工单
            header('Location: tickets.php');
            exit;
        }
    }
}

// 处理回复工单
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reply') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = '安全验证失败';
    } else {
        $ticket_id = $_POST['ticket_id'] ?? '';
        $reply_content = trim($_POST['reply_content'] ?? '');

        if (empty($ticket_id) || empty($reply_content)) {
            $error = '回复内容不能为空';
        } else {
            foreach ($tickets_data as &$ticket) {
                if (($ticket['id'] ?? '') === $ticket_id) {
                    $ticket['replies'][] = [
                        'id' => uniqid('reply_'),
                        'user_id' => $user_id,
                        'username' => $username,
                        'content' => $reply_content,
                        'is_admin' => false,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $ticket['status'] = 'replying';
                    $ticket['updated_at'] = date('Y-m-d H:i:s');
                    break;
                }
            }
            unset($ticket);
            save_content('tickets', $tickets_data);
            $success = '回复成功！';
        }
    }
}

// 处理关闭工单
if (isset($_GET['close'])) {
    $ticket_id = $_GET['close'];
    foreach ($tickets_data as &$ticket) {
        if (($ticket['id'] ?? '') === $ticket_id && ($ticket['user_id'] ?? '') == $user_id) {
            $ticket['status'] = 'closed';
            $ticket['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    unset($ticket);
    save_content('tickets', $tickets_data);
    header('Location: tickets.php');
    exit;
}

// 筛选当前用户的工单
$my_tickets = array_filter($tickets_data, function($t) use ($user_id) {
    return ($t['user_id'] ?? '') == $user_id;
});

// 按时间倒序排序
usort($my_tickets, function($a, $b) {
    return strtotime($b['created_at'] ?? 0) - strtotime($a['created_at'] ?? 0);
});

// 分页
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 10;
$total_tickets = count($my_tickets);
$total_pages = ceil($total_tickets / $per_page);
$page = min($page, max(1, $total_pages));
$offset = ($page - 1) * $per_page;
$paged_tickets = array_slice(array_values($my_tickets), $offset, $per_page);

// 状态映射
$status_map = [
    'open' => ['text' => '待处理', 'class' => 'warning', 'bg' => '#FEF3C7', 'color' => '#D97706'],
    'replying' => ['text' => '处理中', 'class' => 'info', 'bg' => '#DBEAFE', 'color' => '#2563EB'],
    'closed' => ['text' => '已关闭', 'class' => '', 'bg' => '#F3F4F6', 'color' => '#6B7280'],
    'resolved' => ['text' => '已解决', 'class' => 'success', 'bg' => '#D1FAE5', 'color' => '#059669']
];

// 优先级映射
$priority_map = [
    'low' => ['text' => '低', 'color' => '#6B7280'],
    'normal' => ['text' => '中', 'color' => '#0066CC'],
    'high' => ['text' => '高', 'color' => '#FF6B00'],
    'urgent' => ['text' => '紧急', 'color' => '#EF4444']
];
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
        .user-layout { display: flex; min-height: calc(100vh - var(--nav-height)); background: var(--gray-50); }

        .user-sidebar {
            width: 260px; background: var(--white); border-right: 1px solid var(--gray-200);
            padding: 24px 0; position: fixed; top: var(--nav-height); left: 0; bottom: 0;
            overflow-y: auto; z-index: 100;
        }

        .sidebar-user-info { padding: 0 20px 24px; border-bottom: 1px solid var(--gray-100); margin-bottom: 16px; }
        .sidebar-avatar { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-bg); margin-bottom: 12px; }
        .sidebar-username { font-size: 16px; font-weight: 700; color: var(--gray-900); margin-bottom: 4px; }
        .sidebar-email { font-size: 13px; color: var(--gray-500); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .sidebar-menu { list-style: none; padding: 0 12px; }
        .sidebar-menu li { margin-bottom: 4px; }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            font-size: 14px; font-weight: 500; color: var(--gray-600);
            border-radius: var(--radius-md); transition: all var(--transition-fast); text-decoration: none;
        }
        .sidebar-link:hover, .sidebar-link.active { background: var(--primary-bg); color: var(--primary); }
        .sidebar-link i { width: 20px; text-align: center; font-size: 16px; }
        .sidebar-divider { height: 1px; background: var(--gray-100); margin: 16px 20px; }

        .user-main { flex: 1; margin-left: 260px; padding: 32px; min-width: 0; }

        /* 页面头部 */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; }
        .page-title { font-size: 24px; font-weight: 800; color: var(--gray-900); }
        .page-subtitle { font-size: 14px; color: var(--gray-500); margin-top: 4px; }

        /* 工单表格 */
        .tickets-card {
            background: var(--white); border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg); overflow: hidden;
        }

        .table-responsive { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }

        thead { background: var(--gray-50); }

        th {
            padding: 14px 16px; text-align: left; font-size: 13px; font-weight: 600;
            color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.05em;
            border-bottom: 2px solid var(--gray-200);
        }

        td {
            padding: 16px; font-size: 14px; color: var(--gray-700);
            border-bottom: 1px solid var(--gray-100); vertical-align: middle;
        }

        tr:hover { background: var(--gray-50); }

        tr:last-child td { border-bottom: none; }

        /* 状态Badge */
        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 20px; font-size: 12px;
            font-weight: 600; white-space: nowrap;
        }

        .badge::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
        }

        .badge-warning { background: #FEF3C7; color: #D97706; }
        .badge-warning::before { background: #D97706; }

        .badge-info { background: #DBEAFE; color: #2563EB; }
        .badge-info::before { background: #2563EB; }

        .badge-success { background: #D1FAE5; color: #059669; }
        .badge-success::before { background: #059669; }

        .badge-gray { background: #F3F4F6; color: #6B7280; }
        .badge-gray::before { background: #6B7280; }

        /* 优先级标签 */
        .priority-tag {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: var(--radius-sm);
            font-size: 12px; font-weight: 600;
        }

        /* 操作按钮 */
        .action-btns { display: flex; gap: 8px; flex-wrap: wrap; }

        .btn-xs {
            padding: 5px 12px; font-size: 12px; border-radius: var(--radius-sm);
            font-weight: 500;
        }

        .btn-link {
            background: transparent; color: var(--primary); padding: 4px 8px;
            font-size: 13px; cursor: pointer; transition: color var(--transition-fast);
        }
        .btn-link:hover { color: var(--primary-dark); text-decoration: underline; }

        /* 工单详情展开区 */
        .ticket-detail {
            display: none; background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
        }

        .ticket-detail.show { display: block; }

        .detail-inner { padding: 20px; }

        .detail-meta {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;
            margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--gray-200);
        }

        .meta-item { font-size: 13px; }
        .meta-label { color: var(--gray-500); margin-bottom: 4px; }
        .meta-value { color: var(--gray-800); font-weight: 500; }

        .detail-content {
            background: var(--white); border: 1px solid var(--gray-200);
            border-radius: var(--radius-md); padding: 20px; margin-bottom: 20px;
            line-height: 1.7; font-size: 14px; color: var(--gray-700);
        }

        /* 回复列表 */
        .replies-section h4 { font-size: 15px; font-weight: 700; color: var(--gray-900); margin-bottom: 16px; }

        .reply-item {
            background: var(--white); border: 1px solid var(--gray-200);
            border-radius: var(--radius-md); padding: 16px; margin-bottom: 12px;
        }

        .reply-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 10px; font-size: 13px;
        }

        .reply-author { font-weight: 600; color: var(--gray-900); }
        .reply-time { color: var(--gray-400); }

        .reply-body { font-size: 14px; color: var(--gray-700); line-height: 1.7; }

        .reply-admin { border-left: 3px solid var(--primary); }

        /* 回复表单 */
        .reply-form { margin-top: 16px; }
        .reply-form textarea { min-height: 80px; }

        /* 空状态 */
        .empty-state {
            text-align: center; padding: 60px 20px; color: var(--gray-400);
        }
        .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }
        .empty-state p { font-size: 15px; margin-bottom: 16px; }

        /* 分页 */
        .pagination { display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 24px; padding: 20px; }

        .page-btn {
            padding: 8px 14px; border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm); font-size: 13px; color: var(--gray-600);
            background: var(--white); cursor: pointer; transition: all var(--transition-fast);
        }
        .page-btn:hover:not(:disabled) { border-color: var(--primary); color: var(--primary); }
        .page-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
        .page-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* 提示消息 */
        .alert-message {
            padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px;
            font-size: 14px; display: flex; align-items: center; gap: 10px;
        }
        .alert-message.error { background: #FEF2F2; color: var(--error); border: 1px solid #FECACA; }
        .alert-message.success { background: #F0FDF4; color: var(--success); border: 1px solid #BBF7D0; }

        /* 移动端适配 */
        @media (max-width: 768px) {
            .user-sidebar { transform: translateX(-100%); }
            .user-sidebar.show { transform: translateX(0); }
            .user-main { margin-left: 0; padding: 20px 16px; }
            .mobile-menu-btn { display: block !important; }
            .detail-meta { grid-template-columns: 1fr; }
            th, td { padding: 10px 8px; font-size: 13px; }
        }

        .mobile-menu-btn {
            display: none; position: fixed; bottom: 24px; right: 24px;
            width: 56px; height: 56px; background: var(--primary); color: white;
            border-radius: 50%; box-shadow: var(--shadow-lg); z-index: 101;
            font-size: 24px; cursor: pointer; border: none;
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
                <li><a href="dashboard.php" class="sidebar-link"><i>&#127968;</i> 仪表盘</a></li>
                <li><a href="profile.php" class="sidebar-link"><i>&#128100;</i> 个人资料</a></li>
                <li><a href="tickets.php" class="sidebar-link active"><i>&#128221;</i> 我的工单</a></li>
                <li><a href="feedback.php" class="sidebar-link"><i>&#128172;</i> 建议举报</a></li>
                <li class="sidebar-divider"></li>
                <li><a href="logout.php" class="sidebar-link" style="color:var(--error);"><i>&#128682;</i> 退出登录</a></li>
            </ul>
        </aside>

        <!-- 主内容区 -->
        <main class="user-main">
            <!-- 页面头部 -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">我的工单</h1>
                    <p class="page-subtitle">共 <?php echo $total_tickets; ?> 个工单</p>
                </div>
                <button class="btn btn-primary" onclick="showCreateModal()">
                    + 新建工单
                </button>
            </div>

            <!-- 消息提示 -->
            <?php if ($error): ?>
            <div class="alert-message error"><span>&#9888;</span><span><?php echo e($error); ?></span></div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert-message success"><span>&#10003;</span><span><?php echo e($success); ?></span></div>
            <?php endif; ?>

            <!-- 工单列表 -->
            <div class="tickets-card">
                <?php if (!empty($paged_tickets)): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width:60px;">ID</th>
                                <th>主题</th>
                                <th style="width:90px;">状态</th>
                                <th style="width:80px;">优先级</th>
                                <th style="width:150px;">创建时间</th>
                                <th style="width:140px;">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paged_tickets as $index => $ticket):
                                $status_info = $status_map[$ticket['status']] ?? $status_map['open'];
                                $pri_info = $priority_map[$ticket['priority']] ?? $priority_map['normal'];
                                $ticket_num = $offset + $index + 1;
                            ?>
                            <tr data-ticket-id="<?php echo e($ticket['id']); ?>">
                                <td style="font-weight:600;color:var(--gray-500);">#<?php echo $ticket_num; ?></td>
                                <td>
                                    <strong style="color:var(--gray-900);"><?php echo e(truncate($ticket['subject'], 40)); ?></strong>
                                    <?php $reply_count = count($ticket['replies'] ?? []); if ($reply_count > 0): ?>
                                    <span style="font-size:12px;color:var(--gray-400);margin-left:8px;">
                                        (<?php echo $reply_count; ?>条回复)
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $status_info['class']; ?>">
                                        <?php echo $status_info['text']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="priority-tag" style="background:<?php echo $pri_info['color']; ?>15;color:<?php echo $pri_info['color']; ?>">
                                        <?php echo $pri_info['text']; ?>
                                    </span>
                                </td>
                                <td style="font-size:13px;color:var(--gray-500);">
                                    <?php echo format_date($ticket['created_at'], 'Y-m-d H:i'); ?>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <button class="btn-link" onclick="toggleDetail('<?php echo e($ticket['id']); ?>')">
                                            查看
                                        </button>
                                        <?php if (!in_array($ticket['status'], ['closed', 'resolved'])): ?>
                                        <button class="btn-link" onclick="showConfirm('确定要关闭此工单吗？','关闭确认',function(){
                                            window.location.href='?close=<?php echo urlencode($ticket['id']); ?>';
                                        })">关闭</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- 工单详情（隐藏） -->
                            <tr>
                                <td colspan="6" style="padding:0;border:none;">
                                    <div class="ticket-detail" id="detail-<?php echo e($ticket['id']); ?>">
                                        <div class="detail-inner">
                                            <div class="detail-meta">
                                                <div class="meta-item">
                                                    <div class="meta-label">工单编号</div>
                                                    <div class="meta-value">#<?php echo $ticket_num; ?></div>
                                                </div>
                                                <div class="meta-item">
                                                    <div class="meta-label">优先级</div>
                                                    <div class="meta-value"><?php echo $pri_info['text']; ?></div>
                                                </div>
                                                <div class="meta-item">
                                                    <div class="meta-label">状态</div>
                                                    <div class="meta-value">
                                                        <span class="badge badge-<?php echo $status_info['class']; ?>">
                                                            <?php echo $status_info['text']; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="detail-content">
                                                <h4 style="margin-bottom:12px;font-size:15px;"><?php echo e($ticket['subject']); ?></h4>
                                                <?php echo nl2br(e($ticket['content'])); ?>
                                            </div>

                                            <!-- 回复列表 -->
                                            <?php if (!empty($ticket['replies'])): ?>
                                            <div class="replies-section">
                                                <h4>回复记录 (<?php echo count($ticket['replies']); ?>)</h4>
                                                <?php foreach ($ticket['replies'] as $reply): ?>
                                                <div class="reply-item <?php echo ($reply['is_admin'] ?? false) ? 'reply-admin' : ''; ?>">
                                                    <div class="reply-header">
                                                        <span class="reply-author">
                                                            <?php echo e($reply['username']); ?>
                                                            <?php if ($reply['is_admin'] ?? false): ?>
                                                            <span style="background:var(--primary);color:white;padding:1px 6px;border-radius:4px;font-size:11px;margin-left:6px;">客服</span>
                                                            <?php endif; ?>
                                                        </span>
                                                        <span class="reply-time"><?php echo format_date($reply['created_at'], 'm-d H:i'); ?></span>
                                                    </div>
                                                    <div class="reply-body"><?php echo nl2br(e($reply['content'])); ?></div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>

                                            <!-- 回复表单 -->
                                            <?php if (!in_array($ticket['status'], ['closed', 'resolved'])): ?>
                                            <form method="POST" action="" class="reply-form" onsubmit="return validateReply(this)">
                                                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                                <input type="hidden" name="action" value="reply">
                                                <input type="hidden" name="ticket_id" value="<?php echo e($ticket['id']); ?>">
                                                <div class="form-group">
                                                    <textarea name="reply_content" class="form-textarea"
                                                              placeholder="输入您的回复..." required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">提交回复</button>
                                            </form>
                                            <?php else: ?>
                                            <p style="font-size:13px;color:var(--gray-400);padding:10px 0;">
                                                此工单已关闭，无法继续回复。
                                            </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 分页 -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <a class="page-btn" href="?page=<?php echo $page - 1; ?>">上一页</a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a class="page-btn <?php echo $i === $page ? 'active' : ''; ?>"
                       href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                    <a class="page-btn" href="?page=<?php echo $page + 1; ?>">下一页</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="empty-state">
                    <span>&#128220;</span>
                    <p>暂无工单记录</p>
                    <button class="btn btn-primary" onclick="showCreateModal()">立即创建工单</button>
                </div>
                <?php endif; ?>
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

        // 展开/收起工单详情
        function toggleDetail(ticketId) {
            var detail = document.getElementById('detail-' + ticketId);
            if (detail) {
                detail.classList.toggle('show');
            }
        }

        // 显示新建工单弹窗
        function showCreateModal() {
            var modalHtml =
                '<form method="POST" action="" onsubmit="return validateTicketForm(this)" style="display:block;">' +
                '<input type="hidden" name="csrf_token" value="' +
                document.querySelector('[name="csrf_token"]')?.value + '">' +
                '<input type="hidden" name="action" value="create">' +

                '<div class="form-group">' +
                '<label class="form-label">工单主题 <span style="color:var(--error)">*</span></label>' +
                '<input type="text" name="subject" class="form-input" placeholder="简要描述您的问题" required maxlength="100">' +
                '</div>' +

                '<div class="form-group">' +
                '<label class="form-label">详细描述 <span style="color:var(--error)">*</span></label>' +
                '<textarea name="content" class="form-textarea" placeholder="请详细描述您遇到的问题，以便我们更好地帮助您" required minlength="10"></textarea>' +
                '</div>' +

                '<div class="form-group">' +
                '<label class="form-label">优先级</label>' +
                '<select name="priority" class="form-select">' +
                '<option value="low">低 - 一般咨询</option>' +
                '<option value="normal" selected>中等 - 常规问题</option>' +
                '<option value="high">高 - 影响使用</option>' +
                '<option value="urgent">紧急 - 服务异常</option>' +
                '</select>' +
                '</div>' +

                '<button type="submit" class="btn btn-primary" style="width:100%;">提交工单</button>' +
                '</form>';

            showAlert(modalHtml, '新建工单', null);
        }

        // 验证工单表单
        function validateTicketForm(form) {
            var subject = form.querySelector('[name="subject"]').value.trim();
            var content = form.querySelector('[name="content"]').value.trim();

            if (!subject || !content) {
                showToast('请填写完整的工单信息', 'warning');
                return false;
            }

            if (content.length < 10) {
                showToast('详细描述至少需要10个字符', 'warning');
                return false;
            }

            form.submit();
            return true;
        }

        // 验证回复表单
        function validateReply(form) {
            var content = form.querySelector('[name="reply_content"]').value.trim();
            if (!content) {
                showToast('请输入回复内容', 'warning');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
