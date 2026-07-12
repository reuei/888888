<?php
$activeMenu = 'settings';
$pageTitle = '系统设置';
include __DIR__ . '/header.php';

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => trim($_POST['site_name'] ?? ''),
        'site_title' => trim($_POST['site_title'] ?? ''),
        'site_keywords' => trim($_POST['site_keywords'] ?? ''),
        'site_description' => trim($_POST['site_description'] ?? ''),
        'footer_copyright' => trim($_POST['footer_copyright'] ?? ''),
        'icp' => trim($_POST['icp'] ?? ''),
        'contact_email' => trim($_POST['contact_email'] ?? ''),
    ];

    foreach ($settings as $key => $value) {
        setSetting($key, $value);
    }

    if (isset($_FILES['footer_image']) && $_FILES['footer_image']['error'] == UPLOAD_ERR_OK) {
        $result = uploadFile('footer_image');
        if ($result['success']) {
            setSetting('footer_image', $result['path']);
        }
    }

    if (isset($_POST['remove_footer_image'])) {
        setSetting('footer_image', '');
    }

    $msg = '设置保存成功';
    $msgType = 'success';
}

$settings = [];
$allSettings = DB::fetchAll("SELECT * FROM settings");
foreach ($allSettings as $s) {
    $settings[$s['key']] = $s['value'];
}
?>

<div class="admin-card">
    <h3>系统设置</h3>

    <?php if ($msg): ?>
    <div class="alert alert-<?php echo $msgType; ?>"><?php echo e($msg); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-item">
                <label>网站名称</label>
                <input type="text" name="site_name" value="<?php echo e($settings['site_name'] ?? SITE_NAME); ?>">
            </div>
            <div class="form-item">
                <label>网站标题</label>
                <input type="text" name="site_title" value="<?php echo e($settings['site_title'] ?? SITE_TITLE); ?>">
            </div>
        </div>

        <div class="form-item" style="margin-bottom:15px;">
            <label>网站关键词</label>
            <input type="text" name="site_keywords" value="<?php echo e($settings['site_keywords'] ?? SITE_KEYWORDS); ?>">
        </div>

        <div class="form-item" style="margin-bottom:15px;">
            <label>网站描述</label>
            <textarea name="site_description" rows="2"><?php echo e($settings['site_description'] ?? SITE_DESCRIPTION); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-item">
                <label>备案号</label>
                <input type="text" name="icp" value="<?php echo e($settings['icp'] ?? ''); ?>" placeholder="如：京ICP备12345678号">
            </div>
            <div class="form-item">
                <label>联系邮箱</label>
                <input type="email" name="contact_email" value="<?php echo e($settings['contact_email'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-item" style="margin-bottom:15px;">
            <label>页脚版权信息</label>
            <input type="text" name="footer_copyright" value="<?php echo e($settings['footer_copyright'] ?? ''); ?>">
        </div>

        <div class="form-item" style="margin-bottom:20px;">
            <label>页脚图片（二维码等）</label>
            <input type="file" name="footer_image" accept="image/*">
            <?php if (!empty($settings['footer_image'])): ?>
            <div style="margin-top:10px;">
                <img src="../<?php echo UPLOAD_URL . e($settings['footer_image']); ?>" style="max-width:150px; max-height:150px; border:1px solid #eee; border-radius:4px; padding:5px;">
                <label style="margin-left:15px; font-size:13px; color:#ff4d4f; cursor:pointer;">
                    <input type="checkbox" name="remove_footer_image" value="1" style="width:auto; margin-right:5px;"> 删除当前图片
                </label>
            </div>
            <?php endif; ?>
            <p style="font-size:12px; color:#999; margin-top:5px;">建议尺寸：200x200像素，用于页脚展示二维码等</p>
        </div>

        <button type="submit" class="btn btn-primary">保存设置</button>
    </form>
</div>

<div class="admin-card">
    <h3>数据库备份</h3>
    <p style="color:#666; margin-bottom:15px;">点击下方按钮可下载SQLite数据库文件进行备份。</p>
    <a href="?action=backup" class="btn btn-secondary" onclick="return confirm('确定下载数据库备份？');">下载数据库备份</a>
</div>

<?php
if (isset($_GET['action']) && $_GET['action'] == 'backup') {
    requireAdmin();
    $backupFile = DB_PATH;
    if (file_exists($backupFile)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="cms_backup_' . date('YmdHis') . '.db"');
        header('Content-Length: ' . filesize($backupFile));
        readfile($backupFile);
        exit;
    }
}
?>

<?php include __DIR__ . '/footer.php'; ?>
