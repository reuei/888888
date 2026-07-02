<style>
.user-header {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 24px;
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
    color: #fff;
    border-radius: 8px;
    margin-bottom: 16px;
}
.user-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
}
.user-info h2 { font-size: 20px; font-weight: 600; margin-bottom: 6px; }
.user-info p { opacity: 0.85; font-size: 13px; }
.user-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}
.user-stats .stat-item {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    padding: 16px;
    text-align: center;
}
.user-stats .stat-value { font-size: 22px; font-weight: 600; color: #2563EB; }
.user-stats .stat-label { font-size: 12px; color: #64748B; margin-top: 4px; }
.user-menu {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
}
.user-menu a {
    display: block;
    padding: 16px;
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    text-align: center;
    transition: all 0.2s;
}
.user-menu a:hover { border-color: #2563EB; background: #F8FAFF; }
.user-menu .icon { font-size: 24px; margin-bottom: 8px; }
.user-menu .label { font-size: 14px; color: #1F2937; }
.login-card { max-width: 420px; margin: 0 auto; }
</style>

<?php if (!$contact || !$user): ?>
<div class="card login-card">
    <div class="section-title" style="justify-content: center;">
        <span>个人中心登录</span>
    </div>
    <p style="text-align: center; color: #64748B; margin-bottom: 20px; font-size: 13px;">
        填写下单时使用的联系方式，即可查询订单与优惠券
    </p>
    <form id="userLoginForm">
        <div class="form-group">
            <label>联系方式</label>
            <input type="text" name="contact" placeholder="手机号 / 邮箱 / 昵称" required>
        </div>
        <button type="submit" class="btn btn-block" id="loginBtn">进入个人中心</button>
    </form>
    <div style="text-align: center; margin-top: 16px;">
        <a href="<?php echo url('index/order'); ?>" style="color: #64748B; font-size: 13px;">临时查询订单</a>
    </div>
</div>
<?php else: ?>
<div class="user-header">
    <div class="user-avatar">👤</div>
    <div class="user-info">
        <h2><?php echo h($contact); ?></h2>
        <p>注册时间：<?php echo $user['create_time']; ?> · 余额：¥<?php echo number_format($user['balance'] ?? 0, 2); ?></p>
        <p>积分：<?php echo number_format($user['points'] ?? 0); ?> · 成长值：<?php echo number_format($user['growth_value'] ?? 0); ?></p>
    </div>
    <div style="margin-left: auto;">
        <a href="<?php echo url('index/pointsCenter'); ?>" class="btn" style="background: rgba(255,255,255,0.2); border-color: transparent; margin-right:8px;">积分中心</a>
        <a href="<?php echo url('index/userLogout'); ?>" class="btn" style="background: rgba(255,255,255,0.2); border-color: transparent;">退出</a>
    </div>
</div>

<div class="user-stats">
    <div class="stat-item">
        <div class="stat-value"><?php echo $stats['total_orders'] ?? 0; ?></div>
        <div class="stat-label">累计订单</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">¥<?php echo number_format($stats['total_pay'] ?? 0, 2); ?></div>
        <div class="stat-label">累计消费</div>
    </div>
    <div class="stat-item">
        <div class="stat-value"><?php echo $stats['unpaid_orders'] ?? 0; ?></div>
        <div class="stat-label">待支付</div>
    </div>
    <div class="stat-item">
        <div class="stat-value"><?php echo $stats['delivered_orders'] ?? 0; ?></div>
        <div class="stat-label">已发货</div>
    </div>
</div>

<div class="card">
    <div class="section-title">
        <span>快捷入口</span>
    </div>
    <div class="user-menu">
        <a href="<?php echo url('index/userOrders'); ?>">
            <div class="icon">📦</div>
            <div class="label">我的订单</div>
        </a>
        <a href="<?php echo url('index/userCoupons'); ?>">
            <div class="icon">🎟️</div>
            <div class="label">我的优惠券</div>
        </a>
        <a href="<?php echo url('index/coupon'); ?>">
            <div class="icon">🎁</div>
            <div class="label">领券中心</div>
        </a>
        <a href="<?php echo url('index/order'); ?>">
            <div class="icon">🔍</div>
            <div class="label">订单查询</div>
        </a>
    </div>
</div>
<?php endif; ?>

<script>
document.getElementById('userLoginForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.textContent = '登录中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('index/userLogin'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.code === 0 && data.data.redirect) {
            location.href = data.data.redirect;
        } else {
            alert(data.msg);
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '进入个人中心';
    }
});
</script>
