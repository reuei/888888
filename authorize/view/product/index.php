<div class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="product" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>授权产品</span>
    </div>
    <form method="get" action="<?php echo url('product'); ?>" class="search-bar">
        <div style="position:relative;flex:1;">
            <i data-icon="search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;"></i>
            <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索产品名称" style="padding-left:42px;">
        </div>
        <button type="submit" class="btn"><i data-icon="search" class="svg-icon-sm"></i>搜索</button>
    </form>

    <?php if (empty($list)): ?>
    <div class="empty-state">
        <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M21 76V44a2 2 0 0 1 1-1.73l36-20.7a2 2 0 0 1 2 0l36 20.7A2 2 0 0 1 97 44v32a2 2 0 0 1-1 1.73l-36 20.7a2 2 0 0 1-2 0l-36-20.7A2 2 0 0 1 21 76z" stroke="#c4b5fd" stroke-width="2.5"/>
            <path d="M22 44l36 20 36-20 M58 84V64" stroke="#c4b5fd" stroke-width="2.5"/>
        </svg>
        <h4>暂无产品</h4>
        <p>暂未上架任何授权产品</p>
    </div>
    <?php else: ?>
    <div class="grid">
        <?php foreach ($list as $item): ?>
        <a class="item-card product-card fade-in-up" href="<?php echo url('product/detail', ['id' => $item['id']]); ?>">
            <div class="item-cover">
                <svg class="cover-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
                <span>授权产品</span>
            </div>
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
