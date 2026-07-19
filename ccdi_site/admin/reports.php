<?php
/**
 * 后台管理 - 举报管理
 * 支持列表、查看、处理、删除操作
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
        $report = db_fetch("SELECT * FROM reports WHERE id = ?", [$id]);
        if (!$report) {
            $error = '举报不存在';
        } else {
            if (!empty($report['attachment'])) {
                $attach_path = UPLOADS_PATH . $report['attachment'];
                if (file_exists($attach_path)) {
                    @unlink($attach_path);
                }
            }
            $result = db_delete('reports', 'id = ?', [$id]);
            if ($result !== false) {
                add_log('report_delete', "删除举报：{$report['title']}");
                $message = '举报删除成功';
                $action = 'list';
            } else {
                $error = '删除失败，请稍后重试';
            }
        }
    }
}

// ==================== 处理操作 ====================
if ($action === 'handle') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $error = '无效的请求方式';
    } elseif (!csrf_verify(post('csrf_token', ''))) {
        $error = '安全验证失败，请刷新页面后重试';
    } elseif ($id <= 0) {
        $error = '缺少举报ID';
    } else {
        $report = db_fetch("SELECT * FROM reports WHERE id = ?", [$id]);
        if (!$report) {
            $error = '举报不存在';
        } else {
            $handle_remark = trim($_POST['handle_remark'] ?? '');
            $reply = trim($_POST['reply'] ?? '');
            $status = in_array(post('status', 'pending'), ['pending', 'processing', 'resolved', 'dismissed']) ? post('status') : 'pending';

            if (empty($handle_remark)) {
                $error = '请填写处理备注';
            } else {
                $data = [
                    'status' => $status,
                    'handle_remark' => $handle_remark,
                    'reply' => $reply,
                    'handled_at' => date('Y-m-d H:i:s')
                ];
                $result = db_update('reports', $data, 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('report_handle', "处理举报：{$report['title']} -> {$status}");
                    $message = '举报处理成功';
                    $action = 'list';
                } else {
                    $error = '处理失败，请稍后重试';
                }
            }
        }
    }
}

// ==================== 查看详情 ====================
if ($action === 'view' && $id > 0) {
    $report = db_fetch("SELECT * FROM reports WHERE id = ?", [$id]);
    if (!$report) {
        $error = '举报不存在';
        $action = 'list';
    }
}

// ==================== 列表视图 ====================
if ($action === 'list') {
    $total = db_count('reports');
    $offset = ($page - 1) * $per_page;
    $reports = db_fetch_all(
        "SELECT * FROM reports ORDER BY id DESC LIMIT ? OFFSET ?",
        [$per_page, $offset]
    );

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title">举报管理</h2>
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
                    <th width="80">举报人</th>
                    <th>标题</th>
                    <th width="80">举报类型</th>
                    <th width="80">状态</th>
                    <th width="140">举报时间</th>
                    <th width="160">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reports)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:#999;padding:40px;">暂无举报数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reports as $r): ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo htmlspecialchars($r['name'] ?: '匿名'); ?></td>
                        <td>
                            <a href="<?php echo admin_url('reports.php?action=view&id=' . $r['id']); ?>" title="<?php echo htmlspecialchars($r['title']); ?>">
                                <?php echo htmlspecialchars(str_cut($r['title'], 40)); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($r['report_type']); ?></td>
                        <td>
                            <?php
                            $status_label = '';
                            $status_class = '';
                            switch ($r['status']) {
                                case 'pending':
                                    $status_label = '待处理';
                                    $status_class = 'badge-danger';
                                    break;
                                case 'processing':
                                    $status_label = '处理中';
                                    $status_class = 'badge-warning';
                                    break;
                                case 'resolved':
                                    $status_label = '已处理';
                                    $status_class = 'badge-success';
                                    break;
                                case 'dismissed':
                                    $status_label = '已驳回';
                                    $status_class = 'badge-disabled';
                                    break;
                                default:
                                    $status_label = $r['status'];
                                    $status_class = 'badge-disabled';
                            }
                            ?>
                            <span class="badge <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                        </td>
                        <td><?php echo format_time($r['created_at']); ?></td>
                        <td class="table-actions">
                            <a href="<?php echo admin_url('reports.php?action=view&id=' . $r['id']); ?>" class="btn btn-sm btn-info" title="查看">
                                <i class="fas fa-eye"></i> 查看
                            </a>
                            <form method="post" action="<?php echo admin_url('reports.php?action=delete&id=' . $r['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除举报「<?php echo htmlspecialchars(addslashes($r['title'])); ?>」吗？此操作不可恢复。');">
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
    echo pagination($total, $page, admin_url('reports.php?'), $per_page);
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
    .badge-warning { background: #fff7e6; color: #fa8c16; }
    .badge-success { background: #f6ffed; color: #52c41a; }
    .badge-info { background: #e6f7ff; color: #1890ff; }
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
        <h2 class="admin-page-title">举报详情</h2>
        <a href="<?php echo admin_url('reports.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> 返回列表
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="detail-card">
        <div class="detail-header">
            <h3><?php echo htmlspecialchars($report['title']); ?></h3>
            <span class="badge <?php
                switch ($report['status']) {
                    case 'pending': echo 'badge-danger'; break;
                    case 'processing': echo 'badge-warning'; break;
                    case 'resolved': echo 'badge-success'; break;
                    case 'dismissed': echo 'badge-disabled'; break;
                    default: echo 'badge-disabled';
                }
            ?>">
                <?php
                switch ($report['status']) {
                    case 'pending': echo '待处理'; break;
                    case 'processing': echo '处理中'; break;
                    case 'resolved': echo '已处理'; break;
                    case 'dismissed': echo '已驳回'; break;
                    default: echo htmlspecialchars($report['status']);
                }
                ?>
            </span>
        </div>

        <div class="detail-info">
            <div class="info-row">
                <span class="info-label">举报人：</span>
                <span class="info-value"><?php echo htmlspecialchars($report['name'] ?: '匿名'); ?></span>
            </div>
            <?php if (!empty($report['email'])): ?>
            <div class="info-row">
                <span class="info-label">邮箱：</span>
                <span class="info-value"><?php echo htmlspecialchars($report['email']); ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($report['phone'])): ?>
            <div class="info-row">
                <span class="info-label">电话：</span>
                <span class="info-value"><?php echo htmlspecialchars($report['phone']); ?></span>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="info-label">举报类型：</span>
                <span class="info-value"><?php echo htmlspecialchars($report['report_type']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">举报时间：</span>
                <span class="info-value"><?php echo format_time($report['created_at']); ?></span>
            </div>
            <?php if (!empty($report['ip_address'])): ?>
            <div class="info-row">
                <span class="info-label">IP地址：</span>
                <span class="info-value"><?php echo htmlspecialchars($report['ip_address']); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="detail-content">
            <h4>举报内容</h4>
            <div class="content-box"><?php echo nl2br(htmlspecialchars($report['content'])); ?></div>
        </div>

        <?php if (!empty($report['attachment'])): ?>
        <div class="detail-attachment">
            <h4>附件</h4>
            <div class="attachment-box">
                <a href="<?php echo site_url('uploads/' . $report['attachment']); ?>" target="_blank" class="attachment-link">
                    <i class="fas fa-paperclip"></i>
                    <?php echo htmlspecialchars(basename($report['attachment'])); ?>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($report['handle_remark'])): ?>
        <div class="detail-handle">
            <h4>处理记录</h4>
            <div class="handle-meta">
                处理时间：<?php echo format_time($report['handled_at']); ?>
                &nbsp;|&nbsp;
                状态：<?php
                switch ($report['status']) {
                    case 'pending': echo '待处理'; break;
                    case 'processing': echo '处理中'; break;
                    case 'resolved': echo '已处理'; break;
                    case 'dismissed': echo '已驳回'; break;
                }
                ?>
            </div>
            <div class="content-box handle-box"><?php echo nl2br(htmlspecialchars($report['handle_remark'])); ?></div>
            <?php if (!empty($report['reply'])): ?>
            <div class="handle-reply">
                <h4>回复内容</h4>
                <div class="content-box reply-box"><?php echo nl2br(htmlspecialchars($report['reply'])); ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="handle-form-card">
        <h4><?php echo !empty($report['handle_remark']) ? '更新处理' : '处理举报'; ?></h4>
        <form method="post" action="<?php echo admin_url('reports.php?action=handle&id=' . $report['id']); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-row">
                <div class="form-group">
                    <label for="handle_status">处理状态 <span class="required">*</span></label>
                    <select id="handle_status" name="status">
                        <option value="pending" <?php echo $report['status'] === 'pending' ? 'selected' : ''; ?>>待处理</option>
                        <option value="processing" <?php echo $report['status'] === 'processing' ? 'selected' : ''; ?>>处理中</option>
                        <option value="resolved" <?php echo $report['status'] === 'resolved' ? 'selected' : ''; ?>>已处理</option>
                        <option value="dismissed" <?php echo $report['status'] === 'dismissed' ? 'selected' : ''; ?>>已驳回</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="handle_remark">处理备注 <span class="required">*</span></label>
                <textarea id="handle_remark" name="handle_remark" rows="4" placeholder="请输入处理备注信息..." required><?php echo htmlspecialchars($report['handle_remark'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="reply">回复内容</label>
                <textarea id="reply" name="reply" rows="4" placeholder="回复给举报人的内容（可选）"><?php echo htmlspecialchars($report['reply'] ?? ''); ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> <?php echo !empty($report['handle_remark']) ? '更新处理' : '提交处理'; ?>
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
    .badge-warning { background: #fff7e6; color: #fa8c16; }
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
    .detail-attachment h4,
    .detail-handle h4,
    .handle-reply h4 { font-size: 15px; color: #555; margin-bottom: 10px; }
    .content-box { background: #fafafa; border: 1px solid #f0f0f0; border-radius: 4px; padding: 16px; font-size: 14px; line-height: 1.8; color: #333; word-break: break-all; }
    .handle-box { background: #fffbe6; border-color: #ffe58f; }
    .reply-box { background: #f6ffed; border-color: #b7eb8f; }
    .detail-attachment { margin-bottom: 20px; }
    .attachment-box { background: #fafafa; border: 1px solid #f0f0f0; border-radius: 4px; padding: 12px 16px; }
    .attachment-link { color: #c41230; text-decoration: none; font-size: 14px; }
    .attachment-link:hover { text-decoration: underline; }
    .detail-handle { margin-bottom: 20px; }
    .handle-meta { font-size: 12px; color: #999; margin-bottom: 8px; }
    .handle-reply { margin-top: 16px; }
    .handle-form-card { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 24px; }
    .handle-form-card h4 { font-size: 15px; color: #555; margin-bottom: 16px; }
    .form-row { display: flex; gap: 20px; margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 6px; }
    .form-group select,
    .form-group textarea { width: 100%; padding: 9px 12px; border: 1px solid #d9d9d9; border-radius: 4px; font-size: 14px; font-family: inherit; transition: border-color 0.3s; box-sizing: border-box; }
    .form-group select:focus,
    .form-group textarea:focus { border-color: #c41230; outline: none; box-shadow: 0 0 0 2px rgba(196,18,48,0.1); }
    .form-group textarea { resize: vertical; }
    .required { color: #c41230; }
    .form-actions { display: flex; gap: 12px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin_url('reports.php'));