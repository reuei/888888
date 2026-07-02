<div class="breadcrumb">系统设置 / 文件存储</div>
<div class="page-header">
    <h2>文件存储</h2>
</div>

<div class="card" style="max-width: 720px;">
    <form id="settingForm">
        <input type="hidden" name="group" value="storage">
        <div class="form-group">
            <label>存储方式</label>
            <select name="type">
                <option value="local" <?php echo ($config['storage_type'] ?? 'local') === 'local' ? 'selected' : ''; ?>>本地存储</option>
                <option value="oss" <?php echo ($config['storage_type'] ?? 'local') === 'oss' ? 'selected' : ''; ?>>阿里云 OSS</option>
                <option value="cos" <?php echo ($config['storage_type'] ?? 'local') === 'cos' ? 'selected' : ''; ?>>腾讯云 COS</option>
                <option value="qiniu" <?php echo ($config['storage_type'] ?? 'local') === 'qiniu' ? 'selected' : ''; ?>>七牛云</option>
            </select>
        </div>
        <div class="form-group">
            <label>访问域名</label>
            <input type="text" name="domain" value="<?php echo h($config['storage_domain'] ?? ''); ?>" placeholder="如：https://cdn.example.com">
        </div>
        <div class="form-group">
            <label>Bucket / 空间名</label>
            <input type="text" name="bucket" value="<?php echo h($config['storage_bucket'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>AccessKey</label>
            <input type="text" name="ak" value="<?php echo h($config['storage_ak'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>SecretKey</label>
            <input type="password" name="sk" value="<?php echo h($config['storage_sk'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn" id="saveBtn">保存存储设置</button>
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
        btn.textContent = '保存存储设置';
    }
});
</script>
