(function () {
  'use strict';

  var $ = function (sel, ctx) {
    return (ctx || document).querySelector(sel);
  };

  var $$ = function (sel, ctx) {
    return Array.prototype.slice.call((ctx || document).querySelectorAll(sel));
  };

  var ICONS = {
    success: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
    error: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    warning: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    info: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
  };

  var LS = {
    THEME: 'qeefg_theme',
    ANN: 'qeefg_ann_hidden',
    LANG: 'qeefg_lang'
  };

  function getToastContainer() {
    var c = $('.toast-center');
    if (!c) {
      c = document.createElement('div');
      c.className = 'toast-center';
      document.body.appendChild(c);
    }
    return c;
  }

  function removeToast(toast, item) {
    if (toast._removed) return;
    toast._removed = true;
    clearTimeout(toast._timer);
    toast.classList.remove('show');
    toast.classList.add('hide');
    setTimeout(function () {
      if (item.parentNode) item.parentNode.removeChild(item);
    }, 300);
  }

  function showToast(msg, type) {
    type = type || 'info';
    var container = getToastContainer();
    var item = document.createElement('div');
    item.className = 'toast-center-item';
    var toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    var icon = document.createElement('span');
    icon.className = 'toast-icon';
    icon.innerHTML = ICONS[type] || ICONS.info;
    var body = document.createElement('span');
    body.className = 'toast-body';
    body.textContent = msg;
    toast.appendChild(icon);
    toast.appendChild(body);
    item.appendChild(toast);
    container.appendChild(item);
    void toast.offsetWidth;
    toast.classList.add('show');
    var timer = setTimeout(function () { removeToast(toast, item); }, 3000);
    toast._timer = timer;
    toast._item = item;
    toast.addEventListener('click', function () {
      clearTimeout(timer);
      removeToast(toast, item);
    });
  }

  function showError(msg) { showToast(msg, 'error'); }
  function showSuccess(msg) { showToast(msg, 'success'); }
  function showWarning(msg) { showToast(msg, 'warning'); }

  function initTheme() {
    var saved = localStorage.getItem(LS.THEME);
    if (saved === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    } else if (saved === 'light') {
      document.documentElement.removeAttribute('data-theme');
    }
    if (!saved && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  }

  function toggleTheme() {
    var cur = document.documentElement.getAttribute('data-theme');
    var next = cur === 'dark' ? 'light' : 'dark';
    if (next === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    } else {
      document.documentElement.removeAttribute('data-theme');
    }
    localStorage.setItem(LS.THEME, next);
    var icon = $('#themeIcon');
    if (icon) {
      icon.innerHTML = next === 'dark'
        ? '<use href="#i-sun"/>'
        : '<use href="#i-moon"/>';
    }
  }

  function bindThemeToggle() {
    var btn = $('.theme-toggle') || $('#themeToggle');
    if (btn) btn.addEventListener('click', toggleTheme);
    document.addEventListener('keydown', function (e) {
      if (e.ctrlKey && e.key === 't') {
        e.preventDefault();
        toggleTheme();
      }
    });
  }

  function initSmoothScroll() {
    document.documentElement.style.scrollBehavior = 'smooth';
    document.addEventListener('click', function (e) {
      var link = e.target.closest('a[href^="#"]');
      if (!link) return;
      var id = link.getAttribute('href');
      if (!id || id === '#') return;
      var target = document.querySelector(id);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }

  function initMobileNav() {
    var btn = $('.hamburger-btn') || $('#hamburgerBtn');
    var nav = $('.mobile-nav') || $('#mobileNav');
    if (!btn || !nav) return;
    btn.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('show');
      btn.classList.toggle('open', isOpen);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('show')) {
        nav.classList.remove('show');
        btn.classList.remove('open');
      }
    });
  }

  function pathMatches(href, curPath) {
    if (!href || href === '#') return false;
    var nh = href !== '/' && href.slice(-1) === '/' ? href.slice(0, -1) : href;
    var cp = curPath !== '/' && curPath.slice(-1) === '/' ? curPath.slice(0, -1) : curPath;
    if (cp === nh) return true;
    if (nh !== '/' && cp.indexOf(nh + '/') === 0) return true;
    return false;
  }

  function initSidebarActive() {
    var cur = window.location.pathname;
    $$('.nav-link').forEach(function (link) {
      var href = link.getAttribute('href');
      if (pathMatches(href, cur)) link.classList.add('active');
    });
  }

  function initAnnouncement() {
    var modal = $('.announcement-modal') || $('#announcementModal');
    if (!modal) return;

    var hidden = localStorage.getItem(LS.ANN);
    if (hidden) {
      try {
        var p = JSON.parse(hidden);
        if (Date.now() - p.timestamp < 3600000) return;
      } catch (e) {}
    }

    showAnnouncement();

    var closeBtn = $('.am-close', modal);
    if (!closeBtn) closeBtn = $('#amCloseBtn', modal);
    if (closeBtn) {
      closeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        hideAnnouncement();
      });
    }

    var suppressBtn = $('#amConfirmBtn', modal);
    if (suppressBtn) {
      suppressBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        localStorage.setItem(LS.ANN, JSON.stringify({ timestamp: Date.now() }));
        hideAnnouncement();
      });
    }

    var overlay = $('.am-overlay', modal);
    if (overlay) {
      overlay.addEventListener('click', hideAnnouncement);
    } else {
      modal.addEventListener('click', function (e) {
        if (e.target === modal) hideAnnouncement();
      });
    }

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.classList.contains('show')) hideAnnouncement();
    });
  }

  function showAnnouncement() {
    var modal = $('.announcement-modal') || $('#announcementModal');
    if (modal) {
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  }

  function hideAnnouncement() {
    var modal = $('.announcement-modal') || $('#announcementModal');
    if (modal) {
      modal.classList.remove('show');
      document.body.style.overflow = '';
    }
  }

  window.showAnnouncement = showAnnouncement;
  window.hideAnnouncement = hideAnnouncement;
  window.showAnnouncementPopup = showAnnouncement;

  function refreshCaptcha() {
    var img = $('.captcha-img');
    if (!img) return;
    var src = img.getAttribute('src') || img.getAttribute('data-src') || '';
    src = src.replace(/[?&]_t=\d+/, '');
    var sep = src.indexOf('?') > -1 ? '&' : '?';
    img.setAttribute('src', src + sep + '_t=' + Date.now());
  }

  function initCaptcha() {
    var img = $('.captcha-img');
    if (img) img.addEventListener('click', refreshCaptcha);
  }

  function showPageTransition() {
    var bar = $('.page-loading');
    if (!bar) {
      bar = document.createElement('div');
      bar.className = 'page-loading';
      bar.innerHTML = '<div class="page-loading-bar"></div>';
      document.body.appendChild(bar);
    }
    bar.style.display = 'block';
    var inner = $('.page-loading-bar', bar);
    if (inner) {
      inner.style.width = '0%';
      void inner.offsetWidth;
      inner.style.width = '90%';
    }
  }

  function hidePageTransition() {
    var bar = $('.page-loading');
    if (!bar) return;
    var inner = $('.page-loading-bar', bar);
    if (inner) {
      inner.style.width = '100%';
    }
    setTimeout(function () {
      bar.style.display = 'none';
      if (inner) inner.style.width = '0%';
    }, 400);
  }

  function initAjaxForms() {
    document.addEventListener('submit', function (e) {
      var form = e.target.closest('form[data-ajax]');
      if (!form) return;
      e.preventDefault();
      var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
      var originalText = submitBtn ? (submitBtn.textContent || submitBtn.value) : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        var loadingText = submitBtn.getAttribute('data-loading') || (originalText + '...');
        if (submitBtn.tagName === 'INPUT') submitBtn.value = loadingText;
        else submitBtn.textContent = loadingText;
      }
      showPageTransition();
      var formData = new FormData(form);
      var method = (form.getAttribute('method') || 'POST').toUpperCase();
      var action = form.getAttribute('action') || window.location.href;
      var body, headers = {};
      if (method === 'GET') {
        var params = new URLSearchParams(formData);
        action = action + (action.indexOf('?') > -1 ? '&' : '?') + params.toString();
        body = null;
      } else {
        body = new URLSearchParams(formData);
        headers['Content-Type'] = 'application/x-www-form-urlencoded';
      }
      fetch(action, { method: method, headers: headers, body: body })
        .then(function (res) {
          return res.json().then(function (data) {
            return { status: res.status, ok: res.ok, data: data };
          }).catch(function () {
            return { status: res.status, ok: res.ok, data: null };
          });
        })
        .then(function (result) {
          hidePageTransition();
          if (result.ok && result.data) {
            if (result.data.code === 200 || result.data.code == 200) {
              showSuccess(result.data.msg || '操作成功');
              if (result.data.data && result.data.data.redirect) {
                setTimeout(function () { window.location.href = result.data.data.redirect; }, 1000);
              } else if (result.data.data && result.data.data.reload) {
                setTimeout(function () { window.location.reload(); }, 1000);
              }
            } else {
              showError(result.data.msg || '操作失败');
            }
          } else {
            showError('请求失败 (' + result.status + ')');
          }
        })
        .catch(function () {
          hidePageTransition();
          showError('网络错误，请稍后重试');
        })
        .finally(function () {
          if (submitBtn) {
            submitBtn.disabled = false;
            if (submitBtn.tagName === 'INPUT') submitBtn.value = originalText;
            else submitBtn.textContent = originalText;
          }
        });
    });
  }

  function validateEmail(v) {
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return { valid: false, message: '请输入有效的邮箱地址' };
    return { valid: true };
  }

  function validatePassword(v, input) {
    var min = 6;
    if (input) {
      var attr = input.getAttribute('data-min-length');
      if (attr) min = parseInt(attr, 10) || 6;
    }
    if (v.length < min) return { valid: false, message: '密码长度至少 ' + min + ' 位' };
    return { valid: true };
  }

  function validatePhone(v) {
    if (!/^1[3-9]\d{9}$/.test(v)) return { valid: false, message: '请输入有效的手机号码' };
    return { valid: true };
  }

  function validateUsername(v) {
    if (v.length < 3) return { valid: false, message: '用户名长度至少 3 个字符' };
    if (v.length > 20) return { valid: false, message: '用户名长度不能超过 20 个字符' };
    return { valid: true };
  }

  function validateLogin(v) {
    if (v.length < 1) return { valid: false, message: '请输入用户名、邮箱或手机号' };
    return { valid: true };
  }

  function validateInput(input) {
    var type = input.getAttribute('data-validate');
    var value = input.value;
    var result;
    switch (type) {
      case 'login': result = validateLogin(value); break;
      case 'email': result = validateEmail(value); break;
      case 'password': result = validatePassword(value, input); break;
      case 'phone': result = validatePhone(value); break;
      case 'username': result = validateUsername(value); break;
      default: return null;
    }
    var existing = input.parentNode.querySelector('.form-feedback');
    if (existing) existing.parentNode.removeChild(existing);
    input.classList.remove('is-invalid', 'is-valid');
    if (value.length === 0) return result;
    if (result.valid) {
      input.classList.add('is-valid');
    } else {
      input.classList.add('is-invalid');
      var fb = document.createElement('div');
      fb.className = 'form-feedback error';
      fb.textContent = result.message;
      input.parentNode.appendChild(fb);
    }
    return result;
  }

  function bindValidationEvents(input) {
    input.addEventListener('blur', function () { validateInput(input); });
    input.addEventListener('input', function () {
      if (input.classList.contains('is-invalid') || input.classList.contains('is-valid')) {
        validateInput(input);
      }
    });
  }

  function initFormValidation() {
    $$('form[data-validate]').forEach(function (form) {
      var inputs = $$('input[data-validate]', form);
      inputs.forEach(function (input) { bindValidationEvents(input); });
      form.addEventListener('submit', function (e) {
        var all = $$('input[data-validate]', form);
        var hasError = false;
        all.forEach(function (inp) {
          var r = validateInput(inp);
          if (r && !r.valid) hasError = true;
        });
        if (hasError) {
          e.preventDefault();
          showWarning('请修正表单中的错误');
        }
      });
    });
    $$('input[data-validate]').forEach(function (input) {
      if (!input.closest('form[data-validate]')) bindValidationEvents(input);
    });
  }

  function initPageLoadingSpinner() {
    document.addEventListener('click', function (e) {
      var link = e.target.closest('a');
      if (!link) return;
      var href = link.getAttribute('href');
      if (!href) return;
      if (href === '#' || href === '') return;
      if (href.indexOf('javascript:') === 0) return;
      if (link.getAttribute('target') === '_blank') return;
      if (link.getAttribute('download') !== null) return;
      if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return;
      if (link.getAttribute('data-no-spinner') !== null) return;
      if (href.indexOf('://') > -1 && href.indexOf(window.location.origin) !== 0) return;
      showPageTransition();
    });
    window.addEventListener('pageshow', function () { hidePageTransition(); });
  }

  function initScrollAnimations() {
    var els = $$('[data-animate]');
    if (!els.length) return;

    function check() {
      var h = window.innerHeight;
      els.forEach(function (el) {
        if (el.classList.contains('animated')) return;
        var r = el.getBoundingClientRect();
        var th = el.getAttribute('data-animate-threshold');
        var offset = th ? parseInt(th, 10) : 100;
        if (r.top < h - offset && r.bottom > 0) {
          el.classList.add('animated');
          var anim = el.getAttribute('data-animate') || 'fadeIn';
          el.style.animation = anim + ' 0.6s ease forwards';
        }
      });
    }

    check();
    var ticking = false;
    window.addEventListener('scroll', function () {
      if (!ticking) {
        window.requestAnimationFrame(function () {
          check();
          ticking = false;
        });
        ticking = true;
      }
    });
  }

  function init() {
    initTheme();
    bindThemeToggle();
    initSmoothScroll();
    initMobileNav();
    initSidebarActive();
    initCaptcha();
    initAjaxForms();
    initAnnouncement();
    initFormValidation();
    initPageLoadingSpinner();
    initScrollAnimations();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.QEEFG = {
    showToast: showToast,
    showError: showError,
    showSuccess: showSuccess,
    showWarning: showWarning,
    showPageTransition: showPageTransition,
    hidePageTransition: hidePageTransition,
    refreshCaptcha: refreshCaptcha,
    toggleTheme: toggleTheme,
    showAnnouncement: showAnnouncement,
    showAnnouncementPopup: showAnnouncement,
    hideAnnouncement: hideAnnouncement,
    $: $,
    $$: $$
  };

})();