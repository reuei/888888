<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>平台能力 - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></symbol>
            <symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></symbol>
            <symbol id="i-zap" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></symbol>
            <symbol id="i-bar-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></symbol>
            <symbol id="i-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></symbol>
            <symbol id="i-tool" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></symbol>
            <symbol id="i-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></symbol>
            <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></symbol>
            <symbol id="i-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></symbol>
        </defs>
    </svg>

    <header class="site-header">
        <div class="header-inner">
            <a href="/" class="logo">
                <span class="logo-mark"><?= htmlspecialchars(mb_substr($siteSettings['site_name'] ?? '熵云', 0, 1)) ?></span>
                <span><?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></span>
            </a>
            <nav class="main-nav">
                <a href="/" class="nav-link">首页</a>
                <a href="/platform" class="nav-link active">平台能力</a>
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
        <a href="/" class="nav-link">首页</a>
        <a href="/platform" class="nav-link active">平台能力</a>
        <a href="/license-query" class="nav-link">授权查询</a>
        <a href="/documents" class="nav-link">文档中心</a>
        <a href="javascript:void(0)" class="nav-link" onclick="showAnnouncement()">网站公告</a>
        <div class="auth-links" style="margin-top: 16px; display: flex; gap: 10px; justify-content: center;">
            <a href="/user/login" class="btn btn-ghost btn-sm">登录</a>
            <a href="/user/register" class="btn btn-primary btn-sm">注册</a>
        </div>
    </nav>

    <section class="platform-hero">
        <div class="platform-hero-content">
            <div class="hero-badge">
                <span class="dot"></span>
                全功能授权管理平台
            </div>
            <h1>平台<span class="gradient-text">能力</span></h1>
            <p>全面的软件授权管理功能，助力产品安全运营</p>
        </div>
    </section>

    <section class="section" style="padding-top: 40px;">
        <div class="container">
            <div class="bento-grid">
                <div class="bento-item feature-lg" data-animate="fadeInUp">
                    <div class="bento-icon">
                        <svg width="24" height="24"><use href="#i-key"/></svg>
                    </div>
                    <h3 class="bento-title">授权发放与管理</h3>
                    <p class="bento-desc">集中管理授权信息、有效期与使用状态。支持批量发放与实时查询，让授权管理变得简单高效。通过灵活的授权策略满足不同业务场景需求。</p>
                    <div style="margin-top: 20px;">
                        <div class="feature-row">
                            <svg class="feature-row-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span class="feature-row-text">批量生成与发放授权</span>
                        </div>
                        <div class="feature-row">
                            <svg class="feature-row-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span class="feature-row-text">灵活的有效期与续费策略</span>
                        </div>
                        <div class="feature-row">
                            <svg class="feature-row-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span class="feature-row-text">实时状态监控与管理</span>
                        </div>
                    </div>
                </div>
                <div class="bento-item" data-animate="fadeInUp">
                    <div class="bento-icon">
                        <svg width="24" height="24"><use href="#i-shield"/></svg>
                    </div>
                    <h3 class="bento-title">在线验证与设备绑定</h3>
                    <p class="bento-desc">支持授权查询、在线激活与设备特征管理，基于设备指纹绑定确保授权安全可控。</p>
                </div>
                <div class="bento-item" data-animate="fadeInUp">
                    <div class="bento-icon">
                        <svg width="24" height="24"><use href="#i-zap"/></svg>
                    </div>
                    <h3 class="bento-title">API 接口集成</h3>
                    <p class="bento-desc">提供标准 RESTful API 接口，支持快速集成到您的软件产品中。</p>
                </div>
                <div class="bento-item feature-md" data-animate="fadeInUp">
                    <div class="bento-icon">
                        <svg width="24" height="24"><use href="#i-bar-chart"/></svg>
                    </div>
                    <h3 class="bento-title">数据分析与统计</h3>
                    <p class="bento-desc">实时掌握授权动态，提供多维度数据统计与分析报告。包括授权使用趋势、设备分布、活跃度分析等，帮助您做出更明智的商业决策。</p>
                </div>
                <div class="bento-item" data-animate="fadeInUp">
                    <div class="bento-icon">
                        <svg width="24" height="24"><use href="#i-code"/></svg>
                    </div>
                    <h3 class="bento-title">SDK 支持</h3>
                    <p class="bento-desc">提供多语言 SDK，降低集成门槛，快速实现授权验证功能。</p>
                </div>
                <div class="bento-item" data-animate="fadeInUp">
                    <div class="bento-icon">
                        <svg width="24" height="24"><use href="#i-doc"/></svg>
                    </div>
                    <h3 class="bento-title">完善的文档与支持</h3>
                    <p class="bento-desc">提供详细的文档中心与公告通知，帮助用户快速上手使用。</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section" style="padding-top: 40px;">
        <div class="container">
            <div class="cta-card">
                <h2 class="cta-title">准备开始使用<?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?>？</h2>
                <p class="cta-subtitle">注册即可体验完整的软件授权管理服务</p>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <a href="/user/register" class="btn btn-primary btn-lg">
                        立即注册
                        <svg width="16" height="16"><use href="#i-arrow-right"/></svg>
                    </a>
                    <a href="/documents" class="btn btn-outline btn-lg">查看文档</a>
                </div>
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