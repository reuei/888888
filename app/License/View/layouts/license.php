<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? '玄武授权站') ?> - v1.1.1</title>
    <link rel="stylesheet" href="/static/css/app.css?v=1.0.5">
</head>
<body class="license-body">
    <header class="license-header">
        <a href="/license" class="brand">
            <span class="brand-mark"></span>
            <span class="brand-name">玄武授权站</span>
            <span class="brand-version">v1.1.1</span>
        </a>
        <nav class="license-nav">
            <a href="/license" class="nav-link">首页</a>
            <a href="/license/admin" class="nav-link">后台</a>
            <a href="/" class="nav-link">主站</a>
        </nav>
    </header>
    <main class="license-main">
        <?= $__content__ ?? '' ?>
    </main>
    <footer class="license-footer">© 2026 玄武授权站 · 授权系统 v1.1.1 · 兼容 v1.0.5 客户端</footer>
    <script src="/static/js/app.js?v=1.0.5"></script>
</body>
</html>
