<?php
/**
 * 搜索页面 v10.0.0
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$q = trim(get('q', ''));
$page = max(1, (int)get('page', 1));
$page_title = $q ? '搜索：' . $q : '搜索';
$articles = [];
$total = 0;

if ($q) {
    $total = db_count('articles', "status = 'publish' AND (title LIKE ? OR content LIKE ? OR summary LIKE ?)", ['%' . $q . '%', '%' . $q . '%', '%' . $q . '%']);
    $offset = ($page - 1) * ITEMS_PER_PAGE;
    $articles = db_fetch_all(
        "SELECT * FROM articles WHERE status = 'publish' AND (title LIKE ? OR content LIKE ? OR summary LIKE ?) ORDER BY publish_time DESC LIMIT ? OFFSET ?",
        ['%' . $q . '%', '%' . $q . '%', '%' . $q . '%', ITEMS_PER_PAGE, $offset]
    );
}

include TEMPLATES_PATH . 'header.php';
?>

<div class="search-page">
    <div class="search-page__header">
        <h1 class="search-page__title">站内搜索</h1>
        <form action="<?php echo site_url('search.php'); ?>" method="get" class="search-page__form">
            <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="请输入搜索关键词..." class="search-page__input">
            <button type="submit" class="search-page__btn"><i class="fas fa-search"></i> 搜索</button>
        </form>
    </div>

    <?php if ($q): ?>
    <p class="search-page__info">搜索 "<strong><?php echo htmlspecialchars($q); ?></strong>" ，共找到 <strong><?php echo $total; ?></strong> 条结果</p>

    <?php if (empty($articles)): ?>
    <div class="empty-state">
        <div class="empty-state__icon"><i class="fas fa-search"></i></div>
        <h3 class="empty-state__title">未找到相关内容</h3>
        <p class="empty-state__desc">请尝试其他关键词搜索</p>
    </div>
    <?php else: ?>
    <div class="search-page__results">
        <?php foreach ($articles as $article): ?>
        <div class="search-result-item">
            <h3 class="search-result-item__title">
                <a href="<?php echo site_url('article.php?id=' . $article['id']); ?>"><?php echo htmlspecialchars($article['title']); ?></a>
            </h3>
            <p class="search-result-item__text"><?php echo str_cut($article['summary'] ?: strip_tags($article['content']), 200); ?></p>
            <div class="search-result-item__meta">
                <span><i class="far fa-clock"></i> <?php echo format_time($article['publish_time']); ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php echo pagination($total, $page, site_url('search.php?q=' . urlencode($q) . '&'), ITEMS_PER_PAGE); ?>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php include TEMPLATES_PATH . 'footer.php'; ?>