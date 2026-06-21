/* ==========================================================================
   语云科技官网 - 主JS
   ========================================================================== */

(function() {
    'use strict';

    // ===== 头部滚动效果 =====
    const header = document.querySelector('.header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 20) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // ===== 汉堡菜单 =====
    const hamburger = document.querySelector('.hamburger');
    const mobileMenu = document.querySelector('.mobile-menu');
    const menuOverlay = document.querySelector('.menu-overlay');
    const closeBtn = document.querySelector('.mobile-menu-close');

    function toggleMenu(show) {
        if (!mobileMenu) return;
        const shouldShow = typeof show === 'boolean' ? show : !mobileMenu.classList.contains('active');
        mobileMenu.classList.toggle('active', shouldShow);
        if (menuOverlay) menuOverlay.classList.toggle('active', shouldShow);
        if (hamburger) hamburger.classList.toggle('active', shouldShow);
        document.body.style.overflow = shouldShow ? 'hidden' : '';
    }

    if (hamburger) {
        hamburger.addEventListener('click', function() { toggleMenu(); });
    }
    if (closeBtn) closeBtn.addEventListener('click', function() { toggleMenu(false); });
    if (menuOverlay) menuOverlay.addEventListener('click', function() { toggleMenu(false); });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            toggleMenu(false);
            closeModal();
        }
    });

    // ===== 轮播图 =====
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        if (!slides.length) return;
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        currentSlide = index;
        slides.forEach(function(s, i) { s.classList.toggle('active', i === currentSlide); });
        dots.forEach(function(d, i) { d.classList.toggle('active', i === currentSlide); });
    }

    function nextSlide() { showSlide(currentSlide + 1); }
    function prevSlide() { showSlide(currentSlide - 1); }

    function startSlider() {
        if (!slides.length) return;
        slideInterval = setInterval(nextSlide, 6000);
    }
    function stopSlider() { clearInterval(slideInterval); }

    if (slides.length) {
        dots.forEach(function(dot, i) {
            dot.addEventListener('click', function() {
                stopSlider();
                showSlide(i);
                startSlider();
            });
        });
        const prevBtn = document.querySelector('.slider-arrow.prev');
        const nextBtn = document.querySelector('.slider-arrow.next');
        if (prevBtn) prevBtn.addEventListener('click', function() { stopSlider(); prevSlide(); startSlider(); });
        if (nextBtn) nextBtn.addEventListener('click', function() { stopSlider(); nextSlide(); startSlider(); });
        showSlide(0);
        startSlider();
    }

    // ===== 弹窗系统 =====
    window.showModal = function(options) {
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.innerHTML =
            '<div class="modal">' +
                '<div class="modal-header">' +
                    '<h3>' + (options.title || '提示') + '</h3>' +
                    '<button class="modal-close" aria-label="关闭">&times;</button>' +
                '</div>' +
                '<div class="modal-body">' + (options.content || '') + '</div>' +
                (options.showFooter !== false ? (
                    '<div class="modal-footer">' +
                        '<button class="btn btn-ghost modal-cancel">' + (options.cancelText || '取消') + '</button>' +
                        '<button class="btn btn-primary modal-confirm">' + (options.confirmText || '确定') + '</button>' +
                    '</div>'
                ) : '') +
            '</div>';
        document.body.appendChild(overlay);
        requestAnimationFrame(function() { overlay.classList.add('active'); });
        document.body.style.overflow = 'hidden';

        function close() {
            overlay.classList.remove('active');
            setTimeout(function() { overlay.remove(); document.body.style.overflow = ''; }, 300);
        }

        overlay.querySelector('.modal-close').addEventListener('click', close);
        const cancel = overlay.querySelector('.modal-cancel');
        const confirm = overlay.querySelector('.modal-confirm');
        if (cancel) cancel.addEventListener('click', function() {
            if (typeof options.onCancel === 'function') options.onCancel();
            close();
        });
        if (confirm) confirm.addEventListener('click', function() {
            if (typeof options.onConfirm === 'function') options.onConfirm();
            if (options.closeOnConfirm !== false) close();
        });
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) close();
        });

        return { close: close };
    };

    window.closeModal = function() {
        const active = document.querySelectorAll('.modal-overlay.active');
        active.forEach(function(m) { m.classList.remove('active'); });
    };

    // ===== Toast提示 =====
    window.showToast = function(message, type) {
        type = type || 'info';
        const iconMap = {
            success: 'fa-check-circle',
            warning: 'fa-exclamation-triangle',
            danger: 'fa-times-circle',
            info: 'fa-info-circle'
        };
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.innerHTML = '<i class="fas ' + (iconMap[type] || iconMap.info) + '"></i><span>' + message + '</span>';
        container.appendChild(toast);
        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    };

    // ===== 悬浮客服按钮 =====
    const fcButtons = document.querySelectorAll('.fc-btn[data-modal]');
    fcButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const modalType = btn.getAttribute('data-modal');
            const contentMap = {
                phone: '<div style="text-align:center;padding:10px 0;">' +
                    '<i class="fas fa-phone-alt" style="font-size:48px;color:#ff6b35;"></i>' +
                    '<h3 style="margin:16px 0 8px;">销售热线</h3>' +
                    '<p style="font-size:28px;font-weight:700;color:#ff6b35;">400-800-8541</p>' +
                    '<p style="color:#6a6a8a;font-size:14px;margin-top:12px;">7x24小时专业服务</p>' +
                    '</div>',
                qq: '<div style="text-align:center;padding:10px 0;">' +
                    '<i class="fab fa-qq" style="font-size:48px;color:#1a73e8;"></i>' +
                    '<h3 style="margin:16px 0 8px;">QQ咨询</h3>' +
                    '<p style="font-size:22px;font-weight:700;color:#1a73e8;">800888888</p>' +
                    '<p style="color:#6a6a8a;font-size:14px;margin-top:12px;">点击添加专属顾问</p>' +
                    '</div>',
                wechat: '<div style="text-align:center;padding:10px 0;">' +
                    '<div style="width:160px;height:160px;margin:0 auto;background:linear-gradient(135deg,#f5f5f5,#e0e0e0);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#999;font-size:14px;">微信二维码</div>' +
                    '<h3 style="margin:16px 0 8px;">微信咨询</h3>' +
                    '<p style="font-size:16px;color:#00a86b;font-weight:600;">yuyun_tech</p>' +
                    '</div>',
                feedback: '<form onsubmit="event.preventDefault();showToast(\'感谢您的反馈！\',\'success\');closeModal();">' +
                    '<div class="form-group"><label class="form-label">您的姓名<span class="required">*</span></label><input type="text" class="form-input" required></div>' +
                    '<div class="form-group"><label class="form-label">联系方式<span class="required">*</span></label><input type="text" class="form-input" required></div>' +
                    '<div class="form-group"><label class="form-label">反馈内容<span class="required">*</span></label><textarea class="form-textarea" required></textarea></div>' +
                    '</form>'
            };
            showModal({
                title: btn.textContent.trim(),
                content: contentMap[modalType] || '',
                showFooter: modalType === 'feedback'
            });
        });
    });

    // ===== 返回顶部 =====
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // ===== 滚动淡入动画 =====
    const fadeElements = document.querySelectorAll('.fade-in');
    if ('IntersectionObserver' in window && fadeElements.length) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        fadeElements.forEach(function(el) { observer.observe(el); });
    } else {
        fadeElements.forEach(function(el) { el.classList.add('visible'); });
    }

    // ===== 数字计数动画 =====
    const statNums = document.querySelectorAll('.stat-number[data-count]');
    if ('IntersectionObserver' in window && statNums.length) {
        const counterObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseFloat(el.getAttribute('data-count'));
                    const unit = el.getAttribute('data-unit') || '';
                    const duration = 2000;
                    const start = performance.now();
                    function step(now) {
                        const progress = Math.min((now - start) / duration, 1);
                        const value = Math.floor(target * (1 - Math.pow(1 - progress, 3)));
                        el.innerHTML = value.toLocaleString() + '<span class="unit">' + unit + '</span>';
                        if (progress < 1) requestAnimationFrame(step);
                    }
                    requestAnimationFrame(step);
                    counterObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        statNums.forEach(function(el) { counterObserver.observe(el); });
    }

    // ===== 地图点击事件 =====
    document.querySelectorAll('.map-pin').forEach(function(pin) {
        pin.addEventListener('click', function() {
            const city = pin.getAttribute('data-city');
            const region = pin.getAttribute('data-region');
            showModal({
                title: region + ' - ' + city,
                content: '<div style="text-align:center;padding:20px;"><i class="fas fa-map-marker-alt" style="font-size:48px;color:#ff6b35;"></i><h3 style="margin:16px 0 8px;">' + city + '数据中心</h3><p style="color:#6a6a8a;">位于' + region + '的高可用数据中心<br>为您提供稳定、安全的云服务</p><p style="margin-top:20px;font-size:14px;color:#1a73e8;"><i class="fas fa-shield-alt"></i> 高安全 &nbsp; <i class="fas fa-bolt"></i> 高速度 &nbsp; <i class="fas fa-server"></i> 高可用</p></div>',
                showFooter: false
            });
        });
    });

    // ===== 证书点击预览 =====
    document.querySelectorAll('.cert-card').forEach(function(card) {
        card.addEventListener('click', function() {
            const name = card.querySelector('h4')?.textContent || '资质证书';
            showModal({
                title: name,
                content: '<div style="text-align:center;"><div style="height:300px;background:linear-gradient(135deg,#f5f7fa,#e4e8f0);border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-certificate" style="font-size:80px;color:#ff6b35;opacity:0.6;"></i></div><p style="margin-top:20px;color:#6a6a8a;">语云科技' + name + '<br>官方认证 · 权威机构颁发</p></div>',
                showFooter: false
            });
        });
    });

    // ===== 表单处理 =====
    document.querySelectorAll('form.ajax-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            showToast('提交成功，我们将尽快与您联系！', 'success');
            form.reset();
        });
    });

    // ===== 后台管理汉堡菜单 =====
    const adminHamburger = document.querySelector('.admin-hamburger');
    const adminSidebar = document.querySelector('.admin-sidebar');
    if (adminHamburger && adminSidebar) {
        adminHamburger.addEventListener('click', function() {
            adminSidebar.classList.toggle('active');
        });
    }

})();

// ===== 主题切换 =====
window.toggleTheme = function() {
    document.body.classList.toggle('theme-dark');
    showToast(document.body.classList.contains('theme-dark') ? '已切换到深色主题' : '已切换到浅色主题', 'success');
};
