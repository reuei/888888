<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$total = @DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE status=1 AND (title LIKE '%意见%' OR title LIKE '%解释%' OR title LIKE '%规定%' OR title LIKE '%办法%' OR title LIKE '%准则%')")['cnt'] ?: 0;
$policies = @DB::fetchAll("SELECT * FROM articles WHERE status=1 AND (title LIKE '%意见%' OR title LIKE '%解释%' OR title LIKE '%规定%' OR title LIKE '%办法%' OR title LIKE '%准则%') ORDER BY publish_time DESC LIMIT $offset, $perPage") ?: [];

$hotPolicies = @DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC LIMIT 10") ?: [];

$pageTitle = '检务公开';
include __DIR__ . '/includes/header.php';
?>

    <div class="" style="background:linear-gradient(135deg,#d4a017 0%,#b8860b 50%,#a67c00 100%);">
        <div class="container" style="padding:50px 20px;text-align:center;">
            <h1 style="font-family:'SimSun','Songti SC',serif;font-size:36px;color:#fff;letter-spacing:6px;margin-bottom:15px;">检务公开</h1>
            <p style="color:rgba(255,255,255,0.9);font-size:16px;">深化检务公开 保障群众知情权参与权监督权</p>
        </div>
    </div>

    <div class="container">
        <div class="crums" style="padding:15px 0;">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>检务公开</span>
        </div>
    </div>

    <div class="">
        <div class="container">
            <div class="two-col">
                <div class="">
                    <div class="section scroll-reveal">
                        <div class="block-head">
                            <h3>政策文件</h3>
                            <span style="font-size:13px;color:#999;">共 <?php echo $total; ?> 条</span>
                        </div>
                        <div class="block-body">
                            <?php if ($policies): ?>
                            <ul class="news-list">
                                <?php foreach ($policies as $p): ?>
                                <li>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $p['id']; ?>" class="title">
                                        <?php echo e($p['title']); ?>
                                    </a>
                                    <span class="date"><?php echo formatDate($p['publish_time']); ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php echo paginate($total, $page, $perPage, BASE_URL . 'policy.php'); ?>
                            <?php else: ?>
                            <div style="text-align:center;padding:60px 20px;color:#999;">
                                <p style="font-size:48px;margin-bottom:20px;">📜</p>
                                <p>暂无政策解读内容</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="">
                    <div class="block scroll-reveal">
                        <div class="block-title">热门政策</div>
                        <div class="block-body">
                            <ul class="hot-list">
                                <?php foreach ($hotPolicies as $idx => $hp): ?>
                                <li>
                                    <span class="rank"><?php echo $idx + 1; ?></span>
                                    <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $hp['id']; ?>" class="item-title"><?php echo e(truncateStr($hp['title'], 28)); ?></a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="block scroll-reveal">
                        <div class="block-title">法规检索</div>
                        <div class="block-body">
                            <form action="<?php echo BASE_URL; ?>laws.php" method="get">
                                <input type="text" name="q" placeholder="搜索法规..." style="width:100%;padding:10px;border:2px solid #ddd;border-radius:6px;">
                                <button type="submit" class="btn btn-block" style="margin-top:10px;">搜索</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>