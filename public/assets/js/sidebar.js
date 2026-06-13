/**
 * 语云科技官网 - 客服侧边栏
 * Service Sidebar Component
 */

(function() {
  'use strict';

  class ServiceSidebar {
    constructor() {
      this.toggleBtn = document.querySelector('.sidebar-toggle');
      this.panel = document.querySelector('.sidebar-panel');
      this.backdrop = document.querySelector('.sidebar-backdrop');
      this.isOpen = false;

      if (this.toggleBtn && this.panel) {
        this.init();
      }
    }

    init() {
      // 绑定切换按钮
      this.toggleBtn.addEventListener('click', () => this.toggle());

      // 关闭按钮
      const closeBtn = this.panel?.querySelector('.sidebar-panel-close');
      if (closeBtn) {
        closeBtn.addEventListener('click', () => this.close());
      }

      // 背景遮罩点击关闭
      if (this.backdrop) {
        this.backdrop.addEventListener('click', () => this.close());
      }

      // ESC键关闭
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && this.isOpen) {
          this.close();
        }
      });

      // 加载联系信息
      this.loadContactInfo();
    }

    toggle() {
      this.isOpen ? this.close() : this.open();
    }

    open() {
      this.isOpen = true;
      this.panel?.classList.add('show');
      this.backdrop?.classList.add('show');
      this.toggleBtn?.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    }

    close() {
      this.isOpen = false;
      this.panel?.classList.remove('show');
      this.backdrop?.classList.remove('show');
      this.toggleBtn?.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
    }

    async loadContactInfo() {
      const companyData = await window.YuyunAPI?.apiFetch('/company');
      if (!companyData) return;

      // 更新电话
      const phoneEls = this.panel?.querySelectorAll('.sidebar-phone-highlight');
      phoneEls?.forEach(el => {
        if (companyData.phone) el.textContent = companyData.phone;
      });

      // 更新邮箱
      const emailEl = this.panel?.querySelector('[data-field="email"]');
      if (emailEl && companyData.email) {
        emailEl.textContent = companyData.email;
      }

      // 更新地址
      const addressEl = this.panel?.querySelector('[data-field="address"]');
      if (addressEl && companyData.address?.full) {
        addressEl.textContent = companyData.address.full;
      }

      // 更新二维码
      const qrEl = this.panel?.querySelector('.sidebar-qrcode img');
      if (qrEl && companyData.qrCodeImage) {
        qrEl.src = companyData.qrCodeImage;
        qrEl.alt = '官方群聊二维码';
      }
    }
  }

  // DOM Ready初始化
  document.addEventListener('DOMContentLoaded', () => {
    new ServiceSidebar();
  });

  // 暴露全局方法
  window.openServiceSidebar = () => {
    const sidebar = new ServiceSidebar();
    sidebar.open();
  };

})();
