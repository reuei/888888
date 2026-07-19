/**
 * 主脚本文件
 * 中央纪委国家监委网站风格 CMS
 */
(function() {
    'use strict';

    // DOM 加载完成后初始化
    document.addEventListener('DOMContentLoaded', function() {
        initPreloader();
        initMobileMenu();
        initCarousel();
        initBackToTop();
        initPopup();
        initFormValidation();
    });

    // 预载动画
    function initPreloader() {
        var preloader = document.getElementById('preloader');
        if (!preloader) return;
        window.addEventListener('load', function() {
            setTimeout(function() {
                preloader.classList.add('hide');
                setTimeout(function() { preloader.style.display = 'none'; }, 500);
            }, 800);
        });
    }

    // 手机端汉堡菜单
    function initMobileMenu() {
        var hamburger = document.getElementById('hamburgerBtn');
        var sidebar = document.getElementById('mobileSidebar');
        var overlay = document.getElementById('mobileOverlay');
        var closeBtn = document.getElementById('mobileSidebarClose');

        if (!hamburger || !sidebar) return;

        function openMenu() {
            sidebar.classList.add('active');
            if (overlay) overlay.classList.add('active');
            hamburger.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            hamburger.classList.remove('active');
            document.body.style.overflow = '';
        }

        hamburger.addEventListener('click', function() {
            if (sidebar.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        if (closeBtn) closeBtn.addEventListener('click', closeMenu);
        if (overlay) overlay.addEventListener('click', closeMenu);

        // ESC 关闭
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeMenu();
            }
        });
    }

    // 轮播图
    function initCarousel() {
        var carousel = document.getElementById('carousel');
        if (!carousel) return;

        var track = document.getElementById('carouselTrack');
        var slides = track.querySelectorAll('.carousel-slide');
        var progressBars = document.querySelectorAll('.carousel-progress-bar');
        var prevBtn = document.getElementById('carouselPrev');
        var nextBtn = document.getElementById('carouselNext');
        var currentIndex = 0;
        var totalSlides = slides.length;
        var intervalId = null;
        var isTransitioning = false;

        if (totalSlides <= 1) return;

        function goToSlide(index) {
            if (isTransitioning || index === currentIndex) return;
            isTransitioning = true;

            // 移除当前
            slides[currentIndex].classList.remove('active');
            progressBars[currentIndex].classList.remove('active');
            var oldFill = progressBars[currentIndex].querySelector('.progress-fill');
            if (oldFill) oldFill.style.transition = 'none';
            if (oldFill) oldFill.style.width = '0';

            // 设置新
            currentIndex = index;
            slides[currentIndex].classList.add('active');
            progressBars[currentIndex].classList.add('active');
            var newFill = progressBars[currentIndex].querySelector('.progress-fill');
            if (newFill) {
                newFill.style.transition = 'none';
                newFill.style.width = '0';
                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        newFill.style.transition = 'width 5s linear';
                        newFill.style.width = '100%';
                    });
                });
            }

            setTimeout(function() { isTransitioning = false; }, 300);
            resetAutoPlay();
        }

        function nextSlide() {
            var next = (currentIndex + 1) % totalSlides;
            goToSlide(next);
        }

        function prevSlide() {
            var prev = (currentIndex - 1 + totalSlides) % totalSlides;
            goToSlide(prev);
        }

        function resetAutoPlay() {
            if (intervalId) clearInterval(intervalId);
            intervalId = setInterval(nextSlide, 5200);
        }

        if (prevBtn) prevBtn.addEventListener('click', function(e) { e.preventDefault(); prevSlide(); });
        if (nextBtn) nextBtn.addEventListener('click', function(e) { e.preventDefault(); nextSlide(); });

        // 进度条点击
        progressBars.forEach(function(bar, index) {
            bar.addEventListener('click', function() { goToSlide(index); });
        });

        // 触摸滑动支持
        var touchStartX = 0;
        var touchEndX = 0;
        carousel.addEventListener('touchstart', function(e) { touchStartX = e.changedTouches[0].screenX; });
        carousel.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            var diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) nextSlide();
                else prevSlide();
            }
        });

        // 启动自动播放
        resetAutoPlay();
        // 初始化第一个进度条
        var firstFill = progressBars[0].querySelector('.progress-fill');
        if (firstFill) {
            firstFill.style.transition = 'width 5s linear';
            firstFill.style.width = '100%';
        }
    }

    // 返回顶部
    function initBackToTop() {
        var btn = document.getElementById('backToTop');
        if (!btn) return;

        function toggleBtn() {
            if (window.pageYOffset > 300) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        }

        window.addEventListener('scroll', toggleBtn);
        btn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        toggleBtn();
    }

    // B2弹窗
    function initPopup() {
        var overlay = document.getElementById('popupOverlay');
        if (!overlay) return;

        var closeBtn = document.getElementById('popupClose');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                overlay.style.display = 'none';
            });
        }
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.style.display = 'none';
            }
        });
    }

    // 表单验证
    function initFormValidation() {
        // 通用输入框实时验证
        document.querySelectorAll('input[data-validate]').forEach(function(input) {
            var type = input.dataset.validate;
            var feedback = input.parentElement.nextElementSibling;
            
            input.addEventListener('input', function() {
                var val = input.value.trim();
                var valid = true;
                var msg = '';

                switch (type) {
                    case 'required':
                        valid = val !== '';
                        msg = valid ? '' : '此项为必填';
                        break;
                    case 'email':
                        valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
                        msg = valid ? '' : '邮箱格式不正确';
                        break;
                    case 'username':
                        valid = val.length >= 3 && val.length <= 20;
                        msg = valid ? '' : '用户名需3-20位';
                        break;
                    case 'password':
                        valid = val.length >= 6;
                        msg = valid ? '' : '密码至少6位';
                        break;
                }

                if (val === '' && type !== 'required') {
                    input.classList.remove('invalid', 'valid');
                    if (feedback) { feedback.textContent = ''; feedback.className = 'form-feedback'; }
                } else if (valid) {
                    input.classList.add('valid');
                    input.classList.remove('invalid');
                    if (feedback && msg) { feedback.textContent = '格式正确'; feedback.className = 'form-feedback success'; }
                } else {
                    input.classList.add('invalid');
                    input.classList.remove('valid');
                    if (feedback) { feedback.textContent = msg; feedback.className = 'form-feedback error'; }
                }
            });
        });
    }

})();