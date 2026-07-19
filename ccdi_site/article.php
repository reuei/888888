<?php
/**
 * 文章详情页
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$id = get('id', 0);
$article = get_article($id);

if (!$article) {
    header('HTTP/1.0 404 Not Found');
    $page_title = '文章未找到';
    include TEMPLATES_PATH . 'header.php';
    echo '<div class="page-content"><div class="container"><div class="empty-state"><i class="fas fa-file-alt"></i><p>文章未找到或已被删除</p><a href="' . site_url() . '">返回首页</a></div></div></div>';
    include TEMPLATES_PATH . 'footer.php';
    exit;
}

// 更新浏览量
db_update('articles', ['view_count' => $article['view_count'] + 1], 'id = ?', [$article['id']]);
$article['view_count']++;

$page_title = $article['title'];
$category = db_fetch("SELECT * FROM categories WHERE id = ?", [$article['category_id']]);

// 相关文章
$related = db_fetch_all(
    "SELECT * FROM articles WHERE category_id = ? AND id != ? AND status = 'publish' ORDER BY publish_time DESC LIMIT 5",
    [$article['category_id'], $article['id']]
);

include TEMPLATES_PATH . 'header.php';
?>

<div class="page-content">
    <div class="content-layout">
        <div class="content-main">
            <div class="breadcrumb">
                <a href="<?php echo site_url(); ?>">首页</a>
                <?php if ($category): ?>
                <span class="separator">&raquo;</span>
                <a href="<?php echo site_url('category.php?slug=' . $category['slug']); ?>"><?php echo htmlspecialchars($category['name']); ?></a>
                <?php endif; ?>
                <span class="separator">&raquo;</span>
                <span class="current">正文</span>
            </div>
            
            <article class="article-detail">
                <header class="article-header">
                    <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <div class="article-meta">
                        <span><i class="far fa-clock"></i> 发布时间：<?php echo format_time($article['publish_time']); ?></span>
                        <?php if ($article['source']): ?>
                        <span><i class="fas fa-link"></i> 来源：<?php echo htmlspecialchars($article['source']); ?></span>
                        <?php endif; ?>
                        <?php if ($article['author']): ?>
                        <span><i class="far fa-user"></i> 作者：<?php echo htmlspecialchars($article['author']); ?></span>
                        <?php endif; ?>
                        <span><i class="far fa-eye"></i> <?php echo (int)$article['view_count']; ?> 次阅读</span>
                    </div>
                </header>
                
                <?php if ($article['cover_image']): ?>
                <div class="article-cover">
                    <img src="<?php echo site_url('uploads/' . $article['cover_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                </div>
                <?php endif; ?>
                
                <div class="article-content">
                    <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </div>
                
                <?php if ($article['keywords']): ?>
                <div class="article-tags">
                    <span>标签：</span>
                    <?php foreach (explode(',', $article['keywords']) as $tag): ?>
                    <a href="<?php echo site_url('search.php?q=' . urlencode(trim($tag))); ?>" class="tag"><?php echo htmlspecialchars(trim($tag)); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </article>
            
            <?php if (!empty($related)): ?>
            <div class="related-articles">
                <h3>相关文章</h3>
                <ul>
                    <?php foreach ($related as $r): ?>
                    <li><a href="<?php echo site_url('article.php?id=' . $r['id']); ?>"><?php echo htmlspecialchars($r['title']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="content-sidebar">
            <div class="sidebar-widget">
                <h3 class="widget-title">最新文章</h3>
                <ul class="sidebar-article-list">
                    <?php $latest = db_fetch_all("SELECT id, title, publish_time FROM articles WHERE status = 'publish' ORDER BY publish_time DESC LIMIT 8"); ?>
                    <?php foreach ($latest as $item): ?>
                    <li>
                        <a href="<?php echo site_url('article.php?id=' . $item['id']); ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                        <span class="time"><?php echo format_time($item['publish_time'], 'm-d'); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include TEMPLATES_PATH . 'footer.php'; ?>