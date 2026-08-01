<?php
$pageTitle = '幻灯片管理';
require_once __DIR__ . '/../header.php';

$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? 0;
$message = '';
$messageType = '';

if ($action === 'delete' && $editId) {
    DB::delete('slides', $editId);
    $message = '幻灯片已删除';
    $messageType = 'success';
    $action = 'list';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    if ($postAction === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'subtitle' => trim($_POST['subtitle'] ?? ''),
            'image' => trim($_POST['image'] ?? ''),
            'link' => trim($_POST['link'] ?? ''),
            'sort' => (int)($_POST['sort'] ?? 0),
            'status' => (int)($_POST['status'] ?? 1),
        ];

        if (empty($data['title'])) {
            $message = '请填写标题';
            $messageType = 'error';
        } elseif (empty($data['image'])) {
            $message = '请填写图片URL';
            $messageType = 'error';
        } else {
            if ($id > 0) {
                DB::update('slides', $id, $data);
                $message = '幻灯片已更新';
            } else {
                DB::insert('slides', $data);
                $message = '幻灯片已添加';
            }
            $messageType = 'success';
            $action = 'list';
        }
    }
}

$editingSlide = null;
if ($action === 'edit' && $editId) {
    $editingSlide = DB::getRow('slides', ['id' => $editId]);
    if (!$editingSlide) {
        $action = 'list';
    }
}

if ($action === 'add') {
    $editingSlide = ['title' => '', 'subtitle' => '', 'image' => '', 'link' => '', 'sort' => 0, 'status' => 1];
}

$slides = DB::getAll('slides', 'sort', 'ASC');
?>
<div class="admin-breadcrumbs">
    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">首页</a>
    <span class="sep">/</span>
    <span class="current">幻灯片管理</span>
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
        <h2 class="admin-page-title">幻灯片管理</h2>
        <div class="admin-page-subtitle">管理首页轮播幻灯片</div>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/pages/slides.php?action=add" class="btn btn-primary">+ 新增幻灯片</a>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h3>幻灯片列表</h3>
        <span style="font-size:0.85rem;color:var(--text-muted);">拖拽调整排序</span>
    </div>
    <div class="admin-table-wrapper">
        <table class="admin-table" id="sortableTable">
            <thead>
                <tr>
                    <th style="width:40px;">排序</th>
                    <th>图片</th>
                    <th>标题</th>
                    <th>副标题</th>
                    <th>链接</th>
                    <th>状态</th>
                    <th style="width:150px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($slides) > 0): ?>
                <?php foreach ($slides as $slide): ?>
                <tr class="admin-sortable-item" data-id="<?php echo $slide['id']; ?>" data-sort="<?php echo $slide['sort']; ?>">
                    <td><span class="admin-sort-handle">⋮⋮</span></td>
                    <td>
                        <div class="admin-table-thumb">
                            <?php if (!empty($slide['image'])): ?>
                            <img src="<?php echo h($slide['image']); ?>" alt="" onerror="this.style.display='none'">
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo h($slide['title']); ?></td>
                    <td><?php echo h(truncate($slide['subtitle'], 40)); ?></td>
                    <td><?php echo h(truncate($slide['link'], 30) || '-'); ?></td>
                    <td>
                        <label class="admin-toggle">
                            <input type="checkbox" class="toggle-status" data-table="slides" data-id="<?php echo $slide['id']; ?>" <?php echo $slide['status'] ? 'checked' : ''; ?>>
                            <span class="admin-toggle-slider"></span>
                        </label>
                    </td>
                    <td>
                        <div class="admin-actions">
                            <a href="<?php echo SITE_URL; ?>/admin/pages/slides.php?action=edit&id=<?php echo $slide['id']; ?>" class="admin-action-btn primary" title="编辑">✎</a>
                            <a href="<?php echo SITE_URL; ?>/admin/pages/slides.php?action=delete&id=<?php echo $slide['id']; ?>" class="admin-action-btn delete" title="删除" onclick="return confirm('确定要删除此幻灯片吗？')">🗑</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="admin-table-empty">
                        <div class="admin-table-empty-icon">🖼️</div>
                        暂无幻灯片，点击右上角添加
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
        <h2 class="admin-page-title"><?php echo $action === 'add' ? '新增幻灯片' : '编辑幻灯片'; ?></h2>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/pages/slides.php" class="btn btn-outline">← 返回列表</a>
</div>

<form method="POST" action="">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?php echo (int)($editingSlide['id'] ?? 0); ?>">

    <div class="admin-form">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">标题 <span class="required">*</span></label>
                <input type="text" name="title" class="form-control" value="<?php echo h($editingSlide['title']); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">副标题</label>
                <input type="text" name="subtitle" class="form-control" value="<?php echo h($editingSlide['subtitle']); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">图片URL <span class="required">*</span></label>
            <input type="text" name="image" class="form-control" value="<?php echo h($editingSlide['image']); ?>" placeholder="/assets/images/slide.jpg" required>
            <?php if (!empty($editingSlide['image'])): ?>
            <div style="margin-top:0.75rem;">
                <img src="<?php echo h($editingSlide['image']); ?>" alt="" style="max-width:200px;border-radius:8px;" onerror="this.style.display='none'">
            </div>
            <?php endif; ?>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">链接</label>
                <input type="text" name="link" class="form-control" value="<?php echo h($editingSlide['link']); ?>" placeholder="点击跳转链接">
            </div>
            <div class="form-group">
                <label class="form-label">排序</label>
                <input type="number" name="sort" class="form-control" value="<?php echo (int)$editingSlide['sort']; ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">状态</label>
            <div style="display:flex;gap:1rem;align-items:center;">
                <label class="form-check">
                    <input type="radio" name="status" value="1" <?php echo $editingSlide['status'] ? 'checked' : ''; ?>>
                    <span class="form-check-label">启用</span>
                </label>
                <label class="form-check">
                    <input type="radio" name="status" value="0" <?php echo !$editingSlide['status'] ? 'checked' : ''; ?>>
                    <span class="form-check-label">禁用</span>
                </label>
            </div>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? '添加' : '保存'; ?></button>
            <a href="<?php echo SITE_URL; ?>/admin/pages/slides.php" class="btn btn-outline">取消</a>
        </div>
    </div>
</form>
<?php endif; ?>

<?php require_once __DIR__ . '/../footer.php'; ?>