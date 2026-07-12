<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (!file_exists(DB_PATH)) {
    header('Location: install.php');
    exit;
}

$yaowenCat = getCategoryBySlug('yaowen');
$shenchaCat = getCategoryBySlug('shencha');
$xunshiCat = getCategoryBySlug('xunshi');
$faguiCat = getCategoryBySlug('fagui');

$featured = $yaowenCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY is_top DESC, publish_time DESC LIMIT 1", [$yaowenCat['id']]) : [];
$featured = $featured ? $featured[0] : null;

$yaowenList = $yaowenCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY is_top DESC, publish_time DESC LIMIT 8", [$yaowenCat['id']]) : [];
$shenchaList = $shenchaCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 8", [$shenchaCat['id']]) : [];
$xunshiList = $xunshiCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 6", [$xunshiCat['id']]) : [];
$faguiList = $faguiCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 6", [$faguiCat['id']]) : [];

$hotArticles = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC, publish_time DESC LIMIT 10");

$obFile = __DIR__ . '/data/install.lock';
if (file_exists($obFile)) {
    $canInstall = false;
} else {
    $canInstall = true;
}

$pageTitle = '';
include __DIR__ . '/includes/header.php';
?>

    <section class="banner">
        <div class="container">
            <h2>坚定不移推进党风廉政建设和反腐败斗争</h2>
            <p>以永远在路上的坚韧和执着，坚决打赢反腐败这场攻坚战、持久战</p>
        </div>
    </section>

    <div class="main-content">
        <div class="container">
            <div class="content-wrap">
                <div class="main-col">
                    <?php if ($yaowenCat): ?>
                    <div class="section">
                        <div class="section-header">
                            <h3>要闻动态</h3>
                            <a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($yaowenCat['slug']); ?>" class="more">更多 &raquo;</a>
                        </div>
                        <div class="section-body">
                            <?php if ($featured): ?>
                            <div class="feature-article">
                                <div class="feature-img">
                                    <?php if ($featured['cover_image']): ?>
                                        <img src="<?php echo BASE_URL . UPLOAD_URL . e($featured['cover_image']); ?>" alt="<?php echo e($featured['title']); ?>">
                                    <?php else: ?>
                                        <div style="width:100%;height:100%;background:linear-gradient(135deg,#c20000,#8b0000);display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;">要闻</div>
                                    <?php endif; ?>
                                </div>
                                <div class="feature-info">
                                    <h2><a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $featured['id']; ?>"><?php echo e($featured['title']); ?></a></h2>
                                    <p><?php echo e($featured['summary']); ?></p>
                                    <div class="feature-meta">
                                        <span>发布时间：<?php echo formatDate($featured['publish_time']); ?></span>
                                        <span style="margin-left:15px;">阅读：<?php echo $featured['views']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <ul class="news-list">
                                <?php foreach (array_slice($yaowenList, 1) as $article): ?>
                                <li class="<?php echo $article['is_top'] ? 'top' : ''; ?>">
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="title">
                                        <?php echo e($article['title']); ?>
                                    </a>
                                    <span class="date"><?php echo formatDate($article['publish_time']); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="section">
                        <div class="section-header">
                            <h3>审查调查</h3>
                            <a href="<?php echo BASE_URL; ?>category.php?slug=shencha" class="more">更多 &raquo;</a>
                        </div>
                        <div class="section-body">
                            <ul class="news-list">
                                <?php foreach ($shenchaList as $article): ?>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="title">
                                        <?php echo e($article['title']); ?>
                                    </a>
                                    <span class="date"><?php echo formatDate($article['publish_time']); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="section">
                        <div class="section-header">
                            <h3>巡视巡察</h3>
                            <a href="<?php echo BASE_URL; ?>category.php?slug=xunshi" class="more">更多 &raquo;</a>
                        </div>
                        <div class="section-body">
                            <ul class="news-list">
                                <?php foreach ($xunshiList as $article): ?>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="title">
                                        <?php echo e($article['title']); ?>
                                    </a>
                                    <span class="date"><?php echo formatDate($article['publish_time']); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="side-col">
                    <div class="side-block">
                        <div class="side-block-title">监督举报</div>
                        <div class="side-block-body">
                            <div class="quick-links">
                                <a href="<?php echo BASE_URL; ?>report.php">
                                    <div class="icon">📝</div>
                                    <div>我要举报</div>
                                </a>
                                <a href="<?php echo BASE_URL; ?>message.php">
                                    <div class="icon">💬</div>
                                    <div>留言板</div>
                                </a>
                                <a href="#">
                                    <div class="icon">📧</div>
                                    <div>投稿</div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="side-block">
                        <div class="side-block-title">热点排行</div>
                        <div class="side-block-body">
                            <ul class="hot-list">
                                <?php foreach ($hotArticles as $index => $article): ?>
                                <li>
                                    <span class="rank"><?php echo $index + 1; ?></span>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="item-title">
                                        <?php echo e($article['title']); ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="side-block">
                        <div class="side-block-title">党纪法规</div>
                        <div class="side-block-body">
                            <ul class="news-list">
                                <?php foreach ($faguiList as $article): ?>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="title">
                                        <?php echo e(truncateStr($article['title'], 25)); ?>
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
