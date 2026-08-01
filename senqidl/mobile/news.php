<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$currentPage = 'news';
$category = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;

$conditions = ['status' => 1];
if ($category) {
    $conditions['category'] = $category;
}

$total = DB::count('news', $conditions);
$totalPages = max(1, ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$newsList = DB::getList('news', $conditions, 'date', 'DESC', $perPage, $offset);
$allNews = DB::getAll('news');

$categories = [];
foreach ($allNews as $n) {
    if (!empty($n['category']) && !in_array($n['category'], $categories)) {
        $categories[] = $n['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1a5fff">
    <title>新闻资讯 - <?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="/mobile/assets/css/style.css">
</head>
<body class="page page-news">

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

<div class="page-header">
    <h1>新闻资讯</h1>
    <p>了解行业最新动态和公司资讯</p>
    <div class="breadcrumb-nav">
        <a href="/mobile/index.php">首页</a>
        <span class="sep">/</span>
        <span class="current">新闻资讯</span>
    </div>
</div>

<section class="news-list-page">
    <div class="news-cat-filter">
        <a href="/mobile/news.php" class="news-cat-filter-item <?php echo !$category ? 'active' : ''; ?>">全部</a>
        <?php foreach ($categories as $cat): ?>
        <a href="/mobile/news.php?cat=<?php echo urlencode($cat); ?>" class="news-cat-filter-item <?php echo $category === $cat ? 'active' : ''; ?>"><?php echo h($cat); ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (count($newsList) > 0): ?>
    <div class="news-list">
        <?php foreach ($newsList as $item): ?>
        <a href="/mobile/news_detail.php?id=<?php echo $item['id']; ?>" class="news-card">
            <div class="news-thumb">
                <img src="<?php echo h($item['image']); ?>" alt="<?php echo h($item['title']); ?>" onerror="this.parentElement.classList.add('no-img-placeholder');this.style.display='none';">
            </div>
            <div class="news-body">
                <div class="news-meta">
                    <span class="news-cat"><?php echo h($item['category']); ?></span>
                    <span><?php echo h(formatDate($item['date'])); ?></span>
                </div>
                <h3 class="news-title"><?php echo h($item['title']); ?></h3>
                <p class="news-desc"><?php echo h(truncate(strip_tags($item['content']), 80)); ?></p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="/mobile/news.php<?php echo $category ? '?cat=' . urlencode($category) : ''; ?>&page=<?php echo $page - 1; ?>" class="pagination-item">‹</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="/mobile/news.php<?php echo $category ? '?cat=' . urlencode($category) : ''; ?>&page=<?php echo $i; ?>" class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
        <a href="/mobile/news.php<?php echo $category ? '?cat=' . urlencode($category) : ''; ?>&page=<?php echo $page + 1; ?>" class="pagination-item">›</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php else: ?>
    <div class="empty-state">
        <p>暂无相关资讯</p>
    </div>
    <?php endif; ?>
</section>

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