// ========================================
// 清廉在线 V2.0 - 前端增强脚本
// 实时表单检测 + Toast动画 + 滚动动画 + 交互增强
// ========================================

document.addEventListener('DOMContentLoaded', function() {

    // ===== 移动端汉堡菜单 =====
    var mobileToggle = document.getElementById('mobileToggle');
    var mobileMenu = document.getElementById('mobileMenu');
    var mobileOverlay = document.getElementById('mobileOverlay');
    var mobileClose = document.getElementById('mobileClose');

    function openMobileMenu() {
        if (mobileMenu) mobileMenu.classList.add('open');
        if (mobileOverlay) mobileOverlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function closeMobileMenu() {
        if (mobileMenu) mobileMenu.classList.remove('open');
        if (mobileOverlay) mobileOverlay.style.display = 'none';
        document.body.style.overflow = '';
    }
    if (mobileToggle) mobileToggle.addEventListener('click', openMobileMenu);
    if (mobileClose) mobileClose.addEventListener('click', closeMobileMenu);
    if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileMenu);

    // 移动端子菜单折叠
    var submenuToggles = document.querySelectorAll('.mobile-nav .has-submenu > a');
    submenuToggles.forEach(function(link) {
        link.addEventListener('click', function(e) {
            var submenu = link.nextElementSibling;
            if (submenu && submenu.classList.contains('submenu')) {
                e.preventDefault();
                submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
            }
        });
    });

    // ===== Toast 通知系统 =====
    window.showToast = function(message, type) {
        type = type || 'info';
        var container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        var icons = { success: '✓', error: '✕', warning: '⚠', info: 'ℹ' };
        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.innerHTML = '<span class="toast-icon">' + (icons[type] || 'ℹ') + '</span><span>' + message + '</span>';
        container.appendChild(toast);
        setTimeout(function() { toast.remove(); }, 3000);
    };

    // ===== 实时表单验证 =====
    var validators = {
        username: function(val) {
            if (!val) return { valid: false, msg: '用户名不能为空' };
            if (val.length < 3) return { valid: false, msg: '用户名至少3个字符' };
            if (val.length > 20) return { valid: false, msg: '用户名最多20个字符' };
            if (!/^[a-zA-Z0-9_\u4e00-\u9fa5]+$/.test(val)) return { valid: false, msg: '只能含字母、数字、下划线、中文' };
            return { valid: true, msg: '用户名可用' };
        },
        password: function(val) {
            if (!val) return { valid: false, msg: '密码不能为空' };
            if (val.length < 6) return { valid: false, msg: '密码至少6位' };
            return { valid: true, msg: '密码有效' };
        },
        email: function(val) {
            if (!val) return { valid: true, msg: '' };
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) return { valid: false, msg: '邮箱格式不正确' };
            return { valid: true, msg: '邮箱格式正确' };
        },
        confirm_password: function(val) {
            var original = document.querySelector('input[name="password"], input[name="new_password"]');
            if (!original) return { valid: true, msg: '' };
            if (val !== original.value) return { valid: false, msg: '两次密码不一致' };
            return { valid: true, msg: '密码一致' };
        },
        title: function(val) {
            if (!val || val.trim().length < 2) return { valid: false, msg: '标题至少2个字符' };
            return { valid: true, msg: '标题有效' };
        },
        content: function(val) {
            if (!val || val.trim().length < 10) return { valid: false, msg: '内容至少10个字符' };
            return { valid: true, msg: '内容有效' };
        },
        contact: function(val) {
            if (!val) return { valid: true, msg: '' };
            if (!/^1[3-9]\d{9}$|^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) return { valid: false, msg: '请输入正确的手机号或邮箱' };
            return { valid: true, msg: '联系方式有效' };
        }
    };

    // 为所有带 data-validate 属性的输入框绑定实时验证
    document.querySelectorAll('input[data-validate], textarea[data-validate]').forEach(function(input) {
        var feedback = document.createElement('div');
        feedback.className = 'field-feedback';
        input.parentNode.appendChild(feedback);

        input.addEventListener('input', function() {
            var type = input.getAttribute('data-validate');
            var validator = validators[type];
            if (!validator) return;
            var result = validator(input.value);
            input.classList.remove('valid', 'invalid');
            feedback.classList.remove('show', 'success', 'error');
            if (input.value.length > 0) {
                feedback.classList.add('show');
                if (result.valid) {
                    input.classList.add('valid');
                    feedback.classList.add('success');
                    feedback.innerHTML = '<span>✓</span> ' + result.msg;
                } else {
                    input.classList.add('invalid');
                    feedback.classList.add('error');
                    feedback.innerHTML = '<span>✕</span> ' + result.msg;
                }
            }
        });
    });

    // ===== 密码强度检测 =====
    document.querySelectorAll('input[data-password-strength]').forEach(function(input) {
        var strengthContainer = document.createElement('div');
        strengthContainer.innerHTML = '<div class="password-strength"><div class="bar"></div><div class="bar"></div><div class="bar"></div><div class="bar"></div></div><div class="strength-text" style="font-size:12px;color:#999;"></div>';
        input.parentNode.appendChild(strengthContainer);

        input.addEventListener('input', function() {
            var val = input.value;
            var score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
            if (/\d/.test(val) && /[^a-zA-Z\d]/.test(val)) score++;

            var bars = strengthContainer.querySelectorAll('.bar');
            var text = strengthContainer.querySelector('.strength-text');
            var labels = ['弱', '一般', '中等', '强'];
            var classes = ['weak', 'weak', 'medium', 'strong'];

            bars.forEach(function(bar, i) {
                bar.className = 'bar';
                if (i < score) bar.classList.add('active', classes[score - 1]);
            });
            text.textContent = val ? '密码强度：' + labels[Math.max(0, score - 1)] : '';
            text.style.color = score >= 3 ? '#52c41a' : score >= 2 ? '#faad14' : '#ff4d4f';
        });
    });

    // ===== 表单提交拦截 + Toast =====
    document.querySelectorAll('form[data-toast-form]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var inputs = form.querySelectorAll('input[data-validate], textarea[data-validate]');
            var allValid = true;
            inputs.forEach(function(input) {
                var type = input.getAttribute('data-validate');
                var validator = validators[type];
                if (validator) {
                    var result = validator(input.value);
                    if (!result.valid) {
                        allValid = false;
                        input.classList.add('invalid');
                    }
                }
            });
            if (!allValid) {
                e.preventDefault();
                showToast('请检查表单填写是否正确', 'error');
            }
        });
    });

    // ===== 滚动揭示动画 =====
    var revealElements = document.querySelectorAll('.scroll-reveal');
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    revealElements.forEach(function(el) { observer.observe(el); });

    // ===== 返回顶部按钮 =====
    var scrollTopBtn = document.createElement('button');
    scrollTopBtn.className = 'scroll-top';
    scrollTopBtn.innerHTML = '↑';
    scrollTopBtn.title = '返回顶部';
    document.body.appendChild(scrollTopBtn);

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollTopBtn.classList.add('visible');
        } else {
            scrollTopBtn.classList.remove('visible');
        }
    });
    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ===== 数字滚动动画 =====
    document.querySelectorAll('[data-count]').forEach(function(el) {
        var target = parseInt(el.getAttribute('data-count'));
        var observer2 = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var current = 0;
                    var step = Math.ceil(target / 50);
                    var timer = setInterval(function() {
                        current += step;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        el.textContent = current.toLocaleString();
                    }, 30);
                    observer2.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        observer2.observe(el);
    });

    // ===== 搜索框增强 =====
    var searchInputs = document.querySelectorAll('.search-box input, .mobile-search input');
    searchInputs.forEach(function(input) {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                var form = input.closest('form');
                if (form) form.submit();
            }
        });
    });

    // ===== 页面加载完成动画 =====
    document.body.classList.add('loaded');
});
