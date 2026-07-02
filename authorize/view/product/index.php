<div class="card">
    <div class="section-title">授权产品</div>
    <form method="get" action="<?php echo url('product'); ?>" class="search-bar" style="margin-bottom: 20px;">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索产品名称">
        <button type="submit" class="btn">搜索</button>
    </form>

    <?php if (empty($list)): ?>
    <div class="empty-tip">暂无产品</div>
    <?php else: ?>
    <div class="grid">
        <?php foreach ($list as $item): ?>
        <a class="item-card" href="<?php echo url('product/detail', ['id' => $item['id']]); ?>">
            <div class="item-cover">授权产品</div>
            <div class="item-info">
                <div class="item-name"><?php echo h($item['name']); ?></div>
                <div class="item-meta">
                    <span class="item-price"><?php echo format_price($item['price']); ?></span>
                    <span class="tag <?php echo $item['license_type'] === 'domain' ? 'tag-blue' : 'tag-green'; ?>"><?php echo $item['license_type'] === 'domain' ? '域名授权' : '授权码'; ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php echo pagination($total, $page, 12, url('product', ['keyword' => $keyword, 'page' => '{page}'])); ?>
    <?php endif; ?>
</div>
