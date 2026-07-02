<div class="breadcrumb">系统设置 / 站点基础</div>
<div class="page-header">
    <h2>站点基础设置</h2>
</div>

<div class="card" style="max-width: 720px;">
    <form id="settingForm">
        <input type="hidden" name="group" value="base">
        <div class="form-group">
            <label>站点名称</label>
            <input type="text" name="site_name" value="<?php echo h($config['base_site_name'] ?? '鲸商城 Pro'); ?>" required>
        </div>
        <div class="form-group">
            <label>站点 Logo URL</label>
            <input type="text" name="logo" value="<?php echo h($config['base_logo'] ?? ''); ?>" placeholder="留空显示文字 Logo">
        </div>
        <div class="form-group">
            <label>ICP 备案号</label>
            <input type="text" name="icp" value="<?php echo h($config['base_icp'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>客服联系方式</label>
            <input type="text" name="contact" value="<?php echo h($config['base_contact'] ?? ''); ?>" placeholder="QQ / 微信 / 电话">
        </div>
        <div class="form-group">
            <label>版权信息</label>
            <input type="text" name="copyright" value="<?php echo h($config['base_copyright'] ?? '鲸商城 Pro v1.0.0'); ?>">
        </div>
        <button type="submit" class="btn" id="saveBtn">保存设置</button>
    </form>
</div>

<script>
document.getElementById('settingForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '保存中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/setting/save'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存设置';
    }
});
</script>
