<?php
/**
 * 语云科技 - 工单管理API
 * 支持工单的创建、回复、关闭和查询功能
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_once YUYUN_ROOT . '/core/Database.php';

// 设置响应头
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];
$db = Database::getInstance();

// 检查用户是否登录（除管理员查看外都需要登录）
if (!is_admin() && !is_logged_in()) {
    error('请先登录', 401);
}

/**
 * 获取工单列表
 */
function getTicketList() {
    global $db;

    $status = $_GET['status'] ?? 'all';
    $page = max(1, intval($_GET['page'] ?? 1));
    $per_page = min(50, max(10, intval($_GET['per_page'] ?? 15)));

    $tickets = get_content('tickets') ?: [];

    // 状态筛选
    if ($status !== 'all') {
        $tickets = array_filter($tickets, function($t) use ($status) {
            return $t['status'] === $status;
        });
    }

    // 管理员可以看到所有工单，普通用户只能看到自己的
    if (!is_admin()) {
        $userId = $_SESSION['user_id'] ?? 0;
        $tickets = array_filter($tickets, function($t) use ($userId) {
            return (int)($t['user_id'] ?? 0) === (int)$userId;
        });
    }

    // 按时间倒序排序
    usort($tickets, function($a, $b) {
        return strtotime($b['created_at'] ?? '') - strtotime($a['created_at'] ?? '');
    });

    // 分页
    $result = paginate(array_values($tickets), $page, $per_page);

    success([
        'list' => $result['items'],
        'pagination' => [
            'current_page' => $result['current_page'],
            'total_pages' => $result['total_pages'],
            'total_items' => $result['total_items'],
            'per_page' => $result['per_page']
        ]
    ]);
}

/**
 * 获取工单详情及回复
 */
function getTicketDetail() {
    $ticketId = intval($_GET['id'] ?? 0);

    if ($ticketId <= 0) {
        error('缺少工单ID参数');
    }

    $tickets = get_content('tickets') ?: [];
    $ticket = null;

    foreach ($tickets as $t) {
        if ((int)$t['id'] === $ticketId) {
            $ticket = $t;
            break;
        }
    }

    if (!$ticket) {
        error('工单不存在');
    }

    // 权限检查
    if (!is_admin() && (int)($ticket['user_id'] ?? 0) !== (int)($_SESSION['user_id'] ?? 0)) {
        error('无权查看此工单', 403);
    }

    // 获取回复记录
    $replies = get_content('ticket_replies') ?: [];
    $ticketReplies = array_filter($replies, function($r) use ($ticketId) {
        return (int)($r['ticket_id'] ?? 0) === $ticketId;
    });

    usort($ticketReplies, function($a, $b) {
        return strtotime($a['created_at'] ?? '') - strtotime($b['created_at'] ?? '');
    });

    $ticket['replies'] = array_values($ticketReplies);

    success($ticket);
}

/**
 * 创建新工单
 */
