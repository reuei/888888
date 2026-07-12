<?php
$activeMenu = 'dashboard';
$pageTitle = '仪表盘';
include __DIR__ . '/header.php';

$totalArticles = DB::fetchOne("SELECT COUNT(*) as cnt FROM articles")['cnt'];
$totalCategories = DB::fetchOne("SELECT COUNT(*) as cnt FROM categories")['cnt'];
$totalUsers = DB::fetchOne("SELECT COUNT(*) as cnt FROM users")['cnt'];
$totalMessages = DB::fetchOne("SELECT COUNT(*) as cnt FROM messages WHERE type='message'")['cnt'];
$totalReports = DB::fetchOne("SELECT COUNT(*) as cnt FROM messages WHERE type='report'")['cnt'];
$totalViews = DB::fetchOne("SELECT SUM(views) as total FROM articles")['total'] ?: 0;

$latestArticles = DB::fetchAll("SELECT * FROM articles ORDER BY publish_time DESC LIMIT 8");
$latestUsers = DB::fetchAll("SELECT * FROM users ORDER BY reg_time DESC LIMIT 8");
$latestMessages = DB::fetchAll("SELECT * FROM messages ORDER BY create_time DESC LIMIT 8");
$unreadReports = DB::fetchAll("SELECT * FROM messages WHERE type='report' AND status=0 ORDER BY create_time DESC LIMIT 5");
?>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-num"><?php echo $totalArticles; ?></div>
        <div class="stat-label">文章总数</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?php echo $totalUsers; ?></div>
        <div class="stat-label">注册用户</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?php echo $totalCategories; ?></div>
        <div class="stat-label">栏目数量</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?php echo $totalViews; ?></div>
        <div class="stat-label">总阅读量</div>
    </div>
</div>

<div class="admin-card">
    <h3>最新文章</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>标题</th>
                <th>栏目</th>
                <th>状态</th>
                <th>发布时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($latestArticles as $art): ?>
            <tr>
                <td><?php echo $art['id']; ?></td>
                <td><?php echo e(truncateStr($art['title'], 40)); ?></td>
                <td><?php
                    $cat = getCategory($art['category_id']);
                    echo $cat ? e($cat['name']) : '-';
                ?></td>
                <td>
                    <span class="badge <?php echo $art['status'] == 1 ? 'badge-success' : 'badge-warning'; ?>">
                        <?php echo $art['status'] == 1 ? '已发布' : '草稿'; ?>
                    </span>
                </td>
                <td><?php echo formatDate($art['publish_time']); ?></td>
                <td>
                    <a href="article_edit.php?id=<?php echo $art['id']; ?>" class="btn-small btn-default">编辑</a>
                    <a href="../article.php?id=<?php echo $art['id']; ?>" target="_blank" class="btn-small btn-default">查看</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="admin-card">
    <h3>待处理举报 (<?php echo count($unreadReports); ?>)</h3>
    <?php if ($unreadReports): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>标题</th>
                <th>举报人</th>
                <th>提交时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($unreadReports as $rep): ?>
            <tr>
                <td><?php echo $rep['id']; ?></td>
                <td><?php echo e(truncateStr($rep['title'], 30)); ?></td>
                <td><?php echo e($rep['name'] ?: '匿名'); ?></td>
                <td><?php echo formatDate($rep['create_time']); ?></td>
                <td>
                    <a href="messages.php?type=report" class="btn-small btn-primary">处理</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="color:#999; padding:20px 0; text-align:center;">暂无待处理举报</p>
    <?php endif; ?>
</div>

<div class="admin-card">
    <h3>最新注册用户</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>用户名</th>
                <th>昵称</th>
                <th>角色</th>
                <th>状态</th>
                <th>注册时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($latestUsers as $u): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo e($u['username']); ?></td>
                <td><?php echo e($u['nickname'] ?: '-'); ?></td>
                <td>
                    <?php
                    $roleMap = ['super_admin' => '超级管理员', 'admin' => '管理员', 'subscriber' => '普通用户'];
                    echo e($roleMap[$u['role']] ?? $u['role']);
                    ?>
                </td>
                <td>
                    <span class="badge <?php echo $u['status'] == 1 ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $u['status'] == 1 ? '正常' : '禁用'; ?>
                    </span>
                </td>
                <td><?php echo formatDate($u['reg_time']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
