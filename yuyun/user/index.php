<?php
require __DIR__ . '/../includes/config.php';
require_login();
$pageTitle = __('user_center');
require __DIR__ . '/../includes/header.php';
$db = getDb();
$user = current_user();
$ticketCount = $db->prepare('SELECT COUNT(*) FROM tickets WHERE user_id=:uid');
$ticketCount->execute([':uid'=>$user['id']]);
$feedbackCount = $db->prepare('SELECT COUNT(*) FROM feedback WHERE user_id=:uid');
$feedbackCount->execute([':uid'=>$user['id']]);
$unread = unread_notification_count($user['id']);
?>
<section class="section bg-white">
    <div class="container">
        <div class="user-layout">
            <?php require __DIR__ . '/../includes/user_sidebar.php'; ?>
            <div>
                    <h2 style="margin-bottom:20px"><?php echo __('welcome') ?>，<?php echo e($user['nickname'] ?: $user['email']) ?></h2>
                    <div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr))">
                        <div class="admin-card" style="text-align:center">
                            <div style="font-size:32px;font-weight:800;color:var(--brand)"><?php echo $ticketCount->fetchColumn() ?></div>
                            <div style="color:var(--text-2)"><?php echo __('my_tickets') ?></div>
                        </div>
                        <div class="admin-card" style="text-align:center">
                            <div style="font-size:32px;font-weight:800;color:var(--brand)"><?php echo $feedbackCount->fetchColumn() ?></div>
                            <div style="color:var(--text-2)"><?php echo __('feedback') ?></div>
                        </div>
                        <div class="admin-card" style="text-align:center;position:relative">
                            <div style="font-size:32px;font-weight:800;color:var(--brand)"><?php echo $unread ?></div>
                            <div style="color:var(--text-2)"><?php echo __('notifications') ?></div>
                            <?php if ($unread > 0): ?><a href="<?php echo YUYUN_URL ?>/user/notifications.php" style="position:absolute;inset:0"></a><?php endif; ?>
                        </div>
                    </div>
                    <div class="admin-card" style="margin-top:24px">
                        <h3 style="margin-bottom:12px">快捷入口</h3>
                        <a href="<?php echo YUYUN_URL ?>/user/tickets.php" class="btn btn-primary" style="margin-right:10px"><?php echo __('my_tickets') ?></a>
                        <a href="<?php echo YUYUN_URL ?>/user/feedback.php" class="btn btn-outline"><?php echo __('feedback') ?></a>
                        <a href="<?php echo YUYUN_URL ?>/user/notifications.php" class="btn btn-outline" style="margin-left:10px"><?php echo __('notifications') ?></a>
                    </div>
                </div>
            </div>
        </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
