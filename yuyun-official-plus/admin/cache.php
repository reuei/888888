<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';
require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';
requireAdminLogin();

function getDirSize($path) {
    $size = 0;
    if (!is_dir($path)) return 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file) {
        $size += $file->getSize();
    }
    return $size;
}

function humanSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('danger', '安全验证失败');
        header('Location: cache.php');
        exit;
    }
    $op = $_POST['op'] ?? '';
    if ($op === 'json_cache') {
        $files = glob(YUYUN_ROOT . '/data/tmp_*');
        $count = 0;
        foreach ($files as $f) {
            if (is_file($f)) { unlink($f); $count++; }
            elseif (is_dir($f)) { rrmdir($f); $count++; }
        }
        setFlash('success', "已清理 {$count} 个临时文件/目录");
    } elseif ($op === 'uploads_cache') {
        $files = glob(YUYUN_ROOT . '/uploads/.cache/*');
        $count = 0;
        foreach ($files as $f) {
            if (is_file($f)) { unlink($f); $count++; }
        }
        setFlash('success', "已清理 {$count} 个上传缓存文件");
    }
    addLog('清理缓存', '操作：' . $op);
    header('Location: cache.php');
    exit;
}

function rrmdir($dir) {
    if (!is_dir($dir)) return;
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? rrmdir($path) : unlink($path);
    }
    rmdir($dir);
}

$tmpSize = getDirSize(YUYUN_ROOT . '/data');
$uploadsSize = getDirSize(YUYUN_ROOT . '/uploads');

$pageTitle = '缓存清理';
include __DIR__ . '/includes/header.php';
?>

<div class="stats">
    <div class="stat-card">
        <h3><?php echo humanSize($tmpSize); ?></h3>
        <p>data 目录占用</p>
    </div>
    <div class="stat-card">
        <h3><?php echo humanSize($uploadsSize); ?></h3>
        <p>uploads 目录占用</p>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2>清理选项</h2></div>
    <div class="card-body">
        <form method="post" class="cache-form" style="display:flex;gap:16px;flex-wrap:wrap;">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <div style="flex:1;min-width:260px;">
                <h4>数据临时文件</h4>
                <p style="color:#666;font-size:13px;">清理 data 目录下的临时备份、压缩等文件，不影响正常数据。</p>
                <button type="submit" name="op" value="json_cache" class="btn btn-primary">立即清理</button>
            </div>
            <div style="flex:1;min-width:260px;">
                <h4>上传缓存</h4>
                <p style="color:#666;font-size:13px;">清理上传目录中的缩略图或临时文件。</p>
                <button type="submit" name="op" value="uploads_cache" class="btn btn-primary">立即清理</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
