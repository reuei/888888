<div class="card">
    <div class="section-title">插件市场</div>
    <form method="get" action="<?php echo url('plugin'); ?>" class="search-bar" style="margin-bottom: 20px;">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索插件">
        <button type="submit" class="btn">搜索</button>
    </form>

    <?php if (empty($list)): ?>
    <div class="empty-tip">暂无插件</div>
    <?php else: ?>
    <div class="grid">
        <?php foreach ($list as $item): ?>
        <a class="item-card" href="<?php echo url('plugin/detail', ['id' => $item['id']]); ?>">
            <div class="item-cover">插件</div>
            <div class="item-info">
                <div class="item-name"><?php echo h($item['name']); ?></div>
                <div class="item-meta">
                    <span class="item-price"><?php echo $item['price'] > 0 ? format_price($item['price']) : '免费'; ?></span>
                    <span class="item-sold"><?php echo h($item['author']); ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php echo pagination($total, $page, 12, url('plugin', ['keyword' => $keyword, 'page' => '{page}'])); ?>
    <?php endif; ?>
</div>
