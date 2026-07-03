/* =====================================================================
   鲸商城 Pro · 全局 JS 框架  app.js
   功能：
   1) SVG 图标注入系统（拉取 sprite.svg 并内联，<use> 即时可用）
   2) 页面加载动画控制（loader 淡出 + 内容淡入）
   3) 汉堡菜单切换（三线变 X 流畅动画）
   4) 移动端抽屉菜单（滑入 + 遮罩）
   5) 侧边栏折叠 / 移动端开合
   6) Toast 通知系统
   7) AJAX 请求封装
   8) 搜索建议（防抖）
   9) 按钮 loading 状态
   10) 滚动动画（IntersectionObserver）
   11) 数字滚动动画
   12) 顶栏滚动效果
   依赖：无（纯原生 JS，PHP 8.2 兼容仅指后端）
   ===================================================================== */
(function () {
    'use strict';

    /* ---------------------- 工具函数 ---------------------- */
    var doc = document;
    var win = window;
    function $(sel, ctx) { return (ctx || doc).querySelector(sel); }
    function $all(sel, ctx) { return Array.prototype.slice.call((ctx || doc).querySelectorAll(sel)); }
    function on(el, evt, handler) {
        if (el && typeof el.addEventListener === 'function') { el.addEventListener(evt, handler); }
    }
    function debounce(fn, wait) {
        var t = null;
        return function () {
            var args = arguments, self = this;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(self, args); }, wait || 250);
        };
    }

    /* =====================================================================
       1) SVG 图标注入系统
       拉取 /static/icons/sprite.svg，将 <symbol> 注入到 <body> 顶部一个隐藏 <svg>。
       之后 <svg class="icon"><use href="#icon-xxx"></use></svg> 即可工作。
       同时提供 window.IconSVG.get(name) 与对 [data-icon] 的自动替换。
       ===================================================================== */
    var IconSystem = (function () {
        var SPRITE_URL = '/static/icons/sprite.svg';
        var injected = false;
        var cache = null;

        function inject(symbolsXml) {
            if (injected) return;
            var holder = doc.createElement('svg');
            holder.setAttribute('aria-hidden', 'true');
            holder.setAttribute('style', 'position:absolute;width:0;height:0;overflow:hidden');
            holder.innerHTML = symbolsXml;
            doc.body.insertBefore(holder, doc.body.firstChild);
            injected = true;
        }

        function replacePlaceholders() {
            $all('[data-icon]').forEach(function (el) {
                var name = el.getAttribute('data-icon');
                var size = el.getAttribute('data-size') || '';
                var cls = el.className || '';
                el.outerHTML = '<svg class="icon ' + (cls ? cls : '') + ' ' + (size ? ('icon-' + size) : '') + '" aria-hidden="true"><use href="#icon-' + name + '"></use></svg>';
            });
        }

        function load() {
            if (cache) { inject(cache); replacePlaceholders(); return; }
            // 优先 fetch
            if (win.fetch) {
                fetch(SPRITE_URL, { credentials: 'same-origin' })
                    .then(function (r) { return r.ok ? r.text() : Promise.reject(r.status); })
                    .then(function (txt) {
                        cache = txt;
                        inject(txt);
                        replacePlaceholders();
                    })
                    .catch(function () { /* 静默失败，页面仍可用 */ });
            } else {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', SPRITE_URL, true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        cache = xhr.responseText;
                        inject(cache);
                        replacePlaceholders();
                    }
                };
                xhr.send();
            }
        }

        function get(name) {
            return '<svg class="icon" aria-hidden="true"><use href="#icon-' + name + '"></use></svg>';
        }

        return { load: load, get: get, inject: inject };
    })();
    win.IconSystem = IconSystem;

    /* =====================================================================
       2) 页面加载动画控制
       ===================================================================== */
    var PageLoader = (function () {
        function create() {
            if ($('#__pageLoader')) return;
            var el = doc.createElement('div');
            el.id = '__pageLoader';
            el.className = 'page-loader';
            el.innerHTML =
                '<div class="loader-mark">' +
                '<svg class="icon" aria-hidden="true"><use href="#icon-zap"></use></svg>' +
                '</div><div class="loader-text">加载中…</div>';
            doc.body.appendChild(el);
        }
        function hide() {
            var el = $('#__pageLoader');
            if (!el) return;
            el.classList.add('hide');
            setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 450);
        }
        return { create: create, hide: hide };
    })();
    win.PageLoader = PageLoader;

    /* =====================================================================
       3) Toast 通知系统
       Toast.success(msg, opts) / .error / .warning / .info
       ===================================================================== */
    var Toast = (function () {
        var ICONS = {
            success: 'icon-check-circle',
            error: 'icon-x-circle',
            warning: 'icon-alert',
            info: 'icon-info'
        };
        var TITLES = { success: '成功', error: '错误', warning: '提示', info: '消息' };

        function container() {
            var c = $('#__toastStack');
            if (!c) {
                c = doc.createElement('div');
                c.id = '__toastStack';
                c.className = 'toast-stack';
                doc.body.appendChild(c);
            }
            return c;
        }
        function show(type, msg, opts) {
            opts = opts || {};
            var title = opts.title || TITLES[type] || '消息';
            var duration = opts.duration || 3500;
            var el = doc.createElement('div');
            el.className = 'toast toast-' + type;
            el.innerHTML =
                '<span class="toast-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#' + ICONS[type] + '"></use></svg></span>' +
                '<div class="toast-body"><div class="toast-title">' + escapeHtml(title) + '</div><div>' + escapeHtml(msg) + '</div></div>' +
                '<span class="toast-close"><svg class="icon icon-sm" aria-hidden="true"><use href="#icon-close"></use></svg></span>';
            container().appendChild(el);
            // 触发进入动画
            requestAnimationFrame(function () { el.classList.add('show'); });
            var timer = null;
            function dismiss() {
                if (timer) { clearTimeout(timer); timer = null; }
                el.classList.remove('show');
                setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 420);
            }
            el.querySelector('.toast-close').addEventListener('click', dismiss);
            if (duration > 0) { timer = setTimeout(dismiss, duration); }
            return { dismiss: dismiss };
        }
        function escapeHtml(s) {
            return String(s == null ? '' : s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }
        return {
            show: show,
            success: function (m, o) { return show('success', m, o); },
            error: function (m, o) { return show('error', m, o); },
            warning: function (m, o) { return show('warning', m, o); },
            info: function (m, o) { return show('info', m, o); }
        };
    })();
    win.Toast = Toast;

    /* =====================================================================
       4) AJAX 请求封装
       App.ajax(url, opts) 返回 Promise；自动处理 JSON、loading 与错误 Toast
       ===================================================================== */
    function buildQuery(params) {
        if (!params) return '';
        var parts = [];
        for (var k in params) {
            if (Object.prototype.hasOwnProperty.call(params, k) && params[k] != null) {
                parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(params[k]));
            }
        }
        return parts.length ? '?' + parts.join('&') : '';
    }
    function ajax(url, opts) {
        opts = opts || {};
        var method = (opts.method || 'GET').toUpperCase();
        var headers = Object.assign({ 'X-Requested-With': 'XMLHttpRequest' }, opts.headers || {});
        var fetchOpts = { method: method, headers: headers, credentials: 'same-origin' };
        var isForm = opts.body instanceof FormData;
        if (opts.body) {
            if (isForm) {
                fetchOpts.body = opts.body;
            } else if (typeof opts.body === 'object') {
                fetchOpts.body = JSON.stringify(opts.body);
                headers['Content-Type'] = 'application/json';
            } else {
                fetchOpts.body = opts.body;
            }
        }
        return fetch(url, fetchOpts).then(function (res) {
            var ct = res.headers.get('content-type') || '';
            if (ct.indexOf('application/json') !== -1) {
                return res.json().then(function (data) {
                    if (!res.ok && data.code == null) { data = { code: res.status, msg: data }; }
                    return data;
                });
            }
            return res.text().then(function (txt) { return { code: res.ok ? 0 : res.status, msg: txt, data: txt }; });
        }).catch(function (err) {
            return { code: -1, msg: '网络请求失败', data: null, _err: err };
        });
    }
    // 便捷方法：ajax.get / ajax.post
    ajax.get = function (url, params) {
        return ajax(url + buildQuery(params), { method: 'GET' });
    };
    ajax.post = function (url, body, params) {
        return ajax(url + buildQuery(params), { method: 'POST', body: body });
    };
    win.ajax = ajax;

    /* =====================================================================
       5) 按钮 loading 状态
       App.btnLoading(el) / App.btnReset(el)
       ===================================================================== */
    function btnLoading(el) {
        if (!el) return function () {};
        if (el._appLoading) return function () {};
        el._appLoading = true;
        var oldHtml = el.innerHTML;
        var oldWidth = el.style.width;
        var oldDisabled = el.disabled;
        el.style.width = el.offsetWidth + 'px';
        el.disabled = true;
        el.classList.add('is-loading');
        el._appLoadingOldHtml = oldHtml;
        return function () {
            if (!el._appLoading) return;
            el._appLoading = false;
            el.innerHTML = el._appLoadingOldHtml;
            el.style.width = oldWidth;
            el.disabled = oldDisabled;
            el.classList.remove('is-loading');
        };
    }
    function btnReset(el) {
        if (!el || !el._appLoading) return;
        el._appLoading = false;
        el.innerHTML = el._appLoadingOldHtml;
        el.style.width = '';
        el.disabled = false;
        el.classList.remove('is-loading');
    }
    win.btnLoading = btnLoading;
    win.btnReset = btnReset;

    /* =====================================================================
       6) 汉堡菜单 + 移动端抽屉
       ===================================================================== */
    function initHamburger() {
        var btn = $('#hamburgerBtn, .hamburger, #mobileMenuBtn');
        var drawer = $('#mobileDrawer, .mobile-drawer');
        var overlay = $('#drawerOverlay');
        if (!btn || !drawer) return;

        function createOverlay() {
            if (overlay) return overlay;
            var o = doc.createElement('div');
            o.id = 'drawerOverlay';
            o.className = 'drawer-overlay';
            drawer.parentNode.insertBefore(o, drawer);
            return o;
        }
        overlay = createOverlay();

        function open() {
            btn.classList.add('open');
            drawer.classList.add('open');
            overlay.classList.add('open');
            doc.body.style.overflow = 'hidden';
        }
        function close() {
            btn.classList.remove('open');
            drawer.classList.remove('open');
            overlay.classList.remove('open');
            doc.body.style.overflow = '';
        }
        function toggle() { drawer.classList.contains('open') ? close() : open(); }

        on(btn, 'click', function (e) { e.stopPropagation(); toggle(); });
        on(overlay, 'click', close);
        on(drawer, 'click', 'a', function () { /* 点击链接后关闭 */ close(); });
        on(doc, 'click', function (e) {
            if (!drawer.classList.contains('open')) return;
            if (!btn.contains(e.target) && !drawer.contains(e.target)) { close(); }
        });
        on(win, 'resize', debounce(function () {
            if (win.innerWidth > 768) { close(); }
        }, 200));
    }

    /* =====================================================================
       7) 侧边栏折叠 / 移动端开合（后台布局）
       ===================================================================== */
    function initSidebar() {
        var toggle = $('#menuToggle, .menu-toggle');
        var sidebar = $('#sidebar, .sidebar');
        if (!toggle || !sidebar) return;

        var overlay = null;
        function ensureOverlay() {
            if (overlay) return overlay;
            overlay = doc.createElement('div');
            overlay.className = 'drawer-overlay';
            sidebar.parentNode.appendChild(overlay);
            on(overlay, 'click', function () {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                doc.body.style.overflow = '';
            });
            return overlay;
        }

        on(toggle, 'click', function () {
            if (win.innerWidth <= 768) {
                sidebar.classList.toggle('open');
                ensureOverlay().classList.toggle('open', sidebar.classList.contains('open'));
                doc.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
            } else {
                sidebar.classList.toggle('collapsed');
                try { localStorage.setItem('app_sidebar_collapsed', sidebar.classList.contains('collapsed') ? '1' : '0'); } catch (e) {}
            }
        });

        // 记忆折叠状态（桌面端）
        try {
            if (win.innerWidth > 768 && localStorage.getItem('app_sidebar_collapsed') === '1') {
                sidebar.classList.add('collapsed');
            }
        } catch (e) {}

        // 子菜单展开
        $all('.menu-link[data-has-submenu="1"]').forEach(function (link) {
            on(link, 'click', function (e) {
                e.preventDefault();
                var submenu = link.nextElementSibling;
                if (!submenu) return;
                var arrow = link.querySelector('.arrow');
                var isOpen = submenu.classList.contains('show');
                // 折叠态不展开子菜单
                if (sidebar.classList.contains('collapsed') && win.innerWidth > 768) return;
                submenu.classList.toggle('show');
                if (arrow) arrow.classList.toggle('rotate', !isOpen);
            });
        });
    }

    /* =====================================================================
       8) 搜索建议（防抖）
       依赖元素：#globalSearchInput（输入框）、#searchSuggest（结果容器）
       可通过 data-suggest-url 指定接口（默认使用元素 action 或 window.App.searchSuggestUrl）
       ===================================================================== */
    function initSearchSuggest() {
        var input = $('#globalSearchInput');
        var suggest = $('#searchSuggest');
        if (!input || !suggest) return;
        var apiUrl = input.getAttribute('data-suggest-url') || (win.App && win.App.searchSuggestUrl) || '';
        // 从所在 form 的 action 推导
        if (!apiUrl) {
            var form = input.closest('form');
            if (form && form.getAttribute('action')) {
                apiUrl = form.getAttribute('action').replace(/\/category.*$/, '/searchSuggest');
            }
        }

        var render = win.App && win.App.renderSearchSuggest ? win.App.renderSearchSuggest : null;
        var run = debounce(function () {
            var kw = input.value.trim();
            if (!kw || !apiUrl) { suggest.style.display = 'none'; return; }
            ajax(apiUrl + (apiUrl.indexOf('?') === -1 ? '?' : '&') + 'keyword=' + encodeURIComponent(kw))
                .then(function (data) {
                    if (!data || data.code !== 0 || !data.data) { suggest.style.display = 'none'; return; }
                    if (render) {
                        suggest.innerHTML = render(data.data) || '';
                    } else {
                        suggest.innerHTML = defaultRender(data.data);
                    }
                    suggest.style.display = suggest.innerHTML ? 'block' : 'none';
                });
        }, 300);

        on(input, 'input', run);
        on(doc, 'click', function (e) {
            if (!input.contains(e.target) && !suggest.contains(e.target)) { suggest.style.display = 'none'; }
        });
        on(input, 'focus', function () { if (suggest.innerHTML) suggest.style.display = 'block'; });

        function defaultRender(d) {
            var html = '';
            if (d.categories && d.categories.length) {
                html += '<div class="suggest-title">分类</div>';
                d.categories.forEach(function (c) {
                    html += '<a href="' + (win.App ? (win.App.urlBase || '') : '') + '/index/category?id=' + c.id + '"><span>' + escapeHtml(c.name) + '</span><svg class="icon icon-sm"><use href="#icon-chevron-right"></use></svg></a>';
                });
            }
            if (d.goods && d.goods.length) {
                html += '<div class="suggest-title">商品</div>';
                d.goods.forEach(function (g) {
                    html += '<a href="' + (win.App ? (win.App.urlBase || '') : '') + '/index/goods?id=' + g.id + '"><span>' + escapeHtml(g.name) + '</span><span style="color:#EF4444;font-weight:600">¥' + parseFloat(g.price).toFixed(2) + '</span></a>';
                });
            }
            return html;
        }
        function escapeHtml(s) {
            return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    }

    /* =====================================================================
       9) 滚动动画（IntersectionObserver）
       给 .reveal / .stagger 添加 in-view
       ===================================================================== */
    function initReveal() {
        var els = $all('.reveal, .stagger');
        if (!els.length) return;
        if (!('IntersectionObserver' in win)) {
            els.forEach(function (el) { el.classList.add('in-view'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        els.forEach(function (el) { io.observe(el); });
    }

    /* =====================================================================
       10) 数字滚动动画
       元素加 data-count="12345" 即可；可选 data-duration
       ===================================================================== */
    function animateCount(el) {
        var target = parseFloat(el.getAttribute('data-count'));
        if (isNaN(target)) return;
        var dur = parseInt(el.getAttribute('data-duration') || '1200', 10);
        var prefix = el.getAttribute('data-prefix') || '';
        var suffix = el.getAttribute('data-suffix') || '';
        var decimals = (el.getAttribute('data-decimals') | 0);
        var start = 0, startTime = null;
        function step(ts) {
            if (!startTime) startTime = ts;
            var p = Math.min(1, (ts - startTime) / dur);
            var eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
            var val = start + (target - start) * eased;
            el.textContent = prefix + formatNum(val, decimals) + suffix;
            if (p < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }
    function formatNum(n, decimals) {
        var fixed = n.toFixed(decimals);
        var parts = fixed.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return parts.join('.');
    }
    function initCounters() {
        var els = $all('[data-count]');
        if (!els.length) return;
        if (!('IntersectionObserver' in win)) { els.forEach(animateCount); return; }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { animateCount(entry.target); io.unobserve(entry.target); }
            });
        }, { threshold: 0.5 });
        els.forEach(function (el) { io.observe(el); });
    }

    /* =====================================================================
       11) 顶栏滚动效果
       ===================================================================== */
    function initTopbarScroll() {
        var topbar = $('.topbar');
        if (!topbar) return;
        var onScroll = debounce(function () {
            if (win.scrollY > 8) topbar.classList.add('scrolled');
            else topbar.classList.remove('scrolled');
        }, 10);
        on(win, 'scroll', onScroll, { passive: true });
        onScroll();
    }

    /* =====================================================================
       12) 全屏切换
       ===================================================================== */
    function initFullscreen() {
        $all('[data-action="fullscreen"]').forEach(function (btn) {
            on(btn, 'click', function () {
                if (!doc.fullscreenElement) {
                    if (doc.documentElement.requestFullscreen) doc.documentElement.requestFullscreen();
                } else if (doc.exitFullscreen) {
                    doc.exitFullscreen();
                }
            });
        });
    }

    /* =====================================================================
       启动
       ===================================================================== */
    function boot() {
        // 1. 注入图标（尽早执行）
        IconSystem.load();

        // 2. 隐藏页面加载动画（等图标注入稍后）
        win.addEventListener('load', function () {
            setTimeout(function () { PageLoader.hide(); }, 200);
        });
        // 兜底：若 load 已触发或卡住，3s 后强制隐藏
        setTimeout(function () { PageLoader.hide(); }, 3000);

        // 3. 各交互模块
        initHamburger();
        initSidebar();
        initSearchSuggest();
        initReveal();
        initCounters();
        initTopbarScroll();
        initFullscreen();

        // 4. data-loading 按钮：点击自动加 loading
        $all('[data-loading]').forEach(function (btn) {
            on(btn, 'click', function () { btnLoading(btn); });
        });

        // 5. 表单提交加 loading（带 data-form-loading 的表单）
        $all('form[data-form-loading]').forEach(function (form) {
            on(form, 'submit', function () {
                var btn = form.querySelector('[type="submit"]');
                if (btn) btnLoading(btn);
            });
        });
    }

    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    /* ---------------------- 公共命名空间 ---------------------- */
    win.App = win.App || {};
    Object.assign(win.App, {
        ajax: ajax,
        toast: Toast,
        btnLoading: btnLoading,
        btnReset: btnReset,
        Icon: IconSystem.get,
        buildQuery: buildQuery
    });
})();
