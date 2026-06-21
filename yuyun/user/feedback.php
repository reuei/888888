<?php
require __DIR__ . '/../includes/config.php';
require_login();
$pageTitle = '建议与举报';
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
        flash('success', '提交成功，感谢您的反馈');
    } else {
        flash('error', '请填写内容');
    }
    redirect(YUYUN_URL . '/user/feedback.php');
}
require __DIR__ . '/../includes/header.php';
?>
<section class="section bg-white">
    <div class="container">
        <div style="display:grid;grid-template-columns:240px 1fr;gap:24px">
            <div style="background:var(--dark-2);border-radius:12px;padding:14px 0">
                <a href="<?php echo YUYUN_URL ?>/user/index.php"><i class="iconfont icon-gauge"></i> 概览</a>
                <a href="<?php echo YUYUN_URL ?>/user/tickets.php"><i class="iconfont icon-ticket"></i> 我的工单</a>
                <a href="<?php echo YUYUN_URL ?>/user/feedback.php" class="active"><i class="iconfont icon-edit"></i> 建议/举报</a>
                <a href="<?php echo YUYUN_URL ?>/user/profile.php"><i class="iconfont icon-user"></i> 个人资料</a>
            </div>
            <div>
                <h2 style="margin-bottom:20px">建议 / 举报</h2>
                <?php echo render_flash() ?>
                <div class="admin-card" style="max-width:640px">
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                        <div class="form-group"><label>类型</label>
                            <select name="type" class="form-control">
                                <option value="suggestion">产品建议</option>
                                <option value="report">违规举报</option>
                                <option value="complaint">投诉</option>
                            </select>
                        </div>
                        <div class="form-group"><label>内容</label><textarea name="content" class="form-control" required></textarea></div>
                        <div class="form-group"><label>联系方式（选填）</label><input type="text" name="contact" class="form-control"></div>
                        <button type="submit" class="btn btn-primary">提交</button>
                    </form>
                </div>
                <div class="admin-card">
                    <h3 style="margin-bottom:16px">历史记录</h3>
                    <table class="admin-table">
                        <thead><tr><th>类型</th><th>内容</th><th>提交时间</th></tr></thead>
                        <tbody>
                            <?php
                            $list = $db->prepare('SELECT * FROM feedback WHERE user_id=:uid ORDER BY created_at DESC');
                            $list->execute([':uid'=>$user['id']]);
                            foreach ($list->fetchAll(PDO::FETCH_ASSOC) as $f):
                                $typeLabel = ['suggestion'=>'建议','report'=>'举报','complaint'=>'投诉','contact'=>'留言'][$f['type']] ?? $f['type'];
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
