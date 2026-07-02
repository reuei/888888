<div class="breadcrumb">店铺设置 / 修改密码</div>
<div class="page-header">
    <h2>修改密码</h2>
</div>

<div class="card" style="max-width: 500px;">
    <form id="passwordForm">
        <div class="form-group">
            <label>原密码 <span style="color: #EF4444;">*</span></label>
            <input type="password" name="old_password" required>
        </div>
        <div class="form-group">
            <label>新密码 <span style="color: #EF4444;">*</span></label>
            <input type="password" name="new_password" minlength="6" required>
            <p class="hint">密码长度不能少于6位</p>
        </div>
        <div class="form-group">
            <label>确认新密码 <span style="color: #EF4444;">*</span></label>
            <input type="password" name="confirm_password" minlength="6" required>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存密码</button>
    </form>
</div>

<script>
document.getElementById('passwordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '保存中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('merchant/setting/savePassword'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) {
            if (data.redirect) location.href = data.redirect;
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存密码';
    }
});
</script>