function createTicket() {
    $subject = trim($_POST['subject'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $priority = $_POST['priority'] ?? 'normal';
    $userId = $_SESSION['user_id'] ?? 0;

    if (empty($subject)) {
        error('请填写工单标题');
    }

    if (empty($content)) {
        error('请填写工单内容');
    }

    if (mb_strlen($subject) > 200) {
        error('标题长度不能超过200字');
    }

    if (mb_strlen($content) > 10000) {
        error('内容长度不能超过10000字');
    }

    $valid_priorities = ['low', 'normal', 'high', 'urgent'];
    if (!in_array($priority, $valid_priorities)) {
        $priority = 'normal';
    }

    $tickets = get_content('tickets') ?: [];

    // 生成ID
    $max_id = 0;
    foreach ($tickets as $t) {
        if (($t['id'] ?? 0) > $max_id) {
            $max_id = $t['id'];
        }
    }

    // 生成工单号
    $ticket_no = 'TK' . date('YmdHis') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

    $ticket = [
        'id' => $max_id + 1,
        'ticket_no' => $ticket_no,
        'user_id' => $userId,
        'user_email' => $_SESSION['user_email'] ?? '',
        'user_name' => $_SESSION['user_name'] ?? '',
        'subject' => $subject,
        'content' => $content,
        'priority' => $priority,
        'status' => 'open',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    $tickets[] = $ticket;

    if (save_content('tickets', $tickets)) {
        log_message("用户新建工单 #{$ticket_no}: {$subject}");
        success($ticket, '工单创建成功');
    } else {
        error('工单创建失败');
    }
}

/**
 * 回复工单
 */
function replyTicket() {
    $ticketId = intval($_POST['ticket_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');

    if ($ticketId <= 0) {
        error('缺少工单ID参数');
    }

    if (empty($content)) {
        error('请填写回复内容');
    }

    if (mb_strlen($content) > 10000) {
        error('回复内容不能超过10000字');
    }

    // 获取工单
    $tickets = get_content('tickets') ?: [];
    $ticket = null;
    $ticketIndex = -1;

    foreach ($tickets as $index => $t) {
        if ((int)$t['id'] === $ticketId) {
            $ticket = $t;
            $ticketIndex = $index;
            break;
        }
    }

    if (!$ticket) {
        error('工单不存在');
    }

    // 检查权限
    if (!is_admin() && (int)($ticket['user_id'] ?? 0) !== (int)($_SESSION['user_id'] ?? 0)) {
        error('无权回复此工单', 403);
    }

    // 已关闭的工单不允许回复
    if ($ticket['status'] === 'closed') {
        error('该工单已关闭，无法回复');
    }

    // 创建回复
    $replies = get_content('ticket_replies') ?: [];

    $max_id = 0;
    foreach ($replies as $r) {
        if (($r['id'] ?? 0) > $max_id) {
            $max_id = $r['id'];
        }
    }

    $reply = [
        'id' => $max_id + 1,
        'ticket_id' => $ticketId,
        'user_id' => is_admin() ? 0 : ($_SESSION['user_id'] ?? 0),
        'user_name' => is_admin() ? ('客服-' . ($_SESSION['admin_name'] ?? '管理员')) : ($_SESSION['user_name'] ?? ''),
        'is_admin' => is_admin() ? 1 : 0,
        'content' => $content,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $replies[] = $reply;

    // 更新工单状态和时间
    $tickets[$ticketIndex]['status'] = 'processing';
    $tickets[$ticketIndex]['updated_at'] = date('Y-m-d H:i:s');
    $tickets[$ticketIndex]['replies_count'] = ($tickets[$ticketIndex]['replies_count'] ?? 0) + 1;

    if (save_content('ticket_replies', $replies) && save_content('tickets', $tickets)) {
        $role = is_admin() ? '管理员' : '用户';
        log_message("{$role}回复了工单 #{$ticket['ticket_no']}");
        success($reply, '回复成功');
    } else {
        error('回复失败');
    }
}

/**
 * 关闭工单
 */
function closeTicket() {
    $ticketId = intval($_POST['ticket_id'] ?? 0);

    if ($ticketId <= 0) {
        error('缺少工单ID参数');
    }

    $tickets = get_content('tickets') ?: [];
    $ticket = null;
    $ticketIndex = -1;

    foreach ($tickets as $index => $t) {
        if ((int)$t['id'] === $ticketId) {
            $ticket = $t;
            $ticketIndex = $index;
            break;
        }
    }

    if (!$ticket) {
        error('工单不存在');
    }

    // 权限检查
    if (!is_admin() && (int)($ticket['user_id'] ?? 0) !== (int)($_SESSION['user_id'] ?? 0)) {
        error('无权操作此工单', 403);
    }

    if ($ticket['status'] === 'closed') {
        error('工单已经是关闭状态');
    }

    $tickets[$ticketIndex]['status'] = 'closed';
    $tickets[$ticketIndex]['closed_at'] = date('Y-m-d H:i:s');
    $tickets[$ticketIndex]['updated_at'] = date('Y-m-d H:i:s');

    if (save_content('tickets', $tickets)) {
        $role = is_admin() ? '管理员' : '用户';
        log_message("{$role}关闭了工单 #{$ticket['ticket_no']}");
        success(null, '工单已关闭');
    } else {
        error('操作失败');
    }
}

// 路由分发
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'detail' || !empty($_GET['id'])) {
            getTicketDetail();
        } else {
            getTicketList();
        }
        break;

    case 'POST':
        switch ($action) {
            case 'create':
                createTicket();
                break;
            case 'reply':
                replyTicket();
                break;
            case 'close':
                closeTicket();
                break;
            default:
                error('未知的操作类型');
        }
        break;

    default:
        error('不支持的请求方法');
}
