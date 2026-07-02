<div class="card" style="max-width: 420px; margin: 60px auto;">
    <h2 style="text-align:center; margin-bottom: 24px;">用户登录</h2>
    <form id="loginForm">
        <div class="form-group">
            <label>账号</label>
            <input type="text" name="username" placeholder="请输入账号" required>
        </div>
        <div class="form-group">
            <label>密码</label>
            <input type="password" name="password" placeholder="请输入密码" required>
        </div>
        <button type="submit" class="btn btn-block">登录</button>
    </form>
    <p style="text-align:center; margin-top: 16px; font-size: 13px; color: #64748B;">
        还没有账号？<a href="<?php echo url('login/register'); ?>" style="color: #2563EB;">立即注册</a>
    </p>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    fetch('<?php echo url('login/doLogin'); ?>', {
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
