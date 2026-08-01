<?php
$pageTitle = '站点设置';
require_once __DIR__ . '/header.php';

$settings = DB::getSetting();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_settings') {
    $data = [
        'site_name' => trim($_POST['site_name'] ?? ''),
        'site_title' => trim($_POST['site_title'] ?? ''),
        'site_description' => trim($_POST['site_description'] ?? ''),
        'site_keywords' => trim($_POST['site_keywords'] ?? ''),
        'logo' => trim($_POST['logo'] ?? ''),
        'favicon' => trim($_POST['favicon'] ?? ''),
        'copyright' => trim($_POST['copyright'] ?? ''),
        'icp' => trim($_POST['icp'] ?? ''),
        'contact_phone' => trim($_POST['contact_phone'] ?? ''),
        'contact_email' => trim($_POST['contact_email'] ?? ''),
        'contact_address' => trim($_POST['contact_address'] ?? ''),
        'about_title' => trim($_POST['about_title'] ?? ''),
        'about_content' => trim($_POST['about_content'] ?? ''),
        'home_title' => trim($_POST['home_title'] ?? ''),
        'home_subtitle' => trim($_POST['home_subtitle'] ?? ''),
    ];

    $homeStats = [];
    $nums = $_POST['stat_num'] ?? [];
    $labels = $_POST['stat_label'] ?? [];
    if (is_array($nums)) {
        foreach ($nums as $idx => $num) {
            $num = trim($num);
            $label = trim($labels[$idx] ?? '');
            if ($num !== '' || $label !== '') {
                $homeStats[] = ['num' => $num, 'label' => $label];
            }
        }
    }
    $data['home_stats'] = $homeStats;

    if (DB::updateSettings($data)) {
        $message = '设置已保存成功！';
        $messageType = 'success';
        $settings = DB::getSetting();
    } else {
        $message = '保存失败，请重试';
        $messageType = 'error';
    }
}
?>
<div class="admin-breadcrumbs">
    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">首页</a>
    <span class="sep">/</span>
    <span class="current">站点设置</span>
</div>

<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title">站点设置</h2>
        <div class="admin-page-subtitle">管理站点的基本信息和配置</div>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?>">
    <span class="alert-icon"><?php echo $messageType === 'success' ? '✓' : '⚠'; ?></span>
    <?php echo h($message); ?>
</div>
<?php endif; ?>

<form method="POST" action="">
    <input type="hidden" name="action" value="save_settings">

    <div class="admin-form">
        <div class="admin-form-header">
            <h2>基本信息</h2>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">站点名称 <span class="required">*</span></label>
                <input type="text" name="site_name" class="form-control" value="<?php echo h($settings['site_name']); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">站点标题 <span class="required">*</span></label>
                <input type="text" name="site_title" class="form-control" value="<?php echo h($settings['site_title']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">站点描述</label>
            <textarea name="site_description" class="form-control" rows="3"><?php echo h($settings['site_description']); ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">关键词</label>
            <input type="text" name="site_keywords" class="form-control" value="<?php echo h($settings['site_keywords']); ?>" placeholder="用逗号分隔">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Logo 路径</label>
                <input type="text" name="logo" class="form-control" value="<?php echo h($settings['logo']); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Favicon 路径</label>
                <input type="text" name="favicon" class="form-control" value="<?php echo h($settings['favicon']); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">版权信息</label>
                <input type="text" name="copyright" class="form-control" value="<?php echo h($settings['copyright']); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">ICP备案号</label>
                <input type="text" name="icp" class="form-control" value="<?php echo h($settings['icp']); ?>">
            </div>
        </div>
    </div>

    <div class="admin-form" style="margin-top:1.5rem;">
        <div class="admin-form-header">
            <h2>联系信息</h2>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">联系电话</label>
                <input type="text" name="contact_phone" class="form-control" value="<?php echo h($settings['contact_phone']); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">联系邮箱</label>
                <input type="email" name="contact_email" class="form-control" value="<?php echo h($settings['contact_email']); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">联系地址</label>
            <input type="text" name="contact_address" class="form-control" value="<?php echo h($settings['contact_address']); ?>">
        </div>
    </div>

    <div class="admin-form" style="margin-top:1.5rem;">
        <div class="admin-form-header">
            <h2>首页与关于</h2>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">首页主标题</label>
                <input type="text" name="home_title" class="form-control" value="<?php echo h($settings['home_title']); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">首页副标题</label>
                <input type="text" name="home_subtitle" class="form-control" value="<?php echo h($settings['home_subtitle']); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">关于页面标题</label>
                <input type="text" name="about_title" class="form-control" value="<?php echo h($settings['about_title']); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">关于页面内容</label>
            <textarea name="about_content" class="form-control" rows="6"><?php echo h($settings['about_content']); ?></textarea>
        </div>
    </div>

    <div class="admin-form" style="margin-top:1.5rem;">
        <div class="admin-form-header">
            <h2>首页统计数据</h2>
        </div>

        <div id="statsContainer">
            <?php
            $stats = $settings['home_stats'] ?? [];
            $statIndex = 0;
            foreach ($stats as $stat):
                $statIndex++;
            ?>
            <div class="stat-row" style="display:grid;grid-template-columns:1fr 2fr auto;gap:0.75rem;margin-bottom:0.75rem;align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">数值</label>
                    <input type="text" name="stat_num[]" class="form-control" value="<?php echo h($stat['num']); ?>">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">标签</label>
                    <input type="text" name="stat_label[]" class="form-control" value="<?php echo h($stat['label']); ?>">
                </div>
                <button type="button" class="btn btn-sm btn-outline" onclick="this.closest('.stat-row').remove()" style="margin-bottom:0;">删除</button>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline" onclick="addStatRow()">+ 添加统计项</button>
    </div>

    <div class="admin-form-actions">
        <button type="submit" class="btn btn-primary">保存设置</button>
        <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-outline">取消</a>
    </div>
</form>

<script>
function addStatRow() {
    const container = document.getElementById('statsContainer');
    const row = document.createElement('div');
    row.className = 'stat-row';
    row.style.cssText = 'display:grid;grid-template-columns:1fr 2fr auto;gap:0.75rem;margin-bottom:0.75rem;align-items:end;';
    row.innerHTML = `
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">数值</label>
            <input type="text" name="stat_num[]" class="form-control" value="">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">标签</label>
            <input type="text" name="stat_label[]" class="form-control" value="">
        </div>
        <button type="button" class="btn btn-sm btn-outline" onclick="this.closest('.stat-row').remove()" style="margin-bottom:0;">删除</button>
    `;
    container.appendChild(row);
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>