// Yuyun Technology - main JS
(function () {
  // Hamburger menu
  const ham = document.querySelector('.hamburger');
  const mNav = document.querySelector('.mobile-nav');
  if (ham && mNav) {
    ham.addEventListener('click', () => {
      ham.classList.toggle('active');
      mNav.classList.toggle('open');
    });
  }

  // Carousel
  const carousel = document.querySelector('.carousel');
  if (carousel) {
    const track = carousel.querySelector('.carousel-track');
    const slides = carousel.querySelectorAll('.carousel-slide');
    const dotsWrap = carousel.querySelector('.carousel-dots');
    let idx = 0;
    let timer = null;
    slides.forEach((_, i) => {
      const b = document.createElement('button');
      if (i === 0) b.classList.add('active');
      b.addEventListener('click', () => { goTo(i); resetTimer(); });
      dotsWrap.appendChild(b);
    });
    const dots = dotsWrap.querySelectorAll('button');
    function goTo(i) {
      idx = (i + slides.length) % slides.length;
      track.style.transform = `translateX(-${idx * 100}%)`;
      dots.forEach((d, di) => d.classList.toggle('active', di === idx));
    }
    function next() { goTo(idx + 1); }
    function resetTimer() {
      if (timer) clearInterval(timer);
      timer = setInterval(next, 5000);
    }
    carousel.querySelector('.carousel-prev').addEventListener('click', () => { goTo(idx - 1); resetTimer(); });
    carousel.querySelector('.carousel-next').addEventListener('click', () => { goTo(idx + 1); resetTimer(); });
    resetTimer();
  }

  // Modal
  function openModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.add('open');
  }
  function closeModal(el) {
    el.classList.remove('open');
  }
  window.openModal = openModal;
  document.querySelectorAll('.modal-backdrop').forEach(m => {
    m.addEventListener('click', (e) => {
      if (e.target === m) closeModal(m);
    });
    m.querySelectorAll('.modal-close').forEach(btn => {
      btn.addEventListener('click', () => closeModal(m));
    });
  });
  document.querySelectorAll('[data-modal]').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      openModal(el.getAttribute('data-modal'));
    });
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-backdrop.open').forEach(m => closeModal(m));
    }
  });

  // Toasts from flash messages rendered as .toast-wait
  document.querySelectorAll('.toast-wait').forEach(t => {
    showToast(t.dataset.type || 'success', t.dataset.message || '');
  });
  function showToast(type, msg) {
    const wrap = document.querySelector('.toast-wrap') || (() => {
      const w = document.createElement('div');
      w.className = 'toast-wrap';
      document.body.appendChild(w);
      return w;
    })();
    const el = document.createElement('div');
    el.className = 'toast ' + (type || 'success');
    const icon = document.createElement('div');
    icon.className = 'toast-icon';
    icon.innerHTML = (type === 'error') ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-check"></i>';
    const text = document.createElement('span');
    text.textContent = msg;
    el.appendChild(icon);
    el.appendChild(text);
    wrap.appendChild(el);
    setTimeout(() => {
      el.style.transition = 'all .4s';
      el.style.opacity = 0;
      el.style.transform = 'translateX(40px)';
      setTimeout(() => el.remove(), 400);
    }, 3800);
  }
  window.showToast = showToast;

  // Map markers
  const mapContainer = document.querySelector('.offices-map');
  if (mapContainer && !mapContainer.dataset.loaded) {
    mapContainer.dataset.loaded = 'true';
    fetch('/api/offices').then(r => r.json()).then(data => {
      data.forEach(o => {
        const m = document.createElement('div');
        m.className = 'office-marker';
        const x = ((o.lng + 180) / 360) * 100;
        const y = ((90 - o.lat) / 180) * 100;
        m.style.left = x + '%';
        m.style.top = y + '%';
        m.setAttribute('data-name', o.name + ' (' + o.region + ')');
        mapContainer.appendChild(m);
      });
    }).catch(() => {});
  }

  // Contact form basic validate
  const contactForm = document.querySelector('#contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      const name = contactForm.querySelector('[name=name]').value.trim();
      const phone = contactForm.querySelector('[name=phone]').value.trim();
      if (!name || !phone) {
        e.preventDefault();
        showToast('error', '请填写姓名和联系电话');
      }
    });
  }
})();
