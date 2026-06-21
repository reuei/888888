<?php
$pageTitle = '产品管理';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM products WHERE id=:id');
    $stmt->execute([':id'=>$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (!empty($_GET['delete'])) {
    verify_csrf();
    $db->prepare('DELETE FROM products WHERE id=:id')->execute([':id'=>$_GET['delete']]);
    flash('success', '已删除');
    redirect(YUYUN_URL . '/admin/products.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $summary = trim($_POST['summary'] ?? '');
    $detail = trim($_POST['detail'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $sort = intval($_POST['sort_order'] ?? 0);
    $active = isset($_POST['is_active']) ? 1 : 0;
    $image = $edit['image'] ?? '';
    if (!empty($_FILES['image']['tmp_name'])) {
        try { $image = upload_file($_FILES['image'], 'products'); } catch (Exception $e) { flash('error', $e->getMessage()); }
    }
    if ($id) {
        $db->prepare('UPDATE products SET name=:n, summary=:s, detail=:d, icon=:i, image=:img, sort_order=:o, is_active=:a WHERE id=:id')->execute([':n'=>$name,':s'=>$summary,':d'=>$detail,':i'=>$icon,':img'=>$image,':o'=>$sort,':a'=>$active,':id'=>$id]);
    } else {
        $db->prepare('INSERT INTO products (name, summary, detail, icon, image, sort_order, is_active) VALUES (:n,:s,:d,:i,:img,:o,:a)')->execute([':n'=>$name,':s'=>$summary,':d'=>$detail,':i'=>$icon,':img'=>$image,':o'=>$sort,':a'=>$active]);
    }
    flash('success', '保存成功');
    redirect(YUYUN_URL . '/admin/products.php');
}
$products = $db->query('SELECT * FROM products ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-card">
    <h3 style="margin-bottom:18px"><?php echo $edit?'编辑产品':'添加产品' ?></h3>
    <?php echo render_flash() ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?php echo $edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <div class="form-group"><label>名称</label><input type="text" name="name" class="form-control" value="<?php echo e($edit['name'] ?? '') ?>" required></div>
            <div class="form-group"><label>图标（Font Awesome class）</label><input type="text" name="icon" class="form-control" value="<?php echo e($edit['icon'] ?? '') ?>" placeholder="fa-server"></div>
        </div>
        <div class="form-group"><label>一句话简介</label><input type="text" name="summary" class="form-control" value="<?php echo e($edit['summary'] ?? '') ?>"></div>
        <div class="form-group"><label>详情</label><textarea name="detail" class="form-control" rows="5"><?php echo e($edit['detail'] ?? '') ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label>封面图</label><input type="file" name="image" class="form-control" accept="image/*"> <?php if (!empty($edit['image'])): ?><p style="font-size:12px">当前：<?php echo e($edit['image']) ?></p><?php endif; ?></div>
            <div class="form-group"><label>排序</label><input type="number" name="sort_order" class="form-control" value="<?php echo e($edit['sort_order'] ?? '0') ?>"></div>
        </div>
        <div class="form-group"><label><input type="checkbox" name="is_active" <?php echo (!isset($edit) || !empty($edit['is_active']))?'checked':'' ?>> 启用</label></div>
        <button type="submit" class="btn btn-primary">保存</button>
        <?php if ($edit): ?><a href="<?php echo YUYUN_URL ?>/admin/products.php" class="btn btn-outline">取消</a><?php endif; ?>
    </form>
</div>
<div class="admin-card">
    <h3 style="margin-bottom:18px">产品列表</h3>
    <table class="admin-table">
        <thead><tr><th>名称</th><th>简介</th><th>排序</th><th>状态</th><th>操作</th></tr></thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?php echo e($p['name']) ?></td>
                <td><?php echo e($p['summary']) ?></td>
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
