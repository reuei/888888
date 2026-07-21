<?php
/**
 * 网站首页 v7.0.0
 * 中央纪委国家监委网站 CMS 系统
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';
$page_title = '首页';
$current_page = 'index';

// Fetch data
$carousel_items = get_carousel(); // all carousel items (image + video)
$yaowen_articles = db_fetch_all("SELECT a.* FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE c.slug = 'yaowen' AND a.status = 'publish' ORDER BY a.publish_time DESC LIMIT 8");
$gongzuodongtai_articles = db_fetch_all("SELECT a.* FROM articles a LEFT JOIN categories c ON a.category_id = c.id WHERE c.slug = 'gongzuodongtai' AND a.status = 'publish' ORDER BY a.publish_time DESC LIMIT 8");
$categories = get_categories();
$staff_members = get_staff(4);
$footer_carousel = get_footer_carousel();
$videos = get_videos(4);

include TEMPLATES_PATH . 'header.php';
?>

<!-- Section 1: Main Carousel -->
<div class="home-layout">
    <!-- 主内容区：左栏 + 中栏 -->
    <div class="home-layout__main">
        <!-- 左栏：轮播图 + 标签页新闻 -->
        <div class="home-layout__col">
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
        <div class="home-layout__col">
            <?php $banner_img = get_banner_image(); ?>
            <?php if ($banner_img): ?>
            <div class="banner-section">
                <img src="<?php echo $banner_img; ?>" alt="宣传横幅" class="banner-section__img">
            </div>
            <?php endif; ?>

            <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
            <?php $cat_articles = db_fetch_all("SELECT * FROM articles WHERE category_id = ? AND status = 'publish' ORDER BY publish_time DESC LIMIT 5", [$cat['id']]); ?>
            <?php if (!empty($cat_articles)): ?>
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
                        <?php foreach ($cat_articles as $article): ?>
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

<!-- Section 2: Home Features -->
<section class="home-features-section">
    <div class="container">
        <div class="section-heading">
            <h2 class="section-heading__title">工作重点</h2>
            <p class="section-heading__desc">全面从严治党 永远在路上</p>
        </div>
        <div class="home-features">
            <div class="home-feature-card animate-on-scroll">
                <div class="home-feature-card__icon"><i class="fas fa-gavel"></i></div>
                <h3>纪律审查</h3>
                <p>坚持无禁区、全覆盖、零容忍</p>
            </div>
            <div class="home-feature-card animate-on-scroll">
                <div class="home-feature-card__icon"><i class="fas fa-shield-alt"></i></div>
                <h3>监督执纪</h3>
                <p>强化监督执纪问责</p>
            </div>
            <div class="home-feature-card animate-on-scroll">
                <div class="home-feature-card__icon"><i class="fas fa-search"></i></div>
                <h3>巡视巡察</h3>
                <p>发现问题、形成震慑</p>
            </div>
            <div class="home-feature-card animate-on-scroll">
                <div class="home-feature-card__icon"><i class="fas fa-bullhorn"></i></div>
                <h3>警示曝光</h3>
                <p>以案为鉴、以案促改</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 3: Statistics -->
<section class="home-stats-section">
    <div class="container">
        <div class="home-stats">
            <div class="home-stat-item animate-on-scroll">
                <span class="home-stat-item__number counter" data-count="<?php echo db_count('articles', 'status = ?', ['publish']); ?>">0</span>
                <span class="home-stat-item__label">文章总数</span>
            </div>
            <div class="home-stat-item animate-on-scroll">
                <span class="home-stat-item__number counter" data-count="<?php echo db_count('categories'); ?>">0</span>
                <span class="home-stat-item__label">分类栏目</span>
            </div>
            <div class="home-stat-item animate-on-scroll">
                <span class="home-stat-item__number counter" data-count="<?php echo db_count('users'); ?>">0</span>
                <span class="home-stat-item__label">注册用户</span>
            </div>
            <div class="home-stat-item animate-on-scroll">
                <span class="home-stat-item__number counter" data-count="<?php echo db_count('videos'); ?>">0</span>
                <span class="home-stat-item__label">视频资源</span>
            </div>
        </div>
    </div>
</section>

<!-- Section 4: Split Hero -->
<section class="home-hero-split-section">
    <div class="container">
        <div class="home-hero-split animate-on-scroll">
            <div class="home-hero-split__image">
                <img src="<?php echo get_banner_image(); ?>" alt="中央纪委国家监委" onerror="this.style.display='none'">
            </div>
            <div class="home-hero-split__content">
                <h2>忠诚 干净 担当</h2>
                <p>中央纪委国家监委是党中央领导下管党治党的重要力量，忠实履行党章和宪法赋予的职责，紧紧围绕党和国家工作大局，持之以恒正风肃纪，坚定不移惩治腐败，推动全面从严治党向纵深发展。</p>
                <a href="<?php echo site_url('category.php?slug=yaowen'); ?>" class="btn btn-primary">了解更多</a>
            </div>
        </div>
    </div>
</section>

<!-- Section 5: Latest News Cards -->
<section class="home-news-section">
    <div class="container">
        <div class="section-heading">
            <h2 class="section-heading__title">最新资讯</h2>
            <a href="<?php echo site_url('category.php?slug=yaowen'); ?>" class="section-heading__more">查看更多 <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="home-card-grid">
            <?php 
            $latest = db_fetch_all("SELECT * FROM articles WHERE status = 'publish' ORDER BY publish_time DESC LIMIT 6");
            foreach ($latest as $a): ?>
            <div class="home-card animate-on-scroll">
                <?php if ($a['cover_image']): ?>
                <div class="home-card__image">
                    <img src="<?php echo site_url('uploads/' . $a['cover_image']); ?>" alt="<?php echo htmlspecialchars($a['title']); ?>">
                </div>
                <?php endif; ?>
                <div class="home-card__body">
                    <span class="home-card__date"><?php echo format_time($a['publish_time'], 'Y-m-d'); ?></span>
                    <h3 class="home-card__title">
                        <a href="<?php echo site_url('article.php?id=' . $a['id']); ?>"><?php echo htmlspecialchars($a['title']); ?></a>
                    </h3>
                    <p class="home-card__summary"><?php echo htmlspecialchars(str_cut($a['summary'] ?: strip_tags($a['content']), 80)); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 6: Staff Showcase -->
<?php if (!empty($staff_members)): ?>
<section class="home-staff-section">
    <div class="container">
        <div class="section-heading">
            <h2 class="section-heading__title">工作人员</h2>
            <a href="<?php echo site_url('staff.php'); ?>" class="section-heading__more">查看全部 <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="staff-grid">
            <?php foreach ($staff_members as $s): ?>
            <div class="staff-card animate-on-scroll">
                <?php if ($s['avatar']): ?>
                <img src="<?php echo site_url('uploads/' . $s['avatar']); ?>" alt="<?php echo htmlspecialchars($s['name']); ?>" class="staff-card__avatar">
                <?php else: ?>
                <div class="staff-card__avatar staff-card__avatar--placeholder">
                    <span><?php echo mb_substr($s['name'], 0, 1); ?></span>
                </div>
                <?php endif; ?>
                <h4 class="staff-card__name"><?php echo htmlspecialchars($s['name']); ?></h4>
                <p class="staff-card__title"><?php echo htmlspecialchars($s['title']); ?></p>
                <?php if ($s['department']): ?>
                <p class="staff-card__dept"><?php echo htmlspecialchars($s['department']); ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Section 7: CTA -->
<section class="home-cta-section">
    <div class="container">
        <div class="home-cta animate-on-scroll">
            <h2>监督举报</h2>
            <p>欢迎广大人民群众对党员干部违纪违法行为进行监督举报</p>
            <a href="<?php echo site_url('report.php'); ?>" class="btn btn-report">在线举报</a>
        </div>
    </div>
</section>

<?php include TEMPLATES_PATH . 'footer.php'; ?>