<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// 自动检测移动端或桌面端
if (isMobile()) {
    // 如果URL中有/mobile或相关参数，保持在移动端
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (strpos($path, '/mobile') === false) {
        $query = $_SERVER['QUERY_STRING'] ?? '';
        $redirect = SITE_URL . '/mobile/' . ltrim($path, '/');
        if ($query) $redirect .= '?' . $query;
        redirect($redirect);
    }
} else {
    // 桌面端用户访问/mobile，重定向到首页
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (strpos($path, '/mobile') === 0) {
        $query = $_SERVER['QUERY_STRING'] ?? '';
        $redirect = SITE_URL . '/' . substr($path, 8);
        if ($query) $redirect .= '?' . $query;
        redirect($redirect);
    }
}

// 普通桌面端首页
$currentPage = 'home';
include __DIR__ . '/includes/header.php';
?>

<!-- Banner Section -->
<section class="banner" id="banner">
    <?php
    $slides = DB::getList('slides', ['status' => 1], 'sort', 'ASC');
    if (count($slides) > 0):
    ?>
    <div class="carousel" id="carousel">
        <?php foreach ($slides as $key => $slide): ?>
        <div class="carousel-item <?php echo $key === 0 ? 'active' : ''; ?>" style="background-image: url('<?php echo h($slide['image']); ?>');">
            <div class="carousel-content">
                <div class="carousel-title"><?php echo h($slide['title']); ?></div>
                <?php if (!empty($slide['subtitle'])): ?>
                <div class="carousel-subtitle"><?php echo h($slide['subtitle']); ?></div>
                <?php endif; ?>
                <?php if (!empty($slide['link'])): ?>
                <a href="<?php echo h($slide['link']); ?>" class="btn btn-primary btn-lg">了解更多 →</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <button class="carousel-prev" onclick="changeSlide(-1)">‹</button>
        <button class="carousel-next" onclick="changeSlide(1)">›</button>
        
        <div class="carousel-dots">
            <?php foreach ($slides as $key => $slide): ?>
            <span class="dot <?php echo $key === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $key; ?>)"></span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <?php 
            $stats = DB::getSettingValue('home_stats', []);
            foreach ($stats as $stat): 
            ?>
            <div class="stat-item">
                <div class="stat-num"><?php echo h($stat['num']); ?></div>
                <div class="stat-label"><?php echo h($stat['label']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">我们的服务</h2>
            <p class="section-subtitle">全链路品牌数字化解决方案</p>
        </div>
        <div class="services-grid">
            <?php
            $services = DB::getList('services', ['status' => 1], 'sort', 'ASC');
            foreach ($services as $service):
            ?>
            <div class="service-card">
                <div class="service-icon"><?php echo h($service['icon']); ?></div>
                <h3 class="service-title"><?php echo h($service['title']); ?></h3>
                <p class="service-desc"><?php echo h($service['desc']); ?></p>
                <a href="<?php echo siteUrl('service.php'); ?>" class="service-link">了解更多 →</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section">
    <div class="container">
        <div class="about-grid">
            <div class="about-content">
                <div class="section-header left">
                    <h2 class="section-title"><?php echo h(DB::getSettingValue('about_title', '关于森企动力')); ?></h2>
                </div>
                <p class="about-text"><?php echo h(DB::getSettingValue('about_content', '')); ?></p>
                <div class="about-features">
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <div class="feature-text">10年+行业经验</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <div class="feature-text">5000+成功案例</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <div class="feature-text">专业技术团队</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <div class="feature-text">7x24贴心服务</div>
                    </div>
                </div>
                <a href="<?php echo siteUrl('about.php'); ?>" class="btn btn-primary">了解更多</a>
            </div>
            <div class="about-image">
                <img src="/assets/images/about.jpg" alt="关于森企动力" onerror="this.style.display='none';this.parentNode.classList.add('no-image');">
            </div>
        </div>
    </div>
</section>

<!-- Cases Section -->
<section class="cases-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">精选案例</h2>
            <p class="section-subtitle">我们为客户创造的价值</p>
        </div>
        <div class="cases-grid">
            <?php
            $cases = DB::getList('cases', ['status' => 1], 'sort', 'ASC', 4);
            foreach ($cases as $case):
            ?>
            <div class="case-card">
                <a href="<?php echo siteUrl('case_detail.php?id=' . $case['id']); ?>">
                    <div class="case-image">
                        <img src="<?php echo h($case['image']); ?>" alt="<?php echo h($case['title']); ?>" onerror="this.src='/assets/images/placeholder.jpg';">
                        <div class="case-overlay">
                            <span class="case-cat"><?php echo h($case['category']); ?></span>
                        </div>
                    </div>
                    <div class="case-info">
                        <h3 class="case-title"><?php echo h($case['title']); ?></h3>
                        <p class="case-desc"><?php echo h(truncate($case['desc'], 80)); ?></p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-more">
            <a href="<?php echo siteUrl('case.php'); ?>" class="btn btn-outline">查看全部案例</a>
        </div>
    </div>
</section>

<!-- News Section -->
<section class="news-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">新闻动态</h2>
            <p class="section-subtitle">了解行业最新资讯</p>
        </div>
        <div class="news-grid">
            <?php
            $news = DB::getList('news', ['status' => 1], 'date', 'DESC', 3);
            foreach ($news as $item):
            ?>
            <div class="news-card">
                <div class="news-image">
                    <img src="<?php echo h($item['image']); ?>" alt="<?php echo h($item['title']); ?>" onerror="this.src='/assets/images/placeholder.jpg';">
                </div>
                <div class="news-info">
                    <div class="news-meta">
                        <span class="news-cat"><?php echo h($item['category']); ?></span>
                        <span class="news-date"><?php echo formatDate($item['date']); ?></span>
                    </div>
                    <h3 class="news-title"><?php echo h($item['title']); ?></h3>
                    <p class="news-desc"><?php echo h(truncate(strip_tags($item['content']), 100)); ?></p>
                    <a href="<?php echo siteUrl('news_detail.php?id=' . $item['id']); ?>" class="news-link">阅读全文 →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">准备好开启您的品牌数字化之旅了吗？</h2>
            <p class="cta-desc">联系我们的专家团队，获取专属解决方案</p>
            <a href="<?php echo siteUrl('contact.php'); ?>" class="btn btn-white btn-lg">立即咨询 →</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
