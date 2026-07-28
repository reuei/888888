<div class="user-breadcrumb">
    <span>用户中心</span> / <span>产品中心</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">产品中心</h1>

<?php if (!empty($products)): ?>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <?php foreach ($products as $product): ?>
    <div class="card">
        <h3 class="card-title" style="font-size: 18px;"><?= htmlspecialchars($product['name'] ?? '') ?></h3>
        <p style="color: #687690; font-size: 14px; margin-bottom: 16px; min-height: 44px;"><?= htmlspecialchars($product['description'] ?? '暂无描述') ?></p>
        <div style="font-size: 24px; font-weight: 700; color: #e74c3c; margin-bottom: 16px;">
            ¥<?= number_format($product['price'] ?? 0, 2) ?><span style="font-size: 12px; color: #999; font-weight: 400;"> / 永久</span>
        </div>
        <form method="POST" action="/user/buyProduct" data-ajax="true">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
            <button type="submit" class="btn btn-primary btn-block">立即购买</button>
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
<div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无可用产品</div>
<?php endif; ?>