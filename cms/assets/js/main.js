/* 人民检察 V7.0 - 前端交互 */
(function() {
    'use strict';

    var doc = document;

    /* === 移动端抽屉菜单 === */
    function initMobileNav() {
        var trigger = doc.getElementById('navTrigger');
        var drawer = doc.getElementById('mobileDrawer');
        var overlay = doc.getElementById('mobileOverlay');
        var close = doc.getElementById('drawerClose');

        function open() {
            if (drawer) drawer.classList.add('open');
            if (overlay) overlay.classList.add('open');
        }
        function shut() {
            if (drawer) drawer.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
        }
        if (trigger) trigger.onclick = open;
        if (overlay) overlay.onclick = shut;
        if (close) close.onclick = shut;
    }

    /* === Toast 全局通知 === */
    window.showToast = function(msg, type) {
        type = type || 'info';
        var stack = doc.getElementById('toastStack');
        if (!stack) return;
        var el = doc.createElement('div');
        el.className = 'toast t-' + type;
        el.textContent = msg;
        stack.appendChild(el);
        setTimeout(function() {
            el.style.opacity = '0';
            el.style.transform = 'translateX(100%)';
            el.style.transition = 'all 0.3s';
            setTimeout(function() { el.remove(); }, 300);
        }, 2800);
    };

    /* === Modal 弹窗 === */
    window.showModal = function(title, content) {
        var mask = doc.getElementById('modalMask');
        var t = doc.getElementById('modalTitle');
        var b = doc.getElementById('modalBody');
        if (!mask) return;
        if (t) t.textContent = title || '提示';
        if (b) b.innerHTML = content || '';
        mask.classList.add('open');
    };
    window.closeModal = function() {
        var mask = doc.getElementById('modalMask');
        if (mask) mask.classList.remove('open');
    };

    /* === 表单提交时弹出提示 === */
    doc.addEventListener('DOMContentLoaded', function() {
        initMobileNav();
        initSlider();
        initFormValidate();

        // 表单提交成功提示
        var formToast = doc.querySelectorAll('[data-toast-form]');
        formToast.forEach(function(f) {
            f.addEventListener('submit', function(e) {
                var btn = f.querySelector('button[type=submit]');
                if (btn) { btn.disabled = true; btn.textContent = '处理中...'; }
            });
        });

        // 顶部公告轮播自动失效时间刷新
        var successMsg = doc.querySelector('.alert-success');
        if (successMsg) showToast(successMsg.textContent.trim(), 'success');
        var errorMsg = doc.querySelector('.alert-error');
        if (errorMsg) showToast(errorMsg.textContent.trim(), 'error');
    });

    /* === 轮播图 5秒方形进度条 === */
    function initSlider() {
        var slider = doc.querySelector('.slider');
        if (!slider) return;
        var track = slider.querySelector('.slider-track');
        var items = slider.querySelectorAll('.slider-item');
        var dots = slider.querySelectorAll('.slider-dots span');
        var progress = slider.querySelector('.slider-progress-fill');
        var prev = slider.querySelector('.slider-arrow.prev');
        var next = slider.querySelector('.slider-arrow.next');
        if (items.length <= 1) return;

        var idx = 0;
        var timer = null;
        var startTime = 0;
        var duration = 5000;
        var paused = false;

        function go(n) {
            idx = (n + items.length) % items.length;
            if (track) track.style.transform = 'translateX(-' + (idx * 100) + '%)';
            dots.forEach(function(d, i) { d.classList.toggle('on', i === idx); });
            startTime = Date.now();
        }
        function tick() {
            if (paused) return;
            var elapsed = Date.now() - startTime;
            if (elapsed >= duration) {
                go(idx + 1);
            } else {
                if (progress) progress.style.width = ((elapsed / duration) * 100) + '%';
            }
            timer = requestAnimationFrame(tick);
        }
        function play() { paused = false; startTime = Date.now() - (progress ? parseFloat(progress.style.width) / 100 * duration : 0); timer = requestAnimationFrame(tick); }
        function stop() { paused = true; if (timer) cancelAnimationFrame(timer); }

        if (prev) prev.onclick = function() { go(idx - 1); };
        if (next) next.onclick = function() { go(idx + 1); };
        dots.forEach(function(d, i) { d.onclick = function() { go(i); }; });
        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', play);
        // 触屏滑动
        var sx = 0;
        slider.addEventListener('touchstart', function(e) { sx = e.touches[0].clientX; stop(); });
        slider.addEventListener('touchend', function(e) {
            var ex = e.changedTouches[0].clientX;
            if (Math.abs(ex - sx) > 40) go(idx + (ex < sx ? 1 : -1));
            play();
        });

        go(0);
        play();
    }

    /* === 表单实时验证 === */
    function initFormValidate() {
        var validators = {
            username: function(v) {
                if (!v) return { ok: false, msg: '请输入用户名' };
                if (v.length < 3) return { ok: false, msg: '用户名至少3个字符' };
                if (v.length > 20) return { ok: false, msg: '用户名不能超过20字符' };
                if (!/^[\w\u4e00-\u9fa5]+$/.test(v)) return { ok: false, msg: '只能包含字母数字下划线汉字' };
                return { ok: true, msg: '用户名格式正确' };
            },
            password: function(v) {
                if (!v) return { ok: false, msg: '请输入密码' };
                if (v.length < 6) return { ok: false, msg: '密码至少6个字符' };
                if (v.length > 32) return { ok: false, msg: '密码不能超过32字符' };
                if (/^\d+$/.test(v)) return { ok: false, msg: '不能为纯数字' };
                var level = 0;
                if (/[a-z]/.test(v)) level++;
                if (/[A-Z]/.test(v)) level++;
                if (/\d/.test(v)) level++;
                if (/[^a-zA-Z0-9]/.test(v)) level++;
                var tip = level <= 1 ? '密码强度：弱' : level <= 2 ? '密码强度：中' : '密码强度：强';
                return { ok: true, msg: tip };
            },
            confirm_password: function(v) {
                var pwd = doc.querySelector('input[name=password]');
                if (!v) return { ok: false, msg: '请再次输入密码' };
                if (pwd && pwd.value && pwd.value !== v) return { ok: false, msg: '两次密码不一致' };
                return { ok: true, msg: '密码匹配' };
            },
            email: function(v) {
                if (!v) return { ok: true, msg: '' };
                if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v)) return { ok: false, msg: '邮箱格式不正确' };
                return { ok: true, msg: '邮箱格式正确' };
            },
            phone: function(v) {
                if (!v) return { ok: true, msg: '' };
                if (!/^1[3-9]\d{9}$/.test(v)) return { ok: false, msg: '请输入正确的11位手机号' };
                return { ok: true, msg: '手机号格式正确' };
            },
            title: function(v) {
                if (!v) return { ok: false, msg: '请输入标题' };
                if (v.length < 4) return { ok: false, msg: '标题至少4个字符' };
                if (v.length > 100) return { ok: false, msg: '标题不能超过100字符' };
                return { ok: true, msg: '标题符合要求' };
            },
            content: function(v) {
                if (!v) return { ok: false, msg: '请输入内容' };
                if (v.length < 10) return { ok: false, msg: '内容至少10个字符' };
                return { ok: true, msg: '内容长度合适' };
            }
        };

        doc.querySelectorAll('input[data-validate], textarea[data-validate]').forEach(function(input) {
            var tip = input.parentNode.querySelector('.form-tip');
            if (!tip) {
                tip = doc.createElement('div');
                tip.className = 'form-tip';
                input.parentNode.appendChild(tip);
            }
            input.addEventListener('input', function() {
                var type = input.getAttribute('data-validate');
                var validator = validators[type];
                if (!validator) return;
                var result = validator(input.value);
                input.classList.remove('ok', 'err');
                tip.classList.remove('tip-err', 'tip-ok');
                if (input.value.length > 0) {
                    if (result.ok) {
                        input.classList.add('ok');
                        tip.classList.add('tip-ok');
                    } else {
                        input.classList.add('err');
                        tip.classList.add('tip-err');
                    }
                    tip.textContent = result.msg;
                } else {
                    tip.textContent = '';
                }
            });
        });
    }
})();