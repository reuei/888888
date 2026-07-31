<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>授权查询 - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></symbol>
            <symbol id="i-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></symbol>
            <symbol id="i-box" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></symbol>
            <symbol id="i-bell" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></symbol>
            <symbol id="i-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
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
                <a href="/license-query" class="nav-link active">授权查询</a>
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
        <a href="/platform" class="nav-link">平台能力</a>
        <a href="/license-query" class="nav-link active">授权查询</a>
        <a href="/documents" class="nav-link">文档中心</a>
        <a href="javascript:void(0)" class="nav-link" onclick="showAnnouncement()">网站公告</a>
        <div class="auth-links" style="margin-top: 16px; display: flex; gap: 10px; justify-content: center;">
            <a href="/user/login" class="btn btn-ghost btn-sm">登录</a>
            <a href="/user/register" class="btn btn-primary btn-sm">注册</a>
        </div>
    </nav>

    <section class="page-banner">
        <div class="container">
            <h1>授权<span class="gradient-text">查询</span></h1>
            <p>输入授权码查询授权信息</p>
        </div>
    </section>

    <section class="license-query-section">
        <div class="container">
            <div class="query-input-wrap">
                <div class="card">
                    <form method="GET" action="/license-query">
                        <div class="form-group">
                            <label class="form-label" for="license_key">授权码</label>
                            <input type="text" class="form-control" id="license_key" name="key" placeholder="请输入您的授权码" value="<?= htmlspecialchars($key ?? '') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <svg width="16" height="16" style="vertical-align: middle; margin-right: 6px;"><use href="#i-search"/></svg>查询授权
                        </button>
                    </form>
                </div>
            </div>

            <?php if (isset($result)): ?>
            <div class="query-result">
                <div class="card">
                    <h3 style="font-size: 18px; font-weight: 600; color: var(--text); margin-bottom: 16px;">查询结果</h3>
                    <?php if ($result): ?>
                    <div>
                        <div class="query-result-row">
                            <span class="label">授权码</span>
                            <span class="value" style="font-family: monospace; font-size: 13px;"><?= htmlspecialchars($result['license_key'] ?? '-') ?></span>
                        </div>
                        <div class="query-result-row">
                            <span class="label">产品名称</span>
                            <span class="value"><?= htmlspecialchars($result['product_name'] ?? '-') ?></span>
                        </div>
                        <div class="query-result-row">
                            <span class="label">授权状态</span>
                            <?php $status = $result['status'] ?? 0; ?>
                            <span class="badge <?= $status == 1 ? 'badge-success' : 'badge-danger' ?>"><?= $status == 1 ? '有效' : '已失效' ?></span>
                        </div>
                        <div class="query-result-row">
                            <span class="label">到期时间</span>
                            <span class="value"><?= htmlspecialchars($result['expires_at'] ?? '永久') ?></span>
                        </div>
                        <div class="query-result-row">
                            <span class="label">绑定用户</span>
                            <span class="value"><?= htmlspecialchars($result['username'] ?? '未知') ?></span>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="empty-state" style="padding: 40px 20px;">
                        <svg width="48" height="48" style="color: var(--text-muted); margin-bottom: 12px;"><use href="#i-x"/></svg>
                        <p style="color: var(--text-secondary); font-size: 14px;"><?= htmlspecialchars($error ?? '未找到该授权码对应的信息') ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
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