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
            <symbol id="i-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></symbol>
            <symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></symbol>
            <symbol id="i-zap" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></symbol>
        </defs>
    </svg>

    <div class="auth-page">
        <div class="auth-visual">
            <div class="auth-visual-content">
                <h2>加入我们</h2>
                <p>创建您的<?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?>账号，开启专业的软件授权管理之旅。</p>
                <ul class="auth-features">
                    <li>
                        <svg width="18" height="18"><use href="#i-check"/></svg>
                        免费注册，永久有效
                    </li>
                    <li>
                        <svg width="18" height="18"><use href="#i-check"/></svg>
                        一站式授权管理
                    </li>
                    <li>
                        <svg width="18" height="18"><use href="#i-check"/></svg>
                        完善的 API 与 SDK 支持
                    </li>
                </ul>
            </div>
        </div>
        <div class="auth-form-wrap">
            <div class="auth-form">
                <div class="auth-form-header">
                    <div class="auth-form-logo"><?= htmlspecialchars(mb_substr($siteSettings['site_name'] ?? '熵云', 0, 1)) ?></div>
                    <h1 class="auth-form-title">创建账号</h1>
                    <p class="auth-form-subtitle">创建您的<?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?>账号</p>
                </div>

                <?php if (!empty($error)): ?>
                <div style="background: var(--danger-light); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--danger); padding: 10px 14px; margin-bottom: 16px; font-size: 14px; border-radius: var(--radius);">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="/user/doregister" data-ajax="true" id="registerForm">
                    <div class="form-group">
                        <label class="form-label" for="username">用户名</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="3-20个字符" required minlength="3" maxlength="20" data-validate="username">
                    </div>
                    <?php $requireEmail = ($siteSettings['require_email_register'] ?? '1') === '1'; ?>
                    <div class="form-group">
                        <label class="form-label" for="email">邮箱<?= $requireEmail ? '' : ' <span style="color: var(--text-muted); font-weight: 400;">(选填)</span>' ?></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="请输入邮箱地址" <?= $requireEmail ? 'required' : '' ?> data-validate="email">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">手机号 <span style="color: var(--text-muted); font-weight: 400;">(选填)</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="请输入手机号" data-validate="phone">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">密码</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="至少6位密码" required minlength="6" data-validate="password">
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
                    <button type="submit" class="btn btn-primary btn-block">注册</button>
                </form>

                <div class="auth-form-actions">
                    <a href="/user/login">已有账号？立即登录</a>
                    <a href="/">返回首页</a>
                </div>

                <div class="auth-footer">
                    注册即表示您同意 <a href="/terms" style="color: var(--primary);">服务条款</a> 和 <a href="/privacy" style="color: var(--primary);">隐私政策</a>
                </div>
            </div>
        </div>
    </div>

    <script src="/static/js/main.js"></script>
</body>
</html>