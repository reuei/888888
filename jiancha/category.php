<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$slug = $_GET['slug'] ?? '';
$cat = getCategoryBySlug($slug);
if (!$cat) { $pageTitle = '404'; include __DIR__ . '/includes/header.php'; echo '<div class="section"><div class="container"><div class="empty">栏目不存在</div></div></div>'; include __DIR__ . '/includes/footer.php'; exit; }
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$total = DB::fetchOne("SELECT COUNT(*) as c FROM articles WHERE category_id=? AND status=1", [$cat['id']])['c'] ?? 0;
$articles = DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY is_top DESC, publish_time DESC LIMIT ? OFFSET ?", [$cat['id'], $perPage, ($page - 1) * $perPage]);
$pageTitle = $cat['name'];
$currentSlug = $slug;
include __DIR__ . '/includes/header.php';
?>
<div class="page-hero"><h1><?php echo e($cat['name']); ?></h1><p><?php echo e($cat['description'] ?? '检察信息发布'); ?></p></div>
<div class="section"><div class="container">
<?php if ($articles): ?>
<div class="card"><div class="card-body"><ul class="news-list">
<?php foreach ($articles as $a): ?>
<li><a href="article.php?id=<?php echo $a['id']; ?>"><?php if ($a['is_top']): ?><span class="top">置顶</span><?php endif; ?><?php echo e($a['title']); ?></a><span class="date"><?php echo formatDate($a['publish_time']); ?></span></li>
<?php endforeach; ?>
</ul></div></div>
<?php echo paginate($total, $page, $perPage, "category.php?slug=" . urlencode($slug)); ?>
<?php else: ?><div class="empty">暂无内容</div><?php endif; ?>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>