<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$q = trim($_GET['q'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$articles = [];
$total = 0;
if ($q) {
    $kw = '%' . $q . '%';
    $total = DB::fetchOne("SELECT COUNT(*) as c FROM articles WHERE status=1 AND (title LIKE ? OR content LIKE ?)", [$kw, $kw])['c'] ?? 0;
    $articles = DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE ? OR content LIKE ?) ORDER BY publish_time DESC LIMIT ? OFFSET ?", [$kw, $kw, $perPage, ($page - 1) * $perPage]);
}
$pageTitle = '搜索';
include __DIR__ . '/includes/header.php';
?>
<div class="page-hero"><h1>信息检索</h1><p>关键词搜索</p></div>
<div class="section"><div class="container" style="max-width:800px">
<form method="get" class="card" style="padding:16px;margin-bottom:20px">
<div style="display:flex;gap:8px">
<input type="text" name="q" value="<?php echo e($q); ?>" placeholder="搜索关键词..." style="flex:1;padding:10px 14px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:14px;background:rgba(255,255,255,0.03);color:var(--text1);outline:none">
<button type="submit" class="btn btn-primary">搜索</button>
</div>
</form>
<?php if ($q): ?>
<h4 style="margin-bottom:16px;color:var(--text2);font-size:14px">找到 <strong style="color:var(--gold)"><?php echo $total; ?></strong> 条关于 "<?php echo e($q); ?>" 的结果</h4>
<?php if ($articles): ?>
<div class="card"><div class="card-body"><ul class="news-list">
<?php foreach ($articles as $a): ?>
<li><a href="article.php?id=<?php echo $a['id']; ?>"><?php echo e($a['title']); ?></a><span class="date"><?php echo formatDate($a['publish_time']); ?></span></li>
<?php endforeach; ?>
</ul></div></div>
<?php echo paginate($total, $page, $perPage, "search.php?q=" . urlencode($q)); ?>
<?php else: ?><div class="empty">未找到相关内容</div><?php endif; ?>
<?php endif; ?>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>