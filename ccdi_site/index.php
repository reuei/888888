<?php
/**
 * 网站首页 v5.0.0
 * 中央纪委国家监委网站 CMS 系统
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$page_title = '首页';
$current_page = 'index';

// 获取轮播图
$carousel_items = get_carousel();

// 获取置顶文章
$top_articles = db_fetch_all("SELECT * FROM articles WHERE status = 'publish' AND is_top = 1 ORDER BY publish_time DESC LIMIT 5");

// 获取最新文章
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

// 获取要闻和公共动态类别的文章（用于Tab切换）
$yaowen_articles = db_fetch_all(
    "SELECT a.* FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE c.slug = 'yaowen' AND a.status = 'publish' ORDER BY a.publish_time DESC LIMIT 8"
);
$gongzuodongtai_articles = db_fetch_all(
    "SELECT a.* FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE c.slug = 'gongzuodongtai' AND a.status = 'publish' ORDER BY a.publish_time DESC LIMIT 8"
);

include TEMPLATES_PATH . 'header.php';
?>

<!-- 首页布局：三栏 -->
<div class="home-layout">
    <!-- 主内容区：左栏 + 中栏 -->
    <div class="home-layout__main" style="display:grid;grid-template-columns:1fr 1fr;gap:var(--spacing-lg);">
        <!-- 左栏：轮播图 + 标签页新闻 -->
        <div style="display:flex;flex-direction:column;gap:var(--spacing-lg);">
            <!-- 轮播图 -->
            <?php if (!empty($carousel_items)): ?>
            <div class="carousel-container" id="carousel">
                <div class="carousel-track">
                    <?php foreach ($carousel_items as $index => $item): ?>
                    <div class="carousel-slide<?php echo $index === 0 ? ' active' : ''; ?>">
                        <?php if ($item['link']): ?><a href="<?php echo htmlspecialchars($item['link']); ?>"><?php endif; ?>
                        <img src="<?php echo site_url('uploads/' . $item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php if ($item['link']): ?></a><?php endif; ?>
                        <?php if ($item['title']): ?>
                        <div class="carousel-caption">
                            <h3 class="carousel-caption__title"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <?php if ($item['description']): ?>
                            <p class="carousel-caption__desc"><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
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
            <?php endif; ?>

            <!-- 标签页新闻区 -->
            <div class="section">
                <div class="home-tabs">
                    <div class="home-tabs__nav">
                        <button class="home-tab-btn active" data-tab="tab-yaowen">要闻</button>
                        <button class="home-tab-btn" data-tab="tab-gongzuodongtai">工作动态</button>
                    </div>
                    <!-- 要闻面板 -->
                    <div class="home-tab-panel active" id="tab-yaowen">
                        <div class="news-list">
                            <?php if (!empty($yaowen_articles)): ?>
                                <?php foreach ($yaowen_articles as $article): ?>
                                <div class="news-item">
                                    <span class="news-item__dot"></span>
                                    <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>" class="news-item__title"><?php echo htmlspecialchars($article['title']); ?></a>
                                    <span class="news-item__time"><?php echo format_time($article['publish_time'], 'm-d'); ?></span>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="news-item">
                                    <span class="news-item__title" style="color:var(--color-text-muted);">暂无要闻</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- 工作动态面板 -->
                    <div class="home-tab-panel" id="tab-gongzuodongtai">
                        <div class="news-list">
                            <?php if (!empty($gongzuodongtai_articles)): ?>
                                <?php foreach ($gongzuodongtai_articles as $article): ?>
                                <div class="news-item">
                                    <span class="news-item__dot"></span>
                                    <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>" class="news-item__title"><?php echo htmlspecialchars($article['title']); ?></a>
                                    <span class="news-item__time"><?php echo format_time($article['publish_time'], 'm-d'); ?></span>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="news-item">
                                    <span class="news-item__title" style="color:var(--color-text-muted);">暂无工作动态</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 中栏：横幅图片 + 分类文章 -->
        <div style="display:flex;flex-direction:column;gap:var(--spacing-lg);">
            <?php if ($banner_img): ?>
            <div class="banner-section">
                <img src="<?php echo $banner_img; ?>" alt="宣传横幅" class="banner-section__img">
            </div>
            <?php endif; ?>

            <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
            <?php if (!empty($category_articles[$cat['id']])): ?>
            <div class="section">
                <div class="section-header">
                    <h2 class="section-header__title">
                        <span class="section-header__title-icon"><i class="fas fa-folder"></i></span>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </h2>
                    <a href="<?php echo site_url('category.php?slug=' . $cat['slug']); ?>" class="section-header__more">更多 <i class="fas fa-angle-right"></i></a>
                </div>
                <div class="section__body">
                    <div class="news-list">
                        <?php foreach ($category_articles[$cat['id']] as $article): ?>
                        <div class="news-item">
                            <span class="news-item__dot"></span>
                            <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>" class="news-item__title"><?php echo htmlspecialchars($article['title']); ?></a>
                            <span class="news-item__time"><?php echo format_time($article['publish_time'], 'm-d'); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- 右栏：快捷入口 + 举报方式 -->
    <div class="home-layout__sidebar">
        <!-- 快捷入口网格 -->
        <div class="sidebar-widget">
            <div class="sidebar-widget__header">
                <span class="sidebar-widget__header-icon"><i class="fas fa-bolt"></i></span>
                快捷入口
            </div>
            <div class="sidebar-widget__body">
                <div class="quick-link-grid">
                    <a href="<?php echo site_url('report.php'); ?>" class="quick-link-grid__item">
                        <span class="quick-link-grid__icon"><i class="fas fa-bullhorn"></i></span>
                        <span class="quick-link-grid__label">监督举报</span>
                    </a>
                    <a href="<?php echo site_url('category.php?slug=dangjifagui'); ?>" class="quick-link-grid__item">
                        <span class="quick-link-grid__icon"><i class="fas fa-gavel"></i></span>
                        <span class="quick-link-grid__label">党纪法规</span>
                    </a>
                    <a href="<?php echo site_url('category.php?slug=jifabaike'); ?>" class="quick-link-grid__item">
                        <span class="quick-link-grid__icon"><i class="fas fa-book"></i></span>
                        <span class="quick-link-grid__label">纪法百科</span>
                    </a>
                    <a href="<?php echo site_url('category.php?slug=shipin'); ?>" class="quick-link-grid__item">
                        <span class="quick-link-grid__icon"><i class="fas fa-video"></i></span>
                        <span class="quick-link-grid__label">视频中心</span>
                    </a>
                    <a href="<?php echo site_url('category.php?slug=shenchadiaocha'); ?>" class="quick-link-grid__item">
                        <span class="quick-link-grid__icon"><i class="fas fa-search"></i></span>
                        <span class="quick-link-grid__label">审查调查</span>
                    </a>
                    <a href="<?php echo site_url('category.php?slug=guojizhuitao'); ?>" class="quick-link-grid__item">
                        <span class="quick-link-grid__icon"><i class="fas fa-globe"></i></span>
                        <span class="quick-link-grid__label">国际追逃</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 举报方式 -->
        <div class="report-widget">
            <div class="report-widget__title">
                <i class="fas fa-phone-alt"></i> 举报方式
            </div>
            <p class="report-widget__text">
                <strong>来信地址：</strong>中央纪委国家监委信访室<br>
                <strong>举报电话：</strong>12388<br>
                <strong>举报网站：</strong><a href="http://www.12388.gov.cn" target="_blank" style="color:var(--color-primary);">www.12388.gov.cn</a>
            </p>
            <a href="<?php echo site_url('report.php'); ?>" class="report-widget__btn">在线举报</a>
        </div>
    </div>
</div>

<?php include TEMPLATES_PATH . 'footer.php'; ?>