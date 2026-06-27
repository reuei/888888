<div class="card" style="margin-bottom: 16px;">
    <form method="get" action="<?php echo url('index/category'); ?>" style="display: flex; gap: 12px;">
        <?php if ($category): ?>
        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
        <?php endif; ?>
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索商品" style="flex:1; max-width: 320px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        <button type="submit" class="btn">搜索</button>
    </form>
</div>

<div class="section-title">
    <span><?php echo $category ? h($category['name']) : h($tpl['goods_seo_title'] ?? '全部商品'); ?></span>
    <span style="font-size: 13px; color: #64748B; font-weight: normal;">共 <?php echo $total; ?> 件商品</span>
    <div style="margin-left: auto;">
        <select onchange="location.href=this.value" style="padding: 6px 10px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 13px;">
            <option value="<?php echo url('index/category', ['id' => $category['id'] ?? 0, 'keyword' => $keyword, 'sort' => 'sold']); ?>" <?php echo $sort === 'sold' ? 'selected' : ''; ?>>按销量</option>
            <option value="<?php echo url('index/category', ['id' => $category['id'] ?? 0, 'keyword' => $keyword, 'sort' => 'id']); ?>" <?php echo $sort === 'id' ? 'selected' : ''; ?>>按最新</option>
            <option value="<?php echo url('index/category', ['id' => $category['id'] ?? 0, 'keyword' => $keyword, 'sort' => 'price_asc']); ?>" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>价格从低到高</option>
            <option value="<?php echo url('index/category', ['id' => $category['id'] ?? 0, 'keyword' => $keyword, 'sort' => 'price_desc']); ?>" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>价格从高到低</option>
        </select>
    </div>
</div>

<?php if (!empty($categoryTop)): ?>
<div style="margin-bottom: 16px;">
    <?php foreach ($categoryTop as $ad): ?>
    <a href="<?php echo h($ad['link'] ?: 'javascript:;'); ?>" target="_blank" style="display: block; border-radius: 8px; overflow: hidden; border: 1px solid #E2E8F0; margin-bottom: 12px;">
        <img src="<?php echo h($ad['image']); ?>" alt="<?php echo h($ad['title']); ?>" style="width: 100%; height: 120px; object-fit: cover; display: block;">
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (empty($list)): ?>
<div class="card empty-tip"><?php echo h($tpl['goods_empty_tip'] ?? '暂无商品'); ?></div>
<?php else: ?>
<div class="grid">
    <?php foreach ($list as $item): ?>
    <div class="goods-card">
        <a href="<?php echo url('index/goods', ['id' => $item['id']]); ?>">
            <div class="goods-cover">
                <?php if ($item['cover']): ?>
                <img src="<?php echo h($item['cover']); ?>" alt="<?php echo h($item['name']); ?>">
                <?php else: ?>
                暂无图片
                <?php endif; ?>
            </div>
            <div class="goods-info">
                <div class="goods-name"><?php echo h($item['name']); ?></div>
                <div class="goods-meta">
                    <span class="goods-price">¥<?php echo $item['price']; ?></span>
                    <?php if (($tpl['goods_show_sold'] ?? '1') === '1'): ?>
                    <span class="goods-sold">已售 <?php echo $item['sold']; ?></span>
                    <?php endif; ?>
                </div>
                <?php if (($tpl['goods_show_stock'] ?? '1') === '1' || ($tpl['goods_show_merchant'] ?? '1') === '1'): ?>
                <div style="font-size: 12px; color: #94A3B8; margin-top: 6px;">
                    <?php if (($tpl['goods_show_stock'] ?? '1') === '1'): ?>
                    <span>库存 <?php echo $item['stock']; ?></span>
                    <?php endif; ?>
                    <?php if (($tpl['goods_show_merchant'] ?? '1') === '1'): ?>
                    <span><?php echo ($tpl['goods_show_stock'] ?? '1') === '1' ? ' | ' : ''; ?><?php echo h($item['shop_name'] ?? '-'); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($totalPages > 1): ?>
<div style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
    <?php if ($page > 1): ?>
    <a href="<?php echo url('index/category') . '?page=' . ($page - 1) . '&id=' . ($category['id'] ?? 0) . '&keyword=' . urlencode($keyword) . '&sort=' . $sort; ?>" class="btn btn-outline">上一页</a>
    <?php endif; ?>
    <span style="padding: 8px 16px; color: #64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
    <?php if ($page < $totalPages): ?>
    <a href="<?php echo url('index/category') . '?page=' . ($page + 1) . '&id=' . ($category['id'] ?? 0) . '&keyword=' . urlencode($keyword) . '&sort=' . $sort; ?>" class="btn btn-outline">下一页</a>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>
