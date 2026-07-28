<div class="user-breadcrumb">
    <span>用户中心</span> / <span>工作台</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">工作台</h1>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-key"/></svg>最近授权
        </h3>
    </div>
    <?php if (!empty($licenses)): ?>
    <div class="table-wrap">
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
    <div class="empty-state" style="text-align: center; padding: 40px; color: #687690;">暂无授权记录</div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-orders"/></svg>最近订单
        </h3>
    </div>
    <?php if (!empty($orders)): ?>
    <div class="table-wrap">
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
                    <td>¥<?= number_format($order['amount'] ?? 0, 2) ?></td>
                    <td><span class="badge <?= ($order['payment_status'] ?? 0) == 1 ? 'badge-success' : 'badge-warning' ?>"><?= ($order['payment_status'] ?? 0) == 1 ? '已支付' : '待支付' ?></span></td>
                    <td><?= htmlspecialchars($order['created_at'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 40px; color: #687690;">暂无订单记录</div>
    <?php endif; ?>
</div>