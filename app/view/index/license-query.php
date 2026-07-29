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
        </defs>
    </svg>

    <header class="site-header">
        <div class="header-inner container">
            <a href="/" class="logo"><span class="logo-mark">☁</span><span>熵云</span></a>
            <nav class="main-nav">
                <a href="/" class="nav-link">首页</a>
                <a href="/platform" class="nav-link">平台能力</a>
                <a href="/license-query" class="nav-link active">授权查询</a>
                <a href="/documents" class="nav-link">文档中心</a>
                <a href="javascript:void(0)" class="nav-link" onclick="showAnnouncementPopup()">网站公告</a>
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
        <a href="javascript:void(0)" class="nav-link" onclick="showAnnouncementPopup()">网站公告</a>
    </div>

    <div style="padding-top: 80px;">
        <div class="container">
            <h1 style="font-size: 28px; color: #1a1a2e; margin-bottom: 8px;">授权查询</h1>
            <p style="color: #687690; font-size: 14px; margin-bottom: 32px;">输入授权码查询授权信息</p>
        </div>
    </div>

    <div class="container" style="padding-bottom: 60px;">
        <div class="card" style="max-width: 640px; margin: 0 auto;">
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

        <?php if (isset($result)): ?>
        <div class="card" style="max-width: 640px; margin: 24px auto 0;">
            <h3 style="font-size: 18px; color: #1a1a2e; margin-bottom: 16px;">查询结果</h3>
            <?php if ($result): ?>
            <div style="display: grid; gap: 12px;">
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                    <span style="color: #687690;">授权码</span>
                    <span style="font-weight: 500; font-family: monospace; font-size: 13px;"><?= htmlspecialchars($result['license_key'] ?? '-') ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                    <span style="color: #687690;">产品名称</span>
                    <span style="font-weight: 500;"><?= htmlspecialchars($result['product_name'] ?? '-') ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                    <span style="color: #687690;">授权状态</span>
                    <?php $status = $result['status'] ?? 0; ?>
                    <span class="badge <?= $status == 1 ? 'badge-success' : 'badge-danger' ?>"><?= $status == 1 ? '有效' : '已失效' ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                    <span style="color: #687690;">到期时间</span>
                    <span style="font-weight: 500;"><?= htmlspecialchars($result['expires_at'] ?? '永久') ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                    <span style="color: #687690;">绑定用户</span>
                    <span style="font-weight: 500;"><?= htmlspecialchars($result['username'] ?? '未知') ?></span>
                </div>
            </div>
            <?php else: ?>
            <div class="empty-state" style="text-align: center; padding: 40px 20px;">
                <svg width="48" height="48" style="color: #c0c8d8; margin-bottom: 12px;"><use href="#i-x"/></svg>
                <p style="color: #687690; font-size: 14px;"><?= htmlspecialchars($error ?? '未找到该授权码对应的信息') ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <footer class="site-footer">
        <div class="container">
            <p style="margin-bottom: 8px;">© <?= date('Y') ?> <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?> All Rights Reserved.</p>
            <?php if (!empty($siteSettings['icp'])): ?>
            <p style="color: #687690; font-size: 12px;"><?= htmlspecialchars($siteSettings['icp']) ?></p>
            <?php endif; ?>
        </div>
    </footer>

    <!-- Announcement Popup Modal -->
    <?php if (!empty($siteSettings['announcement'])): ?>
    <div class="announcement-modal" id="announcementModal">
        <div class="am-overlay"></div>
        <div class="am-dialog">
            <div class="am-header">
                <h3><svg width="18" height="18" style="vertical-align: middle; margin-right: 6px;"><use href="#i-bell"/></svg>网站公告</h3>
                <button class="am-close" onclick="hideAnnouncement()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
            </div>
            <div class="am-body">
                <p style="color: #444; line-height: 1.8; white-space: pre-wrap;"><?= htmlspecialchars($siteSettings['announcement']) ?></p>
            </div>
            <div class="am-footer">
                <button class="btn btn-primary btn-sm" onclick="hideAnnouncement()">我知道了</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Hamburger menu
        document.getElementById('hamburgerBtn').addEventListener('click', function() {
            document.getElementById('mobileNav').classList.toggle('show');
        });

        // Announcement popup
        function showAnnouncementPopup() {
            var modal = document.getElementById('announcementModal');
            if (modal) {
                modal.classList.add('show');
            }
        }
        function hideAnnouncement() {
            var modal = document.getElementById('announcementModal');
            if (modal) {
                modal.classList.remove('show');
            }
        }
        // Close modal on overlay click
        document.addEventListener('click', function(e) {
            var modal = document.getElementById('announcementModal');
            if (modal && e.target.classList.contains('am-overlay')) {
                hideAnnouncement();
            }
        });
        // Close modal on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideAnnouncement();
            }
        });
    </script>
</body>
</html>