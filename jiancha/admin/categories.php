<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) { redirect('login.php'); exit; }
$adminTitle = '栏目管理';
$error = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!checkCsrf()) { $error = '非法请求'; }
    else {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $parentId = intval($_POST['parent_id'] ?? 0);
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $showMenu = intval($_POST['show_in_menu'] ?? 1);
        if ($_POST['action'] === 'add') {
            if (!$name || !$slug) { $error = '请填写名称和别名'; }
            else { DB::insert('categories', ['name' => $name, 'slug' => $slug, 'description' => $desc, 'parent_id' => $parentId, 'sort_order' => $sortOrder, 'show_in_menu' => $showMenu]); $ok = '添加成功'; }
        } elseif ($_POST['action'] === 'edit') {
            $id = intval($_POST['id'] ?? 0);
            if (!$name || !$slug) { $error = '请填写名称和别名'; }
            else { DB::update('categories', ['name' => $name, 'slug' => $slug, 'description' => $desc, 'parent_id' => $parentId, 'sort_order' => $sortOrder, 'show_in_menu' => $showMenu], 'id=?', [$id]); $ok = '更新成功'; }
        }
    }
}
if (isset($_GET['delete'])) {
    DB::delete('categories', 'id=?', [intval($_GET['delete'])]);
    redirect('categories.php');
}
$cats = DB::fetchAll("SELECT * FROM categories ORDER BY sort_order ASC");
include __DIR__ . '/header.php';
?>
<?php if ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
<div class="admin-card">
<h4>栏目列表</h4>
<div class="admin-table-wrap"><table class="admin-table">
<thead><tr><th>排序</th><th>名称</th><th>别名</th><th>菜单</th><th>操作</th></tr></thead>
<tbody>
<?php foreach ($cats as $c): ?>
<tr>
<td><?php echo $c['sort_order']; ?></td>
<td><?php echo e($c['name']); ?></td>
<td><?php echo e($c['slug']); ?></td>
<td><?php echo $c['show_in_menu'] ? '是' : '否'; ?></td>
<td><a href="?delete=<?php echo $c['id']; ?>" onclick="return confirm('确定删除？')" class="btn btn-sm btn-danger">删除</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table></div>
</div>
<div class="admin-card">
<h4>添加栏目</h4>
<form method="post">
<?php echo csrfField(); ?>
<input type="hidden" name="action" value="add">
<div class="admin-form admin-form-row">
<div class="admin-form"><label>名称</label><input type="text" name="name" required></div>
<div class="admin-form"><label>别名（英文）</label><input type="text" name="slug" required></div>
</div>
<div class="admin-form admin-form-row">
<div class="admin-form"><label>描述</label><input type="text" name="description"></div>
<div class="admin-form"><label>排序</label><input type="number" name="sort_order" value="0"></div>
</div>
<div class="admin-form"><label><input type="checkbox" name="show_in_menu" value="1" checked> 显示在菜单</label></div>
<div class="admin-form"><button type="submit" class="btn btn-primary">添加</button></div>
</form>
</div>
<?php include __DIR__ . '/footer.php'; ?>