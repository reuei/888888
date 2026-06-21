<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';
require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';
requireAdminLogin();

$baseDir = YUYUN_ROOT . '/uploads';
$relative = $_GET['dir'] ?? '';
$relative = trim($relative, '/');
$relative = str_replace('..', '', $relative);
$currentDir = $baseDir . ($relative ? '/' . $relative : '');

if (!is_dir($currentDir)) {
    $currentDir = $baseDir;
    $relative = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('danger', '安全验证失败');
        header('Location: filemanager.php?dir=' . urlencode($relative));
        exit;
    }

    $action = $_POST['action'] ?? '';
    if ($action === 'upload' && !empty($_FILES['file']['tmp_name'])) {
        $sub = $_POST['subdir'] ?? '';
        $sub = trim(str_replace('..', '', $sub), '/');
        $targetDir = $baseDir . ($sub ? '/' . $sub : '');
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $up = yyUpload($_FILES['file'], $sub ?: 'files');
        if (empty($up['error'])) {
            setFlash('success', '文件已上传：' . $up['path']);
            addLog('上传文件', $up['path']);
        } else {
            setFlash('danger', $up['error']);
        }
    } elseif ($action === 'mkdir' && !empty($_POST['folder_name'])) {
        $folder = preg_replace('/[^a-zA-Z0-9_\-\x{4e00}-\x{9fa5}]/u', '', $_POST['folder_name']);
        $newDir = $currentDir . '/' . $folder;
        if ($folder && !is_dir($newDir)) {
            mkdir($newDir, 0755, true);
            setFlash('success', '目录已创建');
        } else {
            setFlash('warning', '目录名无效或已存在');
        }
    } elseif ($action === 'delete' && !empty($_POST['file'])) {
        $file = basename($_POST['file']);
        $path = $currentDir . '/' . $file;
        if (realpath($path) && strpos(realpath($path), realpath($baseDir)) === 0) {
            if (is_file($path)) {
                unlink($path);
                setFlash('success', '文件已删除');
                addLog('删除文件', $file);
            } elseif (is_dir($path)) {
                rmdir($path);
                setFlash('success', '目录已删除');
            }
        } else {
            setFlash('danger', '路径不合法');
        }
    }
    header('Location: filemanager.php?dir=' . urlencode($relative));
    exit;
}

$items = [];
if ($handle = opendir($currentDir)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry === '.' || $entry === '..') continue;
        $path = $currentDir . '/' . $entry;
        $items[] = [
            'name' => $entry,
            'is_dir' => is_dir($path),
            'size' => is_file($path) ? filesize($path) : 0,
            'mtime' => filemtime($path),
            'url' => '../uploads/' . ($relative ? $relative . '/' : '') . $entry,
        ];
    }
    closedir($handle);
}

usort($items, function($a, $b) {
    if ($a['is_dir'] !== $b['is_dir']) return $a['is_dir'] ? -1 : 1;
    return strcasecmp($a['name'], $b['name']);
});

function fmHumanSize($bytes) {
    $units = ['B','KB','MB','GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) { $bytes /= 1024; $i++; }
    return round($bytes, 2) . ' ' . $units[$i];
}

$pageTitle = '文件管理';
include __DIR__ . '/includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>文件管理</h2>
        <div>
            <a href="filemanager.php" class="btn btn-sm <?php echo $relative === '' ? 'btn-primary' : ''; ?>">根目录</a>
            <?php if ($relative): ?>
                <a href="filemanager.php?dir=<?php echo urlencode(dirname($relative) === '.' ? '' : dirname($relative)); ?>" class="btn btn-sm" style="background:#f0f0f0;">返回上级</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" style="margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <input type="hidden" name="action" value="upload">
            <input type="hidden" name="subdir" value="<?php echo yy_e($relative); ?>">
            <div class="form-group" style="flex:1;min-width:220px;">
                <label>上传文件到当前目录</label>
                <input type="file" name="file" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">上传</button>
        </form>

        <form method="post" style="margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <input type="hidden" name="action" value="mkdir">
            <div class="form-group" style="flex:1;min-width:220px;">
                <label>新建目录</label>
                <input type="text" name="folder_name" class="form-control" placeholder="目录名">
            </div>
            <button type="submit" class="btn btn-primary">创建</button>
        </form>

        <table class="table">
            <thead>
                <tr><th>名称</th><th>类型</th><th>大小</th><th>修改时间</th><th>操作</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td>
                            <?php if ($it['is_dir']): ?>
                                <i class="fa-solid fa-folder" style="color:#f59e0b;"></i>
                                <a href="filemanager.php?dir=<?php echo urlencode(($relative ? $relative . '/' : '') . $it['name']); ?>"><?php echo yy_e($it['name']); ?></a>
                            <?php else: ?>
                                <i class="fa-solid fa-file" style="color:#64748b;"></i>
                                <?php echo yy_e($it['name']); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $it['is_dir'] ? '目录' : '文件'; ?></td>
                        <td><?php echo $it['is_dir'] ? '-' : fmHumanSize($it['size']); ?></td>
                        <td><?php echo date('Y-m-d H:i', $it['mtime']); ?></td>
                        <td>
                            <?php if (!$it['is_dir']): ?>
                                <a href="<?php echo yy_e($it['url']); ?>" target="_blank" class="btn btn-sm btn-primary">查看</a>
                            <?php endif; ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="file" value="<?php echo yy_e($it['name']); ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('确定删除吗？')">删除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <tr><td colspan="5" style="text-align:center;color:#888;">当前目录为空</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
