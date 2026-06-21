/**
 * 语云科技企业官网 - 主脚本
 * 核心交互逻辑
 */

(function() {
    'use strict';

    // ============================================
    // DOM Ready
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        initNavbar();
        initHamburger();
        initScrollAnimations();
        initCounters();
        initLazyLoad();
        initSmoothScroll();
    });

    // ============================================
    // 导航栏滚动效果
    // ============================================
    function initNavbar() {
        const navbar = document.querySelector('.navbar');
        if (!navbar) return;

        let lastScroll = 0;
        const scrollThreshold = 50;

        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;

            if (currentScroll > scrollThreshold) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        }, { passive: true });
    }

    // ============================================
    // 汉堡菜单
    // ============================================
    function initHamburger() {
        const hamburger = document.querySelector('.hamburger');
        const mobileMenu = document.querySelector('.mobile-menu');
        const overlay = document.querySelector('.mobile-menu-overlay');

        if (!hamburger || !mobileMenu) return;

        function toggleMenu() {
            const isOpen = hamburger.classList.contains('active');

            hamburger.classList.toggle('active');
            mobileMenu.classList.toggle('show');
            overlay?.classList.toggle('show');
            document.body.style.overflow = isOpen ? '' : 'hidden';
        }

        function closeMenu() {
            hamburger.classList.remove('active');
            mobileMenu.classList.remove('show');
            overlay?.classList.remove('show');
            document.body.style.overflow = '';
        }

        hamburger.addEventListener('click', toggleMenu);
        overlay?.addEventListener('click', closeMenu);

        // ESC关闭
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeMenu();
        });

        // 点击菜单链接后关闭
        mobileMenu.querySelectorAll('.nav-link').forEach(function(link) {
            link.addEventListener('click', closeMenu);
        });
    }

    // ============================================
    // 滚动动画 (AOS风格)
    // ============================================
    function initScrollAnimations() {
        const elements = document.querySelectorAll('[data-animate]');
        if (!elements.length) return;

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        elements.forEach(function(el) {
            observer.observe(el);
        });
    }

    // ============================================
    // 数字计数动画
    // ============================================
    function initCounters() {
        const counters = document.querySelectorAll('[data-count]');
        if (!counters.length) return;

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(function(counter) {
            observer.observe(counter);
        });
    }

    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'), 10);
        const suffix = element.getAttribute('data-suffix') || '';
        const prefix = element.getAttribute('data-prefix') || '';
        const duration = parseInt(element.getAttribute('data-duration'), 10) || 2000;
        const start = 0;
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            // 缓动函数 - easeOutExpo
            const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
            const current = Math.floor(start + (target - start) * easeProgress);

            element.textContent = prefix + current.toLocaleString() + suffix;

            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }

        requestAnimationFrame(update);
    }

    // ============================================
    // 图片懒加载
    // ============================================
    function initLazyLoad() {
        const images = document.querySelectorAll('img[data-src]');
        if (!images.length) return;
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.getAttribute('data-src');
                        img.removeAttribute('data-src');
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                });
            }, { rootMargin: '100px' });

            images.forEach(function(img) {
                imageObserver.observe(img);
            });
        } else {
            // 降级处理
            images.forEach(function(img) {
                img.src = img.getAttribute('data-src');
            });
        }
    }

    // ============================================
    // 平滑滚动
    // ============================================
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    const navHeight = document.querySelector('.navbar')?.offsetHeight || 72;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navHeight;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // ============================================
    // Toast通知系统
    // ============================================
    window.showToast = function(message, type, duration) {
        type = type || 'info';
        duration = duration || 3000;

        var container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        var icons = {
            success: '&#10003;',
            error: '&#10007;',
            warning: '&#9888;',
            info: '&#8505;'
        };

        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.innerHTML =
            '<span class="toast-icon">' + icons[type] + '</span>' +
            '<span class="toast-message">' + message + '</span>' +
            '<span class="toast-close">&times;</span>';

        container.appendChild(toast);

        // 关闭按钮
        toast.querySelector('.toast-close').addEventListener('click', function() {
            removeToast(toast);
        });

        // 自动关闭
        setTimeout(function() {
            removeToast(toast);
        }, duration);
    };

    function removeToast(toast) {
        toast.style.animation = 'toastOut 0.3s ease forwards';
        setTimeout(function() {
            toast.remove();
        }, 300);
    }

    // ============================================
    // AJAX请求封装
    // ============================================
    window.ajax = function(options) {
        options = options || {};
        var url = options.url;
        var method = (options.method || 'GET').toUpperCase();
        var data = options.data || null;
        var success = options.success || function() {};
        var error = options.error || function() {};
        var headers = options.headers || {};

        var xhr = new XMLHttpRequest();
        xhr.open(method, url, true);

        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        for (var key in headers) {
            xhr.setRequestHeader(key, headers[key]);
        }

        if (method === 'POST' && !(data instanceof FormData)) {
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            data = Object.keys(data).map(function(k) {
                return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]);
            }).join('&');
        }

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        success(response);
                    } catch (e) {
                        success({ code: 200, data: xhr.responseText });
                    }
                } else {
                    error(xhr.status, xhr.statusText);
                }
            }
        };

        xhr.send(data);
    };

    // ============================================
    // 表单验证工具
    // ============================================
    window.validateForm = function(formElement) {
        var isValid = true;
        var requiredFields = formElement.querySelectorAll('[required]');

        requiredFields.forEach(function(field) {
            clearFieldError(field);

            if (!field.value.trim()) {
                showFieldError(field, '此字段为必填项');
                isValid = false;
                return;
            }

            // 邮箱验证
            if (field.type === 'email' && field.value) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value)) {
                    showFieldError(field, '请输入有效的邮箱地址');
                    isValid = false;
                    return;
                }
            }

            // 密码长度验证
            if (field.type === 'password' && field.minLength && field.value.length < field.minLength) {
                showFieldError(field, '密码至少需要' + field.minLength + '位字符');
                isValid = false;
                return;
            }
        });

        return isValid;
    };

    function showFieldError(field, message) {
        field.classList.add('error');
        var errorEl = document.createElement('div');
        errorEl.className = 'form-error';
        errorEl.textContent = message;
        field.parentNode.appendChild(errorEl);
    }

    function clearFieldError(field) {
        field.classList.remove('error');
        var existing = field.parentNode.querySelector('.form-error');
        if (existing) existing.remove();
    }

    // ============================================
    // 倒计时/验证码计时器
    // ============================================
    window.startCountdown = function(button, seconds, onComplete) {
        button.disabled = true;
        var originalText = button.textContent;
        var remaining = seconds;

        var timer = setInterval(function() {
            button.textContent = remaining + '秒后重发';
            remaining--;

            if (remaining < 0) {
                clearInterval(timer);
                button.disabled = false;
                button.textContent = originalText;
                if (onComplete) onComplete();
            }
        }, 1000);

        return timer;
    };

    // ============================================
    // 工具函数
    // ============================================
    window.debounce = function(func, wait) {
        var timeout;
        return function() {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    };

    window.throttle = function(func, limit) {
        var inThrottle;
        return function() {
            var args = arguments;
            var context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function() {
                    inThrottle = false;
                }, limit);
            }
        };
    };

})();
