<?php
$pageTitle = '轮播管理';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = $db->prepare('SELECT * FROM slides WHERE id=:id');
    $stmt->execute([':id'=>$_GET['edit']]);
    $edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (!empty($_GET['delete'])) {
    verify_csrf();
    $db->prepare('DELETE FROM slides WHERE id=:id')->execute([':id'=>$_GET['delete']]);
    flash('success', '已删除');
    redirect(YUYUN_URL . '/admin/slides.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $sort = intval($_POST['sort_order'] ?? 0);
    $active = isset($_POST['is_active']) ? 1 : 0;
    $image = $edit['image'] ?? '';
    if (!empty($_FILES['image']['tmp_name'])) {
        try { $image = upload_file($_FILES['image'], 'slides'); } catch (Exception $e) { flash('error', $e->getMessage()); }
    }
    if ($id) {
        $db->prepare('UPDATE slides SET title=:t, subtitle=:s, image=:i, link=:l, sort_order=:o, is_active=:a WHERE id=:id')->execute([':t'=>$title,':s'=>$subtitle,':i'=>$image,':l'=>$link,':o'=>$sort,':a'=>$active,':id'=>$id]);
    } else {
        $db->prepare('INSERT INTO slides (title, subtitle, image, link, sort_order, is_active) VALUES (:t,:s,:i,:l,:o,:a)')->execute([':t'=>$title,':s'=>$subtitle,':i'=>$image,':l'=>$link,':o'=>$sort,':a'=>$active]);
    }
    flash('success', '保存成功');
    redirect(YUYUN_URL . '/admin/slides.php');
}
$slides = $db->query('SELECT * FROM slides ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-card">
    <h3 style="margin-bottom:18px"><?php echo $edit?'编辑轮播':'添加轮播' ?></h3>
    <?php echo render_flash() ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
        <?php if ($edit): ?><input type="hidden" name="id" value="<?php echo $edit['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <div class="form-group"><label>标题</label><input type="text" name="title" class="form-control" value="<?php echo e($edit['title'] ?? '') ?>" required></div>
            <div class="form-group"><label>副标题</label><input type="text" name="subtitle" class="form-control" value="<?php echo e($edit['subtitle'] ?? '') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>链接</label><input type="text" name="link" class="form-control" value="<?php echo e($edit['link'] ?? '') ?>"></div>
            <div class="form-group"><label>排序</label><input type="number" name="sort_order" class="form-control" value="<?php echo e($edit['sort_order'] ?? '0') ?>"></div>
        </div>
        <div class="form-group"><label>背景图片（留空使用渐变）</label><input type="file" name="image" class="form-control" accept="image/*"> <?php if (!empty($edit['image'])): ?><p style="font-size:12px">当前：<?php echo e($edit['image']) ?></p><?php endif; ?></div>
        <div class="form-group"><label><input type="checkbox" name="is_active" <?php echo (!isset($edit) || !empty($edit['is_active']))?'checked':'' ?>> 启用</label></div>
        <button type="submit" class="btn btn-primary">保存</button>
        <?php if ($edit): ?><a href="<?php echo YUYUN_URL ?>/admin/slides.php" class="btn btn-outline">取消</a><?php endif; ?>
    </form>
</div>
<div class="admin-card">
    <h3 style="margin-bottom:18px">轮播列表</h3>
    <table class="admin-table">
        <thead><tr><th>标题</th><th>副标题</th><th>图片</th><th>排序</th><th>状态</th><th>操作</th></tr></thead>
        <tbody>
            <?php foreach ($slides as $s): ?>
            <tr>
                <td><?php echo e($s['title']) ?></td>
                <td><?php echo e($s['subtitle']) ?></td>
                <td><?php echo $s['image']?'<img src="'.YUYUN_URL.'/'.e($s['image']).'" style="height:40px">':'无' ?></td>
                <td><?php echo e($s['sort_order']) ?></td>
                <td><?php echo $s['is_active']?'启用':'禁用' ?></td>
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
