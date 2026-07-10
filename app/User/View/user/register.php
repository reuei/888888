<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册 - 玄武发卡</title>
    <link rel="stylesheet" href="/static/css/app.css?v=1.0.5">
</head>
<body class="auth-body">
    <div class="auth-page">
        <div class="auth-left">
            <a href="/" class="brand">
                <span class="brand-mark"></span>
                <span class="brand-name">玄武发卡</span>
            </a>
            <h1 class="auth-title">创建新账户</h1>
            <p class="auth-sub">加入玄武发卡，开启数字商品之旅</p>
        </div>
        <div class="auth-right">
            <div class="auth-tabs">
                <a href="/login" class="auth-tab">登录</a>
                <a href="/register" class="auth-tab active">注册</a>
            </div>
            <form id="registerForm" class="form">
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" name="username" data-validate="username" data-min="3" data-max="20" placeholder="3-20位字母/数字/下划线" required>
                    <p class="form-error"></p>
                </div>
                <div class="form-group">
                    <label>邮箱</label>
                    <input type="email" name="email" data-validate="email" placeholder="用于接收通知" required>
                    <p class="form-error"></p>
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" name="password" data-validate="password" data-min="6" placeholder="至少6位字符" required>
                    <p class="form-error"></p>
                </div>
                <div class="form-group">
                    <label>确认密码</label>
                    <input type="password" name="confirm_password" data-validate="confirm" placeholder="再次输入密码" required>
                    <p class="form-error"></p>
                </div>
                <div class="form-group">
                    <label>人机验证</label>
                    <div class="slider-captcha" id="sliderCaptcha">
                        <div class="slider-track"></div>
                        <div class="slider-handle"><span class="slider-arrow">→</span></div>
                        <span class="slider-tip">拖动滑块完成验证</span>
                        <span class="slider-success" style="display:none">验证通过 ✓</span>
                    </div>
                </div>
                <input type="hidden" name="slider_token" id="sliderToken">
                <input type="hidden" name="slider_x" id="sliderX">
                <button type="submit" class="btn btn-primary btn-block">注册</button>
                <p class="form-foot">已有账号？<a href="/login">立即登录</a></p>
            </form>
        </div>
    </div>
    <script src="/static/js/app.js?v=1.0.5"></script>
</body>
</html>
