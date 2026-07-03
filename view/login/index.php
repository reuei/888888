<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 鲸商城 Pro</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body class="login-body">
    <div class="login-shell">
        <!-- 左侧品牌/插画区 -->
        <aside class="login-aside">
            <div class="brand">
                <span class="logo-mark"><svg class="icon" aria-hidden="true"><use href="#icon-zap"></use></svg></span>
                鲸商城 Pro
            </div>

            <div class="illust">
                <!-- 3D 风格 SVG 装饰插画 -->
                <svg viewBox="0 0 320 240" xmlns="http://www.w3.org/2000/svg" fill="none">
                    <defs>
                        <linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#fff" stop-opacity="0.95"/>
                            <stop offset="1" stop-color="#BFDBFE" stop-opacity="0.85"/>
                        </linearGradient>
                        <linearGradient id="g2" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0" stop-color="#fff"/>
                            <stop offset="1" stop-color="#DBEAFE"/>
                        </linearGradient>
                        <filter id="sh" x="-20%" y="-20%" width="140%" height="140%">
                            <feDropShadow dx="0" dy="10" stdDeviation="12" flood-color="#0F172A" flood-opacity="0.18"/>
                        </filter>
                    </defs>
                    <!-- 底座 -->
                    <ellipse cx="160" cy="210" rx="120" ry="14" fill="#0F172A" opacity="0.18"/>
                    <!-- 主卡片 -->
                    <g filter="url(#sh)">
                        <rect x="60" y="60" width="200" height="130" rx="18" fill="url(#g1)"/>
                        <rect x="60" y="60" width="200" height="38" rx="18" fill="#fff" opacity="0.5"/>
                        <circle cx="84" cy="79" r="5" fill="#EF4444"/>
                        <circle cx="100" cy="79" r="5" fill="#F59E0B"/>
                        <circle cx="116" cy="79" r="5" fill="#10B981"/>
                    </g>
                    <!-- 卡片内容线 -->
                    <rect x="82" y="116" width="100" height="10" rx="5" fill="#fff"/>
                    <rect x="82" y="136" width="156" height="8" rx="4" fill="#fff" opacity="0.7"/>
                    <rect x="82" y="152" width="120" height="8" rx="4" fill="#fff" opacity="0.6"/>
                    <!-- 浮动徽章：闪电（自动发货） -->
                    <g filter="url(#sh)">
                        <circle cx="244" cy="56" r="26" fill="#fff"/>
                        <path d="M244 44 l-8 14 h6 l-2 12 10 -16 h-6 z" fill="#F59E0B"/>
                    </g>
                    <!-- 浮动徽章：盾牌（安全） -->
                    <g filter="url(#sh)">
                        <circle cx="76" cy="170" r="22" fill="#10B981"/>
                        <path d="M76 158 l8 3 v8 c0 6 -4 9 -8 11 c-4 -2 -8 -5 -8 -11 v-8 z" fill="#fff"/>
                    </g>
                    <!-- 浮动徽章：勾 -->
                    <g filter="url(#sh)">
                        <circle cx="200" cy="186" r="20" fill="#fff"/>
                        <path d="M191 186 l6 6 12 -12" stroke="#2563EB" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                </svg>
            </div>

            <div class="tagline">
                <h2>安全、便捷的<br>卡密自动发货平台</h2>
                <p>汇聚海量优质商品，7×24 小时自动发货，资金托管交易无忧。</p>
            </div>
        </aside>

        <!-- 右侧登录表单 -->
        <main class="login-main">
            <div class="login-title">
                <h1>欢迎回来</h1>
                <p id="subTitle">请登录总站管理后台</p>
            </div>

            <div class="tabs">
                <div class="tab <?php echo $type === 'admin' ? 'active' : ''; ?>" data-type="admin">
                    <svg class="icon icon-sm" aria-hidden="true" style="vertical-align:-3px;margin-right:4px"><use href="#icon-admin"></use></svg>
                    总站后台
                </div>
                <div class="tab <?php echo $type === 'merchant' ? 'active' : ''; ?>" data-type="merchant">
                    <svg class="icon icon-sm" aria-hidden="true" style="vertical-align:-3px;margin-right:4px"><use href="#icon-merchant"></use></svg>
                    商户后台
                </div>
            </div>

            <div class="alert" id="alertBox"></div>

            <form id="loginForm" data-form-loading>
                <input type="hidden" name="type" id="loginType" value="<?php echo h($type); ?>">
                <div class="form-group">
                    <label>账号</label>
                    <div class="input-wrap">
                        <span class="input-icon"><svg class="icon icon-sm" aria-hidden="true"><use href="#icon-user"></use></svg></span>
                        <input type="text" name="username" placeholder="请输入账号" required autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <div class="input-wrap">
                        <span class="input-icon"><svg class="icon icon-sm" aria-hidden="true"><use href="#icon-lock"></use></svg></span>
                        <input type="password" name="password" id="pwdInput" placeholder="请输入密码" required>
                    </div>
                </div>
                <?php if (captcha_required('login')): ?>
                <div class="form-group">
                    <label>验证码</label>
                    <div class="captcha-row">
                        <div class="input-wrap" style="flex:1">
                            <span class="input-icon"><svg class="icon icon-sm" aria-hidden="true"><use href="#icon-shield"></use></svg></span>
                            <input type="text" name="captcha" placeholder="请输入验证码" maxlength="4" required>
                        </div>
                        <img src="<?php echo url('login/captcha'); ?>" alt="验证码" id="captchaImg" title="点击刷新">
                    </div>
                </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-block btn-lg" id="submitBtn" style="margin-top: 8px;">
                    <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
                    登录
                </button>
            </form>

            <a href="<?php echo url('/'); ?>" class="back-link">
                <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-chevron-left"></use></svg>
                返回首页
            </a>
        </main>
    </div>

    <script src="/static/js/app.js"></script>
    <script>
    (function() {
        var tabs = document.querySelectorAll('.tab');
        var typeInput = document.getElementById('loginType');
        var subTitle = document.getElementById('subTitle');
        var alertBox = document.getElementById('alertBox');
        var form = document.getElementById('loginForm');
        var submitBtn = document.getElementById('submitBtn');

        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                tabs.forEach(function(t) { t.classList.remove('active'); });
                tab.classList.add('active');
                var type = tab.dataset.type;
                typeInput.value = type;
                subTitle.textContent = type === 'merchant' ? '请登录商户管理后台' : '请登录总站管理后台';
                hideAlert();
            });
        });

        var captchaImg = document.getElementById('captchaImg');
        if (captchaImg) {
            captchaImg.addEventListener('click', function() {
                captchaImg.src = '<?php echo url('login/captcha'); ?>?' + Date.now();
            });
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            hideAlert();
            var restore = btnLoading(submitBtn);

            var formData = new FormData(form);
            try {
                var data = await App.ajax('<?php echo url('login/doLogin'); ?>', { method: 'POST', body: formData });
                if (data && data.code === 0) {
                    if (window.Toast) { Toast.success(data.msg || '登录成功'); }
                    showAlert(data.msg || '登录成功', 'success');
                    setTimeout(function() { location.href = data.data.redirect; }, 600);
                    return;
                }
                showAlert((data && data.msg) || '登录失败', 'error');
            } catch (err) {
                showAlert('网络请求失败', 'error');
            } finally {
                // 稍后恢复按钮，避免重复提交
                setTimeout(function() { restore(); }, 300);
            }
        });

        function showAlert(msg, type) {
            alertBox.textContent = msg;
            alertBox.className = 'alert alert-' + type + ' show';
        }
        function hideAlert() { alertBox.className = 'alert'; }
    })();
    </script>
</body>
</html>
