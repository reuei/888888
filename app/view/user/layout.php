<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? '用户中心') ?> - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
    <style>
        .uc-layout { display: flex; min-height: calc(100vh - 64px); }
        .uc-sidebar { width: 248px; background: var(--bg-elevated); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; position: sticky; top: 64px; height: calc(100vh - 64px); overflow-y: auto; }
        .uc-sidebar-header { padding: 20px 20px 16px; border-bottom: 1px solid var(--border-light); }
        .uc-user-card { display: flex; align-items: center; gap: 12px; }
        .uc-avatar { width: 44px; height: 44px; border-radius: 12px; background: var(--primary-gradient); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 600; flex-shrink: 0; }
        .uc-user-info { flex: 1; min-width: 0; }
        .uc-user-name { font-size: 14px; font-weight: 600; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .uc-user-email { font-size: 12px; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .uc-sidebar-nav { flex: 1; padding: 12px; overflow-y: auto; }
        .uc-nav-group { margin-bottom: 8px; }
        .uc-nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; color: var(--text-secondary); font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.15s ease; text-decoration: none; position: relative; }
        .uc-nav-item:hover { background: var(--bg-hover); color: var(--text); }
        .uc-nav-item.active { background: var(--primary-gradient); color: #fff; box-shadow: 0 4px 14px rgba(var(--primary-rgb), 0.3); }
        .uc-nav-item.active svg { color: #fff; }
        .uc-nav-icon { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--text-muted); }
        .uc-nav-item:hover .uc-nav-icon { color: var(--primary); }
        .uc-nav-badge { margin-left: auto; background: var(--danger); color: #fff; font-size: 11px; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center; line-height: 1.2; }
        .uc-nav-submenu { display: none; padding-left: 32px; margin-top: 4px; }
        .uc-nav-submenu.open { display: block; }
        .uc-nav-submenu .uc-nav-item { padding: 8px 12px; font-size: 13px; }
        .uc-nav-submenu .uc-nav-item .uc-nav-icon { width: 16px; height: 16px; }
        .uc-nav-toggle { margin-left: auto; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s; }
        .uc-nav-parent.open .uc-nav-toggle { transform: rotate(90deg); }
        .uc-nav-children { display: none; }
        .uc-nav-parent.has-submenu.open ~ .uc-nav-children,
        .uc-nav-parent.open + .uc-nav-children { display: block; }
        .uc-sidebar-footer { padding: 16px 20px; border-top: 1px solid var(--border-light); }
        .uc-sidebar-footer-btn { display: flex; align-items: center; gap: 8px; width: 100%; padding: 10px; border-radius: 8px; background: var(--danger-light); color: var(--danger); font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.15s; text-decoration: none; }
        .uc-sidebar-footer-btn:hover { opacity: 0.9; }
        .uc-content { flex: 1; padding: 24px; max-width: calc(100% - 248px); }
        .uc-page-header { margin-bottom: 24px; }
        .uc-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-muted); margin-bottom: 8px; }
        .uc-breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .uc-breadcrumb a:hover { color: var(--primary); }
        .uc-breadcrumb .separator { color: var(--border-strong); }
        .uc-breadcrumb .current { color: var(--text); }
        .uc-page-title { font-size: 24px; font-weight: 700; color: var(--text); margin: 0; letter-spacing: -0.02em; }
        .uc-page-subtitle { font-size: 14px; color: var(--text-secondary); margin-top: 4px; }
        .uc-toast { position: fixed; top: 80px; left: 50%; transform: translateX(-50%); padding: 12px 20px; border-radius: 10px; box-shadow: var(--shadow-lg); z-index: 1000; display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 500; animation: ucToastIn 0.3s ease; }
        .uc-toast-success { background: var(--success); color: #fff; }
        .uc-toast-error { background: var(--danger); color: #fff; }
        .uc-toast-warning { background: var(--warning); color: #fff; }
        .uc-toast-info { background: var(--info); color: #fff; }
        @keyframes ucToastIn { from { opacity: 0; transform: translateX(-50%) translateY(-10px); } to { opacity: 1; transform: translateX(-50%) translateY(0); } }
        .uc-sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 200; opacity: 0; transition: opacity 0.3s; }
        .uc-sidebar-overlay.show { display: block; opacity: 1; }
        @media (max-width: 768px) {
            .uc-sidebar { position: fixed; left: -260px; top: 0; z-index: 300; transition: left 0.3s ease; height: 100vh; }
            .uc-sidebar.show { left: 0; }
            .uc-content { max-width: 100%; padding: 16px; }
            .uc-mobile-menu-btn { display: flex; }
            .uc-sidebar-header { padding-top: 60px; }
        }
        .uc-mobile-menu-btn { display: none; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; background: var(--bg-hover); color: var(--text); cursor: pointer; }
        .uc-mobile-menu-btn:hover { background: var(--primary-50); color: var(--primary); }
        .uc-mobile-header { display: none; align-items: center; gap: 12px; padding: 12px 16px; background: var(--bg-elevated); border-bottom: 1px solid var(--border); }
        .uc-mobile-header-title { font-size: 16px; font-weight: 600; color: var(--text); }
        @media (max-width: 768px) {
            .uc-mobile-header { display: flex; position: sticky; top: 0; z-index: 150; }
            .uc-page-header { display: none; }
            .uc-content { padding-top: 0; }
        }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; display: flex; flex-direction: column; gap: 8px; transition: all 0.2s; }
        .stat-card:hover { box-shadow: var(--shadow-md); border-color: var(--primary); }
        .stat-card .stat-icon-box { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; }
        .stat-card .stat-value { font-size: 28px; font-weight: 700; color: var(--text); }
        .stat-card .stat-label { font-size: 13px; color: var(--text-secondary); }
        .stat-card.accent-blue .stat-icon-box { background: var(--primary-50); color: var(--primary); }
        .stat-card.accent-green .stat-icon-box { background: var(--success-light); color: var(--success); }
        .stat-card.accent-orange .stat-icon-box { background: var(--warning-light); color: var(--warning); }
        .stat-card.accent-purple .stat-icon-box { background: var(--accent); color: #fff; }
        .quick-actions { display: flex; gap: 12px; flex-wrap: wrap; }
        .welcome-banner { background: var(--primary-gradient); border-radius: var(--radius-lg); padding: 28px; color: #fff; margin-bottom: 24px; position: relative; overflow: hidden; }
        .welcome-banner::before { content: ''; position: absolute; top: -50%; right: -20%; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; }
        .welcome-banner h2 { font-size: 20px; font-weight: 700; margin-bottom: 6px; position: relative; z-index: 1; }
        .welcome-banner p { opacity: 0.9; font-size: 14px; position: relative; z-index: 1; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .price-tag { font-size: 24px; font-weight: 700; }
        .price-free { color: var(--success); }
        .price-paid { color: var(--danger); }
        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty-state-icon { width: 64px; height: 64px; margin: 0 auto 16px; background: var(--bg-tertiary); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); }
        .empty-state-text { font-size: 14px; margin-bottom: 16px; }
        .filter-bar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .filter-search { flex: 1; min-width: 200px; position: relative; }
        .filter-search input { width: 100%; padding: 10px 14px 10px 40px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg-card); font-size: 14px; color: var(--text); outline: none; transition: border-color 0.15s; }
        .filter-search input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-100); }
        .filter-search svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }
        .filter-tabs { display: flex; gap: 4px; background: var(--bg-tertiary); padding: 4px; border-radius: var(--radius); }
        .filter-tab { padding: 6px 14px; font-size: 13px; font-weight: 500; color: var(--text-secondary); border-radius: calc(var(--radius) - 2px); cursor: pointer; transition: all 0.15s; text-decoration: none; }
        .filter-tab:hover { color: var(--text); }
        .filter-tab.active { background: var(--bg-card); color: var(--primary); box-shadow: var(--shadow-xs); }
        .form-section { margin-bottom: 32px; }
        .form-section-title { display: flex; align-items: center; gap: 8px; font-size: 16px; font-weight: 600; color: var(--text); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-light); }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (max-width: 640px) { .two-col { grid-template-columns: 1fr; } }
        .message-list .message-item { padding: 16px 0; border-bottom: 1px solid var(--border-light); cursor: pointer; transition: background 0.15s; }
        .message-list .message-item:last-child { border-bottom: none; }
        .message-list .message-item:hover { background: var(--bg-hover); margin: 0 -16px; padding: 16px; border-radius: var(--radius); }
        .message-item-row { display: flex; align-items: flex-start; gap: 12px; }
        .message-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--primary); flex-shrink: 0; margin-top: 6px; }
        .message-dot.read { background: var(--border-strong); }
        .message-content { flex: 1; min-width: 0; }
        .message-title { font-size: 14px; font-weight: 500; color: var(--text); margin-bottom: 4px; }
        .message-preview { font-size: 13px; color: var(--text-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .message-time { font-size: 12px; color: var(--text-muted); flex-shrink: 0; }
        .message-full-content { margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border-light); font-size: 14px; color: var(--text); line-height: 1.8; white-space: pre-wrap; }
        .feedback-item { border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 16px; }
        .feedback-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
        .feedback-content { color: var(--text); font-size: 14px; line-height: 1.7; margin-bottom: 12px; white-space: pre-wrap; }
        .feedback-reply { background: var(--primary-50); border-left: 3px solid var(--primary); padding: 12px 16px; border-radius: 0 var(--radius) var(--radius) 0; }
        .feedback-reply-title { font-size: 12px; font-weight: 600; color: var(--primary); margin-bottom: 4px; }
        .feedback-reply-content { font-size: 13px; color: var(--text-secondary); line-height: 1.6; white-space: pre-wrap; }
        .payment-option { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border: 1px solid var(--border); border-radius: var(--radius); cursor: pointer; transition: all 0.15s; }
        .payment-option:hover { border-color: var(--primary); background: var(--primary-50); }
        .payment-option.selected { border-color: var(--primary); background: var(--primary-50); }
        .payment-option input[type="radio"] { accent-color: var(--primary); }
        .payment-option-info { flex: 1; }
        .payment-option-name { font-size: 14px; font-weight: 500; color: var(--text); }
        .payment-option-desc { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .buy-summary-card { background: var(--primary-gradient); border-radius: var(--radius-lg); padding: 28px; color: #fff; text-align: center; }
        .buy-summary-product { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
        .buy-summary-price { font-size: 32px; font-weight: 700; margin-bottom: 16px; }
        .buy-summary-user { display: flex; justify-content: space-around; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.2); }
        .buy-summary-user-item .label { font-size: 12px; opacity: 0.8; }
        .buy-summary-user-item .value { font-size: 14px; font-weight: 600; margin-top: 4px; }
    </style>
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
            <symbol id="i-send" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></symbol>
            <symbol id="i-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></symbol>
            <symbol id="i-cart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></symbol>
            <symbol id="i-credit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></symbol>
            <symbol id="i-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
            <symbol id="i-arrow-up" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></symbol>
            <symbol id="i-arrow-down" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></symbol>
        </defs>
    </svg>

    <header class="site-header">
        <div class="header-inner container">
            <a href="/" class="logo">
                <span class="logo-mark">☁</span>
                <span><?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></span>
            </a>

            <nav class="main-nav">
                <a href="/" class="nav-link">首页</a>
                <a href="/platform" class="nav-link">平台能力</a>
                <a href="/license-query" class="nav-link">授权查询</a>
                <a href="/documents" class="nav-link">文档中心</a>
            </nav>

            <div class="auth-links">
                <button class="theme-toggle" id="themeToggle" title="切换主题">
                    <svg width="18" height="18" id="themeIcon"><use href="#i-moon"/></svg>
                </button>

                <div class="lang-switch">
                    <a href="?lang=zh" class="lang-btn<?= ($lang ?? 'zh') === 'zh' ? ' active' : '' ?>">中</a>
                    <a href="?lang=en" class="lang-btn<?= ($lang ?? 'zh') === 'en' ? ' active' : '' ?>">EN</a>
                </div>

                <div class="bell-wrapper">
                    <button class="bell-btn" id="bellBtn" title="消息通知">
                        <svg width="20" height="20" style="color: var(--text-secondary);"><use href="#i-bell"/></svg>
                        <?php if (($unreadCount ?? 0) > 0): ?>
                        <span class="bell-badge" id="bellBadge"><?= ($unreadCount ?? 0) > 99 ? '99+' : ($unreadCount ?? 0) ?></span>
                        <?php endif; ?>
                    </button>

                    <div class="message-dropdown" id="messageDropdown">
                        <div class="md-header">
                            <span>消息通知</span>
                            <?php if (($unreadCount ?? 0) > 0): ?>
                            <a href="/user/messages?action=read-all" style="font-size: 12px; color: var(--primary);">全部已读</a>
                            <?php endif; ?>
                        </div>
                        <div class="md-body">
                            <?php if (!empty($latestMessages)): ?>
                                <?php foreach ($latestMessages as $msg): ?>
                                <a href="/user/messages?id=<?= $msg['id'] ?? 0 ?>" class="md-item" style="<?= ($msg['is_read'] ?? 0) == 0 ? 'background: var(--primary-light);' : '' ?>">
                                    <div style="font-size: 13px; font-weight: 500; color: var(--text); margin-bottom: 4px; <?= ($msg['is_read'] ?? 0) == 0 ? '' : 'color: var(--text-secondary);' ?>"><?= htmlspecialchars($msg['title'] ?? '') ?></div>
                                    <div style="font-size: 12px; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($msg['content'] ?? '') ?></div>
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;"><?= htmlspecialchars($msg['created_at'] ?? '') ?></div>
                                </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="text-align: center; padding: 32px; color: var(--text-muted); font-size: 13px;">暂无消息</div>
                            <?php endif; ?>
                        </div>
                        <div class="md-footer">
                            <a href="/user/messages">查看全部消息</a>
                        </div>
                    </div>
                </div>

                <div class="user-avatar" title="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    <?= mb_strtoupper(mb_substr($user['email'] ?? 'U', 0, 1)) ?>
                </div>

                <a href="/user/logout" class="btn btn-ghost btn-sm">退出</a>

                <button class="hamburger-btn" id="hamburgerBtn" title="菜单">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <div class="uc-mobile-header">
        <button class="uc-mobile-menu-btn" id="mobileMenuBtn">
            <svg width="20" height="20"><use href="#i-menu"/></svg>
        </button>
        <span class="uc-mobile-header-title"><?= htmlspecialchars($pageTitle ?? '用户中心') ?></span>
    </div>

    <div class="uc-layout">
        <div class="uc-sidebar-overlay" id="ucSidebarOverlay"></div>
        <aside class="uc-sidebar" id="ucSidebar">
            <div class="uc-sidebar-header">
                <div class="uc-user-card">
                    <div class="uc-avatar"><?= mb_strtoupper(mb_substr($user['email'] ?? 'U', 0, 1)) ?></div>
                    <div class="uc-user-info">
                        <div class="uc-user-name"><?= htmlspecialchars($user['username'] ?? '') ?></div>
                        <div class="uc-user-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                    </div>
                </div>
            </div>
            <nav class="uc-sidebar-nav">
                <div class="uc-nav-group">
                    <a href="/user/dashboard" class="uc-nav-item<?= ($activeMenu ?? '') === 'dashboard' ? ' active' : '' ?>">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-home"/></svg></span>
                        <span>用户中心</span>
                    </a>
                    <a href="/user/workplace" class="uc-nav-item<?= ($activeMenu ?? '') === 'workplace' ? ' active' : '' ?>">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-platform"/></svg></span>
                        <span>工作台</span>
                    </a>
                    <a href="/user/products" class="uc-nav-item<?= ($activeMenu ?? '') === 'products' ? ' active' : '' ?>">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-box"/></svg></span>
                        <span>产品中心</span>
                    </a>
                    <a href="/user/my-products" class="uc-nav-item<?= ($activeMenu ?? '') === 'myProducts' ? ' active' : '' ?>">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-key"/></svg></span>
                        <span>我的产品</span>
                    </a>
                    <a href="/user/orders" class="uc-nav-item<?= ($activeMenu ?? '') === 'orders' ? ' active' : '' ?>">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-orders"/></svg></span>
                        <span>我的订单</span>
                    </a>
                    <a href="/user/balance" class="uc-nav-item<?= ($activeMenu ?? '') === 'balance' ? ' active' : '' ?>">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-wallet"/></svg></span>
                        <span>余额管理</span>
                    </a>
                </div>
                <div class="uc-nav-group">
                    <div class="uc-nav-item uc-nav-parent has-submenu<?= in_array($activeMenu ?? '', ['balance-logs', 'login-logs', 'operation-logs']) ? ' open' : '' ?>" onclick="toggleSubmenu(this)">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-log"/></svg></span>
                        <span>站点日志</span>
                        <span class="uc-nav-toggle"><svg width="16" height="16"><use href="#i-chevron"/></svg></span>
                    </div>
                    <div class="uc-nav-children" style="<?= in_array($activeMenu ?? '', ['balance-logs', 'login-logs', 'operation-logs']) ? 'display: block;' : '' ?>">
                        <a href="/user/balance-logs" class="uc-nav-item<?= ($activeMenu ?? '') === 'balance-logs' ? ' active' : '' ?>">
                            <span class="uc-nav-icon"><svg width="16" height="16"><use href="#i-wallet"/></svg></span>
                            <span>余额明细</span>
                        </a>
                        <a href="/user/login-logs" class="uc-nav-item<?= ($activeMenu ?? '') === 'login-logs' ? ' active' : '' ?>">
                            <span class="uc-nav-icon"><svg width="16" height="16"><use href="#i-user"/></svg></span>
                            <span>登录日志</span>
                        </a>
                        <a href="/user/operation-logs" class="uc-nav-item<?= ($activeMenu ?? '') === 'operation-logs' ? ' active' : '' ?>">
                            <span class="uc-nav-icon"><svg width="16" height="16"><use href="#i-doc"/></svg></span>
                            <span>操作日志</span>
                        </a>
                    </div>
                </div>
                <div class="uc-nav-group">
                    <div class="uc-nav-item uc-nav-parent has-submenu<?= in_array($activeMenu ?? '', ['settings', 'rebind']) ? ' open' : '' ?>" onclick="toggleSubmenu(this)">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-settings"/></svg></span>
                        <span>账户设置</span>
                        <span class="uc-nav-toggle"><svg width="16" height="16"><use href="#i-chevron"/></svg></span>
                    </div>
                    <div class="uc-nav-children" style="<?= in_array($activeMenu ?? '', ['settings', 'rebind']) ? 'display: block;' : '' ?>">
                        <a href="/user/settings" class="uc-nav-item<?= ($activeMenu ?? '') === 'settings' ? ' active' : '' ?>">
                            <span class="uc-nav-icon"><svg width="16" height="16"><use href="#i-user"/></svg></span>
                            <span>基本设置</span>
                        </a>
                        <a href="/user/rebind" class="uc-nav-item<?= ($activeMenu ?? '') === 'rebind' ? ' active' : '' ?>">
                            <span class="uc-nav-icon"><svg width="16" height="16"><use href="#i-key"/></svg></span>
                            <span>换绑信息</span>
                        </a>
                    </div>
                </div>
                <div class="uc-nav-group">
                    <a href="/user/messages" class="uc-nav-item<?= ($activeMenu ?? '') === 'messages' ? ' active' : '' ?>">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-message"/></svg></span>
                        <span>消息中心</span>
                        <?php if (($unreadCount ?? 0) > 0): ?>
                        <span class="uc-nav-badge"><?= ($unreadCount ?? 0) > 99 ? '99+' : ($unreadCount ?? 0) ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="/user/plugin-market" class="uc-nav-item<?= ($activeMenu ?? '') === 'plugin-market' ? ' active' : '' ?>">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-plugin"/></svg></span>
                        <span>插件市场</span>
                    </a>
                    <?php if (($user['is_developer'] ?? 0) == 1): ?>
                    <a href="/user/developer" class="uc-nav-item<?= ($activeMenu ?? '') === 'developer' ? ' active' : '' ?>">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-dev"/></svg></span>
                        <span>开发者选项</span>
                    </a>
                    <?php endif; ?>
                    <a href="/user/feedback" class="uc-nav-item<?= ($activeMenu ?? '') === 'feedback' ? ' active' : '' ?>">
                        <span class="uc-nav-icon"><svg width="20" height="20"><use href="#i-feedback"/></svg></span>
                        <span>意见反馈</span>
                    </a>
                </div>
            </nav>
            <div class="uc-sidebar-footer">
                <a href="/user/logout" class="uc-sidebar-footer-btn">
                    <svg width="16" height="16"><use href="#i-logout"/></svg>
                    <span>退出登录</span>
                </a>
            </div>
        </aside>

        <main class="uc-content">
            <div class="uc-page-header">
                <div class="uc-breadcrumb">
                    <a href="/user/dashboard">用户中心</a>
                    <?php if (!empty($breadcrumb)): ?>
                        <?php foreach ($breadcrumb as $crumb): ?>
                            <span class="separator">/</span>
                            <?php if (isset($crumb['url'])): ?>
                                <a href="<?= $crumb['url'] ?>"><?= htmlspecialchars($crumb['label']) ?></a>
                            <?php else: ?>
                                <span class="current"><?= htmlspecialchars($crumb['label']) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="separator">/</span>
                        <span class="current"><?= htmlspecialchars($pageTitle ?? '首页') ?></span>
                    <?php endif; ?>
                </div>
                <h1 class="uc-page-title"><?= htmlspecialchars($pageTitle ?? '用户中心') ?></h1>
            </div>

            <?php if (isset($toast)): ?>
            <div class="uc-toast uc-toast-<?= $toast['type'] ?? 'success' ?>">
                <svg width="16" height="16"><use href="#i-<?= $toast['type'] ?? 'check' ?>"/></svg>
                <?= htmlspecialchars($toast['message'] ?? '') ?>
            </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>

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

    <div class="toast-center" id="toastContainer"></div>

    <footer class="site-footer">
        <div class="container">
            <p style="margin-bottom: 8px;">© <?= date('Y') ?> <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?> All Rights Reserved.</p>
            <?php if (!empty($siteSettings['icp'])): ?>
            <p style="color: var(--text-muted); font-size: 12px;"><?= htmlspecialchars($siteSettings['icp']) ?></p>
            <?php endif; ?>
        </div>
    </footer>

    <script src="/static/js/main.js"></script>
    <script>
        function toggleSubmenu(el) {
            var parent = el;
            parent.classList.toggle('open');
            var children = parent.nextElementSibling;
            if (children && children.classList.contains('uc-nav-children')) {
                children.style.display = parent.classList.contains('open') ? 'block' : 'none';
            }
        }

        (function() {
            var mobileMenuBtn = document.getElementById('mobileMenuBtn');
            var hamburgerBtn = document.getElementById('hamburgerBtn');
            var sidebar = document.getElementById('ucSidebar');
            var overlay = document.getElementById('ucSidebarOverlay');

            function openSidebar() {
                sidebar.classList.add('show');
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    openSidebar();
                });
            }

            if (hamburgerBtn) {
                hamburgerBtn.addEventListener('click', function() {
                    openSidebar();
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    closeSidebar();
                });
            }

            var mdOverlay = document.querySelector('.hamburger-overlay');
            var mdSidebar = document.getElementById('hamburgerSidebar');
            var mdBtn = document.getElementById('bellBtn');
            var mdDropdown = document.getElementById('messageDropdown');

            if (mdBtn && mdDropdown) {
                mdBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    mdDropdown.style.display = mdDropdown.style.display === 'block' ? 'none' : 'block';
                });
                document.addEventListener('click', function(e) {
                    if (!mdDropdown.contains(e.target)) {
                        mdDropdown.style.display = 'none';
                    }
                });
            }

            var themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    var currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                    var newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', newTheme);
                    var themeIcon = document.getElementById('themeIcon');
                    if (themeIcon) {
                        themeIcon.innerHTML = newTheme === 'dark'
                            ? '<use href="#i-sun"/>'
                            : '<use href="#i-moon"/>';
                    }
                    try {
                        localStorage.setItem('theme', newTheme);
                    } catch(e) {}
                });
                try {
                    var savedTheme = localStorage.getItem('theme');
                    if (savedTheme) {
                        document.documentElement.setAttribute('data-theme', savedTheme);
                        var themeIcon = document.getElementById('themeIcon');
                        if (themeIcon) {
                            themeIcon.innerHTML = savedTheme === 'dark'
                                ? '<use href="#i-sun"/>'
                                : '<use href="#i-moon"/>';
                        }
                    }
                } catch(e) {}
            }

            var amModal = document.getElementById('announcementModal');
            if (amModal) {
                var amCloseBtn = document.getElementById('amCloseBtn');
                var amConfirmBtn = document.getElementById('amConfirmBtn');
                var amOverlay = amModal.querySelector('.am-overlay');
                amModal.classList.add('show');
                if (amCloseBtn) amCloseBtn.addEventListener('click', function() { amModal.classList.remove('show'); });
                if (amConfirmBtn) amConfirmBtn.addEventListener('click', function() { amModal.classList.remove('show'); });
                if (amOverlay) amOverlay.addEventListener('click', function() { amModal.classList.remove('show'); });
            }
        })();
    </script>
</body>
</html>
