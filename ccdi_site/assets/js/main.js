/**
 * 主脚本 v4.0.0
 * 中央纪委国家监委网站
 */
(function() {
    'use strict';

    var D = document;
    var W = window;

    /* ================================================================
       DOMContentLoaded — 初始化所有模块
       ================================================================ */
    D.addEventListener('DOMContentLoaded', function() {
        initLoader();
        initMobileMenu();
        initCarousel();
        initBackToTop();
        initPopup();
        initFormValidation();
    });

    /* ================================================================
       1. 加载动画 (.page-loader)
       ================================================================ */
    function initLoader() {
        var loader = D.getElementById('pageLoader');
        if (!loader) return;
        W.addEventListener('load', function() {
            setTimeout(function() {
                loader.classList.add('page-loader--hidden');
            }, 600);
        });
    }

    /* ================================================================
       2. Toast 全局提示系统
       ================================================================ */
    W.showToast = function(msg, type) {
        type = type || 'info';
        var container = D.getElementById('toastContainer');
        if (!container) return;

        var icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        var toast = D.createElement('div');
        toast.className = 'toast toast--' + type;
        toast.innerHTML = '<span class="toast__icon"><i class="fas ' + (icons[type] || icons.info) + '"></i></span><span class="toast__content">' + msg + '</span>';
        container.appendChild(toast);

        // 3.5s 后自动移除（带滑出动画）
        setTimeout(function() {
            toast.classList.add('toast--removing');
            toast.addEventListener('transitionend', function() {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, { once: true });
        }, 3500);
    };

    /* ================================================================
       3. 手机端汉堡菜单
       ================================================================ */
    function initMobileMenu() {
        var hamburger = D.getElementById('hamburgerBtn');
        var sidebar   = D.getElementById('mobileSidebar');
        var overlay   = D.getElementById('mobileOverlay');
        var closeBtn  = D.getElementById('mobileSidebarClose');
        if (!hamburger || !sidebar) return;

        function openMenu() {
            sidebar.classList.add('mobile-sidebar--active');
            if (overlay) overlay.classList.add('mobile-overlay--active');
            hamburger.classList.add('hamburger--active');
            D.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            sidebar.classList.remove('mobile-sidebar--active');
            if (overlay) overlay.classList.remove('mobile-overlay--active');
            hamburger.classList.remove('hamburger--active');
            D.body.style.overflow = '';
        }

        hamburger.addEventListener('click', function() {
            sidebar.classList.contains('mobile-sidebar--active') ? closeMenu() : openMenu();
        });

        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (overlay) overlay.addEventListener('click', closeMenu);

        D.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('mobile-sidebar--active')) {
                closeMenu();
            }
        });
    }

    /* ================================================================
       4. 轮播图 v4.0 — 淡入淡出 + 进度条 + 触摸滑动
       ================================================================ */
    function initCarousel() {
        var carousel = D.getElementById('carousel');
        if (!carousel) return;

        var slides = carousel.querySelectorAll('.carousel-slide');
        var total = slides.length;
        if (total <= 1) return;

        var current     = 0;
        var counterEl   = carousel.querySelector('.carousel-counter .current');
        var progressFill = D.getElementById('carouselProgressFill');
        var prevBtn     = D.getElementById('carouselPrev');
        var nextBtn     = D.getElementById('carouselNext');
        var timer       = null;
        var progressTimer = null;
        var autoDuration = 3200;   // 3.2s 自动切换
        var progressDuration = 3000; // 3s 进度条走完

        /* ---- 切换幻灯片 ---- */
        function goTo(idx) {
            if (idx === current) return;
            slides[current].classList.remove('active');
            current = idx;
            slides[current].classList.add('active');
            if (counterEl) counterEl.textContent = current + 1;
            resetProgress();
            resetTimer();
        }

        function next() { goTo((current + 1) % total); }
        function prev() { goTo((current - 1 + total) % total); }

        /* ---- 进度条 ---- */
        function resetProgress() {
            if (!progressFill) return;
            clearTimeout(progressTimer);
            // 重置 → 立即归零
            progressFill.style.transition = 'none';
            progressFill.style.width = '0';
            // 强制回流后启动线性过渡
            progressFill.offsetWidth; // eslint-disable-line no-unused-expressions
            progressFill.style.transition = 'width ' + progressDuration + 'ms linear';
            progressFill.style.width = '100%';
        }

        /* ---- 自动轮播 ---- */
        function resetTimer() {
            clearTimeout(timer);
            timer = setTimeout(function() {
                next();
            }, autoDuration);
        }

        // 前后按钮
        if (prevBtn) prevBtn.addEventListener('click', function(e) { e.preventDefault(); prev(); });
        if (nextBtn) nextBtn.addEventListener('click', function(e) { e.preventDefault(); next(); });

        /* ---- 触摸滑动 ---- */
        var touchStartX = 0;
        carousel.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        carousel.addEventListener('touchend', function(e) {
            var diff = touchStartX - e.changedTouches[0].screenX;
            if (Math.abs(diff) > 40) {
                diff > 0 ? next() : prev();
            }
        });

        // 启动
        resetTimer();
        resetProgress();
    }

    /* ================================================================
       5. 返回顶部
       ================================================================ */
    function initBackToTop() {
        var btn = D.getElementById('backToTop');
        if (!btn) return;

        function toggle() {
            btn.classList.toggle('back-to-top--visible', W.pageYOffset > 300);
        }

        W.addEventListener('scroll', toggle, { passive: true });
        btn.addEventListener('click', function() {
            W.scrollTo({ top: 0, behavior: 'smooth' });
        });
        toggle();
    }

    /* ================================================================
       6. B2弹窗
       ================================================================ */
    function initPopup() {
        var overlay = D.getElementById('popupOverlay');
        if (!overlay) return;

        var closeBtn = D.getElementById('popupClose');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                overlay.classList.remove('popup-overlay--active');
            });
        }

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.classList.remove('popup-overlay--active');
            }
        });
    }

    /* ================================================================
       7. 表单验证（data-validate 属性）
       ================================================================ */
    function initFormValidation() {
        var inputs = D.querySelectorAll('input[data-validate], textarea[data-validate]');
        if (!inputs.length) return;

        var validators = {
            required: function(val) {
                return val.trim().length > 0 ? '' : '此项为必填';
            },
            email: function(val) {
                if (!val.trim()) return '';
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) ? '' : '请输入有效的邮箱地址';
            },
            username: function(val) {
                if (!val.trim()) return '';
                return /^[a-zA-Z0-9_\u4e00-\u9fa5]{3,20}$/.test(val) ? '' : '用户名需3-20位字母、数字、下划线或中文';
            },
            password: function(val) {
                if (!val.trim()) return '';
                return val.length >= 6 ? '' : '密码长度不能少于6位';
            }
        };

        function showFeedback(input, message) {
            // 移除旧反馈
            var existing = input.parentNode.querySelector('.form-feedback--error');
            if (existing) existing.parentNode.removeChild(existing);

            input.classList.remove('form-input--error', 'form-input--success');

            if (message) {
                input.classList.add('form-input--error');
                var feedback = D.createElement('div');
                feedback.className = 'form-feedback form-feedback--error';
                feedback.textContent = message;
                input.parentNode.appendChild(feedback);
            } else {
                input.classList.add('form-input--success');
            }
        }

        function validateInput(input) {
            var rules = input.getAttribute('data-validate').split(/\s+/);
            var val = input.value;
            var firstError = '';

            for (var i = 0; i < rules.length; i++) {
                var rule = rules[i];
                var validator = validators[rule];
                if (validator) {
                    var error = validator(val);
                    if (error) {
                        firstError = error;
                        break;
                    }
                }
            }

            showFeedback(input, firstError);
            return !firstError;
        }

        // 失焦校验
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].addEventListener('blur', function() {
                validateInput(this);
            });

            inputs[i].addEventListener('input', function() {
                // 如果已有错误状态，实时清除
                if (this.classList.contains('form-input--error')) {
                    validateInput(this);
                }
            });
        }

        // 表单提交时校验全部
        for (var j = 0; j < inputs.length; j++) {
            var form = inputs[j].closest('form');
            if (form && !form._validationBound) {
                form._validationBound = true;
                form.addEventListener('submit', function(e) {
                    var allValid = true;
                    var formInputs = this.querySelectorAll('input[data-validate], textarea[data-validate]');
                    for (var k = 0; k < formInputs.length; k++) {
                        if (!validateInput(formInputs[k])) {
                            allValid = false;
                        }
                    }
                    if (!allValid) {
                        e.preventDefault();
                    }
                });
            }
        }
    }

})();