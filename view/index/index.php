<?php
/**
 * 前台首页（对标 entropy.slmsns.com + m.xingluo.cyou 重写版）
 * 依赖 layout/main.php 与 /static/css/style.css
 * 保留全部 PHP 变量与 goods_effective_price() 逻辑
 * PHP 8.2 兼容
 */

// 变量兜底（保留控制器传入的全部变量，未定义时不报错）
$goods          = $goods ?? [];
$articles        = $articles ?? [];
$helps           = $helps ?? [];
$categories      = $categories ?? [];
$hotMerchants    = $hotMerchants ?? [];
$homeBanner      = $homeBanner ?? [];
$homeTop         = $homeTop ?? [];
$seckillGoods    = $seckillGoods ?? [];
$discountGoods   = $discountGoods ?? [];
$newGoods        = $newGoods ?? [];
$platformStats   = $platformStats ?? ['total_orders' => 0, 'total_amount' => 0];
$tpl             = $tpl ?? [];
$banners         = $banners ?? $homeBanner ?? [];

// 热门商品取前 8 条
$hotGoods = is_array($goods) ? array_slice($goods, 0, 8) : [];

// 4 个迷你统计
$onlineGoodsCount = is_array($goods) ? count($goods) : 0;
$todayOrders       = (int) ($platformStats['total_orders'] ?? 0);
$activeMerchants   = is_array($hotMerchants) ? count($hotMerchants) : 0;
$totalAmount       = (float) ($platformStats['total_amount'] ?? 0);

// $helps 为空时的 4 个固定帮助链接
$fixedHelps = [
    ['title' => '如何购买商品', 'icon' => 'shopping-bag', 'link' => url('index/category')],
    ['title' => '支付方式说明', 'icon' => 'payment',     'link' => url('index/order')],
    ['title' => '订单查询指南', 'icon' => 'order',       'link' => url('index/order')],
    ['title' => '常见问题解答', 'icon' => 'help',         'link' => url('chat')],
];
?>
<style>
/* ===== 首页定制样式（使用主站 CSS 变量系统） ===== */
.fade-in-up { animation: fadeInUp 0.6s var(--ease-out) both; }

/* ---------- 1. Hero 区（深色蓝紫渐变 + 3D 装饰） ---------- */
.hero-pro {
    position: relative;
    background: linear-gradient(135deg, #1e3a8a 0%, #4c1d95 100%);
    color: #fff;
    border-radius: var(--radius-xl);
    padding: 56px 40px;
    margin-bottom: var(--sp-8);
    overflow: hidden;
    box-shadow: var(--shadow-xl);
}
.hero-pro::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(circle at 20% 30%, rgba(96,165,250,0.35) 0%, transparent 45%),
        radial-gradient(circle at 80% 70%, rgba(167,139,250,0.32) 0%, transparent 45%),
        radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
    background-size: auto, auto, 24px 24px;
    animation: heroProPulse 10s var(--ease-in-out) infinite;
}
@keyframes heroProPulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.05); } }

.hero-pro-orb { position: absolute; border-radius: 50%; filter: blur(8px); pointer-events: none; }
.hero-pro-orb.o1 { top: -40px; right: -30px; width: 220px; height: 220px; background: radial-gradient(circle, rgba(96,165,250,0.55), transparent 70%); animation: floatOrb 6s var(--ease-in-out) infinite; }
.hero-pro-orb.o2 { bottom: -50px; left: 18%; width: 180px; height: 180px; background: radial-gradient(circle, rgba(167,139,250,0.45), transparent 70%); animation: floatOrb 7s var(--ease-in-out) infinite 1s; }
@keyframes floatOrb { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-16px); } }

