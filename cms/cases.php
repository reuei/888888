<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$total = @DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE status=1 AND (title LIKE '%案例%' OR title LIKE '%通报%' OR title LIKE '%处分%')")['cnt'] ?: 0;
$cases = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%案例%' OR title LIKE '%通报%' OR title LIKE '%处分%') ORDER BY publish_time DESC LIMIT $offset, $perPage") ?: [];

$pageTitle = '典型案例通报';
include __DIR__ . '/includes/header.php';
?>

    <div class="gov-header">
        <div class="container" style="padding:50px 20px;text-align:center;position:relative;">
            <span style="position:absolute;top:20px;left:20px;font-size:48px;opacity:0.2;">⚖</span>
            <h1 style="font-family:'SimSun','Songti SC',serif;font-size:36px;color:#fff;letter-spacing:6px;margin-bottom:15px;">典型案例通报</h1>
            <p style="color:rgba(255,255,255,0.9);font-size:16px;">以案为鉴 以案促改 以案促治</p>
        </div>
    </div>

    <div class="container">
        <div class="breadcrumb" style="padding:15px 0;">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <a href="<?php echo BASE_URL; ?>anticorruption.php">反腐倡廉</a>
            <span class="sep">/</span>
            <span>典型案例通报</span>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="content-wrap">
                <div class="main-col">
                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>通报列表</h3>
                            <span style="font-size:13px;color:#999;">共 <?php echo $total; ?> 条</span>
                        </div>
                        <div class="section-body">
                            <?php if ($cases): ?>
                            <div class="timeline">
                                <?php foreach ($cases as $idx => $case): ?>
                                <div class="timeline-item animate-fade-up delay-<?php echo ($idx % 3) + 1; ?>">
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content">
                                        <div class="case-card">
                                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $case['id']; ?>" class="case-title"><?php echo e($case['title']); ?></a>
                                            <div class="case-meta"><?php echo formatDate($case['publish_time']); ?></div>
                                            <?php if ($case['summary']): ?>
                                            <div class="case-result"><?php echo e(truncateStr(strip_tags($case['summary']), 100)); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php echo paginate($total, $page, $perPage, BASE_URL . 'cases.php'); ?>
                            <?php else: ?>
                            <div style="text-align:center;padding:60px 20px;color:#999;">
                                <p style="font-size:48px;margin-bottom:20px;">⚖</p>
                                <p>暂无典型案例通报</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="side-col">
                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">举报入口</div>
                        <div class="side-block-body" style="text-align:center;">
                            <a href="<?php echo BASE_URL; ?>report.php" class="report-btn-large">监督举报</a>
                        </div>
                    </div>

                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">警示教育</div>
                        <div class="side-block-body">
                            <?php
                            $warnings = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%警示%' OR title LIKE '%教育%') ORDER BY publish_time DESC LIMIT 5") ?: [];
                            ?>
                            <ul class="news-list">
                                <?php foreach ($warnings as $w): ?>
                                <li><a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $w['id']; ?>" class="title"><?php echo e(truncateStr($w['title'], 25)); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>