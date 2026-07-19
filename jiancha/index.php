<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/slide.php';

$slides = DB::fetchAll("SELECT * FROM slides WHERE status=1 ORDER BY sort_order ASC LIMIT 5");
$topNews = DB::fetchAll("SELECT * FROM articles WHERE status=1 AND is_top=1 ORDER BY publish_time DESC LIMIT 1");
$recentNews = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY publish_time DESC LIMIT 8");
$hotNews = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC LIMIT 8");
$totalArt = DB::fetchOne("SELECT COUNT(*) as c FROM articles WHERE status=1")['c'] ?? 0;
$totalMsg = DB::fetchOne("SELECT COUNT(*) as c FROM messages")['c'] ?? 0;
$totalUser = DB::fetchOne("SELECT COUNT(*) as c FROM users")['c'] ?? 0;
$totalCase = DB::fetchOne("SELECT COUNT(*) as c FROM articles WHERE status=1 AND category_id IN (SELECT id FROM categories WHERE slug LIKE '%fanfu%' OR slug LIKE '%anli%')")['c'] ?? 0;

$pageTitle = '';
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
<div class="container">
<h1>维护宪法权威<br><span>践行检察使命</span></h1>
<p>人民检察信息公开平台，依法接受群众监督，推进检务公开透明，维护社会公平正义。</p>
<div class="hero-btns">
<a href="report.php" class="btn btn-primary">信访举报</a>
<a href="message.php" class="btn btn-outline">在线留言</a>
</div>
</div>
</section>

<section class="section" style="padding-top:0">
<div class="container">
<div class="grid-main">
<div>
<div style="margin-bottom:20px"><?php echo getSliderHtml(); ?></div>
<div class="card">
<div class="card-head">检察要闻</div>
<div class="card-body">
<ul class="news-list">
<?php foreach ($recentNews as $n): ?>
<li><a href="article.php?id=<?php echo $n['id']; ?>"><?php if ($n['is_top']): ?><span class="top">置顶</span><?php endif; ?><?php echo e($n['title']); ?></a><span class="date"><?php echo formatDate($n['publish_time']); ?></span></li>
<?php endforeach; ?>
</ul>
</div>
<a href="category.php?slug=yaowen" class="card-more">更多 &rarr;</a>
</div>
</div>
<div>
<div class="card" style="margin-bottom:20px">
<div class="card-head">检察服务</div>
<div class="card-body">
<div class="service-grid">
<a href="report.php" class="service-item"><div class="icon">&#9993;</div><h5>信访举报</h5></a>
<a href="message.php" class="service-item"><div class="icon">&#9998;</div><h5>在线留言</h5></a>
<a href="anticorruption.php" class="service-item"><div class="icon">&#9878;</div><h5>反腐专题</h5></a>
<a href="cases.php" class="service-item"><div class="icon">&#128196;</div><h5>典型案例</h5></a>
</div>
</div>
</div>
<div class="card" style="margin-bottom:20px">
<div class="card-head">热门排行</div>
<div class="card-body">
<ul class="hot-list">
<?php foreach ($hotNews as $h): ?>
<li><a href="article.php?id=<?php echo $h['id']; ?>"><?php echo e(truncateStr($h['title'], 24)); ?></a></li>
<?php endforeach; ?>
</ul>
</div>
</div>
<div class="hotline">
<h4>检察服务热线</h4>
<div class="num">12309</div>
</div>
</div>
</div>
</div>
</section>

<section class="section" style="padding-top:0">
<div class="container">
<div class="stats-grid">
<div class="stat-item"><div class="stat-num"><?php echo $totalArt; ?></div><div class="stat-label">信息总数</div></div>
<div class="stat-item"><div class="stat-num"><?php echo $totalCase; ?></div><div class="stat-label">反腐案例</div></div>
<div class="stat-item"><div class="stat-num"><?php echo $totalMsg; ?></div><div class="stat-label">群众留言</div></div>
<div class="stat-item"><div class="stat-num"><?php echo $totalUser; ?></div><div class="stat-label">注册用户</div></div>
</div>
</div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>