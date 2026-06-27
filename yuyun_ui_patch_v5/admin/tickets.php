<?php
$pageTitle = '工单管理';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$adminUser = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $now = date('Y-m-d H:i:s');
    if (isset($_POST['reply'])) {
        $tid = intval($_POST['ticket_id']);
        $content = trim($_POST['content'] ?? '');
        if ($content) {
            $db->prepare('INSERT INTO ticket_replies (ticket_id, user_id, content, is_staff, created_at) VALUES (:tid,:uid,:c,1,:now)')->execute([':tid'=>$tid,':uid'=>$adminUser['id'],':c'=>$content,':now'=>$now]);
            $db->prepare('UPDATE tickets SET updated_at=:now WHERE id=:id')->execute([':id'=>$tid,':now'=>$now]);
        }
        redirect(YUYUN_URL . '/admin/tickets.php?id=' . $tid);
    } elseif (isset($_POST['status'])) {
        $tid = intval($_POST['ticket_id']);
        $st = intval($_POST['status']);
        $db->prepare('UPDATE tickets SET status=:s, updated_at=:now WHERE id=:id')->execute([':s'=>$st,':id'=>$tid,':now'=>$now]);
        redirect(YUYUN_URL . '/admin/tickets.php?id=' . $tid);
    }
}
$detailId = intval($_GET['id'] ?? 0);
?>
<div class="admin-card">
    <?php if ($detailId): ?>
        <?php
        $t = $db->prepare('SELECT t.*, u.email, u.nickname FROM tickets t LEFT JOIN users u ON t.user_id=u.id WHERE t.id=:id LIMIT 1');
        $t->execute([':id'=>$detailId]);
        $ticket = $t->fetch(PDO::FETCH_ASSOC);
        if (!$ticket) { echo '<div class="alert alert-error">工单不存在</div>'; }
        else {
            $replies = $db->prepare('SELECT r.*, u.nickname, u.email FROM ticket_replies r LEFT JOIN users u ON r.user_id=u.id WHERE r.ticket_id=:tid ORDER BY r.created_at');
            $replies->execute([':tid'=>$ticket['id']]);
        ?>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
            <h3 style="margin:0"><?php echo e($ticket['title']) ?></h3>
            <span class="btn btn-sm <?php echo $ticket['status']==0?'btn-primary':'btn-dark' ?>"><?php echo $ticket['status']==0?'处理中':'已关闭' ?></span>
        </div>
        <p style="color:var(--text-2)">用户：<?php echo e($ticket['nickname'] ?: $ticket['email']) ?> | 分类：<?php echo e($ticket['category']) ?> | 时间：<?php echo e($ticket['created_at']) ?></p>
        <div style="margin:20px 0">
            <?php foreach ($replies->fetchAll(PDO::FETCH_ASSOC) as $r): ?>
                <div style="margin-bottom:16px;padding:14px;border-radius:8px;background:<?php echo $r['is_staff']?'#fff7e6':'#f5f7fa' ?>;border-left:3px solid <?php echo $r['is_staff']?'var(--brand)':'#ccc' ?>">
                    <div style="font-weight:600;margin-bottom:6px;color:var(--dark)"><?php echo e($r['nickname'] ?: $r['email']) ?> <?php echo $r['is_staff']?'<span style="color:var(--brand)">[官方]</span>':'' ?></div>
                    <div style="color:var(--text-2);white-space:pre-wrap"><?php echo nl2br(e($r['content'])) ?></div>
                    <div style="font-size:12px;color:#999;margin-top:6px"><?php echo e($r['created_at']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if ($ticket['status'] == 0): ?>
            <form method="post" class="mt-4">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id'] ?>">
                <div class="form-group"><textarea name="content" class="form-control" placeholder="官方回复" required></textarea></div>
                <button type="submit" name="reply" class="btn btn-primary">回复</button>
            </form>
        <?php endif; ?>
        <form method="post" style="margin-top:16px">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id'] ?>">
            <button type="submit" name="status" value="<?php echo $ticket['status']==0?1:0 ?>" class="btn btn-outline"><?php echo $ticket['status']==0?'关闭工单':'重新打开' ?></button>
        </form>
        <p style="margin-top:20px"><a href="<?php echo YUYUN_URL ?>/admin/tickets.php" class="text-brand">&larr; 返回列表</a></p>
        <?php } ?>
    <?php else: ?>
        <h3 style="margin-bottom:18px">工单列表</h3>
        <table class="admin-table">
            <thead><tr><th>ID</th><th>用户</th><th>标题</th><th>分类</th><th>状态</th><th>更新时间</th><th>操作</th></tr></thead>
            <tbody>
                <?php
                $list = $db->query('SELECT t.*, u.email, u.nickname FROM tickets t LEFT JOIN users u ON t.user_id=u.id ORDER BY t.updated_at DESC')->fetchAll(PDO::FETCH_ASSOC);
                foreach ($list as $tk): ?>
                <tr>
                    <td><?php echo $tk['id'] ?></td>
                    <td><?php echo e($tk['nickname'] ?: $tk['email']) ?></td>
                    <td><?php echo e($tk['title']) ?></td>
                    <td><?php echo e($tk['category']) ?></td>
                    <td><?php echo $tk['status']==0?'<span class="status-dot status-open"></span>处理中':'<span class="status-dot status-closed"></span>已关闭' ?></td>
                    <td><?php echo e($tk['updated_at']) ?></td>
                    <td><a href="?id=<?php echo $tk['id'] ?>" class="text-brand">处理</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
