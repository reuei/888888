<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <div class="hero-tag">全新版本 · v1.0.5</div>
            <h1 class="hero-title">数字商品交易平台</h1>
            <p class="hero-subtitle">采用自研轻量框架，重新设计UI与交互，更快、更轻、更稳。</p>
            <div class="hero-actions">
                <a href="/category/1" class="btn btn-primary">浏览商品</a>
                <a href="/register" class="btn btn-line">立即注册</a>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <div class="stat-num">12,580+</div>
                    <div class="stat-label">累计用户</div>
                </div>
                <div class="stat">
                    <div class="stat-num">45,678+</div>
                    <div class="stat-label">完成订单</div>
                </div>
                <div class="stat">
                    <div class="stat-num">99.9%</div>
                    <div class="stat-label">系统稳定</div>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="visual-card visual-card-1">
                <div class="visual-card-bar"></div>
                <div class="visual-card-title">腾讯视频 VIP</div>
                <div class="visual-card-meta">月卡 · 即时发货</div>
            </div>
            <div class="visual-card visual-card-2">
                <div class="visual-card-bar"></div>
                <div class="visual-card-title">网易云音乐</div>
                <div class="visual-card-meta">黑胶年卡</div>
            </div>
            <div class="visual-card visual-card-3">
                <div class="visual-card-bar"></div>
                <div class="visual-card-title">Steam 充值</div>
                <div class="visual-card-meta">100元面值</div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="section-head">
        <h2 class="section-title">商品分类</h2>
        <p class="section-sub">覆盖主流数字商品</p>
    </div>
    <div class="category-grid">
        <?php foreach ($categories as $cat): ?>
        <a href="/category/<?= (int) $cat['id'] ?>" class="category-tile">
            <div class="category-icon icon-<?= h($cat['icon'] ?? 'game') ?>"></div>
            <div class="category-name"><?= h($cat['name']) ?></div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section-alt">
    <div class="section-head">
        <h2 class="section-title">热门商品</h2>
        <a href="/category/1" class="section-more">查看全部 →</a>
    </div>
    <div class="goods-grid">
        <?php foreach ($goods as $g): ?>
        <a href="/goods/<?= (int) $g['id'] ?>" class="goods-card">
            <div class="goods-cover">
                <div class="goods-cover-tag">HOT</div>
                <div class="goods-cover-img"></div>
            </div>
            <div class="goods-body">
                <div class="goods-name"><?= h($g['name']) ?></div>
                <div class="goods-foot">
                    <div class="goods-price">¥<?= format_money($g['price']) ?></div>
                    <div class="goods-sold">已售 <?= (int) $g['sold'] ?></div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="section-head">
        <h2 class="section-title">站点公告</h2>
        <a href="/notice" class="section-more">更多 →</a>
    </div>
    <div class="notice-list">
        <?php foreach ($notices as $n): ?>
        <a href="/notice/<?= (int) $n['id'] ?>" class="notice-item">
            <span class="notice-dot"></span>
            <span class="notice-title"><?= h($n['title']) ?></span>
            <span class="notice-time"><?= format_time($n['create_time'] ?? null, 'm-d H:i') ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</section>
