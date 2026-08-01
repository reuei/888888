<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$news = $id ? DB::getRow('news', ['id' => $id]) : null;

if (!$news) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>资讯未找到 - <?php echo h(SITE_NAME); ?></title>
        <link rel="stylesheet" href="/mobile/assets/css/style.css">
    </head>
    <body class="error-page">
        <div>
            <div class="error-code">404</div>
            <h1>资讯未找到</h1>
            <p>抱歉，您访问的资讯不存在或已被删除。</p>
            <a href="/mobile/news.php" class="btn">返回新闻列表</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$relatedNews = DB::getList('news', ['status' => 1, 'category' => $news['category']], 'date', 'DESC', 3);
$relatedNews = array_filter($relatedNews, function($r) use ($id) { return $r['id'] != $id; });
if (count($relatedNews) < 3) {
    $moreNews = DB::getList('news', ['status' => 1], 'date', 'DESC', 3);
    foreach ($moreNews as $r) {
        if ($r['id'] != $id && count($relatedNews) < 3 && !in_array($r['id'], array_column($relatedNews, 'id'))) {
            $relatedNews[] = $r;
        }
    }
}
$relatedNews = array_slice($relatedNews, 0, 3);

$allNews = DB::getList('news', ['status' => 1], 'date', 'DESC');
$currentIndex = -1;
foreach ($allNews as $i => $n) {
    if ($n['id'] == $id) { $currentIndex = $i; break; }
}
$prevNews = $currentIndex > 0 ? $allNews[$currentIndex - 1] : null;
$nextNews = $currentIndex < count($allNews) - 1 ? $allNews[$currentIndex + 1] : null;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1a5fff">
    <title><?php echo h($news['title']); ?> - <?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="/mobile/assets/css/style.css">
</head>
<body class="page page-news-detail">

<header class="site-header">
    <div class="header-inner">
        <a href="/mobile/index.php" class="logo">
            <span class="logo-text"><?php echo h(SITE_NAME); ?></span>
        </a>
        <button class="hamburger" id="hamburger" aria-label="菜单">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <nav>
        <ul>
            <li><a href="/mobile/index.php">首页</a></li>
            <li><a href="/mobile/about.php">关于我们</a></li>
            <li><a href="/mobile/service.php">服务项目</a></li>
            <li><a href="/mobile/case.php">客户案例</a></li>
            <li><a href="/mobile/news.php">新闻资讯</a></li>
            <li><a href="/mobile/contact.php">联系我们</a></li>
        </ul>
    </nav>
</div>
<div class="mobile-menu-backdrop" id="mobileMenuBackdrop"></div>

<div class="news-article-header">
    <span class="cat"><?php echo h($news['category']); ?></span>
    <h1><?php echo h($news['title']); ?></h1>
    <div class="meta">
        <span>📅 <?php echo h(formatDate($news['date'])); ?></span>
    </div>
</div>

<?php if (!empty($news['image'])): ?>
<div class="news-article-image">
    <img src="<?php echo h($news['image']); ?>" alt="<?php echo h($news['title']); ?>" onerror="this.parentElement.classList.add('no-img-placeholder');this.style.display='none';">
</div>
<?php endif; ?>

<div class="news-article-content">
    <div class="case-content">
        <?php echo nl2br(h($news['content'])); ?>
    </div>
</div>

<div class="news-article-nav">
    <?php if ($prevNews): ?>
    <a href="/mobile/news_detail.php?id=<?php echo $prevNews['id']; ?>">
        <span class="nav-label">← 上一篇</span>
        <span class="nav-title"><?php echo h($prevNews['title']); ?></span>
    </a>
    <?php else: ?>
    <div></div>
    <?php endif; ?>
    <?php if ($nextNews): ?>
    <a href="/mobile/news_detail.php?id=<?php echo $nextNews['id']; ?>">
        <span class="nav-label">下一篇 →</span>
        <span class="nav-title"><?php echo h($nextNews['title']); ?></span>
    </a>
    <?php else: ?>
    <div></div>
    <?php endif; ?>
</div>

<?php if (count($relatedNews) > 0): ?>
<section class="section section-alt">
    <div class="related-cases">
        <h3>相关资讯</h3>
        <div class="cases-grid">
            <?php foreach ($relatedNews as $rNews): ?>
            <a href="/mobile/news_detail.php?id=<?php echo $rNews['id']; ?>" class="case-card">
                <div class="case-image">
                    <img src="<?php echo h($rNews['image']); ?>" alt="<?php echo h($rNews['title']); ?>" onerror="this.parentElement.classList.add('no-img-placeholder');this.style.display='none';">
                    <span class="case-cat"><?php echo h($rNews['category']); ?></span>
                </div>
                <div class="case-info">
                    <h3 class="case-title"><?php echo h($rNews['title']); ?></h3>
                    <p class="case-desc"><?php echo h(truncate(strip_tags($rNews['content']), 50)); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<nav class="tab-bar">
    <a href="/mobile/index.php" class="tab-item">
        <img src="/mobile/assets/images/home.svg" alt="首页">
        <span>首页</span>
    </a>
    <a href="/mobile/service.php" class="tab-item">
        <img src="/mobile/assets/images/service.svg" alt="服务">
        <span>服务</span>
    </a>
    <a href="/mobile/case.php" class="tab-item">
        <img src="/mobile/assets/images/case.svg" alt="案例">
        <span>案例</span>
    </a>
    <a href="/mobile/news.php" class="tab-item active">
        <img src="/mobile/assets/images/news_h.svg" alt="新闻">
        <span>新闻</span>
    </a>
    <a href="/mobile/contact.php" class="tab-item">
        <img src="/mobile/assets/images/contact.svg" alt="联系">
        <span>联系</span>
    </a>
</nav>
<script src="/mobile/assets/js/main.js"></script>
</body>
</html>