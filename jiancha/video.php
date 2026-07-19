<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$total = 0; $articles = [];
try {
    $total = DB::fetchOne("SELECT COUNT(*) as c FROM articles WHERE status=1 AND category_id IN (SELECT id FROM categories WHERE slug LIKE '%shipin%' OR slug LIKE '%media%')")['c'] ?? 0;
    $articles = DB::fetchAll("SELECT * FROM articles WHERE status=1 AND category_id IN (SELECT id FROM categories WHERE slug LIKE '%shipin%' OR slug LIKE '%media%') ORDER BY publish_time DESC LIMIT ? OFFSET ?", [$perPage, ($page - 1) * $perPage]);
} catch (Exception $e) {}
$pageTitle = '检察视频';
include __DIR__ . '/includes/header.php';
?>
<div class="page-hero"><h1>检察视频</h1><p>检察视听 · 法治影像</p></div>
<div class="section"><div class="container">
<?php if ($articles): ?>
<div class="card"><div class="card-body"><ul class="news-list">
<?php foreach ($articles as $a): ?>
<li><a href="article.php?id=<?php echo $a['id']; ?>"><?php echo e($a['title']); ?></a><span class="date"><?php echo formatDate($a['publish_time']); ?></span></li>
<?php endforeach; ?>
</ul></div></div>
<?php echo paginate($total, $page, $perPage, 'video.php'); ?>
<?php else: ?><div class="empty">暂无视频</div><?php endif; ?>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>