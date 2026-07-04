<?php
/** @var array $products */
/** @var array $plugins */
/** @var array $articles */
/** @var array $stats */
$siteName = site_config('site_name');
$flagshipProducts = [
    [
        'icon'    => 'shield',
        'name'    => 'QEEFG 寄售系统',
        'desc'    => '一站式数字商品 SaaS 平台，授权码销售 · 域名授权 · 自动发卡，为开发者构建现代化寄售体系。',
        'tags'    => ['授权码', '域名授权', '自动发卡'],
        'url'     => url('product'),
        'cta'     => '查看详情',
    ],
    [
        'icon'    => 'dollar',
        'name'    => '超级支付插件',
        'desc'    => '开箱即用的底层支付能力，多渠道聚合、即时到账、对账清晰，让交易流程安全可控。',
        'tags'    => ['多渠道', '即时到账', '安全对账'],
        'url'     => url('plugin'),
        'cta'     => '查看详情',
    ],
    [
        'icon'    => 'package',
        'name'    => '应用市场',
        'desc'    => '开发者自助上架插件与扩展，审核机制完善，下载即用，构建可持续的应用生态。',
        'tags'    => ['自助上架', '审核机制', '下载即用'],
        'url'     => url('plugin'),
        'cta'     => '查看详情',
    ],
];
$capabilities = [
    ['icon' => 'shield',  'title' => '系统稳定安全',  'sub' => '10年+技术积累', 'desc' => '基于成熟框架与多层防护机制构建，历经大规模生产验证，运行稳定可靠。'],
    ['icon' => 'tag',     'title' => '极致设计',      'sub' => '符合用户习惯',   'desc' => '遵循现代 SaaS 设计语言，交互流畅自然，让每一次操作都顺滑高效。'],
    ['icon' => 'code',    'title' => '个性需求',      'sub' => '定制化开发',     'desc' => '支持私有化部署与功能定制，灵活适配不同业务场景，1V1 技术对接。'],
    ['icon' => 'download','title' => '可靠服务',      'sub' => '多地实时备份',   'desc' => '多地容灾备份与实时同步，数据安全有保障，业务连续不中断。'],
    ['icon' => 'clock',   'title' => '智能高效',      'sub' => '快速响应',       'desc' => '智能调度与异步处理，毫秒级响应，让授权验证、发卡、下载更高效。'],
    ['icon' => 'check',   'title' => '质量保障',      'sub' => '严格测试流程',   'desc' => '完善的自动化测试与人工验收流程，确保每一次发版都经得起考验。'],
];
$counterStats = [
    ['count' => 8,     'prefix' => '',     'suffix' => '年+',   'label' => '开发经验'],
    ['count' => 1400,  'prefix' => '',     'suffix' => '+',    'label' => '服务客户'],
    ['count' => 22000, 'prefix' => '',     'suffix' => '+',    'label' => '累计下载'],
    ['count' => 24,    'prefix' => '7×',   'suffix' => '小时', 'label' => '技术服务'],
];
?>

