<div class="filter-bar">
    <div class="filter-search">
        <svg width="16" height="16"><use href="#i-search"/></svg>
        <input type="text" id="pluginSearch" placeholder="搜索插件..." value="<?= htmlspecialchars($keyword ?? '') ?>" oninput="searchPlugins()">
    </div>
    <div class="filter-tabs">
        <a href="/user/plugin-market" class="filter-tab<?= !isset($filter) || $filter === 'all' ? ' active' : '' ?>">全部</a>
        <a href="/user/plugin-market?filter=free" class="filter-tab<?= ($filter ?? '') === 'free' ? ' active' : '' ?>">免费</a>
        <a href="/user/plugin-market?filter=paid" class="filter-tab<?= ($filter ?? '') === 'paid' ? ' active' : '' ?>">付费</a>
    </div>
</div>

<?php if (!empty($plugins)): ?>
<div class="product-grid">
    <?php foreach ($plugins as $plugin): ?>
    <div class="card" style="display: flex; flex-direction: column; transition: all 0.2s;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #7c3aed, #4f8cff); color: #fff; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24"><use href="#i-plugin"/></svg>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 16px; font-weight: 600; color: var(--text); margin: 0;"><?= htmlspecialchars($plugin['name'] ?? '') ?></h3>
                <?php if (!empty($plugin['author'])): ?>
                <span style="font-size: 12px; color: var(--text-muted);">by <?= htmlspecialchars($plugin['author']) ?></span>
                <?php endif; ?>
            </div>
            <?php if (($plugin['price'] ?? 0) == 0): ?>
            <span class="badge badge-success">免费</span>
            <?php else: ?>
            <span class="badge badge-info">付费</span>
            <?php endif; ?>
        </div>
        <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 16px; min-height: 44px; line-height: 1.6;"><?= htmlspecialchars($plugin['description'] ?? '暂无描述') ?></p>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto;">
            <div class="price-tag <?= ($plugin['price'] ?? 0) == 0 ? 'price-free' : 'price-paid' ?>">
                <?php if (($plugin['price'] ?? 0) == 0): ?>
                    免费
                <?php else: ?>
                    ¥<?= number_format($plugin['price'] ?? 0, 2) ?>
                <?php endif; ?>
            </div>
            <form method="POST" action="/user/buy" data-ajax="false" style="margin: 0;">
                <input type="hidden" name="plugin_id" value="<?= $plugin['id'] ?? 0 ?>">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg width="14" height="14"><use href="#i-download"/></svg>
                    <?= ($plugin['price'] ?? 0) == 0 ? '免费获取' : '立即购买' ?>
                </button>
            </form>
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
        <svg width="32" height="32"><use href="#i-plugin"/></svg>
    </div>
    <div class="empty-state-text">暂无可用插件</div>
    <p style="font-size: 13px; color: var(--text-muted);">更多插件即将上线，敬请期待</p>
</div>
<?php endif; ?>

<script>
    function searchPlugins() {
        var keyword = document.getElementById('pluginSearch').value;
        window.location.href = '/user/plugin-market?keyword=' + encodeURIComponent(keyword);
    }
</script>
