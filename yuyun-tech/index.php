<?php
/**
 * 首页
 */
$currentPage = 'home';
$pageTitle = '全球云服务专家';
require_once 'includes/header.php';

$slides = $site_data['slides'] ?? [];
$products = $site_data['products'] ?? [];
$partners = $site_data['partners'] ?? [];
$locations = $site_data['locations'] ?? [];
$certificates = $site_data['certificates'] ?? [];
$employees = $site_data['employees'] ?? [];
$testimonials = $site_data['testimonials'] ?? [];
$news = $site_data['news'] ?? [];

// 定义地图坐标（百分比）
$mapPins = [
    ['city' => '迪拜', 'region' => '中东地区', 'x' => 63, 'y' => 48],
    ['city' => '利雅得', 'region' => '中东地区', 'x' => 62, 'y' => 50],
    ['city' => '伦敦', 'region' => '欧洲地区', 'x' => 48, 'y' => 32],
    ['city' => '巴黎', 'region' => '欧洲地区', 'x' => 49, 'y' => 34],
    ['city' => '法兰克福', 'region' => '欧洲地区', 'x' => 51, 'y' => 33],
    ['city' => '北京', 'region' => '中国', 'x' => 72, 'y' => 36],
    ['city' => '青岛', 'region' => '中国', 'x' => 74, 'y' => 35],
    ['city' => '上海', 'region' => '中国', 'x' => 74, 'y' => 40],
    ['city' => '深圳', 'region' => '中国', 'x' => 73, 'y' => 45],
    ['city' => '莫斯科', 'region' => '俄罗斯', 'x' => 58, 'y' => 28],
    ['city' => '圣彼得堡', 'region' => '俄罗斯', 'x' => 59, 'y' => 25],
    ['city' => '首尔', 'region' => '韩国', 'x' => 77, 'y' => 36],
    ['city' => '新加坡', 'region' => '东南亚', 'x' => 73, 'y' => 58],
    ['city' => '悉尼', 'region' => '澳大利亚', 'x' => 86, 'y' => 68],
    ['city' => '纽约', 'region' => '北美地区', 'x' => 27, 'y' => 34],
    ['city' => '华盛顿', 'region' => '北美地区', 'x' => 28, 'y' => 35],
    ['city' => '旧金山', 'region' => '北美地区', 'x' => 19, 'y' => 38],
];
?>

<!-- 轮播图 -->
<section class="hero">
    <div class="slider">
        <?php foreach ($slides as $idx => $slide): ?>
        <div class="slide <?php echo $idx===0?'active':''; ?>">
            <div class="slide-bg" style="background:linear-gradient(135deg, <?php echo htmlspecialchars($slide['color'] ?? '#1a73e8'); ?>33, #0a0a1a), radial-gradient(circle at 70% 50%, <?php echo htmlspecialchars($slide['color'] ?? '#1a73e8'); ?>44, transparent 50%);"></div>
            <div class="container">
                <div class="slide-content" style="max-width:720px;">
                    <span class="slide-subtitle"><?php echo htmlspecialchars($slide['subtitle'] ?? '全球云服务专家'); ?></span>
                    <h1 class="slide-title"><?php echo htmlspecialchars($slide['title'] ?? '语云科技'); ?></h1>
                    <p class="slide-desc"><?php echo htmlspecialchars($slide['desc'] ?? '为您提供安全、稳定、高效的云基础设施'); ?></p>
                    <div class="slide-actions">
                        <a href="<?php echo htmlspecialchars($slide['link'] ?? 'products.php'); ?>" class="btn btn-primary btn-lg">立即开始 <i class="fas fa-arrow-right"></i></a>
                        <a href="about.php" class="btn btn-outline btn-lg" style="color:#fff;border-color:rgba(255,255,255,0.5);">了解更多</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <button class="slider-arrow prev" aria-label="上一张"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-arrow next" aria-label="下一张"><i class="fas fa-chevron-right"></i></button>

        <div class="slider-nav">
            <?php foreach ($slides as $idx => $s): ?>
            <button class="slider-dot <?php echo $idx===0?'active':''; ?>" aria-label="第<?php echo $idx+1; ?>张"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 数据统计 -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number" data-count="15" data-unit="+">15+</div>
                <div class="stat-label">全球数据中心</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="50000" data-unit="+">50000+</div>
                <div class="stat-label">企业客户</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="99" data-unit=".99%">99.99%</div>
                <div class="stat-label">服务可用性</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-count="7" data-unit="x24">7x24</div>
                <div class="stat-label">专业技术支持</div>
            </div>
        </div>
    </div>
</section>

<!-- 产品服务 -->
<section class="section">
    <div class="container">
        <div class="section-title fade-in">
            <h2>产品与服务</h2>
            <p>一站式云解决方案，为您的业务提供全方位支持</p>
        </div>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card fade-in">
                <div class="product-icon" style="background:<?php echo htmlspecialchars($product['color'] ?? '#1a73e8'); ?>;">
                    <i class="fas <?php echo htmlspecialchars($product['icon'] ?? 'fa-server'); ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($product['name'] ?? '产品名称'); ?></h3>
                <p><?php echo htmlspecialchars($product['desc'] ?? '产品描述'); ?></p>
                <div class="product-price"><?php echo htmlspecialchars($product['price'] ?? '¥99/月起'); ?></div>
                <a href="<?php echo htmlspecialchars($product['link'] ?? 'products.php'); ?>" class="product-more">
                    了解详情 <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 合作伙伴滚动 -->
