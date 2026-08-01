/**
 * senqidl - Carousel Component
 * 蓝主题 | 通用轮播组件
 *
 * 特性:
 *   - 自动播放 (可配置间隔, 默认 5 秒)
 *   - 上一张/下一张导航
 *   - 指示点 (dot indicators)
 *   - 触摸/滑动支持
 *   - 鼠标悬停暂停
 *   - 平滑淡入淡出过渡
 *   - 支持页面多个轮播实例
 *
 * 使用方式:
 *   HTML 结构:
 *     <div class="carousel" data-carousel data-interval="5000">
 *       <div class="carousel-item active">...</div>
 *       <div class="carousel-item">...</div>
 *       <button class="carousel-prev" data-carousel-prev>‹</button>
 *       <button class="carousel-next" data-carousel-next>›</button>
 *       <div class="carousel-dots">
 *         <span class="dot active" data-carousel-dot></span>
 *         <span class="dot" data-carousel-dot></span>
 *       </div>
 *     </div>
 *
 *   JS 初始化 (自动或手动):
 *     // 自动: 页面加载后会自动查找 [data-carousel] 元素初始化
 *     // 手动:
 *     var carousel = new Carousel(document.querySelector('.my-carousel'), { interval: 5000 });
 *     carousel.init();
 */

