<?php
/**
 * 合作伙伴
 */
$currentPage = 'partners';
$pageTitle = '合作伙伴';
require_once 'includes/header.php';
$partners = $site_data['partners'] ?? [];
?>

<div class="page-banner">
    <div class="container">
        <h1>合作伙伴</h1>
        <p>与全球领先的企业携手共进，共创美好未来</p>
        <div class="breadcrumbs">
            <a href="index.php">首页</a>
            <span> / </span>
            <span>合作伙伴</span>
        </div>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="section-title fade-in">
            <h2>战略合作伙伴</h2>
            <p>携手全球领先企业，共同打造优质云服务生态</p>
        </div>

        <div class="products-grid">
            <?php foreach ($partners as $partner): ?>
            <div class="product-card fade-in">
                <div class="product-icon" style="background:linear-gradient(135deg,#1a73e8,#ff6b35);">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3><?php echo htmlspecialchars($partner['name'] ?? '合作伙伴'); ?></h3>
                <p>与<?php echo htmlspecialchars($partner['name'] ?? ''); ?>建立战略合作，共同提供优质的云服务解决方案</p>
                <a href="#" class="product-more">了解详情 <i class="fas fa-arrow-right"></i></a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 合作伙伴计划 -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title fade-in">
            <h2>合作伙伴计划</h2>
            <p>加入语云科技合作伙伴计划，共享发展机遇</p>
        </div>

        <div class="feature-list">
            <div class="feature-item fade-in">
                <div class="feature-icon"><i class="fas fa-handshake"></i></div>
                <div>
                    <h4>渠道合作伙伴</h4>
                    <p>面向代理商、经销商、集成商，提供优惠合作政策</p>
                </div>
            </div>
            <div class="feature-item fade-in">
                <div class="feature-icon"><i class="fas fa-code"></i></div>
                <div>
                    <h4>技术合作伙伴</h4>
                    <p>面向软件开发商、ISV，提供API和技术支持</p>
                </div>
            </div>
            <div class="feature-item fade-in">
                <div class="feature-icon"><i class="fas fa-graduation-cap"></i></div>
                <div>
                    <h4>教育合作伙伴</h4>
                    <p>面向高校和培训机构，提供教育资源和实践平台</p>
                </div>
            </div>
            <div class="feature-item fade-in">
                <div class="feature-icon"><i class="fas fa-star"></i></div>
                <div>
                    <h4>战略合作伙伴</h4>
                    <p>面向行业领先企业，开展深度战略合作</p>
                </div>
            </div>
        </div>

        <div style="text-align:center;margin-top:40px;">
            <a href="contact.php" class="btn btn-primary btn-lg">
                <i class="fas fa-handshake"></i> 申请成为合作伙伴
            </a>
        </div>
    </div>
</section>

<!-- 合作优势 -->
<section class="section">
    <div class="container">
        <div class="section-title fade-in">
            <h2>合作优势</h2>
            <p>选择与语云科技合作的理由</p>
        </div>

        <div class="stats-grid">
            <div class="stat-item fade-in">
                <div class="stat-number" data-count="50" data-unit="+">50+</div>
                <div class="stat-label">战略合作伙伴</div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number" data-count="1000" data-unit="+">1000+</div>
                <div class="stat-label">渠道合作伙伴</div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number" data-count="15" data-unit="+">15+</div>
                <div class="stat-label">行业合作领域</div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number" data-count="99" data-unit="%">99%</div>
                <div class="stat-label">合作伙伴满意度</div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
