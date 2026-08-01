<?php
$pageTitle = '新闻管理';
require_once __DIR__ . '/../header.php';

$action = $_GET['action'] ?? 'list';
$editId = $_GET['id'] ?? 0;
$message = '';
$messageType = '';

if ($action === 'delete' && $editId) {
    DB::delete('news', $editId);
    $message = '新闻已删除';
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
            'content' => trim($_POST['content'] ?? ''),
            'image' => trim($_POST['image'] ?? ''),
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
                DB::update('news', $id, $data);
                $message = '新闻已更新';
            } else {
                DB::insert('news', $data);
                $message = '新闻已添加';
            }
            $messageType = 'success';
            $action = 'list';
        }
    }
}

$editingItem = null;
if ($action === 'edit' && $editId) {
    $editingItem = DB::getRow('news', ['id' => $editId]);
    if (!$editingItem) $action = 'list';
}
if ($action === 'add') {
    $editingItem = ['title' => '', 'category' => '', 'content' => '', 'image' => '', 'date' => date('Y-m-d'), 'sort' => 0, 'status' => 1];
}

$news = DB::getAll('news', 'id', 'DESC');
$categories = [];
$allNews = DB::getAll('news');
foreach ($allNews as $n) {
    if (!empty($n['category']) && !in_array($n['category'], $categories)) {
        $categories[] = $n['category'];
    }
}
?>
<div class="admin-breadcrumbs">
    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">首页</a>
    <span class="sep">/</span>
    <span class="current">新闻管理</span>
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
        <h2 class="admin-page-title">新闻管理</h2>
        <div class="admin-page-subtitle">管理新闻资讯</div>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/pages/news.php?action=add" class="btn btn-primary">+ 新增新闻</a>
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
                    <th>日期</th>
                    <th>状态</th>
                    <th style="width:150px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($news) > 0): ?>
                <?php foreach ($news as $item): ?>
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
                    <td><?php echo h($item['date']); ?></td>
                    <td>
                        <label class="admin-toggle">
                            <input type="checkbox" class="toggle-status" data-table="news" data-id="<?php echo $item['id']; ?>" <?php echo $item['status'] ? 'checked' : ''; ?>>
                            <span class="admin-toggle-slider"></span>
                        </label>
                    </td>
                    <td>
                        <div class="admin-actions">
                            <a href="<?php echo SITE_URL; ?>/admin/pages/news.php?action=edit&id=<?php echo $item['id']; ?>" class="admin-action-btn primary" title="编辑">✎</a>
                            <a href="<?php echo SITE_URL; ?>/admin/pages/news.php?action=delete&id=<?php echo $item['id']; ?>" class="admin-action-btn delete" title="删除" onclick="return confirm('确定要删除此新闻吗？')">🗑</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="admin-table-empty">
                        <div class="admin-table-empty-icon">📰</div>
                        暂无新闻
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
        <h2 class="admin-page-title"><?php echo $action === 'add' ? '新增新闻' : '编辑新闻'; ?></h2>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin/pages/news.php" class="btn btn-outline">← 返回列表</a>
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
                <input type="text" name="category" class="form-control" value="<?php echo h($editingItem['category']); ?>" required placeholder="如：公司动态、行业资讯">
            </div>
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
                <label class="form-label">日期</label>
                <input type="date" name="date" class="form-control" value="<?php echo h($editingItem['date']); ?>">
            </div>
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

        <div class="form-group">
            <label class="form-label">详细内容</label>
            <textarea name="content" class="form-control" rows="10"><?php echo h($editingItem['content']); ?></textarea>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary"><?php echo $action === 'add' ? '添加' : '保存'; ?></button>
            <a href="<?php echo SITE_URL; ?>/admin/pages/news.php" class="btn btn-outline">取消</a>
        </div>
    </div>
</form>
<?php endif; ?>

<?php require_once __DIR__ . '/../footer.php'; ?>