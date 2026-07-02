<div class="breadcrumb">系统设置 / 邮件系统</div>
<div class="page-header">
    <h2>邮件系统</h2>
</div>

<div class="card" style="max-width: 720px;">
    <form id="settingForm">
        <input type="hidden" name="group" value="email">
        <div class="form-group">
            <label>SMTP 服务器</label>
            <input type="text" name="host" value="<?php echo h($config['email_host'] ?? ''); ?>" placeholder="如：smtp.qq.com">
        </div>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>端口</label>
                <input type="number" name="port" value="<?php echo h($config['email_port'] ?? '465'); ?>">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>加密方式</label>
                <select name="secure">
                    <option value="ssl" <?php echo ($config['email_secure'] ?? 'ssl') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                    <option value="tls" <?php echo ($config['email_secure'] ?? 'ssl') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                    <option value="" <?php echo ($config['email_secure'] ?? 'ssl') === '' ? 'selected' : ''; ?>>无</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>发件账号</label>
            <input type="text" name="user" value="<?php echo h($config['email_user'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>发件密码 / 授权码</label>
            <input type="password" name="pass" value="<?php echo h($config['email_pass'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>发件人名称</label>
            <input type="text" name="from" value="<?php echo h($config['email_from'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn" id="saveBtn">保存邮件设置</button>
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
        btn.textContent = '保存邮件设置';
    }
});
</script>
