/**
 * 语云科技官网 - 全局脚本
 * Global App Script: Navigation, API, Utilities
 */

(function() {
  'use strict';

  // ========== API 基础配置 ==========
  const API_BASE = '/api';

  /**
   * 通用API请求方法
   */
  async function apiFetch(endpoint, options = {}) {
    try {
      const response = await fetch(`${API_BASE}${endpoint}`, {
        headers: {
          'Content-Type': 'application/json',
          ...options.headers
        },
        ...options
      });
      const data = await response.json();
      if (data.code !== 200) {
        console.warn(`API [${endpoint}] 返回异常:`, data.error);
        return null;
      }
      return data.data;
    } catch (error) {
      console.warn(`API [${endpoint}] 请求失败:`, error);
      return null;
    }
  }

  /**
   * 从API获取数据并填充到DOM
   */
  async function loadAndRender(config) {
    const { endpoint, target, renderer, fallback } = config;
    const data = await apiFetch(endpoint);
    const el = typeof target === 'string' ? document.querySelector(target) : target;
    if (!el) return;

    if (data && renderer) {
      renderer(el, data);
    } else if (fallback) {
      el.innerHTML = fallback;
    }
  }

  // 暴露到全局供其他模块使用
  window.YuyunAPI = { apiFetch, loadAndRender };

  // ========== 导航栏功能 ==========
  class Navbar {
    constructor() {
      this.navbar = document.querySelector('.navbar');
      this.hamburger = document.querySelector('.hamburger');
      this.mobileMenu = document.querySelector('.mobile-nav-menu');
      this.mobileOverlay = document.querySelector('.mobile-nav-overlay');
      this.isOpen = false;

      if (this.navbar) {
        this.init();
      }
    }

    init() {
      // 滚动效果
      let lastScrollY = 0;
      window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        if (scrollY > 20) {
          this.navbar.classList.add('scrolled');
        } else {
          this.navbar.classList.remove('scrolled');
        }
        lastScrollY = scrollY;
      }, { passive: true });

      // 汉堡菜单
      if (this.hamburger) {
        this.hamburger.addEventListener('click', () => this.toggleMobile());
      }

      // 移动端遮罩点击关闭
      if (this.mobileOverlay) {
        this.mobileOverlay.addEventListener('click', () => this.closeMobile());
      }

      // 高亮当前页面的导航项
      this.highlightActiveNav();

      // 加载导航菜单数据
      this.loadMenuData();

      // 页脚电话动态渲染
      this.loadFooterData();
    }

    toggleMobile() {
      this.isOpen = !this.isOpen;
      this.hamburger.classList.toggle('active', this.isOpen);
      this.mobileMenu?.classList.toggle('show', this.isOpen);
      this.mobileOverlay?.classList.toggle('show', this.isOpen);
      document.body.style.overflow = this.isOpen ? 'hidden' : '';
    }

    closeMobile() {
      this.isOpen = false;
      this.hamburger?.classList.remove('active');
      this.mobileMenu?.classList.remove('show');
      this.mobileOverlay?.classList.remove('show');
      document.body.style.overflow = '';
    }

    highlightActiveNav() {
      const currentPath = window.location.pathname;
      const navItems = document.querySelectorAll('.navbar-item[data-page]');
      navItems.forEach(item => {
        const page = item.dataset.page;
        if (
          (page === 'home' && (currentPath === '/' || currentPath === '/index.html')) ||
          currentPath.includes(page)
        ) {
          item.classList.add('active');
        }
      });
    }

    async loadMenuData() {
      const settings = await apiFetch('/settings');
      if (settings?.navMenu) {
        this.renderNavMenu(settings.navMenu);
      }
      if (settings?.internationalUrl) {
        const intlLinks = document.querySelectorAll('[data-intl-link]');
        intlLinks.forEach(link => {
          link.href = settings.internationalUrl;
        });
      }
    }

    renderNavMenu(menuItems) {
      const desktopMenu = document.querySelector('.navbar-menu');
      const mobileMenu = document.querySelector('.mobile-nav-menu');

      if (!desktopMenu || !mobileMenu) return;

      const renderItems = (items, isMobile = false) => items.map(item => {
        const hasChildren = item.children && item.children.length > 0;
        const activeClass = this.isCurrentPage(item.link) ? 'active' : '';
        const dropdownHtml = hasChildren ? `
          <div class="dropdown-menu">
            ${item.children.map(child => `<a class="dropdown-item" href="${child.link}">${child.label}</a>`).join('')}
          </div>
        ` : '';

        return `
          <a class="navbar-item ${hasChildren ? 'has-dropdown' : ''} ${activeClass}" href="${item.link}" data-page="${item.label}">
            ${item.label}
            ${dropdownHtml}
          </a>
        `;
      }).join('');

      desktopMenu.innerHTML = renderItems(menuItems) + `
        <div class="navbar-actions">
          <a class="nav-intl-link" href="#" data-intl-link target="_blank">国际版</a>
        </div>
      `;

      mobileMenu.innerHTML = renderItems(menuItems, true);

      // 重新绑定汉堡菜单事件（因为innerHTML重置了DOM）
      const newHamburger = document.querySelector('.hamburger');
      if (newHamburger) {
        this.hamburger = newHamburger;
        newHamburger.addEventListener('click', () => this.toggleMobile());
      }
    }

    isCurrentPage(link) {
      const path = window.location.pathname;
      if (link === '/' && (path === '/' || path === '/index.html')) return true;
      return path.includes(link.replace('/', '').replace('.html', ''));
    }

    async loadFooterData() {
      const settings = await apiFetch('/settings');
      if (settings?.footer) {
        const phoneEl = document.querySelector('.footer-sales-phone .phone-number');
        if (phoneEl && settings.footer.salesPhone) {
          phoneEl.textContent = settings.footer.salesPhone;
        }

        const icpEl = document.querySelector('.footer-icp');
        if (icpEl && settings.footer.icp) {
          icpEl.textContent = settings.footer.icp;
          icpEl.href = settings.footer.icpUrl || '#';
        }

        const policeEl = document.querySelector('.footer-police');
        if (policeEl && settings.footer.policeCode) {
          policeEl.textContent = settings.footer.policeCode;
          policeEl.href = settings.footer.policeUrl || '#';
        }

        const licenseEl = document.querySelector('.footer-license');
        if (licenseEl && settings.footer.license) {
          licenseEl.textContent = settings.footer.license;
        }

        const declEl = document.querySelector('.footer-declaration');
        if (declEl && settings.footer.declaration) {
          declEl.textContent = settings.footer.declaration;
        }
      }
    }
  }

  // ========== 公告弹窗管理 ==========
  class AnnouncementModal {
    constructor() {
      this.modal = document.getElementById('announcementModal');
      if (this.modal) {
        this.init();
      }
    }

    init() {
      this.checkAndShow();
      // 绑定关闭按钮
      const closeBtns = this.modal.querySelectorAll('.modal-close, .modal-confirm-btn');
      closeBtns.forEach(btn => {
        btn.addEventListener('click', () => this.close());
      });

      // 点击遮罩关闭
      this.modal.addEventListener('click', (e) => {
        if (e.target === this.modal) this.close();
      });
    }

    async checkAndShow() {
      const settings = await apiFetch('/settings');
      const announcement = settings?.announcement;

      if (!announcement || !announcement.enabled) return;

      // 检查是否每日仅显示一次
      if (announcement.showOnceDaily) {
        const today = new Date().toDateString();
        const lastShown = localStorage.getItem('yuyun_announcement_last');
        if (lastShown === today) return;
      }

      // 设置样式
      const headerLine = this.modal.querySelector('.header-line');
      if (headerLine && announcement.headerColor) {
        headerLine.style.background = announcement.headerColor;
      }

      const confirmBtn = this.modal.querySelector('.modal-confirm-btn');
      if (confirmBtn && announcement.buttonColor) {
        confirmBtn.style.background = announcement.buttonColor;
        confirmBtn.style.borderColor = announcement.buttonColor;
      }

      // 设置内容
      const titleEl = this.modal.querySelector('.modal-body h3');
      const contentEl = this.modal.querySelector('.modal-body p');
      if (titleEl && announcement.title) titleEl.textContent = announcement.title;
      if (contentEl && announcement.content) contentEl.textContent = announcement.content;

      // 延迟显示
      setTimeout(() => {
        this.show();
      }, 800);
    }

    show() {
      this.modal.classList.add('show');
      document.body.style.overflow = 'hidden';
      localStorage.setItem('yuyun_announcement_last', new Date().toDateString());
    }

    close() {
      this.modal.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  // ========== 工具函数 ==========
  window.YuyunUtils = {
    /**
     * 防抖函数
     */
    debounce(fn, delay = 300) {
      let timer = null;
      return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
      };
    },

    /**
     * 节流函数
     */
    throttle(fn, limit = 200) {
      let inThrottle = false;
      return function(...args) {
        if (!inThrottle) {
          fn.apply(this, args);
          inThrottle = true;
          setTimeout(() => inThrottle = false, limit);
        }
      };
    },

    /**
     * 平滑滚动到元素
     */
    scrollTo(selector, offset = 80) {
      const el = document.querySelector(selector);
      if (el) {
        const top = el.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    },

    /**
     * 格式化日期
     */
    formatDate(dateStr) {
      const d = new Date(dateStr);
      return `${d.getFullYear()}年${d.getMonth() + 1}月${d.getDate()}日`;
    },

    /**
     * 显示操作确认弹窗
     */
    showConfirm(options = {}) {
      const { title = '确认操作', message = '确定要执行此操作吗？', confirmText = '确认', cancelText = '取消', onConfirm, onCancel } = options;

      const overlay = document.createElement('div');
      overlay.className = 'modal-overlay modal-confirm show';
      overlay.innerHTML = `
        <div class="modal">
          <div class="modal-body">
            <div class="modal-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4M12 8h.01"/>
              </svg>
            </div>
            <h3>${title}</h3>
            <p>${message}</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline modal-cancel-btn">${cancelText}</button>
            <button class="btn btn-primary modal-ok-btn">${confirmText}</button>
          </div>
        </div>
      `;

      document.body.appendChild(overlay);

      const cleanup = () => {
        overlay.remove();
        document.body.style.overflow = '';
      };

      overlay.querySelector('.modal-cancel-btn').addEventListener('click', () => {
        cleanup();
        if (onCancel) onCancel();
      });

      overlay.querySelector('.modal-ok-btn').addEventListener('click', () => {
        cleanup();
        if (onConfirm) onConfirm();
      });

      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          cleanup();
          if (onCancel) onCancel();
        }
      });

      document.body.style.overflow = 'hidden';
    }
  };

  // ========== DOM Ready 初始化 ==========
  document.addEventListener('DOMContentLoaded', () => {
    // 初始化导航
    new Navbar();

    // 初始化公告弹窗（仅首页）
    if (document.getElementById('announcementModal')) {
      new AnnouncementModal();
    }

    // 为所有带 data-confirm 的按钮绑定确认弹窗
    document.querySelectorAll('[data-confirm]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const msg = btn.dataset.confirm || '确定执行此操作？';
        YuyunUtils.showConfirm({
          message: msg,
          onConfirm: () => {
            if (btn.tagName === 'A') {
              window.location.href = btn.href;
            } else {
              btn.click();
            }
          }
        });
      });
    });

    // 页面入场动画 - 使用 IntersectionObserver
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -40px 0px'
    };

    const animateObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-fade-in-up');
          animateObserver.unobserve(entry.target);
        }
      });
    }, observerOptions);

    document.querySelectorAll('[data-animate]').forEach(el => {
      animateObserver.observe(el);
    });
  });

})();
