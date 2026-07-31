<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文档中心 - <?php echo htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-home" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></symbol>
            <symbol id="i-platform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></symbol>
            <symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></symbol>
            <symbol id="i-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></symbol>
            <symbol id="i-announcement" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></symbol>
            <symbol id="i-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"/><path d="M21 2l-9.6 9.6"/><path d="M15.5 7.5l3 3L22 7l-3-3"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></symbol>
            <symbol id="i-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></symbol>
            <symbol id="i-box" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></symbol>
            <symbol id="i-download" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></symbol>
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
                    <span class="logo-sub">文档中心</span>
                </div>
            </a>
            <nav class="main-nav">
                <a href="/" class="nav-link">首页</a>
                <a href="/#features" class="nav-link">平台能力</a>
                <a href="/license-query" class="nav-link">授权查询</a>
                <a href="/documents" class="nav-link active">文档中心</a>
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
        <a href="/" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open')">首页</a>
        <a href="/#features" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open')">平台能力</a>
        <a href="/license-query" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open')">授权查询</a>
        <a href="/documents" class="nav-link active" onclick="document.getElementById('mobileNav').classList.remove('open')">文档中心</a>
        <a href="javascript:void(0)" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open');showAnnouncement();">网站公告</a>
        <a href="/app-download" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open')">APP下载</a>
        <a href="/user/login" class="nav-link" onclick="document.getElementById('mobileNav').classList.remove('open')">用户登录</a>
    </nav>

    <div style="padding-top: 80px;"></div>

    <div class="docs-layout">
        <aside class="docs-sidebar">
            <div class="docs-sidebar-inner">
                <div class="docs-sidebar-title">文档目录</div>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat => $catDocs): ?>
                    <div class="docs-cat-section">
                        <div class="docs-cat-title"><?php echo htmlspecialchars($cat) ?></div>
                        <ul class="docs-cat-list">
                            <?php foreach ($catDocs as $doc): ?>
                            <li>
                                <a href="/documents?doc=<?php echo $doc['id'] ?? 0 ?>" class="docs-link <?php echo ($currentDoc['id'] ?? 0) == ($doc['id'] ?? 0) ? 'active' : '' ?>">
                                    <svg width="14" height="14"><use href="#i-doc"/></svg>
                                    <span><?php echo htmlspecialchars($doc['title'] ?? '') ?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="muted centered" style="padding: 20px; color: var(--text-secondary); text-align: center;">暂无文档</div>
                <?php endif; ?>
            </div>
        </aside>
        <main class="docs-content">
            <?php if (!empty($currentDoc)): ?>
            <article class="docs-article">
                <div class="docs-breadcrumb">
                    <a href="/">首页</a> / <a href="/documents">文档中心</a> / <?php echo htmlspecialchars($currentDoc['title'] ?? '') ?>
                </div>
                <h1 class="docs-title"><?php echo htmlspecialchars($currentDoc['title'] ?? '') ?></h1>
                <div class="docs-meta">
                    <small>最后更新：<?php echo htmlspecialchars($currentDoc['updated_at'] ?? '') ?>　类别：<?php echo htmlspecialchars($currentDoc['category'] ?? '') ?></small>
                </div>
                <hr class="docs-divider">
                <div class="markdown-body">
                    <?php
                    $content = htmlspecialchars($currentDoc['content'] ?? '');
                    $content = nl2br($content);
                    $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);
                    $content = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $content);
                    $content = preg_replace('/`([^`]+)`/', '<code>$1</code>', $content);
                    echo $content;
                    ?>
                </div>
            </article>
            <?php else: ?>
            <div class="empty-state centered" style="padding: 80px 20px; text-align: center;">
                <svg width="64" height="64" style="color: var(--text-muted); margin-bottom: 16px;"><use href="#i-doc"/></svg>
                <h3 style="font-size: 18px; color: var(--text); margin-bottom: 8px;">选择文档</h3>
                <p style="color: var(--text-secondary); font-size: 14px;">请从左侧目录选择一篇文档查看</p>
            </div>
            <?php endif; ?>
        </main>
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
            <p class="footer-copyright">© <?php echo date('Y') ?> <?php echo htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?> All Rights Reserved. <?php echo !empty($siteSettings['icp']) ? htmlspecialchars($siteSettings['icp']) : '' ?></p>
        </div>
    </footer>

    <script src="/static/js/main.js"></script>
</body>
</html>
