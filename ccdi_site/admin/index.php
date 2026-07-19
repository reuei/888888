<?php
/**
 * 后台管理 - 仪表盘
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/../includes/init.php';
require_admin();

$article_count = db_count('articles');
$draft_count = db_count('articles', "status = 'draft'");
$user_count = db_count('users');
$message_count = db_count('messages', "status = 'unread'");
$report_count = db_count('reports', "status = 'pending'");
$total_views = db_fetch("SELECT COALESCE(SUM(view_count),0) as total FROM articles")['total'] ?? 0;

include __DIR__ . '/header.php';
?>

<div class="dashboard">
    <h2 class="admin-page-title">仪表盘</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f7ff;"><i class="fas fa-newspaper" style="color:#1890ff;"></i></div>
            <div class="stat-info">
                <h3><?php echo $article_count; ?></h3>
                <p>文章总数</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff7e6;"><i class="fas fa-edit" style="color:#fa8c16;"></i></div>
            <div class="stat-info">
                <h3><?php echo $draft_count; ?></h3>
                <p>待审文章</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f6ffed;"><i class="fas fa-users" style="color:#52c41a;"></i></div>
            <div class="stat-info">
                <h3><?php echo $user_count; ?></h3>
                <p>注册用户</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff2f0;"><i class="fas fa-envelope" style="color:#ff4d4f;"></i></div>
            <div class="stat-info">
                <h3><?php echo $message_count; ?></h3>
                <p>未读留言</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f9f0ff;"><i class="fas fa-flag" style="color:#722ed1;"></i></div>
            <div class="stat-info">
                <h3><?php echo $report_count; ?></h3>
                <p>待处理举报</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6fffb;"><i class="fas fa-eye" style="color:#13c2c2;"></i></div>
            <div class="stat-info">
                <h3><?php echo number_format($total_views); ?></h3>
                <p>总浏览量</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3><i class="fas fa-clock"></i> 最新文章</h3>
            <table class="data-table">
                <thead>
                    <tr><th>标题</th><th>状态</th><th>发布时间</th></tr>
                </thead>
                <tbody>
                    <?php $articles = db_fetch_all("SELECT * FROM articles ORDER BY created_at DESC LIMIT 5"); ?>
                    <?php foreach ($articles as $a): ?>
                    <tr>
                        <td><a href="<?php echo admin_url('articles.php?action=edit&id=' . $a['id']); ?>"><?php echo htmlspecialchars(str_cut($a['title'], 30)); ?></a></td>
                        <td><span class="badge badge-<?php echo $a['status'] == 'publish' ? 'success' : 'warning'; ?>"><?php echo $a['status'] == 'publish' ? '已发布' : '草稿'; ?></span></td>
                        <td><?php echo format_time($a['publish_time']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="dashboard-card">
            <h3><i class="fas fa-exclamation-triangle"></i> 待处理举报</h3>
            <table class="data-table">
                <thead>
                    <tr><th>标题</th><th>提交人</th><th>时间</th></tr>
                </thead>
                <tbody>
                    <?php $reports = db_fetch_all("SELECT * FROM reports WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5"); ?>
                    <?php if (empty($reports)): ?>
                    <tr><td colspan="3" style="text-align:center;color:#999;">暂无待处理举报</td></tr>
                    <?php else: ?>
                    <?php foreach ($reports as $r): ?>
                    <tr>
                        <td><a href="<?php echo admin_url('reports.php?action=view&id=' . $r['id']); ?>"><?php echo htmlspecialchars(str_cut($r['title'], 30)); ?></a></td>
                        <td><?php echo htmlspecialchars($r['name'] ?: '匿名'); ?></td>
                        <td><?php echo format_time($r['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>