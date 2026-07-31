<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户中心登录 - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
            <symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></symbol>
            <symbol id="i-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></symbol>
            <symbol id="i-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></symbol>
            <symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></symbol>
            <symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></symbol>
            <symbol id="i-zap" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></symbol>
            <symbol id="i-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></symbol>
            <symbol id="i-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></symbol>
            <symbol id="i-home" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></symbol>
            <symbol id="i-arrow-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></symbol>
            <symbol id="i-loader" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></symbol>
            <symbol id="i-check-circle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></symbol>
            <symbol id="i-x-circle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></symbol>
        </defs>
    </svg>

    <header class="auth-header">
        <div class="auth-header-inner">
            <div class="auth-header-brand">
                <div class="ah-logo"><?= htmlspecialchars(mb_substr($siteSettings['site_name'] ?? '熵云', 0, 1)) ?></div>
                <div class="ah-brand-text">
                    <span class="ah-site-name"><?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></span>
                    <span class="ah-site-subtitle">企业级软件定制与私有化交付服务商</span>
                </div>
            </div>
            <a href="/" class="auth-header-back">
                <svg width="16" height="16"><use href="#i-home"/></svg>
                <span>返回首页</span>
            </a>
        </div>
    </header>

    <div class="auth-page">
        <div class="auth-visual">
            <div class="av-decoration">
                <div class="av-deco-circle av-deco-circle-1"></div>
                <div class="av-deco-circle av-deco-circle-2"></div>
                <div class="av-deco-grid"></div>
                <div class="av-deco-glow"></div>
            </div>
            <div class="auth-visual-content">
                <div class="av-logo-circle">熵</div>
                <h2 class="av-title">企业级软件定制与<br/>私有化交付服务商</h2>
                <p class="av-desc">安全授权验证 · 稳定设备绑定 · 高效管理服务</p>
                <ul class="av-feature-list">
                    <li>
                        <svg width="18" height="18"><use href="#i-check-circle"/></svg>
                        安全的授权验证机制
                    </li>
                    <li>
                        <svg width="18" height="18"><use href="#i-check-circle"/></svg>
                        实时授权查询与管理
                    </li>
                    <li>
                        <svg width="18" height="18"><use href="#i-check-circle"/></svg>
                        7×24 小时技术支持
                    </li>
                </ul>
                <div class="av-copyright">© 2026 熵云. All rights reserved.</div>
            </div>
        </div>
        <div class="auth-card-wrap">
            <div class="auth-card">
                <div class="auth-header-card">
                    <h3 class="auth-title">用户中心登录</h3>
                    <p class="auth-subtitle">使用您的账号继续访问工作台与服务记录</p>
                </div>

                <?php if (!empty($error)): ?>
                <div class="auth-error-banner">
                    <svg width="18" height="18"><use href="#i-x-circle"/></svg>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
                <?php endif; ?>

                <form id="loginForm" method="POST" action="/user/dologin" data-ajax="true">
                    <div class="auth-field">
                        <span class="auth-field-icon">
                            <svg width="18" height="18"><use href="#i-user"/></svg>
                        </span>
                        <input type="text" class="form-control auth-input" name="username" id="username" placeholder="请输入用户名" required data-validate="login">
                    </div>
                    <div class="auth-field">
                        <span class="auth-field-icon">
                            <svg width="18" height="18"><use href="#i-lock"/></svg>
                        </span>
                        <input type="password" class="form-control auth-input" name="password" id="password" placeholder="请输入密码" required data-validate="password" minlength="6">
                        <button type="button" class="toggle-eye" aria-label="显示密码" data-target="password">
                            <svg class="eye-on" width="18" height="18"><use href="#i-eye"/></svg>
                            <svg class="eye-off" width="18" height="18" style="display:none;"><use href="#i-eye-off"/></svg>
                        </button>
                    </div>
                    <?php if (!empty($captchaUrl)): ?>
                    <div class="captcha-row">
                        <div class="auth-field" style="flex:1;">
                            <span class="auth-field-icon">
                                <svg width="18" height="18"><use href="#i-key"/></svg>
                            </span>
                            <input type="text" class="form-control auth-input" name="captcha" id="captcha" placeholder="请输入验证码" required data-validate="captcha">
                        </div>
                        <img class="captcha-img" src="<?= htmlspecialchars($captchaUrl) ?>" alt="验证码" title="点击刷新" onclick="this.src='<?= htmlspecialchars($captchaUrl) ?>&t='+new Date().getTime()">
                    </div>
                    <?php endif; ?>
                    <div class="auth-remember-row">
                        <label class="auth-remember">
                            <input type="checkbox" class="checkbox" id="remember" name="remember">
                            <span>记住密码</span>
                        </label>
                        <a href="/user/forgot" class="auth-link-forgot">忘记密码</a>
                    </div>
                    <button type="submit" class="auth-submit-btn">
                        <span class="btn-text">登录</span>
                        <svg class="btn-loader" width="18" height="18" style="display:none;"><use href="#i-loader"/></svg>
                    </button>
                    <a href="/user/register" class="auth-register-btn" type="button">注册账号</a>
                </form>

                <div class="auth-footer-text">
                    还没有账号？<a href="/user/register">立即注册</a>
                </div>
            </div>
        </div>
    </div>

    <script src="/static/js/main.js"></script>
    <script>
    (function() {
        document.querySelectorAll('.toggle-eye').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var targetId = this.getAttribute('data-target');
                var input = document.getElementById(targetId);
                if (!input) return;
                var eyeOn = this.querySelector('.eye-on');
                var eyeOff = this.querySelector('.eye-off');
                if (input.type === 'password') {
                    input.type = 'text';
                    eyeOn.style.display = 'none';
                    eyeOff.style.display = 'inline-block';
                } else {
                    input.type = 'password';
                    eyeOn.style.display = 'inline-block';
                    eyeOff.style.display = 'none';
                }
            });
        });

        <?php if (!empty($error)): ?>
        if (typeof Toast !== 'undefined') {
            Toast.show({
                type: 'error',
                message: <?= json_encode($error) ?>
            });
        }
        <?php endif; ?>
    })();
    </script>
</body>
</html>
