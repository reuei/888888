<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) { redirect('login.php'); exit; }
$adminTitle = '留言管理';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;
if (isset($_GET['delete'])) {
    DB::delete('messages', 'id=?', [intval($_GET['delete'])]);
    redirect('messages.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply'])) {
    $mid = intval($_POST['id'] ?? 0);
    DB::update('messages', ['reply' => $_POST['reply'], 'status' => 1], 'id=?', [$mid]);
}
$total = DB::fetchOne("SELECT COUNT(*) as c FROM messages")['c'] ?? 0;
$msgs = DB::fetchAll("SELECT * FROM messages ORDER BY id DESC LIMIT ? OFFSET ?", [$perPage, ($page - 1) * $perPage]);
include __DIR__ . '/header.php';
?>
<div class="admin-card">
<h4>留言列表</h4>
<table class="admin-table">
<thead><tr><th>ID</th><th>标题</th><th>姓名</th><th>时间</th><th>状态</th><th>操作</th></tr></thead>
<tbody>
<?php foreach ($msgs as $m): ?>
<tr>
<td><?php echo $m['id']; ?></td>
<td><?php echo e(truncateStr($m['title'], 30)); ?></td>
<td><?php echo e($m['name'] ?: '-'); ?></td>
<td><?php echo formatDate($m['create_time']); ?></td>
<td><?php echo $m['status'] ? '<span style="color:var(--green)">已回复</span>' : '<span style="color:var(--red)">未处理</span>'; ?></td>
<td><a href="?delete=<?php echo $m['id']; ?>" onclick="return confirm('确定删除？')" class="btn btn-sm btn-danger">删除</a></td>
</tr>
<tr><td colspan="6" style="background:#f8fafc;font-size:12px;padding:8px 16px">
<strong>内容：</strong><?php echo e($m['content']); ?><?php if ($m['phone']): ?> <strong>电话：</strong><?php echo e($m['phone']); ?><?php endif; ?>
<?php if ($m['reply']): ?><br><strong style="color:var(--green)">回复：</strong><?php echo e($m['reply']); ?><?php endif; ?>
<details style="margin-top:4px"><summary>回复</summary>
<form method="post" style="margin-top:8px"><input type="hidden" name="id" value="<?php echo $m['id']; ?>"><textarea name="reply" rows="3" style="width:100%;padding:8px;border:1px solid var(--border);border-radius:6px;font-size:13px"><?php echo e($m['reply'] ?? ''); ?></textarea><button type="submit" class="btn btn-primary btn-sm" style="margin-top:6px">保存回复</button></form>
</details>
</td></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php echo paginate($total, $page, $perPage, 'messages.php'); ?>
</div>
<?php include __DIR__ . '/footer.php'; ?>