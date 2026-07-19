<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) { redirect('login.php'); exit; }
$adminTitle = '系统设置';
$ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf()) { $ok = '非法请求'; }
    else {
        foreach ($_POST as $key => $value) {
            if (in_array($key, ['csrf_token', 'action'])) continue;
            $val = trim($value);
            $exists = DB::fetchOne("SELECT id FROM settings WHERE key=?", [$key]);
            if ($exists) { DB::update('settings', ['value' => $val], 'key=?', [$key]); }
            else { DB::insert('settings', ['key' => $key, 'value' => $val]); }
        }
        $upload = uploadFile('footer_image');
        if ($upload['success']) {
            $exists = DB::fetchOne("SELECT id FROM settings WHERE key='footer_image'");
            if ($exists) { DB::update('settings', ['value' => $upload['path']], 'key=?', ['footer_image']); }
            else { DB::insert('settings', ['key' => 'footer_image', 'value' => $upload['path']]); }
        }
        $ok = '设置已保存';
    }
}
include __DIR__ . '/header.php';
?>
<?php if ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>
<div class="admin-card">
<h4>基本设置</h4>
<form method="post" enctype="multipart/form-data">
<?php echo csrfField(); ?>
<div class="admin-form"><label>网站名称</label><input type="text" name="site_name" value="<?php echo e(getSetting('site_name', '人民检察')); ?>"></div>
<div class="admin-form"><label>版权信息</label><input type="text" name="footer_copyright" value="<?php echo e(getSetting('footer_copyright')); ?>"></div>
<div class="admin-form"><label>ICP备案号</label><input type="text" name="icp" value="<?php echo e(getSetting('icp')); ?>"></div>
<div class="admin-form"><label>页脚二维码</label><input type="file" name="footer_image" accept="image/*"></div>
<?php $fi = getSetting('footer_image'); if ($fi): ?><div style="margin-bottom:16px"><img src="<?php echo SITE_URL . UPLOAD_URL . e($fi); ?>" style="width:90px;height:90px;border-radius:6px;object-fit:cover"></div><?php endif; ?>
<div class="admin-form"><button type="submit" class="btn btn-primary">保存设置</button></div>
</form>
</div>
<?php include __DIR__ . '/footer.php'; ?>