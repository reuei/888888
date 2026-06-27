<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? site_config('site_name')); ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #F8FAFC;
            color: #1F2937;
            font-size: 14px;
            line-height: 1.5;
        }
        a { text-decoration: none; color: inherit; }
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
        .topbar-left { display: flex; align-items: center; gap: 24px; }
        .logo { font-size: 18px; font-weight: 600; color: #2563EB; }
        .topbar-links a {
            margin-left: 20px;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
        }
        .topbar-links a:hover, .topbar-links a.active { color: #2563EB; }
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
            text-align: center;
        }
        .btn:hover { opacity: 0.9; }
        .btn-outline {
            background: #FFFFFF;
            color: #2563EB;
        }
        .btn-success { background: #10B981; border-color: #10B981; }
        .btn-warning { background: #F59E0B; border-color: #F59E0B; }
        .btn-danger { background: #EF4444; border-color: #EF4444; }
        .btn-lg { padding: 12px 24px; font-size: 16px; }
        .btn-block { display: block; width: 100%; }
        .footer {
            text-align: center;
            padding: 24px;
            color: #64748B;
            font-size: 12px;
            border-top: 1px solid #E2E8F0;
            margin-top: 40px;
        }
        .search-box {
            display: flex;
            max-width: 420px;
            width: 100%;
        }
        .search-box input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #CBD5E1;
            border-radius: 6px 0 0 6px;
            border-right: none;
            outline: none;
        }
        .search-box button {
            border-radius: 0 6px 6px 0;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .section-title a { font-size: 13px; font-weight: normal; color: #2563EB; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }
        .goods-card {
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .goods-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .goods-card a { display: block; }
        .goods-cover {
            height: 140px;
            background: #F1F5F9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94A3B8;
            font-size: 13px;
        }
        .goods-cover img { width: 100%; height: 100%; object-fit: cover; }
        .goods-info { padding: 12px; }
        .goods-name {
            font-size: 14px;
            color: #1F2937;
            margin-bottom: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .goods-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .goods-price { color: #EF4444; font-weight: 600; font-size: 16px; }
        .goods-sold { color: #94A3B8; font-size: 12px; }
        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            background: #F1F5F9;
            color: #475569;
        }
        .tag-blue { background: #EFF6FF; color: #2563EB; }
        .tag-green { background: #ECFDF5; color: #059669; }
        .tag-orange { background: #FFFBEB; color: #D97706; }
        .tag-red { background: #FEF2F2; color: #DC2626; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 13px; color: #64748B; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        .form-group input:focus, .form-group textarea:focus { border-color: #2563EB; }
        .empty-tip { text-align: center; padding: 60px 20px; color: #64748B; }
        @media (max-width: 768px) {
            .topbar-left { gap: 12px; }
            .topbar-links a { margin-left: 12px; }
            .search-box { display: none; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <a href="<?php echo url('/'); ?>" class="logo">
                <?php if (site_config('logo')): ?>
                <img src="<?php echo h(site_config('logo')); ?>" alt="<?php echo h(site_config('site_name')); ?>" style="height: 32px; vertical-align: middle;">
                <?php else: ?>
                <?php echo h(site_config('site_name')); ?>
                <?php endif; ?>
            </a>
            <form class="search-box" method="get" action="<?php echo url('index/category'); ?>">
                <input type="text" name="keyword" placeholder="搜索商品" value="<?php echo h($_GET['keyword'] ?? ''); ?>">
                <button type="submit" class="btn">搜索</button>
            </form>
        </div>
        <div class="topbar-links">
            <a href="<?php echo url('index/category'); ?>">购卡中心</a>
            <a href="<?php echo url('index/order'); ?>">查询订单</a>
            <a href="<?php echo url('login'); ?>?type=admin">总站后台</a>
            <a href="<?php echo url('login'); ?>?type=merchant">商户后台</a>
        </div>
    </div>
    <div class="container">
        <?php echo $__content__ ?? ''; ?>
    </div>
    <div class="footer">
        <?php echo h(site_config('copyright', '鲸商城 Pro v1.0.0')); ?> | <?php echo h(site_config('icp') ?: ''); ?><?php echo site_config('icp') ? ' | ' : ''; ?>客服：<?php echo h(site_config('contact') ?: '-'); ?>
    </div>
</body>
</html>