(function (global) {
    'use strict';

    /* ================================================
     * Carousel 类
     * ================================================ */
    function Carousel(element, options) {
        this.element = element;
        this.options = Object.assign({
            interval: 5000,
            transitionDuration: 800,
            autoPlay: true,
            pauseOnHover: true,
            enableTouch: true,
            showArrows: true,
            showDots: true,
            startIndex: 0
        }, options || {});

        this.currentIndex = 0;
        this.items = [];
        this.dots = [];
        this.prevBtn = null;
        this.nextBtn = null;
        this.timer = null;
        this.isTransitioning = false;

        // 触摸相关
        this.touchStartX = 0;
        this.touchEndX = 0;
        this.touchStartTime = 0;

        // 绑定上下文
        this._onPrev = this._onPrev.bind(this);
        this._onNext = this._onNext.bind(this);
        this._onDotClick = this._onDotClick.bind(this);
        this._onMouseEnter = this._onMouseEnter.bind(this);
        this._onMouseLeave = this._onMouseLeave.bind(this);
        this._onTouchStart = this._onTouchStart.bind(this);
        this._onTouchMove = this._onTouchMove.bind(this);
        this._onTouchEnd = this._onTouchEnd.bind(this);
        this._onVisibilityChange = this._onVisibilityChange.bind(this);
    }

    /* ================================================
     * 初始化
     * ================================================ */
    Carousel.prototype.init = function () {
        var self = this;

        // 查找轮播项
        this.items = Array.prototype.slice.call(
            this.element.querySelectorAll('.carousel-item, .banner-slide')
        );

        if (this.items.length === 0) return;

        // 查找控件
        this.prevBtn = this.element.querySelector('[data-carousel-prev], .carousel-prev, .banner-arrow.prev');
        this.nextBtn = this.element.querySelector('[data-carousel-next], .carousel-next, .banner-arrow.next');
        this.dots = Array.prototype.slice.call(
            this.element.querySelectorAll('[data-carousel-dot], .carousel-dots .dot, .banner-dot')
        );

        // 设置初始状态
        this.currentIndex = this.options.startIndex || 0;
        if (this.currentIndex >= this.items.length) {
            this.currentIndex = 0;
        }

        this._updateActiveItem(false);

        // 绑定事件
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', this._onPrev);
        }
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', this._onNext);
        }
        this.dots.forEach(function (dot, index) {
            dot.addEventListener('click', function () { self._onDotClick(index); });
        });

        // 悬停暂停
        if (this.options.pauseOnHover) {
            this.element.addEventListener('mouseenter', this._onMouseEnter);
            this.element.addEventListener('mouseleave', this._onMouseLeave);
        }

        // 触摸支持
        if (this.options.enableTouch) {
            this.element.addEventListener('touchstart', this._onTouchStart, { passive: true });
            this.element.addEventListener('touchmove', this._onTouchMove, { passive: true });
            this.element.addEventListener('touchend', this._onTouchEnd, { passive: true });
        }

        // 页面可见性 (标签页切换时暂停)
        document.addEventListener('visibilitychange', this._onVisibilityChange);

        // 启动自动播放
        if (this.options.autoPlay) {
            this.start();
        }

        // 暴露 API
        this.element._carousel = this;

        return this;
    };

    /* ================================================
     * 销毁
     * ================================================ */
    Carousel.prototype.destroy = function () {
        this.stop();

        if (this.prevBtn) this.prevBtn.removeEventListener('click', this._onPrev);
        if (this.nextBtn) this.nextBtn.removeEventListener('click', this._onNext);

        var self = this;
        this.dots.forEach(function (dot, index) {
            dot.removeEventListener('click', function () { self._onDotClick(index); });
        });

        if (this.options.pauseOnHover) {
            this.element.removeEventListener('mouseenter', this._onMouseEnter);
            this.element.removeEventListener('mouseleave', this._onMouseLeave);
        }

        if (this.options.enableTouch) {
            this.element.removeEventListener('touchstart', this._onTouchStart);
            this.element.removeEventListener('touchmove', this._onTouchMove);
            this.element.removeEventListener('touchend', this._onTouchEnd);
        }

        document.removeEventListener('visibilitychange', this._onVisibilityChange);
        this.element._carousel = null;
    };

    /* ================================================
     * 自动播放控制
     * ================================================ */
    Carousel.prototype.start = function () {
        var self = this;
        this.stop();
        this.timer = setInterval(function () {
            self.next();
        }, this.options.interval);
    };

    Carousel.prototype.stop = function () {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    };

    /* ================================================
     * 导航方法
     * ================================================ */
    Carousel.prototype.next = function () {
        this._goTo(this.currentIndex + 1);
    };

    Carousel.prototype.prev = function () {
        this._goTo(this.currentIndex - 1);
    };

    Carousel.prototype.goTo = function (index) {
        this._goTo(index);
    };

    /* ================================================
     * 内部: 切换到指定索引
     * ================================================ */
    Carousel.prototype._goTo = function (index) {
        if (this.isTransitioning) return;
        if (this.items.length <= 1) return;

        // 循环
        var newIndex = index;
        if (newIndex >= this.items.length) newIndex = 0;
        if (newIndex < 0) newIndex = this.items.length - 1;

        if (newIndex === this.currentIndex) return;

        this.isTransitioning = true;
        var oldIndex = this.currentIndex;
        this.currentIndex = newIndex;

        this._updateActiveItem(true);

        // 重置计时器
        if (this.options.autoPlay && this.timer) {
            this.stop();
            var self = this;
            this.timer = setInterval(function () { self.next(); }, this.options.interval);
        }

        // 过渡完成后解锁
        var self = this;
        setTimeout(function () {
            self.isTransitioning = false;
        }, this.options.transitionDuration);
    };

    /* ================================================
     * 内部: 更新激活项
     * ================================================ */
    Carousel.prototype._updateActiveItem = function (animate) {
        var self = this;

        this.items.forEach(function (item, index) {
            if (index === self.currentIndex) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        this.dots.forEach(function (dot, index) {
            if (index === self.currentIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    };

    /* ================================================
     * 事件处理器
     * ================================================ */
    Carousel.prototype._onPrev = function (e) {
        if (e) e.stopPropagation();
        this.prev();
    };

    Carousel.prototype._onNext = function (e) {
        if (e) e.stopPropagation();
        this.next();
    };

    Carousel.prototype._onDotClick = function (index) {
        this.goTo(index);
    };

    Carousel.prototype._onMouseEnter = function () {
        this.stop();
    };

    Carousel.prototype._onMouseLeave = function () {
        if (this.options.autoPlay) {
            this.start();
        }
    };

    /* 触摸支持 */
    Carousel.prototype._onTouchStart = function (e) {
        var touch = e.touches[0];
        this.touchStartX = touch.clientX;
        this.touchStartTime = Date.now();
        this.stop();
    };

    Carousel.prototype._onTouchMove = function (e) {
        var touch = e.touches[0];
        this.touchEndX = touch.clientX;
    };

    Carousel.prototype._onTouchEnd = function () {
        var diff = this.touchStartX - this.touchEndX;
        var elapsed = Date.now() - this.touchStartTime;

        // 滑动距离超过 50px 且耗时少于 500ms 才认为是滑动
        if (Math.abs(diff) > 50 && elapsed < 500) {
            if (diff > 0) {
                this.next();
            } else {
                this.prev();
            }
        }

        // 重置
        this.touchStartX = 0;
        this.touchEndX = 0;

        if (this.options.autoPlay) {
            this.start();
        }
    };

    /* 标签页切换 */
    Carousel.prototype._onVisibilityChange = function () {
        if (document.hidden) {
            this.stop();
        } else if (this.options.autoPlay) {
            this.start();
        }
    };

    /* ================================================
     * 自动初始化 (页面加载后)
     * ================================================ */
    function autoInit() {
        var elements = document.querySelectorAll('[data-carousel], .carousel');
        var instances = [];

        elements.forEach(function (el) {
            // 避免重复初始化
            if (el._carousel) return;

            var interval = el.getAttribute('data-interval');
            var options = {};
            if (interval) {
                options.interval = parseInt(interval, 10);
            }

            var instance = new Carousel(el, options);
            instance.init();
            instances.push(instance);
        });

        // 暴露到全局方便调试
        global._carousels = instances;
    }

    // 全局辅助函数 - 供内联事件处理器使用
    global.changeSlide = function (direction) {
        if (global._carousels && global._carousels[0]) {
            if (direction > 0) {
                global._carousels[0].next();
            } else {
                global._carousels[0].prev();
            }
        }
    };

    global.goToSlide = function (index) {
        if (global._carousels && global._carousels[0]) {
            global._carousels[0].goTo(index);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInit);
    } else {
        autoInit();
    }

    // 暴露 Carousel 构造函数到全局
    global.Carousel = Carousel;

})(window);