<!-- ====================== Hero 区域 ====================== -->
<section class="hero fade-in-up">
    <div class="hero-inner">
        <div class="hero-illustration">
            <svg width="240" height="200" viewBox="0 0 240 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <!-- 漂浮云朵 -->
                <ellipse cx="60" cy="40" rx="44" ry="20" fill="rgba(255,255,255,0.16)"/>
                <ellipse cx="84" cy="32" rx="30" ry="16" fill="rgba(255,255,255,0.24)"/>
                <!-- 3D 盾牌主体 -->
                <path d="M120 50 L165 66 V108 C165 136 142 158 120 168 C98 158 75 136 75 108 V66 Z" fill="rgba(255,255,255,0.96)"/>
                <path d="M120 50 L165 66 V108 C165 136 142 158 120 168 C98 158 75 136 75 108 V66 Z" fill="url(#shieldGradHero)" opacity="0.18"/>
                <!-- 盾牌高光 -->
                <path d="M120 50 L165 66 V88 C165 94 158 96 152 90 L120 60 Z" fill="rgba(255,255,255,0.42)"/>
                <!-- 钥匙环 -->
                <circle cx="120" cy="100" r="13" stroke="#7c3aed" stroke-width="3.8" fill="none"/>
                <!-- 钥匙杆与齿 -->
                <path d="M120 113 L120 138 M120 124 L132 124 M120 130 L128 130" stroke="#7c3aed" stroke-width="3.8" stroke-linecap="round"/>
                <!-- 漂浮装饰 -->
                <circle cx="200" cy="56" r="6" fill="rgba(255,255,255,0.6)"/>
                <circle cx="40" cy="118" r="4" fill="rgba(255,255,255,0.5)"/>
                <rect x="188" y="108" width="11" height="11" rx="2.5" fill="rgba(255,255,255,0.5)" transform="rotate(15 193 113)"/>
                <circle cx="50" cy="160" r="3" fill="rgba(255,255,255,0.55)"/>
                <defs>
                    <linearGradient id="shieldGradHero" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#a855f7"/>
                        <stop offset="1" stop-color="#7c3aed"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
        <h1>专业软件解决方案服务商</h1>
        <p>让您的想法快速落地，并且持续迭代，私有化方案，功能定制 + 技术支持，为开发者与企业构建现代化数字商品 SaaS 平台</p>
        <div class="hero-actions">
            <a href="<?php echo url('product'); ?>" class="btn btn-primary-hero btn-lg">
                <i data-icon="product"></i><span>浏览授权产品</span>
            </a>
            <a href="<?php echo url('plugin'); ?>" class="btn btn-ghost btn-lg">
                <i data-icon="plugin"></i><span>探索插件市场</span>
            </a>
        </div>
    </div>
</section>

<!-- ====================== 我们的产品 ====================== -->
<section class="card card-glow fade-in-up">
    <div class="section-title">
        <span><i data-icon="package" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>提供高标准面向交付的产品服务</span>
    </div>
    <div class="grid-features flagship-grid">
        <?php foreach ($flagshipProducts as $fp): ?>
        <a class="feature-card flagship-card" href="<?php echo $fp['url']; ?>">
            <div class="feature-icon"><i data-icon="<?php echo $fp['icon']; ?>"></i></div>
            <h3><?php echo h($fp['name']); ?></h3>
            <p><?php echo h($fp['desc']); ?></p>
            <div class="flagship-tags">
                <?php foreach ($fp['tags'] as $tag): ?>
                <span class="tag tag-purple"><?php echo h($tag); ?></span>
                <?php endforeach; ?>
            </div>
            <span class="flagship-cta">
                <?php echo h($fp['cta']); ?>
                <i data-icon="chevron-right" class="svg-icon-sm"></i>
            </span>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- ====================== 核心能力 ====================== -->
<section class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="shield" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>开箱即用的底层能力支持</span>
    </div>
    <div class="grid-features">
        <?php foreach ($capabilities as $cap): ?>
        <div class="feature-card">
            <div class="feature-icon"><i data-icon="<?php echo $cap['icon']; ?>"></i></div>
            <h3><?php echo h($cap['title']); ?> · <?php echo h($cap['sub']); ?></h3>
            <p><?php echo h($cap['desc']); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ====================== 统计数字 ====================== -->
<section class="card counter-card fade-in-up">
    <div class="counter-grid">
        <?php foreach ($counterStats as $cs): ?>
        <div class="counter-item">
            <div class="counter-number">
                <span class="counter-prefix"><?php echo h($cs['prefix']); ?></span>
                <span class="stat-value" data-count="<?php echo (int)$cs['count']; ?>">0</span>
                <span class="counter-suffix"><?php echo h($cs['suffix']); ?></span>
            </div>
            <div class="counter-label"><?php echo h($cs['label']); ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ====================== 授权产品（动态数据） ====================== -->
