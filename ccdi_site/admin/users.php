<?php
/**
 * 后台管理 - 用户管理
 * 支持列表、添加、编辑、删除、启用/禁用操作
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

// ==================== 删除操作（仅超级管理员） ====================
if ($action === 'delete' && $id > 0) {
    require_super_admin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $error = '无效的请求方式';
    } elseif (!csrf_verify(post('csrf_token', ''))) {
        $error = '安全验证失败，请刷新页面后重试';
    } else {
        // 不能删除自己的账号
        if ($id == current_user_id()) {
            $error = '不允许删除自己的账号';
        } else {
            $user = db_fetch("SELECT * FROM users WHERE id = ?", [$id]);
            if (!$user) {
                $error = '用户不存在';
            } else {
                $result = db_delete('users', 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('user_delete', "删除用户：{$user['username']}");
                    $message = '用户删除成功';
                    $action = 'list';
                } else {
                    $error = '删除失败，请稍后重试';
                }
            }
        }
    }
}

// ==================== 启用/禁用操作 ====================
if ($action === 'toggle_status' && $id > 0) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $error = '无效的请求方式';
    } elseif (!csrf_verify(post('csrf_token', ''))) {
        $error = '安全验证失败，请刷新页面后重试';
    } else {
        $user = db_fetch("SELECT * FROM users WHERE id = ?", [$id]);
        if (!$user) {
            $error = '用户不存在';
        } elseif ($id == current_user_id()) {
            $error = '不允许禁用自己的账号';
        } else {
            $new_status = ($user['status'] === 'active') ? 'disabled' : 'active';
            $result = db_update('users', ['status' => $new_status], 'id = ?', [$id]);
            if ($result !== false) {
                $status_text = ($new_status === 'active') ? '启用' : '禁用';
                add_log('user_status', "{$status_text}用户：{$user['username']}");
                $message = "用户已{$status_text}";
                $action = 'list';
            } else {
                $error = '操作失败，请稍后重试';
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
        $username = trim(post('username', ''));
        $password = trim($_POST['password'] ?? '');
        $email = trim(post('email', ''));
        $role = trim(post('role', 'subscriber'));
        $status = trim(post('status', 'active'));

        // 验证用户名
        if (empty($username)) {
            $error = '请输入用户名';
        } elseif (!is_valid_username($username)) {
            $error = '用户名格式不正确（3-20位，支持字母、数字、下划线、中文）';
        }

        // 验证邮箱
        if (empty($error) && !empty($email) && !is_valid_email($email)) {
            $error = '邮箱格式不正确';
        }

        // 验证角色
        $allowed_roles = ['super_admin', 'admin', 'editor', 'subscriber'];
        if (empty($error) && !in_array($role, $allowed_roles)) {
            $error = '无效的用户角色';
        }

        // 验证状态
        if (empty($error) && !in_array($status, ['active', 'disabled'])) {
            $error = '无效的用户状态';
        }

        // 检查用户名唯一性
        if (empty($error)) {
            if ($id > 0) {
                $existing = db_fetch("SELECT id FROM users WHERE username = ? AND id != ?", [$username, $id]);
            } else {
                $existing = db_fetch("SELECT id FROM users WHERE username = ?", [$username]);
            }
            if ($existing) {
                $error = '用户名已被占用';
            }
        }

        // 检查邮箱唯一性
        if (empty($error) && !empty($email)) {
            if ($id > 0) {
                $existing = db_fetch("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $id]);
            } else {
                $existing = db_fetch("SELECT id FROM users WHERE email = ?", [$email]);
            }
            if ($existing) {
                $error = '邮箱已被占用';
            }
        }

        // 添加新用户时密码必填
        if (empty($error) && $id <= 0 && empty($password)) {
            $error = '请输入密码';
        }

        // 验证密码强度
        if (empty($error) && !empty($password) && !is_strong_password($password)) {
            $error = '密码长度需在6-50位之间';
        }

        if (empty($error)) {
            $data = [
                'username' => $username,
                'email' => $email,
                'status' => $status,
            ];

            // 角色修改：仅超级管理员可以修改角色
            if (is_super_admin()) {
                $data['role'] = $role;
            }

            // 密码处理
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            }

            if ($id > 0) {
                // 编辑模式
                $result = db_update('users', $data, 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('user_update', "更新用户：{$username}");
                    $message = '用户更新成功';
                } else {
                    $error = '更新失败，请稍后重试';
                }
            } else {
                // 添加模式
                $data['role'] = $role;
                $data['reg_time'] = date('Y-m-d H:i:s');
                $data['last_login'] = date('Y-m-d H:i:s');
                $new_id = db_insert('users', $data);
                if ($new_id) {
                    add_log('user_create', "创建用户：{$username}");
                    $message = '用户添加成功';
                    $id = $new_id;
                } else {
                    $error = '添加失败，请稍后重试';
                }
            }
        }

        // 保存成功后跳转到列表
        if (empty($error)) {
            $action = 'list';
        } else {
            // 保持在编辑表单
            $action = ($id > 0) ? 'edit' : 'add';
        }
    }
}

// ==================== 列表视图（默认） ====================
if ($action === 'list') {
    $total = db_count('users');
    $offset = ($page - 1) * $per_page;
    $users = db_fetch_all(
        "SELECT * FROM users ORDER BY id ASC LIMIT ? OFFSET ?",
        [$per_page, $offset]
    );

    $role_badges = [
        'super_admin' => '<span class="badge badge-super-admin">超级管理员</span>',
        'admin'       => '<span class="badge badge-admin">管理员</span>',
        'editor'      => '<span class="badge badge-editor">编辑</span>',
        'subscriber'  => '<span class="badge badge-subscriber">订阅者</span>',
    ];

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title">用户管理</h2>
        <a href="<?php echo admin_url('users.php?action=add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加用户
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
                    <th>用户名</th>
                    <th width="180">邮箱</th>
                    <th width="100">角色</th>
                    <th width="70">状态</th>
                    <th width="150">注册时间</th>
                    <th width="150">最后登录</th>
                    <th width="170">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:#999;padding:40px;">暂无用户数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                        <td><?php echo htmlspecialchars($u['email'] ?: '-'); ?></td>
                        <td><?php echo isset($role_badges[$u['role']]) ? $role_badges[$u['role']] : htmlspecialchars($u['role']); ?></td>
                        <td>
                            <span class="badge <?php echo $u['status'] === 'active' ? 'badge-success' : 'badge-disabled'; ?>">
                                <?php echo $u['status'] === 'active' ? '正常' : '禁用'; ?>
                            </span>
                        </td>
                        <td><?php echo format_time($u['reg_time']); ?></td>
                        <td><?php echo format_time($u['last_login']); ?></td>
                        <td class="table-actions">
                            <a href="<?php echo admin_url('users.php?action=edit&id=' . $u['id']); ?>" class="btn btn-sm btn-secondary" title="编辑">
                                <i class="fas fa-edit"></i> 编辑
                            </a>
                            <?php if ($u['id'] != current_user_id()): ?>
                            <form method="post" action="<?php echo admin_url('users.php?action=toggle_status&id=' . $u['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要<?php echo $u['status'] === 'active' ? '禁用' : '启用'; ?>该用户吗？');">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm <?php echo $u['status'] === 'active' ? 'btn-warning' : 'btn-success'; ?>" title="<?php echo $u['status'] === 'active' ? '禁用' : '启用'; ?>">
                                    <i class="fas <?php echo $u['status'] === 'active' ? 'fa-ban' : 'fa-check'; ?>"></i>
                                    <?php echo $u['status'] === 'active' ? '禁用' : '启用'; ?>
                                </button>
                            </form>
                            <?php if (is_super_admin()): ?>
                            <form method="post" action="<?php echo admin_url('users.php?action=delete&id=' . $u['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除用户「<?php echo htmlspecialchars(addslashes($u['username'])); ?>」吗？此操作不可恢复。');">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-danger" title="删除">
                                    <i class="fas fa-trash"></i> 删除
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php else: ?>
                            <span style="color:#999;font-size:12px;">当前用户</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    echo pagination($total, $page, admin_url('users.php?'), $per_page);
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
    .btn-warning { background: #faad14; color: #fff; }
    .btn-warning:hover { background: #d48806; }
    .btn-success { background: #52c41a; color: #fff; }
    .btn-success:hover { background: #389e0d; }
    .btn-sm { padding: 4px 12px; font-size: 12px; }
    .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
    .alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-success { background: #f6ffed; color: #52c41a; }
    .badge-disabled { background: #f5f5f5; color: #999; }
    .badge-super-admin { background: #fff2f0; color: #cf1322; }
    .badge-admin { background: #fffbe6; color: #d48806; }
    .badge-editor { background: #e6f7ff; color: #096dd9; }
    .badge-subscriber { background: #f6ffed; color: #389e0d; }
    .table-container { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 20px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #fafafa; padding: 12px 14px; text-align: left; font-size: 13px; font-weight: 600; color: #555; border-bottom: 1px solid #e8e8e8; }
    .data-table td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f0f0f0; color: #333; }
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

// ==================== 添加/编辑表单 ====================
if ($action === 'add' || $action === 'edit') {
    $user = [
        'id' => 0,
        'username' => '',
        'email' => '',
        'role' => 'subscriber',
        'status' => 'active',
    ];

    if ($action === 'edit') {
        if ($id <= 0) {
            $error = '缺少用户ID';
        } else {
            $existing = db_fetch("SELECT * FROM users WHERE id = ?", [$id]);
            if (!$existing) {
                $error = '用户不存在';
            } else {
                $user = $existing;
            }
        }
    }

    // 如果保存失败，将 POST 数据回填到表单
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user['username'] = post('username', $user['username']);
        $user['email'] = post('email', $user['email']);
        if (is_super_admin()) {
            $user['role'] = post('role', $user['role']);
        }
        $user['status'] = post('status', $user['status']);
    }

    $is_edit = ($action === 'edit' && $user['id'] > 0);
    $form_title = $is_edit ? '编辑用户' : '添加用户';
    $can_change_role = is_super_admin();

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title"><?php echo $form_title; ?></h2>
        <a href="<?php echo admin_url('users.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> 返回列表
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo admin_url('users.php?action=save' . ($is_edit ? '&id=' . $user['id'] : '')); ?>" class="user-form">
        <?php echo csrf_field(); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="username">用户名 <span class="required">*</span></label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required placeholder="请输入用户名" maxlength="20">
                <span class="form-hint">3-20位，支持字母、数字、下划线、中文</span>
            </div>
            <div class="form-group form-group-side">
                <label for="email">邮箱</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="请输入邮箱地址" maxlength="100">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="password"><?php echo $is_edit ? '新密码（留空则保持不变）' : '密码'; ?> <?php echo $is_edit ? '' : '<span class="required">*</span>'; ?></label>
                <input type="password" id="password" name="password" value="" placeholder="<?php echo $is_edit ? '留空则保持不变' : '请输入密码'; ?>" maxlength="50" autocomplete="new-password">
                <span class="form-hint">密码长度需在6-50位之间</span>
            </div>
            <div class="form-group form-group-side">
                <label for="status">状态</label>
                <select id="status" name="status">
                    <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>正常</option>
                    <option value="disabled" <?php echo $user['status'] === 'disabled' ? 'selected' : ''; ?>>禁用</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="role">角色</label>
                <?php if ($can_change_role): ?>
                <select id="role" name="role">
                    <option value="super_admin" <?php echo $user['role'] === 'super_admin' ? 'selected' : ''; ?>>超级管理员</option>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>管理员</option>
                    <option value="editor" <?php echo $user['role'] === 'editor' ? 'selected' : ''; ?>>编辑</option>
                    <option value="subscriber" <?php echo $user['role'] === 'subscriber' ? 'selected' : ''; ?>>订阅者</option>
                </select>
                <span class="form-hint">仅超级管理员可以修改角色</span>
                <?php else: ?>
                <select disabled>
                    <option><?php echo htmlspecialchars($user['role']); ?></option>
                </select>
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($user['role']); ?>">
                <span class="form-hint" style="color:#faad14;">您的权限不足，无法修改角色</span>
                <?php endif; ?>
            </div>
            <div class="form-group form-group-side">
                <label>角色说明</label>
                <div class="role-info">
                    <p><span class="badge badge-super-admin">超级管理员</span> 拥有所有权限</p>
                    <p><span class="badge badge-admin">管理员</span> 管理后台大部分功能</p>
                    <p><span class="badge badge-editor">编辑</span> 管理文章和分类</p>
                    <p><span class="badge badge-subscriber">订阅者</span> 仅能浏览和评论</p>
                </div>
            </div>
        </div>

        <?php if ($is_edit): ?>
        <div class="form-row">
            <div class="form-group">
                <label>注册信息</label>
                <div class="info-text">
                    <span>注册时间：<?php echo format_time($user['reg_time'], 'Y-m-d H:i:s'); ?></span>
                    <span style="margin-left: 24px;">最后登录：<?php echo format_time($user['last_login'], 'Y-m-d H:i:s'); ?></span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php echo $is_edit ? '更新用户' : '添加用户'; ?>
            </button>
            <a href="<?php echo admin_url('users.php'); ?>" class="btn btn-secondary">取消</a>
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
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-super-admin { background: #fff2f0; color: #cf1322; }
    .badge-admin { background: #fffbe6; color: #d48806; }
    .badge-editor { background: #e6f7ff; color: #096dd9; }
    .badge-subscriber { background: #f6ffed; color: #389e0d; }
    .user-form { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 24px; }
    .form-row { display: flex; gap: 20px; margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-group-main { flex: 1; }
    .form-group-side { width: 280px; flex-shrink: 0; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 6px; }
    .form-group input[type="text"],
    .form-group input[type="password"],
    .form-group input[type="email"],
    .form-group select { width: 100%; padding: 9px 12px; border: 1px solid #d9d9d9; border-radius: 4px; font-size: 14px; font-family: inherit; transition: border-color 0.3s; box-sizing: border-box; }
    .form-group input:focus,
    .form-group select:focus { border-color: #c41230; outline: none; box-shadow: 0 0 0 2px rgba(196,18,48,0.1); }
    .form-group select[disabled] { background: #f5f5f5; color: #999; cursor: not-allowed; }
    .form-hint { display: block; font-size: 12px; color: #999; margin-top: 4px; }
    .required { color: #c41230; }
    .role-info { background: #fafafa; border: 1px solid #f0f0f0; border-radius: 4px; padding: 10px 14px; }
    .role-info p { margin: 4px 0; font-size: 12px; color: #666; }
    .info-text { font-size: 13px; color: #666; background: #fafafa; padding: 10px 14px; border-radius: 4px; border: 1px solid #f0f0f0; }
    .form-actions { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 12px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin_url('users.php'));