<?php
require __DIR__ . '/../includes/config.php';
require_login();
$pageTitle = '用户中心';
require __DIR__ . '/../includes/header.php';
$db = getDb();
$user = current_user();
$ticketCount = $db->prepare('SELECT COUNT(*) FROM tickets WHERE user_id=:uid');
$ticketCount->execute([':uid'=>$user['id']]);
$feedbackCount = $db->prepare('SELECT COUNT(*) FROM feedback WHERE user_id=:uid');
$feedbackCount->execute([':uid'=>$user['id']]);
?>
<section class="section bg-white">
    <div class="container">
        <div class="admin-body" style="min-height:auto;display:block">
            <div style="display:grid;grid-template-columns:240px 1fr;gap:24px">
                <div style="background:var(--dark-2);border-radius:12px;padding:14px 0">
                    <a href="<?php echo YUYUN_URL ?>/user/index.php" class="active"><i class="fa-solid fa-gauge"></i> 概览</a>
                    <a href="<?php echo YUYUN_URL ?>/user/tickets.php"><i class="fa-solid fa-ticket"></i> 我的工单</a>
                    <a href="<?php echo YUYUN_URL ?>/user/feedback.php"><i class="fa-solid fa-comment-dots"></i> 建议/举报</a>
                    <a href="<?php echo YUYUN_URL ?>/user/profile.php"><i class="fa-solid fa-user-gear"></i> 个人资料</a>
                </div>
                <div>
                    <h2 style="margin-bottom:20px">欢迎，<?php echo e($user['nickname'] ?: $user['email']) ?></h2>
                    <div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr))">
                        <div class="admin-card" style="text-align:center">
                            <div style="font-size:32px;font-weight:800;color:var(--brand)"><?php echo $ticketCount->fetchColumn() ?></div>
                            <div style="color:var(--text-2)">我的工单</div>
                        </div>
                        <div class="admin-card" style="text-align:center">
                            <div style="font-size:32px;font-weight:800;color:var(--brand)"><?php echo $feedbackCount->fetchColumn() ?></div>
                            <div style="color:var(--text-2)">建议/举报</div>
                        </div>
                    </div>
                    <div class="admin-card" style="margin-top:24px">
                        <h3 style="margin-bottom:12px">快捷入口</h3>
                        <a href="<?php echo YUYUN_URL ?>/user/tickets.php" class="btn btn-primary" style="margin-right:10px">提交工单</a>
                        <a href="<?php echo YUYUN_URL ?>/user/feedback.php" class="btn btn-outline">提交建议/举报</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
