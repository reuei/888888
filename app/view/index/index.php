<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?> - 授权服务平台</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-home" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></symbol>
            <symbol id="i-platform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></symbol>
            <symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></symbol>
            <symbol id="i-license" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><path d="M8 21h8"/><path d="M12 17v4"/></symbol>
            <symbol id="i-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></symbol>
            <symbol id="i-announcement" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
            <symbol id="i-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></symbol>
            <symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></symbol>
            <symbol id="i-zap" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></symbol>
            <symbol id="i-bar-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></symbol>
            <symbol id="i-tool" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></symbol>
            <symbol id="i-download" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></symbol>
            <symbol id="i-box" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></symbol>
            <symbol id="i-wallet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></symbol>
            <symbol id="i-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></symbol>
            <symbol id="i-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></symbol>
            <symbol id="i-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></symbol>
            <symbol id="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></symbol>
            <symbol id="i-globe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></symbol>
            <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></symbol>
            <symbol id="i-star" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></symbol>
            <symbol id="i-github" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.438 9.8 8.205 11.385.6.111.82-.26.82-.577 0-.285-.01-1.04-.015-2.04-3.338.725-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.43.37.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 21.795 24 17.295 24 12c0-6.63-5.37-12-12-12z"/></symbol>
        </defs>
    </svg>

    <header class="site-header">
        <div class="header-inner">
            <a href="/" class="logo">
                <span class="logo-mark"><?= htmlspecialchars(mb_substr($siteSettings['site_name'] ?? '熵云', 0, 1)) ?></span>
                <span><?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></span>
            </a>
            <nav class="main-nav">
                <a href="/" class="nav-link active">首页</a>
                <a href="/platform" class="nav-link">平台能力</a>
                <a href="/license-query" class="nav-link">授权查询</a>
                <a href="/documents" class="nav-link">文档中心</a>
                <a href="javascript:void(0)" class="nav-link" onclick="showAnnouncement()">网站公告</a>
            </nav>
            <div class="auth-links">
                <a href="/user/login" class="btn btn-ghost btn-sm">登录</a>
                <a href="/user/register" class="btn btn-primary btn-sm">注册</a>
            </div>
            <button class="hamburger-btn" id="hamburgerBtn" aria-label="菜单">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <nav class="mobile-nav" id="mobileNav">
        <a href="/" class="nav-link active">首页</a>
        <a href="/platform" class="nav-link">平台能力</a>
        <a href="/license-query" class="nav-link">授权查询</a>
        <a href="/documents" class="nav-link">文档中心</a>
        <a href="javascript:void(0)" class="nav-link" onclick="showAnnouncement()">网站公告</a>
        <div class="auth-links" style="margin-top: 16px; display: flex; gap: 10px; justify-content: center;">
            <a href="/user/login" class="btn btn-ghost btn-sm">登录</a>
            <a href="/user/register" class="btn btn-primary btn-sm">注册</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">
                <span class="dot"></span>
                专业软件授权管理平台
            </div>
            <h1 class="hero-title"><?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?><span class="gradient-text">授权服务平台</span></h1>
            <p class="hero-subtitle"><?= htmlspecialchars($siteSettings['site_description'] ?? '为软件开发者与企业提供一站式授权管理解决方案，涵盖授权发放、在线验证、设备绑定与数据分析等核心能力。') ?></p>
            <div class="hero-buttons">
                <a href="/user/register" class="btn btn-primary btn-lg">
                    立即注册
                    <svg width="16" height="16"><use href="#i-arrow-right"/></svg>
                </a>
                <a href="/platform" class="btn btn-outline btn-lg">了解更多</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">核心能力</h2>
                <p class="section-subtitle">一站式授权管理，助力您的产品安全高效运营</p>
            </div>
            <div class="features-grid">
                <div class="feature-card" data-animate="fadeInUp">
                    <div class="feature-icon">
                        <svg width="24" height="24"><use href="#i-key"/></svg>
                    </div>
                    <h3 class="feature-title">授权发放与管理</h3>
                    <p class="feature-desc">集中管理授权信息、有效期与使用状态，支持批量发放与实时查询，让授权管理变得简单高效。</p>
                </div>
                <div class="feature-card" data-animate="fadeInUp" data-animate-threshold="120">
                    <div class="feature-icon">
                        <svg width="24" height="24"><use href="#i-shield"/></svg>
                    </div>
                    <h3 class="feature-title">在线验证与设备绑定</h3>
                    <p class="feature-desc">支持授权查询、在线激活与设备特征管理，基于设备指纹绑定确保授权安全可控。</p>
                </div>
                <div class="feature-card" data-animate="fadeInUp" data-animate-threshold="140">
                    <div class="feature-icon">
                        <svg width="24" height="24"><use href="#i-zap"/></svg>
                    </div>
                    <h3 class="feature-title">API 接口集成</h3>
                    <p class="feature-desc">提供标准 RESTful API 接口与多语言 SDK，支持快速集成到您的软件产品中。</p>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($products)): ?>
    <section class="section" style="padding-top: 0;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">热门产品</h2>
                <p class="section-subtitle">精选优质产品，满足不同业务需求</p>
            </div>
            <div class="product-showcase">
                <?php foreach ($products as $product): ?>
                <div class="showcase-card" data-animate="fadeInUp">
                    <span class="showcase-badge">
                        <svg width="12" height="12"><use href="#i-star"/></svg>
                        推荐
                    </span>
                    <h3 class="showcase-title"><?= htmlspecialchars($product['name'] ?? '') ?></h3>
                    <p class="showcase-desc"><?= htmlspecialchars($product['description'] ?? '') ?></p>
                    <div class="showcase-price">
                        <span class="currency">¥</span><?= number_format($product['price'] ?? 0, 2) ?>
                    </div>
                    <a href="/user/register" class="btn btn-primary btn-block">立即购买</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="section" style="padding-top: 0;">
        <div class="container">
            <div class="stats-banner">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?= (int)($stats['products'] ?? 0) ?></div>
                        <div class="stat-label">产品数量</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= (int)($stats['users'] ?? 0) ?></div>
                        <div class="stat-label">注册用户</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= (int)($stats['licenses'] ?? 0) ?></div>
                        <div class="stat-label">授权总数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">99.9%</div>
                        <div class="stat-label">服务可用性</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <h2 class="cta-title">准备好开始了吗？</h2>
                <p class="cta-subtitle">立即注册，体验专业的软件授权管理服务</p>
                <a href="/user/register" class="btn btn-primary btn-lg">
                    免费注册
                    <svg width="16" height="16"><use href="#i-arrow-right"/></svg>
                </a>
            </div>
        </div>
    </section>

    <?php if (!empty($siteSettings['announcement'])): ?>
    <div class="announcement-modal" id="announcementModal">
        <div class="am-overlay"></div>
        <div class="am-dialog">
            <div class="am-header">
                <h3><svg width="18" height="18" style="vertical-align: middle; margin-right: 6px;"><use href="#i-bell"/></svg>网站公告</h3>
                <button class="am-close" id="amCloseBtn"><svg width="18" height="18"><use href="#i-close"/></svg></button>
            </div>
            <div class="am-body">
                <p style="color: var(--text-secondary); line-height: 1.8; white-space: pre-wrap;"><?= htmlspecialchars($siteSettings['announcement']) ?></p>
            </div>
            <div class="am-footer">
                <button class="btn btn-primary btn-sm" id="amConfirmBtn">我知道了</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-links">
                <a href="/">首页</a>
                <a href="/platform">平台能力</a>
                <a href="/license-query">授权查询</a>
                <a href="/documents">文档中心</a>
            </div>
            <p class="footer-copyright">© <?= date('Y') ?> <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?> All Rights Reserved. <?= !empty($siteSettings['icp']) ? htmlspecialchars($siteSettings['icp']) : '' ?></p>
        </div>
    </footer>

    <script src="/static/js/main.js"></script>
</body>
</html>