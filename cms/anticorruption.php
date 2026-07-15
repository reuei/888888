<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = '检察反腐';
include __DIR__ . '/includes/header.php';
?>

<div style="background:linear-gradient(135deg, #0a2540 0%, #1e3a5f 100%); color:#fff; padding:60px 20px; text-align:center; position:relative; overflow:hidden;">
    <svg viewBox="0 0 200 60" xmlns="http://www.w3.org/2000/svg" style="position:absolute; left:-30px; bottom:-10px; opacity:0.08; width:240px; height:240px;">
        <path d="M100 8 L120 30 L160 28 L150 60 L180 70 L150 90 L160 130 L120 120 L100 150 L80 120 L40 130 L50 90 L20 70 L50 60 L40 28 L80 30 Z" fill="#c9a227"/>
    </svg>
    <div style="position:relative; z-index:1;">
        <h1 style="font-family:var(--pk-font-serif); font-size:36px; letter-spacing:6px; margin-bottom:14px; font-weight:600;">依法反腐&nbsp;&nbsp;利剑出鞘</h1>
        <p style="font-size:15px; opacity:0.85; letter-spacing:1px;">人民检察院依法履行法律监督职责&nbsp;&nbsp;坚决惩治和预防腐败</p>
    </div>
</div>

<div class="container">
    <div class="crumbs">
        <a href="<?php echo BASE_URL; ?>index.php">首页</a>
        <span class="sep">/</span>
        <span>检察反腐</span>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <div class="two-col">
        <div>
            <div class="block">
                <div class="block-head">
                    <h2>职务犯罪大要案</h2>
                    <a href="<?php echo BASE_URL; ?>cases.php" class="more">查看全部 &raquo;</a>
                </div>
                <div class="block-body">
                    <?php
                    $cases = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%犯罪%' OR title LIKE '%起诉%' OR title LIKE '%审判%' OR title LIKE '%检察%') ORDER BY publish_time DESC LIMIT 6");
                    if ($cases): ?>
                    <div class="case-row">
                        <?php foreach ($cases as $case): ?>
                        <div class="case-item">
                            <div class="case-no">CASE · <?php echo str_pad($case['id'], 4, '0', STR_PAD_LEFT); ?></div>
                            <h4><a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $case['id']; ?>" style="color:inherit;"><?php echo e($case['title']); ?></a></h4>
                            <p><?php echo e(truncateStr($case['summary'] ?: $case['title'], 80)); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state"><div class="ico">📋</div><p>暂无职务犯罪案件信息</p></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="block">
                <div class="block-head">
                    <h2>司法解释与指导案例</h2>
                    <a href="<?php echo BASE_URL; ?>policy.php" class="more">查看全部 &raquo;</a>
                </div>
                <div class="block-body">
                    <?php
                    $policies = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%司法解释%' OR title LIKE '%指导%' OR title LIKE '%意见%' OR title LIKE '%办法%') ORDER BY publish_time DESC LIMIT 6");
                    if ($policies): ?>
                    <ul class="news-list">
                        <?php foreach ($policies as $p): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $p['id']; ?>" class="news-title"><?php echo e($p['title']); ?></a>
                            <span class="date"><?php echo formatDate($p['publish_time']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <div class="empty-state"><div class="ico">📋</div><p>暂无司法解释</p></div>
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
                    <a href="<?php echo BASE_URL; ?>report.php" class="btn btn-gold btn-block" style="padding:14px;">立即提交举报</a>
                    <div style="margin-top:18px; font-size:13px; color:var(--pk-gray-600); line-height:2; text-align:left;">
                        <p>· 检察服务热线：<strong style="color:var(--pk-blue);">12309</strong></p>
                        <p>· 举报邮箱：jubao@spp.gov.cn</p>
                        <p>· 来访地址：最高人民检察院信访接待室</p>
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="block-head">
                    <h2>最新动态</h2>
                </div>
                <div class="block-body">
                    <?php
                    $news = @DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY publish_time DESC LIMIT 8");
                    if ($news): ?>
                    <ol class="rank-list">
                        <?php $r = 0; foreach ($news as $n): $r++; ?>
                        <li>
                            <span class="rank-no"><?php echo $r; ?></span>
                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $n['id']; ?>"><?php echo e(truncateStr($n['title'], 26)); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>