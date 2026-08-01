<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$currentPage = 'home';
$settings = DB::getSetting();
$slides = DB::getList('slides', ['status' => 1], 'sort', 'ASC');
$stats = $settings['home_stats'] ?? [];
$services = DB::getList('services', ['status' => 1], 'sort', 'ASC');
$cases = DB::getList('cases', ['status' => 1], 'sort', 'ASC', 4);
$news = DB::getList('news', ['status' => 1], 'date', 'DESC', 3);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1a5fff">
    <title><?php echo h(DB::getSettingValue('site_title', SITE_TITLE)); ?></title>
    <meta name="description" content="<?php echo h(DB::getSettingValue('site_description', SITE_DESCRIPTION)); ?>">
    <link rel="stylesheet" href="/mobile/assets/css/style.css">
</head>
<body class="page page-home">

<header class="site-header">
    <div class="header-inner">
        <a href="<?php echo siteUrl(); ?>" class="logo">
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
    <div class="mobile-menu-footer">
        <p>电话: <?php echo h($settings['contact_phone'] ?? ''); ?></p>
        <p>邮箱: <?php echo h($settings['contact_email'] ?? ''); ?></p>
    </div>
</div>
<div class="mobile-menu-backdrop" id="mobileMenuBackdrop"></div>

<section class="banner">
    <?php if (count($slides) > 0): ?>
    <div class="carousel" id="carousel">
        <div class="carousel-track">
            <?php foreach ($slides as $key => $slide): ?>
            <div class="carousel-item" style="background-image: url('<?php echo h($slide['image']); ?>');">
                <div class="carousel-content">
                    <div class="carousel-title"><?php echo h($slide['title']); ?></div>
                    <?php if (!empty($slide['subtitle'])): ?>
                    <div class="carousel-subtitle"><?php echo h($slide['subtitle']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($slide['link'])): ?>
                    <a href="<?php echo h($slide['link']); ?>" class="carousel-btn">了解更多</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="carousel-dots">
            <?php foreach ($slides as $key => $slide): ?>
            <span class="dot <?php echo $key === 0 ? 'active' : ''; ?>"></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<section class="stats-section">
    <div class="stats-grid">
        <?php foreach ($stats as $stat): ?>
        <div class="stat-item">
            <div class="stat-num"><?php echo h($stat['num']); ?></div>
            <div class="stat-label"><?php echo h($stat['label']); ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="section-header">
        <h2 class="section-title">我们的服务</h2>
        <p class="section-subtitle">全链路品牌数字化解决方案</p>
        <div class="section-divider"></div>
    </div>
    <div class="services-grid">
        <?php foreach ($services as $service): ?>
        <a href="/mobile/service.php" class="service-card">
            <div class="service-icon"><?php echo h($service['icon']); ?></div>
            <h3 class="service-title"><?php echo h($service['title']); ?></h3>
            <p class="service-desc"><?php echo h($service['desc']); ?></p>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="about-section">
    <div class="about-preview">
        <h2><?php echo h(DB::getSettingValue('about_title', '关于森企动力')); ?></h2>
        <p><?php echo h(DB::getSettingValue('about_content', '')); ?></p>
        <div class="about-features">
            <div class="about-feature"><div class="about-feature-icon">✓</div>10年+行业经验</div>
            <div class="about-feature"><div class="about-feature-icon">✓</div>5000+成功案例</div>
            <div class="about-feature"><div class="about-feature-icon">✓</div>专业技术团队</div>
            <div class="about-feature"><div class="about-feature-icon">✓</div>7x24贴心服务</div>
        </div>
        <a href="/mobile/about.php" class="btn btn-outline">了解更多</a>
    </div>
</section>

<section>
    <div class="section-header" style="padding: 0 16px;">
        <h2 class="section-title">精选案例</h2>
        <p class="section-subtitle">我们为客户创造的价值</p>
        <div class="section-divider"></div>
    </div>
    <div class="cases-scroll">
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
</section>

<section class="section section-alt">
    <div class="section-header">
        <h2 class="section-title">新闻动态</h2>
        <p class="section-subtitle">了解行业最新资讯</p>
        <div class="section-divider"></div>
    </div>
    <div class="news-list">
        <?php foreach ($news as $item): ?>
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
</section>

<section class="cta-section">
    <h2>准备好开启品牌数字化之旅了吗？</h2>
    <p>联系我们的专家团队，获取专属解决方案</p>
    <a href="/mobile/contact.php" class="btn">立即咨询 →</a>
</section>

<nav class="tab-bar">
    <a href="/mobile/index.php" class="tab-item active">
        <img src="/mobile/assets/images/home_h.svg" alt="首页">
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