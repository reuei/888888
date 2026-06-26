<?php
$pageTitle = '模板管理';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
if (!empty($_GET['activate'])) {
    verify_csrf();
    $folder = basename($_GET['activate']);
    if (is_dir(YUYUN_ROOT . '/templates/' . $folder)) {
        $db->prepare('UPDATE templates SET is_active=0')->execute();
        $db->prepare('UPDATE templates SET is_active=1 WHERE folder=:f')->execute([':f'=>$folder]);
        setSetting('template', $folder);
        flash('success', '模板已切换');
    } else {
        flash('error', '模板目录不存在');
    }
    redirect(YUYUN_URL . '/admin/templates.php');
}
if (!empty($_GET['delete'])) {
    verify_csrf();
    $folder = basename($_GET['delete']);
    if ($folder === 'default') {
        flash('error', '默认模板不能删除');
    } else {
        $db->prepare('DELETE FROM templates WHERE folder=:f')->execute([':f'=>$folder]);
        $dir = YUYUN_ROOT . '/templates/' . $folder;
        if (is_dir($dir)) array_map('unlink', glob($dir . '/*')) && rmdir($dir);
        flash('success', '模板已删除');
    }
    redirect(YUYUN_URL . '/admin/templates.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['zip'])) {
    verify_csrf();
    $file = $_FILES['zip'];
    if ($file['type'] !== 'application/zip' && substr($file['name'],-4)!=='.zip') {
        flash('error', '请上传 ZIP 模板包');
    } else {
        $name = pathinfo($file['name'], PATHINFO_FILENAME);
        $folder = slugify($name);
        $target = YUYUN_ROOT . '/templates/' . $folder;
        if (!is_dir($target)) mkdir($target, 0775, true);
        $zip = new ZipArchive();
        if ($zip->open($file['tmp_name']) === TRUE) {
            $zip->extractTo($target);
            $zip->close();
            $exists = $db->prepare('SELECT id FROM templates WHERE folder=:f');
            $exists->execute([':f'=>$folder]);
            if (!$exists->fetch()) {
                $db->prepare('INSERT INTO templates (name, folder, is_active) VALUES (:n,:f,0)')->execute([':n'=>$name,':f'=>$folder]);
            }
            flash('success', '模板上传成功');
        } else {
            flash('error', '解压失败');
        }
    }
    redirect(YUYUN_URL . '/admin/templates.php');
}
$templates = $db->query('SELECT * FROM templates ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-card">
    <h3 style="margin-bottom:18px">上传新模板</h3>
    <?php echo render_flash() ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
        <div class="form-group"><label>模板 ZIP 包（需包含 index.php 等文件）</label><input type="file" name="zip" class="form-control" accept=".zip" required></div>
        <button type="submit" class="btn btn-primary">上传</button>
    </form>
</div>
<div class="admin-card">
    <h3 style="margin-bottom:18px">模板列表</h3>
    <table class="admin-table">
        <thead><tr><th>名称</th><th>目录</th><th>状态</th><th>操作</th></tr></thead>
        <tbody>
            <?php foreach ($templates as $tp): ?>
            <tr>
                <td><?php echo e($tp['name']) ?></td>
                <td>templates/<?php echo e($tp['folder']) ?></td>
                <td><?php echo $tp['is_active']?'<span style="color:var(--brand);font-weight:700">当前启用</span>':'未启用' ?></td>
                <td>
                    <?php if (!$tp['is_active']): ?><a href="?activate=<?php echo e($tp['folder']) ?>&csrf_token=<?php echo csrf_token() ?>" class="text-brand">启用</a><?php endif; ?>
                    <?php if ($tp['folder'] !== 'default'): ?><a href="?delete=<?php echo e($tp['folder']) ?>&csrf_token=<?php echo csrf_token() ?>" class="text-brand" style="margin-left:10px" onclick="return confirm('删除模板？')">删除</a><?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
