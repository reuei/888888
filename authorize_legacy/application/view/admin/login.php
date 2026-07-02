<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员登录 - <?php echo h(site_config('site_name')); ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #F8FAFC;
            color: #1F2937;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh;
        }
        .login-box {
            width: 100%; max-width: 400px;
            background: #fff; border: 1px solid #E2E8F0;
            border-radius: 8px; padding: 32px;
        }
        .login-box h1 { font-size: 20px; text-align: center; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-size: 13px; color: #64748B; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 10px 12px;
            border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;
        }
        .btn {
            width: 100%; padding: 10px; background: #2563EB; color: #fff;
            border: none; border-radius: 6px; font-size: 14px; cursor: pointer;
        }
        .back { display: block; text-align: center; margin-top: 16px; font-size: 13px; color: #64748B; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1><?php echo h(site_config('site_name')); ?> · 后台</h1>
        <form id="loginForm">
            <div class="form-group">
                <label>管理员账号</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">登录</button>
        </form>
        <a href="<?php echo url('/'); ?>" class="back">返回前台</a>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('<?php echo url('admin/admin/doLogin'); ?>', {
            method: 'POST',
            body: new FormData(e.target)
        }).then(r => r.json()).then(res => {
            alert(res.msg);
            if (res.code === 0 && res.data.redirect) {
                location.href = res.data.redirect;
            }
        });
    });
    </script>
</body>
</html>
