<?php
/**
 * 分类列表页
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

<div class="page-content">
    <div class="content-layout">
        <div class="content-main">
            <div class="page-header">
                <h1 class="page-title"><?php echo htmlspecialchars($category['name']); ?></h1>
                <?php if ($category['description']): ?>
                <p class="page-desc"><?php echo htmlspecialchars($category['description']); ?></p>
                <?php endif; ?>
            </div>
            
            <?php if (empty($articles)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>暂无内容</p>
            </div>
            <?php else: ?>
            <div class="article-list">
                <?php foreach ($articles as $article): ?>
                <div class="article-card">
                    <?php if ($article['cover_image']): ?>
                    <div class="article-card-image">
                        <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>">
                            <img src="<?php echo site_url('uploads/' . $article['cover_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="article-card-body">
                        <h3 class="article-card-title">
                            <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>"><?php echo htmlspecialchars($article['title']); ?></a>
                            <?php if ($article['is_top']): ?><span class="badge-top">置顶</span><?php endif; ?>
                        </h3>
                        <p class="article-card-summary"><?php echo str_cut($article['summary'] ?: strip_tags($article['content']), 200); ?></p>
                        <div class="article-card-meta">
                            <span><i class="far fa-clock"></i> <?php echo format_time($article['publish_time']); ?></span>
                            <?php if ($article['source']): ?>
                            <span><i class="fas fa-link"></i> 来源：<?php echo htmlspecialchars($article['source']); ?></span>
                            <?php endif; ?>
                            <span><i class="far fa-eye"></i> <?php echo (int)$article['view_count']; ?> 次阅读</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php echo pagination($total, $page, site_url('category.php?slug=' . $slug . '&'), ITEMS_PER_PAGE); ?>
            <?php endif; ?>
        </div>
        
        <div class="content-sidebar">
            <div class="sidebar-widget">
                <h3 class="widget-title">分类导航</h3>
                <ul class="category-nav">
                    <?php $cats = get_categories(); ?>
                    <?php foreach ($cats as $cat): ?>
                    <li class="<?php echo ($cat['id'] == $category['id']) ? 'active' : ''; ?>">
                        <a href="<?php echo site_url('category.php?slug=' . $cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include TEMPLATES_PATH . 'footer.php'; ?>