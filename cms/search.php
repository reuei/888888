<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$q = trim($_GET['q'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$articles = [];
$total = 0;

if ($q) {
    $like = '%' . $q . '%';
    $total = DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE status=1 AND (title LIKE ? OR summary LIKE ? OR content LIKE ?)", [$like, $like, $like])['cnt'];
    $articles = DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE ? OR summary LIKE ? OR content LIKE ?) ORDER BY publish_time DESC LIMIT $offset, $perPage", [$like, $like, $like]);
}

$pageTitle = '搜索: ' . $q;
include __DIR__ . '/includes/header.php';
?>

    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>搜索结果</span>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="section">
                <div class="section-header">
                    <h3>搜索结果</h3>
                    <span style="font-size:13px; color:#999;">共找到 <?php echo $total; ?> 条关于 "<?php echo e($q); ?>" 的结果</span>
                </div>
                <div class="section-body">
                    <?php if ($articles): ?>
                    <ul class="news-list">
                        <?php foreach ($articles as $article): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="title">
                                <?php echo e($article['title']); ?>
                            </a>
                            <span class="date"><?php echo formatDate($article['publish_time']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php echo paginate($total, $page, $perPage, BASE_URL . 'search.php?q=' . urlencode($q)); ?>
                    <?php else: ?>
                    <p style="text-align:center; color:#999; padding:40px 0;">
                        <?php if ($q): ?>未找到相关内容，请尝试其他关键词<?php else: ?>请输入关键词搜索<?php endif; ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
