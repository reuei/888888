<?php
$pageTitle = '管理员管理';
require_once __DIR__ . '/../header.php';

$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? 0;
$message = '';
$messageType = '';

$currentAdmin = Auth::user();

if ($action === 'delete' && $editId) {
    if ($editId == $currentAdmin['id']) {
        $message = '不能删除当前登录的账号';
        $messageType = 'error';
        $action = 'list';
    } else {
        DB::delete('admins', $editId);
        $message = '管理员已删除';
        $messageType = 'success';
        $action = 'list';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    if ($postAction === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = trim($_POST['role'] ?? 'editor');

        if (empty($username)) {
            $message = '请填写用户名';
            $messageType = 'error';
        } elseif ($id === 0 && empty($password)) {
            $message = '新管理员必须设置密码';
            $messageType = 'error';
        } else {
            $existing = DB::getRow('admins', ['username' => $username]);
            if ($existing && $existing['id'] != $id) {
                $message = '用户名已存在';
                $messageType = 'error';
            } else {
                $data = [
                    'username' => $username,
                    'role' => $role,
                ];
                if (!empty($password)) {
                    $data['password'] = $password;
                }
                if ($id > 0) {
                    DB::update('admins', $id, $data);
                    $message = '管理员已更新';
                } else {
                    $data['last_login'] = '';
                    DB::insert('admins', $data);
                    $message = '管理员已添加';
                }
                $messageType = 'success';
                $action = 'list';
            }
        }
    }
}

$editingItem = null;
if ($action === 'edit' && $editId) {
    $editingItem = DB::getRow('admins', ['id' => $editId]);
    if (!$editingItem) $action = 'list';
}
if ($action === 'add') {
    $editingItem = ['username' => '', 'password' => '', 'role' => 'editor'];
}

$admins = DB::getAll('admins', 'id', 'ASC');
?>
<div class="admin-breadcrumbs">
    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">首页</a>
    <span class="sep">/</span>
    <span class="current">管理员管理</span>
</div>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?>">
    <span class="alert-icon"><?php echo $messageType === 'success' ? '✓' : '⚠'; ?></span>
    <?php echo h($message); ?>
</div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title">管理员管理</h2>
        <div class="admin-page-subtitle">管理后台管理员账号</div>
    </div>
    <?php if ($currentAdmin['role'] === 'superadmin'): ?>
    <a href="<?php echo SITE_URL; ?>/admin/pages/users.php?action=add" class="btn btn-primary">+ 新增管理员</a>
    <?php endif; ?>
</div>

<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>用户名</th>
                    <th>角色</th>
                    <th>最后登录</th>
                    <th style="width:150px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($admins) > 0): ?>
                <?php foreach ($admins as $admin): ?>
                <tr>
                    <td><?php echo $admin['id']; ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.625rem;">
                            <div class="admin-avatar" style="width:32px;height:32px;font-size:0.8rem;"><?php echo h(mb_substr($admin['username'], 0, 1)); ?></div>
                            <span><?php echo h($admin['username']); ?></span>
                            <?php if ($admin['id'] == $currentAdmin['id']): ?>
                            <span class="admin-status active" style="font-size:0.7rem;">当前</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="admin-status <?php echo $admin['role'] === 'superadmin' ? 'active' : ''; ?>">
                            <?php echo h($admin['role'] === 'superadmin' ? '超级管理员' : '编辑员'); ?>
                        </span>
                    </td>
                    <td><?php echo h($admin['last_login'] ?: '-'); ?></td>
                    <td>
                        <div class="admin-actions">
                            <a href="<?php echo SITE_URL; ?>/admin/pages/users.php?action=edit&id=<?php echo $admin['id']; ?>" class="admin-action-btn primary" title="编辑">✎</a>
                            <?php if ($admin['id'] != $currentAdmin['id'] && $currentAdmin['role'] === 'superadmin'): ?>
                            <a href="<?php echo SITE_URL; ?>/admin/pages/users.php?action=delete&id=<?php echo $admin['id']; ?>" class="admin-action-btn delete" title="删除" onclick="return confirm('确定要删除此管理员吗？')">🗑</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5" class="admin-table-empty">
                        <div class="admin-table-empty-icon">👥</div>
                        暂无管理员
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title"><?php echo $action === 'add' ? '新增管理员' : '编辑管理员'; ?></h2>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/pages/users.php" class="btn btn-outline">← 返回列表</a>
</div>

<form method="POST" action="">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?php echo (int)($editingItem['id'] ?? 0); ?>">

    <div class="admin-form">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">用户名 <span class="required">*</span></label>
                <input type="text" name="username" class="form-control" value="<?php echo h($editingItem['username']); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label"><?php echo $action === 'add' ? '密码 <span class="required">*</span>' : '新密码（留空不修改）'; ?></label>
                <input type="password" name="password" class="form-control" <?php echo $action === 'add' ? 'required' : ''; ?>>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">角色</label>
            <select name="role" class="form-control form-select">
                <option value="editor" <?php echo ($editingItem['role'] ?? '') === 'editor' ? 'selected' : ''; ?>>编辑员</option>
                <option value="superadmin" <?php echo ($editingItem['role'] ?? '') === 'superadmin' ? 'selected' : ''; ?>>超级管理员</option>
            </select>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? '添加' : '保存'; ?></button>
            <a href="<?php echo SITE_URL; ?>/admin/pages/users.php" class="btn btn-outline">取消</a>
        </div>
    </div>
</form>
<?php endif; ?>

<?php require_once __DIR__ . '/../footer.php'; ?>