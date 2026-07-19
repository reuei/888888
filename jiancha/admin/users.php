<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) { redirect('login.php'); exit; }
$adminTitle = '用户管理';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;
$total = DB::fetchOne("SELECT COUNT(*) as c FROM users")['c'] ?? 0;
$users = DB::fetchAll("SELECT * FROM users ORDER BY id DESC LIMIT ? OFFSET ?", [$perPage, ($page - 1) * $perPage]);
include __DIR__ . '/header.php';
?>
<div class="admin-card">
<h4>用户列表</h4>
<table class="admin-table">
<thead><tr><th>ID</th><th>用户名</th><th>昵称</th><th>角色</th><th>注册时间</th></tr></thead>
<tbody>
<?php foreach ($users as $u): ?>
<tr><td><?php echo $u['id']; ?></td><td><?php echo e($u['username']); ?></td><td><?php echo e($u['nickname'] ?: '-'); ?></td><td><?php echo $u['role'] === 'admin' ? '管理员' : ($u['role'] === 'super_admin' ? '超级管理员' : '普通用户'); ?></td><td><?php echo formatDate($u['create_time']); ?></td></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php echo paginate($total, $page, $perPage, 'users.php'); ?>
</div>
<?php include __DIR__ . '/footer.php'; ?>