<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 玄武发卡</title>
    <link rel="stylesheet" href="/static/css/app.css?v=1.0.5">
</head>
<body class="auth-body">
    <div class="auth-page">
        <div class="auth-left">
            <a href="/" class="brand">
                <span class="brand-mark"></span>
                <span class="brand-name">玄武发卡</span>
            </a>
            <h1 class="auth-title">欢迎回来</h1>
            <p class="auth-sub">登录后管理您的订单、消息与账户</p>
            <ul class="auth-feature">
                <li>· 自研轻量框架 v1.0.5</li>
                <li>· 实时输入验证</li>
                <li>· 滑块人机验证</li>
                <li>· 授权版本 v1.1.1</li>
            </ul>
        </div>
        <div class="auth-right">
            <div class="auth-tabs">
                <a href="/login" class="auth-tab active">登录</a>
                <a href="/register" class="auth-tab">注册</a>
            </div>
            <form id="loginForm" class="form">
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" name="username" data-validate="username" data-min="3" data-max="20" placeholder="请输入用户名" required>
                    <p class="form-error"></p>
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" name="password" data-validate="password" data-min="6" placeholder="请输入密码" required>
                    <p class="form-error"></p>
                </div>
                <div class="form-group">
                    <label>人机验证</label>
                    <div class="slider-captcha" id="sliderCaptcha">
                        <div class="slider-track"></div>
                        <div class="slider-handle">
                            <span class="slider-arrow">→</span>
                        </div>
                        <span class="slider-tip">拖动滑块完成验证</span>
                        <span class="slider-success" style="display:none">验证通过 ✓</span>
                    </div>
                </div>
                <input type="hidden" name="slider_token" id="sliderToken">
                <input type="hidden" name="slider_x" id="sliderX">
                <button type="submit" class="btn btn-primary btn-block">登录</button>
                <p class="form-foot">还没有账号？<a href="/register">立即注册</a></p>
            </form>
        </div>
    </div>
    <script src="/static/js/app.js?v=1.0.5"></script>
</body>
</html>
