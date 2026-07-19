/**
 * 主脚本 v3.0.0
 * 中央纪委国家监委网站
 */
(function() {
    'use strict';

    var D = document;
    var W = window;

    D.addEventListener('DOMContentLoaded', function() {
        initLoader();
        initMobileMenu();
        initCarousel();
        initBackToTop();
        initPopup();
    });

    /* ========== 加载动画 ========== */
    function initLoader() {
        var loader = D.getElementById('pageLoader');
        if (!loader) return;
        W.addEventListener('load', function() {
            setTimeout(function() {
                loader.classList.add('hidden');
            }, 600);
        });
    }

    /* ========== Toast 全局提示 ========== */
    W.showToast = function(msg, type) {
        type = type || 'info';
        var container = D.getElementById('toastContainer');
        if (!container) return;
        var icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
        var toast = D.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.innerHTML = '<span class="toast-icon"><i class="fas ' + (icons[type] || icons.info) + '"></i></span><span class="toast-content">' + msg + '</span>';
        container.appendChild(toast);
        setTimeout(function() {
            toast.classList.add('removing');
            setTimeout(function() { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 300);
        }, 3500);
    };

    /* ========== 手机端汉堡菜单 ========== */
    function initMobileMenu() {
        var hamburger = D.getElementById('hamburgerBtn');
        var sidebar = D.getElementById('mobileSidebar');
        var overlay = D.getElementById('mobileOverlay');
        var closeBtn = D.getElementById('mobileSidebarClose');
        if (!hamburger || !sidebar) return;

        function openMenu() {
            sidebar.classList.add('active');
            if (overlay) overlay.classList.add('active');
            hamburger.classList.add('active');
            D.body.style.overflow = 'hidden';
        }
        function closeMenu() {
            sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            hamburger.classList.remove('active');
            D.body.style.overflow = '';
        }

        hamburger.addEventListener('click', function() {
            sidebar.classList.contains('active') ? closeMenu() : openMenu();
        });
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (overlay) overlay.addEventListener('click', closeMenu);
        D.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) closeMenu();
        });
    }

    /* ========== 轮播图 v3.0 ========== */
    function initCarousel() {
        var carousel = D.getElementById('carousel');
        if (!carousel) return;

        var slides = carousel.querySelectorAll('.carousel-slide');
        var total = slides.length;
        if (total <= 1) return;

        var current = 0;
        var counterEl = carousel.querySelector('.carousel-counter .current');
        var progressFill = D.getElementById('carouselProgressFill');
        var prevBtn = D.getElementById('carouselPrev');
        var nextBtn = D.getElementById('carouselNext');
        var timer = null;
        var duration = 3000; // 3秒

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

        function resetProgress() {
            if (!progressFill) return;
            progressFill.style.transition = 'none';
            progressFill.style.width = '0';
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    progressFill.style.transition = 'width ' + duration + 'ms linear';
                    progressFill.style.width = '100%';
                });
            });
        }

        function resetTimer() {
            clearTimeout(timer);
            timer = setTimeout(next, duration + 200);
        }

        if (prevBtn) prevBtn.addEventListener('click', function(e) { e.preventDefault(); prev(); });
        if (nextBtn) nextBtn.addEventListener('click', function(e) { e.preventDefault(); next(); });

        // 触摸滑动
        var touchX = 0;
        carousel.addEventListener('touchstart', function(e) { touchX = e.changedTouches[0].screenX; });
        carousel.addEventListener('touchend', function(e) {
            var diff = touchX - e.changedTouches[0].screenX;
            if (Math.abs(diff) > 40) { diff > 0 ? next() : prev(); }
        });

        resetTimer();
        resetProgress();
    }

    /* ========== 返回顶部 ========== */
    function initBackToTop() {
        var btn = D.getElementById('backToTop');
        if (!btn) return;
        function toggle() { btn.classList.toggle('visible', W.pageYOffset > 300); }
        W.addEventListener('scroll', toggle);
        btn.addEventListener('click', function() { W.scrollTo({ top: 0, behavior: 'smooth' }); });
        toggle();
    }

    /* ========== B2弹窗 ========== */
    function initPopup() {
        var overlay = D.getElementById('popupOverlay');
        if (!overlay) return;
        var closeBtn = D.getElementById('popupClose');
        if (closeBtn) closeBtn.addEventListener('click', function() { overlay.style.display = 'none'; });
        overlay.addEventListener('click', function(e) { if (e.target === overlay) overlay.style.display = 'none'; });
    }

})();