<div class="user-layout">
    <div class="user-sidebar">
        <a href="<?php echo url('user'); ?>">个人中心</a>
        <a href="<?php echo url('user/license'); ?>">我的授权</a>
        <a href="<?php echo url('user/plugin'); ?>">我的插件</a>
        <a href="<?php echo url('user/order'); ?>">我的订单</a>
        <a href="<?php echo url('user/recharge'); ?>">余额充值</a>
        <a href="<?php echo url('user/profile'); ?>">修改资料</a>
        <a href="<?php echo url('user/password'); ?>" class="active">修改密码</a>
    </div>
    <div class="user-content">
        <div class="card">
            <h2 style="margin-bottom: 20px;">修改密码</h2>
            <form id="passwordForm">
                <div class="form-group">
                    <label>原密码</label>
                    <input type="password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label>新密码</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>确认新密码</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn">保存</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url('user/savePassword'); ?>', {
        method: 'POST',
        body: new FormData(e.target)
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) e.target.reset();
    });
});
</script>
