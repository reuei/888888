<?php
/**
 * 分类列表页 v9.0.0
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$slug = get('slug', '');
$page = max(1, (int)get('page', 1));

$category = null;
if ($slug) {
    $category = db_fetch("SELECT * FROM categories WHERE slug = ? AND status = 1", [$slug]);
}

if (!$category) {
    $category = db_fetch("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC LIMIT 1");
}

$page_title = $category ? $category['name'] : '分类';
$total = db_count('articles', "category_id = ? AND status = 'publish'", [$category['id']]);
$offset = ($page - 1) * ITEMS_PER_PAGE;
$articles = db_fetch_all(
    "SELECT * FROM articles WHERE category_id = ? AND status = 'publish' ORDER BY is_top DESC, publish_time DESC LIMIT ? OFFSET ?",
    [$category['id'], ITEMS_PER_PAGE, $offset]
);

include TEMPLATES_PATH . 'header.php';
?>

<div class="article-list-page">
    <!-- 主内容区 -->
    <div class="article-list__main">
        <div class="section">
            <div class="section-header">
                <h2 class="section-header__title">
                    <span class="section-header__title-icon"><i class="fas fa-folder"></i></span>
                    <?php echo htmlspecialchars($category['name']); ?>
                </h2>
                <?php if ($category['description']): ?>
                <span class="text-muted" style="font-size:13px;"><?php echo htmlspecialchars($category['description']); ?></span>
                <?php endif; ?>
            </div>
            <div class="section__body">
                <?php if (empty($articles)): ?>
                <div class="empty-state">
                    <div class="empty-state__icon"><i class="fas fa-inbox"></i></div>
                    <h3 class="empty-state__title">暂无内容</h3>
                    <p class="empty-state__desc">该分类下暂无文章</p>
                </div>
                <?php else: ?>
                <div class="article-list__grid">
                    <?php foreach ($articles as $article): ?>
                    <div class="article-card">
                        <?php if ($article['is_top']): ?>
                        <span class="article-card__badge">置顶</span>
                        <?php endif; ?>
                        <?php if ($article['cover_image']): ?>
                        <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>">
                            <img src="<?php echo site_url('uploads/' . $article['cover_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-card__image">
                        </a>
                        <?php endif; ?>
                        <div class="article-card__body">
                            <h3 class="article-card__title">
                                <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>"><?php echo htmlspecialchars($article['title']); ?></a>
                            </h3>
                            <div class="article-card__meta">
                                <span><i class="far fa-clock"></i> <?php echo format_time($article['publish_time']); ?></span>
                                <?php if ($article['source']): ?>
                                <span>来源：<?php echo htmlspecialchars($article['source']); ?></span>
                                <?php endif; ?>
                                <span><i class="far fa-eye"></i> <?php echo (int)$article['view_count']; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php echo pagination($total, $page, site_url('category.php?slug=' . $slug . '&'), ITEMS_PER_PAGE); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 侧边栏 -->
    <div class="home-layout__sidebar">
        <div class="sidebar-widget">
            <div class="sidebar-widget__header">
                <span class="sidebar-widget__header-icon"><i class="fas fa-list"></i></span>
                分类导航
            </div>
            <div class="sidebar-widget__body">
                <div class="category-nav">
                    <ul class="category-nav__list">
                        <?php $cats = get_categories(); ?>
                        <?php foreach ($cats as $cat): ?>
                        <li class="category-nav__item">
                            <a href="<?php echo site_url('category.php?slug=' . $cat['slug']); ?>" class="category-nav__link<?php echo ($cat['id'] == $category['id']) ? ' category-nav__link--active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include TEMPLATES_PATH . 'footer.php'; ?>