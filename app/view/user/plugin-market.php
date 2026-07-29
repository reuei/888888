<div class="user-breadcrumb">
    <span>用户中心</span> / <span>插件市场</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">插件市场</h1>

<!-- User Info Header -->
<div style="display: flex; gap: 12px; margin-bottom: 24px;">
    <div style="background: #fffbe6; border: 1px solid #ffe58f; padding: 10px 18px; font-size: 14px; color: #8c6d00; display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16"><use href="#i-mail"/></svg>
        <span><?= htmlspecialchars($user['email'] ?? '') ?></span>
    </div>
    <div style="background: #fffbe6; border: 1px solid #ffe58f; padding: 10px 18px; font-size: 14px; color: #8c6d00; display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16"><use href="#i-phone"/></svg>
        <span><?= htmlspecialchars($user['phone'] ?? '未绑定手机') ?></span>
    </div>
</div>

<!-- Plugin Grid -->
<?php if (!empty($plugins)): ?>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <?php foreach ($plugins as $plugin): ?>
    <div class="card">
        <h3 class="card-title" style="font-size: 18px; margin-bottom: 8px;"><?= htmlspecialchars($plugin['name'] ?? '') ?></h3>
        <p style="color: #687690; font-size: 14px; margin-bottom: 16px; min-height: 44px;"><?= htmlspecialchars($plugin['description'] ?? '暂无描述') ?></p>
        <div style="font-size: 24px; font-weight: 700; color: #e74c3c; margin-bottom: 16px;">
            <?php if (($plugin['price'] ?? 0) == 0): ?>
            <span style="color: #10b981;">免费</span>
            <?php else: ?>
            ¥<?= number_format($plugin['price'] ?? 0, 2) ?>
            <?php endif; ?>
        </div>
        <form method="POST" action="/user/buy" data-ajax="false">
            <input type="hidden" name="plugin_id" value="<?= $plugin['id'] ?? 0 ?>">
            <button type="submit" class="btn btn-primary btn-block">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-download"/></svg>
                <?= ($plugin['price'] ?? 0) == 0 ? '免费获取' : '立即购买' ?>
            </button>
        </form>
    </div>
    <?php endforeach; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php else: ?>
<div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">
    <svg width="48" height="48" style="color: #c0c8d8; margin-bottom: 12px; display: block; margin-left: auto; margin-right: auto;"><use href="#i-plugin"/></svg>
    暂无可用插件
</div>
<?php endif; ?>