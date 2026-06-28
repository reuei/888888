<div class="breadcrumb">店铺设置 / 自定义支付</div>
<div class="page-header">
    <h2>自定义支付</h2>
</div>

<div class="card">
    <form id="paymentForm" enctype="multipart/form-data">
        <div class="form-group">
            <label>收款方式 <span style="color: #EF4444;">*</span></label>
            <select name="pay_type" id="payType" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <option value="0" <?php echo ($merchant['pay_type'] ?? 0) == 0 ? 'selected' : ''; ?>>平台代收（默认）</option>
                <option value="1" <?php echo ($merchant['pay_type'] ?? 0) == 1 ? 'selected' : ''; ?>>个人收款码</option>
                <option value="2" <?php echo ($merchant['pay_type'] ?? 0) == 2 ? 'selected' : ''; ?>>第三方接口</option>
            </select>
            <p class="hint">选择“平台代收”由平台统一结算；选择其他方式需自行配置收款渠道。</p>
        </div>

        <div id="personalPanel" style="display: <?php echo ($merchant['pay_type'] ?? 0) == 1 ? 'block' : 'none'; ?>;">
            <div class="form-group">
                <label>支付宝账号</label>
                <input type="text" name="pay_alipay_account" value="<?php echo h($merchant['pay_alipay_account'] ?? ''); ?>" placeholder="如：138****1234 或邮箱">
            </div>
            <div class="form-group">
                <label>支付宝收款二维码</label>
                <input type="file" name="pay_alipay_qr" accept="image/*">
                <?php if (!empty($merchant['pay_alipay_qr'])): ?>
                    <p class="hint">已上传：<a href="<?php echo base_url($merchant['pay_alipay_qr']); ?>" target="_blank">查看</a></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>微信账号</label>
                <input type="text" name="pay_wechat_account" value="<?php echo h($merchant['pay_wechat_account'] ?? ''); ?>" placeholder="如：微信号">
            </div>
            <div class="form-group">
                <label>微信收款二维码</label>
                <input type="file" name="pay_wechat_qr" accept="image/*">
                <?php if (!empty($merchant['pay_wechat_qr'])): ?>
                    <p class="hint">已上传：<a href="<?php echo base_url($merchant['pay_wechat_qr']); ?>" target="_blank">查看</a></p>
                <?php endif; ?>
            </div>
        </div>

        <div id="apiPanel" style="display: <?php echo ($merchant['pay_type'] ?? 0) == 2 ? 'block' : 'none'; ?>;">
            <div class="form-group">
                <label>接口地址 <span style="color: #EF4444;">*</span></label>
                <input type="url" name="pay_api_url" value="<?php echo h($merchant['pay_api_url'] ?? ''); ?>" placeholder="https://api.example.com/create">
            </div>
            <div class="form-group">
                <label>接口 KEY <span style="color: #EF4444;">*</span></label>
                <input type="text" name="pay_api_key" value="<?php echo h($merchant['pay_api_key'] ?? ''); ?>" placeholder="">
            </div>
            <div class="form-group">
                <label>接口 SECRET</label>
                <input type="text" name="pay_api_secret" value="<?php echo h($merchant['pay_api_secret'] ?? ''); ?>" placeholder="">
                <p class="hint">请确保第三方接口支持本系统对接文档。</p>
            </div>
        </div>

        <button type="submit" class="btn" id="saveBtn">保存配置</button>
    </form>
</div>

<script>
const payType = document.getElementById('payType');
const personalPanel = document.getElementById('personalPanel');
const apiPanel = document.getElementById('apiPanel');

function togglePanel() {
    const val = payType.value;
    personalPanel.style.display = val === '1' ? 'block' : 'none';
    apiPanel.style.display = val === '2' ? 'block' : 'none';
}

payType.addEventListener('change', togglePanel);

document.getElementById('paymentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '保存中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('merchant/setting/savePayment'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存配置';
    }
});
</script>
