<?php
/**
 * 网站首页
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$page_title = '首页';
$current_page = 'index';

// 获取轮播图
$carousel_items = get_carousel();

// 获取要闻（置顶+最新）
$top_articles = db_fetch_all("SELECT * FROM articles WHERE status = 'publish' AND is_top = 1 ORDER BY publish_time DESC LIMIT 5");
$latest_articles = db_fetch_all("SELECT * FROM articles WHERE status = 'publish' ORDER BY publish_time DESC LIMIT 10");

// 获取横幅图片
$banner_img = get_banner_image();

// 获取各分类最新文章
$categories = get_categories();
$category_articles = [];
foreach ($categories as $cat) {
    $category_articles[$cat['id']] = db_fetch_all(
        "SELECT * FROM articles WHERE category_id = ? AND status = 'publish' ORDER BY publish_time DESC LIMIT 5",
        [$cat['id']]
    );
}

include TEMPLATES_PATH . 'header.php';
?>

<!-- 首页内容区 -->
<div class="home-layout">
    <!-- 左侧：轮播图 + 要闻 -->
    <div class="home-left">
        <!-- 轮播图 -->
        <?php if (!empty($carousel_items)): ?>
        <div class="carousel-section">
            <div class="carousel-container" id="carousel">
                <div class="carousel-track" id="carouselTrack">
                    <?php foreach ($carousel_items as $index => $item): ?>
                    <div class="carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                        <?php if ($item['link']): ?><a href="<?php echo htmlspecialchars($item['link']); ?>"><?php endif; ?>
                        <img src="<?php echo site_url('uploads/' . $item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php if ($item['title']): ?>
                        <div class="carousel-caption">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <?php if ($item['description']): ?>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($item['link']): ?></a><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="carousel-controls">
                    <span class="carousel-counter"><span class="current">1</span> / <?php echo count($carousel_items); ?></span>
                    <div class="carousel-progress-track">
                        <div class="carousel-progress-fill" id="carouselProgressFill"></div>
                    </div>
                </div>
                <button class="carousel-prev" id="carouselPrev"><i class="fas fa-chevron-left"></i></button>
                <button class="carousel-next" id="carouselNext"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        <?php endif; ?>

        <!-- 要闻列表 -->
        <div class="section news-section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-newspaper"></i> 要闻</h2>
                <a href="<?php echo site_url('category.php?slug=yaowen'); ?>" class="more-link">更多 <i class="fas fa-angle-right"></i></a>
            </div>
            <div class="news-list">
                <?php if (!empty($top_articles)): ?>
                    <?php foreach ($top_articles as $article): ?>
                    <div class="news-item top-news">
                        <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>">
                            <?php if ($article['cover_image']): ?>
                            <div class="news-thumb">
                                <img src="<?php echo site_url('uploads/' . $article['cover_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                            </div>
                            <?php endif; ?>
                            <div class="news-info">
                                <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                                <p class="news-summary"><?php echo str_cut($article['summary'] ?: strip_tags($article['content']), 120); ?></p>
                                <span class="news-time"><i class="far fa-clock"></i> <?php echo format_time($article['publish_time']); ?></span>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php foreach ($latest_articles as $article): ?>
                <div class="news-item">
                    <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>">
                        <span class="news-dot"></span>
                        <span class="news-title-text"><?php echo htmlspecialchars($article['title']); ?></span>
                        <span class="news-time"><?php echo format_time($article['publish_time'], 'm-d'); ?></span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- 中间：横幅图片 -->
    <div class="home-center">
        <?php if ($banner_img): ?>
        <div class="banner-section">
            <img src="<?php echo $banner_img; ?>" alt="宣传横幅" class="banner-image">
        </div>
        <?php endif; ?>
        
        <!-- 工作动态 -->
        <?php if (!empty($category_articles)): ?>
        <?php foreach ($categories as $cat): ?>
        <?php if (!empty($category_articles[$cat['id']])): ?>
        <div class="section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-folder"></i> <?php echo htmlspecialchars($cat['name']); ?></h2>
                <a href="<?php echo site_url('category.php?slug=' . $cat['slug']); ?>" class="more-link">更多 <i class="fas fa-angle-right"></i></a>
            </div>
            <div class="news-list compact">
                <?php foreach ($category_articles[$cat['id']] as $article): ?>
                <div class="news-item">
                    <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>">
                        <span class="news-dot"></span>
                        <span class="news-title-text"><?php echo htmlspecialchars($article['title']); ?></span>
                        <span class="news-time"><?php echo format_time($article['publish_time'], 'm-d'); ?></span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- 右侧：快捷入口 + 专题 -->
    <div class="home-right">
        <!-- 快捷入口 -->
        <div class="sidebar-widget quick-links">
            <h3 class="widget-title"><i class="fas fa-bolt"></i> 快捷入口</h3>
            <div class="quick-link-grid">
                <a href="<?php echo site_url('report.php'); ?>" class="quick-link-item report-link">
                    <i class="fas fa-bullhorn"></i>
                    <span>监督举报</span>
                </a>
                <a href="<?php echo site_url('category.php?slug=dangjifagui'); ?>" class="quick-link-item">
                    <i class="fas fa-gavel"></i>
                    <span>党纪法规</span>
                </a>
                <a href="<?php echo site_url('category.php?slug=jifabaike'); ?>" class="quick-link-item">
                    <i class="fas fa-book"></i>
                    <span>纪法百科</span>
                </a>
                <a href="<?php echo site_url('category.php?slug=shipin'); ?>" class="quick-link-item">
                    <i class="fas fa-video"></i>
                    <span>视频中心</span>
                </a>
                <a href="<?php echo site_url('category.php?slug=shenchadiaocha'); ?>" class="quick-link-item">
                    <i class="fas fa-search"></i>
                    <span>审查调查</span>
                </a>
                <a href="<?php echo site_url('category.php?slug=guojizhuitao'); ?>" class="quick-link-item">
                    <i class="fas fa-globe"></i>
                    <span>国际追逃</span>
                </a>
            </div>
        </div>

        <!-- 举报通道 -->
        <div class="sidebar-widget report-widget">
            <h3 class="widget-title"><i class="fas fa-phone-alt"></i> 举报方式</h3>
            <div class="report-info">
                <p><strong>来信地址：</strong>中央纪委国家监委信访室</p>
                <p><strong>举报电话：</strong>12388</p>
                <p><strong>举报网站：</strong><a href="http://www.12388.gov.cn" target="_blank">www.12388.gov.cn</a></p>
                <a href="<?php echo site_url('report.php'); ?>" class="btn-report">在线举报</a>
            </div>
        </div>
    </div>
</div>

<?php include TEMPLATES_PATH . 'footer.php'; ?>