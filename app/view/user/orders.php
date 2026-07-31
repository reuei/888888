<div class="filter-bar">
    <div class="filter-tabs">
        <a href="/user/orders" class="filter-tab<?= !isset($filter) || $filter === 'all' ? ' active' : '' ?>">全部订单</a>
        <a href="/user/orders?filter=pending" class="filter-tab<?= ($filter ?? '') === 'pending' ? ' active' : '' ?>">待支付</a>
        <a href="/user/orders?filter=completed" class="filter-tab<?= ($filter ?? '') === 'completed' ? ' active' : '' ?>">已完成</a>
    </div>
</div>

<?php if (!empty($orders)): ?>
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>订单号</th>
                    <th>产品名称</th>
                    <th>金额</th>
                    <th>状态</th>
                    <th>下载</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td style="font-family: monospace; font-size: 13px;"><?= htmlspecialchars($order['order_no'] ?? '') ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="14" height="14"><use href="#i-box"/></svg>
                            </div>
                            <span><?= htmlspecialchars($order['product_name'] ?? '') ?></span>
                        </div>
                    </td>
                    <td style="font-weight: 600; color: var(--danger);">¥<?= number_format($order['amount'] ?? 0, 2) ?></td>
                    <td>
                        <?php $status = $order['status'] ?? 0; ?>
                        <?php if ($status == 1): ?>
                        <span class="badge badge-success">已完成</span>
                        <?php elseif ($status == 2): ?>
                        <span class="badge badge-warning">待处理</span>
                        <?php else: ?>
                        <span class="badge badge-danger">已取消</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($status == 1 && !empty($order['download_file'])): ?>
                        <a href="/download?order_id=<?= $order['id'] ?? 0 ?>" class="btn btn-primary btn-sm">
                            <svg width="12" height="12"><use href="#i-download"/></svg>
                            下载
                        </a>
                        <?php elseif ($status == 1): ?>
                        <span class="badge badge-info">暂无文件</span>
                        <?php else: ?>
                        <span style="color: var(--text-muted);">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size: 13px;"><?= htmlspecialchars($order['created_at'] ?? '') ?></td>
                    <td>
                        <?php if ($status == 2): ?>
                        <a href="/user/buy?product_id=<?= $order['product_id'] ?? 0 ?>" class="btn btn-primary btn-sm">支付</a>
                        <?php elseif ($status == 1): ?>
                        <a href="/user/my-products" class="btn btn-outline btn-sm">查看</a>
                        <?php else: ?>
                        <span style="color: var(--text-muted);">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination" style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" style="padding: 8px 14px; border: 1px solid var(--border); border-radius: var(--radius); text-decoration: none; color: var(--text); font-size: 13px; transition: all 0.15s; <?= ($page ?? 1) == $i ? 'background: var(--primary); color: #fff; border-color: var(--primary);' : 'background: var(--bg-card);' ?>">
        <?= $i ?>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php else: ?>
<div class="empty-state">
    <div class="empty-state-icon">
        <svg width="32" height="32"><use href="#i-orders"/></svg>
    </div>
    <div class="empty-state-text">暂无订单记录</div>
    <a href="/user/products" class="btn btn-primary">去购买</a>
</div>
<?php endif; ?>
