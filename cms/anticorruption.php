<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = '反腐倡廉';
include __DIR__ . '/includes/header.php';
?>

    <div class="gov-header">
        <div class="container" style="padding:50px 20px;text-align:center;">
            <h1 style="font-family:'SimSun','Songti SC',serif;font-size:36px;color:#fff;letter-spacing:6px;margin-bottom:15px;">反腐倡廉 正风肃纪</h1>
            <p style="color:rgba(255,255,255,0.9);font-size:16px;">坚定不移推进党风廉政建设和反腐败斗争</p>
        </div>
    </div>

    <div class="container">
        <div class="breadcrumb" style="padding:15px 0;">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>反腐倡廉</span>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="content-wrap">
                <div class="main-col">
                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>典型案例通报</h3>
                            <a href="<?php echo BASE_URL; ?>cases.php" class="more">更多 &raquo;</a>
                        </div>
                        <div class="section-body">
                            <?php
                            $cases = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%案例%' OR title LIKE '%通报%' OR title LIKE '%处分%') ORDER BY publish_time DESC LIMIT 6");
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
                            <p style="text-align:center;color:#999;padding:30px 0;">暂无典型案例</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="section scroll-reveal">
                        <div class="section-header">
                            <h3>政策解读</h3>
                            <a href="<?php echo BASE_URL; ?>policy.php" class="more">更多 &raquo;</a>
                        </div>
                        <div class="section-body">
                            <?php
                            $policies = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%政策%' OR title LIKE '%解读%' OR title LIKE '%规定%') ORDER BY publish_time DESC LIMIT 6");
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
                            <p style="text-align:center;color:#999;padding:30px 0;">暂无政策解读</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="side-col">
                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">举报入口</div>
                        <div class="side-block-body" style="text-align:center;">
                            <a href="<?php echo BASE_URL; ?>report.php" class="report-btn-large">监督举报</a>
                            <div style="margin-top:20px;text-align:left;">
                                <p style="font-size:13px;color:#666;margin-bottom:8px;"><strong>举报电话：</strong>12388</p>
                                <p style="font-size:13px;color:#666;margin-bottom:8px;"><strong>举报邮箱：</strong>jubao@ccdi.gov.cn</p>
                                <p style="font-size:13px;color:#666;"><strong>来信地址：</strong>中央纪委国家监委信访室</p>
                            </div>
                        </div>
                    </div>

                    <div class="side-block scroll-reveal">
                        <div class="side-block-title">工作动态</div>
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