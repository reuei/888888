<style>
/* Hero */
.hero {
    position: relative;
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 50%, #0F172A 100%);
    color: #fff;
    border-radius: 16px;
    padding: 64px 32px;
    text-align: center;
    margin-bottom: 24px;
    overflow: hidden;
}
.hero::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.12) 0%, transparent 40%),
                radial-gradient(circle at 70% 70%, rgba(96,165,250,0.18) 0%, transparent 40%);
    animation: heroPulse 8s ease-in-out infinite;
}
@keyframes heroPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
.hero-content { position: relative; z-index: 1; max-width: 640px; margin: 0 auto; }
.hero h1 { font-size: 36px; margin-bottom: 16px; font-weight: 700; letter-spacing: -0.5px; }
.hero p { font-size: 16px; opacity: 0.9; margin-bottom: 28px; line-height: 1.6; }
.hero-badges {
    display: inline-flex;
    gap: 16px;
    margin-bottom: 28px;
    flex-wrap: wrap;
    justify-content: center;
}
.hero-badges span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 20px;
    font-size: 13px;
}
.hero .btn-primary {
    background: #fff;
    color: #2563EB;
    border-color: #fff;
    font-weight: 600;
    padding: 12px 28px;
    margin-right: 12px;
}
.hero .btn-outline-light {
    background: transparent;
    color: #fff;
    border-color: rgba(255,255,255,0.5);
    padding: 12px 28px;
}
.hero .btn-outline-light:hover { background: rgba(255,255,255,0.1); }
.hero-float {
    position: absolute;
    right: 5%;
    top: 50%;
    transform: translateY(-50%);
    width: 160px;
    display: none;
}
.hero-float .float-card {
    background: rgba(255,255,255,0.95);
    color: #1F2937;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    animation: floatCard 4s ease-in-out infinite;
    text-align: left;
}
.hero-float .float-card:nth-child(2) { animation-delay: 1s; }
@keyframes floatCard {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Section */
.section { margin-bottom: 32px; }
.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
}
.section-title2 {
    font-size: 20px;
    font-weight: 700;
    color: #1F2937;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-title2 .tag-pill {
    font-size: 12px;
    font-weight: 500;
    padding: 3px 10px;
    border-radius: 20px;
}
.section-more {
    font-size: 13px;
    color: #2563EB;
    font-weight: 500;
}
.section-more:hover { text-decoration: underline; }

/* Notice */
.notice-bar {
    background: linear-gradient(90deg, #FFFBEB 0%, #FEF3C7 100%);
    border: 1px solid #FDE68A;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 13px;
    overflow: hidden;
}
.notice-bar strong { color: #D97706; flex-shrink: 0; }
.notice-ticker {
    flex: 1;
    overflow: hidden;
    white-space: nowrap;
    position: relative;
}
.notice-ticker-inner {
    display: inline-block;
    animation: ticker 20s linear infinite;
}
@keyframes ticker {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}
.notice-bar a { color: #2563EB; margin: 0 8px; }
.notice-bar a:hover { text-decoration: underline; }

/* Category cards */
.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}
.category-card {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 18px 10px;
    text-align: center;
    transition: all 0.25s;
}
.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(37,99,235,0.12);
    border-color: #2563EB;
}
.category-card .icon {
    width: 44px;
    height: 44px;
    background: #EFF6FF;
    color: #2563EB;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin: 0 auto 10px;
}
.category-card .name {
    font-size: 13px;
    font-weight: 500;
    color: #1F2937;
}

/* Banner */
.banner-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 12px;
    margin-bottom: 24px;
}
.banner-item {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #E2E8F0;
    display: block;
    position: relative;
}
.banner-item img {
    width: 100%;
    height: 110px;
    object-fit: cover;
    display: block;
    transition: transform 0.4s;
}
.banner-item:hover img { transform: scale(1.05); }

