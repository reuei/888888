<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '用户中心') ?> - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <!-- SVG Icon Definitions -->
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
            <symbol id="i-orders" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></symbol>
            <symbol id="i-log" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></symbol>
            <symbol id="i-logout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></symbol>
            <symbol id="i-settings" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></symbol>
            <symbol id="i-feedback" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></symbol>
            <symbol id="i-box" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></symbol>
            <symbol id="i-wallet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></symbol>
            <symbol id="i-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></symbol>
            <symbol id="i-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-info" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></symbol>
            <symbol id="i-warn" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></symbol>
            <symbol id="i-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></symbol>
            <symbol id="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></symbol>
            <symbol id="i-globe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></symbol>
            <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></symbol>
            <symbol id="i-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></symbol>
            <symbol id="i-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></symbol>
            <symbol id="i-trash" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></symbol>
            <symbol id="i-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></symbol>
            <symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></symbol>
            <symbol id="i-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></symbol>
            <symbol id="i-qq" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2c-3.5 0-6.5 2-7.5 5.5-.5 1.5-1 3.5-1 5.5 0 3.5 2.5 5 5.5 5 .5 0 1-.1 1.5-.3.5 1.5 1.5 2.8 3 2.8s2.5-1.3 3-2.8c.5.2 1 .3 1.5.3 3 0 5.5-1.5 5.5-5 0-2-.5-4-1-5.5C18.5 4 15.5 2 12 2z"/></symbol>
            <symbol id="i-captcha" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3 9 12 14 21 9"/></symbol>
            <symbol id="i-star" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></symbol>
            <symbol id="i-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></symbol>
            <symbol id="i-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-plugin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></symbol>
            <symbol id="i-message" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></symbol>
            <symbol id="i-dev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></symbol>
        </defs>
    </svg>

    <!-- Header -->
    <header class="site-header">
        <div class="header-inner container">
            <a href="/" class="logo"><span class="logo-mark">☁</span><span>熵云</span></a>
            <nav class="main-nav">
                <a href="/" class="nav-link">首页</a>
                <a href="/platform" class="nav-link">平台能力</a>
                <a href="/license-query" class="nav-link">授权查询</a>
                <a href="/documents" class="nav-link">文档中心</a>
            </nav>
            <div class="auth-links" style="display: flex; align-items: center; gap: 12px;">
                <button class="theme-toggle" id="themeToggle" title="切换主题" style="background: none; border: none; cursor: pointer; padding: 4px;">
                    <svg width="18" height="18" id="themeIcon"><use href="#i-moon"/></svg>
                </button>
                <div class="lang-switch" style="display: flex; border: 1px solid #e0e0e0; overflow: hidden;">
                    <a href="?lang=zh" class="lang-btn<?= ($lang ?? 'zh') === 'zh' ? ' active' : '' ?>" style="padding: 4px 10px; font-size: 12px; background: <?= ($lang ?? 'zh') === 'zh' ? '#4f8cff' : '#fff' ?>; color: <?= ($lang ?? 'zh') === 'zh' ? '#fff' : '#666' ?>;">中</a>
                    <a href="?lang=en" class="lang-btn<?= ($lang ?? 'zh') === 'en' ? ' active' : '' ?>" style="padding: 4px 10px; font-size: 12px; background: <?= ($lang ?? 'zh') === 'en' ? '#4f8cff' : '#fff' ?>; color: <?= ($lang ?? 'zh') === 'en' ? '#fff' : '#666' ?>;">EN</a>
                </div>
                <!-- Message Bell -->
                <div class="bell-wrapper" style="position: relative;">
                    <button class="bell-btn" id="bellBtn" title="消息通知" style="background: none; border: none; cursor: pointer; padding: 4px; position: relative;">
                        <svg width="20" height="20" style="color: var(--text-secondary);"><use href="#i-bell"/></svg>
                        <?php if (($unreadCount ?? 0) > 0): ?>
                        <span class="bell-badge" id="bellBadge" style="position: absolute; top: -2px; right: -2px; background: #ef4444; color: #fff; font-size: 10px; min-width: 16px; height: 16px; border-radius: 8px; display: flex; align-items: center; justify-content: center; padding: 0 4px; font-weight: 600;"><?= ($unreadCount ?? 0) > 99 ? '99+' : ($unreadCount ?? 0) ?></span>
                        <?php endif; ?>
                    </button>
                    <!-- Message Dropdown -->
                    <div class="message-dropdown" id="messageDropdown" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 8px; width: 340px; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow-lg); z-index: 200;">
                        <div class="md-header" style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-bottom: 1px solid var(--border);">
                            <span style="font-weight: 600; font-size: 14px; color: var(--text);">消息通知</span>
                            <?php if (($unreadCount ?? 0) > 0): ?>
                            <a href="/user/messages?action=read-all" style="font-size: 12px; color: var(--primary);">全部已读</a>
                            <?php endif; ?>
                        </div>
                        <div class="md-body" style="max-height: 320px; overflow-y: auto;">
                            <?php if (!empty($latestMessages)): ?>
                                <?php foreach ($latestMessages as $msg): ?>
                                <a href="/user/messages?id=<?= $msg['id'] ?? 0 ?>" class="md-item" style="display: block; padding: 12px 16px; border-bottom: 1px solid var(--border-light); transition: background var(--transition-fast); text-decoration: none; <?= ($msg['is_read'] ?? 0) == 0 ? 'background: var(--primary-light);' : '' ?>">
                                    <div style="font-size: 13px; font-weight: 500; color: var(--text); margin-bottom: 4px; <?= ($msg['is_read'] ?? 0) == 0 ? '' : 'color: var(--text-secondary);' ?>"><?= htmlspecialchars($msg['title'] ?? '') ?></div>
                                    <div style="font-size: 12px; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($msg['content'] ?? '') ?></div>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;"><?= htmlspecialchars($msg['created_at'] ?? '') ?></div>
                                </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="text-align: center; padding: 32px; color: var(--text-muted); font-size: 13px;">暂无消息</div>
                            <?php endif; ?>
                        </div>
                        <div class="md-footer" style="padding: 10px 16px; border-top: 1px solid var(--border); text-align: center;">
                            <a href="/user/messages" style="font-size: 13px; color: var(--primary);">查看全部消息</a>
                        </div>
                    </div>
                </div>
                <!-- User Avatar (email-based) -->
                <div class="user-avatar" style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #4f8cff, #3868ff); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; text-transform: uppercase;">
                    <?= mb_strtoupper(mb_substr($user['email'] ?? 'U', 0, 1)) ?>
                </div>
                <a href="/user/logout" class="btn btn-ghost btn-sm" style="color: #666;">退出</a>
                <!-- Hamburger Button -->
                <button class="hamburger-btn" id="hamburgerBtn" title="菜单" style="background: none; border: none; cursor: pointer; padding: 4px;">
                    <svg width="24" height="24" style="color: var(--text);"><use href="#i-platform"/></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Hamburger Overlay -->
    <div class="hamburger-overlay" id="hamburgerOverlay"></div>

    <!-- Hamburger Sidebar -->
    <div class="hamburger-sidebar" id="hamburgerSidebar">
        <div class="icon-rail">
            <div class="menu-item">
                <a href="/user/dashboard" class="menu-link<?= ($activeMenu ?? '') === 'dashboard' ? ' active' : '' ?>">
                    <svg width="18" height="18"><use href="#i-home"/></svg>
                    <span>用户中心</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="/user/workplace" class="menu-link<?= ($activeMenu ?? '') === 'workplace' ? ' active' : '' ?>">
                    <svg width="18" height="18"><use href="#i-platform"/></svg>
                    <span>工作台</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="/user/products" class="menu-link<?= ($activeMenu ?? '') === 'products' ? ' active' : '' ?>">
                    <svg width="18" height="18"><use href="#i-box"/></svg>
                    <span>产品中心</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="/user/my-products" class="menu-link<?= ($activeMenu ?? '') === 'myProducts' ? ' active' : '' ?>">
                    <svg width="18" height="18"><use href="#i-key"/></svg>
                    <span>我的产品</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="/user/orders" class="menu-link<?= ($activeMenu ?? '') === 'orders' ? ' active' : '' ?>">
                    <svg width="18" height="18"><use href="#i-orders"/></svg>
                    <span>我的订单</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="/user/balance" class="menu-link<?= ($activeMenu ?? '') === 'balance' ? ' active' : '' ?>">
                    <svg width="18" height="18"><use href="#i-wallet"/></svg>
                    <span>余额管理</span>
                </a>
            </div>
            <!-- 站点日志 with submenu -->
            <div class="menu-item has-submenu<?= in_array($activeMenu ?? '', ['balance-logs', 'login-logs', 'operation-logs']) ? ' open' : '' ?>">
                <a href="javascript:void(0)" class="menu-link submenu-parent" onclick="toggleSubmenu(this)">
                    <svg width="18" height="18"><use href="#i-log"/></svg>
                    <span>站点日志</span>
                    <span class="submenu-toggle" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--text-muted); transition: transform 0.2s ease;">
                        <svg width="12" height="12"><use href="#i-chevron"/></svg>
                    </span>
                </a>
                <ul class="submenu">
                    <li class="submenu-item">
                        <a href="/user/balance-logs" class="menu-link<?= ($activeMenu ?? '') === 'balance-logs' ? ' active' : '' ?>">
                            <span>余额明细</span>
                        </a>
                    </li>
                    <li class="submenu-item">
                        <a href="/user/login-logs" class="menu-link<?= ($activeMenu ?? '') === 'login-logs' ? ' active' : '' ?>">
                            <span>登录日志</span>
                        </a>
                    </li>
                    <li class="submenu-item">
                        <a href="/user/operation-logs" class="menu-link<?= ($activeMenu ?? '') === 'operation-logs' ? ' active' : '' ?>">
                            <span>操作日志</span>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- 账户设置 with submenu -->
            <div class="menu-item has-submenu<?= in_array($activeMenu ?? '', ['settings', 'rebind']) ? ' open' : '' ?>">
                <a href="javascript:void(0)" class="menu-link submenu-parent" onclick="toggleSubmenu(this)">
                    <svg width="18" height="18"><use href="#i-settings"/></svg>
                    <span>账户设置</span>
                    <span class="submenu-toggle" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--text-muted); transition: transform 0.2s ease;">
                        <svg width="12" height="12"><use href="#i-chevron"/></svg>
                    </span>
                </a>
                <ul class="submenu">
                    <li class="submenu-item">
                        <a href="/user/settings" class="menu-link<?= ($activeMenu ?? '') === 'settings' ? ' active' : '' ?>">
                            <span>基本设置</span>
                        </a>
                    </li>
                    <li class="submenu-item">
                        <a href="/user/rebind" class="menu-link<?= ($activeMenu ?? '') === 'rebind' ? ' active' : '' ?>">
                            <span>换绑信息</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="menu-item">
                <a href="/user/messages" class="menu-link<?= ($activeMenu ?? '') === 'messages' ? ' active' : '' ?>">
                    <svg width="18" height="18"><use href="#i-message"/></svg>
                    <span>消息中心</span>
                    <?php if (($unreadCount ?? 0) > 0): ?>
                    <span class="badge badge-danger" style="margin-left: auto; font-size: 10px; min-width: 18px; height: 18px; padding: 0 5px;"><?= ($unreadCount ?? 0) > 99 ? '99+' : ($unreadCount ?? 0) ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <div class="menu-item">
                <a href="/user/plugin-market" class="menu-link<?= ($activeMenu ?? '') === 'plugin-market' ? ' active' : '' ?>">
                    <svg width="18" height="18"><use href="#i-plugin"/></svg>
                    <span>插件市场</span>
                </a>
            </div>
            <?php if (($user['is_developer'] ?? 0) == 1): ?>
            <div class="menu-item">
                <a href="/user/developer" class="menu-link<?= ($activeMenu ?? '') === 'developer' ? ' active' : '' ?>">
                    <svg width="18" height="18"><use href="#i-dev"/></svg>
                    <span>开发者选项</span>
                </a>
            </div>
            <?php endif; ?>
            <div class="menu-item">
                <a href="/user/feedback" class="menu-link<?= ($activeMenu ?? '') === 'feedback' ? ' active' : '' ?>">
                    <svg width="18" height="18"><use href="#i-feedback"/></svg>
                    <span>意见反馈</span>
                </a>
            </div>
        </div>
    </div>

    <!-- User Layout -->
    <div class="user-layout" style="display: flex; min-height: calc(100vh - 80px);">
        <aside class="user-sidebar" id="userSidebar">
            <div class="icon-rail">
                <div class="menu-item">
                    <a href="/user/dashboard" class="menu-link<?= ($activeMenu ?? '') === 'dashboard' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-home"/></svg>
                        <span>用户中心</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/user/workplace" class="menu-link<?= ($activeMenu ?? '') === 'workplace' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-platform"/></svg>
                        <span>工作台</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/user/products" class="menu-link<?= ($activeMenu ?? '') === 'products' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-box"/></svg>
                        <span>产品中心</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/user/my-products" class="menu-link<?= ($activeMenu ?? '') === 'myProducts' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-key"/></svg>
                        <span>我的产品</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/user/orders" class="menu-link<?= ($activeMenu ?? '') === 'orders' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-orders"/></svg>
                        <span>我的订单</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/user/balance" class="menu-link<?= ($activeMenu ?? '') === 'balance' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-wallet"/></svg>
                        <span>余额管理</span>
                    </a>
                </div>
                <!-- 站点日志 with submenu -->
                <div class="menu-item has-submenu<?= in_array($activeMenu ?? '', ['balance-logs', 'login-logs', 'operation-logs']) ? ' open' : '' ?>">
                    <a href="javascript:void(0)" class="menu-link submenu-parent" onclick="toggleSubmenu(this)">
                        <svg width="18" height="18"><use href="#i-log"/></svg>
                        <span>站点日志</span>
                        <span class="submenu-toggle">
                            <svg width="12" height="12"><use href="#i-chevron"/></svg>
                        </span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="/user/balance-logs" class="menu-link<?= ($activeMenu ?? '') === 'balance-logs' ? ' active' : '' ?>">
                                <span>余额明细</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="/user/login-logs" class="menu-link<?= ($activeMenu ?? '') === 'login-logs' ? ' active' : '' ?>">
                                <span>登录日志</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="/user/operation-logs" class="menu-link<?= ($activeMenu ?? '') === 'operation-logs' ? ' active' : '' ?>">
                                <span>操作日志</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- 账户设置 with submenu -->
                <div class="menu-item has-submenu<?= in_array($activeMenu ?? '', ['settings', 'rebind']) ? ' open' : '' ?>">
                    <a href="javascript:void(0)" class="menu-link submenu-parent" onclick="toggleSubmenu(this)">
                        <svg width="18" height="18"><use href="#i-settings"/></svg>
                        <span>账户设置</span>
                        <span class="submenu-toggle">
                            <svg width="12" height="12"><use href="#i-chevron"/></svg>
                        </span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="/user/settings" class="menu-link<?= ($activeMenu ?? '') === 'settings' ? ' active' : '' ?>">
                                <span>基本设置</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="/user/rebind" class="menu-link<?= ($activeMenu ?? '') === 'rebind' ? ' active' : '' ?>">
                                <span>换绑信息</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="menu-item">
                    <a href="/user/messages" class="menu-link<?= ($activeMenu ?? '') === 'messages' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-message"/></svg>
                        <span>消息中心</span>
                        <?php if (($unreadCount ?? 0) > 0): ?>
                        <span class="badge badge-danger" style="margin-left: auto; font-size: 10px; min-width: 18px; height: 18px; padding: 0 5px;"><?= ($unreadCount ?? 0) > 99 ? '99+' : ($unreadCount ?? 0) ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="/user/plugin-market" class="menu-link<?= ($activeMenu ?? '') === 'plugin-market' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-plugin"/></svg>
                        <span>插件市场</span>
                    </a>
                </div>
                <?php if (($user['is_developer'] ?? 0) == 1): ?>
                <div class="menu-item">
                    <a href="/user/developer" class="menu-link<?= ($activeMenu ?? '') === 'developer' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-dev"/></svg>
                        <span>开发者选项</span>
                    </a>
                </div>
                <?php endif; ?>
                <div class="menu-item">
                    <a href="/user/feedback" class="menu-link<?= ($activeMenu ?? '') === 'feedback' ? ' active' : '' ?>">
                        <svg width="18" height="18"><use href="#i-feedback"/></svg>
                        <span>意见反馈</span>
                    </a>
                </div>
            </div>
        </aside>

        <main class="user-content">
            <?php if (isset($toast)): ?>
            <div class="toast toast-<?= $toast['type'] ?? 'success' ?>">
                <?= htmlspecialchars($toast['message'] ?? '') ?>
            </div>
            <?php endif; ?>
            <?= $content ?>
        </main>
    </div>

    <!-- Announcement Modal -->
    <?php if (!empty($siteSettings['announcement'])): ?>
    <div class="announcement-modal" id="announcementModal">
        <div class="am-overlay"></div>
        <div class="am-dialog">
            <div class="am-header">
                <h3><svg width="18" height="18" style="vertical-align: middle; margin-right: 6px;"><use href="#i-bell"/></svg>网站公告</h3>
                <button class="am-close" id="amCloseBtn"><svg width="18" height="18"><use href="#i-close"/></svg></button>
            </div>
            <div class="am-body">
                <p style="color: #444; line-height: 1.8; white-space: pre-wrap;"><?= htmlspecialchars($siteSettings['announcement']) ?></p>
            </div>
            <div class="am-footer">
                <button class="btn btn-primary btn-sm" id="amConfirmBtn">我知道了</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Toast Container -->
    <div class="toast-center" id="toastContainer"></div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <p style="margin-bottom: 8px;">© <?= date('Y') ?> <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?> All Rights Reserved.</p>
            <?php if (!empty($siteSettings['icp'])): ?>
            <p style="color: #687690; font-size: 12px;"><?= htmlspecialchars($siteSettings['icp']) ?></p>
            <?php endif; ?>
        </div>
    </footer>

    <script src="/static/js/main.js"></script>
    <script>
        // Theme toggle
        (function() {
            var btn = document.getElementById('themeToggle');
            var icon = document.getElementById('themeIcon');
            if (btn && icon) {
                btn.addEventListener('click', function() {
                    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                    if (isDark) {
                        document.documentElement.removeAttribute('data-theme');
                        icon.innerHTML = '<use href="#i-moon"/>';
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.setAttribute('data-theme', 'dark');
                        icon.innerHTML = '<use href="#i-sun"/>';
                        localStorage.setItem('theme', 'dark');
                    }
                });
                var saved = localStorage.getItem('theme');
                if (saved === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    icon.innerHTML = '<use href="#i-sun"/>';
                }
            }
        })();

        // Announcement modal
        (function() {
            var modal = document.getElementById('announcementModal');
            if (modal) {
                var closeBtn = document.getElementById('amCloseBtn');
                var confirmBtn = document.getElementById('amConfirmBtn');
                var dismissed = localStorage.getItem('announcementDismissed');
                if (!dismissed) {
                    modal.classList.add('show');
                }
                function dismiss() {
                    modal.classList.remove('show');
                    localStorage.setItem('announcementDismissed', '1');
                }
                if (closeBtn) closeBtn.addEventListener('click', dismiss);
                if (confirmBtn) confirmBtn.addEventListener('click', dismiss);
            }
        })();

        // Message bell dropdown toggle
        (function() {
            var bellBtn = document.getElementById('bellBtn');
            var dropdown = document.getElementById('messageDropdown');
            if (bellBtn && dropdown) {
                bellBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var isVisible = dropdown.style.display === 'block';
                    dropdown.style.display = isVisible ? 'none' : 'block';
                });
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target) && e.target !== bellBtn && !bellBtn.contains(e.target)) {
                        dropdown.style.display = 'none';
                    }
                });
            }
        })();

        // Submenu toggle
        function toggleSubmenu(el) {
            var parent = el.closest('.has-submenu');
            if (parent) {
                parent.classList.toggle('open');
            }
        }

        // Hamburger menu toggle
        (function() {
            var hamburgerBtn = document.getElementById('hamburgerBtn');
            var sidebar = document.getElementById('hamburgerSidebar');
            var overlay = document.getElementById('hamburgerOverlay');
            if (hamburgerBtn && sidebar && overlay) {
                function openMenu() {
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                }
                function closeMenu() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
                hamburgerBtn.addEventListener('click', openMenu);
                overlay.addEventListener('click', closeMenu);
            }
        })();
    </script>
</body>
</html>