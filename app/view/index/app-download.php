<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商家工作台 - 下载 - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
    <style>
        .app-top-header {
            top: 0;
            height: 64px;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(229, 230, 235, 0.5);
        }
        [data-theme="dark"] .app-top-header {
            background-color: rgba(11, 14, 20, 0.8);
            border-bottom-color: rgba(39, 46, 59, 0.5);
        }
        .app-top-header .row {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
            height: 100%;
        }
        .app-top-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }
        .app-top-logo:hover {
            color: inherit;
        }
        .app-logo-img {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            object-fit: cover;
            background: linear-gradient(135deg, #4080FF 0%, #722ED1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
        }
        .app-logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        .app-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
        }
        .app-sublabel {
            font-size: 11px;
            color: var(--text-tertiary);
            margin-top: 2px;
        }
        .app-back-home {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 36px;
            padding: 0 14px;
        }
        .app-info-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 32px;
        }
        .app-info-col-left {
            flex-shrink: 0;
        }
        .app-icon-large {
            width: 120px;
            height: 120px;
            border-radius: 28px;
            object-fit: cover;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.25);
            border: 2px solid rgba(255, 255, 255, 0.25);
        }
        .app-icon-large-fallback {
            width: 120px;
            height: 120px;
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
            backdrop-filter: blur(8px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            font-weight: 800;
            color: #FFFFFF;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);
        }
        .app-info-col-mid {
            flex: 1;
            min-width: 240px;
        }
        .app-product-name {
            font-size: 40px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: -0.02em;
            margin: 0 0 10px 0;
        }
        .app-product-version {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 9999px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 14px;
            backdrop-filter: blur(4px);
        }
        .app-product-slogan {
            display: flex;
            align-items: center;
            font-size: 17px;
            color: rgba(255, 255, 255, 0.92);
        }
        .app-info-col-right {
            flex-shrink: 0;
        }
        .app-official-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 14px;
            color: #FFFFFF;
            font-size: 13px;
            font-weight: 500;
            backdrop-filter: blur(8px);
        }
        .app-official-badge svg {
            color: #A9F0D1;
        }
        .app-slogan-row {
            margin-top: var(--space-6);
            margin-bottom: var(--space-5);
        }
        .app-desc-text {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.85);
            max-width: 760px;
            line-height: 1.8;
        }
        .device-mock-row {
            display: flex;
            flex-wrap: wrap;
            margin-top: var(--space-5);
            margin-bottom: var(--space-4);
        }
        .device-mock-img {
            max-width: 320px;
            width: 100%;
            border-radius: 32px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.35);
            border: 3px solid rgba(255, 255, 255, 0.15);
        }
        .device-mock {
            width: 300px;
            height: 600px;
            background: linear-gradient(145deg, #1A1F2E 0%, #0F1219 100%);
            border-radius: 44px;
            padding: 14px;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.08);
            position: relative;
        }
        .device-mock::before {
            content: '';
            position: absolute;
            top: 22px;
            left: 50%;
            transform: translateX(-50%);
            width: 110px;
            height: 28px;
            background: #000;
            border-radius: 20px;
            z-index: 2;
        }
        .device-mock .device-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, #F7F8FA 0%, #FFFFFF 100%);
            border-radius: 32px;
            overflow: hidden;
            position: relative;
        }
        [data-theme="dark"] .device-mock .device-screen {
            background: linear-gradient(180deg, #161A22 0%, #0B0E14 100%);
        }
        .device-top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 40px 18px 12px;
        }
        .device-top-bar .dots {
            display: flex;
            gap: 6px;
        }
        .device-top-bar .dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
        }
        .device-top-bar .time-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
        }
        .device-content-mock {
            padding: 14px 16px;
        }
        .mock-header-line {
            height: 24px;
            width: 70%;
            border-radius: 6px;
            background: linear-gradient(90deg, #E5E6EB 0%, #F2F3F5 100%);
            margin-bottom: 18px;
        }
        [data-theme="dark"] .mock-header-line {
            background: linear-gradient(90deg, #272E3B 0%, #1F242E 100%);
        }
        .mock-cards-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 18px;
        }
        .mock-card {
            height: 78px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(64, 128, 255, 0.1) 0%, rgba(114, 46, 209, 0.1) 100%);
            border: 1px solid rgba(64, 128, 255, 0.15);
            position: relative;
            overflow: hidden;
        }
        .mock-card::after {
            content: '';
            position: absolute;
            bottom: 10px;
            left: 12px;
            right: 40%;
            height: 6px;
            border-radius: 3px;
            background: rgba(64, 128, 255, 0.25);
        }
        .mock-card::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 12px;
            width: 26px;
            height: 26px;
            border-radius: 7px;
            background: var(--primary-gradient);
            opacity: 0.5;
        }
        .mock-list-lines {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .mock-line {
            height: 56px;
            border-radius: 10px;
            background: var(--bg-card);
            box-shadow: var(--shadow-1);
            border: 1px solid var(--border-light);
            position: relative;
        }
        .mock-line::before {
            content: '';
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: var(--primary-gradient-soft);
        }
        .mock-line::after {
            content: '';
            position: absolute;
            left: 60px;
            top: 50%;
            transform: translateY(-50%);
            width: 55%;
            height: 14px;
            border-radius: 4px;
            background: var(--border-light);
        }
        [data-theme="dark"] .mock-line {
            background: var(--bg-elevated);
        }
        .download-buttons-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            margin-top: var(--space-5);
        }
        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            padding: 14px 24px;
            min-width: 260px;
            border-radius: 16px;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
            backdrop-filter: blur(8px);
            cursor: pointer;
        }
        .btn-download.android {
            background: linear-gradient(135deg, rgba(61, 220, 132, 0.95) 0%, rgba(42, 178, 97, 0.95) 100%);
            border-color: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            box-shadow: 0 8px 24px rgba(42, 178, 97, 0.35);
        }
        .btn-download.android:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(42, 178, 97, 0.5);
            color: #FFFFFF;
        }
        .btn-download.ios {
            background: linear-gradient(135deg, rgba(30, 30, 30, 0.95) 0%, rgba(60, 60, 60, 0.95) 100%);
            border-color: rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }
        .btn-download.ios:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.5);
            color: #FFFFFF;
        }
        .btn-download.disabled {
            background: rgba(134, 144, 156, 0.5);
            border-color: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            box-shadow: none;
            cursor: not-allowed;
            opacity: 0.8;
        }
        .btn-download.disabled:hover {
            transform: none;
            box-shadow: none;
        }
        .btn-download svg {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
        }
        .btn-download-text {
            display: flex;
            flex-direction: column;
            text-align: left;
            line-height: 1.3;
        }
        .btn-download-label {
            font-size: 16px;
            font-weight: 600;
        }
        .btn-download-subversion {
            font-size: 12px;
            opacity: 0.85;
            margin-top: 2px;
        }
        .app-foot-note {
            text-align: center;
            margin-top: var(--space-8);
            color: rgba(255, 255, 255, 0.85);
            line-height: 2;
        }
        .app-foot-note small {
            font-size: 13px;
        }
        .app-download-footer {
            padding: var(--space-8) 0 var(--space-10);
            text-align: center;
            background-color: var(--bg-page);
            border-top: 1px solid var(--border-light);
        }
        .app-download-footer .footer-copyright {
            font-size: 13px;
            color: var(--text-tertiary);
        }
        @media (max-width: 768px) {
            .app-info-row {
                flex-direction: column;
                text-align: center;
                gap: var(--space-5);
            }
            .app-info-col-mid {
                text-align: center;
            }
            .app-product-slogan {
                justify-content: center;
            }
            .app-info-col-right {
                display: none;
            }
            .app-product-name {
                font-size: 30px;
            }
            .btn-download {
                min-width: 100%;
                width: 100%;
                justify-content: center;
            }
            .app-desc-text {
                text-align: center;
            }
            .device-mock {
                width: 260px;
                height: 520px;
            }
        }
    </style>
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-home" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></symbol>
            <symbol id="i-platform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></symbol>
            <symbol id="i-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></symbol>
            <symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></symbol>
            <symbol id="i-announcement" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
            <symbol id="i-download" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></symbol>
            <symbol id="i-android" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12v5c0 2.2 1.8 4 4 4h10c2.2 0 4-1.8 4-4v-5"/><path d="M6 12V8a6 6 0 0 1 12 0v4"/><line x1="6" y1="15" x2="6" y2="19"/><line x1="18" y1="15" x2="18" y2="19"/><line x1="9" y1="4" x2="8" y2="2"/><line x1="15" y1="4" x2="16" y2="2"/><circle cx="9" cy="10" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="10" r="1" fill="currentColor" stroke="none"/></symbol>
            <symbol id="i-apple" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20.94c1.5 0 2.75-.5 3.5-1.3.75-.8 1.5-2 1.5-3.5 0-.5-.1-1-.3-1.5-.2-.5-.5-1-.9-1.4-.4-.4-1-.7-1.7-.8-.7 0-1.3.2-1.7.5-.4.3-.7.7-1.1.7s-.7-.4-1.1-.7c-.4-.3-1-.5-1.7-.5-.7 0-1.3.3-1.7.7-.4.4-.7.9-.9 1.4-.2.5-.3 1-.3 1.5 0 1.5.75 2.7 1.5 3.5.75.8 2 1.3 3.5 1.3z"/><path d="M12 8c1 0 2-.5 2.5-1.5S15 5 15 4c-.7 0-1.4.3-2 1-.6-.7-1.3-1-2-1 0 1 .3 2 1 3z"/></symbol>
            <symbol id="i-arrow-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></symbol>
            <symbol id="i-shield-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></symbol>
            <symbol id="i-check-circle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></symbol>
            <symbol id="i-smartphone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12" y2="18"/></symbol>
            <symbol id="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></symbol>
            <symbol id="i-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></symbol>
            <symbol id="i-globe" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></symbol>
            <symbol id="i-box" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></symbol>
        </defs>
    </svg>

    <?php
    $app = $apps[0] ?? [];
    $appName = $app['app_name'] ?? '商家工作台';
    $appVersion = $app['app_version'] ?? 'v1.0.2';
    $appLogo = $app['app_logo'] ?? '';
    $appScreenshot = $app['app_screenshot'] ?? '';
    $appDescription = $app['app_description'] ?? '面向商家打造的一站式授权管理工作台，随时随地管理产品授权、订单与财务数据。';
    $appSlogan = $app['app_slogan'] ?? '高效运营 · 安全管理 · 随时掌握';
    $androidUrl = $app['android_url'] ?? '';
    $androidVersion = $app['android_version'] ?? $appVersion;
    $iosUrl = $app['ios_url'] ?? '';
    $iosVersion = $app['ios_version'] ?? $appVersion;
    $siteName = $siteSettings['site_name'] ?? '熵云';
    $siteIcp = $siteSettings['icp'] ?? '';
    ?>

    <header class="app-top-header position-fixed w-100" style="z-index: 50;">
        <div class="container">
            <div class="row justify-between align-center" style="height: 64px;">
                <a class="app-top-logo" href="/">
                    <?php if (!empty($appLogo)): ?>
                        <img class="app-logo-img" src="<?= htmlspecialchars($appLogo) ?>" alt="<?= htmlspecialchars($siteName) ?>">
                    <?php else: ?>
                        <div class="app-logo-img"><?= htmlspecialchars(mb_substr($siteName, 0, 1)) ?></div>
                    <?php endif; ?>
                    <div class="app-logo-text">
                        <span class="app-name"><?= htmlspecialchars($siteName) ?></span>
                        <span class="app-sublabel">官方下载中心</span>
                    </div>
                </a>
                <a class="btn btn-outline app-back-home" href="/">
                    <svg width="16" height="16"><use href="#i-arrow-left"/></svg>
                    <span>返回首页</span>
                </a>
            </div>
        </div>
    </header>

    <section class="app-download-hero">
        <div class="container">
            <div class="app-info-row row align-center">
                <div class="app-info-col-left">
                    <?php if (!empty($appLogo)): ?>
                        <img class="app-icon-large" src="<?= htmlspecialchars($appLogo) ?>" alt="<?= htmlspecialchars($appName) ?>">
                    <?php else: ?>
                        <div class="app-icon-large-fallback"><?= htmlspecialchars(mb_substr($appName, 0, 1)) ?></div>
                    <?php endif; ?>
                </div>
                <div class="app-info-col-mid">
                    <h1 class="app-product-name"><?= htmlspecialchars($appName) ?></h1>
                    <div class="app-product-version">版本 <?= htmlspecialchars($appVersion) ?></div>
                    <div class="app-product-slogan">
                        <svg width="18" height="18" style="margin-right: 6px;"><use href="#i-shield-check"/></svg>
                        <span><?= htmlspecialchars($appSlogan) ?></span>
                    </div>
                </div>
                <div class="app-info-col-right">
                    <div class="app-official-badge">
                        <svg width="20" height="20"><use href="#i-check-circle"/></svg>
                        <span>官方发布 安全下载</span>
                    </div>
                </div>
            </div>

            <div class="app-slogan-row mt-6 mb-5">
                <p class="app-desc-text"><?= htmlspecialchars($appDescription) ?></p>
            </div>

            <div class="device-mock-row row justify-center align-center mt-5 mb-4">
                <?php if (!empty($appScreenshot)): ?>
                    <img class="device-mock-img" src="<?= htmlspecialchars($appScreenshot) ?>" alt="应用截图">
                <?php else: ?>
                    <div class="device-mock">
                        <div class="device-screen">
                            <div class="device-top-bar">
                                <div class="dots">
                                    <span class="dot" style="background:#F53F3F;"></span>
                                    <span class="dot" style="background:#FF7D00;"></span>
                                    <span class="dot" style="background:#00B42A;"></span>
                                </div>
                                <span class="time-label">9:41</span>
                            </div>
                            <div class="device-content-mock">
                                <div class="mock-header-line"></div>
                                <div class="mock-cards-row">
                                    <div class="mock-card"></div>
                                    <div class="mock-card"></div>
                                </div>
                                <div class="mock-list-lines">
                                    <div class="mock-line"></div>
                                    <div class="mock-line"></div>
                                    <div class="mock-line"></div>
                                    <div class="mock-line"></div>
                                    <div class="mock-line"></div>
                                    <div class="mock-line"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="download-buttons-section row justify-center" style="gap: 16px;">
                <?php if (!empty($androidUrl)): ?>
                    <a type="button" class="btn-download android" href="<?= htmlspecialchars($androidUrl) ?>" target="_blank" rel="noreferrer">
                        <svg width="28" height="28"><use href="#i-android"/></svg>
                        <div class="btn-download-text">
                            <div class="btn-download-label">立即下载 Android</div>
                            <div class="btn-download-subversion">版本 <?= htmlspecialchars($androidVersion) ?></div>
                        </div>
                    </a>
                <?php else: ?>
                    <button type="button" class="btn-download android disabled" disabled>
                        <svg width="28" height="28"><use href="#i-android"/></svg>
                        <div class="btn-download-text">
                            <div class="btn-download-label">Android 即将上线</div>
                            <div class="btn-download-subversion">敬请期待</div>
                        </div>
                    </button>
                <?php endif; ?>

                <?php if (!empty($iosUrl)): ?>
                    <a type="button" class="btn-download ios" href="<?= htmlspecialchars($iosUrl) ?>" target="_blank" rel="noreferrer">
                        <svg width="28" height="28"><use href="#i-apple"/></svg>
                        <div class="btn-download-text">
                            <div class="btn-download-label">立即下载 iOS</div>
                            <div class="btn-download-subversion">版本 <?= htmlspecialchars($iosVersion) ?></div>
                        </div>
                    </a>
                <?php else: ?>
                    <button type="button" class="btn-download ios disabled" disabled>
                        <svg width="28" height="28"><use href="#i-apple"/></svg>
                        <div class="btn-download-text">
                            <div class="btn-download-label">iOS 即将上线</div>
                            <div class="btn-download-subversion">敬请期待</div>
                        </div>
                    </button>
                <?php endif; ?>
            </div>

            <div class="app-foot-note">
                <small>官方发布 · 安全下载 · 全平台适配</small>
                <br/>
                <small style="color: rgba(255, 255, 255, 0.65);">如遇下载问题请联系客服或加入官方交流群</small>
            </div>
        </div>
    </section>

    <footer class="app-download-footer">
        <div class="container">
            <div class="footer-copyright">
                © <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. All rights reserved.
                <?php if (!empty($siteIcp)): ?>
                    <span style="margin-left: 12px;"><?= htmlspecialchars($siteIcp) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </footer>

    <script src="/static/js/main.js"></script>
</body>
</html>
