<div class="hero">
    <h1>QEEFG 寄售系统售卖网站</h1>
    <p>授权码销售、域名授权、插件市场一站式解决方案</p>
</div>

<div class="card">
    <div class="section-title">
        <span>授权产品</span>
        <a href="<?php echo url('product'); ?>">查看更多 →</a>
    </div>
    <?php if (empty($products)): ?>
    <div class="empty-tip">暂无上架产品</div>
    <?php else: ?>
    <div class="grid">
        <?php foreach ($products as $item): ?>
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
    <?php endif; ?>
</div>

<div class="card">
    <div class="section-title">
        <span>插件市场</span>
        <a href="<?php echo url('plugin'); ?>">查看更多 →</a>
    </div>
    <?php if (empty($plugins)): ?>
    <div class="empty-tip">暂无上架插件</div>
    <?php else: ?>
    <div class="grid">
        <?php foreach ($plugins as $item): ?>
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
    <?php endif; ?>
</div>

<div class="card">
    <div class="section-title">平台公告</div>
    <?php if (empty($articles)): ?>
    <div class="empty-tip">暂无公告</div>
    <?php else: ?>
    <ul class="article-list">
        <?php foreach ($articles as $item): ?>
        <li>
            <a href="<?php echo url('index/article', ['id' => $item['id']]); ?>"><?php echo h($item['title']); ?></a>
            <span><?php echo date('Y-m-d', strtotime($item['create_time'])); ?></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>
