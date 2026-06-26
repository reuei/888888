<?php
require __DIR__ . '/../includes/config.php';
require_login();
$pageTitle = __('notifications');
require __DIR__ . '/../includes/header.php';
$db = getDb();
$user = current_user();
ensure_notifications_table();

if (!empty($_GET['read'])) {
    $id = intval($_GET['read']);
    $db->prepare('UPDATE notifications SET is_read=1 WHERE id=:id AND user_id=:uid')->execute([':id'=>$id,':uid'=>$user['id']]);
    redirect(YUYUN_URL . '/user/notifications.php');
}
if (!empty($_GET['read_all'])) {
    verify_csrf();
    $db->prepare('UPDATE notifications SET is_read=1 WHERE user_id=:uid')->execute([':uid'=>$user['id']]);
    redirect(YUYUN_URL . '/user/notifications.php');
}

$list = $db->prepare('SELECT * FROM notifications WHERE user_id=:uid ORDER BY id DESC');
$list->execute([':uid'=>$user['id']]);
$notifications = $list->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="section bg-white">
    <div class="container">
        <div class="user-layout">
            <div class="user-sidebar">
                <a href="<?php echo YUYUN_URL ?>/user/index.php"><i class="iconfont icon-gauge"></i> <?php echo __('welcome') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/notifications.php" class="active"><i class="iconfont icon-bell"></i> <?php echo __('notifications') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/tickets.php"><i class="iconfont icon-ticket"></i> <?php echo __('my_tickets') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/feedback.php"><i class="iconfont icon-edit"></i> <?php echo __('feedback') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/profile.php"><i class="iconfont icon-user"></i> <?php echo __('profile') ?></a>
            </div>
            <div class="user-content">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
                    <h2 style="margin:0"><?php echo __('notifications') ?></h2>
                    <?php if (count($notifications) > 0): ?>
                    <a href="?read_all=1&csrf_token=<?php echo csrf_token() ?>" class="btn btn-sm btn-outline" onclick="return confirm('全部标记为已读？')"><i class="iconfont icon-check-square"></i> <?php echo __('mark_read') ?></a>
                    <?php endif; ?>
                </div>
                <?php if (empty($notifications)): ?>
                    <div class="admin-card text-center" style="padding:50px 20px;color:var(--text-2)">
                        <i class="iconfont icon-bell icon-3xl" style="display:block;margin-bottom:12px"></i>
                        <?php echo __('no_notifications') ?>
                    </div>
                <?php else: ?>
                    <div style="display:flex;flex-direction:column;gap:14px">
                    <?php foreach ($notifications as $n): ?>
                        <div class="admin-card" style="border-left:4px solid <?php echo $n['is_read']?'#999':'var(--brand)' ?>;padding:18px 22px">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:8px">
                                <h4 style="margin:0;font-size:16px"><?php echo e($n['title']) ?></h4>
                                <span style="font-size:12px;color:var(--text-2);white-space:nowrap"><?php echo e($n['created_at']) ?></span>
                            </div>
                            <p style="margin:0 0 10px;color:var(--text-2);line-height:1.6"><?php echo nl2br(e($n['content'])) ?></p>
                            <?php if (!$n['is_read']): ?>
                            <a href="?read=<?php echo $n['id'] ?>" class="btn btn-sm btn-primary"><i class="iconfont icon-check"></i> <?php echo __('mark_read') ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
