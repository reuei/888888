<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';
require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';
requireAdminLogin();

$type = DB_TYPE;
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
    $op = $_POST['op'] ?? '';
    if ($op === 'json_export') {
        if (!class_exists('ZipArchive')) {
            setFlash('danger', '当前 PHP 未启用 ZipArchive 扩展，无法在线打包备份');
            header('Location: backup.php');
            exit;
        }
        $jsonDir = YUYUN_ROOT . '/data/json';
        $zipFile = YUYUN_ROOT . '/data/backup_' . date('Ymd_His') . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE) === true) {
            foreach (glob($jsonDir . '/*.json') as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
            header('Content-Length: ' . filesize($zipFile));
            readfile($zipFile);
            unlink($zipFile);
            exit;
        } else {
            setFlash('danger', '无法创建备份压缩包，请检查 data 目录权限');
            header('Location: backup.php');
            exit;
        }
    } elseif ($op === 'json_import' && !empty($_FILES['backup']['tmp_name'])) {
        if (!class_exists('ZipArchive')) {
            setFlash('danger', '当前 PHP 未启用 ZipArchive 扩展，无法在线恢复备份');
            header('Location: backup.php');
            exit;
        }
        $up = $_FILES['backup'];
        $ext = strtolower(pathinfo($up['name'], PATHINFO_EXTENSION));
        if ($ext !== 'zip') {
            setFlash('danger', '请上传 zip 格式的备份文件');
        } else {
            $tmpDir = YUYUN_ROOT . '/data/tmp_backup_' . uniqid();
            mkdir($tmpDir, 0755, true);
            $zip = new ZipArchive();
            if ($zip->open($up['tmp_name']) === true) {
                $zip->extractTo($tmpDir);
                $zip->close();
                $jsonDir = YUYUN_ROOT . '/data/json';
                $count = 0;
                foreach (glob($tmpDir . '/*.json') as $file) {
                    $base = basename($file);
                    if (in_array($base, ['settings.json','slides.json','products.json','partners.json','links.json','certificates.json','messages.json','admins.json'])) {
                        copy($file, $jsonDir . '/' . $base);
                        $count++;
                    }
                }
                array_map('unlink', glob($tmpDir . '/*'));
                rmdir($tmpDir);
                setFlash('success', "已恢复 {$count} 个数据文件，请刷新页面查看");
            } else {
                setFlash('danger', '无法解压备份文件');
            }
        }
        header('Location: backup.php');
        exit;
    }
}

$pageTitle = '备份恢复';
include __DIR__ . '/includes/header.php';
?>

<div class="card">
    <div class="card-header"><h2>当前存储模式</h2></div>
    <div class="card-body">
        <p>当前数据库类型：<strong><?php echo yy_e($type); ?></strong></p>
        <?php if ($type === 'json'): ?>
            <p>所有数据以 JSON 文件形式保存在 <code>data/json/</code> 目录下，可直接下载备份或上传恢复。</p>
        <?php elseif ($type === 'sqlite'): ?>
            <p>数据保存在 SQLite 文件 <code><?php echo yy_e(defined('DB_NAME') ? DB_NAME : 'data/yuyun.db'); ?></code> 中，建议通过 FTP/面板定期下载该文件备份。</p>
        <?php else: ?>
            <p>当前使用 MySQL 数据库，请通过 phpMyAdmin 或 mysqldump 定期备份。</p>
        <?php endif; ?>
    </div>
</div>

<?php if ($type === 'json'): ?>
<div class="card">
    <div class="card-header"><h2>下载备份</h2></div>
    <div class="card-body">
        <p>点击按钮将所有 JSON 数据打包为 zip 下载。</p>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <input type="hidden" name="op" value="json_export">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-download"></i> 立即备份</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2>上传恢复</h2></div>
    <div class="card-body">
        <p>选择之前下载的 zip 备份文件进行恢复，恢复将覆盖现有数据，请谨慎操作。</p>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <input type="hidden" name="op" value="json_import">
            <div class="form-group">
                <input type="file" name="backup" class="form-control" accept=".zip" required>
            </div>
            <button type="submit" class="btn btn-primary" onclick="return confirm('确定要覆盖恢复数据吗？')"><i class="fa-solid fa-upload"></i> 上传恢复</button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
