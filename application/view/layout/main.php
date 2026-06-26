<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? '鲸商城 Pro'); ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #F8FAFC;
            color: #1F2937;
            font-size: 14px;
            line-height: 1.5;
        }
        .topbar {
            height: 56px;
            background: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }
        .logo { font-size: 18px; font-weight: 600; color: #2563EB; }
        .topbar-links a {
            margin-left: 20px;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
        }
        .topbar-links a:hover { color: #2563EB; }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 88px 24px 24px;
        }
        .card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #2563EB;
            color: #fff;
            border: 1px solid #2563EB;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-outline {
            background: #FFFFFF;
            color: #2563EB;
        }
        .footer {
            text-align: center;
            padding: 24px;
            color: #64748B;
            font-size: 12px;
            border-top: 1px solid #E2E8F0;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="logo">鲸商城 Pro</div>
        <div class="topbar-links">
            <a href="<?php echo url('login'); ?>?type=admin">总站后台</a>
            <a href="<?php echo url('login'); ?>?type=merchant">商户后台</a>
        </div>
    </div>
    <div class="container">
        <?php echo $__content__ ?? ''; ?>
    </div>
    <div class="footer">
        鲸商城 Pro v1.0.0 | 操作手册 | 客服入口
    </div>
</body>
</html>
