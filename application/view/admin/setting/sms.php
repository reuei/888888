<div class="breadcrumb">系统设置 / 短信通知</div>
<div class="page-header">
    <h2>短信通知</h2>
</div>

<div class="card" style="max-width: 720px;">
    <form id="settingForm">
        <input type="hidden" name="group" value="sms">
        <div class="form-group">
            <label>短信网关</label>
            <select name="gateway">
                <option value="" <?php echo ($config['sms_gateway'] ?? '') === '' ? 'selected' : ''; ?>>未启用</option>
                <option value="aliyun" <?php echo ($config['sms_gateway'] ?? '') === 'aliyun' ? 'selected' : ''; ?>>阿里云短信</option>
                <option value="tencent" <?php echo ($config['sms_gateway'] ?? '') === 'tencent' ? 'selected' : ''; ?>>腾讯云短信</option>
            </select>
        </div>
        <div class="form-group">
            <label>App ID / AccessKey ID</label>
            <input type="text" name="app_id" value="<?php echo h($config['sms_app_id'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>App Key / AccessKey Secret</label>
            <input type="password" name="app_key" value="<?php echo h($config['sms_app_key'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>短信签名</label>
            <input type="text" name="sign" value="<?php echo h($config['sms_sign'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn" id="saveBtn">保存短信设置</button>
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
        btn.textContent = '保存短信设置';
    }
});
</script>
