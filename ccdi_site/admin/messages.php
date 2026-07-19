<?php
/**
 * 后台管理 - 留言管理
 * 支持列表、查看、回复、删除操作
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/../includes/init.php';
require_admin();

$action = get('action', 'list');
$id = (int)get('id', 0);
$page = max(1, (int)get('page', 1));
$per_page = ADMIN_ITEMS_PER_PAGE;
$message = '';
$error = '';

// ==================== 删除操作 ====================
if ($action === 'delete' && $id > 0) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $error = '无效的请求方式';
    } elseif (!csrf_verify(post('csrf_token', ''))) {
        $error = '安全验证失败，请刷新页面后重试';
    } else {
        $msg = db_fetch("SELECT * FROM messages WHERE id = ?", [$id]);
        if (!$msg) {
            $error = '留言不存在';
        } else {
            $result = db_delete('messages', 'id = ?', [$id]);
            if ($result !== false) {
                add_log('message_delete', "删除留言：{$msg['title']}");
                $message = '留言删除成功';
                $action = 'list';
            } else {
                $error = '删除失败，请稍后重试';
            }
        }
    }
}

// ==================== 回复操作 ====================
if ($action === 'reply') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $error = '无效的请求方式';
    } elseif (!csrf_verify(post('csrf_token', ''))) {
        $error = '安全验证失败，请刷新页面后重试';
    } elseif ($id <= 0) {
        $error = '缺少留言ID';
    } else {
        $msg = db_fetch("SELECT * FROM messages WHERE id = ?", [$id]);
        if (!$msg) {
            $error = '留言不存在';
        } else {
            $reply_content = trim($_POST['reply'] ?? '');
            if (empty($reply_content)) {
                $error = '请输入回复内容';
            } else {
                $result = db_update('messages', [
                    'reply' => $reply_content,
                    'status' => 'replied',
                    'replied_at' => date('Y-m-d H:i:s')
                ], 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('message_reply', "回复留言：{$msg['title']}");
                    $message = '留言回复成功';
                    $action = 'list';
                } else {
                    $error = '回复失败，请稍后重试';
                }
            }
        }
    }
}

// ==================== 查看详情 ====================
if ($action === 'view' && $id > 0) {
    $msg = db_fetch("SELECT * FROM messages WHERE id = ?", [$id]);
    if (!$msg) {
        $error = '留言不存在';
        $action = 'list';
    } else {
        // 标记为已读
        if ($msg['status'] === 'unread') {
            db_update('messages', ['status' => 'read'], 'id = ?', [$id]);
        }
    }
}

// ==================== 列表视图 ====================
if ($action === 'list') {
    $total = db_count('messages');
    $offset = ($page - 1) * $per_page;
    $messages = db_fetch_all(
        "SELECT * FROM messages ORDER BY id DESC LIMIT ? OFFSET ?",
        [$per_page, $offset]
    );

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title">留言管理</h2>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="40">ID</th>
                    <th width="80">姓名</th>
                    <th>标题</th>
                    <th width="100">状态</th>
                    <th width="140">留言时间</th>
                    <th width="160">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:#999;padding:40px;">暂无留言数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($messages as $m): ?>
                    <tr>
                        <td><?php echo $m['id']; ?></td>
                        <td><?php echo htmlspecialchars($m['name'] ?: '匿名'); ?></td>
                        <td>
                            <a href="<?php echo admin_url('messages.php?action=view&id=' . $m['id']); ?>" title="<?php echo htmlspecialchars($m['title']); ?>">
                                <?php echo htmlspecialchars(str_cut($m['title'], 40)); ?>
                            </a>
                        </td>
                        <td>
                            <?php
                            $status_label = '';
                            $status_class = '';
                            switch ($m['status']) {
                                case 'unread':
                                    $status_label = '未读';
                                    $status_class = 'badge-danger';
                                    break;
                                case 'read':
                                    $status_label = '已读';
                                    $status_class = 'badge-info';
                                    break;
                                case 'replied':
                                    $status_label = '已回复';
                                    $status_class = 'badge-success';
                                    break;
                                default:
                                    $status_label = $m['status'];
                                    $status_class = 'badge-disabled';
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                        </td>
                        <td><?php echo format_time($m['created_at']); ?></td>
                        <td class="table-actions">
                            <a href="<?php echo admin_url('messages.php?action=view&id=' . $m['id']); ?>" class="btn btn-sm btn-info" title="查看">
                                <i class="fas fa-eye"></i> 查看
                            </a>
                            <form method="post" action="<?php echo admin_url('messages.php?action=delete&id=' . $m['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除留言「<?php echo htmlspecialchars(addslashes($m['title'])); ?>」吗？此操作不可恢复。');">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-danger" title="删除">
                                    <i class="fas fa-trash"></i> 删除
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    echo pagination($total, $page, admin_url('messages.php?'), $per_page);
    ?>

    <style>
    .admin-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .admin-page-title { margin: 0; font-size: 20px; color: #333; }
    .btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-sm { padding: 4px 12px; font-size: 12px; }
    .btn-info { background: #1890ff; color: #fff; }
    .btn-info:hover { background: #1476d6; }
    .btn-danger { background: #ff4d4f; color: #fff; }
    .btn-danger:hover { background: #e04345; }
    .btn-primary { background: #c41230; color: #fff; }
    .btn-primary:hover { background: #a00e28; }
    .btn-secondary { background: #f0f0f0; color: #333; }
    .btn-secondary:hover { background: #e0e0e0; }
    .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
    .alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-danger { background: #fff2f0; color: #ff4d4f; }
    .badge-info { background: #e6f7ff; color: #1890ff; }
    .badge-success { background: #f6ffed; color: #52c41a; }
    .badge-disabled { background: #f5f5f5; color: #999; }
    .table-container { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 20px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #fafafa; padding: 12px 14px; text-align: left; font-size: 13px; font-weight: 600; color: #555; border-bottom: 1px solid #e8e8e8; }
    .data-table td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f0f0f0; color: #333; vertical-align: middle; }
    .data-table tr:hover td { background: #fafafa; }
    .data-table a { color: #c41230; text-decoration: none; }
    .data-table a:hover { text-decoration: underline; }
    .table-actions { white-space: nowrap; }
    .table-actions form { display: inline-block; }
    .pagination { text-align: center; margin-top: 20px; }
    .pagination ul { display: inline-flex; list-style: none; padding: 0; margin: 0; gap: 4px; }
    .pagination li { display: inline; }
    .pagination a, .pagination span { display: inline-block; padding: 6px 12px; border-radius: 4px; font-size: 13px; color: #333; text-decoration: none; border: 1px solid #d9d9d9; background: #fff; }
    .pagination a:hover { border-color: #c41230; color: #c41230; }
    .pagination .active span { background: #c41230; color: #fff; border-color: #c41230; }
    .pagination li span { border: 1px solid #d9d9d9; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 查看详情 ====================
if ($action === 'view' && $id > 0) {
    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title">留言详情</h2>
        <a href="<?php echo admin_url('messages.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> 返回列表
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="detail-card">
        <div class="detail-header">
            <h3><?php echo htmlspecialchars($msg['title']); ?></h3>
            <span class="badge <?php
                switch ($msg['status']) {
                    case 'unread': echo 'badge-danger'; break;
                    case 'read': echo 'badge-info'; break;
                    case 'replied': echo 'badge-success'; break;
                    default: echo 'badge-disabled';
                }
            ?>">
                <?php
                switch ($msg['status']) {
                    case 'unread': echo '未读'; break;
                    case 'read': echo '已读'; break;
                    case 'replied': echo '已回复'; break;
                    default: echo htmlspecialchars($msg['status']);
                }
                ?>
            </span>
        </div>

        <div class="detail-info">
            <div class="info-row">
                <span class="info-label">留言人：</span>
                <span class="info-value"><?php echo htmlspecialchars($msg['name'] ?: '匿名'); ?></span>
            </div>
            <?php if (!empty($msg['email'])): ?>
            <div class="info-row">
                <span class="info-label">邮箱：</span>
                <span class="info-value"><?php echo htmlspecialchars($msg['email']); ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($msg['phone'])): ?>
            <div class="info-row">
                <span class="info-label">电话：</span>
                <span class="info-value"><?php echo htmlspecialchars($msg['phone']); ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($msg['type'])): ?>
            <div class="info-row">
                <span class="info-label">留言类型：</span>
                <span class="info-value"><?php echo htmlspecialchars($msg['type']); ?></span>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="info-label">留言时间：</span>
                <span class="info-value"><?php echo format_time($msg['created_at']); ?></span>
            </div>
            <?php if (!empty($msg['ip_address'])): ?>
            <div class="info-row">
                <span class="info-label">IP地址：</span>
                <span class="info-value"><?php echo htmlspecialchars($msg['ip_address']); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="detail-content">
            <h4>留言内容</h4>
            <div class="content-box"><?php echo nl2br(htmlspecialchars($msg['content'])); ?></div>
        </div>

        <?php if (!empty($msg['reply'])): ?>
        <div class="detail-reply">
            <h4>回复内容</h4>
            <div class="reply-meta">回复时间：<?php echo format_time($msg['replied_at']); ?></div>
            <div class="content-box reply-box"><?php echo nl2br(htmlspecialchars($msg['reply'])); ?></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="reply-form-card">
        <h4><?php echo !empty($msg['reply']) ? '修改回复' : '回复留言'; ?></h4>
        <form method="post" action="<?php echo admin_url('messages.php?action=reply&id=' . $msg['id']); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <textarea name="reply" rows="6" placeholder="请输入回复内容..." required><?php echo htmlspecialchars($msg['reply'] ?? ''); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-reply"></i> <?php echo !empty($msg['reply']) ? '更新回复' : '提交回复'; ?>
                </button>
            </div>
        </form>
    </div>

    <style>
    .admin-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .admin-page-title { margin: 0; font-size: 20px; color: #333; }
    .btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: #c41230; color: #fff; }
    .btn-primary:hover { background: #a00e28; }
    .btn-secondary { background: #f0f0f0; color: #333; }
    .btn-secondary:hover { background: #e0e0e0; }
    .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-danger { background: #fff2f0; color: #ff4d4f; }
    .badge-info { background: #e6f7ff; color: #1890ff; }
    .badge-success { background: #f6ffed; color: #52c41a; }
    .badge-disabled { background: #f5f5f5; color: #999; }
    .detail-card { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 24px; margin-bottom: 20px; }
    .detail-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #f0f0f0; }
    .detail-header h3 { margin: 0; font-size: 18px; color: #333; }
    .detail-info { margin-bottom: 20px; }
    .info-row { display: flex; padding: 6px 0; font-size: 14px; }
    .info-label { color: #888; width: 80px; flex-shrink: 0; }
    .info-value { color: #333; }
    .detail-content { margin-bottom: 20px; }
    .detail-content h4,
    .detail-reply h4 { font-size: 15px; color: #555; margin-bottom: 10px; }
    .content-box { background: #fafafa; border: 1px solid #f0f0f0; border-radius: 4px; padding: 16px; font-size: 14px; line-height: 1.8; color: #333; word-break: break-all; }
    .reply-box { background: #f6ffed; border-color: #b7eb8f; }
    .detail-reply { margin-bottom: 20px; }
    .reply-meta { font-size: 12px; color: #999; margin-bottom: 8px; }
    .reply-form-card { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 24px; }
    .reply-form-card h4 { font-size: 15px; color: #555; margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-group textarea { width: 100%; padding: 9px 12px; border: 1px solid #d9d9d9; border-radius: 4px; font-size: 14px; font-family: inherit; transition: border-color 0.3s; box-sizing: border-box; resize: vertical; }
    .form-group textarea:focus { border-color: #c41230; outline: none; box-shadow: 0 0 0 2px rgba(196,18,48,0.1); }
    .form-actions { display: flex; gap: 12px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin_url('messages.php'));