<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? '首页') ?> - 玄武发卡</title>
    <link rel="stylesheet" href="/static/css/app.css?v=1.0.5">
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a href="/" class="brand">
                <span class="brand-mark"></span>
                <span class="brand-name">玄武发卡</span>
                <span class="brand-version">v1.0.5</span>
            </a>
            <nav class="topbar-nav">
                <a href="/" class="nav-link">首页</a>
                <a href="/category/1" class="nav-link">游戏点卡</a>
                <a href="/category/2" class="nav-link">视频会员</a>
                <a href="/category/3" class="nav-link">音乐会员</a>
                <a href="/category/4" class="nav-link">软件激活</a>
                <a href="/notice" class="nav-link">公告</a>
            </nav>
            <div class="topbar-right">
                <?php if (\Framework\Session::has('user_id')): ?>
                    <a href="/user" class="btn btn-ghost btn-sm">用户中心</a>
                    <a href="/logout" class="btn btn-line btn-sm">退出</a>
                <?php else: ?>
                    <a href="/login" class="btn btn-line btn-sm">登录</a>
                    <a href="/register" class="btn btn-primary btn-sm">注册</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="page-main">
        <?= $__content__ ?? '' ?>
    </main>
    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-col">
                <div class="footer-brand">玄武发卡</div>
                <p class="footer-desc">自研框架 v1.0.5 · 专业的数字商品交易平台</p>
            </div>
            <div class="footer-col">
                <h4>用户</h4>
                <a href="/login">登录</a>
                <a href="/register">注册</a>
                <a href="/user">用户中心</a>
            </div>
            <div class="footer-col">
                <h4>商家</h4>
                <a href="/admin/login">后台入口</a>
                <a href="/license">授权站</a>
            </div>
            <div class="footer-col">
                <h4>关于</h4>
                <a href="/notice">公告</a>
                <p>授权版本 v1.1.1</p>
            </div>
        </div>
        <div class="footer-base">© 2026 玄武发卡 · Powered by 自研框架</div>
    </footer>
    <script src="/static/js/app.js?v=1.0.5"></script>
</body>
</html>
