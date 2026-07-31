<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台登录 - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body class="admin-body">
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></symbol>
        </defs>
    </svg>

    <div class="auth-page">
        <div class="auth-visual" style="background: linear-gradient(135deg, #4f8cff 0%, #7c3aed 100%);">
            <div class="auth-visual-content">
                <div style="font-size:56px;margin-bottom:20px;">☁</div>
                <h2>熵云管理后台</h2>
                <p>安全可靠的软件授权管理平台，为您的业务提供全方位的授权管理解决方案。</p>
                <ul class="auth-features">
                    <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>实时数据统计</li>
                    <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>多产品授权管理</li>
                    <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>订单与财务追踪</li>
                </ul>
            </div>
        </div>
        <div class="auth-form-wrap">
            <div class="auth-form">
                <div class="auth-form-header">
                    <div class="auth-form-logo">☁</div>
                    <h1 class="auth-form-title">管理后台登录</h1>
                    <p class="auth-form-subtitle"><?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></p>
                </div>
                <?php if (!empty($error)): ?>
                <div style="background: var(--danger-light);border:1px solid var(--danger);color:var(--danger);padding:10px 14px;margin-bottom:16px;border-radius:var(--radius);font-size:14px;">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                <form method="POST" action="/admin/dologin" data-ajax="true">
                    <div class="form-group">
                        <label class="form-label" for="username">管理员账号</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="请输入管理员账号" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">密码</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" style="padding:11px 18px;font-size:15px;">登录管理后台</button>
                </form>
                <div class="auth-footer">
                    <a href="/" style="color: var(--text-muted);text-decoration:none;">← 返回前台首页</a>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-center" id="toastContainer"></div>
    <script src="/static/js/main.js"></script>
</body>
</html>