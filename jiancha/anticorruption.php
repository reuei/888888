<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;
$total = DB::fetchOne("SELECT COUNT(*) as c FROM articles WHERE status=1 AND category_id IN (SELECT id FROM categories WHERE slug LIKE '%fanfu%' OR slug LIKE '%fubai%' OR slug LIKE '%jijian%')")['c'] ?? 0;
$articles = DB::fetchAll("SELECT * FROM articles WHERE status=1 AND category_id IN (SELECT id FROM categories WHERE slug LIKE '%fanfu%' OR slug LIKE '%fubai%' OR slug LIKE '%jijian%') ORDER BY is_top DESC, publish_time DESC LIMIT ? OFFSET ?", [$perPage, ($page - 1) * $perPage]);
$pageTitle = '检察反腐';
include __DIR__ . '/includes/header.php';
?>
<div class="page-hero"><h1>检察反腐</h1><p>坚持零容忍 · 打击职务犯罪</p></div>
<div class="section">
<div class="container">
<div class="case-grid">
<?php if ($articles): foreach ($articles as $a): ?>
<a href="article.php?id=<?php echo $a['id']; ?>" class="case-item">
<div class="case-id"><?php echo formatDate($a['publish_time']); ?></div>
<h4><?php echo e($a['title']); ?></h4>
<p><?php echo e(truncateStr($a['content'] ?? '', 80)); ?></p>
</a>
<?php endforeach; else: ?>
<div class="empty">暂无反腐专题内容</div>
<?php endif; ?>
</div>
<?php echo paginate($total, $page, $perPage, 'anticorruption.php'); ?>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>