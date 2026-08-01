<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$currentPage = 'service';
$services = DB::getList('services', ['status' => 1], 'sort', 'ASC');

$process = [
    ['step' => '01', 'title' => '需求分析', 'desc' => '深入了解客户业务现状、痛点和目标，进行全面的行业调研和竞品分析。'],
    ['step' => '02', 'title' => '策略制定', 'desc' => '基于分析结果，制定详细的品牌数字化策略和可执行的落地方案。'],
    ['step' => '03', 'title' => '设计开发', 'desc' => '专业的设计和技术团队，高品质交付，确保每个细节都达到预期标准。'],
    ['step' => '04', 'title' => '测试上线', 'desc' => '全面的测试和优化，确保产品在各种环境下稳定运行，顺利上线。'],
    ['step' => '05', 'title' => '运营维护', 'desc' => '持续的运营支持和数据分析，帮助客户实现长期业务增长。']
];

$whyUs = [
    ['icon' => '🏆', 'title' => '10年+行业经验', 'desc' => '深耕品牌数字化领域，服务超过5000家企业客户'],
    ['icon' => '👨‍💻', 'title' => '200+专业团队', 'desc' => '汇聚设计、开发、运营各领域资深专家'],
    ['icon' => '⚡', 'title' => '快速响应交付', 'desc' => '敏捷开发流程，高效项目管理，确保按时交付'],
    ['icon' => '🔒', 'title' => '品质保证', 'desc' => '严格的质量管控体系，多重测试验证，确保交付品质'],
    ['icon' => '🎯', 'title' => '量身定制', 'desc' => '根据客户实际需求提供定制化解决方案'],
    ['icon' => '📞', 'title' => '7x24服务', 'desc' => '全天候技术支持和客户服务，随时响应您的需求']
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1a5fff">
    <title>服务项目 - <?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="/mobile/assets/css/style.css">
</head>
<body class="page page-service">

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
    <h1>我们的服务</h1>
    <p>全链路品牌数字化解决方案，一站式服务</p>
    <div class="breadcrumb-nav">
        <a href="/mobile/index.php">首页</a>
        <span class="sep">/</span>
        <span class="current">服务项目</span>
    </div>
</div>

<section class="section">
    <div class="services-grid">
        <?php foreach ($services as $svc): ?>
        <div class="service-card">
            <div class="service-icon"><?php echo h($svc['icon']); ?></div>
            <h3 class="service-title"><?php echo h($svc['title']); ?></h3>
            <p class="service-desc"><?php echo h($svc['desc']); ?></p>
            <a href="/mobile/contact.php" class="service-link" style="color: var(--primary); font-weight: 600; font-size: 0.85rem;">了解更多 →</a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section process-section">
    <div class="section-header">
        <h2 class="section-title">服务流程</h2>
        <p class="section-subtitle">专业的项目管理流程</p>
        <div class="section-divider"></div>
    </div>
    <div class="process-steps">
        <?php foreach ($process as $step): ?>
        <div class="process-step">
            <div class="process-step-num"><?php echo h($step['step']); ?></div>
            <div class="process-step-info">
                <h4><?php echo h($step['title']); ?></h4>
                <p><?php echo h($step['desc']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="section-header">
        <h2 class="section-title">为什么选择我们</h2>
        <p class="section-subtitle">六大核心优势，助力您的品牌腾飞</p>
        <div class="section-divider"></div>
    </div>
    <div class="why-list">
        <?php foreach ($whyUs as $item): ?>
        <div class="why-item">
            <div class="why-item-icon"><?php echo h($item['icon']); ?></div>
            <div class="why-item-info">
                <h4><?php echo h($item['title']); ?></h4>
                <p><?php echo h($item['desc']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="cta-section">
    <h2>需要定制化解决方案？</h2>
    <p>联系我们的专家团队，获取专属服务方案</p>
    <a href="/mobile/contact.php" class="btn">立即咨询 →</a>
</section>

<nav class="tab-bar">
    <a href="/mobile/index.php" class="tab-item">
        <img src="/mobile/assets/images/home.svg" alt="首页">
        <span>首页</span>
    </a>
    <a href="/mobile/service.php" class="tab-item active">
        <img src="/mobile/assets/images/service_h.svg" alt="服务">
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