.hero-pro-inner {
    position: relative; z-index: 2;
    display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px;
    align-items: center;
}
.hero-pro h1 { font-size: 40px; font-weight: 800; line-height: 1.18; letter-spacing: -0.5px; margin-bottom: var(--sp-4); }
.hero-pro .hero-sub { font-size: 16px; opacity: 0.92; margin-bottom: var(--sp-6); line-height: 1.7; }
.hero-pro-actions { display: flex; gap: var(--sp-3); flex-wrap: wrap; }
.hero-pro-actions .btn { padding: 13px 30px; font-size: 15px; }
.hero-pro-actions .btn-light { background: #fff; color: #4c1d95; }
.hero-pro-actions .btn-outline-light { border-color: rgba(255,255,255,0.55); }

.hero-pro-illust { position: relative; height: 320px; display: flex; align-items: center; justify-content: center; }
.hero-pro-illust .illust-bag { animation: floatBag 5s var(--ease-in-out) infinite; }
.hero-pro-illust .illust-card { position: absolute; animation: floatCard 4.5s var(--ease-in-out) infinite; }
.hero-pro-illust .illust-card.c1 { top: 8%; right: 8%; animation-delay: 0.3s; }
.hero-pro-illust .illust-card.c2 { bottom: 8%; left: 4%; animation-delay: 0.6s; }
@keyframes floatBag { 0%,100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-12px) rotate(2deg); } }
@keyframes floatCard { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-14px); } }

