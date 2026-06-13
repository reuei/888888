(function () {
  var header = document.getElementById('header');
  var hamburger = document.getElementById('hamburger');
  var nav = document.getElementById('nav');

  if (header) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 20) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  }

  if (hamburger && nav) {
    hamburger.addEventListener('click', function () {
      hamburger.classList.toggle('active');
      nav.classList.toggle('open');
    });
    nav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        hamburger.classList.remove('active');
        nav.classList.remove('open');
      });
    });
  }

  var closeButtons = document.querySelectorAll('[data-close]');
  closeButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = btn.getAttribute('data-close');
      var el = document.getElementById(id);
      if (el) {
        el.classList.remove('show');
      }
    });
  });

  document.querySelectorAll('.popup-mask').forEach(function (mask) {
    mask.addEventListener('click', function (e) {
      if (e.target === mask) {
        mask.classList.remove('show');
      }
    });
  });

  var contactBtn = document.querySelector('.sidebar-item');
  if (contactBtn) {
    contactBtn.addEventListener('click', function () {
      var popup = document.getElementById('popupContact');
      if (popup) {
        popup.classList.add('show');
      }
    });
  }

  function showAnnouncement() {
    var popup = document.getElementById('popupAnnouncement');
    if (!popup) return;
    if (!localStorage.getItem('yuyun_popup_shown')) {
      setTimeout(function () {
        popup.classList.add('show');
        localStorage.setItem('yuyun_popup_shown', '1');
      }, 1500);
    }
  }
  showAnnouncement();

  function setupFadeIn() {
    var items = document.querySelectorAll('.fade-in');
    if (!('IntersectionObserver' in window)) {
      items.forEach(function (i) { i.classList.add('visible'); });
      return;
    }
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.1 });
    items.forEach(function (i) { observer.observe(i); });
  }
  setupFadeIn();

  document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var href = a.getAttribute('href');
      if (href === '#' || href === '#top') {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
  });
})();
