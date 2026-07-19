<?php
/**
 * 后台管理 - 弹窗管理
 * 支持列表、添加、编辑、删除操作
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
        $popup = db_fetch("SELECT * FROM popups WHERE id = ?", [$id]);
        if (!$popup) {
            $error = '弹窗不存在';
        } else {
            if (!empty($popup['image'])) {
                $image_path = UPLOADS_PATH . $popup['image'];
                if (file_exists($image_path)) {
                    @unlink($image_path);
                }
            }
            $result = db_delete('popups', 'id = ?', [$id]);
            if ($result !== false) {
                add_log('popup_delete', "删除弹窗：{$popup['title']}");
                $message = '弹窗删除成功';
                $action = 'list';
            } else {
                $error = '删除失败，请稍后重试';
            }
        }
    }
}

// ==================== 保存操作（添加/编辑） ====================
if ($action === 'save') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $error = '无效的请求方式';
    } elseif (!csrf_verify(post('csrf_token', ''))) {
        $error = '安全验证失败，请刷新页面后重试';
    } else {
        $title = trim(post('title', ''));
        $content = trim($_POST['content'] ?? '');
        $link = trim(post('link', ''));
        $start_time = trim(post('start_time', ''));
        $end_time = trim(post('end_time', ''));
        $status = post('status', '1') === '1' ? 1 : 0;
        $image = '';

        if (empty($title)) {
            $error = '请输入弹窗标题';
        }

        // 处理图片上传
        if (empty($error) && isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_file($_FILES['image_file'], 'popups');
            if (isset($upload_result['success']) && $upload_result['success']) {
                if ($id > 0) {
                    $old_popup = db_fetch("SELECT image FROM popups WHERE id = ?", [$id]);
                    if ($old_popup && !empty($old_popup['image'])) {
                        $old_path = UPLOADS_PATH . $old_popup['image'];
                        if (file_exists($old_path)) {
                            @unlink($old_path);
                        }
                    }
                }
                $image = $upload_result['path'];
            } elseif (isset($upload_result['error'])) {
                $error = $upload_result['error'];
            }
        }

        if (empty($error) && empty($image) && $id > 0) {
            $existing = db_fetch("SELECT image FROM popups WHERE id = ?", [$id]);
            if ($existing) {
                $image = $existing['image'];
            }
        }

        if (empty($error)) {
            $data = [
                'title' => $title,
                'content' => $content,
                'image' => $image,
                'link' => $link,
                'start_time' => !empty($start_time) ? $start_time : null,
                'end_time' => !empty($end_time) ? $end_time : null,
                'status' => $status
            ];

            if ($id > 0) {
                $result = db_update('popups', $data, 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('popup_update', "更新弹窗：{$title}");
                    $message = '弹窗更新成功';
                } else {
                    $error = '更新失败，请稍后重试';
                }
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = db_insert('popups', $data);
                if ($new_id) {
                    add_log('popup_create', "创建弹窗：{$title}");
                    $message = '弹窗添加成功';
                    $id = $new_id;
                } else {
                    $error = '添加失败，请稍后重试';
                }
            }
        }

        if (empty($error)) {
            $action = 'list';
        } else {
            $action = ($id > 0) ? 'edit' : 'add';
        }
    }
}

// ==================== 列表视图 ====================
if ($action === 'list') {
    $total = db_count('popups');
    $offset = ($page - 1) * $per_page;
    $popups = db_fetch_all(
        "SELECT * FROM popups ORDER BY id DESC LIMIT ? OFFSET ?",
        [$per_page, $offset]
    );

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title">弹窗管理</h2>
        <a href="<?php echo admin_url('popups.php?action=add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加弹窗
        </a>
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
                    <th width="100">缩略图</th>
                    <th>标题</th>
                    <th width="110">开始时间</th>
                    <th width="110">结束时间</th>
                    <th width="70">状态</th>
                    <th width="150">创建时间</th>
                    <th width="120">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($popups)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:#999;padding:40px;">暂无弹窗数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($popups as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td>
                            <?php if (!empty($p['image'])): ?>
                                <img src="<?php echo site_url('uploads/' . $p['image']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="thumbnail-preview">
                            <?php else: ?>
                                <span class="no-image">无图片</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('popups.php?action=edit&id=' . $p['id']); ?>" title="<?php echo htmlspecialchars($p['title']); ?>">
                                <?php echo htmlspecialchars(str_cut($p['title'], 40)); ?>
                            </a>
                            <?php if (!empty($p['link'])): ?>
                                <span class="has-link" title="链接：<?php echo htmlspecialchars($p['link']); ?>"><i class="fas fa-link"></i></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo !empty($p['start_time']) ? format_time($p['start_time']) : '-'; ?></td>
                        <td><?php echo !empty($p['end_time']) ? format_time($p['end_time']) : '-'; ?></td>
                        <td>
                            <span class="badge <?php echo $p['status'] ? 'badge-success' : 'badge-disabled'; ?>">
                                <?php echo $p['status'] ? '启用' : '禁用'; ?>
                            </span>
                        </td>
                        <td><?php echo format_time($p['created_at']); ?></td>
                        <td class="table-actions">
                            <a href="<?php echo admin_url('popups.php?action=edit&id=' . $p['id']); ?>" class="btn btn-sm btn-secondary" title="编辑">
                                <i class="fas fa-edit"></i> 编辑
                            </a>
                            <form method="post" action="<?php echo admin_url('popups.php?action=delete&id=' . $p['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除弹窗「<?php echo htmlspecialchars(addslashes($p['title'])); ?>」吗？此操作不可恢复。');">
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
    echo pagination($total, $page, admin_url('popups.php?'), $per_page);
    ?>

    <style>
    .admin-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .admin-page-title { margin: 0; font-size: 20px; color: #333; }
    .btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: #c41230; color: #fff; }
    .btn-primary:hover { background: #a00e28; }
    .btn-secondary { background: #f0f0f0; color: #333; }
    .btn-secondary:hover { background: #e0e0e0; }
    .btn-danger { background: #ff4d4f; color: #fff; }
    .btn-danger:hover { background: #e04345; }
    .btn-sm { padding: 4px 12px; font-size: 12px; }
    .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
    .alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
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
    .thumbnail-preview { width: 80px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #e8e8e8; }
    .no-image { color: #ccc; font-size: 12px; }
    .has-link { color: #c41230; margin-left: 6px; font-size: 12px; cursor: help; }
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

// ==================== 添加/编辑表单 ====================
if ($action === 'add' || $action === 'edit') {
    $popup = [
        'id' => 0,
        'title' => '',
        'content' => '',
        'image' => '',
        'link' => '',
        'start_time' => '',
        'end_time' => '',
        'status' => 1,
    ];

    if ($action === 'edit') {
        if ($id <= 0) {
            $error = '缺少弹窗ID';
        } else {
            $existing = db_fetch("SELECT * FROM popups WHERE id = ?", [$id]);
            if (!$existing) {
                $error = '弹窗不存在';
            } else {
                $popup = $existing;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $popup['title'] = post('title', $popup['title']);
        $popup['content'] = $_POST['content'] ?? $popup['content'];
        $popup['link'] = post('link', $popup['link']);
        $popup['start_time'] = post('start_time', $popup['start_time']);
        $popup['end_time'] = post('end_time', $popup['end_time']);
        $popup['status'] = post('status', '1') === '1' ? 1 : 0;
    }

    $is_edit = ($action === 'edit' && $popup['id'] > 0);
    $form_title = $is_edit ? '编辑弹窗' : '添加弹窗';

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title"><?php echo $form_title; ?></h2>
        <a href="<?php echo admin_url('popups.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> 返回列表
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo admin_url('popups.php?action=save' . ($is_edit ? '&id=' . $popup['id'] : '')); ?>" enctype="multipart/form-data" class="popup-form">
        <?php echo csrf_field(); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $popup['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="title">弹窗标题 <span class="required">*</span></label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($popup['title']); ?>" required placeholder="请输入弹窗标题" maxlength="200">
            </div>
            <div class="form-group form-group-side">
                <label for="status">状态</label>
                <select id="status" name="status">
                    <option value="1" <?php echo $popup['status'] == 1 ? 'selected' : ''; ?>>启用</option>
                    <option value="0" <?php echo $popup['status'] == 0 ? 'selected' : ''; ?>>禁用</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="content">弹窗内容</label>
            <textarea id="content" name="content" rows="6" placeholder="请输入弹窗显示的文本内容（支持HTML）"><?php echo htmlspecialchars($popup['content']); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="link">链接地址</label>
                <input type="text" id="link" name="link" value="<?php echo htmlspecialchars($popup['link']); ?>" placeholder="如：https://..." maxlength="500">
                <span class="form-hint">点击弹窗后跳转的链接地址</span>
            </div>
            <div class="form-group form-group-side">
                <label for="image_file">弹窗图片</label>
                <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/gif,image/webp">
                <span class="form-hint">支持 JPG、PNG、GIF、WebP</span>
                <?php if ($is_edit && !empty($popup['image'])): ?>
                    <div class="current-image">
                        <span>当前图片：</span>
                        <img src="<?php echo site_url('uploads/' . $popup['image']); ?>" alt="弹窗图片预览" class="image-preview">
                        <span class="form-hint">上传新图片将替换当前图片</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="start_time">开始时间</label>
                <input type="datetime-local" id="start_time" name="start_time" value="<?php echo !empty($popup['start_time']) ? date('Y-m-d\TH:i', strtotime($popup['start_time'])) : ''; ?>">
                <span class="form-hint">留空表示立即开始</span>
            </div>
            <div class="form-group">
                <label for="end_time">结束时间</label>
                <input type="datetime-local" id="end_time" name="end_time" value="<?php echo !empty($popup['end_time']) ? date('Y-m-d\TH:i', strtotime($popup['end_time'])) : ''; ?>">
                <span class="form-hint">留空表示永久有效</span>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php echo $is_edit ? '更新弹窗' : '添加弹窗'; ?>
            </button>
            <a href="<?php echo admin_url('popups.php'); ?>" class="btn btn-secondary">取消</a>
        </div>
    </form>

    <style>
    .admin-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .admin-page-title { margin: 0; font-size: 20px; color: #333; }
    .btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: #c41230; color: #fff; }
    .btn-primary:hover { background: #a00e28; }
    .btn-secondary { background: #f0f0f0; color: #333; }
    .btn-secondary:hover { background: #e0e0e0; }
    .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
    .alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .popup-form { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 24px; }
    .form-row { display: flex; gap: 20px; margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; flex: 1; }
    .form-group-main { flex: 2; }
    .form-group-side { flex: 1; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 6px; }
    .form-group input[type="text"],
    .form-group input[type="datetime-local"],
    .form-group select,
    .form-group textarea { width: 100%; padding: 9px 12px; border: 1px solid #d9d9d9; border-radius: 4px; font-size: 14px; font-family: inherit; transition: border-color 0.3s; box-sizing: border-box; }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color: #c41230; outline: none; box-shadow: 0 0 0 2px rgba(196,18,48,0.1); }
    .form-group textarea { resize: vertical; }
    .form-group input[type="file"] { padding: 6px 0; }
    .form-hint { display: block; font-size: 12px; color: #999; margin-top: 4px; }
    .required { color: #c41230; }
    .current-image { margin-top: 12px; }
    .image-preview { display: block; max-width: 300px; max-height: 180px; border: 1px solid #e8e8e8; border-radius: 4px; margin-top: 8px; }
    .form-actions { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 12px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin_url('popups.php'));