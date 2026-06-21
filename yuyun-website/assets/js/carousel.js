/**
 * 语云科技企业官网 - 轮播组件
 * 支持自动播放、手动切换、触摸滑动
 */

(function() {
    'use strict';

    function Carousel(element, options) {
        this.element = element;
        this.options = Object.assign({
            autoplay: true,
            interval: 5000,
            pauseOnHover: true,
            dots: true,
            arrows: true,
            touchSwipe: true,
            transitionSpeed: 800
        }, options);

        this.slidesContainer = element.querySelector('.carousel-slides');
        this.slides = element.querySelectorAll('.carousel-slide');
        this.indicators = element.querySelectorAll('.carousel-indicator');
        this.prevBtn = element.querySelector('.carousel-btn-prev');
        this.nextBtn = element.querySelector('.carousel-btn-next');

        this.currentIndex = 0;
        this.slideCount = this.slides.length;
        this.isPlaying = true;
        this.timer = null;
        this.touchStartX = 0;
        this.touchEndX = 0;

        if (this.slideCount <= 1) {
            this.options.autoplay = false;
            this.options.arrows = false;
            if (this.prevBtn) this.prevBtn.style.display = 'none';
            if (this.nextBtn) this.nextBtn.style.display = 'none';
        }

        this.init();
    }

    Carousel.prototype.init = function() {
        this.bindEvents();
        if (this.options.autoplay && this.slideCount > 1) {
            this.startAutoplay();
        }
        this.updateIndicators();
    };

    Carousel.prototype.bindEvents = function() {
        var self = this;

        // 箭头按钮
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', function() {
                self.prev();
                self.resetAutoplay();
            });
        }

        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', function() {
                self.next();
                self.resetAutoplay();
            });
        }

        // 指示器点击
        this.indicators.forEach(function(indicator, index) {
            indicator.addEventListener('click', function() {
                self.goTo(index);
                self.resetAutoplay();
            });
        });

        // 鼠标悬停暂停
        if (this.options.pauseOnHover) {
            this.element.addEventListener('mouseenter', function() {
                self.pauseAutoplay();
            });
            this.element.addEventListener('mouseleave', function() {
                if (self.options.autoplay) self.startAutoplay();
            });
        }

        // 触摸滑动
        if (this.options.touchSwipe) {
            this.element.addEventListener('touchstart', function(e) {
                self.touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            this.element.addEventListener('touchend', function(e) {
                self.touchEndX = e.changedTouches[0].screenX;
                self.handleSwipe();
            }, { passive: true });
        }

        // 键盘支持
        this.element.setAttribute('tabindex', '0');
        this.element.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                self.prev();
                self.resetAutoplay();
            } else if (e.key === 'ArrowRight') {
                self.next();
                self.resetAutoplay();
            }
        });
    };

    Carousel.prototype.goTo = function(index) {
        if (index < 0) index = this.slideCount - 1;
        if (index >= this.slideCount) index = 0;

        this.currentIndex = index;
        var offset = -index * 100;
        this.slidesContainer.style.transform = 'translateX(' + offset + '%)';
        this.updateIndicators();
        this.triggerSlideChange();
    };

    Carousel.prototype.next = function() {
        this.goTo(this.currentIndex + 1);
    };

    Carousel.prototype.prev = function() {
        this.goTo(this.currentIndex - 1);
    };

    Carousel.prototype.startAutoplay = function() {
        var self = this;
        this.isPlaying = true;
        this.stopAnimationClasses();

        this.timer = setInterval(function() {
            self.next();
            self.animateIndicator(self.currentIndex);
        }, this.options.interval);
    };

    Carousel.prototype.pauseAutoplay = function() {
        this.isPlaying = false;
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
        this.stopAnimationClasses();
    };

    Carousel.prototype.resetAutoplay = function() {
        if (this.options.autoplay) {
            this.pauseAutoplay();
            this.startAutoplay();
        }
    };

    Carousel.prototype.updateIndicators = function() {
        var self = this;
        this.indicators.forEach(function(indicator, index) {
            indicator.classList.toggle('active', index === self.currentIndex);
        });
    };

    Carousel.prototype.animateIndicator = function(index) {
        this.stopAnimationClasses();
        var activeIndicator = this.indicators[index];
        if (activeIndicator) {
            activeIndicator.classList.add('animating');
        }
    };

    Carousel.prototype.stopAnimationClasses = function() {
        this.indicators.forEach(function(indicator) {
            indicator.classList.remove('animating');
        });
        // 强制重绘以重新启动CSS动画
        void this.indicators[0]?.offsetHeight;
    };

    Carousel.prototype.handleSwipe = function() {
        var diff = this.touchStartX - this.touchEndX;
        var threshold = 50;

        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                this.next();
            } else {
                this.prev();
            }
            this.resetAutoplay();
        }
    };

    Carousel.prototype.triggerSlideChange = function() {
        var event = new CustomEvent('slideChange', {
            detail: { index: this.currentIndex, slide: this.slides[this.currentIndex] }
        });
        this.element.dispatchEvent(event);
    };

    // 自动初始化
    document.addEventListener('DOMContentLoaded', function() {
        var carousels = document.querySelectorAll('.hero-carousel');
        carousels.forEach(function(el) {
            el._carousel = new Carousel(el);
        });
    });

    // 暴露给全局
    window.Carousel = Carousel;

})();
