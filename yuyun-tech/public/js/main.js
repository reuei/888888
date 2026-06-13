// ===== 语云科技官网主脚本 =====

const API_BASE = '';

// 通用fetch封装
async function apiGet(url) {
  const res = await fetch(API_BASE + url);
  return res.json();
}

async function apiPost(url, data, token) {
  const opts = {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  };
  if (token) opts.headers['Authorization'] = 'Bearer ' + token;
  const res = await fetch(API_BASE + url, opts);
  return res.json();
}

// ===== 导航滚动效果 =====
function initHeader() {
  const header = document.querySelector('.header');
  if (!header) return;
  window.addEventListener('scroll', () => {
    if (window.scrollY > 10) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  });
}

// ===== 移动端汉堡菜单 =====
function initMobileMenu() {
  const hamburger = document.querySelector('.hamburger');
  const mobileMenu = document.querySelector('.mobile-menu');
  if (!hamburger || !mobileMenu) return;

  hamburger.addEventListener('click', () => {
    mobileMenu.classList.toggle('open');
  });

  mobileMenu.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
      mobileMenu.classList.remove('open');
    });
  });
}

// ===== 轮播图 =====
function initCarousel() {
  const carousel = document.querySelector('.carousel');
  if (!carousel) return;

  const slides = carousel.querySelectorAll('.carousel-slide');
  const dots = carousel.querySelectorAll('.carousel-dot');
  const prevBtn = carousel.querySelector('.carousel-btn.prev');
  const nextBtn = carousel.querySelector('.carousel-btn.next');
  if (!slides.length) return;

  let current = 0;
  let timer = null;

  function show(index) {
    slides.forEach((s, i) => s.classList.toggle('active', i === index));
    dots.forEach((d, i) => d.classList.toggle('active', i === index));
    current = index;
  }

  function next() {
    show((current + 1) % slides.length);
  }

  function prev() {
    show((current - 1 + slides.length) % slides.length);
  }

  function start() {
    timer = setInterval(next, 3000);
  }

  function stop() {
    clearInterval(timer);
  }

  if (prevBtn) prevBtn.addEventListener('click', () => { stop(); prev(); start(); });
  if (nextBtn) nextBtn.addEventListener('click', () => { stop(); next(); start(); });

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => { stop(); show(i); start(); });
  });

  carousel.addEventListener('mouseenter', stop);
  carousel.addEventListener('mouseleave', start);

  show(0);
  start();
}

// ===== 弹窗系统 =====
function initPopup() {
  const overlay = document.getElementById('popupModal');
  if (!overlay) return;

  apiGet('/api/config/popup').then(res => {
    if (!res.success || !res.data.enabled) return;
    const cfg = res.data;

    const shouldShow = () => {
      if (!cfg.oncePerDay) return true;
      const last = localStorage.getItem('popup_last_shown');
      if (!last) return true;
      const lastDate = new Date(last).toDateString();
      const today = new Date().toDateString();
      return lastDate !== today;
    };

    if (!shouldShow()) return;

    const header = overlay.querySelector('.modal-header');
    const title = overlay.querySelector('.modal-title');
    const body = overlay.querySelector('.modal-body');
    const btn = overlay.querySelector('.modal-confirm');

    if (header) header.style.background = cfg.headerColor || '#0052D9';
    if (title) title.textContent = cfg.title || '通知';
    if (body) body.innerHTML = cfg.content || '';
    if (btn) {
      btn.style.background = cfg.buttonColor || '#0052D9';
      btn.addEventListener('click', () => {
        overlay.classList.remove('active');
        localStorage.setItem('popup_last_shown', new Date().toISOString());
      });
    }

    const closeBtn = overlay.querySelector('.modal-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        overlay.classList.remove('active');
        localStorage.setItem('popup_last_shown', new Date().toISOString());
      });
    }

    setTimeout(() => {
      overlay.classList.add('active');
    }, 800);
  });
}

// ===== 客服侧边栏 =====
function initSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const toggle = document.querySelector('.sidebar-toggle');
  if (!sidebar || !toggle) return;

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
  });

  document.addEventListener('click', (e) => {
    if (!sidebar.contains(e.target)) {
      sidebar.classList.remove('open');
    }
  });
}

