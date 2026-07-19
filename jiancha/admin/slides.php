<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) { redirect('login.php'); exit; }
$adminTitle = '轮播图';
$error = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!checkCsrf()) { $error = '非法请求'; }
    else {
        $title = trim($_POST['title'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $status = intval($_POST['status'] ?? 1);
        $image = '';
        $upload = uploadFile('image');
        if ($upload['success']) { $image = $upload['path']; }
        if ($_POST['action'] === 'add') {
            if (!$title) { $error = '请填写标题'; }
            else { DB::insert('slides', ['title' => $title, 'image' => $image, 'link' => $link, 'sort_order' => $sortOrder, 'status' => $status]); $ok = '添加成功'; }
        } elseif ($_POST['action'] === 'edit') {
            $id = intval($_POST['id'] ?? 0);
            $data = ['title' => $title, 'link' => $link, 'sort_order' => $sortOrder, 'status' => $status];
            if ($image) $data['image'] = $image;
            DB::update('slides', $data, 'id=?', [$id]); $ok = '更新成功';
        }
    }
}
if (isset($_GET['delete'])) {
    DB::delete('slides', 'id=?', [intval($_GET['delete'])]);
    redirect('slides.php');
}
$slides = DB::fetchAll("SELECT * FROM slides ORDER BY sort_order ASC");
include __DIR__ . '/header.php';
?>
<?php if ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
<div class="admin-card">
<h4>轮播图列表</h4>
<table class="admin-table">
<thead><tr><th>排序</th><th>标题</th><th>图片</th><th>状态</th><th>操作</th></tr></thead>
<tbody>
<?php foreach ($slides as $s): ?>
<tr><td><?php echo $s['sort_order']; ?></td><td><?php echo e($s['title']); ?></td><td><?php if ($s['image']): ?><img src="<?php echo SITE_URL . UPLOAD_URL . e($s['image']); ?>" style="width:80px;height:45px;object-fit:cover;border-radius:4px"><?php endif; ?></td><td><?php echo $s['status'] ? '启用' : '禁用'; ?></td>
<td><a href="?delete=<?php echo $s['id']; ?>" onclick="return confirm('确定删除？')" class="btn btn-sm btn-danger">删除</a></td></tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div class="admin-card">
<h4>添加轮播图</h4>
<form method="post" enctype="multipart/form-data">
<?php echo csrfField(); ?>
<input type="hidden" name="action" value="add">
<div class="admin-form admin-form-row">
<div class="admin-form"><label>标题</label><input type="text" name="title" required></div>
<div class="admin-form"><label>链接</label><input type="text" name="link"></div>
</div>
<div class="admin-form admin-form-row">
<div class="admin-form"><label>图片</label><input type="file" name="image" accept="image/*"></div>
<div class="admin-form"><label>排序</label><input type="number" name="sort_order" value="0"></div>
</div>
<div class="admin-form"><label><input type="checkbox" name="status" value="1" checked> 启用</label></div>
<div class="admin-form"><button type="submit" class="btn btn-primary">添加</button></div>
</form>
</div>
<?php include __DIR__ . '/footer.php'; ?>