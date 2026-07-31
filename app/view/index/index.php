<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?> - 授权服务平台</title>
    <link rel="stylesheet" href="/static/css/style.css">
    <?php if (!empty($siteSettings['site_description'])): ?>
    <meta name="description" content="<?php echo htmlspecialchars($siteSettings['site_description']) ?>">
    <?php endif; ?>
    <?php if (!empty($siteSettings['site_keywords'])): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($siteSettings['site_keywords']) ?>">
    <?php endif; ?>
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-home" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></symbol>
            <symbol id="i-platform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></symbol>
            <symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></symbol>
            <symbol id="i-license" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="16" height="16" rx="2" ry="2"/><path d="M10 10a2 2 0 1 1 4 0v4a2 2 0 0 1-4 0z"/><path d="M12 10V7"/><path d="M18 8h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2"/></symbol>
            <symbol id="i-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></symbol>
            <symbol id="i-announcement" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></symbol>
            <symbol id="i-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
            <symbol id="i-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></symbol>
            <symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"/><path d="M21 2l-9.6 9.6"/><path d="M15.5 7.5l3 3L22 7l-3-3"/></symbol>
            <symbol id="i-zap" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></symbol>
            <symbol id="i-download" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></symbol>
            <symbol id="i-box" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></symbol>
            <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></symbol>
            <symbol id="i-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></symbol>
            <symbol id="i-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></symbol>
            <symbol id="i-moon" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></symbol>
            <symbol id="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></symbol>
            <symbol id="i-globe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></symbol>
            <symbol id="i-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></symbol>
            <symbol id="i-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></symbol>
            <symbol id="i-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></symbol>
            <symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></symbol>
            <symbol id="i-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></symbol>
            <symbol id="i-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
            <symbol id="i-github" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.3 3.438 9.8 8.205 11.385.6.111.82-.26.82-.577 0-.285-.01-1.04-.015-2.04-3.338.725-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.43.37.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 21.795 24 17.295 24 12c0-6.63-5.37-12-12-12z"/></symbol>
            <symbol id="i-android" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-6z"/><path d="M7 12V8a5 5 0 0 1 10 0v4"/><line x1="8" y1="16" x2="8" y2="19"/><line x1="16" y1="16" x2="16" y2="19"/><line x1="9" y1="5" x2="8" y2="2"/><line x1="15" y1="5" x2="16" y2="2"/></symbol>
            <symbol id="i-apple" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20.94c1.5 0 2.75-.69 3.64-1.67.9-.99 1.36-2.28 1.36-3.83 0-1.51-.4-2.75-1.2-3.7-.8-.95-1.82-1.42-3.05-1.42-.56 0-1.1.12-1.62.36-.52.24-.93.5-1.23.79-.3-.29-.7-.55-1.23-.79-.52-.24-1.06-.36-1.62-.36-1.23 0-2.25.47-3.05 1.42C5.4 11.69 5 12.93 5 14.44c0 1.55.46 2.84 1.36 3.83.89.98 2.14 1.67 3.64 1.67.56 0 1.1-.09 1.62-.27.52-.18.92-.45 1.22-.81.3.36.7.63 1.22.81.52.18 1.06.27 1.62.27z"/><path d="M12 7c0-1.52.62-2.75 1.86-3.69.1-.08.2-.15.3-.2-.1.06-.2.13-.3.21C12.62 4.25 12 5.48 12 7z"/></symbol>
            <symbol id="i-star" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></symbol>
            <symbol id="i-check-circle" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></symbol>
            <symbol id="i-x-circle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></symbol>
            <symbol id="i-loader" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></symbol>
            <symbol id="i-grid" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></symbol>
            <symbol id="i-bag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></symbol>
            <symbol id="i-rocket" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></symbol>
            <symbol id="i-shield-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></symbol>
        </defs>
    </svg>

    <header class="site-header">
        <div class="header-inner">
            <button class="hamburger-btn" id="hamburgerBtn" aria-label="菜单">
                <span></span><span></span><span></span>
            </button>
            <a href="/" class="logo">
                <span class="logo-mark"><?php echo htmlspecialchars(mb_substr($siteSettings['site_name'] ?? '熵云', 0, 1)) ?></span>
                <div class="logo-text">
                    <span class="logo-name"><?php echo htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></span>
                    <span class="logo-sub">授权服务平台</span>
                </div>
            </a>
            <nav class="main-nav">
                <a href="/" class="nav-link active">首页</a>
                <a href="/platform" class="nav-link" onclick="if(location.pathname==='/'){event.preventDefault();document.getElementById('features').scrollIntoView({behavior:'smooth'});}">平台能力</a>
                <a href="/license-query" class="nav-link">授权查询</a>
                <a href="/documents" class="nav-link">文档中心</a>
                <a href="javascript:void(0)" class="nav-link" onclick="showAnnouncement()">网站公告</a>
                <a href="/app-download" class="nav-link">APP下载</a>
            </nav>
            <div class="header-right">
                <a href="/user/login" class="btn btn-outline">登录</a>
                <a href="/user/register" class="btn btn-primary">
                    <svg width="16" height="16"><use href="#i-user"/></svg>
                    注册
                </a>
            </div>
        </div>
    </header>

    <nav class="mobile-nav" id="mobileNav">
        <a href="/" class="nav-link active" onclick="document.getElementById('mobileNav').classList.remove('open')">首页</a>
        <a href="/#features" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open')">平台能力</a>
        <a href="/license-query" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open')">授权查询</a>
        <a href="/documents" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open')">文档中心</a>
        <a href="javascript:void(0)" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open');showAnnouncement();">网站公告</a>
        <a href="/app-download" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open')">APP下载</a>
        <a href="/user/login" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open')">用户登录</a>
    </nav>

    <div class="layout-background">
        <div class="layout">
            <div class="decorator1"></div>
            <div class="decorator2"></div>
            <div class="gridGlow"></div>
            <section class="hero">
                <div class="container">
                    <div class="hero-content">
                        <div class="hero-left">
                            <div class="hero-badge">
                                <span class="dot"></span>
                                安全授权 · 稳定验证 · 高效管理
                            </div>
                            <h1 class="hero-title"><?php echo htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?><span class="gradient-text">授权服务平台</span></h1>
                            <p class="hero-subtitle"><?php echo htmlspecialchars($siteSettings['site_description'] ?? '为软件开发者与企业提供一站式授权管理解决方案，涵盖授权发放、在线验证、设备绑定与数据分析等核心能力。') ?></p>
                            <div class="hero-buttons">
                                <a href="/user/register" class="btn btn-primary btn-lg">
                                    开始使用
                                    <svg width="16" height="16"><use href="#i-arrow-right"/></svg>
                                </a>
                                <a href="/#features" class="btn btn-outline btn-lg">了解更多</a>
                            </div>
                        </div>
                        <div class="hero-right">
                            <div class="preview-card">
                                <div class="preview-sidebar">
                                    <div class="ps-item ps-active"></div>
                                    <div class="ps-item"></div>
                                    <div class="ps-item"></div>
                                    <div class="ps-item"></div>
                                    <div class="ps-item"></div>
                                </div>
                                <div class="preview-main">
                                    <div class="pm-ghost pm-title"></div>
                                    <div class="pm-ghost pm-subtitle"></div>
                                    <div class="pm-stats">
                                        <div class="pm-stat">
                                            <div class="pm-stat-bar pm-stat-bar1"></div>
                                            <div class="pm-stat-num"></div>
                                        </div>
                                        <div class="pm-stat">
                                            <div class="pm-stat-bar pm-stat-bar2"></div>
                                            <div class="pm-stat-num"></div>
                                        </div>
                                        <div class="pm-stat">
                                            <div class="pm-stat-bar pm-stat-bar3"></div>
                                            <div class="pm-stat-num"></div>
                                        </div>
                                        <div class="pm-stat">
                                            <div class="pm-stat-bar pm-stat-bar4"></div>
                                            <div class="pm-stat-num"></div>
                                        </div>
                                    </div>
                                    <div class="pm-table">
                                        <div class="pm-row"><div class="pm-cell pm-cell1"></div><div class="pm-cell pm-cell2"></div><div class="pm-cell pm-cell3"></div></div>
                                        <div class="pm-row"><div class="pm-cell pm-cell1"></div><div class="pm-cell pm-cell2"></div><div class="pm-cell pm-cell3"></div></div>
                                        <div class="pm-row"><div class="pm-cell pm-cell1"></div><div class="pm-cell pm-cell2"></div><div class="pm-cell pm-cell3"></div></div>
                                        <div class="pm-row"><div class="pm-cell pm-cell1"></div><div class="pm-cell pm-cell2"></div><div class="pm-cell pm-cell3"></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <section id="features" class="section">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">核心能力</div>
                <h2 class="section-title">让软件授权管理更简单、更可靠</h2>
                <p class="section-subtitle">覆盖授权创建、激活验证、设备绑定与服务支持，帮助开发者和用户高效管理软件使用权益。</p>
            </div>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon gradient">
                        <svg width="28" height="28"><use href="#i-key"/></svg>
                    </div>
                    <h3 class="feature-title">授权发放与管理</h3>
                    <p class="feature-desc">集中管理授权信息、有效期与使用状态，让每一份软件授权都有清晰记录。</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon gradient">
                        <svg width="28" height="28"><use href="#i-shield"/></svg>
                    </div>
                    <h3 class="feature-title">在线验证与设备绑定</h3>
                    <p class="feature-desc">支持授权查询、在线激活与设备特征管理，保障授权使用过程安全、稳定。</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon gradient">
                        <svg width="28" height="28"><use href="#i-doc"/></svg>
                    </div>
                    <h3 class="feature-title">授权服务支持</h3>
                    <p class="feature-desc">通过文档中心、公告与服务入口，帮助用户快速了解和使用授权服务。</p>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($products)): ?>
    <section class="section">
        <div class="container">
            <div class="section-header">
                <div class="section-tag">产品服务</div>
                <h2 class="section-title">精选产品方案</h2>
                <p class="section-subtitle">多元化产品组合，满足不同规模和场景的授权管理需求。</p>
            </div>
            <div class="products-grid">
                <?php $i = 0; foreach ($products as $product): if ($i++ >= 4) break; ?>
                <div class="product-card">
                    <div class="product-head">
                        <span class="product-icon">
                            <svg width="24" height="24"><use href="#i-box"/></svg>
                        </span>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name'] ?? '') ?></h3>
                    </div>
                    <p class="product-desc"><?php echo htmlspecialchars($product['description'] ?? '') ?></p>
                    <div class="product-foot">
                        <div class="product-price">
                            <span class="currency">¥</span><?php echo number_format($product['price'] ?? 0, 2) ?>
                        </div>
                        <a href="/user/register" class="btn btn-primary">立即购买</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <div class="container">
        <div class="stats-banner">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?php echo (int)($stats['products'] ?? 0) ?></div>
                    <div class="stat-label">产品数量</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo (int)($stats['users'] ?? 0) ?></div>
                    <div class="stat-label">注册用户</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?php echo (int)($stats['licenses'] ?? 0) ?></div>
                    <div class="stat-label">授权总数</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">99.9%</div>
                    <div class="stat-label">服务可用性</div>
                </div>
            </div>
        </div>
    </div>

    <div class="cta-section">
        <div class="container">
            <div class="cta-card">
                <h2>准备好开始了吗？</h2>
                <p>立即注册，体验专业的软件授权管理服务。</p>
                <a href="/user/register" class="btn btn-primary btn-lg">
                    免费注册
                    <svg width="16" height="16"><use href="#i-arrow-right"/></svg>
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty($siteSettings['announcement'])): ?>
    <div class="announcement-modal" id="announcementModal">
        <div class="arco-modal-mask am-overlay fade"></div>
        <div class="arco-modal-wrapper">
            <div class="arco-modal _announcementModal">
                <div class="arco-modal-header">
                    <span class="arco-modal-title">
                        <svg width="18" height="18"><use href="#i-bell"/></svg>
                        网站公告
                    </span>
                    <button class="arco-modal-close-icon" id="amCloseBtn" aria-label="关闭">
                        <svg width="18" height="18"><use href="#i-x"/></svg>
                    </button>
                </div>
                <div class="arco-modal-content">
                    <div class="_announcementContent markdown-body">
                        <?php echo nl2br(htmlspecialchars($siteSettings['announcement'])) ?>
                    </div>
                </div>
                <div class="arco-modal-footer">
                    <button class="btn btn-secondary" id="amHide1h">一小时内不再显示</button>
                    <button class="btn btn-primary" id="amConfirmBtn">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-links">
                <a href="/">首页</a>
                <a href="/#features">平台能力</a>
                <a href="/license-query">授权查询</a>
                <a href="/documents">文档中心</a>
                <a href="/app-download">APP下载</a>
            </div>
            <p class="footer-copyright">© 2026 <?php echo htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?> All Rights Reserved. <?php echo !empty($siteSettings['icp']) ? htmlspecialchars($siteSettings['icp']) : '' ?></p>
        </div>
    </footer>

    <script src="/static/js/main.js"></script>
</body>
</html>
