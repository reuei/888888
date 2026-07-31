<div class="card" style="margin-bottom: 20px;">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-key"/></svg>
            最近授权
        </h3>
        <a href="/user/my-products" style="font-size: 13px; color: var(--primary); text-decoration: none;">查看全部</a>
    </div>
    <?php if (!empty($licenses)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>授权码</th>
                    <th>产品名称</th>
                    <th>状态</th>
                    <th>创建时间</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($licenses as $license): ?>
                <tr>
                    <td style="font-family: monospace; font-size: 13px;"><?= htmlspecialchars($license['license_key'] ?? '') ?></td>
                    <td><?= htmlspecialchars($license['product_name'] ?? '') ?></td>
                    <td><span class="badge <?= ($license['status'] ?? 0) == 1 ? 'badge-success' : 'badge-danger' ?>"><?= ($license['status'] ?? 0) == 1 ? '有效' : '无效' ?></span></td>
                    <td><?= htmlspecialchars($license['created_at'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg width="24" height="24"><use href="#i-key"/></svg>
        </div>
        <div class="empty-state-text">暂无授权记录</div>
        <a href="/user/products" class="btn btn-primary">购买产品</a>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-orders"/></svg>
            最近订单
        </h3>
        <a href="/user/orders" style="font-size: 13px; color: var(--primary); text-decoration: none;">查看全部</a>
    </div>
    <?php if (!empty($orders)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>订单号</th>
                    <th>产品名称</th>
                    <th>金额</th>
                    <th>状态</th>
                    <th>创建时间</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td style="font-family: monospace; font-size: 13px;"><?= htmlspecialchars($order['order_no'] ?? '') ?></td>
                    <td><?= htmlspecialchars($order['product_name'] ?? '') ?></td>
                    <td style="font-weight: 600;">¥<?= number_format($order['amount'] ?? 0, 2) ?></td>
                    <td><span class="badge <?= ($order['payment_status'] ?? 0) == 1 ? 'badge-success' : 'badge-warning' ?>"><?= ($order['payment_status'] ?? 0) == 1 ? '已支付' : '待支付' ?></span></td>
                    <td><?= htmlspecialchars($order['created_at'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg width="24" height="24"><use href="#i-orders"/></svg>
        </div>
        <div class="empty-state-text">暂无订单记录</div>
        <a href="/user/products" class="btn btn-primary">浏览产品</a>
    </div>
    <?php endif; ?>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-message"/></svg>
            系统消息
        </h3>
        <a href="/user/messages" style="font-size: 13px; color: var(--primary); text-decoration: none;">查看全部</a>
    </div>
    <?php if (!empty($messages)): ?>
    <div style="display: flex; flex-direction: column; gap: 12px;">
        <?php foreach ($messages as $msg): ?>
        <div style="display: flex; align-items: flex-start; gap: 12px; padding: 12px; background: var(--bg-tertiary); border-radius: var(--radius); <?= ($msg['is_read'] ?? 0) == 0 ? 'border-left: 3px solid var(--primary);' : '' ?>">
            <div style="width: 32px; height: 32px; border-radius: 8px; background: <?= ($msg['is_read'] ?? 0) == 0 ? 'var(--primary-50)' : 'var(--bg-hover)' ?>; color: <?= ($msg['is_read'] ?? 0) == 0 ? 'var(--primary)' : 'var(--text-muted)' ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="16" height="16"><use href="#i-bell"/></svg>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="font-size: 14px; font-weight: <?= ($msg['is_read'] ?? 0) == 0 ? '600' : '400' ?>; color: var(--text); margin-bottom: 4px;"><?= htmlspecialchars($msg['title'] ?? '') ?></div>
                <div style="font-size: 13px; color: var(--text-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($msg['content'] ?? '') ?></div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;"><?= htmlspecialchars($msg['created_at'] ?? '') ?></div>
            </div>
            <?php if (($msg['is_read'] ?? 0) == 0): ?>
            <span class="uc-nav-badge">新</span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg width="24" height="24"><use href="#i-message"/></svg>
        </div>
        <div class="empty-state-text">暂无新消息</div>
    </div>
    <?php endif; ?>
</div>
