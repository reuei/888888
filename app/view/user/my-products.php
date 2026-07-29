<div class="user-breadcrumb">
    <span>用户中心</span> / <span>我的产品</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">我的产品</h1>

<div class="card">
    <?php if (!empty($orders)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>订单号</th>
                    <th>产品名称</th>
                    <th>金额</th>
                    <th>支付方式</th>
                    <th>状态</th>
                    <th>下载</th>
                    <th>购买时间</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td style="font-family: monospace; font-size: 13px;"><?= htmlspecialchars($order['order_no'] ?? '') ?></td>
                    <td><?= htmlspecialchars($order['product_name'] ?? '') ?></td>
                    <td>¥<?= number_format($order['amount'] ?? 0, 2) ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($order['payment_method'] ?? '余额') ?></span></td>
                    <td><span class="badge <?= ($order['status'] ?? 0) == 1 ? 'badge-success' : 'badge-warning' ?>"><?= ($order['status'] ?? 0) == 1 ? '已完成' : '待处理' ?></span></td>
                    <td>
                        <?php if ($order['status'] == 1 && !empty($order['download_file'])): ?>
                        <a href="/download?order_id=<?= $order['id'] ?? 0 ?>" class="btn btn-primary btn-sm">
                            <svg width="14" height="14" style="vertical-align: middle; margin-right: 4px;"><use href="#i-download"/></svg>下载
                        </a>
                        <?php elseif ($order['status'] == 1 && empty($order['download_file'])): ?>
                        <span class="badge badge-info">暂无文件</span>
                        <?php else: ?>
                        <span class="badge badge-warning">待支付</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($order['created_at'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">
        <svg width="48" height="48" style="color: #c0c8d8; margin-bottom: 12px; display: block; margin-left: auto; margin-right: auto;"><use href="#i-box"/></svg>
        暂无已购买的产品，<a href="/user/products" style="color: #4f8cff;">立即选购</a>
    </div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>