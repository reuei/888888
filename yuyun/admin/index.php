<?php
$pageTitle = '概览';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$counts = [
    'users' => $db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'tickets' => $db->query('SELECT COUNT(*) FROM tickets')->fetchColumn(),
    'open_tickets' => $db->query('SELECT COUNT(*) FROM tickets WHERE status=0')->fetchColumn(),
    'products' => $db->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'partners' => $db->query('SELECT COUNT(*) FROM partners')->fetchColumn(),
    'feedback' => $db->query('SELECT COUNT(*) FROM feedback')->fetchColumn(),
];
?>
<div class="admin-card">
    <h3 style="margin-bottom:18px">数据概览</h3>
    <div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr))">
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['users'] ?></div>
            <div style="color:var(--text-2);font-size:13px">注册用户</div>
        </div>
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['tickets'] ?></div>
            <div style="color:var(--text-2);font-size:13px">工单总数</div>
        </div>
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['open_tickets'] ?></div>
            <div style="color:var(--text-2);font-size:13px">待处理工单</div>
        </div>
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['products'] ?></div>
            <div style="color:var(--text-2);font-size:13px">产品</div>
        </div>
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['partners'] ?></div>
            <div style="color:var(--text-2);font-size:13px">合作伙伴</div>
        </div>
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['feedback'] ?></div>
            <div style="color:var(--text-2);font-size:13px">反馈</div>
        </div>
    </div>
</div>
<div class="admin-card">
    <h3 style="margin-bottom:18px">最新工单</h3>
    <table class="admin-table">
        <thead><tr><th>用户</th><th>标题</th><th>状态</th><th>时间</th><th>操作</th></tr></thead>
        <tbody>
            <?php
            $latest = $db->query('SELECT t.*, u.email FROM tickets t LEFT JOIN users u ON t.user_id=u.id ORDER BY t.created_at DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
            foreach ($latest as $tk): ?>
            <tr>
                <td><?php echo e($tk['email']) ?></td>
                <td><?php echo e($tk['title']) ?></td>
                <td><?php echo $tk['status']==0?'<span class="status-dot status-open"></span>处理中':'<span class="status-dot status-closed"></span>已关闭' ?></td>
                <td><?php echo e($tk['created_at']) ?></td>
                <td><a href="<?php echo YUYUN_URL ?>/admin/tickets.php?id=<?php echo $tk['id'] ?>" class="text-brand">查看</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
