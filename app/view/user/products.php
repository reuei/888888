<div class="filter-bar">
    <div class="filter-search">
        <svg width="16" height="16"><use href="#i-search"/></svg>
        <input type="text" id="productSearch" placeholder="搜索产品..." value="<?= htmlspecialchars($keyword ?? '') ?>" oninput="searchProducts()">
    </div>
    <div class="filter-tabs">
        <a href="/user/products" class="filter-tab<?= !isset($filter) || $filter === 'all' ? ' active' : '' ?>">全部</a>
        <a href="/user/products?filter=new" class="filter-tab<?= ($filter ?? '') === 'new' ? ' active' : '' ?>">最新</a>
        <a href="/user/products?filter=hot" class="filter-tab<?= ($filter ?? '') === 'hot' ? ' active' : '' ?>">热门</a>
    </div>
</div>

<?php if (!empty($products)): ?>
<div class="product-grid">
    <?php foreach ($products as $product): ?>
    <div class="card" style="display: flex; flex-direction: column; transition: all 0.2s; cursor: pointer;" onclick="goToBuy(<?= $product['id'] ?? 0 ?>)">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24"><use href="#i-box"/></svg>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; font-weight: 600; color: var(--text); margin: 0;"><?= htmlspecialchars($product['name'] ?? '') ?></h3>
                <?php if (!empty($product['category'])): ?>
                <span class="badge badge-info" style="margin-top: 4px;"><?= htmlspecialchars($product['category']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 16px; min-height: 44px; line-height: 1.6;"><?= htmlspecialchars($product['description'] ?? '暂无描述') ?></p>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto;">
            <div class="price-tag <?= ($product['price'] ?? 0) == 0 ? 'price-free' : 'price-paid' ?>">
                <?php if (($product['price'] ?? 0) == 0): ?>
                    免费
                <?php else: ?>
                    ¥<?= number_format($product['price'] ?? 0, 2) ?>
                    <span style="font-size: 12px; color: var(--text-muted); font-weight: 400;">/ 永久</span>
                <?php endif; ?>
            </div>
            <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); goToBuy(<?= $product['id'] ?? 0 ?>)">
                <svg width="14" height="14"><use href="#i-cart"/></svg>
                立即购买
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination" style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?><?= !empty($filter) ? '&filter=' . $filter : '' ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>" style="padding: 8px 14px; border: 1px solid var(--border); border-radius: var(--radius); text-decoration: none; color: var(--text); font-size: 13px; transition: all 0.15s; <?= ($page ?? 1) == $i ? 'background: var(--primary); color: #fff; border-color: var(--primary);' : 'background: var(--bg-card);' ?>">
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
    <div class="empty-state-text">暂无可用产品</div>
</div>
<?php endif; ?>

<script>
    function goToBuy(productId) {
        window.location.href = '/user/buy?product_id=' + productId;
    }

    function searchProducts() {
        var keyword = document.getElementById('productSearch').value;
        if (keyword.length >= 0) {
            window.location.href = '/user/products?keyword=' + encodeURIComponent(keyword);
        }
    }
</script>
