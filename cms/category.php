<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$category = getCategoryBySlug($slug);

if (!$category) {
    header('HTTP/1.1 404 Not Found');
    $pageTitle = '页面不存在';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container" style="padding:50px 0; text-align:center;"><h2>栏目不存在</h2><p style="color:#999; margin-top:10px;">您访问的栏目不存在或已删除</p></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$currentCatId = $category['id'];
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$childCats = getChildCategories($category['id']);
$catIds = [$category['id']];
foreach ($childCats as $child) {
    $catIds[] = $child['id'];
}

$placeholders = implode(',', array_fill(0, count($catIds), '?'));
$total = DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE category_id IN ($placeholders) AND status=1", $catIds)['cnt'];
$articles = DB::fetchAll("SELECT * FROM articles WHERE category_id IN ($placeholders) AND status=1 ORDER BY is_top DESC, publish_time DESC LIMIT $offset, $perPage", $catIds);

$pageTitle = $category['name'];
$breadcrumb = getBreadcrumb($category['id']);

include __DIR__ . '/includes/header.php';
?>

    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <?php foreach ($breadcrumb as $bc): ?>
                <span class="sep">/</span>
                <a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($bc['slug']); ?>"><?php echo e($bc['name']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="content-wrap">
                <div class="main-col">
                    <div class="section">
                        <div class="section-header">
                            <h3><?php echo e($category['name']); ?></h3>
                        </div>
                        <div class="section-body">
                            <?php if ($articles): ?>
                            <ul class="news-list">
                                <?php foreach ($articles as $article): ?>
                                <li class="<?php echo $article['is_top'] ? 'top' : ''; ?>">
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $article['id']; ?>" class="title">
                                        <?php echo e($article['title']); ?>
                                    </a>
                                    <span class="date"><?php echo formatDate($article['publish_time']); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php echo paginate($total, $page, $perPage, BASE_URL . 'category.php?slug=' . $slug); ?>
                            <?php else: ?>
                            <p style="text-align:center; color:#999; padding:30px 0;">暂无内容</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="side-col">
                    <div class="side-block">
                        <div class="side-block-title">栏目导航</div>
                        <div class="side-block-body">
                            <ul class="news-list">
                                <?php if ($childCats): ?>
                                    <?php foreach ($childCats as $child): ?>
                                    <li>
                                        <a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($child['slug']); ?>" class="title">
                                            <?php echo e($child['name']); ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php
                                    $parent = getCategory($category['parent_id']);
                                    $siblings = $parent ? getChildCategories($parent['id']) : [];
                                    ?>
                                    <?php foreach ($siblings as $sib): ?>
                                    <li class="<?php echo $sib['id'] == $category['id'] ? 'top' : ''; ?>">
                                        <a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($sib['slug']); ?>" class="title">
                                            <?php echo e($sib['name']); ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="side-block">
                        <div class="side-block-title">热门文章</div>
                        <div class="side-block-body">
                            <ul class="hot-list">
                                <?php
                                $hotList = DB::fetchAll("SELECT * FROM articles WHERE category_id IN ($placeholders) AND status=1 ORDER BY views DESC LIMIT 8", $catIds);
                                foreach ($hotList as $index => $article):
                                ?>
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
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