<div class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="product" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>授权产品</span>
        <a href="<?php echo url('product'); ?>">查看更多 <i data-icon="chevron-right" class="svg-icon-sm"></i></a>
    </div>
    <?php if (empty($products)): ?>
    <div class="empty-state">
        <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect x="30" y="40" width="60" height="50" rx="6" stroke="#c4b5fd" stroke-width="2.5"/>
            <path d="M30 55 H90" stroke="#c4b5fd" stroke-width="2.5"/>
            <circle cx="40" cy="48" r="2" fill="#c4b5fd"/>
            <circle cx="48" cy="48" r="2" fill="#c4b5fd"/>
            <rect x="40" y="65" width="40" height="6" rx="3" fill="#ede9fe"/>
            <rect x="40" y="76" width="28" height="6" rx="3" fill="#ede9fe"/>
        </svg>
        <h4>暂无上架产品</h4>
        <p>管理员尚未发布授权产品</p>
    </div>
    <?php else: ?>
    <div class="grid">
        <?php foreach ($products as $item): ?>
        <a class="item-card product-card" href="<?php echo url('product/detail', ['id' => $item['id']]); ?>">
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
    <?php endif; ?>
</div>

<!-- ====================== 插件市场（动态数据） ====================== -->
<div class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="plugin" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>插件市场</span>
        <a href="<?php echo url('plugin'); ?>">查看更多 <i data-icon="chevron-right" class="svg-icon-sm"></i></a>
    </div>
    <?php if (empty($plugins)): ?>
    <div class="empty-state">
        <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M40 55 H30a6 6 0 0 0-6 6v12a6 6 0 0 0 6 6h10 M80 55h10a6 6 0 0 1 6 6v12a6 6 0 0 1-6 6H80 M40 79v8a6 6 0 0 0 6 6h28a6 6 0 0 0 6-6v-8" stroke="#c4b5fd" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="40" y="45" width="40" height="34" rx="4" fill="#ede9fe"/>
        </svg>
        <h4>暂无上架插件</h4>
        <p>插件市场暂无内容</p>
    </div>
    <?php else: ?>
    <div class="grid">
        <?php foreach ($plugins as $item): ?>
        <a class="item-card plugin-card" href="<?php echo url('plugin/detail', ['id' => $item['id']]); ?>">
            <div class="item-cover">
                <svg class="cover-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 7h6a1 1 0 0 1 1 1v3h-3a1 1 0 0 0 0 2h3v3a1 1 0 0 1-1 1h-6v-2a2 2 0 1 0-4 0v2H4a1 1 0 0 1-1-1v-3h3a1 1 0 0 0 0-2H3V8a1 1 0 0 1 1-1h6v2a2 2 0 1 0 4 0V7z"/>
                </svg>
                <span>插件</span>
            </div>
            <div class="item-info">
                <div class="item-name"><?php echo h($item['name']); ?></div>
                <div class="item-meta">
                    <span class="item-price <?php echo $item['price'] > 0 ? '' : 'free'; ?>"><?php echo $item['price'] > 0 ? format_price($item['price']) : '免费'; ?></span>
                    <span class="item-sold"><?php echo h($item['author']); ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ====================== 平台公告（动态数据） ====================== -->
<div class="card fade-in-up" id="articles">
    <div class="section-title">
        <span><i data-icon="bell" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>平台公告</span>
    </div>
    <?php if (empty($articles)): ?>
    <div class="empty-tip">暂无公告</div>
    <?php else: ?>
    <ul class="article-list">
        <?php foreach ($articles as $item): ?>
        <li>
            <a href="<?php echo url('index/article', ['id' => $item['id']]); ?>"><?php echo h($item['title']); ?></a>
            <span><i data-icon="clock" class="svg-icon-sm" style="vertical-align:-2px;margin-right:2px;"></i><?php echo date('Y-m-d', strtotime($item['create_time'])); ?></span>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>

