<div class="user-layout">
    <div class="user-sidebar">
        <a href="<?php echo url('user'); ?>">个人中心</a>
        <a href="<?php echo url('user/license'); ?>">我的授权</a>
        <a href="<?php echo url('user/plugin'); ?>">我的插件</a>
        <a href="<?php echo url('user/order'); ?>">我的订单</a>
        <a href="<?php echo url('user/recharge'); ?>">余额充值</a>
        <a href="<?php echo url('user/profile'); ?>" class="active">修改资料</a>
        <a href="<?php echo url('user/password'); ?>">修改密码</a>
    </div>
    <div class="user-content">
        <div class="card">
            <h2 style="margin-bottom: 20px;">修改资料</h2>
            <form id="profileForm">
                <div class="form-group">
                    <label>昵称</label>
                    <input type="text" name="nickname" value="<?php echo h($user['nickname']); ?>">
                </div>
                <div class="form-group">
                    <label>邮箱</label>
                    <input type="text" name="email" value="<?php echo h($user['email']); ?>">
                </div>
                <div class="form-group">
                    <label>手机号</label>
                    <input type="text" name="mobile" value="<?php echo h($user['mobile']); ?>">
                </div>
                <button type="submit" class="btn">保存</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('profileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url('user/saveProfile'); ?>', {
        method: 'POST',
        body: new FormData(e.target)
    }).then(r => r.json()).then(res => {
        alert(res.msg);
    });
});
</script>
