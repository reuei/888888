/**
 * CCDI Site - Government Discipline Inspection Website CMS v6.0.0
 * Main JavaScript
 */
(function () {
    'use strict';

    /* ============================================================
       1. Page Loader
       ============================================================ */
    function initLoader() {
        var loader = document.getElementById('pageLoader');
        if (!loader) return;
        window.addEventListener('load', function () {
            setTimeout(function () {
                loader.classList.add('page-loader--hidden');
            }, 500);
        });
    }

    /* ============================================================
       2. Toast Notifications
       ============================================================ */
    window.showToast = function (msg, type) {
        type = type || 'info';
        var container = document.getElementById('toastContainer');
        if (!container) return;

        var iconMap = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        var iconClass = iconMap[type] || iconMap.info;

        var toast = document.createElement('div');
        toast.className = 'toast toast--' + type;
        toast.innerHTML = '<span class="toast__icon"><i class="fas ' + iconClass + '"></i></span>' +
                          '<span class="toast__content">' + msg + '</span>';

        container.appendChild(toast);

        var removeTimer = setTimeout(function () {
            toast.classList.add('toast--removing');
        }, 3500);

        toast.addEventListener('transitionend', function () {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        });
    };

    /* ============================================================
       3. Mobile Menu
       ============================================================ */
    function initMobileMenu() {
        var hamburgerBtn = document.getElementById('hamburgerBtn');
        var mobileSidebar = document.getElementById('mobileSidebar');
        var mobileOverlay = document.getElementById('mobileOverlay');
        var closeBtn = document.getElementById('mobileSidebarClose');
        var body = document.body;

        if (!hamburgerBtn || !mobileSidebar || !mobileOverlay) return;

        function openMenu() {
            mobileSidebar.classList.add('mobile-sidebar--active');
            mobileOverlay.classList.add('mobile-overlay--active');
            hamburgerBtn.classList.add('hamburger--active');
            body.style.overflow = 'hidden';
        }

        function closeMenu() {
            mobileSidebar.classList.remove('mobile-sidebar--active');
            mobileOverlay.classList.remove('mobile-overlay--active');
            hamburgerBtn.classList.remove('hamburger--active');
            body.style.overflow = '';
        }

        hamburgerBtn.addEventListener('click', function () {
            if (mobileSidebar.classList.contains('mobile-sidebar--active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        mobileOverlay.addEventListener('click', closeMenu);

        if (closeBtn) {
            closeBtn.addEventListener('click', closeMenu);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mobileSidebar.classList.contains('mobile-sidebar--active')) {
                closeMenu();
            }
        });
    }

    /* ============================================================
       4. Carousel
       ============================================================ */
    function initCarousel() {
        var carousel = document.getElementById('carousel');
        if (!carousel) return;

        var slides = carousel.querySelectorAll('.carousel-slide');
        var prevBtn = document.getElementById('carouselPrev');
        var nextBtn = document.getElementById('carouselNext');
        var progressFill = document.getElementById('carouselProgressFill');
        var counterCurrent = carousel.querySelector('.carousel-counter .current');

        if (!slides.length) return;

        var totalSlides = slides.length;
        var currentIndex = 0;
        var autoTimer = null;
        var progressTimer = null;
        var progressStart = null;
        var autoDuration = 3200;
        var progressDuration = 3000;
        var isTransitioning = false;

        function goToSlide(index) {
            if (index === currentIndex || isTransitioning) return;
            isTransitioning = true;

            var currentSlide = slides[currentIndex];
            var nextSlide = slides[((index % totalSlides) + totalSlides) % totalSlides];

            currentSlide.classList.remove('active');
            currentIndex = ((index % totalSlides) + totalSlides) % totalSlides;
            nextSlide.classList.add('active');

            if (counterCurrent) {
                counterCurrent.textContent = currentIndex + 1;
            }

            resetProgress();

            setTimeout(function () {
                isTransitioning = false;
            }, 600);
        }

        function nextSlide() {
            goToSlide(currentIndex + 1);
        }

        function prevSlide() {
            goToSlide(currentIndex - 1);
        }

        function resetProgress() {
            if (progressTimer) {
                cancelAnimationFrame(progressTimer);
                progressTimer = null;
            }
            progressStart = null;
            if (progressFill) {
                progressFill.style.transition = 'none';
                progressFill.style.width = '0%';
                // Force reflow
                progressFill.offsetHeight;
                progressFill.style.transition = 'width 0.1s linear';
            }
            startProgress();
        }

        function startProgress() {
            if (!progressFill) return;
            progressStart = performance.now();
            function step(timestamp) {
                if (!progressStart) return;
                var elapsed = timestamp - progressStart;
                var pct = Math.min((elapsed / progressDuration) * 100, 100);
                progressFill.style.width = pct + '%';
                if (pct < 100) {
                    progressTimer = requestAnimationFrame(step);
                }
            }
            progressTimer = requestAnimationFrame(step);
        }

        function startAuto() {
            stopAuto();
            autoTimer = setInterval(nextSlide, autoDuration);
        }

        function stopAuto() {
            if (autoTimer) {
                clearInterval(autoTimer);
                autoTimer = null;
            }
        }

        function resetAuto() {
            stopAuto();
            startAuto();
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                prevSlide();
                resetAuto();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                nextSlide();
                resetAuto();
            });
        }

        // Touch swipe
        var touchStartX = 0;
        var touchEndX = 0;
        var swipeThreshold = 40;

        carousel.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
            stopAuto();
        }, { passive: true });

        carousel.addEventListener('touchend', function (e) {
            touchEndX = e.changedTouches[0].screenX;
            var diff = touchStartX - touchEndX;
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
            resetAuto();
        }, { passive: true });

        // Initialize
        startProgress();
        startAuto();
    }

    /* ============================================================
       5. Back to Top
       ============================================================ */
    function initBackToTop() {
        var btn = document.getElementById('backToTop');
        if (!btn) return;

        var ticking = false;

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () {
                    if (window.scrollY > 300) {
                        btn.classList.add('back-to-top--visible');
                    } else {
                        btn.classList.remove('back-to-top--visible');
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ============================================================
       6. Popup
       ============================================================ */
    function initPopup() {
        var overlay = document.getElementById('popupOverlay');
        if (!overlay) return;

        var closeBtn = document.getElementById('popupClose');

        setTimeout(function () {
            overlay.classList.add('popup-overlay--active');
        }, 800);

        function closePopup() {
            overlay.classList.remove('popup-overlay--active');
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closePopup);
        }

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                closePopup();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('popup-overlay--active')) {
                closePopup();
            }
        });
    }

    /* ============================================================
       7. Homepage Tabs
       ============================================================ */
    function initHomeTabs() {
        var tabBtns = document.querySelectorAll('.home-tab-btn');
        if (!tabBtns.length) return;

        tabBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetPanel = this.getAttribute('data-tab');
                if (!targetPanel) return;

                tabBtns.forEach(function (b) {
                    b.classList.remove('active');
                });
                var panels = document.querySelectorAll('.home-tab-panel');
                panels.forEach(function (p) {
                    p.classList.remove('active');
                });

                this.classList.add('active');
                var panel = document.getElementById(targetPanel);
                if (panel) {
                    panel.classList.add('active');
                }
            });
        });
    }

    /* ============================================================
       8. Sticky Nav Shadow
       ============================================================ */
    function initStickyNavShadow() {
        var nav = document.getElementById('mainNav');
        if (!nav) return;

        var scrollThreshold = 50;
        var scrolledClass = 'main-nav--scrolled';
        var ticking = false;

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(function () {
                    if (window.scrollY > scrollThreshold) {
                        nav.classList.add(scrolledClass);
                    } else {
                        nav.classList.remove(scrolledClass);
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    /* ============================================================
       Init All Modules
       ============================================================ */
    document.addEventListener('DOMContentLoaded', function () {
        initLoader();
        initMobileMenu();
        initCarousel();
        initBackToTop();
        initPopup();
        initHomeTabs();
        initStickyNavShadow();
    });

})();