<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文档中心 - QEEFG授权站</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="/" class="logo">
                <div class="logo-icon">Q</div>
                <div class="logo-text">QEEFG授权站</div>
            </a>
            <nav class="nav">
                <ul class="nav-links">
                    <li><a href="/">首页</a></li>
                    <li><a href="/license-query">授权查询</a></li>
                    <li><a href="/documents" class="active">文档中心</a></li>
                    <li><a href="/user/products">产品列表</a></li>
                </ul>
                <div class="nav-actions">
                    <?php if ($user): ?>
                        <a href="/user/dashboard" class="btn btn-secondary">用户中心</a>
                        <a href="/logout" class="btn btn-sm">退出</a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-secondary">登录</a>
                        <a href="/register" class="btn btn-primary">注册</a>
                    <?php endif; ?>
                    <button class="icon-btn theme-toggle" id="theme-toggle">☀️</button>
                    <button class="icon-btn lang-toggle" id="lang-toggle">CN</button>
                    <div class="burger-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="container" style="padding: 60px 20px;">
        <div class="page-title">
            <h1>文档中心</h1>
            <p>查找您需要的文档和帮助信息</p>
        </div>

        <div class="documents-list">
            <?php foreach ($documents as $doc): ?>
            <div class="document-card">
                <h4><?php echo $doc['title']; ?></h4>
                <p><?php echo $doc['description']; ?></p>
                <span style="display: inline-block; padding: 4px 12px; background: rgba(102, 126, 234, 0.1); color: #667eea; border-radius: 20px; font-size: 12px; margin-bottom: 16px;">
                    <?php echo $doc['category']; ?>
                </span>
                <a href="#" class="document-link">阅读文档 →</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2024 QEEFG授权站. 保留所有权利.</p>
            </div>
        </div>
    </footer>

    <script src="/static/js/main.js"></script>
</body>
</html>
