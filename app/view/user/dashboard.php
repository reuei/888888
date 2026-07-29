<div class="user-breadcrumb">
    <span>用户中心</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">用户中心</h1>

<div class="dashboard-welcome" style="background: linear-gradient(135deg, #4f8cff, #3868ff); padding: 32px; color: #fff; margin-bottom: 24px;">
    <h2 style="font-size: 22px; margin-bottom: 8px;">欢迎回来，<?= htmlspecialchars($user['username'] ?? '') ?>！</h2>
    <p style="opacity: 0.9; font-size: 14px;">这里是您的个人中心，您可以管理您的授权产品、查看余额等。</p>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-value">¥<?= number_format($user['balance'] ?? 0, 2) ?></div>
        <div class="stat-label">账户余额</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats['products'] ?? 0 ?></div>
        <div class="stat-label">授权产品</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats['orders'] ?? 0 ?></div>
        <div class="stat-label">订单数量</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats['login_count'] ?? 0 ?></div>
        <div class="stat-label">登录次数</div>
    </div>
</div>

<div class="user-dashboard-footer" style="margin-top: 24px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">快捷操作</h3>
        </div>
        <div style="display: flex; gap: 12px; margin-top: 16px; flex-wrap: wrap;">
            <a href="/user/products" class="btn btn-primary">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-box"/></svg>购买产品
            </a>
            <a href="/user/my-products" class="btn btn-outline">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-key"/></svg>我的授权
            </a>
            <a href="/user/balance" class="btn btn-outline">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-wallet"/></svg>余额管理
            </a>
            <a href="/user/messages" class="btn btn-outline">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-bell"/></svg>消息中心
                <?php if (($unreadCount ?? 0) > 0): ?>
                <span style="background:#ff4d4f;color:#fff;border-radius:10px;padding:0 6px;font-size:11px;margin-left:4px;"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</div>