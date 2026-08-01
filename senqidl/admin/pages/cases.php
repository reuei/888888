<?php
$pageTitle = '案例管理';
require_once __DIR__ . '/../header.php';

$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? 0;
$message = '';
$messageType = '';

if ($action === 'delete' && $editId) {
    DB::delete('cases', $editId);
    $message = '案例已删除';
    $messageType = 'success';
    $action = 'list';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    if ($postAction === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'desc' => trim($_POST['desc'] ?? ''),
            'image' => trim($_POST['image'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'client' => trim($_POST['client'] ?? ''),
            'date' => trim($_POST['date'] ?? ''),
            'sort' => (int)($_POST['sort'] ?? 0),
            'status' => (int)($_POST['status'] ?? 1),
        ];

        if (empty($data['title'])) {
            $message = '请填写标题';
            $messageType = 'error';
        } elseif (empty($data['category'])) {
            $message = '请选择分类';
            $messageType = 'error';
        } else {
            if ($id > 0) {
                DB::update('cases', $id, $data);
                $message = '案例已更新';
            } else {
                DB::insert('cases', $data);
                $message = '案例已添加';
            }
            $messageType = 'success';
            $action = 'list';
        }
    }
}

$editingItem = null;
if ($action === 'edit' && $editId) {
    $editingItem = DB::getRow('cases', ['id' => $editId]);
    if (!$editingItem) $action = 'list';
}
if ($action === 'add') {
    $editingItem = ['title' => '', 'category' => '', 'desc' => '', 'image' => '', 'content' => '', 'client' => '', 'date' => date('Y-m-d'), 'sort' => 0, 'status' => 1];
}

$filterCat = $_GET['cat'] ?? '';
$cases = $filterCat ? DB::getList('cases', ['category' => $filterCat], 'id', 'DESC') : DB::getAll('cases', 'id', 'DESC');
$categories = [];
$allCases = DB::getAll('cases');
foreach ($allCases as $c) {
    if (!empty($c['category']) && !in_array($c['category'], $categories)) {
        $categories[] = $c['category'];
    }
}
?>
<div class="admin-breadcrumbs">
    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">首页</a>
    <span class="sep">/</span>
    <span class="current">案例管理</span>
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
        <h2 class="admin-page-title">案例管理</h2>
        <div class="admin-page-subtitle">管理客户案例</div>
    </div>
    <div style="display:flex;gap:0.75rem;align-items:center;">
        <select onchange="location.href=this.value" class="form-control" style="width:auto;padding:0.375rem 0.75rem;font-size:0.85rem;">
            <option value="<?php echo SITE_URL; ?>/admin/pages/cases.php" <?php echo !$filterCat ? 'selected' : ''; ?>>全部分类</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?php echo SITE_URL; ?>/admin/pages/cases.php?cat=<?php echo urlencode($cat); ?>" <?php echo $filterCat === $cat ? 'selected' : ''; ?>><?php echo h($cat); ?></option>
            <?php endforeach; ?>
        </select>
        <a href="<?php echo SITE_URL; ?>/admin/pages/cases.php?action=add" class="btn btn-primary btn-sm">+ 新增案例</a>
    </div>
</div>

<div class="admin-card">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>图片</th>
                    <th>标题</th>
                    <th>分类</th>
                    <th>客户</th>
                    <th>日期</th>
                    <th>状态</th>
                    <th style="width:150px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($cases) > 0): ?>
                <?php foreach ($cases as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td>
                        <div class="admin-table-thumb">
                            <?php if (!empty($item['image'])): ?>
                            <img src="<?php echo h($item['image']); ?>" alt="" onerror="this.style.display='none'">
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo h($item['title']); ?></td>
                    <td><span class="admin-status active"><?php echo h($item['category']); ?></span></td>
                    <td><?php echo h($item['client']); ?></td>
                    <td><?php echo h($item['date']); ?></td>
                    <td>
                        <label class="admin-toggle">
                            <input type="checkbox" class="toggle-status" data-table="cases" data-id="<?php echo $item['id']; ?>" <?php echo $item['status'] ? 'checked' : ''; ?>>
                            <span class="admin-toggle-slider"></span>
                        </label>
                    </td>
                    <td>
                        <div class="admin-actions">
                            <a href="<?php echo SITE_URL; ?>/admin/pages/cases.php?action=edit&id=<?php echo $item['id']; ?>" class="admin-action-btn primary" title="编辑">✎</a>
                            <a href="<?php echo SITE_URL; ?>/admin/pages/cases.php?action=delete&id=<?php echo $item['id']; ?>" class="admin-action-btn delete" title="删除" onclick="return confirm('确定要删除此案例吗？')">🗑</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8" class="admin-table-empty">
                        <div class="admin-table-empty-icon">📁</div>
                        暂无案例
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
        <h2 class="admin-page-title"><?php echo $action === 'add' ? '新增案例' : '编辑案例'; ?></h2>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/pages/cases.php" class="btn btn-outline">← 返回列表</a>
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
                <label class="form-label">分类 <span class="required">*</span></label>
                <input type="text" name="category" class="form-control" value="<?php echo h($editingItem['category']); ?>" required placeholder="如：网站建设、SEO优化">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">简短描述</label>
            <textarea name="desc" class="form-control" rows="3"><?php echo h($editingItem['desc']); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">图片URL</label>
                <input type="text" name="image" class="form-control" value="<?php echo h($editingItem['image']); ?>">
                <?php if (!empty($editingItem['image'])): ?>
                <div style="margin-top:0.5rem;">
                    <img src="<?php echo h($editingItem['image']); ?>" alt="" style="max-width:150px;border-radius:8px;" onerror="this.style.display='none'">
                </div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="form-label">客户名称</label>
                <input type="text" name="client" class="form-control" value="<?php echo h($editingItem['client']); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">日期</label>
                <input type="date" name="date" class="form-control" value="<?php echo h($editingItem['date']); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">排序</label>
                <input type="number" name="sort" class="form-control" value="<?php echo (int)$editingItem['sort']; ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">详细内容</label>
            <textarea name="content" class="form-control" rows="8"><?php echo h($editingItem['content']); ?></textarea>
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

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? '添加' : '保存'; ?></button>
            <a href="<?php echo SITE_URL; ?>/admin/pages/cases.php" class="btn btn-outline">取消</a>
        </div>
    </div>
</form>
<?php endif; ?>

<?php require_once __DIR__ . '/../footer.php'; ?>