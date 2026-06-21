document.addEventListener('DOMContentLoaded', function () {
    // Hamburger
    const hamburger = document.getElementById('hamburger');
    const mainNav = document.getElementById('mainNav');
    const hamIcon = hamburger ? hamburger.querySelector('i') : null;
    if (hamburger && mainNav) {
        hamburger.addEventListener('click', function () {
            mainNav.classList.toggle('open');
            if (hamIcon) {
                hamIcon.className = mainNav.classList.contains('open') ? 'iconfont icon-close' : 'iconfont icon-menu';
            }
        });
    }

    // Theme toggle
    const themeBtn = document.getElementById('themeToggle');
    if (themeBtn) {
        const updateThemeIcon = function () {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            themeBtn.innerHTML = '<i class="iconfont icon-' + (isDark ? 'sun' : 'moon') + '"></i>';
        };
        updateThemeIcon();
        themeBtn.addEventListener('click', function () {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const next = isDark ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('yy_theme', next);
            updateThemeIcon();
        });
    }

    // Language toggle
    const langBtn = document.getElementById('langToggle');
    if (langBtn) {
        const currentLang = document.documentElement.lang || 'zh';
        langBtn.innerHTML = '<i class="iconfont icon-translate"></i><span style="font-size:11px;margin-left:2px">' + (currentLang === 'zh' ? 'EN' : '中') + '</span>';
        langBtn.addEventListener('click', function () {
            const nextLang = currentLang === 'zh' ? 'en' : 'zh';
            const url = new URL(window.location.href);
            url.searchParams.set('lang', nextLang);
            window.location.href = url.toString();
        });
    }

    // Pause banner on hover
    const bannerScroll = document.querySelector('.top-banner-scroll');
    if (bannerScroll) {
        bannerScroll.addEventListener('mouseenter', function () {
            const span = this.querySelector('span');
            if (span) span.style.animationPlayState = 'paused';
        });
        bannerScroll.addEventListener('mouseleave', function () {
            const span = this.querySelector('span');
            if (span) span.style.animationPlayState = 'running';
        });
    }

    // Hero slider
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    if (slides.length > 0) {
        let current = 0;
        const show = (idx) => {
            slides.forEach((s, i) => {
                s.classList.toggle('active', i === idx);
            });
            dots.forEach((d, i) => {
                d.classList.toggle('active', i === idx);
            });
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
            this.closest('.modal-overlay').classList.remove('active');
        });
    });

    // Product detail modal injection
    window.openProductModal = function (title, detail) {
        document.getElementById('modalProductTitle').textContent = title;
        document.getElementById('modalProductBody').innerHTML = detail;
        document.getElementById('productModal').classList.add('active');
    };

    // Image modal
    window.openImageModal = function (src, title) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('modalImageSrc');
        const tip = document.getElementById('modalImageTip');
        if (!src) {
            tip.textContent = '暂无图片，请前往后台上传资质证照。';
            img.style.display = 'none';
        } else {
            img.src = src;
            img.alt = title;
            img.style.display = 'inline-block';
            tip.textContent = '点击遮罩关闭';
        }
        document.getElementById('modalImageTitle').textContent = title || '证照预览';
        if (modal) modal.classList.add('active');
    };
    window.closeImageModal = function () {
        const modal = document.getElementById('imageModal');
        if (modal) modal.classList.remove('active');
    };

    // 3D mascot mouse tracking
    const mascot = document.getElementById('heroMascot');
    const mascotWrap = mascot ? mascot.querySelector('.mascot-wrap') : null;
    if (mascot && mascotWrap) {
        document.addEventListener('mousemove', function (e) {
            const cx = window.innerWidth / 2;
            const cy = window.innerHeight / 2;
            const dx = (e.clientX - cx) / cx;
            const dy = (e.clientY - cy) / cy;
            mascotWrap.style.transform = 'rotateY(' + (dx * 25) + 'deg) rotateX(' + (-dy * 20) + 'deg)';
        });
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

    // Toast helper
    window.showToast = function (message, type) {
        type = type || 'info';
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.innerHTML = '<i class="iconfont icon-' + (type === 'success' ? 'certificate' : type === 'error' ? 'close' : 'bell') + '"></i><span>' + message + '</span>';
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    };

    // Convert flash messages to toasts
    const flashEl = document.querySelector('.flash-message');
    if (flashEl) {
        const type = flashEl.classList.contains('flash-error') ? 'error' : 'success';
        showToast(flashEl.textContent.trim(), type);
        flashEl.style.display = 'none';
    }
});
