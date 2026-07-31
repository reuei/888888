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
                <h2 class="av-title">加入我们，开启<br/>高效授权管理</h2>
                <p class="av-desc">免费注册，永久有效 · 一站式授权管理 · 完善 API 与 SDK 支持</p>
                <ul class="av-feature-list">
                    <li>
                        <svg width="18" height="18"><use href="#i-check-circle"/></svg>
                        免费注册，永久有效
                    </li>
                    <li>
                        <svg width="18" height="18"><use href="#i-check-circle"/></svg>
                        一站式授权管理服务
                    </li>
                    <li>
                        <svg width="18" height="18"><use href="#i-check-circle"/></svg>
                        完善的 API 与 SDK 支持
                    </li>
                </ul>
                <div class="av-copyright">© 2026 熵云. All rights reserved.</div>
            </div>
        </div>
        <div class="auth-card-wrap">
            <div class="auth-card">
                <div class="auth-header-card">
                    <h3 class="auth-title">用户注册</h3>
                    <p class="auth-subtitle">创建您的账户后即可开始使用用户中心服务</p>
                    <div class="auth-header-login">
                        已有账户？<a href="/user/login">立即登录</a>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                <div class="auth-error-banner">
                    <svg width="18" height="18"><use href="#i-x-circle"/></svg>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
                <?php endif; ?>

                <div class="auth-tabs">
                    <button type="button" class="auth-tab active" data-tab="email">邮箱注册</button>
                    <button type="button" class="auth-tab" data-tab="phone">手机注册</button>
                </div>

                <form id="registerForm" action="/user/doregister" method="POST" data-ajax="true">
                    <div class="auth-tab-panel" data-panel="email">
                        <div class="auth-field">
                            <span class="auth-field-icon">
                                <svg width="18" height="18"><use href="#i-user"/></svg>
                            </span>
                            <input type="text" class="form-control auth-input" name="username" id="username" placeholder="用户名" required minlength="3" maxlength="20" data-validate="username">
                        </div>
                        <div class="auth-field">
                            <span class="auth-field-icon">
                                <svg width="18" height="18"><use href="#i-mail"/></svg>
                            </span>
                            <input type="email" class="form-control auth-input" name="email" id="email" placeholder="邮箱地址" <?= ($requireEmail ?? '1') === '0' ? '' : 'required' ?> data-validate="email">
                        </div>
                        <div class="auth-field">
                            <span class="auth-field-icon">
                                <svg width="18" height="18"><use href="#i-lock"/></svg>
                            </span>
                            <input type="password" class="form-control auth-input" name="password" id="email_password" placeholder="密码" required minlength="6" data-validate="password">
                            <button type="button" class="toggle-eye" aria-label="显示密码" data-target="email_password">
                                <svg class="eye-on" width="18" height="18"><use href="#i-eye"/></svg>
                                <svg class="eye-off" width="18" height="18" style="display:none;"><use href="#i-eye-off"/></svg>
                            </button>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-bar strength-bar-1"></div>
                            <div class="strength-bar strength-bar-2"></div>
                            <div class="strength-bar strength-bar-3"></div>
                        </div>
                        <div class="auth-field">
                            <span class="auth-field-icon">
                                <svg width="18" height="18"><use href="#i-lock"/></svg>
                            </span>
                            <input type="password" class="form-control auth-input" name="confirm_password" id="email_confirm_password" placeholder="确认密码" required data-validate="confirm">
                            <button type="button" class="toggle-eye" aria-label="显示密码" data-target="email_confirm_password">
                                <svg class="eye-on" width="18" height="18"><use href="#i-eye"/></svg>
                                <svg class="eye-off" width="18" height="18" style="display:none;"><use href="#i-eye-off"/></svg>
                            </button>
                        </div>
                        <div class="captcha-row">
                            <div class="auth-field" style="flex:1;">
                                <span class="auth-field-icon">
                                    <svg width="18" height="18"><use href="#i-key"/></svg>
                                </span>
                                <input type="text" class="form-control auth-input" name="email_code" id="email_code" placeholder="验证码" required data-validate="captcha">
                            </div>
                            <button type="button" class="btn-send-code" id="sendEmailCode">获取验证码</button>
                        </div>
                        <button type="submit" class="auth-submit-btn">
                            <span class="btn-text">注册</span>
                            <svg class="btn-loader" width="18" height="18" style="display:none;"><use href="#i-loader"/></svg>
                        </button>
                        <div class="auth-footer-terms">
                            注册即表示您同意 <a href="/terms">服务条款</a> 和 <a href="/privacy">隐私政策</a>
                        </div>
                    </div>

                    <div class="auth-tab-panel" data-panel="phone" style="display:none;">
                        <div class="auth-field">
                            <span class="auth-field-icon">
                                <svg width="18" height="18"><use href="#i-user"/></svg>
                            </span>
                            <input type="text" class="form-control auth-input" name="username_phone" id="username_phone" placeholder="用户名" required minlength="3" maxlength="20" data-validate="username">
                        </div>
                        <div class="auth-field">
                            <span class="auth-field-icon">
                                <svg width="18" height="18"><use href="#i-phone"/></svg>
                            </span>
                            <input type="text" class="form-control auth-input" name="phone" id="phone" placeholder="手机号" required data-validate="phone">
                        </div>
                        <div class="auth-field">
                            <span class="auth-field-icon">
                                <svg width="18" height="18"><use href="#i-lock"/></svg>
                            </span>
                            <input type="password" class="form-control auth-input" name="password_phone" id="phone_password" placeholder="密码" required minlength="6" data-validate="password">
                            <button type="button" class="toggle-eye" aria-label="显示密码" data-target="phone_password">
                                <svg class="eye-on" width="18" height="18"><use href="#i-eye"/></svg>
                                <svg class="eye-off" width="18" height="18" style="display:none;"><use href="#i-eye-off"/></svg>
                            </button>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-bar strength-bar-1"></div>
                            <div class="strength-bar strength-bar-2"></div>
                            <div class="strength-bar strength-bar-3"></div>
                        </div>
                        <div class="auth-field">
                            <span class="auth-field-icon">
                                <svg width="18" height="18"><use href="#i-lock"/></svg>
                            </span>
                            <input type="password" class="form-control auth-input" name="confirm_password_phone" id="phone_confirm_password" placeholder="确认密码" required data-validate="confirm">
                            <button type="button" class="toggle-eye" aria-label="显示密码" data-target="phone_confirm_password">
                                <svg class="eye-on" width="18" height="18"><use href="#i-eye"/></svg>
                                <svg class="eye-off" width="18" height="18" style="display:none;"><use href="#i-eye-off"/></svg>
                            </button>
                        </div>
                        <div class="captcha-row">
                            <div class="auth-field" style="flex:1;">
                                <span class="auth-field-icon">
                                    <svg width="18" height="18"><use href="#i-key"/></svg>
                                </span>
                                <input type="text" class="form-control auth-input" name="phone_code" id="phone_code" placeholder="验证码" required data-validate="captcha">
                            </div>
                            <button type="button" class="btn-send-code" id="sendPhoneCode">获取短信验证码</button>
                        </div>
                        <button type="submit" class="auth-submit-btn">
                            <span class="btn-text">注册</span>
                            <svg class="btn-loader" width="18" height="18" style="display:none;"><use href="#i-loader"/></svg>
                        </button>
                        <div class="auth-footer-terms">
                            注册即表示您同意 <a href="/terms">服务条款</a> 和 <a href="/privacy">隐私政策</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/static/js/main.js"></script>
    <script>
    (function() {
        var activeTab = 'email';

        document.querySelectorAll('.auth-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                var tabName = this.getAttribute('data-tab');
                if (tabName === activeTab) return;
                activeTab = tabName;

                document.querySelectorAll('.auth-tab').forEach(function(t) {
                    t.classList.remove('active');
                });
                this.classList.add('active');

                document.querySelectorAll('.auth-tab-panel').forEach(function(panel) {
                    panel.style.display = 'none';
                });
                var activePanel = document.querySelector('.auth-tab-panel[data-panel="' + tabName + '"]');
                if (activePanel) {
                    activePanel.style.display = 'block';
                }

                var form = document.getElementById('registerForm');
                if (tabName === 'email') {
                    form.setAttribute('action', '/user/doregister');
                } else {
                    form.setAttribute('action', '/user/doregister');
                }
            });
        });

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

        function calcStrength(pwd) {
            var score = 0;
            if (!pwd) return 0;
            if (pwd.length >= 6) score++;
            if (pwd.length >= 10) score++;
            if (/[A-Z]/.test(pwd)) score++;
            if (/[0-9]/.test(pwd)) score++;
            if (/[^A-Za-z0-9]/.test(pwd)) score++;
            if (score <= 1) return 1;
            if (score <= 3) return 2;
            return 3;
        }

        function updateStrengthMeter(panelSelector, inputId) {
            var panel = document.querySelector(panelSelector);
            if (!panel) return;
            var meter = panel.querySelector('.strength-meter');
            if (!meter) return;
            var bars = meter.querySelectorAll('.strength-bar');
            var input = document.getElementById(inputId);
            if (!input) return;
            input.addEventListener('input', function() {
                var strength = calcStrength(this.value);
                bars.forEach(function(bar, idx) {
                    bar.classList.remove('active', 'weak', 'medium', 'strong');
                    if (idx < strength) {
                        bar.classList.add('active');
                        if (strength === 1) bar.classList.add('weak');
                        else if (strength === 2) bar.classList.add('medium');
                        else bar.classList.add('strong');
                    }
                });
            });
        }

        updateStrengthMeter('.auth-tab-panel[data-panel="email"]', 'email_password');
        updateStrengthMeter('.auth-tab-panel[data-panel="phone"]', 'phone_password');

        function startCountdown(btn, seconds) {
            var originalText = btn.getAttribute('data-original-text') || btn.textContent;
            btn.setAttribute('data-original-text', originalText);
            btn.disabled = true;
            btn.classList.add('counting');
            var remaining = seconds;
            btn.textContent = remaining + 's 后重新获取';
            var timer = setInterval(function() {
                remaining--;
                if (remaining <= 0) {
                    clearInterval(timer);
                    btn.disabled = false;
                    btn.classList.remove('counting');
                    btn.textContent = originalText;
                } else {
                    btn.textContent = remaining + 's 后重新获取';
                }
            }, 1000);
        }

        var sendEmailBtn = document.getElementById('sendEmailCode');
        if (sendEmailBtn) {
            sendEmailBtn.addEventListener('click', function() {
                var email = document.getElementById('email').value;
                if (!email) {
                    if (typeof Toast !== 'undefined') Toast.show({type:'warning', message:'请先输入邮箱地址'});
                    return;
                }
                startCountdown(this, 60);
            });
        }

        var sendPhoneBtn = document.getElementById('sendPhoneCode');
        if (sendPhoneBtn) {
            sendPhoneBtn.addEventListener('click', function() {
                var phone = document.getElementById('phone').value;
                if (!phone) {
                    if (typeof Toast !== 'undefined') Toast.show({type:'warning', message:'请先输入手机号'});
                    return;
                }
                startCountdown(this, 60);
            });
        }

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
