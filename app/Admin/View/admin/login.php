<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - 玄武后台</title>
    <link rel="stylesheet" href="/static/css/app.css?v=1.0.5">
</head>
<body class="admin-auth-body">
    <div class="admin-auth-page">
        <div class="admin-auth-card">
            <div class="admin-auth-head">
                <span class="brand-mark"></span>
                <h1>玄武后台</h1>
                <p>v1.0.5 · 授权站 v1.1.1</p>
            </div>
            <form id="loginForm" class="form">
                <div class="form-group">
                    <label>账号</label>
                    <input type="text" name="username" placeholder="请输入管理员账号" required>
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" name="password" placeholder="请输入密码" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">登录</button>
                <p class="form-foot">默认账号：admin / admin888</p>
            </form>
        </div>
    </div>
    <script src="/static/js/app.js?v=1.0.5"></script>
</body>
</html>
