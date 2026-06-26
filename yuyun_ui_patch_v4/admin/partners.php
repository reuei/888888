<?php
$pageTitle = '合作伙伴';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM partners WHERE id=:id');
    $stmt->execute([':id'=>$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (!empty($_GET['delete'])) {
    verify_csrf();
    $db->prepare('DELETE FROM partners WHERE id=:id')->execute([':id'=>$_GET['delete']]);
    flash('success', '已删除');
    redirect(YUYUN_URL . '/admin/partners.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $sort = intval($_POST['sort_order'] ?? 0);
    $active = isset($_POST['is_active']) ? 1 : 0;
    $logo = $edit['logo'] ?? '';
    if (!empty($_FILES['logo']['tmp_name'])) {
        try { $logo = upload_file($_FILES['logo'], 'partners'); } catch (Exception $e) { flash('error', $e->getMessage()); }
    }
    if ($id) {
        $db->prepare('UPDATE partners SET name=:n, logo=:l, link=:lk, sort_order=:o, is_active=:a WHERE id=:id')->execute([':n'=>$name,':l'=>$logo,':lk'=>$link,':o'=>$sort,':a'=>$active,':id'=>$id]);
    } else {
        $db->prepare('INSERT INTO partners (name, logo, link, sort_order, is_active) VALUES (:n,:l,:lk,:o,:a)')->execute([':n'=>$name,':l'=>$logo,':lk'=>$link,':o'=>$sort,':a'=>$active]);
    }
    flash('success', '保存成功');
    redirect(YUYUN_URL . '/admin/partners.php');
}
$partners = $db->query('SELECT * FROM partners ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-card">
    <h3 style="margin-bottom:18px"><?php echo $edit?'编辑合作伙伴':'添加合作伙伴' ?></h3>
    <?php echo render_flash() ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?php echo $edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <div class="form-group"><label>名称</label><input type="text" name="name" class="form-control" value="<?php echo e($edit['name'] ?? '') ?>" required></div>
            <div class="form-group"><label>官网链接</label><input type="text" name="link" class="form-control" value="<?php echo e($edit['link'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>LOGO</label><input type="file" name="logo" class="form-control" accept="image/*"> <?php if (!empty($edit['logo'])): ?><p style="font-size:12px">当前：<?php echo e($edit['logo']) ?></p><?php endif; ?></div>
            <div class="form-group"><label>排序</label><input type="number" name="sort_order" class="form-control" value="<?php echo e($edit['sort_order'] ?? '0') ?>"></div>
        </div>
        <div class="form-group"><label><input type="checkbox" name="is_active" <?php echo (!isset($edit) || !empty($edit['is_active']))?'checked':'' ?>> 启用</label></div>
        <button type="submit" class="btn btn-primary">保存</button>
        <?php if ($edit): ?><a href="<?php echo YUYUN_URL ?>/admin/partners.php" class="btn btn-outline">取消</a><?php endif; ?>
    </form>
</div>
<div class="admin-card">
    <h3 style="margin-bottom:18px">合作伙伴列表</h3>
    <table class="admin-table">
        <thead><tr><th>名称</th><th>LOGO</th><th>排序</th><th>状态</th><th>操作</th></tr></thead>
        <tbody>
            <?php foreach ($partners as $p): ?>
            <tr>
                <td><?php echo e($p['name']) ?></td>
                <td><?php echo $p['logo']?'<img src="'.YUYUN_URL.'/'.e($p['logo']).'" style="height:30px">':'无' ?></td>
                <td><?php echo e($p['sort_order']) ?></td>
                <td><?php echo $p['is_active']?'启用':'禁用' ?></td>
                <td>
                    <a href="?edit=<?php echo $p['id'] ?>" class="text-brand">编辑</a>
                    <a href="?delete=<?php echo $p['id'] ?>&csrf_token=<?php echo csrf_token() ?>" class="text-brand" style="margin-left:10px" onclick="return confirm('删除？')">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
