<?php
$pageTitle = '员工卡片';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM staff WHERE id=:id');
    $stmt->execute([':id'=>$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (!empty($_GET['delete'])) {
    verify_csrf();
    $db->prepare('DELETE FROM staff WHERE id=:id')->execute([':id'=>$_GET['delete']]);
    flash('success', '已删除');
    redirect(YUYUN_URL . '/admin/staff.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $sort = intval($_POST['sort_order'] ?? 0);
    $avatar = $edit['avatar'] ?? '';
    if (!empty($_FILES['avatar']['tmp_name'])) {
        try { $avatar = upload_file($_FILES['avatar'], 'staff'); } catch (Exception $e) { flash('error', $e->getMessage()); }
    }
    if ($id) {
        $db->prepare('UPDATE staff SET name=:n, position=:p, avatar=:a, bio=:b, sort_order=:o WHERE id=:id')->execute([':n'=>$name,':p'=>$position,':a'=>$avatar,':b'=>$bio,':o'=>$sort,':id'=>$id]);
    } else {
        $db->prepare('INSERT INTO staff (name, position, avatar, bio, sort_order) VALUES (:n,:p,:a,:b,:o)')->execute([':n'=>$name,':p'=>$position,':a'=>$avatar,':b'=>$bio,':o'=>$sort]);
    }
    flash('success', '保存成功');
    redirect(YUYUN_URL . '/admin/staff.php');
}
$staff = $db->query('SELECT * FROM staff ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-card">
    <h3 style="margin-bottom:18px"><?php echo $edit?'编辑员工':'添加员工' ?></h3>
    <?php echo render_flash() ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?php echo $edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <div class="form-group"><label>姓名</label><input type="text" name="name" class="form-control" value="<?php echo e($edit['name'] ?? '') ?>" required></div>
            <div class="form-group"><label>职位</label><input type="text" name="position" class="form-control" value="<?php echo e($edit['position'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>头像</label><input type="file" name="avatar" class="form-control" accept="image/*"> <?php if (!empty($edit['avatar'])): ?><p style="font-size:12px">当前：<?php echo e($edit['avatar']) ?></p><?php endif; ?></div>
            <div class="form-group"><label>排序</label><input type="number" name="sort_order" class="form-control" value="<?php echo e($edit['sort_order'] ?? '0') ?>"></div>
        </div>
        <div class="form-group"><label>简介</label><textarea name="bio" class="form-control"><?php echo e($edit['bio'] ?? '') ?></textarea></div>
        <button type="submit" class="btn btn-primary">保存</button>
        <?php if ($edit): ?><a href="<?php echo YUYUN_URL ?>/admin/staff.php" class="btn btn-outline">取消</a><?php endif; ?>
    </form>
</div>
<div class="admin-card">
    <h3 style="margin-bottom:18px">员工列表</h3>
    <table class="admin-table">
        <thead><tr><th>姓名</th><th>职位</th><th>头像</th><th>排序</th><th>操作</th></tr></thead>
        <tbody>
            <?php foreach ($staff as $s): ?>
            <tr>
                <td><?php echo e($s['name']) ?></td>
                <td><?php echo e($s['position']) ?></td>
                <td><?php echo $s['avatar']?'<img src="'.YUYUN_URL.'/'.e($s['avatar']).'" style="height:40px;border-radius:50%">':'无' ?></td>
                <td><?php echo e($s['sort_order']) ?></td>
                <td>
                    <a href="?edit=<?php echo $s['id'] ?>" class="text-brand">编辑</a>
                    <a href="?delete=<?php echo $s['id'] ?>&csrf_token=<?php echo csrf_token() ?>" class="text-brand" style="margin-left:10px" onclick="return confirm('删除？')">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
