<!DOCTYPE html>
<html lang="<?php echo $currentLang === 'en' ? 'en' : 'zh-CN'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title ?? site_config('site_name')); ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body class="<?php echo !empty($currentSubsite) ? 'has-subsite' : ''; ?>">
    <?php if (!empty($currentSubsite)): ?>
    <div class="subsite-bar">
        <span class="subsite-name"><?php echo h($currentSubsite['name']); ?></span>
        <span class="subsite-desc">分站首页</span>
        <a href="<?php echo base_url(); ?>?clear_subsite=1" class="subsite-back">
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-chevron-left"></use></svg>
            返回总站
        </a>
    </div>
    <?php endif; ?>

    <header class="topbar">
        <div class="topbar-left">
            <a href="<?php echo url('/'); ?>" class="logo">
                <?php if (site_config('logo')): ?>
                    <img src="<?php echo h(site_config('logo')); ?>" alt="<?php echo h(site_config('site_name')); ?>" style="height: 32px;">
                <?php else: ?>
                    <span class="logo-mark">
                        <svg class="icon" aria-hidden="true"><use href="#icon-zap"></use></svg>
                    </span>
                    <?php echo h(site_config('site_name')); ?>
                <?php endif; ?>
            </a>
            <?php if (!empty($currentSubsite)): ?>
            <span class="subsite-tag"><?php echo h($currentSubsite['name']); ?></span>
            <?php endif; ?>
            <form class="search-box" method="get" action="<?php echo url('index/category'); ?>">
                <svg class="icon" aria-hidden="true"><use href="#icon-search"></use></svg>
                <input type="text" name="keyword" placeholder="<?php echo h(lang('goods.search_placeholder')); ?>" value="<?php echo h(input('keyword', '')); ?>" autocomplete="off" id="globalSearchInput">
                <button type="submit" class="btn"><?php echo h(lang('nav.search')); ?></button>
                <div id="searchSuggest" class="search-suggest" style="display:none;"></div>
            </form>
        </div>
        <nav class="topbar-links">
            <a href="<?php echo url('index/category'); ?>">
                <svg class="icon" aria-hidden="true"><use href="#icon-category"></use></svg>
                <?php echo h(lang('nav.category')); ?>
            </a>
            <a href="<?php echo url('index/coupon'); ?>">
                <svg class="icon" aria-hidden="true"><use href="#icon-coupon"></use></svg>
                领券中心
            </a>
            <a href="<?php echo url('index/order'); ?>">
                <svg class="icon" aria-hidden="true"><use href="#icon-search"></use></svg>
                <?php echo h(lang('order.query')); ?>
            </a>
            <a href="<?php echo url('index/user'); ?>">
                <svg class="icon" aria-hidden="true"><use href="#icon-user"></use></svg>
                <?php echo h(lang('nav.user')); ?>
            </a>
            <a href="<?php echo url('index/merchantJoin'); ?>">
                <svg class="icon" aria-hidden="true"><use href="#icon-merchant"></use></svg>
                商户入驻
            </a>
            <a href="<?php echo url('login'); ?>?type=admin">
                <svg class="icon" aria-hidden="true"><use href="#icon-admin"></use></svg>
                总站后台
            </a>
            <a href="<?php echo url('login'); ?>?type=merchant">
                <svg class="icon" aria-hidden="true"><use href="#icon-merchant"></use></svg>
                商户后台
            </a>
            <a href="<?php echo h(switch_lang_url($currentLang === 'zh-cn' ? 'en' : 'zh-cn')); ?>" title="<?php echo h(lang('lang.' . ($currentLang === 'zh-cn' ? 'en' : 'zh-cn'))); ?>">
                <svg class="icon" aria-hidden="true"><use href="#icon-globe"></use></svg>
                <?php echo h(lang('lang.' . ($currentLang === 'zh-cn' ? 'en' : 'zh-cn'))); ?>
            </a>
        </nav>
        <div class="hamburger" id="hamburgerBtn" aria-label="菜单" role="button">
            <span></span><span></span><span></span>
        </div>
    </header>

    <div class="drawer-overlay" id="drawerOverlay"></div>
    <nav class="mobile-drawer" id="mobileDrawer">
        <a href="<?php echo url('index/category'); ?>">
            <svg class="icon" aria-hidden="true"><use href="#icon-category"></use></svg>
            <?php echo h(lang('nav.category')); ?>
        </a>
        <a href="<?php echo url('index/coupon'); ?>">
            <svg class="icon" aria-hidden="true"><use href="#icon-coupon"></use></svg>
            领券中心
        </a>
        <a href="<?php echo url('index/order'); ?>">
            <svg class="icon" aria-hidden="true"><use href="#icon-search"></use></svg>
            <?php echo h(lang('order.query')); ?>
        </a>
        <a href="<?php echo url('index/user'); ?>">
            <svg class="icon" aria-hidden="true"><use href="#icon-user"></use></svg>
            <?php echo h(lang('nav.user')); ?>
        </a>
        <a href="<?php echo url('index/merchantJoin'); ?>">
            <svg class="icon" aria-hidden="true"><use href="#icon-merchant"></use></svg>
            商户入驻
        </a>
        <a href="<?php echo url('login'); ?>?type=admin">
            <svg class="icon" aria-hidden="true"><use href="#icon-admin"></use></svg>
            总站后台
        </a>
        <a href="<?php echo url('login'); ?>?type=merchant">
            <svg class="icon" aria-hidden="true"><use href="#icon-merchant"></use></svg>
            商户后台
        </a>
        <a href="<?php echo h(switch_lang_url($currentLang === 'zh-cn' ? 'en' : 'zh-cn')); ?>">
            <svg class="icon" aria-hidden="true"><use href="#icon-globe"></use></svg>
            <?php echo h(lang('lang.' . ($currentLang === 'zh-cn' ? 'en' : 'zh-cn'))); ?>
        </a>
    </nav>

    <main class="container">
        <?php echo $__content__ ?? ''; ?>
    </main>

    <footer class="footer-site">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="<?php echo url('/'); ?>" class="logo">
                    <span class="logo-mark"><svg class="icon" aria-hidden="true"><use href="#icon-zap"></use></svg></span>
                    <?php echo h(site_config('site_name')); ?>
                </a>
                <p style="margin-top: 8px; max-width: 320px;"><?php echo h(lang('home.slogan')); ?></p>
            </div>
            <div>
                <h4>购物</h4>
                <ul>
                    <li><a href="<?php echo url('index/category'); ?>"><?php echo h(lang('nav.category')); ?></a></li>
                    <li><a href="<?php echo url('index/coupon'); ?>">领券中心</a></li>
                    <li><a href="<?php echo url('index/order'); ?>"><?php echo h(lang('order.query')); ?></a></li>
                </ul>
            </div>
            <div>
                <h4>关于</h4>
                <ul>
                    <li><a href="<?php echo url('index/merchantJoin'); ?>">商户入驻</a></li>
                    <li><a href="<?php echo url('login'); ?>?type=admin">总站后台</a></li>
                    <li><a href="<?php echo url('login'); ?>?type=merchant">商户后台</a></li>
                </ul>
            </div>
            <div>
                <h4>支持</h4>
                <ul>
                    <li><a href="<?php echo url('chat'); ?>">在线客服</a></li>
                    <li><span>客服：<?php echo h(site_config('contact') ?: '-'); ?></span></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span><?php echo h(site_config('copyright', '鲸商城 Pro v1.0.0')); ?><?php if (site_config('icp')): ?> | <?php echo h(site_config('icp')); ?><?php endif; ?></span>
            <span>客服：<?php echo h(site_config('contact') ?: '-'); ?></span>
        </div>
    </footer>

    <a href="<?php echo url('chat'); ?>" class="chat-float" title="在线客服" aria-label="在线客服">
        <svg class="icon" aria-hidden="true"><use href="#icon-headphones"></use></svg>
    </a>

    <script src="/static/js/app.js"></script>
    <script>
    // 搜索建议（保留原有逻辑，使用新的样式类名）
    (function() {
        var input = document.getElementById('globalSearchInput');
        var suggest = document.getElementById('searchSuggest');
        if (!input || !suggest) return;

        var searchUrl = '<?php echo url('index/searchSuggest'); ?>';
        var categoryUrl = '<?php echo url('index/category'); ?>';
        var goodsUrl = '<?php echo url('index/goods'); ?>';
        var langCategory = '<?php echo h(lang('goods.category')); ?>';
        var langPrice = '<?php echo h(lang('goods.price')); ?>';
        var langEmpty = '<?php echo h(lang('common.empty')); ?>';

        var timer = null;
        input.addEventListener('input', function() {
            clearTimeout(timer);
            var keyword = input.value.trim();
            if (!keyword) { suggest.style.display = 'none'; return; }
            timer = setTimeout(function() {
                App.ajax(searchUrl + '?keyword=' + encodeURIComponent(keyword)).then(function(data) {
                    if (!data || data.code !== 0 || !data.data) { suggest.style.display = 'none'; return; }
                    var html = '';
                    if (data.data.categories && data.data.categories.length) {
                        html += '<div class="suggest-title">' + langCategory + '</div>';
                        data.data.categories.forEach(function(c) {
                            html += '<a href="' + categoryUrl + '?id=' + c.id + '"><span>' + escapeHtml(c.name) + '</span></a>';
                        });
                    }
                    if (data.data.goods && data.data.goods.length) {
                        html += '<div class="suggest-title">' + langPrice + '</div>';
                        data.data.goods.forEach(function(g) {
                            html += '<a href="' + goodsUrl + '?id=' + g.id + '"><span>' + escapeHtml(g.name) + '</span><span style="color:#EF4444;font-weight:600;">¥' + parseFloat(g.price).toFixed(2) + '</span></a>';
                        });
                    }
                    if (!html) {
                        html = '<div class="suggest-title" style="text-align:center;color:#94A3B8;">' + langEmpty + '</div>';
                    }
                    suggest.innerHTML = html;
                    suggest.style.display = 'block';
                });
            }, 300);
        });

        function escapeHtml(s) {
            return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
    })();
    </script>
</body>
</html>
