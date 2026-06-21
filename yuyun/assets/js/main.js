document.addEventListener('DOMContentLoaded', function () {
    // Hamburger menu
    const hamburger = document.getElementById('hamburger');
    const mainNav = document.getElementById('mainNav');
    if (hamburger && mainNav) {
        hamburger.addEventListener('click', function () {
            mainNav.classList.toggle('open');
            const icon = hamburger.querySelector('.iconfont');
            if (icon) icon.className = 'iconfont icon-' + (mainNav.classList.contains('open') ? 'close' : 'menu');
        });
    }

    // Theme toggle (front)
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            document.documentElement.classList.toggle('dark');
            try {
                localStorage.setItem('yy_theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            } catch (e) {}
        });
    }

    // Banner close
    const banner = document.getElementById('siteBanner');
    const bannerClose = document.getElementById('bannerClose');
    if (banner && bannerClose) {
        bannerClose.addEventListener('click', function () {
            banner.style.maxHeight = banner.offsetHeight + 'px';
            requestAnimationFrame(function () {
                banner.style.transition = 'max-height .35s ease, opacity .35s ease, padding .35s ease';
                banner.style.maxHeight = '0px';
                banner.style.opacity = '0';
                banner.style.overflow = 'hidden';
            });
            try { localStorage.setItem('yy_banner_closed', '1'); } catch (e) {}
        });
        try {
            if (localStorage.getItem('yy_banner_closed') === '1') {
                banner.style.display = 'none';
            }
        } catch (e) {}
    }

    // Hero slider
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    if (slides.length > 0) {
        let current = 0;
        const show = (idx) => {
            slides.forEach((s, i) => s.classList.toggle('active', i === idx));
            dots.forEach((d, i) => d.classList.toggle('active', i === idx));
        };
        dots.forEach((d, i) => d.addEventListener('click', () => { current = i; show(current); }));
        setInterval(() => {
            current = (current + 1) % slides.length;
            show(current);
        }, 5000);
    }

    // Back to top
    const backTop = document.getElementById('backTop');
    if (backTop) {
        backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    // Modals
    document.querySelectorAll('[data-modal]').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-modal');
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('active');
        });
    });
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('active');
        });
    });
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function () {
            const overlay = this.closest('.modal-overlay');
            if (overlay) overlay.classList.remove('active');
        });
    });

    // Product detail modal
    window.openProductModal = function (title, detail) {
        const t = document.getElementById('modalProductTitle');
        const b = document.getElementById('modalProductBody');
        const m = document.getElementById('productModal');
        if (t) t.textContent = title;
        if (b) b.innerHTML = detail;
        if (m) m.classList.add('active');
    };

    // Image modal
    window.openImageModal = function (src, title) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('modalImageSrc');
        const tip = document.getElementById('modalImageTip');
        if (!src) {
            if (tip) tip.textContent = '暂无图片，请前往后台上传资质证照。';
            if (img) img.style.display = 'none';
        } else {
            if (img) { img.src = src; img.alt = title || ''; img.style.display = 'inline-block'; }
            if (tip) tip.textContent = '点击遮罩关闭';
        }
        const t = document.getElementById('modalImageTitle');
        if (t) t.textContent = title || '证照预览';
        if (modal) modal.classList.add('active');
    };
    window.closeImageModal = function () {
        const modal = document.getElementById('imageModal');
        if (modal) modal.classList.remove('active');
    };

    // 3D mascot mouse tracking (smooth)
    const mascot = document.getElementById('heroMascot');
    const mascotWrap = mascot ? mascot.querySelector('.mascot-wrap') : null;
    if (mascot && mascotWrap) {
        let targetX = 0, targetY = 0, currentX = 0, currentY = 0;
        document.addEventListener('mousemove', function (e) {
            const cx = window.innerWidth / 2;
            const cy = window.innerHeight / 2;
            targetX = ((e.clientX - cx) / cx) * 22;
            targetY = -((e.clientY - cy) / cy) * 18;
        });
        function animateMascot() {
            currentX += (targetX - currentX) * 0.12;
            currentY += (targetY - currentY) * 0.12;
            mascotWrap.style.transform = 'rotateY(' + currentX.toFixed(2) + 'deg) rotateX(' + currentY.toFixed(2) + 'deg)';
            requestAnimationFrame(animateMascot);
        }
        animateMascot();
    }

    // Sticky header shadow
    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.style.boxShadow = window.scrollY > 10 ? '0 2px 12px rgba(0,0,0,.08)' : 'none';
        });
    }

    // Welcome popup (once per session)
    const welcome = document.getElementById('welcomeModal');
    if (welcome && !sessionStorage.getItem('yy_welcome_shown')) {
        setTimeout(() => {
            welcome.classList.add('active');
            sessionStorage.setItem('yy_welcome_shown', '1');
        }, 1200);
    }

    // Global top notifier
    window.showGlobalNotify = function (message, type) {
        type = type || 'info';
        const container = document.getElementById('globalNotifier');
        if (!container) return;
        const icons = { success: 'check-circle', error: 'alert-circle', info: 'info' };
        const el = document.createElement('div');
        el.className = 'global-notify ' + type;
        el.innerHTML = '<i class="iconfont icon-' + icons[type] + ' icon"></i><span>' + message + '</span>';
        container.appendChild(el);
        setTimeout(() => { if (el.parentNode) el.parentNode.removeChild(el); }, 4100);
    };

    // Convert flash-data to global notifier / toast
    document.querySelectorAll('.flash-data').forEach(function (el) {
        const type = el.dataset.type || 'info';
        const msg = el.dataset.message || '';
        if (document.getElementById('toastContainer')) {
            if (typeof showToast === 'function') showToast(msg, type);
        } else {
            showGlobalNotify(msg, type);
        }
        el.remove();
    });

    // Toast helper for admin / pages with toastContainer
    window.showToast = function (message, type) {
        type = type || 'info';
        const container = document.getElementById('toastContainer');
        if (!container) {
            showGlobalNotify(message, type);
            return;
        }
        const icons = { success: 'check-circle', error: 'alert-circle', info: 'info' };
        const toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.innerHTML = '<i class="iconfont icon-' + icons[type] + '"></i><span>' + message + '</span>';
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    };

    // Form validation hints
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const required = form.querySelectorAll('[required]');
            for (let i = 0; i < required.length; i++) {
                if (!required[i].value.trim()) {
                    e.preventDefault();
                    const label = required[i].closest('.form-group')?.querySelector('label')?.textContent || required[i].name || '字段';
                    showGlobalNotify((label ? label + ' ' : '') + '不能为空', 'error');
                    required[i].focus();
                    return false;
                }
            }
        });
    });
});
