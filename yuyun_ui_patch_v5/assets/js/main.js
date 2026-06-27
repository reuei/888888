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

    // Language popup toggle (front)
    const langBtn = document.getElementById('langSwitcherBtn');
    const langPopup = document.getElementById('langPopup');
    if (langBtn && langPopup) {
        langBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            langPopup.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (!langPopup.contains(e.target) && e.target !== langBtn) {
                langPopup.classList.remove('open');
            }
        });
    }

    // Language popup toggle (admin)
    const adminLangBtn = document.getElementById('adminLangSwitcherBtn');
    const adminLangPopup = document.getElementById('adminLangPopup');
    if (adminLangBtn && adminLangPopup) {
        adminLangBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            adminLangPopup.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (!adminLangPopup.contains(e.target) && e.target !== adminLangBtn) {
                adminLangPopup.classList.remove('open');
            }
        });
    }

    // Banner close → collapse to side
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

    // Hero 3D flip carousel with progress bar
    const heroSlides = document.querySelectorAll('.hero-slide-3d');
    const progressBars = document.querySelectorAll('.hero-progress-bar');
    if (heroSlides.length > 0) {
        let current = 0;
        let autoTimer = null;
        const SLIDE_DURATION = 5000; // 5秒

        function showSlide(idx) {
            // 先移除所有active
            heroSlides.forEach(function(s) { s.classList.remove('active','prev'); });
            progressBars.forEach(function(b) { b.classList.remove('active'); var f=b.querySelector('.hero-progress-fill'); if(f){f.style.transition='none';f.style.width='0%';} });

            // 设置当前为active
            if (heroSlides[idx]) heroSlides[idx].classList.add('active');
            if (progressBars[idx]) progressBars[idx].classList.add('active');

            // 进度条动画：从0%到100%耗时5秒
            var fill = progressBars[idx] ? progressBars[idx].querySelector('.hero-progress-fill') : null;
            if (fill) {
                requestAnimationFrame(function() {
                    fill.style.transition = 'width ' + SLIDE_DURATION + 'ms linear';
                    fill.style.width = '100%';
                });
            }
            current = idx;
        }

        function nextSlide() {
            current = (current + 1) % heroSlides.length;
            showSlide(current);
        }

        function startAuto() {
            stopAuto();
            autoTimer = setInterval(nextSlide, SLIDE_DURATION);
        }
        function stopAuto() {
            if (autoTimer) clearInterval(autoTimer);
        }

        // 点击进度条跳转
        progressBars.forEach(function(bar, i) {
            bar.addEventListener('click', function() {
                stopAuto();
                showSlide(i);
                startAuto();
            });
        });

        // 鼠标悬停暂停
        var hero = document.getElementById('hero');
        if (hero) {
            hero.addEventListener('mouseenter', stopAuto);
            hero.addEventListener('mouseleave', startAuto);
        }

        // 初始化第一张
        showSlide(0);
        startAuto();
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
            if (e.target === this && this.id !== 'welcomeModal') this.classList.remove('active');
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
        var noImgText = (document.documentElement.lang || '').indexOf('en') === 0 ? 'No image available, please upload in admin panel.' : '暂无图片，请前往后台上传资质证照。';
        var clickClose = (document.documentElement.lang || '').indexOf('en') === 0 ? 'Click overlay to close' : '点击遮罩关闭';
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('modalImageSrc');
        const tip = document.getElementById('modalImageTip');
        if (!src) {
            if (tip) tip.textContent = noImgText;
            if (img) img.style.display = 'none';
        } else {
            if (img) { img.src = src; img.alt = title || ''; img.style.display = 'inline-block'; }
            if (tip) tip.textContent = clickClose;
        }
        const t = document.getElementById('modalImageTitle');
        if (t) t.textContent = title || (document.documentElement.lang.indexOf('en')===0?'Certificate Preview':'证照预览');
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

    // Welcome popup → collapse to side after closing
    const welcome = document.getElementById('welcomeModal');
    const welcomeCloseBtn = document.getElementById('welcomeCloseBtn');
    const welcomeSideTab = document.getElementById('welcomeSideTab');
    if (welcome && !sessionStorage.getItem('yy_welcome_shown')) {
        setTimeout(() => {
            welcome.classList.add('active');
            sessionStorage.setItem('yy_welcome_shown', '1');
        }, 1200);
    }
    // 关闭后缩至侧边
    function collapseWelcome() {
        if (welcome) {
            welcome.classList.remove('active');
            if (welcomeSideTab) {
                welcomeSideTab.classList.add('visible');
            }
        }
    }
    if (welcomeCloseBtn) {
        welcomeCloseBtn.addEventListener('click', collapseWelcome);
    }
    // 点击遮罩也可关闭（但不是移除，而是缩至侧边）
    if (welcome) {
        welcome.addEventListener('click', function(e) {
            if (e.target === welcome) collapseWelcome();
        });
    }
    // 点击侧边标签重新展开
    if (welcomeSideTab) {
        welcomeSideTab.addEventListener('click', function() {
            if (welcome) {
                welcome.classList.add('active');
                welcomeSideTab.classList.remove('visible');
            }
        });
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
    var langIsEn = (document.documentElement.lang || '').indexOf('en') === 0;
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const required = form.querySelectorAll('[required]');
            for (let i = 0; i < required.length; i++) {
                if (!required[i].value.trim()) {
                    e.preventDefault();
                    var label = required[i].closest('.form-group')?.querySelector('label')?.textContent || required[i].name || (langIsEn?'Field':'字段');
                    showGlobalNotify((label ? label + ' ' : '') + (langIsEn?'cannot be empty':'不能为空'), 'error');
                    required[i].focus();
                    return false;
                }
            }
        });
    });

    // Stats counter animation
    const statObserver = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = el.dataset.target;
                if (!target || el.classList.contains('counted')) return;
                el.classList.add('counted');
                if (isNaN(parseFloat(target))) {
                    el.textContent = target;
                    return;
                }
                const isFloat = target.indexOf('.') !== -1;
                const duration = 1500;
                const start = performance.now();
                const from = 0;
                const to = parseFloat(target);
                function update(now) {
                    const progress = Math.min((now - start) / duration, 1);
                    const ease = 1 - Math.pow(1 - progress, 3);
                    const current = from + (to - from) * ease;
                    el.textContent = isFloat ? current.toFixed(2) : Math.floor(current).toString();
                    if (progress < 1) requestAnimationFrame(update);
                }
                requestAnimationFrame(update);
            }
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('.stat-num[data-target]').forEach(function(el) { statObserver.observe(el); });

    // Map tooltip
    const mapTooltip = document.getElementById('mapTooltip');
    document.querySelectorAll('.map-node-g').forEach(function(node) {
        node.addEventListener('mouseenter', function() {
            if (!mapTooltip) return;
            var city = node.dataset.city || '';
            var region = node.dataset.region || '';
            mapTooltip.innerHTML = '<strong>' + city + '</strong>' + (region ? '<br><span style="opacity:.8;font-size:12px">' + region + '</span>' : '');
            mapTooltip.style.opacity = '1';
        });
        node.addEventListener('mousemove', function(e) {
            if (!mapTooltip) return;
            var rect = node.closest('.map-wrap').getBoundingClientRect();
            mapTooltip.style.left = (e.clientX - rect.left) + 'px';
            mapTooltip.style.top = (e.clientY - rect.top - 10) + 'px';
        });
        node.addEventListener('mouseleave', function() {
            if (mapTooltip) mapTooltip.style.opacity = '0';
        });
    });
});
