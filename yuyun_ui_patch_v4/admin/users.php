<?php
$pageTitle = '用户管理';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$me = current_user();
if (!empty($_GET['toggle'])) {
    verify_csrf();
    $uid = intval($_GET['toggle']);
    if ($uid !== intval($me['id'])) {
        $db->prepare('UPDATE users SET is_admin = 1 - is_admin WHERE id=:id')->execute([':id'=>$uid]);
        flash('success', '权限已切换');
    } else {
        flash('error', '不能修改自己的管理员状态');
    }
    redirect(YUYUN_URL . '/admin/users.php');
}
if (!empty($_GET['delete'])) {
    verify_csrf();
    $uid = intval($_GET['delete']);
    if ($uid !== intval($me['id'])) {
        $db->prepare('DELETE FROM users WHERE id=:id')->execute([':id'=>$uid]);
        flash('success', '已删除');
    } else {
        flash('error', '不能删除自己');
    }
    redirect(YUYUN_URL . '/admin/users.php');
}
$users = $db->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-card">
    <h3 style="margin-bottom:18px">用户列表</h3>
    <?php echo render_flash() ?>
    <table class="admin-table">
        <thead><tr><th>ID</th><th>邮箱</th><th>昵称</th><th>手机</th><th>管理员</th><th>注册时间</th><th>操作</th></tr></thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo $u['id'] ?></td>
                <td><?php echo e($u['email']) ?></td>
                <td><?php echo e($u['nickname']) ?></td>
                <td><?php echo e($u['phone']) ?></td>
                <td><?php echo $u['is_admin']?'是':'否' ?></td>
                <td><?php echo e($u['created_at']) ?></td>
                <td>
                    <a href="?toggle=<?php echo $u['id'] ?>&csrf_token=<?php echo csrf_token() ?>" class="text-brand" onclick="return confirm('切换管理员权限？')"><?php echo $u['is_admin']?'取消管理员':'设为管理员' ?></a>
                    <a href="?delete=<?php echo $u['id'] ?>&csrf_token=<?php echo csrf_token() ?>" class="text-brand" style="margin-left:10px" onclick="return confirm('删除用户？')">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
