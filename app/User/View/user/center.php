<div class="welcome-block">
    <h1 class="welcome-title">你好，<?= h($user['nickname'] ?? $user['username']) ?></h1>
    <p class="welcome-sub">欢迎回到玄武发卡 v1.0.5</p>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-card-label">订单总数</div>
        <div class="stat-card-num"><?= (int) $stats['orders'] ?></div>
    </div>
    <div class="stat-card stat-card-warn">
        <div class="stat-card-label">待支付</div>
        <div class="stat-card-num"><?= (int) $stats['pending'] ?></div>
    </div>
    <div class="stat-card stat-card-success">
        <div class="stat-card-label">账户余额</div>
        <div class="stat-card-num">¥<?= format_money($stats['balance']) ?></div>
    </div>
    <div class="stat-card stat-card-info">
        <div class="stat-card-label">累计消费</div>
        <div class="stat-card-num">¥<?= format_money($stats['total']) ?></div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">最近订单</h3>
        <a href="/user/orders" class="panel-link">查看全部</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>订单号</th>
                <th>商品</th>
                <th>金额</th>
                <th>状态</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $o): ?>
            <tr>
                <td><?= h($o['order_no']) ?></td>
                <td><?= h($o['goods_name']) ?></td>
                <td>¥<?= format_money($o['amount']) ?></td>
                <td>
                    <?php if ((int) $o['status'] === 1): ?>
                        <span class="badge badge-success">已完成</span>
                    <?php elseif ((int) $o['status'] === 0): ?>
                        <span class="badge badge-warning">待支付</span>
                    <?php else: ?>
                        <span class="badge">已关闭</span>
                    <?php endif; ?>
                </td>
                <td class="muted"><?= format_time($o['create_time']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">最新消息</h3>
        <a href="/user/messages" class="panel-link">查看全部</a>
    </div>
    <div class="msg-list">
        <?php foreach ($messages as $m): ?>
        <a href="/user/messages/read/<?= (int) $m['id'] ?>" class="msg-item <?= empty($m['is_read']) ? 'unread' : '' ?>">
            <span class="msg-type tag-<?= h($m['type'] ?? 'system') ?>"><?= h($m['type'] ?? '系统') ?></span>
            <span class="msg-title"><?= h($m['title']) ?></span>
            <span class="msg-time"><?= format_time($m['create_time'] ?? null, 'm-d H:i') ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</div>
