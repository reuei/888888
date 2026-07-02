<div class="breadcrumb">店铺设置 / 店铺信息</div>
<div class="page-header">
    <h2>店铺信息</h2>
</div>

<div class="card">
    <form id="settingForm">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label>店铺ID</label>
                <input type="text" value="<?php echo h($merchant['shop_id'] ?? ''); ?>" disabled>
            </div>
            <div class="form-group">
                <label>登录账号</label>
                <input type="text" value="<?php echo h($merchant['username'] ?? ''); ?>" disabled>
            </div>
            <div class="form-group">
                <label>店铺名称 <span style="color: #EF4444;">*</span></label>
                <input type="text" name="shop_name" value="<?php echo h($merchant['shop_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>联系人</label>
                <input type="text" name="real_name" value="<?php echo h($merchant['real_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>手机号</label>
                <input type="tel" name="mobile" value="<?php echo h($merchant['mobile'] ?? ''); ?>" placeholder="11位手机号">
            </div>
            <div class="form-group">
                <label>邮箱</label>
                <input type="email" name="email" value="<?php echo h($merchant['email'] ?? ''); ?>" placeholder="选填">
            </div>
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
        const res = await fetch('<?php echo url('merchant/setting/save'); ?>', { method: 'POST', body: formData });
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
