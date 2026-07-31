<div class="page-header">
    <div>
        <h1 class="page-title">订单管理</h1>
        <div class="page-subtitle">查看所有交易订单，追踪订单状态与收款情况。</div>
    </div>
</div>

<div class="admin-stats-row" style="grid-template-columns:repeat(3,1fr);">
    <div class="admin-stat-mini">
        <div class="asm-label">总订单</div>
        <div class="asm-value"><?= $stats['orders'] ?? 0 ?></div>
    </div>
    <div class="admin-stat-mini">
        <div class="asm-label">已完成</div>
        <div class="asm-value"><?= $stats['completed_orders'] ?? 0 ?></div>
    </div>
    <div class="admin-stat-mini">
        <div class="asm-label">总收入</div>
        <div class="asm-value">¥<?= number_format($stats['revenue'] ?? 0, 2) ?></div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-search-bar">
        <div class="search-input">
            <svg><use href="#i-search"/></svg>
            <input type="text" class="form-control" placeholder="搜索订单号、产品、用户...">
        </div>
        <div class="admin-filters">
            <select class="form-control" style="max-width:140px;">
                <option value="">全部状态</option>
                <option value="1">已完成</option>
                <option value="0">待处理</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>订单号</th>
                    <th>产品</th>
                    <th>用户</th>
                    <th>金额</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th style="width:100px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?? '' ?></td>
                        <td style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($order['order_no'] ?? '') ?></td>
                        <td><?= htmlspecialchars($order['product_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($order['username'] ?? '') ?></td>
                        <td style="font-weight:600;color:var(--primary);">¥<?= number_format($order['amount'] ?? 0, 2) ?></td>
                        <td>
                            <?php if (($order['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>已完成</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><span class="tag-dot"></span>待处理</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($order['created_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <button class="btn btn-outline btn-sm" onclick="alert('订单详情 #<?= $order['id'] ?>')">
                                    详情
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-orders"/></svg></div><div class="empty-text">暂无订单数据</div></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>