<?php
$pageTitle = '反馈管理';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$detailId = intval($_GET['id'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (isset($_POST['delete'])) {
        $fid = intval($_POST['feedback_id']);
        $db->prepare('DELETE FROM feedback WHERE id=:id')->execute([':id' => $fid]);
        flash('success', '已删除反馈');
        redirect(YUYUN_URL . '/admin/feedback.php');
    }
    if (isset($_POST['reply'])) {
        $fid = intval($_POST['feedback_id']);
        $reply = trim($_POST['content'] ?? '');
        if ($reply) {
            $db->prepare('UPDATE feedback SET reply=:r, replied_at=:t WHERE id=:id')->execute([':r' => $reply, ':t' => date('Y-m-d H:i:s'), ':id' => $fid]);
            $fb = $db->prepare('SELECT user_id FROM feedback WHERE id=:id');
            $fb->execute([':id' => $fid]);
            $row = $fb->fetch(PDO::FETCH_ASSOC);
            if ($row && $row['user_id']) {
                notify_user((int)$row['user_id'], '您的反馈已回复', $reply);
            }
            flash('success', '回复成功');
        }
        redirect(YUYUN_URL . '/admin/feedback.php?id=' . $fid);
    }
}
// 确保反馈表有 reply 列
try {
    $db->query('SELECT reply FROM feedback LIMIT 1');
} catch (PDOException $e) {
    $type = defined('DB_TYPE') && DB_TYPE === 'mysql' ? 'mysql' : 'sqlite';
    $def = $type === 'mysql' ? 'TEXT' : 'TEXT';
    try { $db->exec("ALTER TABLE feedback ADD COLUMN reply {$def}"); } catch (PDOException $e2) {}
}
try {
    $db->query('SELECT replied_at FROM feedback LIMIT 1');
} catch (PDOException $e) {
    $type = defined('DB_TYPE') && DB_TYPE === 'mysql' ? 'mysql' : 'sqlite';
    $def = $type === 'mysql' ? 'DATETIME NULL' : 'TEXT';
    try { $db->exec("ALTER TABLE feedback ADD COLUMN replied_at {$def}"); } catch (PDOException $e2) {}
}
?>
<div class="admin-card">
    <?php echo render_flash() ?>
    <?php if ($detailId): ?>
        <?php
        $s = $db->prepare('SELECT f.*, u.email, u.nickname FROM feedback f LEFT JOIN users u ON f.user_id=u.id WHERE f.id=:id LIMIT 1');
        $s->execute([':id' => $detailId]);
        $fb = $s->fetch(PDO::FETCH_ASSOC);
        if (!$fb): echo '<div class="alert alert-error">反馈不存在</div>';
        else:
            $typeLabel = ['suggestion'=>'产品建议','report'=>'违规举报','complaint'=>'投诉','contact'=>'留言'][$fb['type']] ?? $fb['type'];
        ?>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
            <h3 style="margin:0">反馈详情 #<?php echo $fb['id'] ?></h3>
            <span style="font-size:13px;color:var(--text-2)"><?php echo e($fb['created_at']) ?></span>
        </div>
        <p style="color:var(--text-2);margin-bottom:12px">用户：<?php echo e($fb['nickname'] ?: $fb['email'] ?: '游客') ?> | 类型：<span class="btn btn-sm btn-primary"><?php echo e($typeLabel) ?></span> | 联系方式：<?php echo e($fb['contact'] ?: '无') ?></p>
        <div style="padding:16px;background:#f5f7fa;border-radius:8px;margin-bottom:20px;white-space:pre-wrap;line-height:1.7"><?php echo nl2br(e($fb['content'])) ?></div>
        <?php if (!empty($fb['reply'])): ?>
        <div style="padding:16px;background:#fff7e6;border-radius:8px;border-left:3px solid var(--brand);margin-bottom:20px">
            <div style="font-weight:600;margin-bottom:6px;color:var(--brand)">官方回复 <span style="font-size:12px;color:var(--text-2);font-weight:400"><?php echo e($fb['replied_at']) ?></span></div>
            <div style="white-space:pre-wrap"><?php echo nl2br(e($fb['reply'])) ?></div>
        </div>
        <?php endif; ?>
        <form method="post" style="margin-bottom:16px">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <input type="hidden" name="feedback_id" value="<?php echo $fb['id'] ?>">
            <div class="form-group"><label>回复内容</label><textarea name="content" class="form-control" placeholder="输入回复内容（会发送站内通知给用户）" required></textarea></div>
            <button type="submit" name="reply" class="btn btn-primary"><i class="iconfont icon-send"></i> 回复</button>
        </form>
        <form method="post" onsubmit="return confirm('确定删除此反馈？')">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
            <input type="hidden" name="feedback_id" value="<?php echo $fb['id'] ?>">
            <button type="submit" name="delete" class="btn btn-outline"><i class="iconfont icon-trash"></i> 删除</button>
        </form>
        <p style="margin-top:20px"><a href="<?php echo YUYUN_URL ?>/admin/feedback.php" class="text-brand">&larr; 返回列表</a></p>
        <?php endif; ?>
    <?php else: ?>
        <h3 style="margin-bottom:18px">反馈列表</h3>
        <?php
        $list = $db->query('SELECT f.*, u.email, u.nickname FROM feedback f LEFT JOIN users u ON f.user_id=u.id ORDER BY f.created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
        if (empty($list)): echo '<p style="color:var(--text-2);text-align:center;padding:30px">暂无反馈</p>';
        else: ?>
        <table class="admin-table">
            <thead><tr><th>ID</th><th>用户</th><th>类型</th><th>内容</th><th>回复</th><th>时间</th><th>操作</th></tr></thead>
            <tbody>
                <?php foreach ($list as $f):
                    $typeLabel = ['suggestion'=>'建议','report'=>'举报','complaint'=>'投诉','contact'=>'留言'][$f['type']] ?? $f['type'];
                ?>
                <tr>
                    <td><?php echo $f['id'] ?></td>
                    <td><?php echo e($f['nickname'] ?: $f['email'] ?: '游客') ?></td>
                    <td><span class="btn btn-sm <?php echo $f['type']==='complaint'?'btn-dark':'btn-primary' ?>"><?php echo e($typeLabel) ?></span></td>
                    <td style="max-width:300px"><?php echo e(mb_substr($f['content'],0,50)) ?><?php echo mb_strlen($f['content'])>50?'...':'' ?></td>
                    <td><?php echo !empty($f['reply'])?'<span style="color:#52c41a">已回复</span>':'<span style="color:#999">未回复</span>' ?></td>
                    <td style="white-space:nowrap"><?php echo e($f['created_at']) ?></td>
                    <td><a href="?id=<?php echo $f['id'] ?>" class="text-brand">查看</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
