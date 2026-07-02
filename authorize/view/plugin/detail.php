<div class="card">
    <h1 style="font-size: 22px; margin-bottom: 12px;"><?php echo h($plugin['name']); ?></h1>
    <div class="detail-meta">
        作者：<?php echo h($plugin['author']); ?> | 版本：<?php echo h($plugin['version']); ?> | 下载量：<?php echo $plugin['download_count']; ?>
    </div>
    <div class="detail-price"><?php echo $plugin['price'] > 0 ? format_price($plugin['price']) : '免费'; ?></div>
    <div style="line-height: 1.8; margin-bottom: 24px;">
        <?php echo nl2br(h($plugin['description'])); ?>
    </div>

    <?php if ($owned): ?>
        <button class="btn btn-success btn-lg" disabled>已拥有</button>
    <?php else: ?>
        <form method="post" action="<?php echo url('order/create'); ?>" style="display:inline;">
            <input type="hidden" name="type" value="plugin">
            <input type="hidden" name="id" value="<?php echo $plugin['id']; ?>">
            <button type="submit" class="btn btn-lg"><?php echo $plugin['price'] > 0 ? '立即购买' : '免费领取'; ?></button>
        </form>
    <?php endif; ?>
</div>
