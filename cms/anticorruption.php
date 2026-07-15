<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = '检察反腐';
include __DIR__ . '/includes/header.php';
?>

    <div class="gov-header">
        <div class="container" style="padding:50px 20px;text-align:center;">
            <h1 style="font-family:'SimSun','Songti SC',serif;font-size:36px;color:#fff;letter-spacing:6px;margin-bottom:15px;">依法反腐  利剑出鞘</h1>
            <p style="color:rgba(255,255,255,0.9);font-size:16px;">人民检察院依法履行法律监督职责 坚决惩治和预防腐败</p>
        </div>
    </div>

    <div class="container">
        <div class="breadcrumb" style="padding:15px 0;">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>检察反腐</span>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="content-wrap">
                <div class="main-col">
                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>职务犯罪大要案</h3>
                            <a href="<?php echo BASE_URL; ?>cases.php" class="more">更多 &raquo;</a>
                        </div>
                        <div class="section-body">
                            <?php
                            $cases = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%犯罪%' OR title LIKE '%起诉%' OR title LIKE '%审判%' OR title LIKE '%检察%') ORDER BY publish_time DESC LIMIT 6");
                            if ($cases):
                            ?>
                            <div class="article-grid">
                                <?php foreach ($cases as $idx => $case): ?>
                                <div class="case-card animate-fade-up delay-<?php echo ($idx % 3) + 1; ?>">
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $case['id']; ?>" class="case-title"><?php echo e($case['title']); ?></a>
                                    <div class="case-meta"><?php echo formatDate($case['publish_time']); ?></div>
                                    <?php if ($case['summary']): ?>
                                    <div class="case-result"><?php echo e(truncateStr($case['summary'], 60)); ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p style="text-align:center;color:#999;padding:30px 0;">暂无职务犯罪案件信息</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>司法解释与指导案例</h3>
                            <a href="<?php echo BASE_URL; ?>policy.php" class="more">更多 &raquo;</a>
                        </div>
                        <div class="section-body">
                            <?php
                            $policies = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%司法解释%' OR title LIKE '%指导%' OR title LIKE '%意见%') ORDER BY publish_time DESC LIMIT 6");
                            if ($policies):
                            ?>
                            <ul class="news-list">
                                <?php foreach ($policies as $p): ?>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $p['id']; ?>" class="title"><?php echo e($p['title']); ?></a>
                                    <span class="date"><?php echo formatDate($p['publish_time']); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <p style="text-align:center;color:#999;padding:30px 0;">暂无司法解释</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="side-col">
                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">信访举报</div>
                        <div class="side-block-body" style="text-align:center;">
                            <a href="<?php echo BASE_URL; ?>report.php" class="report-btn-large">提交举报</a>
                            <div style="margin-top:20px;text-align:left;">
                                <p style="font-size:13px;color:#666;margin-bottom:8px;"><strong>检察服务热线：</strong>12309</p>
                                <p style="font-size:13px;color:#666;margin-bottom:8px;"><strong>举报邮箱：</strong>jubao@spp.gov.cn</p>
                                <p style="font-size:13px;color:#666;"><strong>来访地址：</strong>最高人民检察院信访接待室</p>
                            </div>
                        </div>
                    </div>

                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">最新动态</div>
                        <div class="side-block-body">
                            <?php
                            $news = @DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY publish_time DESC LIMIT 8");
                            if ($news):
                            ?>
                            <ul class="hot-list">
                                <?php foreach ($news as $idx => $n): ?>
                                <li>
                                    <span class="rank"><?php echo $idx + 1; ?></span>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $n['id']; ?>" class="item-title"><?php echo e(truncateStr($n['title'], 28)); ?></a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>