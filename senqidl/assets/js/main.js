/**
 * senqidl - Main JavaScript
 * 蓝主题 | 现代企业官网交互
 *
 * 功能模块:
 *   1. 汉堡菜单切换 (动画汉堡→X)
 *   2. 移动端菜单开关 + 遮罩层
 *   3. 头部滚动效果 (添加 .scrolled 类)
 *   4. 回到顶部按钮显示/隐藏
 *   5. 锚点平滑滚动
 *   6. 滚动触发动画 (IntersectionObserver → .visible)
 *   7. 触摸设备下拉菜单切换
 *   8. 浮动咨询交互
 *   9. 搜索表单切换
 *  10. 当前页面导航高亮
 *  11. 图片懒加载
 *  12. 表单验证
 *  13. Toast / 通知系统
 *  14. Cookie 同意横幅
 *  15. 统计数字计数器动画
 */

(function () {
    'use strict';

    /* ================================================
     * 1. 汉堡菜单 + 移动端菜单
     * ================================================ */
    var hamburger = document.getElementById('hamburger');
    var mobileMenu = document.getElementById('mobileMenu');
    var mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');
    var mainNav = document.getElementById('mainNav');

    function openMobileMenu() {
        if (!mobileMenu || !mobileMenuBackdrop) return;
        hamburger.classList.add('active');
        mobileMenu.classList.add('open');
        mobileMenuBackdrop.classList.add('active');
        if (mainNav) mainNav.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        if (!mobileMenu || !mobileMenuBackdrop) return;
        hamburger.classList.remove('active');
        mobileMenu.classList.remove('open');
        mobileMenuBackdrop.classList.remove('active');
        if (mainNav) mainNav.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            if (mobileMenu && mobileMenu.classList.contains('open')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
    }

    if (mobileMenuBackdrop) {
        mobileMenuBackdrop.addEventListener('click', closeMobileMenu);
    }

    // ESC 关闭
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeMobileMenu();
            closeSearchForm();
        }
    });

    /* ================================================
     * 2. 下拉菜单 (触摸设备 / 移动端)
     * ================================================ */
    var isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

    document.querySelectorAll('.has-dropdown > a').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (window.innerWidth <= 1024 || isTouchDevice) {
                e.preventDefault();
                var parent = link.parentElement;
                var wasOpen = parent.classList.contains('open');

                // 关闭同级其他下拉
                parent.parentElement.querySelectorAll('.has-dropdown.open').forEach(function (item) {
                    if (item !== parent) item.classList.remove('open');
                });

                if (wasOpen) {
                    parent.classList.remove('open');
                } else {
                    parent.classList.add('open');
                }
            }
        });
    });

    /* ================================================
     * 3. 头部滚动效果
     * ================================================ */
    var siteHeader = document.getElementById('siteHeader');
    var lastScrollY = 0;

    function handleHeaderScroll() {
        var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        if (currentScroll > 50) {
            siteHeader && siteHeader.classList.add('scrolled');
        } else {
            siteHeader && siteHeader.classList.remove('scrolled');
        }

        // 向下滚动隐藏头部 (可选)
        if (currentScroll > lastScrollY && currentScroll > 200) {
            // siteHeader.style.transform = 'translateY(-100%)';
        } else {
            // siteHeader.style.transform = 'translateY(0)';
        }

        lastScrollY = currentScroll;
    }

    /* ================================================
     * 4. 回到顶部
     * ================================================ */
    var backToTop = document.getElementById('backToTop');

    function handleBackToTop() {
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;
        if (backToTop) {
            if (scrollY > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        }
    }

    if (backToTop) {
        backToTop.addEventListener('click', function (e) {
            e.preventDefault();
            scrollToTop();
        });
    }

    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // 合并滚动监听
    var ticking = false;
    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(function () {
                handleHeaderScroll();
                handleBackToTop();
                ticking = false;
            });
            ticking = true;
        }
    });

    // 初始触发
    handleHeaderScroll();
    handleBackToTop();

    /* ================================================
     * 5. 锚点平滑滚动
     * ================================================ */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = anchor.getAttribute('href');
            if (targetId === '#' || targetId.length < 2) return;

            var target = document.querySelector(targetId);
            if (!target) return;

            e.preventDefault();
            var headerOffset = siteHeader ? siteHeader.offsetHeight : 0;
            var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });

            // 移动端菜单关闭
            closeMobileMenu();
        });
    });

    /* ================================================
     * 6. 滚动触发动画 (IntersectionObserver)
     * ================================================ */
    var revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');

    if ('IntersectionObserver' in window && revealElements.length > 0) {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        revealElements.forEach(function (el) {
            revealObserver.observe(el);
        });
    } else {
        // 回退: 直接显示
        revealElements.forEach(function (el) {
            el.classList.add('visible');
        });
    }

    /* ================================================
     * 7. 触摸设备下拉菜单 (已在 #2 处理)
     * ================================================ */

    /* ================================================
     * 8. 浮动咨询交互
     * ================================================ */
    var floatingContact = document.getElementById('floatingContact');
    if (floatingContact) {
        floatingContact.querySelectorAll('.floating-item, a').forEach(function (item) {
            item.addEventListener('click', function (e) {
                var href = item.getAttribute('href');
                if (href && href !== '#' && href.startsWith('#')) {
                    e.preventDefault();
                    var target = document.querySelector(href);
                    if (target) {
                        var headerOffset = siteHeader ? siteHeader.offsetHeight : 0;
                        var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerOffset;
                        window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                    }
                }
            });
        });
    }

    /* ================================================
     * 9. 搜索表单切换
     * ================================================ */
    var searchToggle = document.getElementById('searchToggle');
    var searchForm = document.getElementById('searchForm');

    function openSearchForm() {
        if (!searchForm) return;
        searchForm.classList.add('active');
        var input = searchForm.querySelector('input');
        if (input) setTimeout(function () { input.focus(); }, 200);
    }

    function closeSearchForm() {
        if (!searchForm) return;
        searchForm.classList.remove('active');
    }

    if (searchToggle) {
        searchToggle.addEventListener('click', function (e) {
            e.preventDefault();
            if (searchForm && searchForm.classList.contains('active')) {
                closeSearchForm();
            } else {
                openSearchForm();
            }
        });
    }

    if (searchForm) {
        searchForm.addEventListener('click', function (e) {
            if (e.target === searchForm) closeSearchForm();
        });
    }

    /* ================================================
     * 10. 当前页面导航高亮
     * ================================================ */
    function setActiveNav() {
        var currentPath = window.location.pathname.split('/').pop() || 'index.php';
        var currentPage = document.body.className.match(/page-([\w-]+)/);
        var pageName = currentPage ? currentPage[1] : '';

        document.querySelectorAll('.main-nav a, .nav-link').forEach(function (link) {
            var href = link.getAttribute('href') || '';
            var linkPage = href.split('/').pop();

            if (pageName && href.indexOf(pageName) !== -1) {
                link.classList.add('active');
                link.closest('.has-dropdown, .nav-item') &&
                    link.closest('.has-dropdown, .nav-item').classList.add('active');
            } else if (currentPath === linkPage && currentPath !== '') {
                link.classList.add('active');
            }
        });
    }

    setActiveNav();

    /* ================================================
     * 11. 图片懒加载
     * ================================================ */
    var lazyImages = document.querySelectorAll('img[data-src], img[data-lazy], .lazy-image');

    if ('IntersectionObserver' in window && lazyImages.length > 0) {
        var lazyObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    var src = img.getAttribute('data-src') || img.getAttribute('data-lazy');
                    if (src) {
                        img.src = src;
                        img.removeAttribute('data-src');
                        img.removeAttribute('data-lazy');
                    }
                    img.classList.add('lazy-loaded');
                    lazyObserver.unobserve(img);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });

        lazyImages.forEach(function (img) {
            lazyObserver.observe(img);
        });
    } else {
        // 回退: 直接加载
        lazyImages.forEach(function (img) {
            var src = img.getAttribute('data-src') || img.getAttribute('data-lazy');
            if (src) img.src = src;
        });
    }

    /* ================================================
     * 12. 表单验证
     * ================================================ */
    var forms = document.querySelectorAll('.contact-form, .validate-form, form[data-validate]');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var isValid = true;
            var firstError = null;

            form.querySelectorAll('[required], [data-required]').forEach(function (field) {
                var wrapper = field.closest('.form-group') || field.parentElement;
                var errorEl = wrapper ? wrapper.querySelector('.form-error') : null;
                var value = field.value.trim();

                if (!value) {
                    isValid = false;
                    if (wrapper) wrapper.classList.add('has-error');
                    if (errorEl && !errorEl.textContent) {
                        errorEl.textContent = '此字段为必填项';
                    }
                    if (!firstError) firstError = field;
                } else {
                    if (wrapper) wrapper.classList.remove('has-error');
                }

                // 邮箱验证
                if (field.type === 'email' && value) {
                    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(value)) {
                        isValid = false;
                        if (wrapper) wrapper.classList.add('has-error');
                        if (errorEl) errorEl.textContent = '请输入有效的邮箱地址';
                        if (!firstError) firstError = field;
                    }
                }

                // 电话验证
                if (field.type === 'tel' && value) {
                    var telPattern = /^[\d\s\-+()]{7,20}$/;
                    if (!telPattern.test(value)) {
                        isValid = false;
                        if (wrapper) wrapper.classList.add('has-error');
                        if (errorEl) errorEl.textContent = '请输入有效的电话号码';
                        if (!firstError) firstError = field;
                    }
                }

                // 最小长度
                var minLength = field.getAttribute('data-minlength');
                if (minLength && value && value.length < parseInt(minLength, 10)) {
                    isValid = false;
                    if (wrapper) wrapper.classList.add('has-error');
                    if (errorEl) errorEl.textContent = '最少需要 ' + minLength + ' 个字符';
                    if (!firstError) firstError = field;
                }
            });

            if (!isValid) {
                e.preventDefault();
                if (firstError) {
                    firstError.focus();
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                showToast('请检查表单填写是否正确', 'error');
            }
        });

        // 实时清除错误
        form.querySelectorAll('.form-control').forEach(function (field) {
            field.addEventListener('input', function () {
                var wrapper = field.closest('.form-group') || field.parentElement;
                if (wrapper) wrapper.classList.remove('has-error');
                var errorEl = wrapper ? wrapper.querySelector('.form-error') : null;
                if (errorEl) errorEl.textContent = '';
            });
        });
    });

    /* ================================================
     * 13. Toast / 通知系统
     * ================================================ */
    var toastContainer = null;

    function getToastContainer() {
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.style.cssText = 'position:fixed;top:2rem;right:2rem;z-index:9999;display:flex;flex-direction:column;gap:0.75rem;pointer-events:none;';
            document.body.appendChild(toastContainer);
        }
        return toastContainer;
    }

    function showToast(message, type, duration) {
        type = type || 'info';
        duration = duration || 4000;

        var container = getToastContainer();
        var toast = document.createElement('div');

        var colors = {
            success: { bg: '#28a745', icon: '✓' },
            error:   { bg: '#dc3545', icon: '✕' },
            warning: { bg: '#ffc107', icon: '⚠' },
            info:    { bg: '#1a5fff', icon: 'ℹ' }
        };

        var config = colors[type] || colors.info;

        toast.style.cssText = [
            'pointer-events:auto',
            'min-width:280px',
            'max-width:420px',
            'padding:1rem 1.25rem',
            'background:' + config.bg,
            'color:#ffffff',
            'border-radius:8px',
            'box-shadow:0 8px 30px rgba(0,0,0,0.2)',
            'display:flex',
            'align-items:center',
            'gap:0.75rem',
            'font-size:0.95rem',
            'font-weight:500',
            'transform:translateX(400px)',
            'opacity:0',
            'transition:transform 0.4s cubic-bezier(0.4,0,0.2,1),opacity 0.3s ease'
        ].join(';');

        toast.innerHTML = '<span style="font-size:1.25rem;font-weight:bold;">' + config.icon + '</span>' +
            '<span style="flex:1;">' + message + '</span>';

        container.appendChild(toast);

        // 入场动画
        requestAnimationFrame(function () {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        });

        // 自动消失
        setTimeout(function () {
            toast.style.transform = 'translateX(400px)';
            toast.style.opacity = '0';
            setTimeout(function () {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 400);
        }, duration);

        return toast;
    }

    // 暴露到全局
    window.showToast = showToast;

    /* ================================================
     * 14. Cookie 同意横幅
     * ================================================ */
    var COOKIE_CONSENT_KEY = 'senqidl_cookie_consent';

    function hasCookieConsent() {
        try {
            return localStorage.getItem(COOKIE_CONSENT_KEY) === 'accepted';
        } catch (e) {
            return false;
        }
    }

    function setCookieConsent(value) {
        try {
            localStorage.setItem(COOKIE_CONSENT_KEY, value);
        } catch (e) { /* ignore */ }
    }

    function initCookieConsent() {
        if (hasCookieConsent()) return;

        var banner = document.createElement('div');
        banner.id = 'cookieBanner';
        banner.style.cssText = [
            'position:fixed',
            'bottom:1.5rem',
            'left:50%',
            'transform:translateX(-50%)',
            'max-width:560px',
            'width:calc(100% - 3rem)',
            'padding:1.25rem 1.5rem',
            'background:#ffffff',
            'border-radius:12px',
            'box-shadow:0 16px 50px rgba(0,0,0,0.15)',
            'z-index:9998',
            'display:flex',
            'align-items:center',
            'gap:1rem',
            'flex-wrap:wrap',
            'font-size:0.9rem',
            'color:#333',
            'border:1px solid #e4e9f0'
        ].join(';');

        banner.innerHTML =
            '<div style="flex:1;min-width:200px;">' +
            '本网站使用 Cookie 以提供更好的浏览体验。继续使用即表示您同意我们的 Cookie 政策。' +
            '</div>' +
            '<div style="display:flex;gap:0.5rem;">' +
            '<button type="button" id="cookieDecline" style="padding:0.5rem 1rem;border:1px solid #e4e9f0;border-radius:6px;background:#fff;color:#666;cursor:pointer;font-size:0.85rem;">拒绝</button>' +
            '<button type="button" id="cookieAccept" style="padding:0.5rem 1.25rem;border:none;border-radius:6px;background:linear-gradient(135deg,#1a5fff,#0d47c2);color:#fff;cursor:pointer;font-size:0.85rem;font-weight:600;box-shadow:0 4px 15px rgba(26,95,255,0.35);">同意</button>' +
            '</div>';

        document.body.appendChild(banner);

        function closeBanner() {
            banner.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            banner.style.opacity = '0';
            banner.style.transform = 'translateX(-50%) translateY(20px)';
            setTimeout(function () {
                if (banner.parentNode) banner.parentNode.removeChild(banner);
            }, 300);
        }

        banner.querySelector('#cookieAccept').addEventListener('click', function () {
            setCookieConsent('accepted');
            closeBanner();
        });

        banner.querySelector('#cookieDecline').addEventListener('click', function () {
            setCookieConsent('declined');
            closeBanner();
        });
    }

    // 延迟初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCookieConsent);
    } else {
        initCookieConsent();
    }

    /* ================================================
     * 15. 统计计数器动画
     * ================================================ */
    var statNumbers = document.querySelectorAll('.stat-number, .stat-num, [data-count]');

    function animateCounter(el) {
        var targetValue = el.getAttribute('data-count') || el.textContent.replace(/[^0-9.]/g, '');
        targetValue = parseFloat(targetValue) || 0;

        var suffix = el.querySelector('.suffix') ? el.querySelector('.suffix').outerHTML : '';
        var hasSuffix = el.querySelector('.suffix');

        // 保存原始后缀
        if (hasSuffix) {
            el.setAttribute('data-suffix', hasSuffix.textContent);
        }

        var duration = 2000;
        var startTime = null;
        var startValue = 0;

        function easeOutCubic(t) {
            return 1 - Math.pow(1 - t, 3);
        }

        function updateCounter(currentTime) {
            if (!startTime) startTime = currentTime;
            var elapsed = currentTime - startTime;
            var progress = Math.min(elapsed / duration, 1);
            var easedProgress = easeOutCubic(progress);
            var currentValue = startValue + (targetValue - startValue) * easedProgress;

            if (Number.isInteger(targetValue)) {
                el.textContent = Math.round(currentValue).toLocaleString();
            } else {
                el.textContent = currentValue.toFixed(1);
            }

            if (hasSuffix) {
                var suffixSpan = document.createElement('span');
                suffixSpan.className = 'suffix';
                suffixSpan.textContent = el.getAttribute('data-suffix') || '';
                el.appendChild(suffixSpan);
            }

            if (progress < 1) {
                requestAnimationFrame(updateCounter);
            }
        }

        requestAnimationFrame(updateCounter);
    }

    if ('IntersectionObserver' in window && statNumbers.length > 0) {
        var counterObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });

        statNumbers.forEach(function (el) {
            counterObserver.observe(el);
        });
    } else {
        statNumbers.forEach(animateCounter);
    }

    /* ================================================
     * 工具函数: 节流
     * ================================================ */
    function throttle(fn, delay) {
        var lastCall = 0;
        var timer = null;
        return function () {
            var now = Date.now();
            var remaining = delay - (now - lastCall);
            var args = arguments;
            var context = this;
            if (remaining <= 0) {
                if (timer) {
                    clearTimeout(timer);
                    timer = null;
                }
                lastCall = now;
                fn.apply(context, args);
            } else if (!timer) {
                timer = setTimeout(function () {
                    lastCall = Date.now();
                    timer = null;
                    fn.apply(context, args);
                }, remaining);
            }
        };
    }

})();