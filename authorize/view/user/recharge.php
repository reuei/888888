<div class="user-layout">
    <div class="user-sidebar">
        <a href="<?php echo url('user'); ?>">个人中心</a>
        <a href="<?php echo url('user/license'); ?>">我的授权</a>
        <a href="<?php echo url('user/plugin'); ?>">我的插件</a>
        <a href="<?php echo url('user/order'); ?>">我的订单</a>
        <a href="<?php echo url('user/recharge'); ?>" class="active">余额充值</a>
        <a href="<?php echo url('user/profile'); ?>">修改资料</a>
        <a href="<?php echo url('user/password'); ?>">修改密码</a>
    </div>
    <div class="user-content">
        <div class="card">
            <h2 style="margin-bottom: 20px;">余额充值</h2>
            <p style="margin-bottom: 16px; color: #64748B;">当前余额：<strong style="color:#EF4444;"><?php echo format_price($currentUser['balance']); ?></strong></p>
            <form id="rechargeForm">
                <div class="form-group">
                    <label>充值金额</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required>
                </div>
                <div class="form-group">
                    <label>备注（转账信息/截图说明）</label>
                    <textarea name="remark" rows="3"></textarea>
                </div>
                <button type="submit" class="btn">提交申请</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('rechargeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url('user/doRecharge'); ?>', {
        method: 'POST',
        body: new FormData(e.target)
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) e.target.reset();
    });
});
</script>
