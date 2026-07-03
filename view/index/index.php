<?php
/**
 * 前台首页（重写版）
 * 依赖 layout/main.php 与 /static/css/style.css
 * 保留全部 PHP 变量与 goods_effective_price() 逻辑
 */
?>
<div class="hero animate-fade-in">
    <div class="hero-content">
        <h1><?php echo h(site_config('site_name', lang('site.name'))); ?></h1>
        <p><?php echo h(lang('home.slogan')); ?></p>
        <div class="hero-badges">
            <span><svg class="icon" aria-hidden="true"><use href="#icon-zap"></use></svg><?php echo h(lang('home.trust_auto')); ?></span>
            <span><svg class="icon" aria-hidden="true"><use href="#icon-shield"></use></svg><?php echo h(lang('home.trust_safe')); ?></span>
            <span><svg class="icon" aria-hidden="true"><use href="#icon-headphones"></use></svg><?php echo h(lang('home.trust_service')); ?></span>
        </div>
        <div class="hero-actions">
            <a href="<?php echo url('index/category'); ?>" class="btn btn-lg btn-light">
                <svg class="icon" aria-hidden="true"><use href="#icon-shopping-bag"></use></svg>
                <?php echo h(lang('home.buy_now')); ?>
            </a>
            <a href="<?php echo url('index/order'); ?>" class="btn btn-lg btn-outline-light">
                <svg class="icon" aria-hidden="true"><use href="#icon-search"></use></svg>
                <?php echo h(lang('order.query')); ?>
            </a>
        </div>
    </div>
    <div class="hero-float">
        <div class="float-card">
            <span class="float-ic green"><svg class="icon icon-sm" aria-hidden="true"><use href="#icon-check"></use></svg></span>
            订单自动发货中…
        </div>
        <div class="float-card">
            <span class="float-ic blue"><svg class="icon icon-sm" aria-hidden="true"><use href="#icon-gift"></use></svg></span>
            新用户首单优惠
        </div>
    </div>
</div>

<?php if (!empty($homeTop)): ?>
<div class="banner-grid reveal">
    <?php foreach ($homeTop as $ad): ?>
    <a href="<?php echo h($ad['link'] ?: 'javascript:;'); ?>" target="_blank" class="banner-item">
        <img src="<?php echo h($ad['image']); ?>" alt="<?php echo h($ad['title']); ?>">
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($articles)): ?>
<div class="notice-bar reveal">
    <strong>
        <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-bell"></use></svg>
        <?php echo h(lang('home.notice')); ?>
    </strong>
    <div class="notice-ticker">
        <div class="notice-ticker-inner">
            <?php foreach ($articles as $article): ?>
            <a href="<?php echo url('index/article', ['id' => $article['id']]); ?>"><?php echo h($article['title']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($categories)): ?>
<section class="section">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true"><use href="#icon-category"></use></svg>
            <?php echo h(lang('goods.category')); ?>
        </div>
        <a href="<?php echo url('index/category'); ?>" class="section-more">
            <?php echo h(lang('common.more')); ?>
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
        </a>
    </div>
    <div class="category-grid stagger">
        <?php foreach ($categories as $c): ?>
        <a href="<?php echo url('index/category', ['id' => $c['id']]); ?>" class="category-card">
            <div class="icon-box">
                <svg class="icon" aria-hidden="true"><use href="#icon-tag"></use></svg>
            </div>
            <div class="name"><?php echo h($c['name']); ?></div>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($homeBanner)): ?>
