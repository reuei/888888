<?php
/**
 * 产品介绍
 */
$currentPage = 'products';
$pageTitle = '产品介绍';
require_once 'includes/header.php';
$products = $site_data['products'] ?? [];
?>

<div class="page-banner">
    <div class="container">
        <h1>产品与服务</h1>
        <p>一站式云解决方案，满足您的所有业务需求</p>
        <div class="breadcrumbs">
            <a href="index.php">首页</a>
            <span> / </span>
            <span>产品介绍</span>
        </div>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="section-title fade-in">
            <h2>核心产品</h2>
            <p>为企业提供全方位的云计算服务</p>
        </div>

        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card fade-in">
                <div class="product-icon" style="background:<?php echo htmlspecialchars($product['color'] ?? '#1a73e8'); ?>;">
                    <i class="fas <?php echo htmlspecialchars($product['icon'] ?? 'fa-server'); ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($product['name'] ?? ''); ?></h3>
                <p><?php echo htmlspecialchars($product['desc'] ?? ''); ?></p>
                <div class="product-price"><?php echo htmlspecialchars($product['price'] ?? ''); ?></div>
                <a href="contact.php" class="product-more">立即咨询 <i class="fas fa-arrow-right"></i></a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 产品特色 -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title fade-in">
            <h2>产品优势</h2>
            <p>为什么选择语云科技</p>
        </div>

        <div class="feature-list">
            <div class="feature-item fade-in">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <div>
                    <h4>安全可靠</h4>
                    <p>多层安全防护体系，数据加密存储和传输，确保您的数据安全</p>
                </div>
            </div>
            <div class="feature-item fade-in">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <div>
                    <h4>高性能</h4>
                    <p>采用最新硬件设备，提供卓越的计算和网络性能</p>
                </div>
            </div>
            <div class="feature-item fade-in">
                <div class="feature-icon"><i class="fas fa-expand-arrows-alt"></i></div>
                <div>
                    <h4>弹性扩展</h4>
                    <p>按需扩展资源，支持业务快速增长，无需担心基础设施</p>
                </div>
            </div>
            <div class="feature-item fade-in">
                <div class="feature-icon"><i class="fas fa-globe"></i></div>
                <div>
                    <h4>全球节点</h4>
                    <p>遍布全球的数据中心节点，为您提供低延迟访问体验</p>
                </div>
            </div>
            <div class="feature-item fade-in">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <div>
                    <h4>专业支持</h4>
                    <p>7x24小时专业技术支持团队，随时响应您的需求</p>
                </div>
            </div>
            <div class="feature-item fade-in">
                <div class="feature-icon"><i class="fas fa-coins"></i></div>
                <div>
                    <h4>经济实惠</h4>
                    <p>灵活的计费方式，按需付费，有效降低您的IT成本</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 应用场景 -->
<section class="section">
    <div class="container">
        <div class="section-title fade-in">
            <h2>应用场景</h2>
            <p>满足各行业的不同业务需求</p>
        </div>
        <div class="products-grid">
            <div class="product-card fade-in">
                <div class="product-icon" style="background:#1a73e8;"><i class="fas fa-shopping-cart"></i></div>
                <h3>电商零售</h3>
                <p>为电商平台提供稳定、可扩展的基础设施支持，应对大流量访问</p>
                <a href="contact.php" class="product-more">了解更多 <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="product-card fade-in">
                <div class="product-icon" style="background:#ff6b35;"><i class="fas fa-gamepad"></i></div>
                <h3>游戏娱乐</h3>
                <p>低延迟、高可用的游戏服务器，为玩家提供极致的游戏体验</p>
                <a href="contact.php" class="product-more">了解更多 <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="product-card fade-in">
                <div class="product-icon" style="background:#00a86b;"><i class="fas fa-university"></i></div>
                <h3>金融服务</h3>
                <p>符合金融行业标准的安全体系，保障业务和数据安全</p>
                <a href="contact.php" class="product-more">了解更多 <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="product-card fade-in">
                <div class="product-icon" style="background:#9b59b6;"><i class="fas fa-graduation-cap"></i></div>
                <h3>在线教育</h3>
                <p>支持大规模在线课程和直播教学，提供稳定的平台服务</p>
                <a href="contact.php" class="product-more">了解更多 <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="product-card fade-in">
                <div class="product-icon" style="background:#e74c3c;"><i class="fas fa-hospital"></i></div>
                <h3>医疗健康</h3>
                <p>为医疗行业提供安全、合规的数据存储和处理服务</p>
                <a href="contact.php" class="product-more">了解更多 <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="product-card fade-in">
                <div class="product-icon" style="background:#2ecc71;"><i class="fas fa-industry"></i></div>
                <h3>企业办公</h3>
                <p>企业级云服务，提供办公系统、邮箱、存储等一站式解决方案</p>
                <a href="contact.php" class="product-more">了解更多 <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
