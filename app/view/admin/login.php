<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台 - <?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <svg style="display:none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <symbol id="i-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
            <symbol id="i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></symbol>
        </defs>
    </svg>

    <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f0f2f5;">
        <div class="card" style="max-width: 400px; width: 100%;">
            <h1 style="font-size: 22px; color: #1a1a2e; margin-bottom: 8px; text-align: center;">管理后台</h1>
            <p style="color: #687690; font-size: 14px; text-align: center; margin-bottom: 24px;"><?= htmlspecialchars($siteSettings['site_name'] ?? '熵云') ?></p>
            <?php if (!empty($error)): ?>
            <div style="background: #fff2f0; border: 1px solid #ffccc7; color: #ff4d4f; padding: 10px 14px; margin-bottom: 16px; font-size: 14px;">
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
                <button type="submit" class="btn btn-primary btn-block">登录</button>
            </form>
            <div style="text-align: center; margin-top: 16px; font-size: 14px;">
                <a href="/" style="color: #687690;">返回首页</a>
            </div>
        </div>
    </div>

    <div class="toast-center" id="toastContainer"></div>

    <script src="/static/js/main.js"></script>
</body>
</html>