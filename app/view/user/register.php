<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户注册 - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
            <symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></symbol>
        </defs>
    </svg>

    <header class="site-header">
        <div class="header-inner container">
            <a href="/" class="logo"><span class="logo-mark">&#9729;</span><span>熵云</span></a>
            <nav class="main-nav">
                <a href="/" class="nav-link">首页</a>
                <a href="/platform" class="nav-link">平台能力</a>
                <a href="/license-query" class="nav-link">授权查询</a>
                <a href="/documents" class="nav-link">文档中心</a>
                <a href="/announcement" class="nav-link">网站公告</a>
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

    <div style="padding-top: 100px; padding-bottom: 60px;">
        <div class="container" style="max-width: 440px;">
            <h1 style="font-size: 24px; color: #1a1a2e; margin-bottom: 8px; text-align: center;">用户注册</h1>
            <p style="color: #687690; font-size: 14px; text-align: center; margin-bottom: 24px;">创建您的熵云账号</p>

            <div class="card">
                <?php if (!empty($error)): ?>
                <div style="background: #fff2f0; border: 1px solid #ffccc7; color: #ff4d4f; padding: 10px 14px; margin-bottom: 16px; font-size: 14px;">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                <form method="POST" action="/user/doregister" data-ajax="true">
                    <div class="form-group">
                        <label class="form-label" for="username">用户名</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="3-20个字符" required minlength="3" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">邮箱</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="请输入邮箱地址" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">密码</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="至少6位密码" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">注册</button>
                </form>
                <div style="display: flex; justify-content: space-between; margin-top: 16px; font-size: 14px;">
                    <a href="/login" style="color: #4f8cff;">已有账号？立即登录</a>
                    <a href="/" style="color: #687690;">返回首页</a>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-center" id="toastContainer"></div>

    <footer class="site-footer">
        <div class="container" style="text-align: center;">
            <p style="margin-bottom: 8px;">&copy; <?= date('Y') ?> <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?> All Rights Reserved.</p>
            <?php if (!empty($siteSettings['icp'])): ?>
            <p style="color: #687690; font-size: 12px;"><?= htmlspecialchars($siteSettings['icp']) ?></p>
            <?php endif; ?>
        </div>
    </footer>

    <script src="/static/js/main.js"></script>
    <script>
        document.getElementById('hamburgerBtn').addEventListener('click', function() {
            document.getElementById('mobileNav').classList.toggle('show');
        });
    </script>
</body>
</html>