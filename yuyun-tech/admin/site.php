<?php
/**
 * 网站设置
 */
$active_page = 'site';
require_once 'header.php';
$site = $site_data['site'] ?? [];
?>
<div class="admin-content">
    <div class="admin-card">
        <h2><i class="fas fa-cog" style="color:#1a73e8;"></i> 网站基本设置</h2>
        <form method="post" id="form_site" style="margin-top:20px;max-width:700px;">
            <input type="hidden" name="action" value="save_site">
            <div class="form-group">
                <label class="form-label">公司/网站名称 *</label>
                <input type="text" class="form-input" name="name" value="<?php echo htmlspecialchars($site['name'] ?? '语云科技'); ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">宣传语 / Slogan</label>
                <input type="text" class="form-input" name="slogan" value="<?php echo htmlspecialchars($site['slogan'] ?? '全球云服务专家'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Logo 图片 URL</label>
                <input type="text" class="form-input" name="logo" value="<?php echo htmlspecialchars($site['logo'] ?? ''); ?>" placeholder="留空使用字母Logo">
            </div>
            <div class="form-group">
                <label class="form-label">Favicon 图标 URL</label>
                <input type="text" class="form-input" name="favicon" value="<?php echo htmlspecialchars($site['favicon'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">主题风格</label>
                <select class="form-input" name="theme">
                    <option value="default" <?php echo ($site['theme'] ?? 'default')==='default'?'selected':''; ?>>默认（蓝色+橙色）</option>
                    <option value="dark" <?php echo ($site['theme'] ?? '')==='dark'?'selected':''; ?>>深色主题</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">版权信息</label>
                <input type="text" class="form-input" name="copyright" value="<?php echo htmlspecialchars($site['copyright'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存设置</button>
        </form>
    </div>
</div>

<script>
document.getElementById('form_site').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch('', { method: 'POST', body: fd })
        .then(r => r.json()).then(d => {
            if (d.code === 0) { showToast(d.msg || '保存成功', 'success'); }
            else { showToast(d.msg || '保存失败', 'danger'); }
        }).catch(() => showToast('请求失败', 'danger'));
});
</script>
</div>
</body>
</html>
