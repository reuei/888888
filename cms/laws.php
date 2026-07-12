<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$faguiCat = getCategoryBySlug('fagui');
$subCats = $faguiCat ? getChildCategories($faguiCat['id']) : [];

$keyword = trim($_GET['q'] ?? '');
$subCatId = intval($_GET['cat'] ?? 0);

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$where = ['status=1'];
$params = [];

if ($faguiCat) {
    $catIds = [$faguiCat['id']];
    foreach ($subCats as $sc) $catIds[] = $sc['id'];
    $placeholders = implode(',', array_fill(0, count($catIds), '?'));
    $where[] = "category_id IN ($placeholders)";
    $params = array_merge($params, $catIds);
}

if ($subCatId) {
    $where[] = 'category_id=?';
    $params[] = $subCatId;
}

if ($keyword) {
    $where[] = '(title LIKE ? OR content LIKE ?)';
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

$whereStr = implode(' AND ', $where);
$total = DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE $whereStr", $params)['cnt'];
$articles = DB::fetchAll("SELECT * FROM articles WHERE $whereStr ORDER BY publish_time DESC LIMIT $offset, $perPage", $params);

$hotArticles = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC LIMIT 10");

$pageTitle = '党纪法规';
include __DIR__ . '/includes/header.php';
?>

    <section class="banner" style="padding:40px 0;">
        <div class="container">
            <h2 style="font-size:30px; display:flex; align-items:center; gap:12px;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                党纪法规
            </h2>
            <p>党内法规 · 国家法律 · 纪法百科 · 明纪释法</p>
        </div>
    </section>

    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>党纪法规</span>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="content-wrap">
                <div class="main-col">
                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>法规检索</h3>
                        </div>
                        <div class="section-body">
                            <form method="get" style="display:flex; gap:10px; margin-bottom:20px;">
                                <input type="text" name="q" value="<?php echo e($keyword); ?>" placeholder="搜索法规标题或内容..." data-validate="title"
                                       style="flex:1; padding:10px 14px; border:2px solid #ddd; border-radius:8px; font-size:14px;">
                                <select name="cat" style="padding:10px 14px; border:2px solid #ddd; border-radius:8px; font-size:14px;">
                                    <option value="">全部分类</option>
                                    <?php foreach ($subCats as $sc): ?>
                                    <option value="<?php echo $sc['id']; ?>" <?php echo $subCatId == $sc['id'] ? 'selected' : ''; ?>><?php echo e($sc['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn">搜索</button>
                            </form>

                            <?php if ($articles): ?>
                            <ul class="news-list">
                                <?php foreach ($articles as $art): ?>
                                <li class="<?php echo $art['is_top'] ? 'top' : ''; ?>">
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $art['id']; ?>" class="title">
                                        <?php echo e($art['title']); ?>
                                    </a>
                                    <span class="date"><?php echo formatDate($art['publish_time']); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php
                            $urlParams = ($keyword ? 'q=' . urlencode($keyword) . '&' : '') . ($subCatId ? 'cat=' . $subCatId . '&' : '');
                            echo paginate($total, $page, $perPage, BASE_URL . 'laws.php?' . $urlParams);
                            ?>
                            <?php else: ?>
                            <p style="text-align:center; color:#999; padding:40px 0;">暂无法规内容<?php echo $keyword ? '，请尝试其他关键词' : ''; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($subCats && !$keyword): ?>
                    <?php foreach ($subCats as $sc): ?>
                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3><?php echo e($sc['name']); ?></h3>
                            <a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($sc['slug']); ?>" class="more">更多 &raquo;</a>
                        </div>
                        <div class="section-body">
                            <?php
                            $subArticles = DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 5", [$sc['id']]);
                            ?>
                            <ul class="news-list">
                                <?php if ($subArticles): ?>
                                    <?php foreach ($subArticles as $art): ?>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $art['id']; ?>" class="title">
                                            <?php echo e($art['title']); ?>
                                        </a>
                                        <span class="date"><?php echo formatDate($art['publish_time']); ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                <li style="text-align:center; color:#999; padding:15px 0;">暂无内容</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="side-col">
                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">法规分类</div>
                        <div class="side-block-body">
                            <ul class="news-list">
                                <?php foreach ($subCats as $sc): ?>
                                <li class="<?php echo $subCatId == $sc['id'] ? 'top' : ''; ?>">
                                    <a href="<?php echo BASE_URL; ?>laws.php?cat=<?php echo $sc['id']; ?>" class="title">
                                        <?php echo e($sc['name']); ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">热门文章</div>
                        <div class="side-block-body">
                            <ul class="hot-list">
                                <?php foreach ($hotArticles as $index => $article): ?>
                                <li>
                                    <span class="rank"><?php echo $index + 1; ?></span>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="item-title">
                                        <?php echo e(truncateStr($article['title'], 28)); ?>
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
