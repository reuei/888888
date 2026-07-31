<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文档中心 - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-home" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></symbol>
            <symbol id="i-doc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></symbol>
            <symbol id="i-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></symbol>
            <symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></symbol>
            <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></symbol>
            <symbol id="i-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
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
                <a href="/platform" class="nav-link">平台能力</a>
                <a href="/license-query" class="nav-link">授权查询</a>
                <a href="/documents" class="nav-link active">文档中心</a>
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
        <a href="/platform" class="nav-link">平台能力</a>
        <a href="/license-query" class="nav-link">授权查询</a>
        <a href="/documents" class="nav-link active">文档中心</a>
        <a href="javascript:void(0)" class="nav-link" onclick="showAnnouncement()">网站公告</a>
        <div class="auth-links" style="margin-top: 16px; display: flex; gap: 10px; justify-content: center;">
            <a href="/user/login" class="btn btn-ghost btn-sm">登录</a>
            <a href="/user/register" class="btn btn-primary btn-sm">注册</a>
        </div>
    </nav>

    <div style="padding-top: 80px;"></div>

    <div class="docs-layout">
        <aside class="docs-sidebar">
            <div class="docs-sidebar-inner">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat => $catDocs): ?>
                    <div class="docs-cat-section">
                        <div class="docs-cat-title"><?= htmlspecialchars($cat) ?></div>
                        <div class="docs-cat-body">
                            <?php foreach ($catDocs as $doc): ?>
                            <a href="/documents?doc=<?= $doc['id'] ?? 0 ?>" class="docs-link<?= ($currentDoc['id'] ?? 0) == ($doc['id'] ?? 0) ? ' active' : '' ?>"><?= htmlspecialchars($doc['title'] ?? '') ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 20px; color: var(--text-secondary); text-align: center;">暂无文档</div>
                <?php endif; ?>
            </div>
        </aside>
        <main class="docs-content">
            <?php if (!empty($currentDoc)): ?>
            <div class="docs-article">
                <div class="markdown-body">
                    <h1><?= htmlspecialchars($currentDoc['title'] ?? '') ?></h1>
                    <?= nl2br(htmlspecialchars($currentDoc['content'] ?? '')) ?>
                </div>
            </div>
            <?php else: ?>
            <div class="empty-state" style="padding: 80px 20px;">
                <svg width="64" height="64" style="color: var(--text-muted); margin-bottom: 16px;"><use href="#i-doc"/></svg>
                <h3 style="font-size: 18px; color: var(--text); margin-bottom: 8px;">选择文档</h3>
                <p style="color: var(--text-secondary); font-size: 14px;">请从左侧目录选择一篇文档查看</p>
            </div>
            <?php endif; ?>
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