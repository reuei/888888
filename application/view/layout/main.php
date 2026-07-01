<!DOCTYPE html>
<html lang="<?php echo $currentLang === 'en' ? 'en' : 'zh-CN'; ?>">
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
            position: relative;
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
        .mobile-menu-btn {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            width: 32px;
            height: 32px;
            cursor: pointer;
            padding: 4px;
        }
        .mobile-menu-btn span {
            display: block;
            height: 2px;
            background: #475569;
            border-radius: 2px;
        }
        .subsite-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 32px;
            background: #1E3A8A;
            color: #fff;
            padding: 6px 24px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 101;
        }
        .subsite-bar .subsite-name { font-weight: 600; }
        .subsite-bar .subsite-desc { opacity: 0.8; }
        .subsite-bar .subsite-back {
            margin-left: auto;
            color: #BFDBFE;
        }
        body.has-subsite .topbar { top: 32px; }
        body.has-subsite .mobile-drawer { top: 88px; }
        body.has-subsite .container { padding-top: 120px; }
        .subsite-tag {
            display: inline-block;
            padding: 2px 8px;
            background: #EFF6FF;
            color: #2563EB;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .mobile-drawer {
            display: none;
            position: fixed;
            top: 56px;
            left: 0;
            right: 0;
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            z-index: 99;
            flex-direction: column;
            padding: 8px 16px;
        }
        .mobile-drawer.open { display: flex; }
        .mobile-drawer a {
            padding: 12px 8px;
            border-bottom: 1px solid #F1F5F9;
            color: #475569;
            font-size: 14px;
        }
        .mobile-drawer a:last-child { border-bottom: none; }
        .chat-float {
            position: fixed;
            right: 24px;
            bottom: 80px;
            width: 56px;
            height: 56px;
            background: #10B981;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            cursor: pointer;
            z-index: 90;
            font-size: 24px;
            text-decoration: none;
        }
        .chat-float:hover { background: #059669; }
        @media (max-width: 768px) {
            .topbar { padding: 0 16px; }
            .topbar-left { gap: 12px; }
            .topbar-links { display: none; }
            .search-box { display: none; }
            .mobile-menu-btn { display: flex; }
            .container { padding: 88px 16px 24px; }
            .grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
            .goods-cover { height: 110px; }
        }
    </style>
</head>
<body class="<?php echo !empty($currentSubsite) ? 'has-subsite' : ''; ?>">
    <?php if (!empty($currentSubsite)): ?>
    <div class="subsite-bar">
        <span class="subsite-name"><?php echo h($currentSubsite['name']); ?></span>
        <span class="subsite-desc">分站首页</span>
        <a href="<?php echo base_url(); ?>?clear_subsite=1" class="subsite-back">返回总站</a>
    </div>
    <?php endif; ?>
    <div class="topbar">
        <div class="topbar-left">
            <a href="<?php echo url('/'); ?>" class="logo">
                <?php if (site_config('logo')): ?>
                <img src="<?php echo h(site_config('logo')); ?>" alt="<?php echo h(site_config('site_name')); ?>" style="height: 32px; vertical-align: middle;">
                <?php else: ?>
                <?php echo h(site_config('site_name')); ?>
                <?php endif; ?>
            </a>
            <?php if (!empty($currentSubsite)): ?>
            <span class="subsite-tag"><?php echo h($currentSubsite['name']); ?></span>
            <?php endif; ?>
            <form class="search-box" method="get" action="<?php echo url('index/category'); ?>">
                <input type="text" name="keyword" placeholder="<?php echo h(lang('goods.search_placeholder')); ?>" value="<?php echo h($_GET['keyword'] ?? ''); ?>" autocomplete="off" id="globalSearchInput">
                <button type="submit" class="btn"><?php echo h(lang('nav.search')); ?></button>
                <div id="searchSuggest" style="display:none; position:absolute; top:40px; left:0; right:0; background:#fff; border:1px solid #E2E8F0; border-radius:0 0 6px 6px; box-shadow:0 4px 12px rgba(0,0,0,0.08); z-index:200; max-height:320px; overflow:auto;"></div>
            </form>
        </div>
        <div class="topbar-links">
            <a href="<?php echo url('index/category'); ?>"><?php echo h(lang('nav.category')); ?></a>
            <a href="<?php echo url('index/coupon'); ?>">领券中心</a>
            <a href="<?php echo url('index/order'); ?>"><?php echo h(lang('order.query')); ?></a>
            <a href="<?php echo url('index/user'); ?>"><?php echo h(lang('nav.user')); ?></a>
            <a href="<?php echo url('index/merchantJoin'); ?>">商户入驻</a>
            <a href="<?php echo url('login'); ?>?type=admin">总站后台</a>
            <a href="<?php echo url('login'); ?>?type=merchant">商户后台</a>
            <a href="<?php echo h(switch_lang_url($currentLang === 'zh-cn' ? 'en' : 'zh-cn')); ?>">🌐 <?php echo h(lang('lang.' . ($currentLang === 'zh-cn' ? 'en' : 'zh-cn'))); ?></a>
        </div>
        <div class="mobile-menu-btn" id="mobileMenuBtn">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="mobile-drawer" id="mobileDrawer">
        <a href="<?php echo url('index/category'); ?>"><?php echo h(lang('nav.category')); ?></a>
        <a href="<?php echo url('index/coupon'); ?>">领券中心</a>
        <a href="<?php echo url('index/order'); ?>"><?php echo h(lang('order.query')); ?></a>
        <a href="<?php echo url('index/user'); ?>"><?php echo h(lang('nav.user')); ?></a>
        <a href="<?php echo url('index/merchantJoin'); ?>">商户入驻</a>
        <a href="<?php echo url('login'); ?>?type=admin">总站后台</a>
        <a href="<?php echo url('login'); ?>?type=merchant">商户后台</a>
    </div>
    <div class="container">
        <?php echo $__content__ ?? ''; ?>
    </div>
    <div class="footer">
        <?php echo h(site_config('copyright', '鲸商城 Pro v1.0.0')); ?> | <?php echo h(site_config('icp') ?: ''); ?><?php echo site_config('icp') ? ' | ' : ''; ?>客服：<?php echo h(site_config('contact') ?: '-'); ?>
    </div>
    <a href="<?php echo url('chat'); ?>" class="chat-float" title="在线客服">💬</a>
    <script>
    (function() {
        const btn = document.getElementById('mobileMenuBtn');
        const drawer = document.getElementById('mobileDrawer');
        if (!btn || !drawer) return;
        btn.addEventListener('click', () => {
            drawer.classList.toggle('open');
        });
        document.addEventListener('click', (e) => {
            if (!btn.contains(e.target) && !drawer.contains(e.target)) {
                drawer.classList.remove('open');
            }
        });
    })();

    // 搜索建议
    (function() {
        const input = document.getElementById('globalSearchInput');
        const suggest = document.getElementById('searchSuggest');
        if (!input || !suggest) return;

        let timer = null;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            const keyword = input.value.trim();
            if (!keyword) {
                suggest.style.display = 'none';
                return;
            }
            timer = setTimeout(async () => {
                try {
                    const res = await fetch('<?php echo url('index/searchSuggest'); ?>?keyword=' + encodeURIComponent(keyword));
                    const data = await res.json();
                    if (data.code !== 0) return;
                    let html = '';
                    if (data.data.categories && data.data.categories.length) {
                        html += '<div style="padding:8px 12px; font-size:12px; color:#64748B; background:#F8FAFC;"><?php echo h(lang('goods.category')); ?></div>';
                        data.data.categories.forEach(c => {
                            html += '<a href="<?php echo url('index/category'); ?>?id=' + c.id + '" style="display:block; padding:10px 12px; color:#1F2937; border-bottom:1px solid #F1F5F9; font-size:13px;">' + c.name + '</a>';
                        });
                    }
                    if (data.data.goods && data.data.goods.length) {
                        html += '<div style="padding:8px 12px; font-size:12px; color:#64748B; background:#F8FAFC;"><?php echo h(lang('goods.price')); ?></div>';
                        data.data.goods.forEach(g => {
                            html += '<a href="<?php echo url('index/goods'); ?>?id=' + g.id + '" style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; color:#1F2937; border-bottom:1px solid #F1F5F9; font-size:13px;"><span>' + g.name + '</span><span style="color:#EF4444; font-weight:600;">¥' + parseFloat(g.price).toFixed(2) + '</span></a>';
                        });
                    }
                    if (!html) {
                        html = '<div style="padding:12px; color:#64748B; font-size:13px; text-align:center;"><?php echo h(lang('common.empty')); ?></div>';
                    }
                    suggest.innerHTML = html;
                    suggest.style.display = 'block';
                } catch (e) {
                    suggest.style.display = 'none';
                }
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !suggest.contains(e.target)) {
                suggest.style.display = 'none';
            }
        });
    })();
    </script>
</body>
</html>
