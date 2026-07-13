<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/slide.php';

$canInstall = !file_exists(DB_PATH) && !file_exists(DATA_DIR . '/install.lock');
if ($canInstall && !file_exists(DB_PATH)) {
    redirect('install.php');
}

$navCategories = getCategories();
$yaowenCat = getCategoryBySlug('yaowen');
$yaowenList = $yaowenCat ? @DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY is_top DESC, publish_time DESC LIMIT 10", [$yaowenCat['id']]) : [];

$hotArticles = @DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC LIMIT 10") ?: [];

$pageTitle = '首页';
include __DIR__ . '/includes/header.php';
?>

<div class="home-layout container">
    <div class="home-left">
        <?php echo getSliderHtml(); ?>
    </div>
    
    <div class="home-center">
        <div class="banner-image" style="background-image: linear-gradient(135deg, #8b0000 0%, #b80000 100%);">
            <h2>坚定不移推进<br>党风廉政建设</h2>
        </div>
    </div>
    
    <div class="home-right">
        <div class="side-block">
            <div class="side-block-title">快捷入口</div>
            <div class="side-block-body">
                <div class="quick-links">
                    <a href="<?php echo BASE_URL; ?>report.php">
                        <i class="iconfont icon-report"></i>
                        <span>监督举报</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>anticorruption.php">
                        <i class="iconfont icon-anticorruption"></i>
                        <span>反腐倡廉</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>laws.php">
                        <i class="iconfont icon-law"></i>
                        <span>党纪法规</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>video.php">
                        <i class="iconfont icon-video"></i>
                        <span>视频中心</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>cases.php">
                        <i class="iconfont icon-case"></i>
                        <span>典型案例</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>policy.php">
                        <i class="iconfont icon-policy"></i>
                        <span>政策解读</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container" style="padding-top:0;">
    <div style="display:flex; gap:20px;">
        <div style="flex:1;">
            <div class="section">
                <div class="section-header">
                    <h3>要闻动态</h3>
                    <a href="<?php echo BASE_URL; ?>category.php?slug=yaowen" class="more">更多 &raquo;</a>
                </div>
                <div class="section-body">
                    <?php if ($yaowenList): ?>
                    <ul class="news-list">
                        <?php foreach ($yaowenList as $art): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $art['id']; ?>" class="title"><?php echo e($art['title']); ?></a>
                            <span class="date"><?php echo formatDate($art['publish_time']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p style="text-align:center; color:#999; padding:30px 0;">暂无内容</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div style="width:300px;">
            <div class="side-block">
                <div class="side-block-title">热门排行</div>
                <div class="side-block-body">
                    <ul class="hot-list">
                        <?php foreach ($hotArticles as $idx => $art): ?>
                        <li>
                            <span class="rank"><?php echo $idx + 1; ?></span>
                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $art['id']; ?>" class="item-title"><?php echo e(truncateStr($art['title'], 28)); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>