/**
 * 语云科技官网 - 轮播图组件
 * Carousel Component
 */

(function() {
  'use strict';

  class Carousel {
    constructor(element) {
      this.container = element;
      if (!this.container) return;

      this.slidesWrapper = this.container.querySelector('.carousel-slides');
      this.slides = this.container.querySelectorAll('.carousel-slide');
      this.prevBtn = this.container.querySelector('.carousel-btn-prev');
      this.nextBtn = this.container.querySelector('.carousel-btn-next');
      this.indicatorsContainer = this.container.querySelector('.carousel-indicators');

      if (this.slides.length === 0) return;

      this.currentIndex = 0;
      this.slideCount = this.slides.length;
      this.autoPlayInterval = null;
      this.AUTO_PLAY_DELAY = 3000;
      this.isTransitioning = false;

      this.init();
    }

    init() {
      // 创建指示器圆点
      this.createIndicators();

      // 绑定控制按钮
      if (this.prevBtn) {
        this.prevBtn.addEventListener('click', () => this.prev());
      }
      if (this.nextBtn) {
        this.nextBtn.addEventListener('click', () => this.next());
      }

      // 键盘支持
      this.container.setAttribute('tabindex', '0');
      this.container.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') this.prev();
        if (e.key === 'ArrowRight') this.next();
      });

      // 触摸滑动支持
      this.initTouchSupport();

      // 鼠标悬停暂停自动播放
      this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
      this.container.addEventListener('mouseleave', () => this.startAutoPlay());

      // 开始自动播放
      this.startAutoPlay();

      // 显示第一张
      this.goTo(0, false);
    }

    createIndicators() {
      if (!this.indicatorsContainer) return;

      for (let i = 0; i < this.slideCount; i++) {
        const dot = document.createElement('button');
        dot.className = `carousel-dot${i === 0 ? ' active' : ''}`;
        dot.setAttribute('aria-label', `切换到第${i + 1}张幻灯片`);
        dot.addEventListener('click', () => this.goTo(i));
        this.indicatorsContainer.appendChild(dot);
      }
    }

    goTo(index, animate = true) {
      if (this.isTransitioning && animate) return;
      if (index === this.currentIndex) return;

      this.isTransitioning = true;

      // 循环处理
      if (index >= this.slideCount) index = 0;
      if (index < 0) index = this.slideCount - 1;

      this.currentIndex = index;

      // 移动滑块
      const offset = -index * 100;
      if (animate) {
        this.slidesWrapper.style.transition = 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
      } else {
        this.slidesWrapper.style.transition = 'none';
      }
      this.slidesWrapper.style.transform = `translateX(${offset}%)`;

      // 更新指示器
      this.updateIndicators();

      // 重置transition标志
      setTimeout(() => {
        this.isTransitioning = false;
      }, animate ? 600 : 50);
    }

    prev() {
      this.stopAutoPlay();
      this.goTo(this.currentIndex - 1);
      this.startAutoPlay();
    }

    next() {
      this.stopAutoPlay();
      this.goTo(this.currentIndex + 1);
      this.startAutoPlay();
    }

    updateIndicators() {
      if (!this.indicatorsContainer) return;
      const dots = this.indicatorsContainer.querySelectorAll('.carousel-dot');
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === this.currentIndex);
      });
    }

    startAutoPlay() {
      this.stopAutoPlay();
      this.autoPlayInterval = setInterval(() => {
        this.next();
      }, this.AUTO_PLAY_DELAY);
    }

    stopAutoPlay() {
      if (this.autoPlayInterval) {
        clearInterval(this.autoPlayInterval);
        this.autoPlayInterval = null;
      }
    }

    initTouchSupport() {
      let startX = 0;
      let startY = 0;
      let isDragging = false;

      this.container.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        isDragging = true;
        this.stopAutoPlay();
      }, { passive: true });

      this.container.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        const diffX = e.touches[0].clientX - startX;
        const diffY = e.touches[0].clientY - startY;

        // 如果是水平滑动，阻止垂直滚动
        if (Math.abs(diffX) > Math.abs(diffY)) {
          // 可以添加拖拽跟随效果
        }
      }, { passive: true });

      this.container.addEventListener('touchend', (e) => {
        if (!isDragging) return;
        isDragging = false;

        const endX = e.changedTouches[0].clientX;
        const diffX = endX - startX;

        if (Math.abs(diffX) > 50) {
          if (diffX > 0) {
            this.prev();
          } else {
            this.next();
          }
        }

        this.startAutoPlay();
      }, { passive: true });
    }

    // 销毁实例
    destroy() {
      this.stopAutoPlay();
    }
  }

  // ========== 动态加载轮播图数据 ==========
  async function initCarouselWithData() {
    const carouselEl = document.querySelector('.hero-carousel');
    if (!carouselEl) return;

    const data = await window.YuyunAPI?.apiFetch('/home');
    if (!data?.carousel) return;

    const slidesWrapper = carouselEl.querySelector('.carousel-slides');
    const indicatorsContainer = carouselEl.querySelector('.carousel-indicators');

    if (!slidesWrapper) return;

    // 过滤启用的轮播项并排序
    const enabledSlides = data.carousel
      .filter(s => s.enabled)
      .sort((a, b) => a.order - b.order);

    if (enabledSlides.length === 0) return;

    // 渲染幻灯片HTML
    slidesWrapper.innerHTML = enabledSlides.map(slide => `
      <div class="carousel-slide">
        <img src="${slide.image}" alt="${slide.title}" loading="${slide.order === 0 ? 'eager' : 'lazy'}" />
        <div class="carousel-slide-overlay">
          <div class="carousel-content">
            <h1>${slide.title}</h1>
            <p>${slide.description}</p>
            <div class="carousel-actions">
              ${slide.ctaText ? `<a href="${slide.ctaLink || '#'}" class="btn btn-accent btn-lg" data-confirm="立即咨询我们的专业顾问">${slide.ctaText}</a>` : ''}
              <a href="/products.html" class="btn btn-outline-white btn-lg">了解产品</a>
            </div>
          </div>
        </div>
      </div>
    `).join('');

    // 清空旧的指示器（如果有）
    if (indicatorsContainer) {
      indicatorsContainer.innerHTML = '';
    }

    // 初始化Carousel实例
    new Carousel(carouselEl);
  }

  // ========== DOM Ready ==========
  document.addEventListener('DOMContentLoaded', () => {
    // 尝试从API加载数据后初始化轮播图
    initCarouselWithData();

    // 如果没有API数据或失败，直接初始化已有HTML的轮播图
    const existingCarousel = document.querySelector('.hero-carousel');
    if (existingCarousel && !existingCarousel.querySelector('.carousel-slides').children.length) {
      // 等待一下看是否API加载成功
      setTimeout(() => {
        if (existingCarousel.querySelector('.carousel-slides').children.length > 0) {
          new Carousel(existingCarousel);
        }
      }, 1000);
    } else if (existingCarousel && existingCarousel.querySelector('.carousel-slides').children.length > 0) {
      // 已有静态HTML内容，直接初始化
      setTimeout(() => new Carousel(existingCarousel), 100);
    }
  });

  // 暴露给外部使用
  window.Carousel = Carousel;

})();
