<?php
$pageTitle = '消息通知';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $target = $_POST['target'] ?? 'all';
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '') {
        flash('error', '请输入消息标题');
    } else {
        if ($target === 'all') {
            $users = $db->query('SELECT id FROM users ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as $u) {
                notify_user($u['id'], $title, $content);
            }
        } else {
            $uid = intval($target);
            if ($uid > 0) notify_user($uid, $title, $content);
        }
        flash('success', '消息已发送');
        redirect(YUYUN_URL . '/admin/messages.php');
    }
}
$users = $db->query('SELECT id, email, nickname FROM users ORDER BY id DESC LIMIT 200')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-card">
    <h3 style="margin-bottom:18px">发送站内消息</h3>
    <?php echo render_flash() ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
        <div class="form-row">
            <div class="form-group">
                <label>发送对象</label>
                <select name="target" class="form-control">
                    <option value="all">全部用户</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?php echo $u['id'] ?>"><?php echo e($u['email'] . ($u['nickname'] ? ' (' . $u['nickname'] . ')' : '')) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>消息标题</label><input type="text" name="title" class="form-control" required></div>
        </div>
        <div class="form-group"><label>消息内容</label><textarea name="content" class="form-control" rows="4"></textarea></div>
        <button type="submit" class="btn btn-primary"><i class="iconfont icon-send"></i> 发送消息</button>
    </form>
</div>
<div class="admin-card">
    <h3 style="margin-bottom:18px">提示</h3>
    <p style="color:var(--text-2)">发送后，用户将在用户中心的「消息通知」中查看。选择「全部用户」将逐个写入通知记录。</p>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>