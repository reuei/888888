<div class="breadcrumb">支付网关 / 风控策略</div>
<div class="page-header">
    <h2>风控策略</h2>
    <a href="<?php echo url('admin/payment/channel'); ?>" class="btn btn-outline">渠道配置</a>
</div>

<div class="card" style="max-width: 720px;">
    <form id="riskForm">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label>是否开启金额随机化</label>
                <select name="amount_jitter">
                    <option value="0" <?php echo ($risk['amount_jitter'] ?? '0') === '0' ? 'selected' : ''; ?>>关闭</option>
                    <option value="1" <?php echo ($risk['amount_jitter'] ?? '0') === '1' ? 'selected' : ''; ?>>开启</option>
                </select>
            </div>
            <div class="form-group">
                <label>随机化范围（0-1）</label>
                <input type="number" name="jitter_range" step="0.001" min="0" max="1" value="<?php echo h($risk['jitter_range'] ?? '0.01'); ?>">
            </div>
            <div class="form-group">
                <label>单笔最小金额</label>
                <input type="number" name="min_amount" step="0.01" min="0" value="<?php echo h($risk['min_amount'] ?? '0.01'); ?>">
            </div>
            <div class="form-group">
                <label>单笔最大金额</label>
                <input type="number" name="max_amount" step="0.01" min="0" value="<?php echo h($risk['max_amount'] ?? '50000.00'); ?>">
            </div>
            <div class="form-group">
                <label>是否限制同IP下单</label>
                <select name="ip_limit">
                    <option value="0" <?php echo ($risk['ip_limit'] ?? '0') === '0' ? 'selected' : ''; ?>>关闭</option>
                    <option value="1" <?php echo ($risk['ip_limit'] ?? '0') === '1' ? 'selected' : ''; ?>>开启</option>
                </select>
            </div>
            <div class="form-group">
                <label>同IP限购次数（0为不限）</label>
                <input type="number" name="ip_limit_count" min="0" value="<?php echo h($risk['ip_limit_count'] ?? '10'); ?>">
            </div>
            <div class="form-group">
                <label>是否强制填写联系方式</label>
                <select name="contact_required">
                    <option value="0" <?php echo ($risk['contact_required'] ?? '1') === '0' ? 'selected' : ''; ?>>否</option>
                    <option value="1" <?php echo ($risk['contact_required'] ?? '1') === '1' ? 'selected' : ''; ?>>是</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>黑名单关键词（逗号分隔）</label>
            <textarea name="blacklist_words" rows="3"><?php echo h($risk['blacklist_words'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存风控策略</button>
    </form>
</div>

<script>
document.getElementById('riskForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '保存中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/payment/riskSave'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存风控策略';
    }
});
</script>
