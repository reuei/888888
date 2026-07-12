<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? 'bainian';
$topicMap = [
    'bainian' => ['name' => '百年纪事', 'desc' => '回顾党的纪律建设百年光辉历程', 'icon' => '📋'],
    'fubai' => ['name' => '正风反腐在身边', 'desc' => '聚焦群众身边的不正之风和腐败问题', 'icon' => '⚖'],
    'zhuitao' => ['name' => '国际追逃追赃', 'desc' => '天网恢恢，疏而不漏，海外追逃追踪报道', 'icon' => '🌐'],
    'jiaoyu' => ['name' => '警示教育', 'desc' => '以案为鉴、以案促改，深入开展警示教育', 'icon' => '📚'],
    'zhengzhi' => ['name' => '政治巡视', 'desc' => '深化政治巡视，发挥利剑作用', 'icon' => '🔍'],
];

$topicInfo = $topicMap[$slug] ?? ['name' => '专题报道', 'desc' => '专题新闻报道集粹', 'icon' => '📋'];

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

$total = DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE status=1 AND (title LIKE ? OR summary LIKE ?)", ['%' . $topicInfo['name'] . '%', '%' . $topicInfo['name'] . '%'])['cnt'];
if ($total == 0) {
    $total = DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE status=1")['cnt'];
    $articles = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY is_top DESC, publish_time DESC LIMIT $offset, $perPage");
} else {
    $articles = DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE ? OR summary LIKE ?) ORDER BY is_top DESC, publish_time DESC LIMIT $offset, $perPage", ['%' . $topicInfo['name'] . '%', '%' . $topicInfo['name'] . '%']);
}

$hotArticles = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC LIMIT 10");

$pageTitle = $topicInfo['name'];
include __DIR__ . '/includes/header.php';
?>

    <section class="banner" style="padding:40px 0;">
        <div class="container">
            <h2 style="font-size:30px; display:flex; align-items:center; gap:12px;">
                <span style="font-size:36px;"><?php echo $topicInfo['icon']; ?></span>
                <?php echo e($topicInfo['name']); ?>
            </h2>
            <p><?php echo e($topicInfo['desc']); ?></p>
        </div>
    </section>

    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>专题集粹</span>
            <span class="sep">/</span>
            <span><?php echo e($topicInfo['name']); ?></span>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="content-wrap">
                <div class="main-col">
                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3><?php echo e($topicInfo['name']); ?> - 文章列表</h3>
                            <span style="font-size:13px; color:#999;">共 <?php echo $total; ?> 篇</span>
                        </div>
                        <div class="section-body">
                            <?php if ($articles): ?>
                            <div class="article-grid">
                                <?php foreach ($articles as $idx => $art): ?>
                                <div class="article-card animate-fade-up delay-<?php echo ($idx % 3) + 1; ?>">
                                    <div class="thumb">
                                        <?php if ($art['cover_image']): ?>
                                            <img src="<?php echo BASE_URL . UPLOAD_URL . e($art['cover_image']); ?>" alt="">
                                        <?php else: ?>
                                            <div style="width:100%;height:100%;background:linear-gradient(135deg,#b80000,#6a0000);display:flex;align-items:center;justify-content:center;color:#fff;font-size:24px;">廉</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="info">
                                        <h4><a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $art['id']; ?>"><?php echo e(truncateStr($art['title'], 30)); ?></a></h4>
                                        <p><?php echo e(truncateStr(strip_tags($art['summary'] ?: $art['content']), 40)); ?></p>
                                        <p style="font-size:11px; color:#bbb; margin-top:5px;"><?php echo formatDate($art['publish_time']); ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php echo paginate($total, $page, $perPage, BASE_URL . 'topic.php?slug=' . $slug); ?>
                            <?php else: ?>
                            <p style="text-align:center; color:#999; padding:40px 0;">暂无内容</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>更多专题</h3>
                        </div>
                        <div class="topic-grid">
                            <?php foreach ($topicMap as $key => $t): ?>
                                <?php if ($key != $slug): ?>
                                <a href="<?php echo BASE_URL; ?>topic.php?slug=<?php echo $key; ?>" class="topic-card" style="text-decoration:none;">
                                    <div class="topic-bg <?php echo 'topic-bg-' . (array_search($key, array_keys($topicMap)) % 3 + 1); ?>"></div>
                                    <div class="topic-icon"><?php echo $t['icon']; ?></div>
                                    <div class="topic-info">
                                        <h4><?php echo e($t['name']); ?></h4>
                                        <p><?php echo e($t['desc']); ?></p>
                                    </div>
                                </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="side-col">
                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">热门排行</div>
                        <div class="side-block-body">
                            <ul class="hot-list">
                                <?php foreach ($hotArticles as $index => $article): ?>
                                <li>
                                    <span class="rank"><?php echo $index + 1; ?></span>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="item-title">
                                        <?php echo e(truncateStr($article['title'], 30)); ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">3D廉洁宣言</div>
                        <div class="side-block-body">
                            <div class="illustration-3d">
                                <div class="illust-card">
                                    <span class="illust-text">廉</span>
                                </div>
                            </div>
                            <p style="text-align:center; font-size:13px; color:#666; margin-top:10px;">清正廉洁，公正用权</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