// ===== 页脚数据加载 =====
function initFooter() {
  const phoneEl = document.getElementById('footerPhone');
  const icpEl = document.getElementById('footerIcp');
  const certEl = document.getElementById('footerCert');
  const policeEl = document.getElementById('footerPolice');
  const stmtEl = document.getElementById('footerStatement');

  apiGet('/api/config/footer').then(res => {
    if (!res.success) return;
    const d = res.data;
    if (phoneEl) phoneEl.textContent = '销售电话:' + (d.phone || '');
    if (icpEl) {
      icpEl.textContent = d.icp || '';
      icpEl.href = d.icpLink || '#';
    }
    if (certEl) certEl.textContent = d.certificate || '';
    if (policeEl) {
      policeEl.textContent = d.police || '';
      policeEl.href = d.policeLink || '#';
    }
    if (stmtEl) stmtEl.textContent = d.statement || '';
  });
}

// ===== 百度地图初始化 =====
function initMap() {
  const mapContainer = document.getElementById('globalMap');
  if (!mapContainer || typeof BMap === 'undefined') return;

  apiGet('/api/config/map').then(res => {
    if (!res.success) return;
    const markers = res.data.markers || [];

    const map = new BMap.Map('globalMap');
    map.enableScrollWheelZoom(true);

    if (markers.length > 0) {
      const first = markers[0];
      map.centerAndZoom(new BMap.Point(first.lng, first.lat), 3);
    } else {
      map.centerAndZoom(new BMap.Point(116.4074, 39.9042), 3);
    }

    markers.forEach(m => {
      const point = new BMap.Point(m.lng, m.lat);
      const marker = new BMap.Marker(point);
      map.addOverlay(marker);

      const info = new BMap.InfoWindow(
        `<div style="padding:8px;"><h4 style="margin:0 0 6px;font-size:15px;color:#0052D9;">${m.name}</h4><p style="margin:0;font-size:13px;color:#4E5969;">${m.description}</p></div>`,
        { width: 260 }
      );

      marker.addEventListener('click', () => {
        map.openInfoWindow(info, point);
      });
    });
  });
}

// ===== 首页数据加载 =====
function initHome() {
  const bannersContainer = document.getElementById('bannersContainer');
  const certsContainer = document.getElementById('certsContainer');
  const productsContainer = document.getElementById('productsContainer');
  const partnersScroll = document.getElementById('partnersScroll');

  apiGet('/api/config/home').then(res => {
    if (!res.success) return;
    const d = res.data;

    // 轮播图
    if (bannersContainer && d.banners) {
      bannersContainer.innerHTML = d.banners.map((b, i) => `
        <div class="carousel-slide ${i === 0 ? 'active' : ''}">
          <img src="${b.image}" alt="${b.title}">
          <div class="carousel-overlay">
            <div class="carousel-content">
              <h2>${b.title}</h2>
              <p>${b.description}</p>
              <a href="${b.link}" class="btn btn-primary">了解更多</a>
            </div>
          </div>
        </div>
      `).join('');

      const dotsContainer = document.getElementById('carouselDots');
      if (dotsContainer) {
        dotsContainer.innerHTML = d.banners.map((_, i) => `
          <button class="carousel-dot ${i === 0 ? 'active' : ''}"></button>
        `).join('');
      }

      initCarousel();
    }

    // 资质
    if (certsContainer && d.certificates) {
      certsContainer.innerHTML = d.certificates.map(c => `
        <div class="certificate-card">
          <img src="${c.image}" alt="${c.title}">
          <div class="certificate-info">
            <h3>${c.title}</h3>
            <p>${c.description}</p>
          </div>
        </div>
      `).join('');
    }

    // 产品
    if (productsContainer && d.products) {
      const iconMap = {
        cloud: '☁', shield: '🛡', database: '🗄', globe: '🌐',
        server: '🖥', network: '🔗', monitor: '📊', lock: '🔒'
      };
      productsContainer.innerHTML = d.products.map(p => `
        <div class="product-card">
          <div class="product-icon">${iconMap[p.icon] || '◆'}</div>
          <h3>${p.title}</h3>
          <p>${p.description}</p>
          <a href="${p.link}" class="link">了解更多 →</a>
        </div>
      `).join('');
    }
  });

  // 合作伙伴
  if (partnersScroll) {
    apiGet('/api/config/partners').then(res => {
      if (!res.success) return;
      const partners = res.data.partners || [];
      // 复制一份实现无缝滚动
      const all = [...partners, ...partners];
      partnersScroll.innerHTML = all.map(p => `
        <a href="${p.link}" target="_blank" rel="noopener">
          <img src="${p.logo}" alt="${p.name}" class="partner-logo" title="${p.name}">
        </a>
      `).join('');
    });
  }
}

// ===== 初始化 =====
document.addEventListener('DOMContentLoaded', () => {
  initHeader();
  initMobileMenu();
  initPopup();
  initSidebar();
  initFooter();
  initHome();
  initMap();
});
