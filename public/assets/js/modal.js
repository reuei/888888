/**
 * 语云科技官网 - 弹窗系统
 * Modal System: Announcements, Confirmations, Product Details
 */

(function() {
  'use strict';

  /**
   * 弹窗管理器
   */
  class ModalManager {
    constructor() {
      this.activeModals = [];
    }

    /**
     * 创建并显示弹窗
     * @param {Object} options - 弹窗配置
     */
    create(options = {}) {
      const {
        type = 'default',       // default | confirm | alert | product
        title = '',
        content = '',
        headerColor = '#0052D9',
        buttonColor = '#0052D9',
        confirmText = '确定',
        cancelText = '取消',
        showCancel = true,
        onClose,
        onConfirm,
        customContent = ''      // 自定义HTML内容
      } = options;

      const overlay = document.createElement('div');
      overlay.className = `modal-overlay modal-${type}`;

      let bodyContent = customContent || `
        <div class="modal-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 16v-4M12 8h.01"/>
          </svg>
        </div>
        ${title ? `<h3>${title}</h3>` : ''}
        ${content ? `<p>${content}</p>` : ''}
      `;

      overlay.innerHTML = `
        <div class="modal">
          <div class="modal-header-bar">
            <div class="header-line" style="background: ${headerColor}"></div>
          </div>
          <button class="modal-close" aria-label="关闭">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
          <div class="modal-body">
            ${bodyContent}
          </div>
          <div class="modal-footer">
            ${showCancel ? `<button class="btn btn-outline modal-cancel-btn">${cancelText}</button>` : ''}
            <button class="btn modal-confirm-btn" style="background:${buttonColor};border-color:${buttonColor};color:#fff">${confirmText}</button>
          </div>
        </div>
      `;

      document.body.appendChild(overlay);
      this.activeModals.push(overlay);

      // 触发动画
      requestAnimationFrame(() => {
        overlay.classList.add('show');
      });

      document.body.style.overflow = 'hidden';

      // 绑定事件
      const closeModal = () => this.destroy(overlay, onClose);
      overlay.querySelector('.modal-close').addEventListener('click', closeModal);
      overlay.querySelector('.modal-cancel-btn')?.addEventListener('click', closeModal);
      overlay.querySelector('.modal-confirm-btn').addEventListener('click', () => {
        this.destroy(overlay, onConfirm);
      });
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
      });

      // ESC关闭
      const escHandler = (e) => {
        if (e.key === 'Escape') {
          closeModal();
          document.removeEventListener('keydown', escHandler);
        }
      };
      document.addEventListener('keydown', escHandler);

      return overlay;
    }

    destroy(modalEl, callback) {
      if (!modalEl) return;

      modalEl.classList.remove('show');
      document.body.style.overflow = '';

      setTimeout(() => {
        if (modalEl.parentNode) {
          modalEl.parentNode.removeChild(modalEl);
        }
        this.activeModals = this.activeModals.filter(m => m !== modalEl);
        if (callback) callback();
      }, 300);
    }

    /** 关闭所有弹窗 */
    closeAll() {
      [...this.activeModals].forEach(m => this.destroy(m));
    }
  }

  // 全局单例
  window.modalManager = new ModalManager();

  /**
   * 产品详情弹窗
   */
  function showProductDetail(productId) {
    // 从产品卡片获取数据或从API获取
    const card = document.querySelector(`[data-product-id="${productId}"]`);
    if (!card) return;

    const name = card.dataset.name || '产品详情';
    const category = card.dataset.category || '';
    const desc = card.querySelector('.product-desc')?.textContent || '';
    const features = [...card.querySelectorAll('.feature-item')].map(el => el.textContent.trim());

    const featuresHtml = features.length > 0
      ? `<ul style="text-align:left;padding-left:20px;margin-top:16px;">
           ${features.map(f => `<li style="font-size:0.875rem;color:#666;margin-bottom:6px;display:flex;align-items:center;gap:8px;"><span style="width:6px;height:6px;border-radius:50%;background:#0052D9;flex-shrink:0;"></span>${f}</li>`).join('')}
         </ul>`
      : '';

    window.modalManager.create({
      type: 'product',
      title: name,
      headerColor: '#0052D9',
      buttonColor: '#FF6B00',
      content: desc,
      confirmText: '立即咨询',
      cancelText: '了解更多',
      customContent: `
        <div style="text-align:center;">
          ${category ? `<span class="tag" style="margin-bottom:16px;">${category}</span>` : ''}
          <h3 style="margin-bottom:12px;">${name}</h3>
          <p style="color:#666;font-size:0.9375rem;line-height:1.7;">${desc}</p>
          ${featuresHtml}
        </div>
      `,
      onConfirm: () => {
        window.location.href = '/contact.html';
      },
      onClose: () => {
        window.location.href = '/products.html';
      }
    });
  }

  // 绑定产品详情按钮
  document.addEventListener('click', (e) => {
    const detailBtn = e.target.closest('[data-product-detail]');
    if (detailBtn) {
      e.preventDefault();
      showProductDetail(detailBtn.dataset.productDetail);
    }
  });

})();
