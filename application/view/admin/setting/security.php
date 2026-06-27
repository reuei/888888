<div class="breadcrumb">系统设置 / 二次认证</div>
<div class="page-header">
    <h2>二次认证与安全</h2>
</div>

<div class="card" style="max-width: 720px;">
    <form id="settingForm">
        <input type="hidden" name="group" value="security">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label>总站后台二次认证</label>
                <select name="admin_2fa">
                    <option value="0" <?php echo ($config['security_admin_2fa'] ?? '0') === '0' ? 'selected' : ''; ?>>关闭</option>
                    <option value="1" <?php echo ($config['security_admin_2fa'] ?? '0') === '1' ? 'selected' : ''; ?>>开启</option>
                </select>
            </div>
            <div class="form-group">
                <label>商户后台二次认证</label>
                <select name="merchant_2fa">
                    <option value="0" <?php echo ($config['security_merchant_2fa'] ?? '0') === '0' ? 'selected' : ''; ?>>关闭</option>
                    <option value="1" <?php echo ($config['security_merchant_2fa'] ?? '0') === '1' ? 'selected' : ''; ?>>开启</option>
                </select>
            </div>
            <div class="form-group">
                <label>登录失败次数限制</label>
                <input type="number" name="login_fail_limit" min="0" value="<?php echo h($config['security_login_fail_limit'] ?? '5'); ?>">
            </div>
            <div class="form-group">
                <label>登录锁定分钟数</label>
                <input type="number" name="login_lock_minutes" min="0" value="<?php echo h($config['security_login_lock_minutes'] ?? '30'); ?>">
            </div>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存安全设置</button>
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
        btn.textContent = '保存安全设置';
    }
});
</script>
