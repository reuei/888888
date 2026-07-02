<div class="card">
    <h1 style="font-size: 22px; margin-bottom: 12px;"><?php echo h($product['name']); ?></h1>
    <div class="detail-meta">
        授权类型：<?php echo $product['license_type'] === 'domain' ? '域名授权' : '授权码'; ?>
        <?php if ($product['valid_days'] > 0): ?> | 有效期：<?php echo $product['valid_days']; ?> 天<?php else: ?> | 有效期：永久<?php endif; ?>
    </div>
    <div class="detail-price"><?php echo format_price($product['price']); ?></div>
    <div style="line-height: 1.8; margin-bottom: 24px;">
        <?php echo nl2br(h($product['description'])); ?>
    </div>

    <form method="post" action="<?php echo url('order/create'); ?>" style="display:flex; align-items:center; gap: 12px;">
        <input type="hidden" name="type" value="product">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <div class="form-group" style="margin-bottom:0; width: auto;">
            <label style="display:inline; margin-right: 8px;">数量</label>
            <input type="number" name="quantity" value="1" min="1" style="width: 80px; display:inline-block;">
        </div>
        <button type="submit" class="btn btn-lg">立即购买</button>
    </form>
</div>
