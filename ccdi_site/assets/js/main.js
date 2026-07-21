/**
 * CCDI Site - Government Discipline Inspection Website CMS v8.0.0
 * Main JavaScript
 */
(function () {
    'use strict';

    /* ============================================================
       Shared Utilities
       ============================================================ */

    /**
     * Debounce utility
     */
    function debounce(fn, delay) {
        var timer = null;
        return function () {
            var context = this;
            var args = arguments;
            if (timer) clearTimeout(timer);
            timer = setTimeout(function () {
                fn.apply(context, args);
            }, delay);
        };
    }

    /**
     * Throttle utility using requestAnimationFrame
     */
    function rafThrottle(fn) {
        var ticking = false;
        return function () {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(function () {
                    fn();
                    ticking = false;
                });
            }
        };
    }

    /* ============================================================
       1. Page Loader
       Cylinders slide up/down in place like a smooth wave
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

        setTimeout(function () {
            toast.classList.add('toast--removing');
        }, 3500);

        toast.addEventListener('transitionend', function () {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        });
    };

    /* ============================================================
       3. Mobile Menu (performance-optimized)
       ============================================================ */
    function initMobileMenu() {
        var hamburgerBtn = document.getElementById('hamburgerBtn');
        var mobileSidebar = document.getElementById('mobileSidebar');
        var mobileOverlay = document.getElementById('mobileOverlay');
        var closeBtn = document.getElementById('mobileSidebarClose');
        var body = document.body;

        if (!hamburgerBtn || !mobileSidebar || !mobileOverlay) return;

        var isOpen = false;

        function openMenu() {
            if (isOpen) return;
            isOpen = true;
            requestAnimationFrame(function () {
                mobileSidebar.classList.add('mobile-sidebar--active');
                mobileOverlay.classList.add('mobile-overlay--active');
                hamburgerBtn.classList.add('hamburger--active');
                body.style.overflow = 'hidden';
            });
        }

        function closeMenu() {
            if (!isOpen) return;
            isOpen = false;
            requestAnimationFrame(function () {
                mobileSidebar.classList.remove('mobile-sidebar--active');
                mobileOverlay.classList.remove('mobile-overlay--active');
                hamburgerBtn.classList.remove('hamburger--active');
                body.style.overflow = '';
            });
        }

        hamburgerBtn.addEventListener('click', function () {
            if (isOpen) {
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
            if (e.key === 'Escape' && isOpen) {
                closeMenu();
            }
        });
    }

    /* ============================================================
       4. Main Carousel
       - Touch swipe support
       - Video carousel support (play/pause on active/inactive)
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

        /**
         * Handle video playback when a slide becomes active/inactive
         */
        function handleVideoForSlide(slide, isActive) {
            var video = slide.querySelector('video');
            if (!video) return;
            if (isActive) {
                // Reset and play
                video.currentTime = 0;
                var playPromise = video.play();
                if (playPromise !== undefined) {
                    playPromise.catch(function () {
                        // Autoplay may be blocked; silently ignore
                    });
                }
            } else {
                video.pause();
            }
        }

        function goToSlide(index) {
            if (index === currentIndex || isTransitioning) return;
            isTransitioning = true;

            var normIndex = ((index % totalSlides) + totalSlides) % totalSlides;
            var currentSlide = slides[currentIndex];
            var nextSlide = slides[normIndex];

            currentSlide.classList.remove('active');
            handleVideoForSlide(currentSlide, false);

            currentIndex = normIndex;
            nextSlide.classList.add('active');
            handleVideoForSlide(nextSlide, true);

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

        // Button controls
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

        // Touch swipe support
        var touchStartX = 0;
        var touchStartY = 0;
        var touchEndX = 0;
        var touchEndY = 0;
        var swipeThreshold = 40;

        carousel.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
            stopAuto();
        }, { passive: true });

        carousel.addEventListener('touchend', function (e) {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;
            var diffX = touchStartX - touchEndX;
            var diffY = touchStartY - touchEndY;
            // Only handle horizontal swipes
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > swipeThreshold) {
                if (diffX > 0) {
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
       9. Scroll-Based Reveal Animations
       Uses IntersectionObserver for .animate-on-scroll elements
       ============================================================ */
    function initScrollReveal() {
        var elements = document.querySelectorAll('.animate-on-scroll');
        if (!elements.length) return;

        // Pre-apply hidden state so they are invisible before intersection
        elements.forEach(function (el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94), transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '0px 0px -30px 0px'
        });

        elements.forEach(function (el) {
            observer.observe(el);
        });
    }

    /* ============================================================
       10. Counters Animation
       Animates .counter elements counting up from 0 to target
       ============================================================ */
    function initCounters() {
        var counters = document.querySelectorAll('.counter');
        if (!counters.length) return;

        function animateCounter(el) {
            var target = parseInt(el.getAttribute('data-count'), 10);
            if (isNaN(target)) {
                // Try to parse from text content
                target = parseInt(el.textContent.replace(/[^0-9]/g, ''), 10);
                if (isNaN(target)) return;
            }

            var startVal = 0;
            var duration = 2000; // ms
            var startTime = null;

            el.textContent = '0';

            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                var elapsed = timestamp - startTime;
                var progress = Math.min(elapsed / duration, 1);

                // Ease-out cubic
                var eased = 1 - Math.pow(1 - progress, 3);
                var currentVal = Math.round(eased * target);

                el.textContent = currentVal.toLocaleString();

                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = target.toLocaleString();
                }
            }

            requestAnimationFrame(step);
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });

        counters.forEach(function (el) {
            observer.observe(el);
        });
    }

    /* ============================================================
       11. Parallax Effect
       Subtle parallax scroll for .parallax-bg elements
       ============================================================ */
    function initParallax() {
        var parallaxEls = document.querySelectorAll('.parallax-bg');
        if (!parallaxEls.length) return;

        var ticking = false;

        function updateParallax() {
            var scrollY = window.scrollY;
            var viewportH = window.innerHeight;

            parallaxEls.forEach(function (el) {
                var rect = el.getBoundingClientRect();
                var elTop = rect.top + scrollY;
                var elHeight = rect.height;

                // Only update if element is visible (within viewport + buffer)
                if (elTop + elHeight > scrollY - viewportH && elTop < scrollY + viewportH) {
                    var relativeScroll = (scrollY - elTop + viewportH) / (elHeight + viewportH);
                    var offset = (relativeScroll - 0.5) * 0.15; // Subtle 15% movement
                    var translateY = offset * elHeight;
                    el.style.transform = 'translate3d(0, ' + translateY + 'px, 0)';
                }
            });

            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }, { passive: true });
    }

    /* ============================================================
       12. Footer Carousel
       Separate carousel instance for .footer-carousel
       ============================================================ */
    function initFooterCarousel() {
        var footerCarousel = document.querySelector('.footer-carousel');
        if (!footerCarousel) return;

        var track = footerCarousel.querySelector('.footer-carousel__track');
        var slides = footerCarousel.querySelectorAll('.footer-carousel__slide');
        var prevBtn = footerCarousel.querySelector('.footer-carousel__prev');
        var nextBtn = footerCarousel.querySelector('.footer-carousel__next');

        if (!slides.length) return;

        var totalSlides = slides.length;
        var currentIndex = 0;
        var autoTimer = null;
        var autoDuration = 4000;
        var isTransitioning = false;

        // If no track element, use the footerCarousel itself as wrapper
        var wrapper = track || footerCarousel;

        function goToSlide(index) {
            if (index === currentIndex || isTransitioning) return;
            isTransitioning = true;

            var normIndex = ((index % totalSlides) + totalSlides) % totalSlides;

            slides[currentIndex].classList.remove('footer-carousel__slide--active');
            currentIndex = normIndex;
            slides[currentIndex].classList.add('footer-carousel__slide--active');

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

        // Touch swipe support for footer carousel
        var touchStartX = 0;
        var touchStartY = 0;
        var touchEndX = 0;
        var touchEndY = 0;
        var swipeThreshold = 40;

        wrapper.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
            stopAuto();
        }, { passive: true });

        wrapper.addEventListener('touchend', function (e) {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;
            var diffX = touchStartX - touchEndX;
            var diffY = touchStartY - touchEndY;
            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > swipeThreshold) {
                if (diffX > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
            resetAuto();
        }, { passive: true });

        // Initialize autoplay
        startAuto();
    }

    /* ============================================================
       13. Smooth Scroll for Anchor Links
       ============================================================ */
    function initSmoothScroll() {
        document.addEventListener('click', function (e) {
            var target = e.target;

            // Walk up to find the anchor
            while (target && target !== document) {
                if (target.tagName === 'A' && target.getAttribute('href')) {
                    break;
                }
                target = target.parentNode;
            }

            if (!target || target.tagName !== 'A') return;

            var href = target.getAttribute('href');

            // Only handle internal anchor links
            if (!href || href.charAt(0) !== '#' || href === '#') return;

            var targetId = href.substring(1);
            var targetEl = document.getElementById(targetId);
            if (!targetEl) return;

            e.preventDefault();

            var navHeight = 0;
            var mainNav = document.getElementById('mainNav');
            if (mainNav) {
                navHeight = mainNav.offsetHeight;
            }

            var targetPosition = targetEl.getBoundingClientRect().top + window.scrollY - navHeight - 20;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        });
    }

    /* ============================================================
       14. Image Lazy Loading
       Loads images with data-src attribute when they enter viewport
       ============================================================ */
    function initLazyLoad() {
        var lazyImages = document.querySelectorAll('img[data-src]');
        if (!lazyImages.length) return;

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    var src = img.getAttribute('data-src');
                    if (src) {
                        img.src = src;
                        img.removeAttribute('data-src');

                        // Handle srcset if present
                        var srcset = img.getAttribute('data-srcset');
                        if (srcset) {
                            img.srcset = srcset;
                            img.removeAttribute('data-srcset');
                        }

                        img.addEventListener('load', function () {
                            img.classList.add('lazy-loaded');
                        });
                    }
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '100px 0px'
        });

        lazyImages.forEach(function (img) {
            observer.observe(img);
        });
    }

    /* ============================================================
       15. Debounced Resize Handler
       ============================================================ */
    function initResizeHandler() {
        var resizeHandlers = [];

        /**
         * Register a callback to be called on debounced resize
         */
        window.addResizeListener = function (fn) {
            if (typeof fn === 'function') {
                resizeHandlers.push(fn);
            }
        };

        var onResize = debounce(function () {
            var vw = window.innerWidth;
            var vh = window.innerHeight;

            // Update CSS custom properties for responsive adjustments
            var root = document.documentElement;
            root.style.setProperty('--vw', vw + 'px');
            root.style.setProperty('--vh', vh + 'px');

            // Call all registered handlers
            resizeHandlers.forEach(function (handler) {
                try {
                    handler({ width: vw, height: vh });
                } catch (e) {
                    // Silently ignore handler errors
                }
            });
        }, 150);

        window.addEventListener('resize', onResize, { passive: true });
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
        initScrollReveal();
        initCounters();
        initParallax();
        initFooterCarousel();
        initSmoothScroll();
        initLazyLoad();
        initResizeHandler();
    });

})();