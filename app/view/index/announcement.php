<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网站公告 - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></symbol>
            <symbol id="i-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
        </defs>
    </svg>

    <header class="site-header">
        <div class="header-inner">
            <button class="hamburger-btn" id="hamburgerBtn" aria-label="菜单">
                <span></span><span></span><span></span>
            </button>
            <a href="/" class="logo">
                <span class="logo-mark"><?= htmlspecialchars(mb_substr($siteSettings['site_name'] ?? '熵云', 0, 1)) ?></span>
                <div class="logo-text">
                    <span class="logo-name"><?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></span>
                    <span class="logo-sub">网站公告</span>
                </div>
            </a>
            <nav class="main-nav">
                <a href="/" class="nav-link">首页</a>
                <a href="/platform" class="nav-link">平台能力</a>
                <a href="/license-query" class="nav-link">授权查询</a>
                <a href="/documents" class="nav-link">文档中心</a>
                <a href="/announcement" class="nav-link active">网站公告</a>
            </nav>
            <div class="header-right">
                <a href="/user/login" class="icon-btn" title="登录">
                    <svg width="20" height="20"><use href="#i-user"/></svg>
                </a>
            </div>
        </div>
    </header>

    <nav class="mobile-nav" id="mobileNav">
        <a href="/" class="nav-link">首页</a>
        <a href="/platform" class="nav-link">平台能力</a>
        <a href="/license-query" class="nav-link">授权查询</a>
        <a href="/documents" class="nav-link">文档中心</a>
        <a href="/announcement" class="nav-link active">网站公告</a>
    </nav>

    <section class="platform-hero">
        <div class="platform-hero-content">
            <div class="hero-badge">
                <span class="dot"></span>
                最新平台动态
            </div>
            <h1>网站<span class="gradient-text">公告</span></h1>
            <p>了解最新的平台动态与通知</p>
        </div>
    </section>

    <section class="section" style="padding-top: 40px; padding-bottom: 60px;">
        <div class="container">
            <div class="card" style="max-width: 800px; margin: 0 auto;" data-animate="fadeInUp">
                <?php if (!empty($siteSettings['announcement'])): ?>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--border-light);">
                    <svg width="20" height="20" style="color: var(--primary);"><use href="#i-bell"/></svg>
                    <span style="font-size: 18px; font-weight: 600; color: var(--text);">平台公告</span>
                </div>
                <div style="color: var(--text-secondary); font-size: 15px; line-height: 1.9; white-space: pre-wrap;"><?= htmlspecialchars($siteSettings['announcement']) ?></div>
                <?php else: ?>
                <div class="empty-state" style="padding: 60px 20px;">
                    <svg width="64" height="64" style="color: var(--text-muted); margin-bottom: 16px;"><use href="#i-bell"/></svg>
                    <h3 style="font-size: 18px; color: var(--text); margin-bottom: 8px;">暂无公告</h3>
                    <p style="color: var(--text-secondary); font-size: 14px;">当前没有发布任何公告</p>
                </div>
                <?php endif; ?>
            </div>

            <div style="max-width: 800px; margin: 24px auto 0; display: flex; gap: 12px; justify-content: center;">
                <a href="/" class="btn btn-ghost btn-sm">
                    <svg width="14" height="14" style="vertical-align: middle; margin-right: 4px;"><use href="#i-arrow-right"/></svg>返回首页
                </a>
                <a href="/documents" class="btn btn-outline btn-sm">
                    <svg width="14" height="14" style="vertical-align: middle; margin-right: 4px;"><use href="#i-doc"/></svg>查看文档
                </a>
            </div>
        </div>
    </section>

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