<section class="section">
    <div class="banner-grid reveal">
        <?php foreach ($homeBanner as $ad): ?>
        <a href="<?php echo h($ad['link'] ?: 'javascript:;'); ?>" target="_blank" class="banner-item">
            <img src="<?php echo h($ad['image']); ?>" alt="<?php echo h($ad['title']); ?>">
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php
function renderGoodsGrid($items, $tpl) {
    if (empty($items)) return;
    echo '<div class="goods-grid stagger">';
    foreach ($items as $item) {
        $eff = goods_effective_price($item);
        $stock = $eff['activity'] === 'seckill' ? max(0, (int)$item['seckill_stock'] - (int)$item['seckill_sold']) : $item['stock'];
        echo '<div class="goods-card">';
        echo '<a href="' . url('index/goods', ['id' => $item['id']]) . '">';
        echo '<div class="goods-cover">';
        if ($item['cover']) {
            echo '<img src="' . h($item['cover']) . '" alt="' . h($item['name']) . '">';
        } else {
            echo '<svg class="icon icon-xl" aria-hidden="true" style="opacity:.5"><use href="#icon-box"></use></svg>';
        }
        if ($eff['activity'] !== 'none') {
            echo '<span class="goods-badge">' . h($eff['label']) . '</span>';
        }
        echo '</div>';
        echo '<div class="goods-info">';
        echo '<div class="goods-name">' . h($item['name']) . '</div>';
        echo '<div class="goods-meta">';
        echo '<span class="goods-price">¥' . number_format($eff['price'], 2) . '</span>';
        if ($eff['activity'] !== 'none') {
            echo '<span class="goods-original">¥' . number_format($eff['original_price'], 2) . '</span>';
        }
        echo '</div>';
        echo '<div class="goods-sub">';
        echo '<span class="shop">' . h($item['shop_name'] ?? '-') . '</span>';
        echo '<span>已售 ' . $item['sold'] . '</span>';
        echo '</div>';
        echo '</div>';
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';
}
?>

<?php if (!empty($seckillGoods)): ?>
<section class="section">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true" style="color:#DC2626"><use href="#icon-zap"></use></svg>
            <?php echo h(lang('home.seckill')); ?>
            <span class="tag-pill tag-red">HOT</span>
        </div>
        <a href="<?php echo url('index/category', ['sort' => 'sold']); ?>" class="section-more">
            <?php echo h(lang('common.more')); ?>
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
        </a>
    </div>
    <?php renderGoodsGrid($seckillGoods, $tpl); ?>
</section>
<?php endif; ?>

<?php if (!empty($discountGoods)): ?>
<section class="section">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true" style="color:#D97706"><use href="#icon-coupon"></use></svg>
            <?php echo h(lang('home.discount')); ?>
            <span class="tag-pill tag-orange">SALE</span>
        </div>
        <a href="<?php echo url('index/category', ['sort' => 'sold']); ?>" class="section-more">
            <?php echo h(lang('common.more')); ?>
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
        </a>
    </div>
    <?php renderGoodsGrid($discountGoods, $tpl); ?>
</section>
<?php endif; ?>

<section class="section">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true" style="color:#EF4444"><use href="#icon-trending"></use></svg>
            <?php echo h(lang('home.hot_goods')); ?>
        </div>
        <a href="<?php echo url('index/category'); ?>" class="section-more">
            <?php echo h(lang('common.more')); ?>
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
        </a>
    </div>
    <?php if (empty($goods)): ?>
    <div class="card empty-state">
        <div class="empty-illust"><svg class="icon" aria-hidden="true"><use href="#icon-box"></use></svg></div>
        <h3><?php echo h($tpl['goods_empty_tip'] ?? '暂无上架商品'); ?></h3>
    </div>
    <?php else: ?>
    <?php renderGoodsGrid($goods, $tpl); ?>
    <?php endif; ?>
</section>

<?php if (!empty($newGoods)): ?>
<section class="section">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true" style="color:#2563EB"><use href="#icon-star"></use></svg>
            <?php echo h(lang('home.new_arrival')); ?>
        </div>
        <a href="<?php echo url('index/category', ['sort' => 'id']); ?>" class="section-more">
            <?php echo h(lang('common.more')); ?>
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
        </a>
    </div>
    <?php renderGoodsGrid($newGoods, $tpl); ?>
</section>
<?php endif; ?>

<?php if (!empty($hotMerchants)): ?>
<section class="section">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true"><use href="#icon-merchant"></use></svg>
            <?php echo h(lang('home.hot_merchants')); ?>
        </div>
    </div>
    <div class="merchant-grid stagger">
        <?php foreach ($hotMerchants as $m): ?>
        <a href="<?php echo url('index/category', ['merchant_id' => $m['id']]); ?>" class="merchant-card">
            <div class="merchant-avatar">
                <?php if ($m['avatar']): ?>
                <img src="<?php echo h($m['avatar']); ?>" alt="<?php echo h($m['shop_name']); ?>">
                <?php else: ?>
                <svg class="icon icon-lg" aria-hidden="true"><use href="#icon-merchant"></use></svg>
                <?php endif; ?>
            </div>
            <div class="merchant-name"><?php echo h($m['shop_name']); ?></div>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true"><use href="#icon-shield"></use></svg>
            <?php echo h(lang('home.trust_title')); ?>
        </div>
    </div>
    <div class="trust-grid stagger">
        <div class="trust-card">
            <div class="trust-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-zap"></use></svg></div>
            <div class="trust-title"><?php echo h(lang('home.trust_auto')); ?></div>
            <div class="trust-desc"><?php echo h(lang('home.trust_auto_desc')); ?></div>
        </div>
        <div class="trust-card">
            <div class="trust-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-shield"></use></svg></div>
            <div class="trust-title"><?php echo h(lang('home.trust_safe')); ?></div>
            <div class="trust-desc"><?php echo h(lang('home.trust_safe_desc')); ?></div>
        </div>
        <div class="trust-card">
            <div class="trust-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-headphones"></use></svg></div>
            <div class="trust-title"><?php echo h(lang('home.trust_service')); ?></div>
            <div class="trust-desc"><?php echo h(lang('home.trust_service_desc')); ?></div>
        </div>
    </div>
</section>

<?php if (($tpl['home_show_stats'] ?? '1') === '1'): ?>
<section class="section">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true"><use href="#icon-stat"></use></svg>
            <?php echo h(lang('home.platform_stats')); ?>
        </div>
    </div>
    <div class="platform-stats reveal">
        <div>
            <div class="stat-value"><?php echo number_format($platformStats['total_orders'] ?? 0); ?></div>
            <div class="stat-label"><?php echo h(lang('home.orders_count')); ?></div>
        </div>
        <div>
            <div class="stat-value">¥<?php echo number_format($platformStats['total_amount'] ?? 0, 2); ?></div>
            <div class="stat-label"><?php echo h(lang('home.amount_count')); ?></div>
        </div>
        <div>
            <div class="stat-value">7×24</div>
            <div class="stat-label"><?php echo h(lang('home.trust_service')); ?></div>
        </div>
        <div>
            <div class="stat-value">100%</div>
            <div class="stat-label"><?php echo h(lang('home.trust_auto')); ?></div>
        </div>
    </div>
</section>
<?php endif; ?>
