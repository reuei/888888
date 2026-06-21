<?php
/**
 * 语云科技 - 工单管理页面
 * 工单列表、筛选、详情查看、回复、关闭
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_admin();

$tickets = get_content('tickets') ?: [];
$replies = get_content('ticket_replies') ?: [];

// 处理回复操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_ticket'])) {
    $ticketId = intval($_POST['ticket_id'] ?? 0);
    $content = trim($_POST['reply_content'] ?? '');

    if ($ticketId > 0 && !empty($content)) {
        $maxId = 0;
        foreach ($replies as $r) { if (($r['id'] ?? 0) > $maxId) $maxId = $r['id']; }

        $replies[] = [
            'id' => $maxId + 1,
            'ticket_id' => $ticketId,
            'user_id' => 0,
            'user_name' => $_SESSION['admin_name'] ?? '管理员',
            'is_admin' => 1,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ];

        save_content('ticket_replies', $replies);

        // 更新工单状态和时间
        foreach ($tickets as &$t) {
            if ((int)$t['id'] === $ticketId) {
                $t['status'] = 'processing';
                $t['updated_at'] = date('Y-m-d H:i:s');
                $t['replies_count'] = ($t['replies_count'] ?? 0) + 1;
                break;
            }
        }
        unset($t);
        save_content('tickets', $tickets);

        log_message("管理员回复了工单 #{$ticketId}");
        header('Location: tickets.php?msg=replied');
        exit;
    }
}

// 处理关闭操作
if (isset($_GET['close']) && is_numeric($_GET['close'])) {
    $ticketId = intval($_GET['close']);
    foreach ($tickets as &$t) {
        if ((int)$t['id'] === $ticketId) {
            $t['status'] = 'closed';
            $t['closed_at'] = date('Y-m-d H:i:s');
            $t['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    unset($t);
    save_content('tickets', $tickets);
    log_message("管理员关闭了工单 #{$ticketId}");
    header('Location: tickets.php?msg=closed');
    exit;
}

// 筛选
$statusFilter = $_GET['status'] ?? 'all';
if ($statusFilter !== 'all') {
    $tickets = array_filter($tickets, fn($t) => ($t['status'] ?? '') === $statusFilter);
}
$tickets = array_values($tickets);

// 排序（最新在前）
usort($tickets, fn($a, $b) => strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? ''));

$msg = $_GET['msg'] ?? '';
$msgText = match($msg) {
    'replied' => '回复成功',
    'closed' => '工单已关闭',
    default => ''
};
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>工单管理 - 语云科技后台</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- 侧边栏 -->
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <!-- 顶部导航 -->
    <header class="header">
        <div class="header-left">
            <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.add('mobile-show'); document.querySelector('.sidebar-overlay').classList.add('show');"><i class="fas fa-bars"></i></button>
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-separator">/</span>
                <span>工单管理</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-dropdown">
                <div class="user-avatar"><?php echo mb_substr($_SESSION['admin_name'] ?? '管', 0, 1); ?></div>
                <div class="user-info"><div class="name"><?php echo e($_SESSION['admin_name'] ?? '管理员'); ?></div><div class="role">超级管理员</div></div>
            </div>
        </div>
    </header>

    <!-- 主内容区 -->
    <main class="main-content">
        <?php if ($msgText): ?>
            <div style="background:rgba(40,167,69,0.12); border:1px solid rgba(40,167,69,0.3); color:#51cf66; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
                <i class="fas fa-check-circle"></i> <?php echo $msgText; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-ticket-alt"></i> 工单管理</h3>
                <span style="color:var(--text-muted); font-size:13px;">共 <?php echo count($tickets); ?> 条工单</span>
            </div>

            <!-- 状态筛选标签 -->
            <div class="tabs" style="border:none; margin-bottom:20px;">
                <a href="?status=all" class="tab-item <?php echo $statusFilter === 'all' ? 'active' : ''; ?>">全部 (<?php echo count(get_content('tickets') ?: []); ?>)</a>
                <a href="?status=open" class="tab-item <?php echo $statusFilter === 'open' ? 'active' : ''; ?>">
                    待处理 <span class="badge" style="margin-left:4px; background:var(--warning-color);">
                        <?php echo count(array_filter(get_content('tickets')?:[], fn($t)=>($t['status']??'')==='open')); ?>
                    </span>
                </a>
                <a href="?status=processing" class="tab-item <?php echo $statusFilter === 'processing' ? 'active' : ''; ?>">处理中</a>
                <a href="?status=closed" class="tab-item <?php echo $statusFilter === 'closed' ? 'active' : ''; ?>">已关闭</a>
            </div>

            <?php if (empty($tickets)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>暂无工单</h3>
                    <p>当前筛选条件下没有找到工单记录</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="120">工单号</th>
                                <th>标题</th>
                                <th width="120">提交人</th>
                                <th width="70">优先级</th>
                                <th width="80">状态</th>
                                <th width="130">创建时间</th>
                                <th width="140">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket):
                                $priorityMap = [
                                    'low' => ['class'=>'badge-secondary','text'=>'低'],
                                    'normal' => ['class'=>'badge-primary','text'=>'普通'],
                                    'high' => ['class'=>'badge-warning','text'=>'高'],
                                    'urgent' => ['class'=>'badge-danger','text'=>'紧急']
                                ];
                                $priorityInfo = $priorityMap[$ticket['priority'] ?? 'normal'] ?? $priorityMap['normal'];

                                $statusMap = [
                                    'open' => ['class'=>'badge-warning','text'=>'待处理'],
                                    'processing' => ['class'=>'badge-info','text'=>'处理中'],
                                    'closed' => ['class'=>'badge-success','text'=>'已关闭']
                                ];
                                $statusInfo = $statusMap[$ticket['status'] ?? 'open'] ?? $statusMap['open'];
                            ?>
                                <tr>
                                    <td><code><?php echo e($ticket['ticket_no'] ?? '#'.$ticket['id']); ?></code></td>
                                    <td>
                                        <strong><?php echo e(mb_substr($ticket['subject'] ?? '', 0, 35)); ?></strong>
                                        <?php if (($ticket['replies_count'] ?? 0) > 0): ?>
                                            <span class="badge badge-info" style="margin-left:6px;"><?php echo $ticket['replies_count']; ?>条回复</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($ticket['user_name'] ?? '-'); ?><br><small style="color:var(--text-muted);"><?php echo e($ticket['user_email'] ?? ''); ?></small></td>
                                    <td><span class="badge <?php echo $priorityInfo['class']; ?>"><?php echo $priorityInfo['text']; ?></span></td>
                                    <td><span class="badge <?php echo $statusInfo['class']; ?>"><?php echo $statusInfo['text']; ?></span></td>
                                    <td style="white-space:nowrap;"><?php echo format_date($ticket['created_at'], 'm-d H:i'); ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="action-btn view" onclick="toggleTicketDetail(<?php echo $ticket['id']; ?>)" title="查看详情">
                                                <i class="fas fa-eye" id="icon-<?php echo $ticket['id']; ?>"></i>
                                            </button>
                                            <?php if (($ticket['status'] ?? '') !== 'closed'): ?>
                                                <a href="?close=<?php echo $ticket['id']; ?>" class="action-btn delete" title="关闭工单" onclick="return confirm('确定要关闭此工单吗？')">
                                                    <i class="fas fa-times-circle"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <!-- 详情展开行 -->
                                <tr id="detail-row-<?php echo $ticket['id']; ?>" style="display:none;">
                                    <td colspan="7" style="padding:0;">
                                        <div style="padding:20px; background:var(--body-bg);">
                                            <!-- 工单内容 -->
                                            <div style="margin-bottom:20px; padding:16px; background:var(--card-bg); border-radius:8px; border-left:3px solid var(--primary-color);">
                                                <h4 style="margin-bottom:8px; font-size:15px;"><?php echo e($ticket['subject'] ?? ''); ?></h4>
                                                <p style="color:var(--text-secondary); line-height:1.7; white-space:pre-wrap;"><?php echo nl2br(e($ticket['content'] ?? '')); ?></p>
                                                <p style="margin-top:10px; font-size:12px; color:var(--text-muted);">
                                                    提交于 <?php echo format_date($ticket['created_at']); ?>
                                                </p>
                                            </div>

                                            <!-- 回复记录 -->
                                            <?php
                                                $ticketReplies = array_filter($replies, fn($r) => (int)($r['ticket_id'] ?? 0) === (int)$ticket['id']);
                                                usort($ticketReplies, fn($a,$b) => strtotime($a['created_at']??'') - strtotime($b['created_at']??''));
                                                $ticketReplies = array_values($ticketReplies);
                                            ?>
                                            <h4 style="margin-bottom:12px; font-size:14px;">
                                                回复记录 (<?php echo count($ticketReplies); ?>)
                                            </h4>

                                            <?php if (!empty($ticketReplies)):
                                                foreach ($ticketReplies as $reply):
                                            ?>
                                                <div style="
                                                    padding:14px; margin-bottom:10px; border-radius:8px;
                                                    background:<?php echo $reply['is_admin'] ? 'rgba(0,102,204,0.06)' : 'rgba(255,107,0,0.06)'; ?>;
                                                    border-left:3px solid <?php echo $reply['is_admin'] ? 'var(--primary-color)' : 'var(--accent-color)'; ?>;
                                                ">
                                                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                                                        <strong style="color:<?php echo $reply['is_admin'] ? 'var(--primary-color)' : 'var(--accent-color)'; ?>;">
                                                            <?php echo e($reply['user_name'] ?? '未知'); ?>
                                                            <?php if ($reply['is_admin']): ?>
                                                                <span class="badge badge-info ml-10" style="font-size:10px;">客服</span>
                                                            <?php endif; ?>
                                                        </strong>
                                                        <span style="font-size:12px; color:var(--text-muted);"><?php echo format_date($reply['created_at'], 'm-d H:i'); ?></span>
                                                    </div>
                                                    <p style="margin:0; color:var(--text-secondary); line-height:1.6;"><?php echo nl2br(e($reply['content'])); ?></p>
                                                </div>
                                            <?php endforeach; else: ?>
                                                <p style="color:var(--text-muted); padding:16px; text-align:center; background:var(--card-bg); border-radius:8px;">暂无回复记录</p>
                                            <?php endif; ?>

                                            <!-- 回复表单 -->
                                            <?php if (($ticket['status'] ?? '') !== 'closed'): ?>
                                                <form method="POST" style="margin-top:16px; display:flex; gap:10px; align-items:flex-end;">
                                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                                    <div style="flex:1;">
                                                        <textarea name="reply_content" rows="2" class="form-control" placeholder="输入回复内容..." required style="resize:none;"></textarea>
                                                    </div>
                                                    <button type="submit" name="reply_ticket" class="btn btn-primary btn-sm" style="height:42px;">
                                                        <i class="fas fa-reply"></i> 回复
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <div style="margin-top:12px; padding:10px; background:var(--table-header); border-radius:6px; text-align:center; color:var(--text-muted); font-size:13px;">
                                                    <i class="fas fa-lock"></i> 此工单已关闭，无法继续回复
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="../assets/js/admin.js"></script>
    <script>
        function toggleTicketDetail(id) {
            const row = document.getElementById('detail-row-' + id);
            const icon = document.getElementById('icon-' + id);
            const isVisible = row.style.display !== 'none';

            row.style.display = isVisible ? 'none' : '';

            if (icon) {
                icon.className = isVisible ? 'fas fa-eye' : 'fas fa-eye-slash';
            }

            // 平滑滚动到详情
            if (!isVisible) {
                row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    </script>
</body>
</html>