<!-- ====================== 底部 CTA ====================== -->
<section class="hero-cta fade-in-up">
    <div class="hero-cta-inner">
        <div class="hero-cta-text">
            <h2>即刻加入 <?php echo h($siteName); ?></h2>
            <p>注册成为会员，尊享 1V1 技术支持、专属授权管理与插件市场入驻权益</p>
        </div>
        <div class="hero-cta-actions">
            <?php if (get_user()): ?>
            <a href="<?php echo url('user'); ?>" class="btn btn-primary-hero btn-lg">
                <i data-icon="dashboard"></i><span>进入个人中心</span>
            </a>
            <?php else: ?>
            <a href="<?php echo url('login/register'); ?>" class="btn btn-primary-hero btn-lg">
                <i data-icon="key"></i><span>立即注册</span>
            </a>
            <a href="<?php echo url('login'); ?>" class="btn btn-ghost btn-lg">
                <i data-icon="user"></i><span>已有账号 · 登录</span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* 旗舰产品卡 */
.flagship-grid { gap: 20px; }
.flagship-card {
    display: flex;
    flex-direction: column;
    text-align: left;
    padding: 28px 24px;
    cursor: pointer;
}
.flagship-card h3 { font-size: 18px; font-weight: 700; margin-bottom: 10px; }
.flagship-card p { flex: 1; }
.flagship-tags { display: flex; flex-wrap: wrap; gap: 6px; margin: 16px 0 14px; }
.flagship-cta {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: var(--color-primary);
    font-weight: 600;
    font-size: 14px;
    transition: gap var(--transition);
}
.flagship-card:hover .flagship-cta { gap: 8px; }

/* 统计数字 */
.counter-card {
    background: var(--gradient-hero);
    color: #fff;
    border: none;
    overflow: hidden;
}
.counter-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 12% 20%, rgba(255,255,255,0.16), transparent 38%),
        radial-gradient(circle at 88% 82%, rgba(168,85,247,0.4), transparent 50%);
    pointer-events: none;
}
.counter-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    position: relative;
    z-index: 1;
}
.counter-item { text-align: center; }
.counter-number {
    font-size: 40px;
    font-weight: 800;
    letter-spacing: 0.5px;
    line-height: 1.1;
    font-variant-numeric: tabular-nums;
}
.counter-prefix, .counter-suffix { font-size: 26px; font-weight: 700; opacity: 0.9; }
.counter-label { font-size: 14px; opacity: 0.85; margin-top: 8px; }

/* 底部 CTA */
.hero-cta {
    background: var(--gradient-hero);
    color: #fff;
    border-radius: var(--radius-xl);
    padding: 48px 40px;
    margin-top: var(--space-lg);
    margin-bottom: var(--space-lg);
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
}
.hero-cta::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 85% 20%, rgba(255,255,255,0.2), transparent 40%),
        radial-gradient(circle at 15% 85%, rgba(168,85,247,0.4), transparent 50%);
    pointer-events: none;
}
.hero-cta-inner {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    flex-wrap: wrap;
}
.hero-cta-text h2 { font-size: 28px; font-weight: 800; margin-bottom: 8px; letter-spacing: 0.5px; }
.hero-cta-text p { font-size: 15px; opacity: 0.92; }
.hero-cta-actions { display: flex; gap: 12px; flex-wrap: wrap; }
.hero-cta-actions .btn-ghost { background: rgba(255,255,255,0.16); color: #fff; border: 1px solid rgba(255,255,255,0.3); }
.hero-cta-actions .btn-ghost:hover { background: rgba(255,255,255,0.28); }

@media (max-width: 768px) {
    .counter-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .counter-number { font-size: 32px; }
    .counter-prefix, .counter-suffix { font-size: 20px; }
    .hero-cta { padding: 36px 24px; }
    .hero-cta-text h2 { font-size: 22px; }
    .hero-cta-actions { width: 100%; }
    .hero-cta-actions .btn { flex: 1; }
}
</style>
