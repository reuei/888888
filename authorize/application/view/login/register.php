<div class="card" style="max-width: 420px; margin: 60px auto;">
    <h2 style="text-align:center; margin-bottom: 24px;">用户注册</h2>
    <form id="registerForm">
        <div class="form-group">
            <label>账号</label>
            <input type="text" name="username" placeholder="4-20 位字母/数字/下划线" required>
        </div>
        <div class="form-group">
            <label>昵称</label>
            <input type="text" name="nickname" placeholder="可选">
        </div>
        <div class="form-group">
            <label>邮箱</label>
            <input type="text" name="email" placeholder="可选">
        </div>
        <div class="form-group">
            <label>密码</label>
            <input type="password" name="password" placeholder="至少 6 位" required>
        </div>
        <div class="form-group">
            <label>确认密码</label>
            <input type="password" name="password_confirm" required>
        </div>
        <button type="submit" class="btn btn-block">注册</button>
    </form>
    <p style="text-align:center; margin-top: 16px; font-size: 13px; color: #64748B;">
        已有账号？<a href="<?php echo url('login'); ?>" style="color: #2563EB;">立即登录</a>
    </p>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    fetch('<?php echo url('login/doRegister'); ?>', {
        method: 'POST',
        body: new FormData(form)
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0 && res.data.redirect) {
            location.href = res.data.redirect;
        }
    });
});
</script>
