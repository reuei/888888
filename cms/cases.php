<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$total = @DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE status=1 AND (title LIKE '%案例%' OR title LIKE '%通报%' OR title LIKE '%起诉%' OR title LIKE '%审判%')")['cnt'] ?: 0;
$cases = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%案例%' OR title LIKE '%通报%' OR title LIKE '%起诉%' OR title LIKE '%审判%') ORDER BY publish_time DESC LIMIT $offset, $perPage") ?: [];

$pageTitle = '检察案例';
include __DIR__ . '/includes/header.php';
?>

<div style="background:linear-gradient(135deg, #0a2540 0%, #1e3a5f 100%); color:#fff; padding:60px 20px; text-align:center;">
    <h1 style="font-family:var(--pk-font-serif); font-size:36px; letter-spacing:6px; margin-bottom:14px; font-weight:600;">检察案例发布</h1>
    <p style="font-size:15px; opacity:0.85;">以案释法&nbsp;&nbsp;以案明理&nbsp;&nbsp;维护社会公平正义</p>
</div>

<div class="container">
    <div class="crumbs">
        <a href="<?php echo BASE_URL; ?>index.php">首页</a>
        <span class="sep">/</span>
        <a href="<?php echo BASE_URL; ?>anticorruption.php">检察反腐</a>
        <span class="sep">/</span>
        <span>检察案例</span>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <div class="two-col">
        <div>
            <div class="block">
                <div class="block-head">
                    <h2>通报列表</h2>
                    <span style="font-size:12px; color:var(--pk-gray-500);">共 <?php echo $total; ?> 条</span>
                </div>
                <div class="block-body">
                    <?php if ($cases): ?>
                    <ul class="news-list">
                        <?php foreach ($cases as $case): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $case['id']; ?>" class="news-title"><?php echo e($case['title']); ?></a>
                            <span class="date"><?php echo formatDate($case['publish_time']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php echo paginate($total, $page, $perPage, BASE_URL . 'cases.php'); ?>
                    <?php else: ?>
                    <div class="empty-state"><div class="ico">📋</div><p>暂无典型案例通报</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div>
            <div class="block">
                <div class="block-head">
                    <h2>举报通道</h2>
                </div>
                <div class="block-body" style="text-align:center;">
                    <a href="<?php echo BASE_URL; ?>report.php" class="btn btn-gold btn-block" style="padding:14px;">提交举报</a>
                </div>
            </div>

            <div class="block">
                <div class="block-head">
                    <h2>警示教育</h2>
                </div>
                <div class="block-body">
                    <?php $warnings = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%警示%' OR title LIKE '%教育%') ORDER BY publish_time DESC LIMIT 5") ?: []; ?>
                    <ul class="news-list">
                        <?php foreach ($warnings as $w): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $w['id']; ?>" class="news-title"><?php echo e(truncateStr($w['title'], 25)); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>