/* Goods */
.goods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
}
.goods-card2 {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.25s;
}
.goods-card2:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.1);
    border-color: #BFDBFE;
}
.goods-card2 a { display: block; }
.goods-cover2 {
    height: 150px;
    background: #F1F5F9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94A3B8;
    font-size: 13px;
    position: relative;
    overflow: hidden;
}
.goods-cover2 img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
.goods-card2:hover .goods-cover2 img { transform: scale(1.06); }
.goods-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: #EF4444;
    color: #fff;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 500;
}
.goods-info2 { padding: 14px; }
.goods-name2 {
    font-size: 14px;
    color: #1F2937;
    font-weight: 500;
    margin-bottom: 8px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.goods-meta2 {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 8px;
}
.goods-price2 { color: #EF4444; font-weight: 700; font-size: 18px; }
.goods-original { font-size: 12px; color: #94A3B8; text-decoration: line-through; }
.goods-sub2 {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    color: #64748B;
}
.goods-sub2 .shop { max-width: 50%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* Merchants */
.merchant-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 14px;
}
.merchant-card {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 18px 12px;
    text-align: center;
    transition: all 0.25s;
}
.merchant-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}
.merchant-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #F1F5F9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 10px;
    overflow: hidden;
}
.merchant-avatar img { width: 100%; height: 100%; object-fit: cover; }
.merchant-name { font-size: 14px; font-weight: 500; color: #1F2937; }

/* Trust */
.trust-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}
.trust-card {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    transition: all 0.25s;
}
.trust-card:hover { border-color: #2563EB; box-shadow: 0 8px 20px rgba(37,99,235,0.08); }
.trust-icon {
    width: 52px;
    height: 52px;
    background: #EFF6FF;
    color: #2563EB;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 14px;
}
.trust-title { font-size: 16px; font-weight: 600; color: #1F2937; margin-bottom: 6px; }
.trust-desc { font-size: 13px; color: #64748B; }

/* Platform stats */
.platform-stats {
    background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
    color: #fff;
    border-radius: 16px;
    padding: 40px 24px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 24px;
    text-align: center;
}
.platform-stats .stat-value { font-size: 28px; font-weight: 700; margin-bottom: 6px; }
.platform-stats .stat-label { font-size: 13px; opacity: 0.7; }

/* Empty */
.empty-tip { text-align: center; padding: 60px 20px; color: #64748B; }

@media (min-width: 992px) {
    .hero-float { display: block; }
    .hero { text-align: left; padding-left: 64px; }
    .hero-content { margin: 0; }
}
@media (max-width: 768px) {
    .hero { padding: 40px 20px; }
    .hero h1 { font-size: 26px; }
    .hero-badges { gap: 8px; }
    .goods-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
    .goods-cover2 { height: 120px; }
    .category-grid { grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 10px; }
}
</style>

<div class="hero">
    <div class="hero-content">
        <h1><?php echo h(site_config('site_name', lang('site.name'))); ?></h1>
        <p><?php echo h(lang('home.slogan')); ?></p>
        <div class="hero-badges">
            <span>⚡ <?php echo h(lang('home.trust_auto')); ?></span>
            <span>🛡️ <?php echo h(lang('home.trust_safe')); ?></span>
            <span>🎧 <?php echo h(lang('home.trust_service')); ?></span>
        </div>
        <div>
            <a href="<?php echo url('index/category'); ?>" class="btn btn-lg btn-primary"><?php echo h(lang('home.buy_now')); ?></a>
            <a href="<?php echo url('index/order'); ?>" class="btn btn-lg btn-outline-light"><?php echo h(lang('order.query')); ?></a>
        </div>
    </div>
    <div class="hero-float">
        <div class="float-card">✅ 订单自动发货中…</div>
        <div class="float-card">🎉 新用户首单优惠</div>
    </div>
</div>

<?php if (!empty($homeTop)): ?>
<div class="banner-grid">
    <?php foreach ($homeTop as $ad): ?>
    <a href="<?php echo h($ad['link'] ?: 'javascript:;'); ?>" target="_blank" class="banner-item">
        <img src="<?php echo h($ad['image']); ?>" alt="<?php echo h($ad['title']); ?>">
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($articles)): ?>
<div class="notice-bar">
    <strong>📢 <?php echo h(lang('home.notice')); ?></strong>
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
<div class="section">
    <div class="section-header">
        <div class="section-title2"><?php echo h(lang('goods.category')); ?></div>
        <a href="<?php echo url('index/category'); ?>" class="section-more"><?php echo h(lang('common.more')); ?> →</a>
    </div>
    <div class="category-grid">
        <?php foreach ($categories as $c): ?>
        <a href="<?php echo url('index/category', ['id' => $c['id']]); ?>" class="category-card">
            <div class="icon">🏷️</div>
            <div class="name"><?php echo h($c['name']); ?></div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($homeBanner)): ?>
<div class="section">
    <div class="banner-grid">
        <?php foreach ($homeBanner as $ad): ?>
        <a href="<?php echo h($ad['link'] ?: 'javascript:;'); ?>" target="_blank" class="banner-item">
            <img src="<?php echo h($ad['image']); ?>" alt="<?php echo h($ad['title']); ?>">
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php
function renderGoodsGrid($items, $tpl) {
    if (empty($items)) return;
    echo '<div class="goods-grid">';
    foreach ($items as $item) {
        $eff = goods_effective_price($item);
        $stock = $eff['activity'] === 'seckill' ? max(0, (int)$item['seckill_stock'] - (int)$item['seckill_sold']) : $item['stock'];
        echo '<div class="goods-card2">';
        echo '<a href="' . url('index/goods', ['id' => $item['id']]) . '">';
        echo '<div class="goods-cover2">';
        if ($item['cover']) {
            echo '<img src="' . h($item['cover']) . '" alt="' . h($item['name']) . '">';
        } else {
            echo h(lang('goods.no_image'));
        }
        if ($eff['activity'] !== 'none') {
            echo '<span class="goods-badge">' . h($eff['label']) . '</span>';
        }
        echo '</div>';
        echo '<div class="goods-info2">';
        echo '<div class="goods-name2">' . h($item['name']) . '</div>';
        echo '<div class="goods-meta2">';
        echo '<span class="goods-price2">¥' . number_format($eff['price'], 2) . '</span>';
        if ($eff['activity'] !== 'none') {
            echo '<span class="goods-original">¥' . number_format($eff['original_price'], 2) . '</span>';
        }
        echo '</div>';
        echo '<div class="goods-sub2">';
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
<div class="section">
    <div class="section-header">
        <div class="section-title2">
            ⚡ <?php echo h(lang('home.seckill')); ?>
            <span class="tag-pill" style="background:#FEF2F2; color:#DC2626;">HOT</span>
        </div>
        <a href="<?php echo url('index/category', ['sort' => 'sold']); ?>" class="section-more"><?php echo h(lang('common.more')); ?> →</a>
    </div>
    <?php renderGoodsGrid($seckillGoods, $tpl); ?>
</div>
<?php endif; ?>

<?php if (!empty($discountGoods)): ?>
<div class="section">
    <div class="section-header">
        <div class="section-title2">
            🏷️ <?php echo h(lang('home.discount')); ?>
            <span class="tag-pill" style="background:#FFFBEB; color:#D97706;">SALE</span>
        </div>
        <a href="<?php echo url('index/category', ['sort' => 'sold']); ?>" class="section-more"><?php echo h(lang('common.more')); ?> →</a>
    </div>
    <?php renderGoodsGrid($discountGoods, $tpl); ?>
</div>
<?php endif; ?>

<div class="section">
    <div class="section-header">
        <div class="section-title2">🔥 <?php echo h(lang('home.hot_goods')); ?></div>
        <a href="<?php echo url('index/category'); ?>" class="section-more"><?php echo h(lang('common.more')); ?> →</a>
    </div>
    <?php if (empty($goods)): ?>
    <div class="card empty-tip"><?php echo h($tpl['goods_empty_tip'] ?? '暂无上架商品'); ?></div>
    <?php else: ?>
    <?php renderGoodsGrid($goods, $tpl); ?>
    <?php endif; ?>
</div>

<?php if (!empty($newGoods)): ?>
<div class="section">
    <div class="section-header">
        <div class="section-title2">✨ <?php echo h(lang('home.new_arrival')); ?></div>
        <a href="<?php echo url('index/category', ['sort' => 'id']); ?>" class="section-more"><?php echo h(lang('common.more')); ?> →</a>
    </div>
    <?php renderGoodsGrid($newGoods, $tpl); ?>
</div>
<?php endif; ?>

<?php if (!empty($hotMerchants)): ?>
<div class="section">
    <div class="section-header">
        <div class="section-title2">🏪 <?php echo h(lang('home.hot_merchants')); ?></div>
    </div>
    <div class="merchant-grid">
        <?php foreach ($hotMerchants as $m): ?>
        <a href="<?php echo url('index/category', ['merchant_id' => $m['id']]); ?>" class="merchant-card">
            <div class="merchant-avatar">
                <?php if ($m['avatar']): ?>
                <img src="<?php echo h($m['avatar']); ?>" alt="<?php echo h($m['shop_name']); ?>">
                <?php else: ?>
                🏪
                <?php endif; ?>
            </div>
            <div class="merchant-name"><?php echo h($m['shop_name']); ?></div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="section">
    <div class="section-header">
        <div class="section-title2"><?php echo h(lang('home.trust_title')); ?></div>
    </div>
    <div class="trust-grid">
        <div class="trust-card">
            <div class="trust-icon">⚡</div>
            <div class="trust-title"><?php echo h(lang('home.trust_auto')); ?></div>
            <div class="trust-desc"><?php echo h(lang('home.trust_auto_desc')); ?></div>
        </div>
        <div class="trust-card">
            <div class="trust-icon">🛡️</div>
            <div class="trust-title"><?php echo h(lang('home.trust_safe')); ?></div>
            <div class="trust-desc"><?php echo h(lang('home.trust_safe_desc')); ?></div>
        </div>
        <div class="trust-card">
            <div class="trust-icon">🎧</div>
            <div class="trust-title"><?php echo h(lang('home.trust_service')); ?></div>
            <div class="trust-desc"><?php echo h(lang('home.trust_service_desc')); ?></div>
        </div>
    </div>
</div>

<?php if (($tpl['home_show_stats'] ?? '1') === '1'): ?>
<div class="section">
    <div class="section-header">
        <div class="section-title2">📊 <?php echo h(lang('home.platform_stats')); ?></div>
    </div>
    <div class="platform-stats">
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
</div>
<?php endif; ?>
