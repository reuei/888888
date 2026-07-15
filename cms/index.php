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
        <div class="banner-image">
            <div class="banner-emblem">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="width:80px; height:80px;">
                    <circle cx="50" cy="50" r="46" fill="none" stroke="#c9a227" stroke-width="2"/>
                    <circle cx="50" cy="50" r="38" fill="none" stroke="#fff" stroke-width="1"/>
                    <path d="M50 18 L58 28 L70 24 L66 36 L78 42 L66 50 L70 62 L58 60 L50 72 L42 60 L30 62 L34 50 L22 42 L34 36 L30 24 L42 28 Z" fill="#c9a227" opacity="0.9"/>
                    <text x="50" y="55" text-anchor="middle" fill="#fff" font-size="11" font-weight="bold" font-family="SimSun, serif">检察</text>
                </svg>
            </div>
            <h2>忠诚 担当<br>公正 清廉</h2>
            <p class="banner-sub">人民检察院是国家的法律监督机关</p>
        </div>
    </div>

    <div class="home-right">
        <div class="side-block">
            <div class="side-block-title">检务直通车</div>
            <div class="side-block-body">
                <div class="quick-links">
                    <a href="<?php echo BASE_URL; ?>report.php">
                        <i class="iconfont icon-report"></i>
                        <span>信访举报</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>anticorruption.php">
                        <i class="iconfont icon-anticorruption"></i>
                        <span>反腐肃贪</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>laws.php">
                        <i class="iconfont icon-law"></i>
                        <span>法律法规</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>video.php">
                        <i class="iconfont icon-video"></i>
                        <span>检察视频</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>cases.php">
                        <i class="iconfont icon-case"></i>
                        <span>典型案例</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>policy.php">
                        <i class="iconfont icon-policy"></i>
                        <span>检务公开</span>
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
                    <h3>检察要闻</h3>
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
                <div class="side-block-title">关注排行</div>
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