<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) { redirect('login.php'); exit; }
if (!isSuperAdmin()) { redirect('index.php'); exit; }
$adminTitle = '管理员管理';
$error = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf()) { $error = '非法请求'; }
    else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'admin';
        if (!$username || !$password) { $error = '请填写用户名和密码'; }
        elseif (strlen($password) < 6) { $error = '密码至少6位'; }
        elseif (DB::fetchOne("SELECT id FROM users WHERE username=?", [$username])) { $error = '用户名已存在'; }
        else {
            DB::insert('users', ['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT), 'nickname' => $username, 'role' => $role, 'create_time' => date('Y-m-d H:i:s')]);
            $ok = '管理员添加成功';
        }
    }
}
$admins = DB::fetchAll("SELECT * FROM users WHERE role IN ('admin','super_admin') ORDER BY id DESC");
include __DIR__ . '/header.php';
?>
<?php if ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
<div class="admin-card">
<h4>管理员列表</h4>
<div class="admin-table-wrap"><table class="admin-table">
<thead><tr><th>ID</th><th>用户名</th><th>角色</th><th>创建时间</th></tr></thead>
<tbody>
<?php foreach ($admins as $a): ?>
<tr><td><?php echo $a['id']; ?></td><td><?php echo e($a['username']); ?></td><td><?php echo $a['role'] === 'super_admin' ? '超级管理员' : '管理员'; ?></td><td><?php echo formatDate($a['create_time']); ?></td></tr>
<?php endforeach; ?>
</tbody>
</table></div>
</div>
<div class="admin-card">
<h4>添加管理员</h4>
<form method="post">
<?php echo csrfField(); ?>
<div class="admin-form admin-form-row">
<div class="admin-form"><label>用户名</label><input type="text" name="username" required></div>
<div class="admin-form"><label>密码</label><input type="password" name="password" required></div>
</div>
<div class="admin-form"><label>角色</label><select name="role"><option value="admin">管理员</option><option value="super_admin">超级管理员</option></select></div>
<div class="admin-form"><button type="submit" class="btn btn-primary">添加</button></div>
</form>
</div>
<?php include __DIR__ . '/footer.php'; ?>