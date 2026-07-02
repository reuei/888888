<div class="user-layout">
    <div class="user-sidebar">
        <a href="<?php echo url('user'); ?>" class="active">个人中心</a>
        <a href="<?php echo url('user/license'); ?>">我的授权</a>
        <a href="<?php echo url('user/plugin'); ?>">我的插件</a>
        <a href="<?php echo url('user/order'); ?>">我的订单</a>
        <a href="<?php echo url('user/recharge'); ?>">余额充值</a>
        <a href="<?php echo url('user/profile'); ?>">修改资料</a>
        <a href="<?php echo url('user/password'); ?>">修改密码</a>
    </div>
    <div class="user-content">
        <div class="card">
            <h2 style="margin-bottom: 20px;">欢迎，<?php echo h($currentUser['nickname'] ?: $currentUser['username']); ?></h2>
            <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));">
                <div class="card" style="text-align:center;">
                    <div style="font-size: 24px; font-weight: 600; color: #2563EB;"><?php echo $licenseCount; ?></div>
                    <div style="color: #64748B; margin-top: 8px;">我的授权</div>
                </div>
                <div class="card" style="text-align:center;">
                    <div style="font-size: 24px; font-weight: 600; color: #10B981;"><?php echo $pluginCount; ?></div>
                    <div style="color: #64748B; margin-top: 8px;">我的插件</div>
                </div>
                <div class="card" style="text-align:center;">
                    <div style="font-size: 24px; font-weight: 600; color: #F59E0B;"><?php echo $orderCount; ?></div>
                    <div style="color: #64748B; margin-top: 8px;">我的订单</div>
                </div>
                <div class="card" style="text-align:center;">
                    <div style="font-size: 24px; font-weight: 600; color: #EF4444;"><?php echo format_price($currentUser['balance']); ?></div>
                    <div style="color: #64748B; margin-top: 8px;">账户余额</div>
                </div>
            </div>
        </div>
    </div>
</div>
