/**
 * 语云科技官网 - 合作伙伴横滚
 * Partners Marquee Animation
 */

(function() {
  'use strict';

  class PartnersMarquee {
    constructor(containerSelector = '.marquee-track') {
      this.track = document.querySelector(containerSelector);
      if (!this.track) return;

      this.init();
    }

    async init() {
      // 尝试从API加载合作伙伴数据
      const data = await window.YuyunAPI?.apiFetch('/partners');
      const partners = data?.partners || [];

      if (partners.length > 0) {
        this.renderPartners(partners);
      }
    }

    renderPartners(partners) {
      // 排序
      const sorted = partners.sort((a, b) => (a.order || 0) - (b.order || 0));

      // 生成单个partner HTML
      const createItem = (partner) => `
        <div class="marquee-item" title="${partner.name}">
          ${partner.logo
            ? `<img src="${partner.logo}" alt="${partner.name}" loading="lazy" />`
            : `<span class="partner-name-text">${partner.name}</span>`
          }
        </div>
      `;

      // 复制一份用于无缝循环
      const itemsHtml = sorted.map(createItem).join('');
      const duplicatedHtml = itemsHtml + itemsHtml;

      this.track.innerHTML = duplicatedHtml;

      // 根据内容数量调整动画速度
      const speed = Math.max(30, sorted.length * 3);
      this.track.style.animationDuration = `${speed}s`;
    }
  }

  /**
   * 合作伙伴页面网格渲染
   */
  class PartnersGrid {
    constructor(containerSelector = '.partners-page-grid') {
      this.container = document.querySelector(containerSelector);
      if (!this.container) return;

      this.init();
    }

    async init() {
      const data = await window.YuyunAPI?.apiFetch('/partners');
      const partners = data?.partners || [];

      if (partners.length > 0) {
        this.renderGrid(partners);
      }
    }

    renderGrid(partners) {
      const sorted = partners.sort((a, b) => (a.order || 0) - (b.order || 0));

      this.container.innerHTML = sorted.map(p => `
        <div class="partner-card" data-animate>
          <div class="partner-logo-area">
            ${p.logo
              ? `<img src="${p.logo}" alt="${p.name}" loading="lazy" />`
              : `<span class="partner-name-text">${p.name}</span>`
            }
          </div>
          <h4>${p.name}</h4>
          <p>${p.description || '战略合作伙伴'}</p>
          ${p.link ? `<a href="${p.link}" target="_blank" rel="noopener noreferrer" class="partner-link">
            访问官网 <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
          </a>` : ''}
        </div>
      `).join('');
    }
  }

  // DOM Ready
  document.addEventListener('DOMContentLoaded', () => {
    // 首页横滚
    new PartnersMarquee('.marquee-track');

    // 合作伙伴页面网格
    new PartnersGrid('.partners-page-grid');
  });

  window.PartnersMarquee = PartnersMarquee;
  window.PartnersGrid = PartnersGrid;

})();
