<?php
$pageTitle = '消息通知';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
ensure_notifications_table();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $target = $_POST['target'] ?? 'all';
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '' || $content === '') {
        flash('error', '标题和内容不能为空');
    } else {
        if ($target === 'all') {
            $users = $db->query('SELECT id FROM users')->fetchAll(PDO::FETCH_COLUMN);
            foreach ($users as $uid) {
                notify_user((int)$uid, $title, $content);
            }
            flash('success', '已向 ' . count($users) . ' 位用户发送消息');
        } else {
            $uid = intval($target);
            notify_user($uid, $title, $content);
            flash('success', '发送成功');
        }
    }
    redirect(YUYUN_URL . '/admin/notifications.php');
}

$userList = $db->query('SELECT id, email, nickname FROM users ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
$recent = $db->query('SELECT n.*, u.email FROM notifications n LEFT JOIN users u ON n.user_id=u.id ORDER BY n.id DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-card">
    <h3 style="margin-bottom:18px">发送站内消息</h3>
    <?php echo render_flash() ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
        <div class="form-row">
            <div class="form-group"><label>接收对象</label>
                <select name="target" class="form-control">
                    <option value="all">全部用户</option>
                    <?php foreach ($userList as $u): ?>
                    <option value="<?php echo $u['id'] ?>"><?php echo e($u['email']) ?>（<?php echo e($u['nickname']) ?>）</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>标题</label><input type="text" name="title" class="form-control" required placeholder="系统公告 / 活动通知"></div>
        </div>
        <div class="form-group"><label>内容</label><textarea name="content" class="form-control" required rows="4" placeholder="请输入消息内容"></textarea></div>
        <button type="submit" class="btn btn-primary"><i class="iconfont icon-send"></i> 发送</button>
    </form>
</div>
<div class="admin-card">
    <h3 style="margin-bottom:18px">最近发送记录</h3>
    <table class="admin-table">
        <thead><tr><th>用户</th><th>标题</th><th>内容</th><th>状态</th><th>时间</th></tr></thead>
        <tbody>
            <?php foreach ($recent as $n): ?>
            <tr>
                <td><?php echo e($n['email'] ?? '未知') ?></td>
                <td><?php echo e($n['title']) ?></td>
                <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo e($n['content']) ?></td>
                <td><?php echo $n['is_read'] ? '已读' : '未读' ?></td>
                <td><?php echo e($n['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
