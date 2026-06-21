<?php
require __DIR__ . '/../includes/config.php';
require_login();
$pageTitle = L('user.messages', '消息通知');
require __DIR__ . '/../includes/header.php';
$db = getDb();
$user = current_user();
if (!empty($_GET['read'])) {
    mark_notification_read(intval($_GET['read']), $user['id']);
    redirect(YUYUN_URL . '/user/messages.php');
}
$notifications = get_notifications($user['id'], 50);
?>
<section class="section bg-white">
    <div class="container">
        <div class="admin-body" style="min-height:auto;display:block">
            <div style="display:grid;grid-template-columns:240px 1fr;gap:24px">
                <div style="background:var(--dark-2);border-radius:12px;padding:14px 0">
                    <a href="<?php echo YUYUN_URL ?>/user/index.php"><i class="iconfont icon-gauge"></i> <?php echo L('user.overview', '概览') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/user/tickets.php"><i class="iconfont icon-ticket"></i> <?php echo L('user.tickets', '我的工单') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/user/feedback.php"><i class="iconfont icon-edit"></i> <?php echo L('user.feedback', '建议/举报') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/user/messages.php" class="active"><i class="iconfont icon-bell"></i> <?php echo L('user.messages', '消息通知') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/user/profile.php"><i class="iconfont icon-user"></i> <?php echo L('user.profile', '个人资料') ?></a>
                </div>
                <div>
                    <h2 style="margin-bottom:20px"><?php echo L('user.messages', '消息通知') ?></h2>
                    <?php if (empty($notifications)): ?>
                        <div class="admin-card text-center" style="padding:40px;color:var(--text-2)"><i class="iconfont icon-bell" style="font-size:40px;display:block;margin-bottom:12px"></i>暂无消息通知</div>
                    <?php else: ?>
                        <div class="message-list">
                            <?php foreach ($notifications as $n): ?>
                            <div class="message-item <?php echo $n['is_read']?'read':'' ?>">
                                <div class="message-head">
                                    <span class="message-title"><?php echo e($n['title']) ?></span>
                                    <span class="message-time"><?php echo e($n['created_at']) ?></span>
                                </div>
                                <div class="message-body"><?php echo nl2br(e($n['content'])) ?></div>
                                <?php if (!$n['is_read']): ?>
                                    <a href="?read=<?php echo $n['id'] ?>" class="btn btn-sm btn-outline" style="margin-top:10px">标记已读</a>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>