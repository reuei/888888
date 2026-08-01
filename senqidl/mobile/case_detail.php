<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$case = $id ? DB::getRow('cases', ['id' => $id]) : null;

if (!$case) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>案例未找到 - <?php echo h(SITE_NAME); ?></title>
        <link rel="stylesheet" href="/mobile/assets/css/style.css">
    </head>
    <body class="error-page">
        <div>
            <div class="error-code">404</div>
            <h1>案例未找到</h1>
            <p>抱歉，您访问的案例不存在或已被删除。</p>
            <a href="/mobile/case.php" class="btn">返回案例列表</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$relatedCases = DB::getList('cases', ['status' => 1, 'category' => $case['category']], 'sort', 'ASC', 3);
$relatedCases = array_filter($relatedCases, function($r) use ($id) { return $r['id'] != $id; });
if (count($relatedCases) < 3) {
    $moreCases = DB::getList('cases', ['status' => 1], 'sort', 'ASC', 3);
    foreach ($moreCases as $r) {
        if ($r['id'] != $id && count($relatedCases) < 3 && !in_array($r['id'], array_column($relatedCases, 'id'))) {
            $relatedCases[] = $r;
        }
    }
}
$relatedCases = array_slice($relatedCases, 0, 3);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1a5fff">
    <title><?php echo h($case['title']); ?> - <?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="/mobile/assets/css/style.css">
</head>
<body class="page page-case-detail">

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

<div class="case-detail-hero">
    <img src="<?php echo h($case['image']); ?>" alt="<?php echo h($case['title']); ?>" onerror="this.style.display='none';this.parentElement.classList.add('no-img-placeholder');">
    <div class="case-detail-info">
        <span style="display: inline-block; padding: 3px 12px; background: rgba(255,255,255,0.2); border-radius: 12px; font-size: 0.75rem; margin-bottom: 8px;"><?php echo h($case['category']); ?></span>
        <h1><?php echo h($case['title']); ?></h1>
        <div class="case-detail-meta">
            <span>👤 <?php echo h($case['client'] ?? ''); ?></span>
            <span>📅 <?php echo h(formatDate($case['date'] ?? '')); ?></span>
        </div>
    </div>
</div>

<section class="section">
    <div class="case-content">
        <?php echo nl2br(h($case['content'])); ?>
    </div>
</section>

<?php if (count($relatedCases) > 0): ?>
<section class="section section-alt">
    <div class="related-cases">
        <h3>相关案例</h3>
        <div class="cases-grid">
            <?php foreach ($relatedCases as $rCase): ?>
            <a href="/mobile/case_detail.php?id=<?php echo $rCase['id']; ?>" class="case-card">
                <div class="case-image">
                    <img src="<?php echo h($rCase['image']); ?>" alt="<?php echo h($rCase['title']); ?>" onerror="this.parentElement.classList.add('no-img-placeholder');this.style.display='none';">
                    <span class="case-cat"><?php echo h($rCase['category']); ?></span>
                </div>
                <div class="case-info">
                    <h3 class="case-title"><?php echo h($rCase['title']); ?></h3>
                    <p class="case-desc"><?php echo h(truncate($rCase['desc'], 50)); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-section">
    <h2>有类似的项目需求？</h2>
    <p>联系我们的专家团队，获取专属解决方案</p>
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