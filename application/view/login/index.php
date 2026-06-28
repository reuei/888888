<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 鲸商城 Pro</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #F8FAFC;
            color: #1F2937;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-box {
            width: 420px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 32px;
        }
        .login-title {
            text-align: center;
            margin-bottom: 24px;
        }
        .login-title h1 { font-size: 20px; color: #2563EB; margin-bottom: 8px; }
        .login-title p { color: #64748B; font-size: 13px; }
        .tabs {
            display: flex;
            border-bottom: 1px solid #E2E8F0;
            margin-bottom: 24px;
        }
        .tab {
            flex: 1;
            text-align: center;
            padding: 12px;
            cursor: pointer;
            color: #64748B;
            border-bottom: 2px solid transparent;
            font-weight: 500;
        }
        .tab.active { color: #2563EB; border-bottom-color: #2563EB; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 500; }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            font-size: 14px;
        }
        input:focus { outline: none; border-color: #2563EB; }
        .captcha-row { display: flex; gap: 10px; align-items: center; }
        .captcha-row input { flex: 1; }
        .captcha-row img { height: 40px; border-radius: 6px; cursor: pointer; border: 1px solid #CBD5E1; }
        .btn {
            width: 100%;
            padding: 11px;
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn:disabled { background: #94A3B8; }
        .alert {
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 13px;
            display: none;
        }
        .alert-error { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        .alert-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: #64748B;
            text-decoration: none;
            font-size: 13px;
        }
        .back-link:hover { color: #2563EB; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-title">
            <h1>鲸商城 Pro</h1>
            <p id="subTitle">总站管理后台</p>
        </div>
        <div class="tabs">
            <div class="tab <?php echo $type === 'admin' ? 'active' : ''; ?>" data-type="admin">总站后台</div>
            <div class="tab <?php echo $type === 'merchant' ? 'active' : ''; ?>" data-type="merchant">商户后台</div>
        </div>
        <div class="alert" id="alertBox"></div>
        <form id="loginForm">
            <input type="hidden" name="type" id="loginType" value="<?php echo h($type); ?>">
            <div class="form-group">
                <label>账号</label>
                <input type="text" name="username" placeholder="请输入账号" required autofocus>
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" placeholder="请输入密码" required>
            </div>
            <?php if (captcha_required('login')): ?>
            <div class="form-group">
                <label>验证码</label>
                <div class="captcha-row">
                    <input type="text" name="captcha" placeholder="请输入验证码" maxlength="4" required>
                    <img src="<?php echo url('login/captcha'); ?>" alt="验证码" id="captchaImg" title="点击刷新">
                </div>
            </div>
            <?php endif; ?>
            <button type="submit" class="btn" id="submitBtn">登录</button>
        </form>
        <a href="<?php echo url('/'); ?>" class="back-link">返回首页</a>
    </div>

    <script>
        const tabs = document.querySelectorAll('.tab');
        const typeInput = document.getElementById('loginType');
        const subTitle = document.getElementById('subTitle');
        const alertBox = document.getElementById('alertBox');
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const type = tab.dataset.type;
                typeInput.value = type;
                subTitle.textContent = type === 'merchant' ? '商户管理后台' : '总站管理后台';
                alertBox.style.display = 'none';
            });
        });

        const captchaImg = document.getElementById('captchaImg');
        if (captchaImg) {
            captchaImg.addEventListener('click', () => {
                captchaImg.src = '<?php echo url('login/captcha'); ?>?' + Date.now();
            });
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            alertBox.style.display = 'none';
            submitBtn.disabled = true;
            submitBtn.textContent = '登录中...';

            const formData = new FormData(form);
            try {
                const res = await fetch('<?php echo url('login/doLogin'); ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.code === 0) {
                    showAlert(data.msg, 'success');
                    setTimeout(() => location.href = data.data.redirect, 500);
                } else {
                    showAlert(data.msg, 'error');
                }
            } catch (err) {
                showAlert('网络请求失败', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = '登录';
            }
        });

        function showAlert(msg, type) {
            alertBox.textContent = msg;
            alertBox.className = 'alert alert-' + type;
            alertBox.style.display = 'block';
        }
    </script>
</body>
</html>