.hero-pro-stats {
    position: relative; z-index: 2;
    margin-top: var(--sp-8);
    display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--sp-4);
    padding-top: var(--sp-6); border-top: 1px solid rgba(255,255,255,0.18);
}
.hero-pro-stat { text-align: center; }
.hero-pro-stat .num {
    font-size: 26px; font-weight: 800; margin-bottom: 4px;
    background: linear-gradient(135deg, #fff, #c7d2fe);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
}
.hero-pro-stat .lbl { font-size: 12px; opacity: 0.8; }

/* ---------- 2. 核心能力区 ---------- */
.capability-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--sp-5); }
.capability-card {
    position: relative; overflow: hidden;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius-lg); padding: var(--sp-8) var(--sp-6);
    transition: transform 0.35s var(--ease-out), box-shadow 0.35s var(--ease-out), border-color 0.35s var(--ease-out);
}
.capability-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--primary-100); }
.capability-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--gradient-primary); transform: scaleX(0); transform-origin: left;
    transition: transform 0.4s var(--ease-out);
}
.capability-card:hover::before { transform: scaleX(1); }
.capability-icon {
    width: 56px; height: 56px; border-radius: var(--radius);
    background: var(--primary-50); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: var(--sp-4);
    transition: transform 0.3s var(--ease-spring), background 0.3s var(--ease), color 0.3s var(--ease);
}
.capability-card:hover .capability-icon { transform: scale(1.08) rotate(-4deg); background: var(--gradient-primary); color: #fff; }
.capability-title { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: var(--sp-3); }
.capability-desc { font-size: 14px; color: var(--text-muted); line-height: 1.7; }
.capability-no {
    position: absolute; top: 18px; right: 22px;
    font-size: 56px; font-weight: 800; color: var(--bg-soft); line-height: 1;
}

/* ---------- 3. 热门商品瀑布流（移动端风格） ---------- */
.hot-goods-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--sp-4); }
.hot-goods-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius-lg); overflow: hidden;
    display: flex; flex-direction: column;
    transition: transform 0.3s var(--ease-out), box-shadow 0.3s var(--ease-out), border-color 0.3s var(--ease-out);
}
.hot-goods-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--primary-100); }
.hot-goods-cover {
    height: 160px; position: relative; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
}
.hot-goods-card:nth-child(4n+1) .hot-goods-cover { background: linear-gradient(135deg, #EEF2FF, #C7D2FE); }
.hot-goods-card:nth-child(4n+2) .hot-goods-cover { background: linear-gradient(135deg, #ECFDF5, #A7F3D0); }
.hot-goods-card:nth-child(4n+3) .hot-goods-cover { background: linear-gradient(135deg, #FFFBEB, #FDE68A); }
.hot-goods-card:nth-child(4n+4) .hot-goods-cover { background: linear-gradient(135deg, #FEF2F2, #FECACA); }
.hot-goods-cover .cover-icon {
    width: 64px; height: 64px; color: rgba(37,99,235,0.35);
    transition: transform 0.4s var(--ease-out);
}
.hot-goods-card:hover .hot-goods-cover .cover-icon { transform: scale(1.1) rotate(-4deg); }
.hot-goods-cover img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s var(--ease-out); }
.hot-goods-card:hover .hot-goods-cover img { transform: scale(1.08); }
.hot-goods-badge {
    position: absolute; top: 10px; left: 10px;
    background: linear-gradient(135deg, #EF4444, #DC2626); color: #fff;
    font-size: 11px; font-weight: 600; padding: 3px 9px;
    border-radius: var(--radius-full); box-shadow: 0 2px 6px rgba(239,68,68,0.4);
}
.hot-goods-body { padding: 14px; display: flex; flex-direction: column; flex: 1; }
.hot-goods-name {
    font-size: 14px; font-weight: 500; color: var(--text);
    line-height: 1.4; height: 2.8em; overflow: hidden;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    margin-bottom: var(--sp-2);
}
.hot-goods-meta { display: flex; align-items: baseline; gap: var(--sp-2); margin-bottom: var(--sp-2); }
.hot-goods-price { color: var(--danger); font-weight: 700; font-size: 20px; }
.hot-goods-original { font-size: 12px; color: var(--text-light); text-decoration: line-through; }
.hot-goods-sold { font-size: 12px; color: var(--text-muted); margin-bottom: var(--sp-3); }
.hot-goods-buy {
    margin-top: auto;
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 8px 14px; border-radius: var(--radius-sm);
    background: var(--gradient-primary); color: #fff;
    font-size: 13px; font-weight: 500;
    transition: transform 0.2s var(--ease-out), box-shadow 0.3s var(--ease);
}
.hot-goods-buy:hover { transform: translateY(-2px); box-shadow: var(--shadow-primary); }
.hot-goods-buy .icon { width: 15px; height: 15px; }

/* ---------- 4. 服务保障区 ---------- */
.feature-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--sp-4); }
.feature-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: var(--sp-6) var(--sp-4);
    text-align: center;
    transition: transform 0.3s var(--ease-out), box-shadow 0.3s var(--ease-out), border-color 0.3s var(--ease-out);
}
.feature-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--primary-100); }
.feature-icon {
    width: 52px; height: 52px; border-radius: 50%;
    background: var(--primary-50); color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto var(--sp-3);
    transition: transform 0.3s var(--ease-spring), background 0.3s var(--ease), color 0.3s var(--ease);
}
.feature-card:hover .feature-icon { transform: scale(1.1) rotate(-6deg); background: var(--gradient-primary); color: #fff; }
.feature-title { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
.feature-desc { font-size: 12px; color: var(--text-muted); }

/* ---------- 5. 商家入驻 CTA ---------- */
.merchant-cta {
    position: relative; overflow: hidden;
    background: linear-gradient(135deg, #2563EB 0%, #4c1d95 100%);
    color: #fff; border-radius: var(--radius-xl);
    padding: 56px 40px; text-align: center;
    margin-bottom: var(--sp-8); box-shadow: var(--shadow-xl);
}
.merchant-cta::before {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.15), transparent 50%),
                radial-gradient(circle at 70% 70%, rgba(167,139,250,0.25), transparent 50%);
}
.merchant-cta > * { position: relative; z-index: 1; }
.merchant-cta h2 { font-size: 30px; font-weight: 800; margin-bottom: var(--sp-3); letter-spacing: -0.3px; }
.merchant-cta p { font-size: 15px; opacity: 0.92; margin-bottom: var(--sp-6); max-width: 540px; margin-left: auto; margin-right: auto; line-height: 1.7; }
.merchant-cta .btn { padding: 13px 32px; font-size: 15px; background: #fff; color: #4c1d95; border-color: #fff; }
.merchant-cta .btn:hover { box-shadow: var(--shadow); transform: translateY(-2px); }

/* ---------- 6. 公告与文章区 ---------- */
.notice-article-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: var(--sp-5); }
.notice-card, .help-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius-lg); overflow: hidden;
}
.notice-card-header, .help-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: var(--sp-5) var(--sp-6); border-bottom: 1px solid var(--border-soft);
}
.notice-card-header h3, .help-card-header h3 {
    font-size: 16px; font-weight: 700; color: var(--text);
    display: flex; align-items: center; gap: var(--sp-2);
}
.notice-card-header h3 .icon, .help-card-header h3 .icon { color: var(--primary); }
.notice-list { padding: var(--sp-2) 0; }
.notice-list li a {
    display: flex; align-items: center; gap: var(--sp-3);
    padding: 12px var(--sp-6); color: var(--text-2); font-size: 14px;
    transition: background 0.2s var(--ease), color 0.2s var(--ease);
}
.notice-list li a:hover { background: var(--primary-50); color: var(--primary); }
.notice-list .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--primary); flex-shrink: 0; opacity: 0.6; }
.notice-list .ttl { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.notice-list .date { font-size: 12px; color: var(--text-light); flex-shrink: 0; }
.notice-empty { padding: var(--sp-8); text-align: center; color: var(--text-muted); font-size: 13px; }

.help-list { padding: var(--sp-2) 0; }
.help-list a {
    display: flex; align-items: center; gap: var(--sp-3);
    padding: 12px var(--sp-6); color: var(--text-2); font-size: 14px;
    transition: background 0.2s var(--ease), color 0.2s var(--ease);
}
.help-list a:hover { background: var(--primary-50); color: var(--primary); }
.help-list .help-ic {
    width: 36px; height: 36px; border-radius: var(--radius-sm);
    background: var(--primary-50); color: var(--primary);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.help-list .help-ttl { flex: 1; }
.help-list .arrow { color: var(--text-light); }

/* ---------- 响应式 ---------- */
@media (max-width: 992px) {
    .hero-pro-inner { grid-template-columns: 1fr; }
    .hero-pro-illust { display: none; }
    .capability-grid { grid-template-columns: 1fr; }
    .hot-goods-grid { grid-template-columns: repeat(3, 1fr); }
    .feature-grid { grid-template-columns: repeat(2, 1fr); }
    .notice-article-grid { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .hero-pro { padding: 40px 20px; }
    .hero-pro h1 { font-size: 26px; }
    .hero-pro-stats { grid-template-columns: repeat(2, 1fr); gap: var(--sp-4); }
    .hot-goods-grid { grid-template-columns: repeat(2, 1fr); }
    .merchant-cta { padding: 40px 20px; }
    .merchant-cta h2 { font-size: 22px; }
}
</style>

<!-- ============ 1. Hero 区 ============ -->
<section class="hero-pro fade-in-up">
    <div class="hero-pro-orb o1"></div>
    <div class="hero-pro-orb o2"></div>
    <div class="hero-pro-inner">
        <div class="hero-pro-left">
            <h1>企业级虚拟商品寄售平台</h1>
            <p class="hero-sub">稳定交付 · 持续迭代 · 专属定制 · 安全·省心·先进</p>
            <div class="hero-pro-actions">
                <a href="<?php echo url('index/category'); ?>" class="btn btn-lg btn-light">
                    <svg class="icon" aria-hidden="true"><use href="#icon-shopping-bag"></use></svg>
                    立即购买
                </a>
                <a href="<?php echo url('index/merchant_join'); ?>" class="btn btn-lg btn-outline-light">
                    <svg class="icon" aria-hidden="true"><use href="#icon-merchant"></use></svg>
                    商家入驻
                </a>
            </div>
        </div>
        <div class="hero-pro-illust" aria-hidden="true">
            <!-- 3D 风格 SVG 插画：盾牌 + 购物袋 + 卡片 + 对勾 -->
            <svg class="illust-bag" width="180" height="200" viewBox="0 0 180 200" fill="none">
                <path d="M90 20 L150 40 V100 Q150 145 90 175 Q30 145 30 100 V40 Z" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.35)" stroke-width="2"/>
                <rect x="55" y="80" width="70" height="65" rx="8" fill="#fff"/>
                <path d="M70 80 V70 a20 20 0 0 1 40 0 V80" stroke="#fff" stroke-width="3" fill="none"/>
                <rect x="65" y="95" width="50" height="6" rx="3" fill="#2563EB"/>
                <rect x="65" y="108" width="35" height="6" rx="3" fill="#4c1d95" opacity="0.55"/>
                <rect x="65" y="121" width="45" height="6" rx="3" fill="#2563EB" opacity="0.35"/>
            </svg>
            <div class="illust-card c1">
                <svg width="64" height="48" viewBox="0 0 64 48" fill="none">
                    <rect x="2" y="2" width="60" height="44" rx="6" fill="#fff" stroke="#2563EB" stroke-width="2"/>
                    <rect x="8" y="14" width="20" height="4" rx="2" fill="#2563EB"/>
                    <rect x="8" y="22" width="14" height="3" rx="1.5" fill="#94A3B8"/>
                    <rect x="8" y="32" width="48" height="6" rx="3" fill="#4c1d95"/>
                </svg>
            </div>
            <div class="illust-card c2">
                <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                    <circle cx="28" cy="28" r="26" fill="#10B981"/>
                    <path d="M18 28 L25 35 L40 20" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>
        </div>
    </div>
    <div class="hero-pro-stats">
        <div class="hero-pro-stat">
            <div class="num" data-count="<?php echo (int) $onlineGoodsCount; ?>">0</div>
            <div class="lbl">在线商品</div>
        </div>
        <div class="hero-pro-stat">
            <div class="num" data-count="<?php echo (int) $todayOrders; ?>">0</div>
            <div class="lbl">今日订单</div>
        </div>
        <div class="hero-pro-stat">
            <div class="num" data-count="<?php echo (int) $activeMerchants; ?>">0</div>
            <div class="lbl">活跃商家</div>
        </div>
        <div class="hero-pro-stat">
            <div class="num" data-count="<?php echo (float) $totalAmount; ?>" data-decimals="2" data-prefix="¥">¥0.00</div>
            <div class="lbl">累计交易</div>
        </div>
    </div>
</section>

<!-- ============ 2. 核心能力区 ============ -->
<section class="section reveal">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true"><use href="#icon-dashboard"></use></svg>
            围绕软件交付全流程提供支持
        </div>
    </div>
    <div class="capability-grid stagger">
        <div class="capability-card">
            <div class="capability-no">01</div>
            <div class="capability-icon">
                <svg class="icon icon-lg" aria-hidden="true"><use href="#icon-shield"></use></svg>
            </div>
            <div class="capability-title">授权与商品管理</div>
            <div class="capability-desc">支持商品寄售、分类管理、库存监控、自动发卡，便于快速管理商品状态</div>
        </div>
        <div class="capability-card">
            <div class="capability-no">02</div>
            <div class="capability-icon">
                <svg class="icon icon-lg" aria-hidden="true"><use href="#icon-order"></use></svg>
            </div>
            <div class="capability-title">订单与交易追踪</div>
            <div class="capability-desc">统一管理订单流程、支付状态与发货记录，方便回溯与持续运营</div>
        </div>
        <div class="capability-card">
            <div class="capability-no">03</div>
            <div class="capability-icon">
                <svg class="icon icon-lg" aria-hidden="true"><use href="#icon-article"></use></svg>
            </div>
            <div class="capability-title">文档与服务支持</div>
            <div class="capability-desc">内置文章公告、客服入口与商家入驻流程，帮助用户更快上手</div>
        </div>
    </div>
</section>

<!-- ============ 3. 热门商品瀑布流 ============ -->
<section class="section reveal">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true" style="color:#EF4444"><use href="#icon-trending-up"></use></svg>
            热门商品
        </div>
        <a href="<?php echo url('index/category'); ?>" class="section-more">
            查看全部
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
        </a>
    </div>
    <?php if (empty($hotGoods)): ?>
    <div class="card empty-state">
        <div class="empty-illust"><svg class="icon" aria-hidden="true"><use href="#icon-box"></use></svg></div>
        <h3><?php echo h($tpl['goods_empty_tip'] ?? '暂无上架商品'); ?></h3>
        <p>暂时还没有热门商品上架，欢迎商家入驻充实货架</p>
    </div>
    <?php else: ?>
    <div class="hot-goods-grid stagger">
        <?php foreach ($hotGoods as $item): ?>
            <?php
            $eff  = goods_effective_price($item);
            $sold = (int) ($item['sold'] ?? 0);
            $cover = !empty($item['cover']) ? h($item['cover']) : '';
            $name  = h($item['name'] ?? '');
            $link  = url('index/goods', ['id' => $item['id']]);
            ?>
            <div class="hot-goods-card">
                <a href="<?php echo h($link); ?>" class="hot-goods-cover">
                    <?php if ($cover): ?>
                        <img src="<?php echo $cover; ?>" alt="<?php echo $name; ?>">
                    <?php else: ?>
                        <svg class="cover-icon" aria-hidden="true"><use href="#icon-box"></use></svg>
                    <?php endif; ?>
                    <?php if ($eff['activity'] !== 'none'): ?>
                        <span class="hot-goods-badge"><?php echo h($eff['label']); ?></span>
                    <?php endif; ?>
                </a>
                <div class="hot-goods-body">
                    <a href="<?php echo h($link); ?>" class="hot-goods-name"><?php echo $name; ?></a>
                    <div class="hot-goods-meta">
                        <span class="hot-goods-price">¥<?php echo number_format($eff['price'], 2); ?></span>
                        <?php if ($eff['activity'] !== 'none'): ?>
                            <span class="hot-goods-original">¥<?php echo number_format($eff['original_price'], 2); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="hot-goods-sold" data-count="<?php echo $sold; ?>" data-prefix="已售 ">已售 0</div>
                    <a href="<?php echo h($link); ?>" class="hot-goods-buy">
                        <svg class="icon" aria-hidden="true"><use href="#icon-shopping-bag"></use></svg>
                        立即购买
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<!-- ============ 4. 服务保障区 ============ -->
<section class="section reveal">
    <div class="section-header">
        <div class="section-title">
            <svg class="icon" aria-hidden="true"><use href="#icon-shield"></use></svg>
            服务保障
        </div>
    </div>
    <div class="feature-grid stagger">
        <div class="feature-card">
            <div class="feature-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-zap"></use></svg></div>
            <div class="feature-title">极速发货</div>
            <div class="feature-desc">自动卡密</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-shield"></use></svg></div>
            <div class="feature-title">安全交易</div>
            <div class="feature-desc">多重防护</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-headphones"></use></svg></div>
            <div class="feature-title">7×24客服</div>
            <div class="feature-desc">随时响应</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-lock"></use></svg></div>
            <div class="feature-title">隐私保护</div>
            <div class="feature-desc">数据加密</div>
        </div>
    </div>
</section>

<!-- ============ 5. 商家入驻 CTA 区 ============ -->
<section class="merchant-cta reveal">
    <h2>加入我们，开启你的寄售业务</h2>
    <p>平台提供自动发卡、订单管理、财务结算、营销工具等全套能力，让商家专注商品、轻松运营。</p>
    <a href="<?php echo url('index/merchant_join'); ?>" class="btn btn-lg">
        <svg class="icon" aria-hidden="true"><use href="#icon-rocket"></use></svg>
        立即入驻
    </a>
</section>

<!-- ============ 6. 公告与文章区 ============ -->
<section class="section reveal">
    <div class="notice-article-grid">
        <div class="notice-card">
            <div class="notice-card-header">
                <h3>
                    <svg class="icon" aria-hidden="true"><use href="#icon-bell"></use></svg>
                    公告
                </h3>
            </div>
            <?php if (empty($articles)): ?>
                <div class="notice-empty">暂无公告</div>
            <?php else: ?>
                <ul class="notice-list">
                    <?php foreach (array_slice($articles, 0, 5) as $article): ?>
                        <li>
                            <a href="<?php echo url('index/article', ['id' => $article['id']]); ?>">
                                <span class="dot"></span>
                                <span class="ttl"><?php echo h($article['title']); ?></span>
                                <span class="date"><?php echo h(mb_substr((string) ($article['create_time'] ?? ''), 5, 5)); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="help-card">
            <div class="help-card-header">
                <h3>
                    <svg class="icon" aria-hidden="true"><use href="#icon-help"></use></svg>
                    帮助中心
                </h3>
            </div>
            <div class="help-list">
                <?php if (!empty($helps)): ?>
                    <?php foreach ($helps as $help): ?>
                        <?php
                        $helpLink = !empty($help['link'])
                            ? $help['link']
                            : (!empty($help['id'])
                                ? url('index/article', ['id' => $help['id']])
                                : url('index/category'));
                        $helpIcon = !empty($help['icon']) ? $help['icon'] : 'article';
                        ?>
                        <a href="<?php echo h($helpLink); ?>">
                            <span class="help-ic">
                                <svg class="icon" aria-hidden="true"><use href="#icon-<?php echo h($helpIcon); ?>"></use></svg>
                            </span>
                            <span class="help-ttl"><?php echo h($help['title'] ?? ''); ?></span>
                            <svg class="icon icon-sm arrow" aria-hidden="true"><use href="#icon-chevron-right"></use></svg>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($fixedHelps as $fh): ?>
                        <a href="<?php echo h($fh['link']); ?>">
                            <span class="help-ic">
                                <svg class="icon" aria-hidden="true"><use href="#icon-<?php echo h($fh['icon']); ?>"></use></svg>
                            </span>
                            <span class="help-ttl"><?php echo h($fh['title']); ?></span>
                            <svg class="icon icon-sm arrow" aria-hidden="true"><use href="#icon-chevron-right"></use></svg>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
