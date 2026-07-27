<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QEEFG授权站 - 专业软件授权管理平台</title>
    <meta name="description" content="QEEFG授权站是一个专业的软件授权管理平台，提供软件授权、许可证管理、产品管理等服务。">
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
                    <li><a href="/" class="active">首页</a></li>
                    <li><a href="/license-query">授权查询</a></li>
                    <li><a href="/documents">文档中心</a></li>
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

    <section class="hero">
        <div class="container">
            <h1>专业软件授权管理平台</h1>
            <p>为您的软件提供安全、可靠、便捷的授权管理解决方案</p>
            <div class="hero-buttons">
                <a href="/user/products" class="btn btn-primary">浏览产品</a>
                <a href="/license-query" class="btn btn-secondary">查询授权</a>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🔑</div>
                    <h3>安全授权</h3>
                    <p>采用先进的加密算法，确保授权密钥的安全性和唯一性</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>实时管理</h3>
                    <p>实时监控授权状态，随时查看和管理您的授权信息</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>高效便捷</h3>
                    <p>一键生成授权密钥，快速完成软件授权流程</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔐</div>
                    <h3>硬件绑定</h3>
                    <p>支持硬件ID绑定，防止授权密钥被非法复制使用</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>跨平台</h3>
                    <p>支持Windows、Linux、Mac等多种操作系统</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>精准统计</h3>
                    <p>详细的授权统计数据，帮助您更好地了解用户使用情况</p>
                </div>
            </div>
        </div>
    </section>

    <section class="products-section">
        <div class="container">
            <div class="section-title">
                <h2>推荐产品</h2>
                <p>选择适合您的授权方案</p>
            </div>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if ($product['icon']): ?>
                            <img src="<?php echo $product['icon']; ?>" alt="<?php echo $product['name']; ?>">
                        <?php else: ?>
                            📦
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <div class="product-price">
                            <span>¥<?php echo $product['price']; ?></span>
                            <a href="/user/products" class="btn btn-primary btn-sm">查看详情</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>关于我们</h4>
                    <ul>
                        <li><a href="#">公司介绍</a></li>
                        <li><a href="#">联系我们</a></li>
                        <li><a href="#">加入我们</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>产品服务</h4>
                    <ul>
                        <li><a href="/user/products">产品列表</a></li>
                        <li><a href="/license-query">授权查询</a></li>
                        <li><a href="/documents">文档中心</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>帮助支持</h4>
                    <ul>
                        <li><a href="/documents">使用文档</a></li>
                        <li><a href="/user/feedback">意见反馈</a></li>
                        <li><a href="#">常见问题</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>联系方式</h4>
                    <ul>
                        <li>邮箱: support@qeefg.com</li>
                        <li>QQ: 123456789</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 QEEFG授权站. 保留所有权利.</p>
            </div>
        </div>
    </footer>

    <script src="/static/js/main.js"></script>
</body>
</html>
