/* ===== 语云科技主脚本 ===== */
(function() {
  'use strict';

  // ========== 数据配置（与后端保持一致，用于无后端场景直接渲染）==========
  const SITE_DATA = {
    navbar: {
      navItems: [
        { text: '首页', url: 'index.html' },
        { text: '关于我们', url: 'about.html' },
        { text: '产品介绍', url: 'products.html', children: [
          { text: '云服务器', url: 'products.html#cloud-server' },
          { text: '云数据库', url: 'products.html#cloud-db' },
          { text: 'CDN加速', url: 'products.html#cdn' },
          { text: '安全防护', url: 'products.html#security' }
        ]},
        { text: '合作伙伴', url: 'partners.html' },
        { text: '联系我们', url: 'contact.html' },
        { text: '国际版', url: 'https://cloud.loveym.cloud', external: true }
      ]
    },
    footer: {
      logoText: '语云科技',
      salesPhone: '400-800-8451',
      companyAddress: '中国·北京市朝阳区语云大厦',
      companyEmail: 'contact@loveym.cloud',
      icpNumber: '京ICP备2024000001号-1',
      icpUrl: 'https://beian.miit.gov.cn/',
      policeNumber: '京公网安备 11010502000001号',
      policeUrl: 'http://www.beian.gov.cn/',
      valueAddedCert: 'B1-20240001（增值电信业务经营许可证）',
      copyright: '© 2015-2026 语云科技美国有限公司 China All Rights Reserved.',
      authorization: '语云科技®等是我们（语云科技美国有限公司）在中国的注册授权',
      internationalUrl: 'https://cloud.loveym.cloud',
      links: [
        { title: '产品服务', items: [
          { text: '云服务器', url: 'products.html#cloud-server' },
          { text: '云数据库', url: 'products.html#cloud-db' },
          { text: 'CDN加速', url: 'products.html#cdn' },
          { text: '安全防护', url: 'products.html#security' }
        ]},
        { title: '关于我们', items: [
          { text: '公司简介', url: 'about.html' },
          { text: '发展历程', url: 'about.html#timeline' },
          { text: '合作伙伴', url: 'partners.html' },
          { text: '联系我们', url: 'contact.html' }
        ]},
        { title: '帮助支持', items: [
          { text: '帮助文档', url: '#' },
          { text: '常见问题', url: '#' },
          { text: '服务等级', url: '#' },
          { text: '工单系统', url: '#' }
        ]}
      ]
    },
    popup: {
      enabled: true,
      title: '欢迎来到语云科技',
      headerColor: '#00a1d6',
      buttonColor: '#ff6b35',
      content: '🎉 感谢您访问语云科技官网！我们为您提供高性能云服务器、全球CDN加速、安全防护等一站式云服务。<br/><br/>📞 销售热线：400-800-8541<br/>💼 新用户专享：首购低至3折，立即咨询获取专属优惠！',
      confirmText: '立即咨询',
      closeText: '稍后再看',
      oncePerDay: true,
      sideBar: {
        enabled: true,
        title: '在线客服',
        phone: '400-800-8541',
        workHours: '周一至周日 9:00-22:00',
        qq: '123456789',
        wechat: 'yuyun-tech-support',
        email: 'contact@loveym.cloud'
      }
    }
  };

  // 通过 id 渲染导航
  function renderNavbar() {
    const navEl = document.getElementById('main-nav');
    if (!navEl) return;
    const data = SITE_DATA.navbar;
    navEl.innerHTML = data.navItems.map(item => {
      if (item.children && item.children.length) {
        const subHtml = item.children.map(c => `<a href="${c.url}">${c.text}</a>`).join('');
        return `<div class="nav-item has-sub"><span>${item.text}</span><div class="dropdown">${subHtml}</div></div>`;
      }
      const target = item.external ? ' target="_blank" rel="noopener"' : '';
      return `<a class="nav-item" href="${item.url}"${target}>${item.text}</a>`;
    }).join('');

    // 移动端子菜单切换
    document.querySelectorAll('.nav-item.has-sub > span').forEach(span => {
      span.addEventListener('click', (e) => {
        if (window.innerWidth <= 768) {
          e.preventDefault();
          span.parentElement.classList.toggle('open');
        }
      });
    });

    // 当前页面高亮
    const currentUrl = location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('.main-nav a.nav-item').forEach(a => {
      const href = a.getAttribute('href') || '';
      if (href === currentUrl || (currentUrl === '' && href === 'index.html')) {
        a.classList.add('active');
      }
    });
  }

  function renderFooter() {
    const footer = document.getElementById('site-footer');
    if (!footer) return;
    const f = SITE_DATA.footer;
    const linksHtml = f.links.map(col =>
      `<div class="footer-col"><h4>${col.title}</h4><ul>${col.items.map(i => `<li><a href="${i.url}">${i.text}</a></li>`).join('')}</ul></div>`
    ).join('');

    footer.innerHTML = `
      <div class="footer-top">
        <div class="container">
          <div class="footer-grid">
            <div class="footer-brand">
              <div class="logo"><div class="logo-icon">语</div><div class="logo-text"><span>语云</span>科技</div></div>
              <p class="footer-brand-desc">智能云服务，连接无限可能。为全球企业用户提供高性能云服务器、CDN加速、安全防护等一站式云计算解决方案。</p>
              <div class="footer-sales">
                <div class="footer-sales-label">📞 销售热线</div>
                <div class="footer-sales-phone">${f.salesPhone}</div>
              </div>
            </div>
            ${linksHtml}
            <div class="footer-col">
              <h4>联系我们</h4>
              <div class="footer-contact-item"><strong>📍 地址：</strong><br/>${f.companyAddress}</div>
              <div class="footer-contact-item"><strong>✉️ 邮箱：</strong><br/>${f.companyEmail}</div>
              <div class="footer-contact-item"><strong>🌐 国际版：</strong><br/><a href="${f.internationalUrl}" target="_blank" rel="noopener" style="color:#ff6b35;">cloud.loveym.cloud</a></div>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <div class="container">
          <a href="${f.icpUrl}" target="_blank" rel="noopener">${f.icpNumber}</a>
          <span class="footer-bottom-sep">|</span>
          <a href="${f.policeUrl}" target="_blank" rel="noopener">${f.policeNumber}</a>
          <span class="footer-bottom-sep">|</span>
          <span>${f.valueAddedCert}</span>
          <br/>
          <span>${f.copyright}</span>
          <span class="footer-auth">${f.authorization}</span>
        </div>
      </div>
    `;
  }

  // ========== 轮播 ==========
  function initCarousel() {
    const hero = document.getElementById('hero');
    if (!hero) return;
    const slides = hero.querySelectorAll('.hero-slide');
    const dotsWrap = hero.querySelector('.hero-dots');
    if (!slides.length) return;
    let current = 0;
    slides.forEach((_, i) => {
      const d = document.createElement('div');
      d.className = 'hero-dot' + (i === 0 ? ' active' : '');
      d.addEventListener('click', () => go(i));
      dotsWrap.appendChild(d);
    });
    const dots = dotsWrap.querySelectorAll('.hero-dot');
    function go(i) {
      slides[current].classList.remove('active');
      dots[current].classList.remove('active');
      current = (i + slides.length) % slides.length;
      slides[current].classList.add('active');
      dots[current].classList.add('active');
    }
    hero.querySelector('.hero-prev')?.addEventListener('click', () => go(current - 1));
    hero.querySelector('.hero-next')?.addEventListener('click', () => go(current + 1));
    let timer = setInterval(() => go(current + 1), 5000);
    hero.addEventListener('mouseenter', () => clearInterval(timer));
    hero.addEventListener('mouseleave', () => timer = setInterval(() => go(current + 1), 5000));
  }

  // ========== 汉堡菜单 ==========
  function initHamburger() {
    const h = document.getElementById('hamburger');
    const nav = document.getElementById('main-nav');
    if (!h || !nav) return;
    h.addEventListener('click', () => {
      h.classList.toggle('active');
      nav.classList.toggle('open');
    });
    document.addEventListener('click', (e) => {
      if (!h.contains(e.target) && !nav.contains(e.target)) {
        h.classList.remove('active');
        nav.classList.remove('open');
      }
    });
  }

  // ========== 回到顶部 ==========
  function initBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;
    window.addEventListener('scroll', () => {
      if (window.scrollY > 300) btn.classList.add('show');
      else btn.classList.remove('show');
    });
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  // ========== 弹窗 ==========
  function initPopup() {
    const p = SITE_DATA.popup;
    if (!p.enabled) return;

    // 首次访问弹窗
    if (p.oncePerDay) {
      const last = localStorage.getItem('yuyun_popup_date');
      const today = new Date().toDateString();
      if (last === today) return;
    } else {
      if (localStorage.getItem('yuyun_popup_shown')) return;
    }

    setTimeout(() => {
      showModal({
        title: p.title,
        content: p.content,
        confirmText: p.confirmText,
        closeText: p.closeText,
        headerColor: p.headerColor,
        buttonColor: p.buttonColor,
        onConfirm: () => { location.href = 'contact.html'; }
      });
      if (p.oncePerDay) localStorage.setItem('yuyun_popup_date', new Date().toDateString());
      else localStorage.setItem('yuyun_popup_shown', '1');
    }, 1500);
  }

  function showModal(opts) {
    const mask = document.createElement('div');
    mask.className = 'modal-mask';
    mask.innerHTML = `
      <div class="modal-box">
        <div class="modal-head" style="background:${opts.headerColor || '#00a1d6'};">
          <h3>${opts.title || '提示'}</h3>
          <button class="modal-close">✕</button>
        </div>
        <div class="modal-body">${opts.content || ''}</div>
        <div class="modal-foot">
          ${opts.closeText ? `<button class="btn btn-ghost modal-cancel">${opts.closeText}</button>` : ''}
          <button class="btn btn-accent modal-ok" style="background:${opts.buttonColor || '#ff6b35'};border-color:${opts.buttonColor || '#ff6b35'};">${opts.confirmText || '确定'}</button>
        </div>
      </div>`;
    document.body.appendChild(mask);
    requestAnimationFrame(() => mask.classList.add('show'));
    const close = () => { mask.classList.remove('show'); setTimeout(() => mask.remove(), 300); };
    mask.querySelector('.modal-close').onclick = close;
    mask.querySelector('.modal-ok').onclick = () => { opts.onConfirm && opts.onConfirm(); close(); };
    mask.querySelector('.modal-cancel')?.addEventListener('click', close);
    mask.addEventListener('click', (e) => { if (e.target === mask) close(); });
  }

  // ========== 客服侧边栏 ==========
  function initSidebar() {
    const wrap = document.getElementById('customer-sidebar');
    if (!wrap) return;
    const s = SITE_DATA.popup.sideBar;
    if (!s.enabled) { wrap.style.display = 'none'; return; }
    wrap.innerHTML = `
      <div class="cs-btn primary" title="在线咨询">
        <span class="cs-btn-icon">💬</span>
        <span>咨询</span>
      </div>
      <div class="cs-btn" title="联系电话">
        <span class="cs-btn-icon">📞</span>
        <span>电话</span>
      </div>
      <div class="cs-btn" title="工作时间">
        <span class="cs-btn-icon">🕒</span>
        <span>时间</span>
      </div>
      <div class="cs-btn" title="QQ咨询">
        <span class="cs-btn-icon">🐧</span>
        <span>QQ</span>
      </div>
      <div class="cs-btn top" id="back-to-top" title="回到顶部">
        <span class="cs-btn-icon">↑</span>
        <span>顶部</span>
      </div>
    `;
    const btns = wrap.querySelectorAll('.cs-btn:not(.top)');
    const titles = ['在线咨询', '联系电话', '工作时间', 'QQ咨询'];
    const contents = [
      `<strong style="color:#00a1d6;">${s.title}</strong><br/><br/>💼 专属顾问一对一服务<br/>📞 销售热线：${s.phone}<br/>✉️ 邮箱：${s.email}<br/><br/>点击「立即咨询」快速联系我们！`,
      `<strong style="color:#00a1d6;">客户服务电话</strong><br/><br/>📞 销售热线：<strong style="color:#ff6b35;font-size:18px;">${s.phone}</strong><br/>🕒 工作时间：${s.workHours}<br/><br/>我们随时为您提供专业服务！`,
      `<strong style="color:#00a1d6;">工作时间</strong><br/><br/>🕒 ${s.workHours}<br/><br/>全年无休，节假日照常服务<br/>紧急问题可通过工单系统7×24小时处理`,
      `<strong style="color:#00a1d6;">QQ 咨询</strong><br/><br/>🐧 QQ群：<strong>${s.qq}</strong><br/>💬 微信：${s.wechat}<br/>✉️ 邮箱：${s.email}<br/><br/>添加时请注明「业务咨询」`
    ];
    btns.forEach((btn, i) => {
      btn.addEventListener('click', () => {
        showModal({
          title: titles[i],
          content: contents[i],
          confirmText: i === 0 ? '立即咨询' : '知道了',
          closeText: '关闭',
          onConfirm: i === 0 ? () => location.href = 'contact.html' : undefined
        });
      });
    });
    initBackToTop();
  }

  // ========== 表单提交 ==========
  function initContactForm() {
    const form = document.getElementById('contact-form');
    if (!form) return;
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = form.querySelector('[name="name"]').value.trim();
      const phone = form.querySelector('[name="phone"]').value.trim();
      const company = form.querySelector('[name="company"]').value.trim();
      const msg = form.querySelector('[name="message"]').value.trim();
      if (!name || !phone || !msg) {
        showModal({ title: '提示', content: '请填写必填项（姓名、电话、需求内容）', confirmText: '好的', headerColor: '#ff6b35', buttonColor: '#ff6b35' });
        return;
      }
      showModal({
        title: '提交成功！',
        content: `🎉 感谢您的咨询，${name}！<br/><br/>我们的销售顾问将在2小时内通过 <strong style="color:#ff6b35;">${phone}</strong> 联系您。<br/><br/>如有紧急需求，请直接拨打销售热线 <strong style="color:#00a1d6;">400-800-8541</strong>。`,
        confirmText: '好的',
        closeText: null
      });
      form.reset();
    });
  }

  // ========== 数字滚动 ==========
  function initCounters() {
    const counters = document.querySelectorAll('.stat-number[data-count]');
    if (!counters.length) return;
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseFloat(el.dataset.count);
          const suffix = el.dataset.suffix || '';
          const duration = 1800;
          const start = performance.now();
          function step(now) {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const val = Math.floor(target * eased);
            el.textContent = val + suffix;
            if (progress < 1) requestAnimationFrame(step);
          }
          requestAnimationFrame(step);
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.3 });
    counters.forEach(c => observer.observe(c));
  }

  // ========== 动态产品/合作伙伴渲染（用于产品页、合作伙伴页）==========
  function renderProducts() {
    const wrap = document.getElementById('products-container');
    if (!wrap) return;
    const data = window.PRODUCT_DATA || { categories: [] };
    wrap.innerHTML = data.categories.map(cat => {
      const items = cat.items.map(p => `
        <div class="product-detail-card">
          ${p.tag ? `<span class="product-tag">${p.tag}</span>` : ''}
          <div class="product-detail-head">
            <div class="product-detail-title">${p.title}</div>
            <div class="product-detail-price">
              <span class="product-price-num">${p.price}</span>
              <span class="product-price-unit">${p.unit}</span>
            </div>
          </div>
          <div class="product-detail-body">
            <p class="product-desc">${p.desc}</p>
            <ul class="product-feature-list">
              ${p.features.map(f => `<li>${f}</li>`).join('')}
            </ul>
            <a class="btn btn-primary" href="contact.html">立即咨询</a>
          </div>
        </div>
      `).join('');
      return `
        <div class="product-category" id="${cat.id}">
          <div class="product-category-header">
            <div class="product-category-icon">${cat.icon}</div>
            <div class="product-category-name">${cat.name}</div>
          </div>
          <div class="product-detail-grid">${items}</div>
        </div>
      `;
    }).join('');
  }

  function renderPartners() {
    const wrap = document.getElementById('partners-page-container');
    if (!wrap) return;
    const data = window.PARTNER_DATA || { partners: [] };
    wrap.innerHTML = `<div class="partner-page-grid">${data.partners.map(p =>
      `<div class="partner-page-card"><div class="partner-page-icon">${p.icon}</div><div class="partner-page-name">${p.name}</div><div class="partner-page-cat">${p.category}</div></div>`
    ).join('')}</div>`;
  }

  // ========== 动态首页组件渲染 ==========
  function renderHomeDynamic() {
    // 合作伙伴横滚
    const pt = document.getElementById('partner-track');
    if (pt) {
      const data = window.PARTNER_DATA || { partners: [] };
      const items = data.partners.concat(data.partners).map(p =>
        `<div class="partner-logo"><span class="partner-logo-icon">${p.icon}</span><span class="partner-logo-name">${p.name}</span></div>`
      ).join('');
      pt.innerHTML = items;
    }
    // 地图标记
    const mw = document.getElementById('map-markers');
    if (mw && window.HOME_DATA && window.HOME_DATA.mapMarkers) {
      mw.innerHTML = window.HOME_DATA.mapMarkers.map(m => {
        // 将经纬度映射到 SVG 视图 (简化版)
        const left = ((m.lng + 180) / 360) * 100;
        const top = ((90 - m.lat) / 180) * 100;
        return `<div class="map-marker" style="left:${left}%;top:${top}%;"><div class="map-marker-label">${m.name}</div></div>`;
      }).join('');
    }
    // 核心产品 / 特色 / 资质
    const fg = document.getElementById('feature-grid');
    if (fg && window.HOME_DATA && window.HOME_DATA.features) {
      fg.innerHTML = window.HOME_DATA.features.map(f =>
        `<div class="feature-card"><div class="feature-icon">${f.icon}</div><div class="feature-title">${f.title}</div><div class="feature-desc">${f.desc}</div></div>`
      ).join('');
    }
    const pg = document.getElementById('product-grid');
    if (pg && window.HOME_DATA && window.HOME_DATA.products) {
      pg.innerHTML = window.HOME_DATA.products.map(p =>
        `<div class="product-card">
          ${p.tag ? `<span class="product-tag">${p.tag}</span>` : ''}
          <div class="product-icon">${p.icon}</div>
          <div class="product-title">${p.title}</div>
          <div class="product-desc">${p.desc}</div>
          <a class="product-link" href="${p.link}">了解更多</a>
        </div>`
      ).join('');
    }
    const cg = document.getElementById('cert-grid');
    if (cg && window.HOME_DATA && window.HOME_DATA.certifications) {
      cg.innerHTML = window.HOME_DATA.certifications.map(c =>
        `<div class="cert-card">
          <div class="cert-icon-box">📜</div>
          <div class="cert-title">${c.title}</div>
          <div class="cert-desc">${c.desc}</div>
          <div class="cert-no">${c.certNo}</div>
        </div>`
      ).join('');
    }
  }

  function renderAbout() {
    const ab = document.getElementById('about-content');
    if (!ab || !window.ABOUT_DATA) return;
    const d = window.ABOUT_DATA;
    const statsHtml = d.stats ? `<div class="stats-bar">${d.stats.map(s =>
      `<div class="stat-item"><div class="stat-number" data-count="${s.number.replace(/\D/g, '')}" data-suffix="${(s.number.match(/\D+/)||[''])[0]}">${s.number}</div><div class="stat-label">${s.label}</div></div>`
    ).join('')}</div>` : '';
    const timelineHtml = d.timeline ? `<div class="section"><div class="container"><div class="section-header"><span class="section-tag">DEVELOPMENT</span><h2 class="section-title">发展历程</h2><p class="section-subtitle">从初创到行业领先，我们一步步稳健前行</p></div><div class="timeline">${d.timeline.map(t =>
      `<div class="timeline-item"><div class="timeline-year">${t.year}</div><div class="timeline-event">${t.event}</div></div>`
    ).join('')}</div></div></div>` : '';
    ab.innerHTML = `
      <div class="section">
        <div class="container" style="max-width:960px;">
          <div class="section-header"><span class="section-tag">ABOUT US</span><h2 class="section-title">${d.companyName || '关于语云科技'}</h2><p class="section-subtitle">${d.companyVision || ''}</p></div>
          <div style="background:#fff;border-radius:16px;padding:48px;border:1px solid var(--border-light);box-shadow:var(--shadow-sm);font-size:16px;line-height:2;color:var(--text);text-align:center;">
            ${d.companyIntro || ''}
            <div style="margin-top:20px;color:var(--primary);font-weight:500;">${d.companyMission || ''}</div>
          </div>
        </div>
      </div>
      ${statsHtml}
      ${timelineHtml}
    `;
  }

  function renderContactPage() {
    const cp = document.getElementById('contact-page-content');
    if (!cp || !window.CONTACT_DATA) return;
    const d = window.CONTACT_DATA;
    const salesHtml = d.salesRegions ? `
      <div class="form-title" style="margin-top:28px;">区域销售经理</div>
      <div class="sales-grid">${d.salesRegions.map(s =>
        `<div class="sales-item"><div class="sales-region">${s.region}</div><div class="sales-name">${s.manager}</div><div class="sales-phone">${s.phone}</div></div>`
      ).join('')}</div>` : '';
    cp.innerHTML = `
      <div class="contact-grid">
        <div class="contact-info-box">
          <div class="form-title">联系方式</div>
          <div class="contact-item"><div class="contact-item-icon">📞</div><div><div class="contact-item-label">销售热线</div><div class="contact-item-value phone">${d.salesPhone}</div></div></div>
          <div class="contact-item"><div class="contact-item-icon">🕒</div><div><div class="contact-item-label">工作时间</div><div class="contact-item-value">${d.workHours}</div></div></div>
          <div class="contact-item"><div class="contact-item-icon">✉️</div><div><div class="contact-item-label">邮箱地址</div><div class="contact-item-value">${d.email}</div></div></div>
          <div class="contact-item"><div class="contact-item-icon">🐧</div><div><div class="contact-item-label">官方Q群</div><div class="contact-item-value">${d.qqGroup}</div></div></div>
          <div class="contact-item"><div class="contact-item-icon">💬</div><div><div class="contact-item-label">微信咨询</div><div class="contact-item-value">${d.wechatGroup}</div></div></div>
          <div class="contact-item"><div class="contact-item-icon">📍</div><div><div class="contact-item-label">公司地址</div><div class="contact-item-value">${d.address}</div></div></div>
        </div>
        <div class="contact-form-box">
          <div class="form-title">在线咨询 / 合作洽谈</div>
          <form id="contact-form">
            <div class="form-group"><label class="form-label">姓名 *</label><input class="form-control" name="name" placeholder="请输入您的姓名"/></div>
            <div class="form-group"><label class="form-label">联系电话 *</label><input class="form-control" name="phone" placeholder="请输入手机号码"/></div>
            <div class="form-group"><label class="form-label">公司名称</label><input class="form-control" name="company" placeholder="请输入公司名称（选填）"/></div>
            <div class="form-group"><label class="form-label">咨询内容 *</label><textarea class="form-control" name="message" placeholder="请简要描述您的业务需求..."></textarea></div>
            <button type="submit" class="btn btn-accent btn-lg" style="width:100%;">立即提交咨询</button>
            ${salesHtml}
          </form>
        </div>
      </div>
    `;
    initContactForm();
  }

  // 启动
  document.addEventListener('DOMContentLoaded', () => {
    renderNavbar();
    renderFooter();
    initCarousel();
    initHamburger();
    initSidebar();
    initPopup();
    renderHomeDynamic();
    renderProducts();
    renderPartners();
    renderAbout();
    renderContactPage();
    initCounters();
  });

  // 暴露给后台管理使用
  window.YUYUN = { showModal, SITE_DATA };
})();
