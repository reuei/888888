<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$currentPage = 'about';
$settings = DB::getSetting();
$stats = $settings['home_stats'] ?? [];
$services = DB::getList('services', ['status' => 1], 'sort', 'ASC');

$teamMembers = [
    ['name' => '张总', 'title' => 'CEO / 创始人', 'image' => '/assets/images/team1.jpg', 'desc' => '10年+品牌数字化行业经验'],
    ['name' => '李总', 'title' => 'CTO / 技术总监', 'image' => '/assets/images/team2.jpg', 'desc' => '深耕技术架构与创新研发'],
    ['name' => '王总', 'title' => 'COO / 运营总监', 'image' => '/assets/images/team3.jpg', 'desc' => '擅长品牌运营与市场策略'],
    ['name' => '赵总', 'title' => '设计总监', 'image' => '/assets/images/team4.jpg', 'desc' => '创意设计与用户体验专家']
];

$values = [
    ['icon' => '🎯', 'title' => '客户至上', 'desc' => '始终以客户成功为我们的成功标准'],
    ['icon' => '💡', 'title' => '创新驱动', 'desc' => '不断探索新技术、新方法、新模式'],
    ['icon' => '🛡️', 'title' => '诚信务实', 'desc' => '以诚待人，脚踏实地做好每一个项目'],
    ['icon' => '🤝', 'title' => '团队协作', 'desc' => '相信团队的力量，共同成长进步']
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1a5fff">
    <title>关于我们 - <?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="/mobile/assets/css/style.css">
</head>
<body class="page page-about">

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
    <h1>关于森企动力</h1>
    <p>让数字化更有价值，助力中国品牌全球化</p>
    <div class="breadcrumb-nav">
        <a href="/mobile/index.php">首页</a>
        <span class="sep">/</span>
        <span class="current">关于我们</span>
    </div>
</div>

<section class="section">
    <div class="section-header left" style="text-align: left;">
        <span style="display: inline-block; padding: 0.25rem 1rem; background: rgba(26,95,255,0.1); color: var(--primary); border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 8px;">公司介绍</span>
        <h2 class="section-title" style="text-align: left;"><?php echo h($settings['about_title'] ?? '关于森企动力'); ?></h2>
        <div class="section-divider" style="margin: 8px 0 0; background: linear-gradient(90deg, var(--primary), var(--accent)); width: 40px; height: 3px; border-radius: 3px;"></div>
    </div>
    <p style="font-size: 0.9rem; line-height: 1.8; color: var(--text-light); margin: 12px 0 16px;"><?php echo h($settings['about_content'] ?? ''); ?></p>
    <div class="about-features">
        <div class="about-feature"><div class="about-feature-icon">✓</div>10年+行业经验</div>
        <div class="about-feature"><div class="about-feature-icon">✓</div>5000+成功案例</div>
        <div class="about-feature"><div class="about-feature-icon">✓</div>200+专业团队</div>
        <div class="about-feature"><div class="about-feature-icon">✓</div>7x24贴心服务</div>
    </div>
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

<section class="section section-alt">
    <div class="section-header">
        <h2 class="section-title">我们的服务</h2>
        <p class="section-subtitle">全链路品牌数字化解决方案</p>
        <div class="section-divider"></div>
    </div>
    <div class="services-grid">
        <?php foreach ($services as $svc): ?>
        <a href="/mobile/service.php" class="service-card">
            <div class="service-icon"><?php echo h($svc['icon']); ?></div>
            <h3 class="service-title"><?php echo h($svc['title']); ?></h3>
            <p class="service-desc"><?php echo h($svc['desc']); ?></p>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="section-header">
        <h2 class="section-title">核心团队</h2>
        <p class="section-subtitle">汇聚行业精英，共创卓越未来</p>
        <div class="section-divider"></div>
    </div>
    <div class="team-grid">
        <?php foreach ($teamMembers as $member): ?>
        <div class="team-card">
            <div class="team-avatar no-img-placeholder">
                <span>👤</span>
            </div>
            <div class="team-info">
                <h4><?php echo h($member['name']); ?></h4>
                <p class="title"><?php echo h($member['title']); ?></p>
                <p><?php echo h($member['desc']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section-alt">
    <div class="section-header">
        <h2 class="section-title">企业文化</h2>
        <p class="section-subtitle">我们的价值观，驱动我们不断前行</p>
        <div class="section-divider"></div>
    </div>
    <div class="values-grid">
        <?php foreach ($values as $val): ?>
        <div class="value-card">
            <div class="value-icon"><?php echo h($val['icon']); ?></div>
            <h4><?php echo h($val['title']); ?></h4>
            <p><?php echo h($val['desc']); ?></p>
        </div>
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