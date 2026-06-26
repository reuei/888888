<?php
require __DIR__ . '/../includes/config.php';
require_login();
$pageTitle = __('feedback');
$db = getDb();
$user = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $type = in_array($_POST['type'] ?? '', ['suggestion','report','complaint']) ? $_POST['type'] : 'suggestion';
    $content = trim($_POST['content'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    if ($content) {
        $now = date('Y-m-d H:i:s');
        $db->prepare('INSERT INTO feedback (user_id, type, content, contact, created_at) VALUES (:uid,:t,:c,:contact,:now)')->execute([':uid'=>$user['id'],':t'=>$type,':c'=>$content,':contact'=>$contact,':now'=>$now]);
        flash('success', __('feedback_submitted'));
    } else {
        flash('error', __('fill_content'));
    }
    redirect(YUYUN_URL . '/user/feedback.php');
}
require __DIR__ . '/../includes/header.php';
?>
<section class="section bg-white">
    <div class="container">
        <div class="user-layout">
            <div class="user-sidebar">
                <a href="<?php echo YUYUN_URL ?>/user/index.php"><i class="iconfont icon-gauge"></i> <?php echo __('welcome') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/notifications.php"><i class="iconfont icon-bell"></i> <?php echo __('notifications') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/tickets.php"><i class="iconfont icon-ticket"></i> <?php echo __('my_tickets') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/feedback.php" class="active"><i class="iconfont icon-edit"></i> <?php echo __('feedback') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/profile.php"><i class="iconfont icon-user"></i> <?php echo __('profile') ?></a>
            </div>
            <div class="user-content">
                <h2 style="margin-bottom:20px"><?php echo __('feedback') ?></h2>
                <?php echo render_flash() ?>
                <div class="admin-card" style="max-width:640px">
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                        <div class="form-group"><label><?php echo __('feedback_type') ?></label>
                            <select name="type" class="form-control">
                                <option value="suggestion"><?php echo __('suggestion') ?></option>
                                <option value="report"><?php echo __('report') ?></option>
                                <option value="complaint"><?php echo __('complaint') ?></option>
                            </select>
                        </div>
                        <div class="form-group"><label><?php echo __('feedback_content') ?></label><textarea name="content" class="form-control" required></textarea></div>
                        <div class="form-group"><label><?php echo __('contact_info') ?></label><input type="text" name="contact" class="form-control"></div>
                        <button type="submit" class="btn btn-primary"><?php echo __('submit') ?></button>
                    </form>
                </div>
                <div class="admin-card">
                    <h3 style="margin-bottom:16px"><?php echo __('history') ?></h3>
                    <table class="admin-table">
                        <thead><tr><th><?php echo __('feedback_type') ?></th><th><?php echo __('feedback_content') ?></th><th>提交时间</th></tr></thead>
                        <tbody>
                            <?php
                            $list = $db->prepare('SELECT * FROM feedback WHERE user_id=:uid ORDER BY created_at DESC');
                            $list->execute([':uid'=>$user['id']]);
                            foreach ($list->fetchAll(PDO::FETCH_ASSOC) as $f):
                                $typeLabel = ['suggestion'=>__('suggestion'),'report'=>__('report'),'complaint'=>__('complaint'),'contact'=>__('contact')][$f['type']] ?? $f['type'];
                            ?>
                            <tr>
                                <td><?php echo e($typeLabel) ?></td>
                                <td><?php echo e(mb_substr($f['content'],0,40)) ?><?php echo mb_strlen($f['content'])>40?'...':'' ?></td>
                                <td><?php echo e($f['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
