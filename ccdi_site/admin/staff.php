<?php
/**
 * 后台管理 - 人员管理 v10.0.0
 * 管理网站工作人员/职工信息
 * 支持姓名、职务、头像、部门、简介、排序等功能
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
        $staff = db_fetch("SELECT * FROM staff WHERE id = ?", [$id]);
        if (!$staff) {
            $error = '人员不存在';
        } else {
            if (!empty($staff['avatar'])) {
                $avatar_path = UPLOADS_PATH . $staff['avatar'];
                if (file_exists($avatar_path)) {
                    @unlink($avatar_path);
                }
            }
            $result = db_delete('staff', 'id = ?', [$id]);
            if ($result !== false) {
                add_log('staff_delete', "删除人员：{$staff['name']}");
                $message = '人员删除成功';
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
        $name = trim(post('name', ''));
        $title = trim(post('title', ''));
        $department = trim(post('department', ''));
        $bio = trim(post('bio', ''));
        $sort_order = (int)post('sort_order', 0);
        $status = post('status', '1') === '1' ? 1 : 0;
        $avatar = '';

        if (empty($name)) {
            $error = '请输入姓名';
        }

        if (empty($error) && empty($title)) {
            $error = '请输入职务';
        }

        // 处理头像上传
        if (empty($error) && isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_file($_FILES['avatar_file'], 'staff');
            if (isset($upload_result['success']) && $upload_result['success']) {
                if ($id > 0) {
                    $old_staff = db_fetch("SELECT avatar FROM staff WHERE id = ?", [$id]);
                    if ($old_staff && !empty($old_staff['avatar'])) {
                        $old_path = UPLOADS_PATH . $old_staff['avatar'];
                        if (file_exists($old_path)) {
                            @unlink($old_path);
                        }
                    }
                }
                $avatar = $upload_result['path'];
            } elseif (isset($upload_result['error'])) {
                $error = $upload_result['error'];
            }
        }

        // 编辑模式下如果没有上传新头像，保留原头像
        if (empty($error) && empty($avatar) && $id > 0) {
            $existing = db_fetch("SELECT avatar FROM staff WHERE id = ?", [$id]);
            if ($existing && !empty($existing['avatar'])) {
                $avatar = $existing['avatar'];
            }
        }

        if (empty($error)) {
            $data = [
                'name' => $name,
                'title' => $title,
                'avatar' => $avatar,
                'department' => $department,
                'bio' => $bio,
                'sort_order' => $sort_order,
                'status' => $status
            ];

            if ($id > 0) {
                $result = db_update('staff', $data, 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('staff_update', "更新人员：{$name}");
                    $message = '人员信息更新成功';
                } else {
                    $error = '更新失败，请稍后重试';
                }
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = db_insert('staff', $data);
                if ($new_id) {
                    add_log('staff_create', "创建人员：{$name}");
                    $message = '人员添加成功';
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
    $total = db_count('staff');
    $offset = ($page - 1) * $per_page;
    $staff_list = db_fetch_all(
        "SELECT * FROM staff ORDER BY sort_order ASC, id DESC LIMIT ? OFFSET ?",
        [$per_page, $offset]
    );

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3>人员管理</h3>
            <a href="<?php echo admin_url('staff.php?action=add'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> 添加人员
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
                        <th width="80">头像</th>
                        <th>姓名</th>
                        <th>职务</th>
                        <th width="100">部门</th>
                        <th width="70">状态</th>
                        <th width="150">创建时间</th>
                        <th width="120">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($staff_list)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;color:#999;padding:40px;">暂无人员数据</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($staff_list as $s): ?>
                        <tr>
                            <td><?php echo $s['id']; ?></td>
                            <td>
                                <?php if (!empty($s['avatar'])): ?>
                                    <img src="<?php echo site_url('uploads/' . $s['avatar']); ?>" alt="<?php echo htmlspecialchars($s['name']); ?>" class="avatar-thumb">
                                <?php else: ?>
                                    <div class="avatar-placeholder"><?php echo mb_substr($s['name'], 0, 1, 'UTF-8'); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo admin_url('staff.php?action=edit&id=' . $s['id']); ?>" title="<?php echo htmlspecialchars($s['name']); ?>">
                                    <?php echo htmlspecialchars($s['name']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($s['title']); ?></td>
                            <td><?php echo htmlspecialchars($s['department']); ?></td>
                            <td>
                                <span class="badge <?php echo $s['status'] ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $s['status'] ? '启用' : '禁用'; ?>
                                </span>
                            </td>
                            <td><?php echo format_time($s['created_at']); ?></td>
                            <td class="table-actions">
                                <a href="<?php echo admin_url('staff.php?action=edit&id=' . $s['id']); ?>" class="btn btn-sm btn-secondary" title="编辑">
                                    <i class="fas fa-edit"></i> 编辑
                                </a>
                                <form method="post" action="<?php echo admin_url('staff.php?action=delete&id=' . $s['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除人员「<?php echo htmlspecialchars(addslashes($s['name'])); ?>」吗？此操作不可恢复。');">
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

        <?php echo pagination($total, $page, admin_url('staff.php?'), $per_page); ?>
    </div>

    <style>
    .admin-card { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 20px; }
    .admin-card-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f0f0f0; }
    .admin-card-header h3 { margin: 0; font-size: 18px; color: #333; }
    .btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: #c41230; color: #fff; }
    .btn-primary:hover { background: #a00e28; }
    .btn-secondary { background: #f0f0f0; color: #333; }
    .btn-secondary:hover { background: #e0e0e0; }
    .btn-danger { background: #ff4d4f; color: #fff; }
    .btn-danger:hover { background: #e04345; }
    .btn-sm { padding: 4px 12px; font-size: 12px; }
    .alert { padding: 12px 16px; margin: 16px 24px; border-radius: 4px; font-size: 14px; }
    .alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-success { background: #f6ffed; color: #52c41a; }
    .badge-warning { background: #fffbe6; color: #faad14; }
    .table-container { overflow-x: auto; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #fafafa; padding: 12px 14px; text-align: left; font-size: 13px; font-weight: 600; color: #555; border-bottom: 1px solid #e8e8e8; }
    .data-table td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f0f0f0; color: #333; vertical-align: middle; }
    .data-table tr:hover td { background: #fafafa; }
    .data-table a { color: #c41230; text-decoration: none; }
    .data-table a:hover { text-decoration: underline; }
    .table-actions { white-space: nowrap; }
    .table-actions form { display: inline-block; }
    .avatar-thumb { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #e8e8e8; display: block; }
    .avatar-placeholder { width: 60px; height: 60px; border-radius: 50%; background: #e8e8e8; color: #999; font-size: 20px; font-weight: 600; display: flex; align-items: center; justify-content: center; }
    .pagination { text-align: center; padding: 16px 24px; border-top: 1px solid #f0f0f0; }
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
    $staff = [
        'id' => 0,
        'name' => '',
        'title' => '',
        'avatar' => '',
        'department' => '',
        'bio' => '',
        'sort_order' => 0,
        'status' => 1,
    ];

    if ($action === 'edit') {
        if ($id <= 0) {
            $error = '缺少人员ID';
        } else {
            $existing = db_fetch("SELECT * FROM staff WHERE id = ?", [$id]);
            if (!$existing) {
                $error = '人员不存在';
            } else {
                $staff = $existing;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $staff['name'] = post('name', $staff['name']);
        $staff['title'] = post('title', $staff['title']);
        $staff['department'] = post('department', $staff['department']);
        $staff['bio'] = post('bio', $staff['bio']);
        $staff['sort_order'] = (int)post('sort_order', $staff['sort_order']);
        $staff['status'] = post('status', '1') === '1' ? 1 : 0;
    }

    $is_edit = ($action === 'edit' && $staff['id'] > 0);
    $form_title = $is_edit ? '编辑人员' : '添加人员';

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3><?php echo $form_title; ?></h3>
            <a href="<?php echo admin_url('staff.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> 返回列表
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo admin_url('staff.php?action=save' . ($is_edit ? '&id=' . $staff['id'] : '')); ?>" enctype="multipart/form-data" class="card-form">
            <?php echo csrf_field(); ?>
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $staff['id']; ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group form-group-main">
                    <label for="name">姓名 <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($staff['name']); ?>" required placeholder="请输入姓名" maxlength="50">
                </div>
                <div class="form-group form-group-side">
                    <label for="sort_order">排序</label>
                    <input type="number" id="sort_order" name="sort_order" value="<?php echo $staff['sort_order']; ?>" placeholder="数字越小越靠前" min="0" step="1">
                    <span class="form-hint">数字越小越靠前</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group-main">
                    <label for="title">职务 <span class="required">*</span></label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($staff['title']); ?>" required placeholder="请输入职务" maxlength="100">
                </div>
                <div class="form-group form-group-side">
                    <label for="status">状态</label>
                    <select id="status" name="status">
                        <option value="1" <?php echo $staff['status'] == 1 ? 'selected' : ''; ?>>启用</option>
                        <option value="0" <?php echo $staff['status'] == 0 ? 'selected' : ''; ?>>禁用</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="department">部门</label>
                <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($staff['department']); ?>" placeholder="请输入所属部门" maxlength="100">
            </div>

            <div class="form-group">
                <label for="bio">个人简介</label>
                <textarea id="bio" name="bio" rows="4" placeholder="人员的简要介绍（可选）"><?php echo htmlspecialchars($staff['bio']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="avatar_file">头像</label>
                <input type="file" id="avatar_file" name="avatar_file" accept="image/jpeg,image/png,image/gif,image/webp">
                <span class="form-hint">支持 JPG、PNG、GIF、WebP 格式，最大 10MB。建议尺寸：200×200 像素</span>
                <?php if ($is_edit && !empty($staff['avatar'])): ?>
                    <div class="current-image">
                        <span>当前头像：</span>
                        <img src="<?php echo site_url('uploads/' . $staff['avatar']); ?>" alt="头像预览" style="max-width:80px;max-height:80px;border-radius:50%;object-fit:cover;border:2px solid #e8e8e8;margin-top:8px;display:block;">
                        <span class="form-hint">上传新头像将替换当前头像</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $is_edit ? '更新人员' : '添加人员'; ?>
                </button>
                <a href="<?php echo admin_url('staff.php'); ?>" class="btn btn-secondary">取消</a>
            </div>
        </form>
    </div>

    <style>
    .admin-card { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 20px; }
    .admin-card-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f0f0f0; }
    .admin-card-header h3 { margin: 0; font-size: 18px; color: #333; }
    .btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: #c41230; color: #fff; }
    .btn-primary:hover { background: #a00e28; }
    .btn-secondary { background: #f0f0f0; color: #333; }
    .btn-secondary:hover { background: #e0e0e0; }
    .alert { padding: 12px 16px; margin: 16px 24px; border-radius: 4px; font-size: 14px; }
    .alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .card-form { padding: 24px; }
    .form-row { display: flex; gap: 20px; margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-group-main { flex: 1; }
    .form-group-side { width: 200px; flex-shrink: 0; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 6px; }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea { width: 100%; padding: 9px 12px; border: 1px solid #d9d9d9; border-radius: 4px; font-size: 14px; font-family: inherit; transition: border-color 0.3s; box-sizing: border-box; }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color: #c41230; outline: none; box-shadow: 0 0 0 2px rgba(196,18,48,0.1); }
    .form-group textarea { resize: vertical; }
    .form-group input[type="file"] { padding: 6px 0; }
    .form-hint { display: block; font-size: 12px; color: #999; margin-top: 4px; }
    .required { color: #c41230; }
    .current-image { margin-top: 8px; }
    .form-actions { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 12px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin_url('staff.php'));