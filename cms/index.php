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
$videoCat = getCategoryBySlug('video');
$wenhuaCat = getCategoryBySlug('wenhua');

$featured = $yaowenCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY is_top DESC, publish_time DESC LIMIT 1", [$yaowenCat['id']]) : [];
$featured = $featured ? $featured[0] : null;

$yaowenList = $yaowenCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY is_top DESC, publish_time DESC LIMIT 8", [$yaowenCat['id']]) : [];
$shenchaList = $shenchaCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 8", [$shenchaCat['id']]) : [];
$xunshiList = $xunshiCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 6", [$xunshiCat['id']]) : [];
$faguiList = $faguiCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 6", [$faguiCat['id']]) : [];
$videoList = $videoCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 3", [$videoCat['id']]) : [];
$wenhuaList = $wenhuaCat ? DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 4", [$wenhuaCat['id']]) : [];

$hotArticles = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC, publish_time DESC LIMIT 10");

// 统计数据
$statArticles = DB::fetchOne("SELECT COUNT(*) AS c FROM articles WHERE status=1");
$statUsers    = DB::fetchOne("SELECT COUNT(*) AS c FROM users");
$statCats     = DB::fetchOne("SELECT COUNT(*) AS c FROM categories");
$statViews    = DB::fetchOne("SELECT COALESCE(SUM(views), 0) AS c FROM articles WHERE status=1");
$totalArticles = $statArticles ? (int)$statArticles['c'] : 0;
$totalUsers    = $statUsers    ? (int)$statUsers['c']    : 0;
$totalCats     = $statCats     ? (int)$statCats['c']     : 0;
$totalViews    = $statViews    ? (int)$statViews['c']    : 0;

