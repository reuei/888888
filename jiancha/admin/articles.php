<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) { redirect('login.php'); exit; }
$adminTitle = '文章管理';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;
$total = DB::fetchOne("SELECT COUNT(*) as c FROM articles")['c'] ?? 0;
$articles = DB::fetchAll("SELECT a.*, c.name as cat_name FROM articles a LEFT JOIN categories c ON a.category_id=c.id ORDER BY a.id DESC LIMIT ? OFFSET ?", [$perPage, ($page - 1) * $perPage]);
include __DIR__ . '/header.php';
?>
<div class="admin-card">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
<h4 style="margin:0;padding:0;border:0">文章列表</h4>
<a href="article_edit.php" class="btn btn-primary btn-sm">+ 新增文章</a>
</div>
<table class="admin-table">
<thead><tr><th>ID</th><th>标题</th><th>栏目</th><th>时间</th><th>操作</th></tr></thead>
<tbody>
<?php foreach ($articles as $a): ?>
<tr>
<td><?php echo $a['id']; ?></td>
<td><a href="article_edit.php?id=<?php echo $a['id']; ?>"><?php echo e(truncateStr($a['title'], 30)); ?></a><?php if ($a['is_top']): ?> <span style="color:var(--red);font-size:11px">[顶]</span><?php endif; ?></td>
<td><?php echo e($a['cat_name'] ?? '-'); ?></td>
<td><?php echo formatDate($a['publish_time']); ?></td>
<td><a href="article_edit.php?id=<?php echo $a['id']; ?>" class="btn btn-sm btn-primary">编辑</a> <a href="?delete=<?php echo $a['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定删除？')">删除</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php echo paginate($total, $page, $perPage, 'articles.php'); ?>
</div>
<?php
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    DB::delete('articles', 'id=?', [$id]);
    redirect('articles.php');
}
include __DIR__ . '/footer.php'; ?>
