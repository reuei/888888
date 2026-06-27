<?php
$pageTitle = __('admin_dashboard');
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
    <h3 style="margin-bottom:18px"><?php echo __('data_overview') ?></h3>
    <div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(160px,1fr))">
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['users'] ?></div>
            <div style="color:var(--text-2);font-size:13px"><?php echo __('registered_users') ?></div>
        </div>
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['tickets'] ?></div>
            <div style="color:var(--text-2);font-size:13px"><?php echo __('total_tickets') ?></div>
        </div>
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['open_tickets'] ?></div>
            <div style="color:var(--text-2);font-size:13px"><?php echo __('open_tickets') ?></div>
        </div>
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['products'] ?></div>
            <div style="color:var(--text-2);font-size:13px"><?php echo __('products_count') ?></div>
        </div>
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['partners'] ?></div>
            <div style="color:var(--text-2);font-size:13px"><?php echo __('partners_count') ?></div>
        </div>
        <div style="text-align:center;padding:18px;background:#f5f7fa;border-radius:8px">
            <div style="font-size:28px;font-weight:800;color:var(--brand)"><?php echo $counts['feedback'] ?></div>
            <div style="color:var(--text-2);font-size:13px"><?php echo __('feedback_count') ?></div>
        </div>
    </div>
</div>
<div class="admin-card">
    <h3 style="margin-bottom:18px"><?php echo __('latest_tickets') ?></h3>
    <table class="admin-table">
        <thead><tr><th><?php echo __('feedback_user') ?></th><th><?php echo __('ticket_title') ?></th><th><?php echo __('status') ?></th><th><?php echo __('feedback_time') ?></th><th><?php echo __('feedback_action') ?></th></tr></thead>
        <tbody>
            <?php
            $latest = $db->query('SELECT t.*, u.email FROM tickets t LEFT JOIN users u ON t.user_id=u.id ORDER BY t.created_at DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
            foreach ($latest as $tk): ?>
            <tr>
                <td><?php echo e($tk['email']) ?></td>
                <td><?php echo e($tk['title']) ?></td>
                <td><?php echo $tk['status']==0?'<span class="status-dot status-open"></span>'.__('status_open'):'<span class="status-dot status-closed"></span>'.__('status_closed') ?></td>
                <td><?php echo e($tk['created_at']) ?></td>
                <td><a href="<?php echo YUYUN_URL ?>/admin/tickets.php?id=<?php echo $tk['id'] ?>" class="text-brand"><?php echo __('view_detail') ?></a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
