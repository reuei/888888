<?php
$pageTitle = '服务管理';
require_once __DIR__ . '/../header.php';

$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? 0;
$message = '';
$messageType = '';

if ($action === 'delete' && $editId) {
    DB::delete('services', $editId);
    $message = '服务已删除';
    $messageType = 'success';
    $action = 'list';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    if ($postAction === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'desc' => trim($_POST['desc'] ?? ''),
            'icon' => trim($_POST['icon'] ?? ''),
            'sort' => (int)($_POST['sort'] ?? 0),
            'status' => (int)($_POST['status'] ?? 1),
        ];

        if (empty($data['title'])) {
            $message = '请填写标题';
            $messageType = 'error';
        } else {
            if ($id > 0) {
                DB::update('services', $id, $data);
                $message = '服务已更新';
            } else {
                DB::insert('services', $data);
                $message = '服务已添加';
            }
            $messageType = 'success';
            $action = 'list';
        }
    }
}

$editingItem = null;
if ($action === 'edit' && $editId) {
    $editingItem = DB::getRow('services', ['id' => $editId]);
    if (!$editingItem) $action = 'list';
}
if ($action === 'add') {
    $editingItem = ['title' => '', 'desc' => '', 'icon' => '🌐', 'sort' => 0, 'status' => 1];
}

$items = DB::getAll('services', 'sort', 'ASC');
?>
<div class="admin-breadcrumbs">
    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">首页</a>
    <span class="sep">/</span>
    <span class="current">服务管理</span>
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
        <h2 class="admin-page-title">服务管理</h2>
        <div class="admin-page-subtitle">管理网站提供的服务项目</div>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/pages/services.php?action=add" class="btn btn-primary">+ 新增服务</a>
</div>

<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>图标</th>
                    <th>标题</th>
                    <th>描述</th>
                    <th>排序</th>
                    <th>状态</th>
                    <th style="width:150px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td style="font-size:1.5rem;"><?php echo h($item['icon']); ?></td>
                    <td><?php echo h($item['title']); ?></td>
                    <td><?php echo h(truncate($item['desc'], 60)); ?></td>
                    <td><?php echo h($item['sort']); ?></td>
                    <td>
                        <label class="admin-toggle">
                            <input type="checkbox" class="toggle-status" data-table="services" data-id="<?php echo $item['id']; ?>" <?php echo $item['status'] ? 'checked' : ''; ?>>
                            <span class="admin-toggle-slider"></span>
                        </label>
                    </td>
                    <td>
                        <div class="admin-actions">
                            <a href="<?php echo SITE_URL; ?>/admin/pages/services.php?action=edit&id=<?php echo $item['id']; ?>" class="admin-action-btn primary" title="编辑">✎</a>
                            <a href="<?php echo SITE_URL; ?>/admin/pages/services.php?action=delete&id=<?php echo $item['id']; ?>" class="admin-action-btn delete" title="删除" onclick="return confirm('确定要删除此服务吗？')">🗑</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="admin-table-empty">
                        <div class="admin-table-empty-icon">🛠️</div>
                        暂无服务
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
        <h2 class="admin-page-title"><?php echo $action === 'add' ? '新增服务' : '编辑服务'; ?></h2>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/pages/services.php" class="btn btn-outline">← 返回列表</a>
</div>

<form method="POST" action="">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?php echo (int)($editingItem['id'] ?? 0); ?>">

    <div class="admin-form">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">标题 <span class="required">*</span></label>
                <input type="text" name="title" class="form-control" value="<?php echo h($editingItem['title']); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">图标</label>
                <input type="text" name="icon" class="form-control" value="<?php echo h($editingItem['icon']); ?>" placeholder="emoji或文字">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">描述</label>
            <textarea name="desc" class="form-control" rows="4"><?php echo h($editingItem['desc']); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">排序</label>
                <input type="number" name="sort" class="form-control" value="<?php echo (int)$editingItem['sort']; ?>">
            </div>
            <div class="form-group">
                <label class="form-label">状态</label>
                <div style="display:flex;gap:1rem;align-items:center;padding-top:0.5rem;">
                    <label class="form-check">
                        <input type="radio" name="status" value="1" <?php echo $editingItem['status'] ? 'checked' : ''; ?>>
                        <span class="form-check-label">启用</span>
                    </label>
                    <label class="form-check">
                        <input type="radio" name="status" value="0" <?php echo !$editingItem['status'] ? 'checked' : ''; ?>>
                        <span class="form-check-label">禁用</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? '添加' : '保存'; ?></button>
            <a href="<?php echo SITE_URL; ?>/admin/pages/services.php" class="btn btn-outline">取消</a>
        </div>
    </div>
</form>
<?php endif; ?>

<?php require_once __DIR__ . '/../footer.php'; ?>