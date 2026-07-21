<?php
/**
 * 文章详情页 v6.0.0
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$id = get('id', 0);
$article = get_article($id);

if (!$article) {
    header('HTTP/1.0 404 Not Found');
    $page_title = '文章未找到';
    include TEMPLATES_PATH . 'header.php';
    echo '<div class="empty-state" style="padding:80px 0;"><div class="empty-state__icon"><i class="fas fa-file-alt"></i></div><h3 class="empty-state__title">文章未找到</h3><p class="empty-state__desc">文章不存在或已被删除</p><a href="' . site_url() . '" class="btn btn-primary">返回首页</a></div>';
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

<div class="article-detail">
    <!-- 主内容区 -->
    <div class="article-detail__main">
        <div class="article-detail__breadcrumb">
            <a href="<?php echo site_url(); ?>">首页</a>
            <span class="article-detail__breadcrumb-sep">/</span>
            <?php if ($category): ?>
            <a href="<?php echo site_url('category.php?slug=' . $category['slug']); ?>"><?php echo htmlspecialchars($category['name']); ?></a>
            <span class="article-detail__breadcrumb-sep">/</span>
            <?php endif; ?>
            <span>正文</span>
        </div>

        <div class="article-detail__header">
            <h1 class="article-detail__title"><?php echo htmlspecialchars($article['title']); ?></h1>
            <div class="article-detail__meta">
                <span><i class="far fa-clock"></i> 发布时间：<?php echo format_time($article['publish_time']); ?></span>
                <?php if ($article['source']): ?>
                <span><i class="fas fa-link"></i> 来源：<?php echo htmlspecialchars($article['source']); ?></span>
                <?php endif; ?>
                <?php if ($article['author']): ?>
                <span><i class="far fa-user"></i> 作者：<?php echo htmlspecialchars($article['author']); ?></span>
                <?php endif; ?>
                <span><i class="far fa-eye"></i> <?php echo (int)$article['view_count']; ?> 次阅读</span>
            </div>
        </div>

        <?php if ($article['cover_image']): ?>
        <img src="<?php echo site_url('uploads/' . $article['cover_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="article-detail__cover">
        <?php endif; ?>

        <?php if (!empty($article['video'])): ?>
        <div class="article-detail__video">
            <video controls style="width:100%;max-width:720px;border-radius:8px;display:block;margin:0 auto 20px;">
                <source src="<?php echo site_url('uploads/' . $article['video']); ?>" type="video/mp4">
                您的浏览器不支持视频播放
            </video>
        </div>
        <?php endif; ?>

        <div class="article-detail__content">
            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
        </div>

        <?php if ($article['keywords']): ?>
        <div class="article-detail__tags">
            <span class="article-detail__tags-label">标签：</span>
            <?php foreach (explode(',', $article['keywords']) as $tag): ?>
            <a href="<?php echo site_url('search.php?q=' . urlencode(trim($tag))); ?>" class="article-detail__tag"><?php echo htmlspecialchars(trim($tag)); ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($related)): ?>
        <div class="article-detail__related">
            <h3 class="article-detail__related-title">相关文章</h3>
            <div class="news-list">
                <?php foreach ($related as $r): ?>
                <div class="news-item">
                    <span class="news-item__dot"></span>
                    <a href="<?php echo site_url('article.php?id=' . $r['id']); ?>" class="news-item__title"><?php echo htmlspecialchars($r['title']); ?></a>
                    <span class="news-item__time"><?php echo format_time($r['publish_time'], 'm-d'); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- 侧边栏 -->
    <div class="article-detail__sidebar">
        <div class="sidebar-widget">
            <div class="sidebar-widget__header">
                <span class="sidebar-widget__header-icon"><i class="fas fa-clock"></i></span>
                最新文章
            </div>
            <div class="sidebar-widget__body">
                <div class="sidebar-article-list">
                    <?php $latest = db_fetch_all("SELECT id, title, cover_image, publish_time FROM articles WHERE status = 'publish' ORDER BY publish_time DESC LIMIT 8"); ?>
                    <?php foreach ($latest as $item): ?>
                    <div class="sidebar-article-item sidebar-article-item--text-only">
                        <div class="sidebar-article-item__body">
                            <a href="<?php echo site_url('article.php?id=' . $item['id']); ?>" class="sidebar-article-item__title"><?php echo htmlspecialchars($item['title']); ?></a>
                            <span class="sidebar-article-item__time"><?php echo format_time($item['publish_time'], 'm-d'); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include TEMPLATES_PATH . 'footer.php'; ?>