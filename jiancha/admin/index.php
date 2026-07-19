<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) { redirect('login.php'); exit; }
$adminTitle = '首页概览';
include __DIR__ . '/header.php';
$totalArt = DB::fetchOne("SELECT COUNT(*) as c FROM articles")['c'] ?? 0;
$totalCat = DB::fetchOne("SELECT COUNT(*) as c FROM categories")['c'] ?? 0;
$totalUser = DB::fetchOne("SELECT COUNT(*) as c FROM users")['c'] ?? 0;
$totalMsg = DB::fetchOne("SELECT COUNT(*) as c FROM messages")['c'] ?? 0;
$recentArts = DB::fetchAll("SELECT * FROM articles ORDER BY id DESC LIMIT 5");
?>
<div class="admin-stat-grid">
<div class="admin-stat"><div class="num"><?php echo $totalArt; ?></div><div class="label">文章总数</div></div>
<div class="admin-stat"><div class="num"><?php echo $totalCat; ?></div><div class="label">栏目总数</div></div>
<div class="admin-stat"><div class="num"><?php echo $totalUser; ?></div><div class="label">用户总数</div></div>
<div class="admin-stat"><div class="num"><?php echo $totalMsg; ?></div><div class="label">留言总数</div></div>
</div>
<div class="admin-card">
<h4>最近文章</h4>
<table class="admin-table">
<thead><tr><th>ID</th><th>标题</th><th>发布时间</th><th>状态</th></tr></thead>
<tbody>
<?php foreach ($recentArts as $a): ?>
<tr><td><?php echo $a['id']; ?></td><td><a href="article_edit.php?id=<?php echo $a['id']; ?>"><?php echo e(truncateStr($a['title'], 40)); ?></a></td><td><?php echo formatDate($a['publish_time']); ?></td><td><?php echo $a['status'] ? '已发布' : '草稿'; ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php include __DIR__ . '/footer.php'; ?>
