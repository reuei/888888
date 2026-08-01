<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$currentPage = 'case';
$category = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;

$conditions = ['status' => 1];
if ($category) {
    $conditions['category'] = $category;
}

$total = DB::count('cases', $conditions);
$totalPages = max(1, ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$cases = DB::getList('cases', $conditions, 'sort', 'ASC', $perPage, $offset);
$allCategories = [];
$allCases = DB::getAll('cases');
foreach ($allCases as $c) {
    if (!empty($c['category']) && !in_array($c['category'], $allCategories)) {
        $allCategories[] = $c['category'];
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1a5fff">
    <title>客户案例 - <?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="/mobile/assets/css/style.css">
</head>
<body class="page page-case">

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
    <h1>客户案例</h1>
    <p>我们为客户创造的价值</p>
    <div class="breadcrumb-nav">
        <a href="/mobile/index.php">首页</a>
        <span class="sep">/</span>
        <span class="current">客户案例</span>
    </div>
</div>

<section class="section">
    <div class="case-filter">
        <a href="/mobile/case.php" class="case-filter-item <?php echo !$category ? 'active' : ''; ?>">全部</a>
        <?php foreach ($allCategories as $cat): ?>
        <a href="/mobile/case.php?cat=<?php echo urlencode($cat); ?>" class="case-filter-item <?php echo $category === $cat ? 'active' : ''; ?>"><?php echo h($cat); ?></a>
        <?php endforeach; ?>
    </div>

    <?php if (count($cases) > 0): ?>
    <div class="cases-grid">
        <?php foreach ($cases as $case): ?>
        <a href="/mobile/case_detail.php?id=<?php echo $case['id']; ?>" class="case-card">
            <div class="case-image">
                <img src="<?php echo h($case['image']); ?>" alt="<?php echo h($case['title']); ?>" onerror="this.parentElement.classList.add('no-img-placeholder');this.style.display='none';">
                <span class="case-cat"><?php echo h($case['category']); ?></span>
            </div>
            <div class="case-info">
                <h3 class="case-title"><?php echo h($case['title']); ?></h3>
                <p class="case-desc"><?php echo h(truncate($case['desc'], 60)); ?></p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="/mobile/case.php<?php echo $category ? '?cat=' . urlencode($category) : ''; ?>&page=<?php echo $page - 1; ?>" class="pagination-item">‹</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="/mobile/case.php<?php echo $category ? '?cat=' . urlencode($category) : ''; ?>&page=<?php echo $i; ?>" class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
        <a href="/mobile/case.php<?php echo $category ? '?cat=' . urlencode($category) : ''; ?>&page=<?php echo $page + 1; ?>" class="pagination-item">›</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php else: ?>
    <div class="empty-state">
        <p>暂无相关案例</p>
    </div>
    <?php endif; ?>
</section>

<section class="cta-section">
    <h2>想成为我们的下一个成功案例？</h2>
    <p>联系我们的专家团队，开始您的数字化之旅</p>
    <a href="/mobile/contact.php" class="btn">立即咨询 →</a>
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
    <a href="/mobile/case.php" class="tab-item active">
        <img src="/mobile/assets/images/case_h.svg" alt="案例">
        <span>案例</span>
    </a>
    <a href="/mobile/news.php" class="tab-item">
        <img src="/mobile/assets/images/news.svg" alt="新闻">
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