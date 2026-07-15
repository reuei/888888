<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$videoCat = getCategoryBySlug('video');
$catIds = [$videoCat ? $videoCat['id'] : 0];
if ($videoCat) {
    $children = getChildCategories($videoCat['id']);
    foreach ($children as $child) $catIds[] = $child['id'];
}

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

$placeholders = implode(',', array_fill(0, count($catIds), '?'));
$total = DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE category_id IN ($placeholders) AND status=1", $catIds)['cnt'];
$videos = DB::fetchAll("SELECT * FROM articles WHERE category_id IN ($placeholders) AND status=1 ORDER BY publish_time DESC LIMIT $offset, $perPage", $catIds);

$hotArticles = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC LIMIT 10");

$pageTitle = '视频中心';
include __DIR__ . '/includes/header.php';
?>

    <section class="banner" style="padding:40px 0;">
        <div class="container">
            <h2 style="font-size:30px; display:flex; align-items:center; gap:12px;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><polygon points="10,8 16,12 10,16" fill="#fff"/></svg>
                视频中心
            </h2>
            <p>权威发布，深度解读，以案说法</p>
        </div>
    </section>

    <div class="container">
        <div class="crums">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>视频中心</span>
        </div>
    </div>

    <div class="">
        <div class="container">
            <div class="two-col">
                <div class="">
                    <div class="section scroll-reveal">
                        <div class="block-head">
                            <h3>最新视频</h3>
                        </div>
                        <div class="block-body">
                            <?php if ($videos): ?>
                            <div class="video-grid">
                                <?php foreach ($videos as $idx => $video): ?>
                                <div class="video-card animate-fade-up delay-<?php echo ($idx % 3) + 1; ?>">
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $video['id']; ?>" style="text-decoration:none;">
                                        <div class="video-thumb">
                                            <?php if ($video['cover_image']): ?>
                                                <img src="<?php echo BASE_URL . UPLOAD_URL . e($video['cover_image']); ?>" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;" alt="">
                                            <?php endif; ?>
                                            <div class="play-btn"></div>
                                            <span class="duration"><?php echo sprintf('%02d:%02d', rand(3, 15), rand(0, 59)); ?></span>
                                        </div>
                                        <div class="video-info">
                                            <h4><?php echo e(truncateStr($video['title'], 35)); ?></h4>
                                            <div class="meta">
                                                <span><?php echo formatDate($video['publish_time']); ?></span>
                                                <span>· <?php echo $video['views']; ?> 次播放</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php echo paginate($total, $page, $perPage, BASE_URL . 'video.php'); ?>
                            <?php else: ?>
                            <p style="text-align:center; color:#999; padding:40px 0;">暂无视频内容</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="">
                    <div class="block scroll-reveal">
                        <div class="block-title">视频分类</div>
                        <div class="block-body">
                            <ul class="news-list">
                                <li><a href="<?php echo BASE_URL; ?>video.php" class="title">全部视频</a></li>
                                <?php
                                $subCats = $videoCat ? getChildCategories($videoCat['id']) : [];
                                foreach ($subCats as $sub):
                                ?>
                                <li><a href="<?php echo BASE_URL; ?>category.php?slug=<?php echo e($sub['slug']); ?>" class="title"><?php echo e($sub['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="block scroll-reveal">
                        <div class="block-title">热门视频</div>
                        <div class="block-body">
                            <ul class="hot-list">
                                <?php foreach (array_slice($hotArticles, 0, 8) as $index => $article): ?>
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
