<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = intval($_GET['id'] ?? 0);
$article = DB::fetchOne("SELECT * FROM articles WHERE id=? AND status=1", [$id]);

if (!$article) {
    header('HTTP/1.1 404 Not Found');
    $pageTitle = '页面不存在';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container" style="padding:50px 0; text-align:center;"><h2>文章不存在</h2><p style="color:#999; margin-top:10px;">您访问的文章不存在或已删除</p></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

DB::update('articles', ['views' => $article['views'] + 1], 'id=?', [$id]);

$category = getCategory($article['category_id']);
$currentCatId = $article['category_id'];
$pageTitle = $article['title'];
$crums = $category ? getBreadcrumb($category['id']) : [];

$relatedArticles = $category ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND id!=? AND status=1 ORDER BY publish_time DESC LIMIT 8", [$category['id'], $id]) : [];

$prevArticle = DB::fetchOne("SELECT * FROM articles WHERE id < ? AND status=1 ORDER BY id DESC LIMIT 1", [$id]);
$nextArticle = DB::fetchOne("SELECT * FROM articles WHERE id > ? AND status=1 ORDER BY id ASC LIMIT 1", [$id]);

include __DIR__ . '/includes/header.php';
?>

    <div class="container">
        <div class="crums">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <?php foreach ($crums as $bc): ?>
                <span class="sep">/</span>
                <a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($bc['slug']); ?>"><?php echo e($bc['name']); ?></a>
            <?php endforeach; ?>
            <span class="sep">/</span>
            <span>正文</span>
        </div>
    </div>

    <div class="">
        <div class="container">
            <div class="two-col">
                <div class="">
                    <div class="article-detail">
                        <h1><?php echo e($article['title']); ?></h1>
                        <div class="article-meta">
                            <span>来源：<?php echo e($article['source'] ?: '本站'); ?></span>
                            <span>作者：<?php echo e($article['author'] ?: '佚名'); ?></span>
                            <span>发布时间：<?php echo formatDate($article['publish_time'], 'Y-m-d H:i'); ?></span>
                            <span>阅读：<?php echo $article['views'] + 1; ?></span>
                        </div>
                        <div class="article-content">
                            <?php echo $article['content']; ?>
                        </div>

                        <div style="margin-top:40px; padding-top:20px; border-top:1px solid #f0f0f0; font-size:13px; color:#999;">
                            <?php if ($prevArticle): ?>
                            <p>上一篇：<a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $prevArticle['id']; ?>"><?php echo e($prevArticle['title']); ?></a></p>
                            <?php endif; ?>
                            <?php if ($nextArticle): ?>
                            <p>下一篇：<a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $nextArticle['id']; ?>"><?php echo e($nextArticle['title']); ?></a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="">
                    <div class="block">
                        <div class="block-title">相关文章</div>
                        <div class="block-body">
                            <ul class="news-list">
                                <?php foreach ($relatedArticles as $rel): ?>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $rel['id']; ?>" class="title">
                                        <?php echo e(truncateStr($rel['title'], 28)); ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="block">
                        <div class="block-title">热点推荐</div>
                        <div class="block-body">
                            <ul class="hot-list">
                                <?php
                                $hotList = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC LIMIT 8");
                                foreach ($hotList as $index => $art):
                                ?>
                                <li>
                                    <span class="rank"><?php echo $index + 1; ?></span>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $art['id']; ?>" class="item-title">
                                        <?php echo e($art['title']); ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
