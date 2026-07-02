<div class="breadcrumb">店铺设置 / 子域名</div>
<div class="page-header">
    <h2>子域名配置</h2>
</div>

<div class="card">
    <div style="margin-bottom: 20px;">
        <span>当前状态：</span>
        <span class="tag <?php echo $merchant['domain_status_class']; ?>"><?php echo h($merchant['domain_status_text']); ?></span>
        <?php if ($merchant['domain_status'] == 1 && $merchant['domain_prefix']): ?>
            <p style="margin-top: 12px; font-size: 16px;">
                访问地址：<a href="<?php echo base_url('/?subsite=' . urlencode($merchant['domain_prefix'])); ?>" target="_blank">
                    <?php echo h($merchant['domain_prefix'] . '.' . ($_SERVER['HTTP_HOST'] ?? 'example.com')); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>

    <?php if ($merchant['domain_status'] == 1): ?>
        <div class="card" style="background: #F0FDF4;">
            <p>子域名已启用，如需更换请先联系平台管理员解绑。</p>
        </div>
    <?php else: ?>
        <form id="domainForm">
            <div class="form-group">
                <label>子域名前缀 <span style="color: #EF4444;">*</span></label>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <input type="text" name="domain_prefix" value="<?php echo h($merchant['domain_prefix'] ?? ''); ?>" placeholder="如：shop123" style="flex: 1;" maxlength="30">
                    <span style="color: #64748B; white-space: nowrap;">.<?php echo h($_SERVER['HTTP_HOST'] ?? 'example.com'); ?></span>
                </div>
                <p class="hint">2-30位，仅支持小写字母、数字和连字符，不能以下划线开头或结尾。</p>
            </div>
            <button type="submit" class="btn" id="saveBtn" <?php echo $merchant['domain_status'] == 2 ? 'disabled' : ''; ?>>
                <?php echo $merchant['domain_status'] == 2 ? '审核中' : '申请子域名'; ?>
            </button>
        </form>
    <?php endif; ?>
</div>

<?php if ($merchant['domain_status'] != 1): ?>
<script>
document.getElementById('domainForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '提交中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('merchant/setting/saveDomain'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '申请子域名';
    }
});
</script>
<?php endif; ?>
