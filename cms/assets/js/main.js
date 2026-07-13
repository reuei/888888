/**
 * 清廉在线 V6.0 - 前端交互脚本
 * 完全重写，去AI味，原生JS无依赖
 */

(function() {
    'use strict';

    var doc = document;

    // === DOM就绪 ===
    function ready(fn) {
        if (doc.readyState !== 'loading') {
            fn();
        } else {
            doc.addEventListener('DOMContentLoaded', fn);
        }
    }

    // === Toast通知 ===
    window.showToast = function(msg, type) {
        type = type || 'info';
        var container = doc.querySelector('.toast-container');
        if (!container) {
            container = doc.createElement('div');
            container.className = 'toast-container';
            doc.body.appendChild(container);
        }
        var toast = doc.createElement('div');
        toast.className = 'toast ' + type;
        toast.textContent = msg;
        container.appendChild(toast);
        setTimeout(function() {
            toast.remove();
        }, 3000);
    };

    // === B2弹窗 ===
    window.showModal = function(title, content, opts) {
        opts = opts || {};
        var overlay = doc.createElement('div');
        overlay.className = 'modal-overlay active';
        var html = '<div class="modal-box">' +
            '<div class="modal-header"><h3>' + (title || '提示') + '</h3><button class="modal-close">&times;</button></div>' +
            '<div class="modal-body">' + content + '</div>';
        if (opts.type === 'confirm') {
            html += '<div class="modal-footer"><button class="btn modal-btn-default" data-action="cancel">' + (opts.cancelText || '取消') + '</button><button class="btn" data-action="ok">' + (opts.okText || '确定') + '</button></div>';
        }
        html += '</div>';
        overlay.innerHTML = html;
        doc.body.appendChild(overlay);

        overlay.querySelector('.modal-close').onclick = close;
        overlay.onclick = function(e) {
            if (e.target === overlay) close();
        };

        var okBtn = overlay.querySelector('[data-action="ok"]');
        var cancelBtn = overlay.querySelector('[data-action="cancel"]');
        if (okBtn) okBtn.onclick = function() { close(); if (opts.onOk) opts.onOk(); };
        if (cancelBtn) cancelBtn.onclick = function() { close(); if (opts.onCancel) opts.onCancel(); };

        doc.addEventListener('keydown', escHandler);

        function close() {
            overlay.remove();
            doc.removeEventListener('keydown', escHandler);
        }
        function escHandler(e) {
            if (e.key === 'Escape') close();
        }
    };

    // === 实时表单验证 ===
    var validators = {
        username: function(v) {
            if (!v) return { ok: false, msg: '请输入用户名' };
            if (v.length < 3) return { ok: false, msg: '用户名至少3个字符' };
            if (v.length > 20) return { ok: false, msg: '用户名最多20个字符' };
            if (!/^[a-zA-Z0-9_\u4e00-\u9fa5]+$/.test(v)) return { ok: false, msg: '只能含字母数字下划线中文' };
            return { ok: true, msg: '用户名可用' };
        },
        password: function(v) {
            if (!v) return { ok: false, msg: '请输入密码' };
            if (v.length < 6) return { ok: false, msg: '密码至少6位' };
            return { ok: true, msg: '密码有效' };
        },
        email: function(v) {
            if (!v) return { ok: true, msg: '' };
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return { ok: false, msg: '邮箱格式不正确' };
            return { ok: true, msg: '邮箱格式正确' };
        },
        confirm_password: function(v) {
            var pwd = doc.querySelector('input[name="password"], input[name="new_password"]');
            if (!pwd) return { ok: true, msg: '' };
            if (v !== pwd.value) return { ok: false, msg: '两次密码不一致' };
            return { ok: true, msg: '密码一致' };
        },
        title: function(v) {
            if (!v || v.length < 2) return { ok: false, msg: '标题至少2个字符' };
            return { ok: true, msg: '标题有效' };
        },
        content: function(v) {
            if (!v || v.length < 10) return { ok: false, msg: '内容至少10个字符' };
            return { ok: true, msg: '内容有效' };
        },
        phone: function(v) {
            if (!v) return { ok: true, msg: '' };
            if (!/^1[3-9]\d{9}$/.test(v)) return { ok: false, msg: '手机号格式不正确' };
            return { ok: true, msg: '手机号正确' };
        }
    };

    // === 初始化 ===
    ready(function() {

        // 移动端菜单
        var mobileToggle = doc.getElementById('mobileToggle');
        var mobileMenu = doc.getElementById('mobileMenu');
        var mobileOverlay = doc.getElementById('mobileOverlay');
        var mobileClose = doc.getElementById('mobileClose');

        if (mobileToggle && mobileMenu) {
            mobileToggle.onclick = function() {
                mobileMenu.classList.add('open');
                if (mobileOverlay) mobileOverlay.style.display = 'block';
            };
        }
        if (mobileClose) {
            mobileClose.onclick = closeMobile;
        }
        if (mobileOverlay) {
            mobileOverlay.onclick = closeMobile;
        }
        function closeMobile() {
            if (mobileMenu) mobileMenu.classList.remove('open');
            if (mobileOverlay) mobileOverlay.style.display = 'none';
        }

        // 轮播图
        var slider = doc.querySelector('.slider-container');
        if (slider) {
            var wrapper = slider.querySelector('.slider-wrapper');
            var items = slider.querySelectorAll('.slide-item');
            var dots = slider.querySelectorAll('.slider-dots .dot');
            var progressBar = slider.querySelector('.progress-bar');
            var prevBtn = slider.querySelector('.slider-prev');
            var nextBtn = slider.querySelector('.slider-next');

            var total = items.length;
            var current = 0;
            var timer = null;
            var duration = 5000;
            var startTime = 0;

            if (total > 0) {
                function goTo(index) {
                    current = index;
                    if (current >= total) current = 0;
                    if (current < 0) current = total - 1;
                    wrapper.style.transform = 'translateX(-' + (current * 100) + '%)';
                    dots.forEach(function(d, i) {
                        d.classList.toggle('active', i === current);
                    });
                    resetProgress();
                }

                function resetProgress() {
                    startTime = Date.now();
                    progressBar.style.transition = 'none';
                    progressBar.style.width = '0';
                    setTimeout(function() {
                        progressBar.style.transition = 'width ' + duration + 'ms linear';
                        progressBar.style.width = '100%';
                    }, 20);
                }

                function next() { goTo(current + 1); }
                function prev() { goTo(current - 1); }

                timer = setInterval(next, duration);

                slider.onmouseenter = function() { clearInterval(timer); };
                slider.onmouseleave = function() { timer = setInterval(next, duration); };

                if (prevBtn) prevBtn.onclick = prev;
                if (nextBtn) nextBtn.onclick = next;

                dots.forEach(function(dot, i) {
                    dot.onclick = function() { goTo(i); };
                });

                goTo(0);
            }
        }

        // 表单验证
        doc.querySelectorAll('input[data-validate], textarea[data-validate]').forEach(function(input) {
            var tip = input.parentNode.querySelector('.field-tip');
            if (!tip) {
                tip = doc.createElement('div');
                tip.className = 'field-tip';
                input.parentNode.appendChild(tip);
            }

            input.addEventListener('input', function() {
                var type = input.getAttribute('data-validate');
                var validator = validators[type];
                if (!validator) return;

                var result = validator(input.value);
                input.classList.remove('valid', 'invalid');
                tip.classList.remove('error', 'success');

                if (input.value.length > 0) {
                    if (result.ok) {
                        input.classList.add('valid');
                        tip.classList.add('success');
                        tip.textContent = result.msg;
                    } else {
                        input.classList.add('invalid');
                        tip.classList.add('error');
                        tip.textContent = result.msg;
                    }
                } else {
                    tip.textContent = '';
                }

                // 密码强度检测
                if (type === 'password') {
                    var strengthEl = input.parentNode.querySelector('.password-strength');
                    if (strengthEl) {
                        var pwd = input.value;
                        strengthEl.classList.remove('weak', 'medium', 'strong');
                        if (pwd.length >= 6) {
                            var score = 0;
                            if (pwd.length >= 8) score++;
                            if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
                            if (/\d/.test(pwd)) score++;
                            if (/[^a-zA-Z0-9]/.test(pwd)) score++;
                            if (score <= 1) strengthEl.classList.add('weak');
                            else if (score <= 2) strengthEl.classList.add('medium');
                            else strengthEl.classList.add('strong');
                        }
                    }
                }
            });

            // 确认密码需要监听原密码变化
            if (input.getAttribute('data-validate') === 'confirm_password') {
                var pwdInput = doc.querySelector('input[name="password"]');
                if (pwdInput) {
                    pwdInput.addEventListener('input', function() {
                        input.dispatchEvent(new Event('input'));
                    });
                }
            }
        });

        // 返回顶部
        var scrollTop = doc.createElement('button');
        scrollTop.className = 'scroll-top';
        scrollTop.textContent = '↑';
        scrollTop.onclick = function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        };
        doc.body.appendChild(scrollTop);

        window.addEventListener('scroll', function() {
            scrollTop.classList.toggle('visible', window.pageYOffset > 300);
        });

    });

})();