<section class="partners-section">
    <div class="container">
        <div class="section-title fade-in">
            <h2>我们与以下企业/组织携手共进</h2>
            <p>全球领先的技术合作伙伴，为您提供最优质的服务</p>
        </div>
    </div>
    <div class="partners-scroll">
        <div class="partners-track">
            <?php
            $allPartners = array_merge($partners, $partners);
            foreach ($allPartners as $p):
            ?>
            <div class="partner-item">
                <i class="fas fa-handshake"></i>
                <span><?php echo htmlspecialchars($p['name'] ?? '合作伙伴'); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 公司分布地图 -->
<section class="map-section">
    <div class="container map-container">
        <div class="section-title fade-in" style="color:#fff;">
            <h2 style="color:#fff;">全球节点分布</h2>
            <p style="color:rgba(255,255,255,0.7);">遍布全球的数据中心，为您提供低延迟、高可用的云服务</p>
        </div>
        <div class="world-map fade-in">
            <!-- 简化的世界地图 SVG 背景 -->
            <svg viewBox="0 0 100 60" style="position:absolute;inset:0;width:100%;height:100%;opacity:0.25;" preserveAspectRatio="none">
                <path d="M10,20 Q15,15 20,18 Q25,22 28,20 L30,25 Q25,27 22,25 L18,27 Q12,25 10,22 Z" fill="#fff" stroke="#fff" stroke-width="0.2"/>
                <path d="M35,18 Q40,14 45,16 Q52,18 58,15 L62,18 Q60,22 55,22 Q50,24 45,22 L40,22 Q35,20 35,18 Z" fill="#fff" stroke="#fff" stroke-width="0.2"/>
                <path d="M5,25 Q8,23 10,25 Q12,28 10,30 L7,28 Z" fill="#fff" stroke="#fff" stroke-width="0.2"/>
                <path d="M60,25 Q65,22 70,24 Q75,26 74,28 Q72,30 68,28 Q64,28 60,26 Z" fill="#fff" stroke="#fff" stroke-width="0.2"/>
                <path d="M72,30 Q75,28 78,30 Q80,32 78,34 Q75,36 72,34 Z" fill="#fff" stroke="#fff" stroke-width="0.2"/>
                <path d="M80,38 Q85,36 88,38 Q90,42 88,44 Q85,46 82,44 Z" fill="#fff" stroke="#fff" stroke-width="0.2"/>
                <path d="M20,30 Q25,28 28,32 Q30,35 26,37 L22,35 Z" fill="#fff" stroke="#fff" stroke-width="0.2"/>
                <circle cx="50" cy="50" r="1" fill="#fff" opacity="0.5"/>
                <circle cx="30" cy="55" r="0.8" fill="#fff" opacity="0.4"/>
                <circle cx="75" cy="52" r="0.8" fill="#fff" opacity="0.4"/>
            </svg>

            <?php foreach ($mapPins as $pin): ?>
            <div class="map-pin" style="left:<?php echo $pin['x']; ?>%;top:<?php echo $pin['y']; ?>%;"
                 data-city="<?php echo htmlspecialchars($pin['city']); ?>"
                 data-region="<?php echo htmlspecialchars($pin['region']); ?>">
            </div>
            <?php endforeach; ?>
        </div>

        <div class="locations-info fade-in">
            <?php foreach ($locations as $loc): ?>
            <div class="location-card">
                <h4><i class="fas fa-map-marker-alt" style="color:#ff6b35;margin-right:6px;"></i><?php echo htmlspecialchars($loc['region'] ?? ''); ?></h4>
                <p><?php echo htmlspecialchars(implode(' · ', $loc['cities'] ?? [])); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 资质证书 -->
<section class="section">
    <div class="container">
        <div class="section-title fade-in">
            <h2>资质与认证</h2>
            <p>权威机构认证，值得信赖的云服务提供商</p>
        </div>
        <div class="certs-grid">
            <?php foreach ($certificates as $cert): ?>
            <div class="cert-card fade-in">
                <div class="cert-thumb">
                    <i class="fas fa-certificate"></i>
                </div>
                <h4><?php echo htmlspecialchars($cert['name'] ?? '资质证书'); ?></h4>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 用户评价 -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title fade-in">
            <h2>客户好评</h2>
            <p>来自客户的真实反馈，是我们持续进步的动力</p>
        </div>
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial fade-in">
                <div class="testimonial-content">"<?php echo htmlspecialchars($t['content'] ?? ''); ?>"</div>
                <div class="testimonial-author">
                    <div class="author-avatar"><?php echo mb_substr($t['name'] ?? '客', 0, 1); ?></div>
                    <div class="author-info">
                        <strong><?php echo htmlspecialchars($t['name'] ?? '客户'); ?></strong>
                        <span><?php echo htmlspecialchars($t['role'] ?? '客户代表'); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 员工卡片 -->
<section class="section">
    <div class="container">
        <div class="section-title fade-in">
            <h2>专业团队</h2>
            <p>资深行业专家，为您提供专业服务</p>
        </div>
        <div class="employees-grid">
            <?php foreach ($employees as $emp): ?>
            <div class="employee-card fade-in">
                <div class="employee-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="employee-info">
                    <h4><?php echo htmlspecialchars($emp['name'] ?? ''); ?></h4>
                    <div class="title"><?php echo htmlspecialchars($emp['title'] ?? ''); ?></div>
                    <p><?php echo htmlspecialchars($emp['desc'] ?? ''); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 新闻动态 -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title fade-in">
            <h2>最新动态</h2>
            <p>了解语云科技的最新资讯和行业动态</p>
        </div>
        <div class="news-grid">
            <?php foreach ($news as $n): ?>
            <div class="news-card fade-in">
                <div class="news-image">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="news-body">
                    <div class="news-date"><?php echo htmlspecialchars($n['date'] ?? ''); ?></div>
                    <h4><?php echo htmlspecialchars($n['title'] ?? ''); ?></h4>
                    <p><?php echo htmlspecialchars($n['desc'] ?? ''); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
