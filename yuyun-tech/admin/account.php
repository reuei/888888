<?php
/**
 * 账号管理 - 修改管理员账号
 */
$active_page = 'account';
require_once 'header.php';
?>
<div class="admin-content">
    <div class="admin-card" style="max-width:600px;">
        <h2><i class="fas fa-user-circle" style="color:#1a73e8;"></i> 管理员账号管理</h2>
        <p style="color:#4a5568;margin-top:12px;font-size:14px;">修改管理员登录账号和密码。修改后将自动写入配置文件。</p>
        <form method="post" id="form_account" style="margin-top:20px;">
            <input type="hidden" name="action" value="change_password">
            <div class="form-group">
                <label class="form-label">管理员用户名</label>
                <input type="text" class="form-input" name="admin_user" value="admin" required>
            </div>
            <div class="form-group">
                <label class="form-label">新密码</label>
                <input type="text" class="form-input" name="admin_pass" placeholder="输入新密码" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> 保存账号</button>
        </form>
    </div>
</div>

<script>
document.getElementById('form_account').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!confirm('确定要修改账号和密码吗？修改后将需要重新登录。')) return;
    const fd = new FormData(this);
    fetch('', { method: 'POST', body: fd })
        .then(r => r.json()).then(d => {
            if (d.code === 0) {
                showToast(d.msg || '保存成功', 'success');
                setTimeout(()=>{ location.href = 'login.php'; }, 1200);
            } else {
                showToast(d.msg || '保存失败', 'danger');
            }
        }).catch(() => showToast('请求失败', 'danger'));
});
</script>
</div>
</body>
</html>
