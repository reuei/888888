<div class="welcome-banner">
    <h2>欢迎回来，<?= htmlspecialchars($user['username'] ?? '') ?>！</h2>
    <p>这里是您的个人中心，您可以管理授权产品、查看余额、查看订单等。</p>
</div>

<div class="stat-grid">
    <div class="stat-card accent-blue">
        <div class="stat-icon-box">
            <svg width="20" height="20"><use href="#i-wallet"/></svg>
        </div>
        <div class="stat-value">¥<?= number_format($user['balance'] ?? 0, 2) ?></div>
        <div class="stat-label">账户余额</div>
    </div>
    <div class="stat-card accent-green">
        <div class="stat-icon-box">
            <svg width="20" height="20"><use href="#i-box"/></svg>
        </div>
        <div class="stat-value"><?= $stats['products'] ?? 0 ?></div>
        <div class="stat-label">授权产品</div>
    </div>
    <div class="stat-card accent-orange">
        <div class="stat-icon-box">
            <svg width="20" height="20"><use href="#i-orders"/></svg>
        </div>
        <div class="stat-value"><?= $stats['orders'] ?? 0 ?></div>
        <div class="stat-label">订单数量</div>
    </div>
    <div class="stat-card accent-purple">
        <div class="stat-icon-box">
            <svg width="20" height="20"><use href="#i-clock"/></svg>
        </div>
        <div class="stat-value"><?= $stats['login_count'] ?? 0 ?></div>
        <div class="stat-label">登录次数</div>
    </div>
</div>

<div class="two-col" style="margin-bottom: 20px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">快捷操作</h3>
        </div>
        <div class="quick-actions">
            <a href="/user/products" class="btn btn-primary">
                <svg width="16" height="16"><use href="#i-box"/></svg>
                购买产品
            </a>
            <a href="/user/my-products" class="btn btn-outline">
                <svg width="16" height="16"><use href="#i-key"/></svg>
                我的授权
            </a>
            <a href="/user/balance" class="btn btn-outline">
                <svg width="16" height="16"><use href="#i-wallet"/></svg>
                余额管理
            </a>
            <a href="/user/messages" class="btn btn-outline">
                <svg width="16" height="16"><use href="#i-bell"/></svg>
                消息中心
                <?php if (($unreadCount ?? 0) > 0): ?>
                <span class="uc-nav-badge" style="margin-left:6px;"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">账户信息</h3>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-secondary); font-size: 13px;">用户名</span>
                <span style="color: var(--text); font-weight: 500;"><?= htmlspecialchars($user['username'] ?? '') ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-secondary); font-size: 13px;">邮箱</span>
                <span style="color: var(--text); font-weight: 500;"><?= htmlspecialchars($user['email'] ?? '未绑定') ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-secondary); font-size: 13px;">手机号</span>
                <span style="color: var(--text); font-weight: 500;"><?= htmlspecialchars($user['phone'] ?? '未绑定') ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: var(--text-secondary); font-size: 13px;">QQ</span>
                <span style="color: var(--text); font-weight: 500;"><?= htmlspecialchars($user['qq'] ?? '未绑定') ?></span>
            </div>
            <a href="/user/settings" style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius); color: var(--text); font-size: 14px; text-decoration: none; transition: all 0.15s;">
                <svg width="14" height="14"><use href="#i-edit"/></svg>
                编辑资料
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">最新动态</h3>
        <a href="/user/workplace" style="font-size: 13px; color: var(--primary); text-decoration: none;">查看更多</a>
    </div>
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <?php if (!empty($latestActivities)): ?>
            <?php foreach ($latestActivities as $activity): ?>
            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg-tertiary); border-radius: var(--radius);">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="16" height="16"><use href="#i-<?= $activity['icon'] ?? 'log' ?>"/></svg>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 13px; color: var(--text);"><?= htmlspecialchars($activity['description'] ?? '') ?></div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;"><?= htmlspecialchars($activity['created_at'] ?? '') ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg width="24" height="24"><use href="#i-log"/></svg>
                </div>
                <div class="empty-state-text">暂无动态记录</div>
            </div>
        <?php endif; ?>
    </div>
</div>
