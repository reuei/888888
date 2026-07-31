<div class="filter-bar">
    <div class="filter-tabs">
        <a href="/user/my-products" class="filter-tab<?= !isset($filter) || $filter === 'all' ? ' active' : '' ?>">全部</a>
        <a href="/user/my-products?filter=active" class="filter-tab<?= ($filter ?? '') === 'active' ? ' active' : '' ?>">有效</a>
        <a href="/user/my-products?filter=expired" class="filter-tab<?= ($filter ?? '') === 'expired' ? ' active' : '' ?>">已过期</a>
    </div>
</div>

<?php if (!empty($orders)): ?>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <?php foreach ($orders as $order): ?>
    <div class="card" style="display: flex; flex-direction: column;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20"><use href="#i-key"/></svg>
                </div>
                <div>
                    <h3 style="font-size: 15px; font-weight: 600; color: var(--text); margin: 0;"><?= htmlspecialchars($order['product_name'] ?? '') ?></h3>
                    <span style="font-size: 12px; color: var(--text-muted); font-family: monospace;"><?= htmlspecialchars($order['order_no'] ?? '') ?></span>
                </div>
            </div>
            <span class="badge <?= ($order['status'] ?? 0) == 1 ? 'badge-success' : 'badge-warning' ?>">
                <?= ($order['status'] ?? 0) == 1 ? '已完成' : '待处理' ?>
            </span>
        </div>
        <div style="padding: 12px; background: var(--bg-tertiary); border-radius: var(--radius); margin-bottom: 12px;">
            <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">授权码</div>
            <div style="font-family: monospace; font-size: 13px; color: var(--text); word-break: break-all;"><?= htmlspecialchars($order['license_key'] ?? 'N/A') ?></div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div>
                <span style="font-size: 12px; color: var(--text-muted);">金额</span>
                <div style="font-size: 16px; font-weight: 600; color: var(--text);">¥<?= number_format($order['amount'] ?? 0, 2) ?></div>
            </div>
            <div>
                <span style="font-size: 12px; color: var(--text-muted);">购买时间</span>
                <div style="font-size: 13px; color: var(--text-secondary);"><?= htmlspecialchars($order['created_at'] ?? '') ?></div>
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            <?php if ($order['status'] == 1 && !empty($order['download_file'])): ?>
            <a href="/download?order_id=<?= $order['id'] ?? 0 ?>" class="btn btn-primary btn-sm" style="flex: 1;">
                <svg width="14" height="14"><use href="#i-download"/></svg>
                下载产品
            </a>
            <?php elseif ($order['status'] == 1): ?>
            <span class="badge badge-info" style="flex: 1; justify-content: center; display: inline-flex;">暂无下载文件</span>
            <?php else: ?>
            <a href="/user/buy?product_id=<?= $order['product_id'] ?? 0 ?>" class="btn btn-outline btn-sm" style="flex: 1;">继续支付</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
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
        <svg width="32" height="32"><use href="#i-box"/></svg>
    </div>
    <div class="empty-state-text">暂无已购买的产品</div>
    <a href="/user/products" class="btn btn-primary">立即选购</a>
</div>
<?php endif; ?>
