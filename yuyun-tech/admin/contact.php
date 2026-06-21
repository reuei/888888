<?php
/**
 * 联系方式管理
 */
$active_page = 'contact';
require_once 'header.php';
$c = $site_data['contact'] ?? [];
?>
<div class="admin-content">
    <div class="admin-card">
        <h2><i class="fas fa-address-card" style="color:#ff6b35;"></i> 联系方式设置</h2>
        <form method="post" id="form_contact" style="margin-top:20px;max-width:700px;">
            <input type="hidden" name="action" value="save_contact">
            <div class="form-group">
                <label class="form-label">销售电话（完整）</label>
                <input type="text" class="form-input" name="phone" value="<?php echo htmlspecialchars($c['phone'] ?? '400-800-8541'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">销售电话（显示用）</label>
                <input type="text" class="form-input" name="phone_display" value="<?php echo htmlspecialchars($c['phone_display'] ?? '400-800-8451'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">商务邮箱</label>
                <input type="email" class="form-input" name="email" value="<?php echo htmlspecialchars($c['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">公司地址</label>
                <input type="text" class="form-input" name="address" value="<?php echo htmlspecialchars($c['address'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">微信号</label>
                <input type="text" class="form-input" name="wechat" value="<?php echo htmlspecialchars($c['wechat'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">QQ号</label>
                <input type="text" class="form-input" name="qq" value="<?php echo htmlspecialchars($c['qq'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">官方QQ群</label>
                <input type="text" class="form-input" name="qq_group" value="<?php echo htmlspecialchars($c['qq_group'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存</button>
        </form>
    </div>
</div>

<script>
document.getElementById('form_contact').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch('', { method: 'POST', body: fd })
        .then(r => r.json()).then(d => {
            if (d.code === 0) { showToast(d.msg || '保存成功', 'success'); setTimeout(()=>location.reload(), 800); }
            else { showToast(d.msg || '保存失败', 'danger'); }
        }).catch(() => showToast('请求失败', 'danger'));
});
</script>
</div>
</body>
</html>
