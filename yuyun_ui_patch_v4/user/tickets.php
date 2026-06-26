<?php
require __DIR__ . '/../includes/config.php';
require_login();
$pageTitle = __('my_tickets');
$db = getDb();
$user = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $now = date('Y-m-d H:i:s');
    if (isset($_POST['close'])) {
        $db->prepare('UPDATE tickets SET status=1, updated_at=:now WHERE id=:id AND user_id=:uid')->execute([':id'=>$_POST['ticket_id'],':uid'=>$user['id'],':now'=>$now]);
        flash('success', __('ticket_closed'));
        redirect(YUYUN_URL . '/user/tickets.php?id=' . intval($_POST['ticket_id']));
    } elseif (isset($_POST['reply'])) {
        $content = trim($_POST['content'] ?? '');
        if ($content) {
            $db->prepare('INSERT INTO ticket_replies (ticket_id, user_id, content, created_at) VALUES (:tid,:uid,:c,:now)')->execute([':tid'=>$_POST['ticket_id'],':uid'=>$user['id'],':c'=>$content,':now'=>$now]);
            $db->prepare('UPDATE tickets SET updated_at=:now WHERE id=:id')->execute([':id'=>$_POST['ticket_id'],':now'=>$now]);
        }
        redirect(YUYUN_URL . '/user/tickets.php?id=' . intval($_POST['ticket_id']));
    } else {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title && $content) {
            $stmt = $db->prepare('INSERT INTO tickets (user_id, title, category, status, created_at, updated_at) VALUES (:uid,:t,:c,0,:now,:now)');
            $stmt->execute([':uid'=>$user['id'],':t'=>$title,':c'=>$category,':now'=>$now]);
            $tid = $db->lastInsertId();
            $db->prepare('INSERT INTO ticket_replies (ticket_id, user_id, content, created_at) VALUES (:tid,:uid,:c,:now)')->execute([':tid'=>$tid,':uid'=>$user['id'],':c'=>$content,':now'=>$now]);
            flash('success', __('ticket_submitted'));
            redirect(YUYUN_URL . '/user/tickets.php?id=' . $tid);
        } else {
            flash('error', __('fill_title_content'));
            redirect(YUYUN_URL . '/user/tickets.php');
        }
    }
}
$detailId = intval($_GET['id'] ?? 0);
require __DIR__ . '/../includes/header.php';
?>
<section class="section bg-white">
    <div class="container">
        <div class="user-layout">
            <div class="user-sidebar">
                <a href="<?php echo YUYUN_URL ?>/user/index.php"><i class="iconfont icon-gauge"></i> <?php echo __('welcome') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/notifications.php"><i class="iconfont icon-bell"></i> <?php echo __('notifications') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/tickets.php" class="active"><i class="iconfont icon-ticket"></i> <?php echo __('my_tickets') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/feedback.php"><i class="iconfont icon-edit"></i> <?php echo __('feedback') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/profile.php"><i class="iconfont icon-user"></i> <?php echo __('profile') ?></a>
            </div>
            <div class="user-content">
                <h2 style="margin-bottom:20px"><?php echo __('my_tickets') ?></h2>
                <?php echo render_flash() ?>
                <?php if ($detailId): ?>
                    <?php
                    $t = $db->prepare('SELECT * FROM tickets WHERE id=:id AND user_id=:uid LIMIT 1');
                    $t->execute([':id'=>$detailId,':uid'=>$user['id']]);
                    $ticket = $t->fetch(PDO::FETCH_ASSOC);
                    if (!$ticket) { echo '<div class="alert alert-error">'.__('ticket_not_exist').'</div>'; }
                    else {
                        $replies = $db->prepare('SELECT r.*, u.nickname, u.email FROM ticket_replies r LEFT JOIN users u ON r.user_id=u.id WHERE r.ticket_id=:tid ORDER BY r.created_at');
                        $replies->execute([':tid'=>$ticket['id']]);
                    ?>
                    <div class="admin-card">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                            <h3 style="margin:0"><?php echo e($ticket['title']) ?></h3>
                            <span class="btn btn-sm <?php echo $ticket['status']==0?'btn-primary':'btn-dark' ?>"><?php echo $ticket['status']==0?__('status_open'):__('status_closed') ?></span>
                        </div>
                        <div style="margin-bottom:20px">
                            <?php foreach ($replies->fetchAll(PDO::FETCH_ASSOC) as $r): ?>
                                <div style="margin-bottom:16px;padding:14px;border-radius:8px;background:<?php echo $r['is_staff']?'#fff7e6':'#f5f7fa' ?>;border-left:3px solid <?php echo $r['is_staff']?'var(--brand)':'#ccc' ?>">
                                    <div style="font-weight:600;margin-bottom:6px;color:var(--dark)"><?php echo e($r['nickname'] ?: $r['email']) ?> <?php echo $r['is_staff']?'<span style="color:var(--brand)">['.__('official_tag').']</span>':'' ?></div>
                                    <div style="color:var(--text-2);white-space:pre-wrap"><?php echo nl2br(e($r['content'])) ?></div>
                                    <div style="font-size:12px;color:#999;margin-top:6px"><?php echo e($r['created_at']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($ticket['status'] == 0): ?>
                            <form method="post" class="mt-4">
                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id'] ?>">
                                <div class="form-group"><textarea name="content" class="form-control" placeholder="<?php echo __('reply_placeholder') ?>" required></textarea></div>
                                <button type="submit" name="reply" class="btn btn-primary"><?php echo __('reply') ?></button>
                                <button type="submit" name="close" class="btn btn-outline" style="margin-left:10px" onclick="return confirm('<?php echo e(__('close_ticket_confirm')) ?>')"><?php echo __('close_ticket') ?></button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <?php } ?>
                    <p><a href="<?php echo YUYUN_URL ?>/user/tickets.php" class="more">&larr; <?php echo __('back_to_list') ?></a></p>
                <?php else: ?>
                    <div class="admin-card">
                        <h3 style="margin-bottom:16px"><?php echo __('new_ticket_form') ?></h3>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                            <div class="form-row">
                                <div class="form-group"><label><?php echo __('ticket_title') ?></label><input type="text" name="title" class="form-control" required></div>
                                <div class="form-group"><label><?php echo __('ticket_category') ?></label>
                                    <select name="category" class="form-control">
                                        <option><?php echo __('cat_technical') ?></option>
                                        <option><?php echo __('cat_after_sales') ?></option>
                                        <option><?php echo __('cat_billing') ?></option>
                                        <option><?php echo __('cat_complaint') ?></option>
                                        <option><?php echo __('cat_other') ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group"><label><?php echo __('ticket_question') ?></label><textarea name="content" class="form-control" required></textarea></div>
                            <button type="submit" class="btn btn-primary"><?php echo __('new_ticket') ?></button>
                        </form>
                    </div>
                    <div class="admin-card">
                        <h3 style="margin-bottom:16px"><?php echo __('ticket_records') ?></h3>
                        <table class="admin-table">
                            <thead><tr><th><?php echo __('ticket_title') ?></th><th><?php echo __('ticket_category') ?></th><th>状态</th><th>更新时间</th><th><?php echo __('view_detail') ?></th></tr></thead>
                            <tbody>
                                <?php
                                $list = $db->prepare('SELECT * FROM tickets WHERE user_id=:uid ORDER BY updated_at DESC');
                                $list->execute([':uid'=>$user['id']]);
                                foreach ($list->fetchAll(PDO::FETCH_ASSOC) as $tk): ?>
                                <tr>
                                    <td><?php echo e($tk['title']) ?></td>
                                    <td><?php echo e($tk['category']) ?></td>
                                    <td><?php echo $tk['status']==0?'<span class="status-dot status-open"></span>'.__('status_open'):'<span class="status-dot status-closed"></span>'.__('status_closed') ?></td>
                                    <td><?php echo e($tk['updated_at']) ?></td>
                                    <td><a href="?id=<?php echo $tk['id'] ?>" class="text-brand"><?php echo __('view_detail') ?></a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
