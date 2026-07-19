<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) { redirect('login.php'); exit; }
$adminTitle = '单页管理';
$error = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!checkCsrf()) { $error = '非法请求'; }
    else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $content = $_POST['content'] ?? '';
        $status = intval($_POST['status'] ?? 1);
        if ($_POST['action'] === 'add') {
            if (!$title || !$slug) { $error = '请填写标题和别名'; }
            else { DB::insert('pages', ['title' => $title, 'slug' => $slug, 'content' => $content, 'status' => $status]); $ok = '添加成功'; }
        } elseif ($_POST['action'] === 'edit') {
            $id = intval($_POST['id'] ?? 0);
            DB::update('pages', ['title' => $title, 'slug' => $slug, 'content' => $content, 'status' => $status], 'id=?', [$id]); $ok = '更新成功';
        }
    }
}
if (isset($_GET['delete'])) {
    DB::delete('pages', 'id=?', [intval($_GET['delete'])]);
    redirect('pages.php');
}
$pages = DB::fetchAll("SELECT * FROM pages ORDER BY id DESC");
include __DIR__ . '/header.php';
?>
<?php if ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
<div class="admin-card">
<h4>单页列表</h4>
<div class="admin-table-wrap"><table class="admin-table">
<thead><tr><th>ID</th><th>标题</th><th>别名</th><th>状态</th><th>操作</th></tr></thead>
<tbody>
<?php foreach ($pages as $p): ?>
<tr><td><?php echo $p['id']; ?></td><td><?php echo e($p['title']); ?></td><td><?php echo e($p['slug']); ?></td><td><?php echo $p['status'] ? '发布' : '草稿'; ?></td>
<td><a href="?delete=<?php echo $p['id']; ?>" onclick="return confirm('确定删除？')" class="btn btn-sm btn-danger">删除</a></td></tr>
<?php endforeach; ?>
</tbody>
</table></div>
</div>
<div class="admin-card">
<h4>添加单页</h4>
<form method="post">
<?php echo csrfField(); ?>
<input type="hidden" name="action" value="add">
<div class="admin-form admin-form-row">
<div class="admin-form"><label>标题</label><input type="text" name="title" required></div>
<div class="admin-form"><label>别名</label><input type="text" name="slug" required></div>
</div>
<div class="admin-form"><label>内容</label><textarea name="content" rows="8"></textarea></div>
<div class="admin-form"><label><input type="checkbox" name="status" value="1" checked> 发布</label></div>
<div class="admin-form"><button type="submit" class="btn btn-primary">添加</button></div>
</form>
</div>
<?php include __DIR__ . '/footer.php'; ?>