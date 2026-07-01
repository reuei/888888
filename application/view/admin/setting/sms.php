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
        <hr style="border: none; border-top: 1px solid #E2E8F0; margin: 20px 0;">
        <div style="font-weight: 500; margin-bottom: 12px;">自定义 HTTP 网关（可选）</div>
        <div class="form-group">
            <label>接口地址</label>
            <input type="text" name="api_url" value="<?php echo h($config['sms_api_url'] ?? ''); ?>" placeholder="如：http://api.example.com/send">
        </div>
        <div class="form-group">
            <label>请求方式</label>
            <select name="api_method">
                <option value="POST" <?php echo ($config['sms_api_method'] ?? 'POST') === 'POST' ? 'selected' : ''; ?>>POST</option>
                <option value="GET" <?php echo ($config['sms_api_method'] ?? 'POST') === 'GET' ? 'selected' : ''; ?>>GET</option>
            </select>
        </div>
        <div class="form-group">
            <label>固定参数</label>
            <textarea name="api_params" rows="3" placeholder="如：appid=123&format=json"><?php echo h($config['sms_api_params'] ?? ''); ?></textarea>
            <div style="font-size: 12px; color: #64748B; margin-top: 4px;">系统会自动附加 mobile、content、sign、app_id、app_key</div>
        </div>
        <div class="form-group">
            <label>调试模式</label>
            <select name="debug">
                <option value="0" <?php echo ($config['sms_debug'] ?? '0') === '0' ? 'selected' : ''; ?>>关闭（真实发送）</option>
                <option value="1" <?php echo ($config['sms_debug'] ?? '0') === '1' ? 'selected' : ''; ?>>开启（仅记录不发短信）</option>
            </select>
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