// 新闻滚动条数据（最新要闻）
$tickerList = $yaowenList;

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

    <!-- 新闻滚动条 -->
    <div class="news-ticker scroll-reveal">
        <div class="container">
            <div class="ticker-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                最新要闻
            </div>
            <div class="ticker-content">
                <div class="ticker-track">
                    <?php if ($tickerList): ?>
                        <?php foreach ($tickerList as $article): ?>
                        <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>"><?php echo e($article['title']); ?></a>
                        <?php endforeach; ?>
                        <?php foreach ($tickerList as $article): ?>
                        <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>"><?php echo e($article['title']); ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="#">暂无最新要闻</a><a href="#">暂无最新要闻</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 统计数据栏 -->
    <section class="stats-bar scroll-reveal">
        <div class="container">
            <div class="stats-grid">
                <div class="stats-item animate-fade-up delay-1">
                    <span class="stats-num" data-count="<?php echo $totalArticles; ?>">0</span>
                    <div class="stats-label">文章总数</div>
                </div>
                <div class="stats-item animate-fade-up delay-2">
                    <span class="stats-num" data-count="<?php echo $totalUsers; ?>">0</span>
                    <div class="stats-label">注册用户</div>
                </div>
                <div class="stats-item animate-fade-up delay-3">
                    <span class="stats-num" data-count="<?php echo $totalCats; ?>">0</span>
                    <div class="stats-label">栏目数量</div>
                </div>
                <div class="stats-item animate-fade-up delay-3">
                    <span class="stats-num" data-count="<?php echo $totalViews; ?>">0</span>
                    <div class="stats-label">总阅读量</div>
                </div>
            </div>
        </div>
    </section>

    <div class="main-content">
        <div class="container">
            <div class="content-wrap">
                <div class="main-col">
                    <?php if ($yaowenCat): ?>
                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>要闻动态</h3>
                            <a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($yaowenCat['slug']); ?>" class="more">更多 &raquo;</a>
                        </div>
                        <div class="section-body">
                            <?php if ($featured): ?>
                            <div class="feature-article animate-fade-up delay-1">
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

                    <!-- 专题推荐 -->
                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>专题推荐</h3>
                            <a href="<?php echo BASE_URL; ?>category.php?slug=yaowen" class="more">更多 &raquo;</a>
                        </div>
                        <div class="topic-grid">
                            <a href="<?php echo BASE_URL; ?>category.php?slug=shencha" class="topic-card animate-fade-up delay-1">
                                <div class="topic-bg topic-bg-1">
                                    <svg class="topic-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                                </div>
                                <div class="topic-info">
                                    <h4>反腐重拳</h4>
                                    <p>严惩腐败 · 正风肃纪</p>
                                </div>
                            </a>
                            <a href="<?php echo BASE_URL; ?>category.php?slug=fagui" class="topic-card animate-fade-up delay-2">
                                <div class="topic-bg topic-bg-2">
                                    <svg class="topic-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                </div>
                                <div class="topic-info">
                                    <h4>党纪教育</h4>
                                    <p>学纪知纪 · 守纪用纪</p>
                                </div>
                            </a>
                            <a href="<?php echo BASE_URL; ?>category.php?slug=xunshi" class="topic-card animate-fade-up delay-3">
                                <div class="topic-bg topic-bg-3">
                                    <svg class="topic-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                </div>
                                <div class="topic-info">
                                    <h4>警钟长鸣</h4>
                                    <p>以案为鉴 · 以案促改</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="section scroll-reveal">
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

                    <!-- 视频中心 -->
                    <?php if ($videoCat): ?>
                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>视频中心</h3>
                            <a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($videoCat['slug']); ?>" class="more">更多 &raquo;</a>
                        </div>
                        <div class="video-grid">
                            <?php if ($videoList): ?>
                                <?php foreach ($videoList as $index => $article): ?>
                                <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="video-card animate-fade-up delay-<?php echo $index + 1; ?>">
                                    <div class="video-thumb">
                                        <?php if ($article['cover_image']): ?>
                                            <img src="<?php echo BASE_URL . UPLOAD_URL . e($article['cover_image']); ?>" alt="<?php echo e($article['title']); ?>" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;">
                                        <?php endif; ?>
                                        <div class="play-btn"></div>
                                        <span class="duration"><?php echo sprintf('%02d:%02d', 3 + $index * 2, 15 + $index * 10); ?></span>
                                    </div>
                                    <div class="video-info">
                                        <h4><?php echo e(truncateStr($article['title'], 30)); ?></h4>
                                        <div class="meta">
                                            <span><?php echo formatDate($article['publish_time']); ?></span>
                                            <span style="margin-left:10px;">播放：<?php echo $article['views']; ?></span>
                                        </div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php for ($i = 0; $i < 3; $i++): ?>
                                <div class="video-card animate-fade-up delay-<?php echo $i + 1; ?>">
                                    <div class="video-thumb">
                                        <div class="play-btn"></div>
                                        <span class="duration">00:00</span>
                                    </div>
                                    <div class="video-info">
                                        <h4>暂无视频内容</h4>
                                        <div class="meta"><span>敬请期待</span></div>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="section scroll-reveal">
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

                    <!-- 文化之约 -->
                    <?php if ($wenhuaCat): ?>
                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>文化之约</h3>
                            <a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($wenhuaCat['slug']); ?>" class="more">更多 &raquo;</a>
                        </div>
                        <div class="section-body">
                            <div class="article-grid">
                                <?php foreach ($wenhuaList as $index => $article): ?>
                                <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="article-card animate-fade-up delay-<?php echo min($index + 1, 3); ?>">
                                    <div class="thumb">
                                        <?php if ($article['cover_image']): ?>
                                            <img src="<?php echo BASE_URL . UPLOAD_URL . e($article['cover_image']); ?>" alt="<?php echo e($article['title']); ?>">
                                        <?php else: ?>
                                            <div style="width:100%;height:100%;background:linear-gradient(135deg,#b80000,#6a0000);display:flex;align-items:center;justify-content:center;color:#fff;font-size:28px;">廉</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="info">
                                        <h4><?php echo e(truncateStr($article['title'], 40)); ?></h4>
                                        <p><?php echo e($article['summary'] ? truncateStr($article['summary'], 40) : '点击查看详细内容'); ?></p>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="side-col">
                    <div class="side-block scroll-reveal">
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

                    <!-- 3D插画区域 -->
                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">清廉之印</div>
                        <div class="side-block-body">
                            <div class="illustration-3d">
                                <div class="illust-card">
                                    <span class="illust-text">廉</span>
                                </div>
                            </div>
                            <p style="text-align:center;margin-top:10px;font-size:13px;color:var(--gray-700);">清正廉洁 · 执政为民</p>
                        </div>
                    </div>

                    <div class="side-block scroll-reveal">
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

                    <div class="side-block scroll-reveal">
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
