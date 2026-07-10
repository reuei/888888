/* 玄武发卡 v1.0.5 - 全新JavaScript
   简洁、克制、无浮夸动画 */

(function() {
'use strict';

window.App = {
    init: function() {
        this.initForms();
        this.initSliders();
        this.initToasts();
    },

    // ============ 表单验证 ============
    initForms: function() {
        var self = this;
        document.querySelectorAll('form').forEach(function(form) {
            // 实时验证
            form.querySelectorAll('input[data-validate], textarea[data-validate]').forEach(function(input) {
                input.addEventListener('input', function() {
                    self.validateField(input);
                });
                input.addEventListener('blur', function() {
                    self.validateField(input);
                });
            });

            // 提交处理
            form.addEventListener('submit', function(e) {
                if (form.id === 'installForm') return;
                e.preventDefault();
                var valid = true;
                form.querySelectorAll('input[data-validate], textarea[data-validate]').forEach(function(input) {
                    if (!self.validateField(input)) valid = false;
                });
                if (!valid) return;

                var slider = form.querySelector('.slider-captcha.success');
                if (form.querySelector('.slider-captcha') && !slider) {
                    self.toast('请先完成滑块验证', 'error');
                    return;
                }

                var btn = form.querySelector('button[type=submit]');
                if (btn) {
                    btn.disabled = true;
                    var orig = btn.textContent;
                    btn.textContent = '处理中...';
                }
                var data = new FormData(form);
                fetch(form.action || location.href, {
                    method: 'POST',
                    body: data
                })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    if (res.code === 0) {
                        self.toast(res.msg, 'success');
                        if (res.data && res.data.url) {
                            setTimeout(function() { location.href = res.data.url; }, 600);
                        }
                    } else {
                        self.toast(res.msg || '操作失败', 'error');
                    }
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = orig;
                    }
                })
                .catch(function() {
                    self.toast('网络错误', 'error');
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = orig;
                    }
                });
            });
        });
    },

    validateField: function(input) {
        var type = input.dataset.validate;
        var value = input.value;
        var min = parseInt(input.dataset.min) || 0;
        var max = parseInt(input.dataset.max) || 999999;
        var error = '';

        if (type === 'username') {
            if (value.length < min) error = '至少 ' + min + ' 个字符';
            else if (value.length > max) error = '不能超过 ' + max + ' 个字符';
            else if (!/^[a-zA-Z0-9_]+$/.test(value)) error = '只能包含字母、数字和下划线';
        } else if (type === 'password') {
            if (value.length < min) error = '至少 ' + min + ' 个字符';
        } else if (type === 'confirm') {
            var form = input.closest('form');
            var pw = form.querySelector('input[type=password]');
            if (pw && value !== pw.value) error = '两次密码不一致';
        } else if (type === 'email') {
            if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) error = '邮箱格式不正确';
        } else if (type === 'nickname') {
            if (value.length < min) error = '至少 ' + min + ' 个字符';
            else if (value.length > max) error = '不能超过 ' + max + ' 个字符';
        } else if (type === 'title') {
            if (value.length < min) error = '至少 ' + min + ' 个字符';
        } else if (type === 'content') {
            if (value.length < min) error = '至少 ' + min + ' 个字符';
        }

        var errEl = input.parentNode.querySelector('.form-error');
        if (error) {
            input.classList.add('error');
            if (errEl) errEl.textContent = error;
            return false;
        }
        input.classList.remove('error');
        if (errEl) errEl.textContent = '';
        return true;
    },

    // ============ 滑块验证 ============
    initSliders: function() {
        var self = this;
        document.querySelectorAll('.slider-captcha').forEach(function(captcha) {
            self.setupSlider(captcha);
        });
    },

    setupSlider: function(captcha) {
        var self = this;
        var handle = captcha.querySelector('.slider-handle');
        var tip = captcha.querySelector('.slider-tip');
        var success = captcha.querySelector('.slider-success');
        var verified = false;
        var startX = 0;
        var currentX = 0;
        var tokenInput = document.getElementById('sliderToken');
        var xInput = document.getElementById('sliderX');

        if (tokenInput && tokenInput.value) {
            verified = true;
            captcha.classList.add('success');
            if (tip) tip.style.display = 'none';
            if (success) success.style.display = 'block';
            if (handle) handle.style.left = '100%';
            return;
        }

        function start(e) {
            if (verified) return;
            e.preventDefault();
            startX = (e.touches ? e.touches[0].clientX : e.clientX) - currentX;
            document.addEventListener(e.touches ? 'touchmove' : 'mousemove', move);
            document.addEventListener(e.touches ? 'touchend' : 'mouseup', end);
        }
        function move(e) {
            var clientX = e.touches ? e.touches[0].clientX : e.clientX;
            currentX = clientX - startX;
            var max = captcha.offsetWidth - handle.offsetWidth;
            if (currentX < 0) currentX = 0;
            if (currentX > max) currentX = max;
            handle.style.left = currentX + 'px';
        }
        function end() {
            document.removeEventListener('mousemove', move);
            document.removeEventListener('touchmove', move);
            document.removeEventListener('mouseup', end);
            document.removeEventListener('touchend', end);
            self.requestSlider(function(token) {
                if (tokenInput) tokenInput.value = token;
                if (xInput) xInput.value = Math.round(currentX);
                verified = true;
                captcha.classList.add('success');
                if (tip) tip.style.display = 'none';
                if (success) success.style.display = 'block';
                var max = captcha.offsetWidth - handle.offsetWidth;
                handle.style.left = max + 'px';
            });
        }

        handle.addEventListener('mousedown', start);
        handle.addEventListener('touchstart', start);
    },

    requestSlider: function(cb) {
        fetch('/slider')
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.code === 0 && res.data && res.data.token) {
                    cb(res.data.token);
                }
            })
            .catch(function() {
                cb('offline-' + Date.now());
            });
    },

    // ============ Toast 提示 ============
    initToasts: function() {
        if (!document.getElementById('toastContainer')) {
            var c = document.createElement('div');
            c.id = 'toastContainer';
            c.style.cssText = 'position:fixed;top:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
            document.body.appendChild(c);
        }
    },

    toast: function(msg, type) {
        var c = document.getElementById('toastContainer');
        if (!c) return;
        type = type || 'info';
        var t = document.createElement('div');
        var bg = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#06b6d4';
        t.style.cssText = 'padding:10px 16px;background:' + bg + ';color:#0a0a0a;border-radius:6px;font-size:13px;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,0.4);animation:toastIn 0.2s ease';
        t.textContent = msg;
        c.appendChild(t);
        setTimeout(function() {
            t.style.transition = 'opacity 200ms, transform 200ms';
            t.style.opacity = '0';
            t.style.transform = 'translateY(-8px)';
            setTimeout(function() { t.remove(); }, 200);
        }, 2400);
    }
};

// 注入动画
var style = document.createElement('style');
style.textContent = '@keyframes toastIn{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}';
document.head.appendChild(style);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { App.init(); });
} else {
    App.init();
}
})();
