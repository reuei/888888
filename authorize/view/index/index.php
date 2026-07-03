<!-- Hero 区域 -->
<section class="hero fade-in-up">
    <div class="hero-inner">
        <!-- 3D 插画风格 SVG 装饰 -->
        <div class="hero-illustration">
            <svg width="220" height="180" viewBox="0 0 220 180" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <!-- 云朵 -->
                <ellipse cx="60" cy="40" rx="42" ry="20" fill="rgba(255,255,255,0.18)"/>
                <ellipse cx="80" cy="32" rx="30" ry="16" fill="rgba(255,255,255,0.25)"/>
                <!-- 盾牌主体（3D 立体效果） -->
                <path d="M110 50 L150 65 V105 C150 130 130 150 110 158 C90 150 70 130 70 105 V65 Z" fill="rgba(255,255,255,0.95)"/>
                <path d="M110 50 L150 65 V105 C150 130 130 150 110 158 C90 150 70 130 70 105 V65 Z" fill="url(#shieldGrad)" opacity="0.18"/>
                <!-- 盾牌高光 -->
                <path d="M110 50 L150 65 V85 C150 90 145 92 140 88 L110 60 Z" fill="rgba(255,255,255,0.4)"/>
                <!-- 钥匙（授权码） -->
                <circle cx="110" cy="100" r="12" stroke="#7c3aed" stroke-width="3.5" fill="none"/>
                <path d="M110 112 L110 132 M110 122 L120 122 M110 128 L116 128" stroke="#7c3aed" stroke-width="3.5" stroke-linecap="round"/>
                <!-- 漂浮元素 -->
                <circle cx="180" cy="60" r="6" fill="rgba(255,255,255,0.6)"/>
                <circle cx="40" cy="110" r="4" fill="rgba(255,255,255,0.5)"/>
                <rect x="168" y="100" width="10" height="10" rx="2" fill="rgba(255,255,255,0.5)" transform="rotate(15 173 105)"/>
                <!-- 渐变定义 -->
                <defs>
                    <linearGradient id="shieldGrad" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#a855f7"/>
                        <stop offset="1" stop-color="#7c3aed"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
        <h1>QEEFG 寄售系统</h1>
        <p>授权码销售 · 域名授权 · 插件市场一站式解决方案，为开发者构建现代化的数字商品 SaaS 平台</p>
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

<!-- 授权产品展示区 -->
<div class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="product" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>授权产品</span>
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
    <?php endif; ?>
</div>

<!-- 插件市场预览 -->
<div class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="plugin" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>插件市场</span>
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
        <a class="item-card plugin-card fade-in-up" href="<?php echo url('plugin/detail', ['id' => $item['id']]); ?>">
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

<!-- 特色功能介绍区 -->
<div class="card card-glow fade-in-up">
    <div class="section-title">
        <span><i data-icon="shield" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>平台特色</span>
    </div>
    <div class="grid-features">
        <div class="feature-card">
            <div class="feature-icon"><i data-icon="key"></i></div>
            <h3>授权码管理</h3>
            <p>自动生成授权码，支持域名绑定与多端授权，灵活可控</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i data-icon="plugin"></i></div>
            <h3>插件市场</h3>
            <p>开发者自助上架插件，审核机制完善，下载即用</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i data-icon="cart"></i></div>
            <h3>在线交易</h3>
            <p>余额充值、订单管理、即时发卡，交易流程清晰透明</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i data-icon="version"></i></div>
            <h3>版本更新</h3>
            <p>版本包分发与更新检测，让用户始终运行最新版本</p>
        </div>
    </div>
</div>

<!-- 平台公告 -->
<div class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="bell" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>平台公告</span>
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
