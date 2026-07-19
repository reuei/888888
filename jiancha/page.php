<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$slug = $_GET['slug'] ?? '';
$pageData = null;
try { $pageData = DB::fetchOne("SELECT * FROM pages WHERE slug=? AND status=1", [$slug]); }
catch (Exception $e) {}
if (!$pageData) { $pageTitle = '404'; include __DIR__ . '/includes/header.php'; echo '<div class="section"><div class="container"><div class="empty">页面不存在</div></div></div>'; include __DIR__ . '/includes/footer.php'; exit; }
$pageTitle = $pageData['title'];
include __DIR__ . '/includes/header.php';
?>
<div class="page-hero"><h1><?php echo e($pageData['title']); ?></h1></div>
<div class="section"><div class="container" style="max-width:860px">
<div class="article"><div class="content"><?php echo $pageData['content']; ?></div></div>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>