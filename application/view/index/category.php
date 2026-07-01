<div class="card" style="margin-bottom: 16px;">
    <form method="get" action="<?php echo url('index/category'); ?>" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
        <?php if ($category): ?>
        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
        <?php endif; ?>
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="<?php echo h(lang('goods.search_placeholder')); ?>" style="flex:1; min-width: 160px; max-width: 260px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        <input type="number" name="min_price" value="<?php echo h($minPrice); ?>" placeholder="<?php echo h(lang('goods.min_price')); ?>" min="0" step="0.01" style="width: 100px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        <span style="color:#94A3B8;">-</span>
        <input type="number" name="max_price" value="<?php echo h($maxPrice); ?>" placeholder="<?php echo h(lang('goods.max_price')); ?>" min="0" step="0.01" style="width: 100px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        <label style="display:flex; align-items:center; gap:6px; font-size:13px; color:#475569; cursor:pointer;">
            <input type="checkbox" name="has_stock" value="1" <?php echo $hasStock === '1' ? 'checked' : ''; ?>> <?php echo h(lang('goods.has_stock')); ?>
        </label>
        <button type="submit" class="btn"><?php echo h(lang('nav.search')); ?></button>
        <?php if ($keyword || $minPrice !== '' || $maxPrice !== '' || $hasStock === '1'): ?>
        <a href="<?php echo url('index/category', ['id' => $category['id'] ?? 0]); ?>" class="btn btn-outline"><?php echo h(lang('common.reset')); ?></a>
        <?php endif; ?>
    </form>
</div>

<div class="section-title">
    <span><?php echo $category ? h($category['name']) : h($tpl['goods_seo_title'] ?? lang('nav.category')); ?></span>
    <span style="font-size: 13px; color: #64748B; font-weight: normal;"><?php echo h(lang('common.total', ['count' => $total])); ?></span>
    <div style="margin-left: auto;">
        <select onchange="location.href=this.value" style="padding: 6px 10px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 13px;">
            <option value="<?php echo url('index/category', ['id' => $category['id'] ?? 0, 'keyword' => $keyword, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'has_stock' => $hasStock, 'sort' => 'sold']); ?>" <?php echo $sort === 'sold' ? 'selected' : ''; ?>><?php echo h(lang('goods.sort_sold')); ?></option>
            <option value="<?php echo url('index/category', ['id' => $category['id'] ?? 0, 'keyword' => $keyword, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'has_stock' => $hasStock, 'sort' => 'id']); ?>" <?php echo $sort === 'id' ? 'selected' : ''; ?>><?php echo h(lang('goods.sort_newest')); ?></option>
            <option value="<?php echo url('index/category', ['id' => $category['id'] ?? 0, 'keyword' => $keyword, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'has_stock' => $hasStock, 'sort' => 'price_asc']); ?>" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>><?php echo h(lang('goods.sort_price_asc')); ?></option>
            <option value="<?php echo url('index/category', ['id' => $category['id'] ?? 0, 'keyword' => $keyword, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'has_stock' => $hasStock, 'sort' => 'price_desc']); ?>" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>><?php echo h(lang('goods.sort_price_desc')); ?></option>
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
<div class="grid">    <?php foreach ($list as $item): ?>
    <?php $eff = goods_effective_price($item); ?>
    <div class="goods-card">
        <a href="<?php echo url('index/goods', ['id' => $item['id']]); ?>">
            <div class="goods-cover" style="position:relative;">
                <?php if ($item['cover']): ?>
                <img src="<?php echo h($item['cover']); ?>" alt="<?php echo h($item['name']); ?>">
                <?php else: ?>
                <?php echo h(lang('goods.no_image')); ?>
                <?php endif; ?>
                <?php if ($eff['activity'] !== 'none'): ?>
                <span style="position:absolute; top:8px; left:8px; background:#EF4444; color:#fff; font-size:12px; padding:2px 8px; border-radius:4px;"><?php echo $eff['label']; ?></span>
                <?php endif; ?>
            </div>
            <div class="goods-info">
                <div class="goods-name"><?php echo h($item['name']); ?></div>
                <div class="goods-meta">
                    <span class="goods-price">¥<?php echo number_format($eff['price'], 2); ?></span>
                    <?php if ($eff['activity'] !== 'none'): ?>
                    <span style="font-size:12px; color:#94A3B8; text-decoration:line-through;">¥<?php echo number_format($eff['original_price'], 2); ?></span>
                    <?php endif; ?>
                    <?php if (($tpl['goods_show_sold'] ?? '1') === '1'): ?>
                    <span class="goods-sold">已售 <?php echo $item['sold']; ?></span>
                    <?php endif; ?>
                </div>
                <?php if (($tpl['goods_show_stock'] ?? '1') === '1' || ($tpl['goods_show_merchant'] ?? '1') === '1'): ?>
                <div style="font-size: 12px; color: #94A3B8; margin-top: 6px;">
                    <?php if (($tpl['goods_show_stock'] ?? '1') === '1'): ?>
                    <span>库存 <?php echo $eff['activity'] === 'seckill' ? max(0, (int)$item['seckill_stock'] - (int)$item['seckill_sold']) : $item['stock']; ?></span>
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
    <a href="<?php echo url('index/category', ['page' => $page - 1, 'id' => $category['id'] ?? 0, 'keyword' => $keyword, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'has_stock' => $hasStock, 'sort' => $sort]); ?>" class="btn btn-outline"><?php echo h(lang('common.prev')); ?></a>
    <?php endif; ?>
    <span style="padding: 8px 16px; color: #64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
    <?php if ($page < $totalPages): ?>
    <a href="<?php echo url('index/category', ['page' => $page + 1, 'id' => $category['id'] ?? 0, 'keyword' => $keyword, 'min_price' => $minPrice, 'max_price' => $maxPrice, 'has_stock' => $hasStock, 'sort' => $sort]); ?>" class="btn btn-outline"><?php echo h(lang('common.next')); ?></a>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>
