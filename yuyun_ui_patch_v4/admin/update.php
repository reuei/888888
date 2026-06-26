<?php
$pageTitle = '代码更新';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$msg = '';
$err = '';
$tempDir = YUYUN_ROOT . '/update_temp';
$backupDir = YUYUN_ROOT . '/update_backup';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['patch'])) {
    verify_csrf();
    $file = $_FILES['patch'];
    if ($file['type'] !== 'application/zip' && substr($file['name'], -4) !== '.zip') {
        flash('error', '请上传 ZIP 补丁包');
    } else {
        if (!is_dir($tempDir)) mkdir($tempDir, 0775, true);
        if (!is_dir($backupDir)) mkdir($backupDir, 0775, true);
        $zip = new ZipArchive();
        if ($zip->open($file['tmp_name']) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS));
            $allowed = ['php','css','js','html','htm','sql','md','txt','jpg','jpeg','png','webp','gif','svg','ico','json','xml'];
            $applied = [];
            $backupName = 'backup_' . date('YmdHis') . '.zip';
            $backupZip = new ZipArchive();
            $backupZip->open($backupDir . '/' . $backupName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            foreach ($files as $f) {
                if ($f->isDir()) continue;
                $rel = str_replace($tempDir . '/', '', $f->getPathname());
                $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) continue;
                $target = YUYUN_ROOT . '/' . $rel;
                if (is_file($target)) {
                    $backupZip->addFile($target, $rel);
                }
                $dir = dirname($target);
                if (!is_dir($dir)) mkdir($dir, 0775, true);
                copy($f->getPathname(), $target);
                $applied[] = $rel;
            }
            $backupZip->close();
            // clean temp
            array_map('unlink', glob($tempDir . '/*'));
            foreach (glob($tempDir . '/*', GLOB_ONLYDIR) as $d) {
                array_map('unlink', glob($d . '/*'));
                rmdir($d);
            }
            $db->prepare('INSERT INTO updates (version, files, backup, applied_at) VALUES (:v, :f, :b, :t)')
               ->execute([':v'=>date('YmdHis'), ':f'=>implode("\n", $applied), ':b'=>$backupName, ':t'=>date('Y-m-d H:i:s')]);
            flash('success', '补丁已应用，共更新 ' . count($applied) . ' 个文件，备份：' . $backupName);
        } else {
            flash('error', '解压失败');
        }
    }
    redirect(YUYUN_URL . '/admin/update.php');
}

$history = $db->query('SELECT * FROM updates ORDER BY applied_at DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="admin-card">
    <h3 style="margin-bottom:18px">上传并应用补丁</h3>
    <?php echo render_flash() ?>
    <p style="color:var(--text-2);font-size:14px;margin-bottom:16px">补丁包为 ZIP 格式，内部目录结构需与网站根目录一致。应用前系统会自动备份将被覆盖的文件。</p>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
        <div class="form-group"><label>补丁 ZIP 包</label><input type="file" name="patch" class="form-control" accept=".zip" required></div>
        <button type="submit" class="btn btn-primary">应用补丁</button>
    </form>
</div>
<div class="admin-card">
    <h3 style="margin-bottom:18px">更新记录</h3>
    <table class="admin-table">
        <thead><tr><th>版本/时间</th><th>备份文件</th><th>更新文件数</th><th>应用时间</th></tr></thead>
        <tbody>
            <?php foreach ($history as $u): ?>
            <tr>
                <td><?php echo e($u['version']) ?></td>
                <td><?php echo e($u['backup']) ?></td>
                <td><?php echo count(array_filter(explode("\n", $u['files']))) ?></td>
                <td><?php echo e($u['applied_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
