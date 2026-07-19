<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$id = intval($_GET['id'] ?? 0);
$article = DB::fetchOne("SELECT * FROM articles WHERE id=? AND status=1", [$id]);
if (!$article) { http_response_code(404); $pageTitle = '404'; include __DIR__ . '/includes/header.php'; echo '<div class="section"><div class="container"><div class="empty">文章不存在</div></div></div>'; include __DIR__ . '/includes/footer.php'; exit; }
DB::exec("UPDATE articles SET views=views+1 WHERE id=" . $id);
$cat = DB::fetchOne("SELECT * FROM categories WHERE id=?", [$article['category_id']]);
$pageTitle = $article['title'];
$currentSlug = $cat['slug'] ?? '';
include __DIR__ . '/includes/header.php';
?>
<div class="section section-sm"><div class="container">
<div class="breadcrumb"><a href="index.php">首页</a><span class="sep">/</span><?php if ($cat): ?><a href="category.php?slug=<?php echo $cat['slug']; ?>"><?php echo e($cat['name']); ?></a><span class="sep">/</span><?php endif; ?>正文</div>
</div></div>
<div class="section" style="padding-top:0"><div class="container" style="max-width:860px">
<div class="article">
<h1><?php echo e($article['title']); ?></h1>
<div class="meta">
<span>来源：<?php echo e($article['source'] ?: siteName()); ?></span>
<span>时间：<?php echo formatDate($article['publish_time'], 'Y-m-d H:i'); ?></span>
<span>阅读：<?php echo $article['views']; ?></span>
</div>
<div class="content"><?php echo $article['content']; ?></div>
</div>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>