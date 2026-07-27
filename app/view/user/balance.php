<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>余额管理 - QEEFG授权站</title>
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
                    <li><a href="/documents">文档中心</a></li>
                </ul>
                <div class="nav-actions">
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

    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="/user/dashboard">🏠 首页</a></li>
            <li><a href="/user/workplace">📋 工作台</a></li>
            <li><a href="/user/products">🛒 产品列表</a></li>
            <li><a href="/user/my-products">💼 我的产品</a></li>
            <li><a href="/user/balance" class="active">💰 余额管理</a></li>
            <li><a href="/user/settings">⚙️ 账户设置</a></li>
            <li><a href="/user/feedback">📝 意见反馈</a></li>
            <li><a href="/logout">🚪 退出登录</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-title">
            <h1>余额管理</h1>
            <p>管理您的账户余额</p>
        </div>

        <div class="balance-card">
            <h3>账户余额</h3>
            <div class="balance-amount">¥<?php echo $user['balance']; ?></div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>充值余额</h3>
            </div>
            
            <form id="recharge-form">
                <div class="form-group">
                    <label>充值金额</label>
                    <div style="display: flex; gap: 12px; margin-bottom: 16px;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('amount').value = '100'">¥100</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('amount').value = '200'">¥200</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('amount').value = '500'">¥500</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('amount').value = '1000'">¥1000</button>
                    </div>
                    <input type="number" id="amount" name="amount" placeholder="请输入充值金额" min="1" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">立即充值</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>充值记录</h3>
            </div>
            
            <div style="text-align: center; padding: 40px; color: #64748b;">
                暂无充值记录
            </div>
        </div>
    </main>

    <script src="/static/js/main.js"></script>
    <script>
        document.getElementById('recharge-form').addEventListener('submit', function(e) {
            e.preventDefault();
            showToast('功能开发中', 'info');
        });
    </script>
</body>
</html>
