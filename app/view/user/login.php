<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户登录 - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></symbol>
            <symbol id="i-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></symbol>
            <symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></symbol>
            <symbol id="i-zap" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></symbol>
        </defs>
    </svg>

    <div class="auth-page">
        <div class="auth-visual">
            <div class="auth-visual-content">
                <h2>欢迎回来</h2>
                <p><?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?>授权服务平台，为您的软件产品提供安全可靠的授权管理解决方案。</p>
                <ul class="auth-features">
                    <li>
                        <svg width="18" height="18"><use href="#i-check"/></svg>
                        安全的授权验证机制
                    </li>
                    <li>
                        <svg width="18" height="18"><use href="#i-check"/></svg>
                        实时查询与管理
                    </li>
                    <li>
                        <svg width="18" height="18"><use href="#i-check"/></svg>
                        7×24 小时技术支持
                    </li>
                </ul>
            </div>
        </div>
        <div class="auth-form-wrap">
            <div class="auth-form">
                <div class="auth-form-header">
                    <div class="auth-form-logo"><?= htmlspecialchars(mb_substr($siteSettings['site_name'] ?? '熵云', 0, 1)) ?></div>
                    <h1 class="auth-form-title">欢迎登录</h1>
                    <p class="auth-form-subtitle">登录您的<?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?>账号</p>
                </div>

                <?php if (!empty($error)): ?>
                <div style="background: var(--danger-light); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--danger); padding: 10px 14px; margin-bottom: 16px; font-size: 14px; border-radius: var(--radius);">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="/user/dologin" data-ajax="true" id="loginForm">
                    <div class="form-group">
                        <label class="form-label" for="username">用户名 / 邮箱 / 手机号</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="请输入用户名、邮箱或手机号" required data-validate="login">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">密码</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码" required data-validate="password">
                    </div>
                    <?php if (!empty($captchaUrl)): ?>
                    <div class="form-group">
                        <label class="form-label" for="captcha">验证码</label>
                        <div class="captcha-row">
                            <input type="text" class="form-control" id="captcha" name="captcha" placeholder="请输入验证码" required style="flex: 1;">
                            <img src="<?= htmlspecialchars($captchaUrl) ?>" class="captcha-img" alt="验证码" title="点击刷新">
                        </div>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-block">登录</button>
                </form>

                <div class="auth-form-actions">
                    <a href="/user/register">注册账号</a>
                    <a href="/">返回首页</a>
                </div>

                <div class="auth-footer">
                    还没有账号？ <a href="/user/register" style="color: var(--primary);">立即注册</a>
                </div>
            </div>
        </div>
    </div>

    <script src="/static/js/main.js"></script>
</body>
</html>