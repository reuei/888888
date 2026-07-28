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
        </defs>
    </svg>

    <header class="site-header">
        <div class="header-inner container">
            <a href="/" class="logo"><span class="logo-mark">☁</span><span>熵云</span></a>
            <nav class="main-nav">
                <a href="/" class="nav-link">首页</a>
                <a href="/platform" class="nav-link">平台能力</a>
                <a href="/license-query" class="nav-link">授权查询</a>
                <a href="/documents" class="nav-link">文档中心</a>
                <a href="/announcement" class="nav-link active">网站公告</a>
            </nav>
            <div class="auth-links">
                <a href="/user/login" class="btn btn-ghost btn-sm">登录</a>
                <a href="/user/register" class="btn btn-primary btn-sm">注册</a>
            </div>
            <button class="hamburger-btn" id="hamburgerBtn" aria-label="菜单"><span></span><span></span><span></span></button>
        </div>
    </header>

    <div class="mobile-nav" id="mobileNav">
        <a href="/" class="nav-link">首页</a>
        <a href="/platform" class="nav-link">平台能力</a>
        <a href="/license-query" class="nav-link">授权查询</a>
        <a href="/documents" class="nav-link">文档中心</a>
        <a href="/announcement" class="nav-link">网站公告</a>
    </div>

    <section class="hero" style="padding: 100px 0 60px;">
        <div class="container hero-content">
            <h1 class="hero-title">网站<span class="gradient-text">公告</span></h1>
            <p class="hero-subtitle">了解最新的平台动态与通知</p>
        </div>
    </section>

    <div class="container" style="padding-bottom: 60px;">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <?php if (!empty($siteSettings['announcement'])): ?>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #f0f0f0;">
                <svg width="20" height="20" style="color: #4f8cff;"><use href="#i-bell"/></svg>
                <span style="font-size: 18px; font-weight: 600; color: #1a1a2e;">平台公告</span>
            </div>
            <div style="color: #444; font-size: 15px; line-height: 1.9; white-space: pre-wrap;"><?= htmlspecialchars($siteSettings['announcement']) ?></div>
            <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 60px 20px;">
                <svg width="64" height="64" style="color: #c0c8d8; margin-bottom: 16px;"><use href="#i-bell"/></svg>
                <h3 style="font-size: 18px; color: #1a1a2e; margin-bottom: 8px;">暂无公告</h3>
                <p style="color: #687690; font-size: 14px;">当前没有发布任何公告</p>
            </div>
            <?php endif; ?>
        </div>

        <div style="max-width: 800px; margin: 24px auto 0; display: flex; gap: 16px; justify-content: center;">
            <a href="/" class="btn btn-ghost btn-sm">
                <svg width="14" height="14" style="vertical-align: middle; margin-right: 4px;"><use href="#i-arrow-right"/></svg>返回首页
            </a>
            <a href="/documents" class="btn btn-outline btn-sm">
                <svg width="14" height="14" style="vertical-align: middle; margin-right: 4px;"><use href="#i-doc"/></svg>查看文档
            </a>
        </div>
    </div>

    <footer class="site-footer">
        <div class="container">
            <p style="margin-bottom: 8px;">© <?= date('Y') ?> <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?> All Rights Reserved.</p>
            <?php if (!empty($siteSettings['icp'])): ?>
            <p style="color: #687690; font-size: 12px;"><?= htmlspecialchars($siteSettings['icp']) ?></p>
            <?php endif; ?>
        </div>
    </footer>

    <script>
        document.getElementById('hamburgerBtn').addEventListener('click', function() {
            document.getElementById('mobileNav').classList.toggle('show');
        });
    </script>
</body>
</html>