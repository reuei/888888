(function () {
  'use strict';

  var $ = function (sel, ctx) {
    return (ctx || document).querySelector(sel);
  };

  var $$ = function (sel, ctx) {
    return Array.prototype.slice.call((ctx || document).querySelectorAll(sel));
  };

  function setCookie(name, value, days) {
    var expires = '';
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + encodeURIComponent(value || '') + expires + '; path=/; SameSite=Lax';
  }

  function getCookie(name) {
    var nameEQ = name + '=';
    var ca = document.cookie ? document.cookie.split(';') : [];
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) === ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) === 0) {
        try {
          return decodeURIComponent(c.substring(nameEQ.length, c.length));
        } catch (e) {
          return c.substring(nameEQ.length, c.length);
        }
      }
    }
    return null;
  }

  function deleteCookie(name) {
    document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Lax';
  }

  var ICONS = {
    success: '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
    error: '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    warning: '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    info: '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    loading: '<svg viewBox="0 0 50 50" width="22" height="22" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><circle cx="25" cy="25" r="20" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-dasharray="80 120" stroke-dashoffset="0"><animateTransform attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.8s" repeatCount="indefinite"/></circle></svg>',
    sun: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>',
    moon: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>'
  };

  var LS = {
    THEME: 'qeefg_theme',
    ANN: 'qeefg_ann_hidden',
    LANG: 'qeefg_lang'
  };

  var toastStack = [];

  function getToastContainer() {
    var c = $('#toast-center');
    if (!c) {
      c = document.createElement('div');
      c.id = 'toast-center';
      c.className = 'toast-center';
      c.style.cssText = 'position:fixed;top:24px;left:50%;transform:translateX(-50%);z-index:99999;display:flex;flex-direction:column;align-items:center;gap:12px;pointer-events:none;';
      document.body.appendChild(c);
    }
    return c;
  }

  function removeToast(toastEl, wrapper) {
    if (toastEl._removed) return;
    toastEl._removed = true;
    clearTimeout(toastEl._timer);
    toastEl.classList.remove('show');
    toastEl.classList.add('hide');
    var idx = toastStack.indexOf(wrapper);
    if (idx > -1) toastStack.splice(idx, 1);
    setTimeout(function () {
      if (wrapper && wrapper.parentNode) wrapper.parentNode.removeChild(wrapper);
    }, 400);
  }

  function showToast(msg, type, duration) {
    type = type || 'info';
    duration = typeof duration === 'number' ? duration : 3000;
    if (!msg) return null;
    var container = getToastContainer();
    var wrapper = document.createElement('div');
    wrapper.className = 'toast-wrapper';
    wrapper.style.cssText = 'pointer-events:auto;';
    var toastEl = document.createElement('div');
    toastEl.className = 'toast toast-' + type;
    toastEl.style.cssText = [
      'display:flex;align-items:center;gap:12px;min-width:260px;max-width:420px;padding:14px 18px;',
      'border-radius:10px;box-shadow:0 6px 24px rgba(0,0,0,0.12);cursor:pointer;',
      'background:#fff;color:#1f2937;font-size:14px;line-height:1.5;font-family:inherit;',
      'border:1px solid rgba(0,0,0,0.06);opacity:0;transform:translateY(-20px);',
      'transition:opacity 0.32s ease,transform 0.32s cubic-bezier(0.16,1,0.3,1);',
      'backdrop-filter:blur(8px);'
    ].join('');
    if (type === 'success') toastEl.style.color = '#059669';
    if (type === 'error') toastEl.style.color = '#dc2626';
    if (type === 'warning') toastEl.style.color = '#d97706';
    if (type === 'info') toastEl.style.color = '#2563eb';
    if (type === 'loading') toastEl.style.color = '#6366f1';
    var iconWrap = document.createElement('span');
    iconWrap.className = 'toast-icon';
    iconWrap.style.cssText = [
      'display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;',
      'width:28px;height:28px;border-radius:50%;background:currentColor;opacity:0.12;'
    ].join('');
    var icon = document.createElement('span');
    icon.style.cssText = 'display:inline-flex;align-items:center;justify-content:center;';
    icon.innerHTML = ICONS[type] || ICONS.info;
    iconWrap.appendChild(icon);
    var body = document.createElement('span');
    body.className = 'toast-body';
    body.style.cssText = 'flex:1;min-width:0;word-break:break-word;color:#1f2937;font-weight:500;';
    if (typeof msg === 'string') {
      body.textContent = msg;
    } else {
      body.appendChild(msg);
    }
    toastEl.appendChild(iconWrap);
    toastEl.appendChild(body);
    wrapper.appendChild(toastEl);
    container.appendChild(wrapper);
    toastStack.push(wrapper);
    void toastEl.offsetWidth;
    requestAnimationFrame(function () {
      toastEl.classList.add('show');
      toastEl.style.opacity = '1';
      toastEl.style.transform = 'translateY(0)';
    });
    if (type !== 'loading' && duration > 0) {
      var timer = setTimeout(function () {
        removeToast(toastEl, wrapper);
      }, duration);
      toastEl._timer = timer;
    }
    toastEl._wrapper = wrapper;
    toastEl.addEventListener('click', function (e) {
      e.stopPropagation();
      clearTimeout(toastEl._timer);
      removeToast(toastEl, wrapper);
    });
    return { toastEl: toastEl, wrapper: wrapper, close: function () { removeToast(toastEl, wrapper); } };
  }

  function showSuccess(msg, duration) { return showToast(msg, 'success', duration); }
  function showError(msg, duration) { return showToast(msg, 'error', duration); }
  function showWarning(msg, duration) { return showToast(msg, 'warning', duration); }
  function showInfo(msg, duration) { return showToast(msg, 'info', duration); }

  var loadingInstance = null;

  function showLoading(text) {
    if (loadingInstance) return loadingInstance;
    var overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.className = 'loading-overlay';
    overlay.style.cssText = [
      'position:fixed;inset:0;z-index:999998;display:flex;align-items:center;justify-content:center;',
      'background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);opacity:0;',
      'transition:opacity 0.25s ease;'
    ].join('');
    var box = document.createElement('div');
    box.className = 'loading-box';
    box.style.cssText = [
      'display:flex;flex-direction:column;align-items:center;gap:18px;padding:36px 44px;',
      'background:#fff;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,0.2);',
      'transform:scale(0.92);transition:transform 0.25s cubic-bezier(0.16,1,0.3,1);',
      'min-width:180px;'
    ].join('');
    var spinner = document.createElement('div');
    spinner.className = 'dual-ring';
    spinner.style.cssText = [
      'position:relative;width:56px;height:56px;',
    ].join('');
    var ring1 = document.createElement('div');
    ring1.style.cssText = [
      'position:absolute;inset:0;border:4px solid rgba(99,102,241,0.18);',
      'border-top-color:#6366f1;border-radius:50%;',
      'animation:loading-spin 1s linear infinite;'
    ].join('');
    var ring2 = document.createElement('div');
    ring2.style.cssText = [
      'position:absolute;inset:8px;border:4px solid rgba(236,72,153,0.18);',
      'border-top-color:#ec4899;border-radius:50%;',
      'animation:loading-spin 0.7s linear infinite reverse;'
    ].join('');
    spinner.appendChild(ring1);
    spinner.appendChild(ring2);
    var label = document.createElement('div');
    label.className = 'loading-text';
    label.style.cssText = 'font-size:14px;color:#374151;font-weight:500;letter-spacing:0.2px;';
    label.textContent = text || '请稍候...';
    box.appendChild(spinner);
    box.appendChild(label);
    overlay.appendChild(box);
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
    var styleId = 'loading-spin-style';
    if (!document.getElementById(styleId)) {
      var st = document.createElement('style');
      st.id = styleId;
      st.textContent = '@keyframes loading-spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}';
      document.head.appendChild(st);
    }
    void overlay.offsetWidth;
    requestAnimationFrame(function () {
      overlay.style.opacity = '1';
      box.style.transform = 'scale(1)';
    });
    loadingInstance = {
      overlay: overlay,
      hide: function () { hideLoading(); }
    };
    return loadingInstance;
  }

  function hideLoading() {
    if (!loadingInstance) return;
    var overlay = loadingInstance.overlay;
    var box = overlay ? overlay.querySelector('.loading-box') : null;
    if (overlay) {
      overlay.style.opacity = '0';
      if (box) box.style.transform = 'scale(0.92)';
    }
    document.body.style.overflow = '';
    setTimeout(function () {
      if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
    }, 280);
    loadingInstance = null;
  }

  function initTheme() {
    var saved = null;
    try {
      saved = localStorage.getItem(LS.THEME);
    } catch (e) {}
    var cookieSaved = getCookie(LS.THEME);
    var applied = saved || cookieSaved;
    if (applied === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    } else if (applied === 'light') {
      document.documentElement.setAttribute('data-theme', 'light');
    } else {
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.setAttribute('data-theme', 'dark');
      } else {
        document.documentElement.setAttribute('data-theme', 'light');
      }
    }
    var themeIcon = $('#themeIcon');
    if (themeIcon) {
      var cur = document.documentElement.getAttribute('data-theme');
      themeIcon.innerHTML = cur === 'dark' ? ICONS.sun : ICONS.moon;
    }
  }

  function applyTheme(theme) {
    if (theme === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    } else {
      document.documentElement.setAttribute('data-theme', 'light');
    }
    try {
      localStorage.setItem(LS.THEME, theme);
    } catch (e) {}
    setCookie(LS.THEME, theme, 365);
    var themeIcon = $('#themeIcon');
    if (themeIcon) {
      themeIcon.innerHTML = theme === 'dark' ? ICONS.sun : ICONS.moon;
    }
  }

  function toggleTheme() {
    var cur = document.documentElement.getAttribute('data-theme');
    var next = cur === 'dark' ? 'light' : 'dark';
    applyTheme(next);
  }

  function bindThemeToggle() {
    var themeBtn = $('#themeBtn');
    if (themeBtn) {
      themeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
        var themeMenu = $('#themeMenu');
        if (themeMenu) {
          themeMenu.classList.toggle('show');
          closeOtherMenus(themeMenu);
        } else {
          toggleTheme();
        }
      });
    }
    var themeToggle = $('.theme-toggle');
    if (themeToggle && !themeBtn) {
      themeToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
        toggleTheme();
      });
    }
    var themeMenu = $('#themeMenu');
    if (themeMenu) {
      $$('button[data-theme], [data-theme]', themeMenu).forEach(function (opt) {
        opt.addEventListener('click', function (e) {
          e.stopPropagation();
          e.preventDefault();
          var t = opt.getAttribute('data-theme');
          if (t === 'dark' || t === 'light') {
            applyTheme(t);
          } else if (t === 'system') {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
              applyTheme('dark');
            } else {
              applyTheme('light');
            }
          }
          themeMenu.classList.remove('show');
        });
      });
    }
    document.addEventListener('keydown', function (e) {
      if (e.ctrlKey && (e.key === 't' || e.key === 'T')) {
        e.preventDefault();
        toggleTheme();
      }
    });
  }

  function initSmoothScroll() {
    if (document.documentElement) {
      document.documentElement.style.scrollBehavior = 'smooth';
    }
    document.addEventListener('click', function (e) {
      var link = e.target.closest('a[href^="#"]');
      if (!link) return;
      var id = link.getAttribute('href');
      if (!id || id === '#' || id.length < 2) return;
      var target = document.querySelector(id);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }

  function closeMobileDrawer() {
    var btn = $('#hamburgerBtn');
    var nav = $('#mobileNav');
    var overlay = $('.hamburger-overlay');
    if (nav) nav.classList.remove('show');
    if (btn) btn.classList.remove('open');
    if (overlay) {
      overlay.style.visibility = 'hidden';
      overlay.style.opacity = '0';
    }
  }

  function openMobileDrawer() {
    var btn = $('#hamburgerBtn');
    var nav = $('#mobileNav');
    var overlay = $('.hamburger-overlay');
    if (nav) nav.classList.add('show');
    if (btn) btn.classList.add('open');
    if (overlay) {
      overlay.style.visibility = 'visible';
      overlay.style.opacity = '1';
    }
  }

  function isHomePage() {
    var path = window.location.pathname;
    return path === '/' || path === '' || path === '/index.php' || /^\/index(\.php)?$/.test(path);
  }

  function initMobileNav() {
    var btn = $('#hamburgerBtn') || $('.hamburger-btn');
    var nav = $('#mobileNav') || $('.mobile-nav');
    if (!btn || !nav) return;
    var overlay = $('.hamburger-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'hamburger-overlay';
      overlay.style.cssText = [
        'position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:998;',
        'visibility:hidden;opacity:0;transition:opacity 0.3s ease,visibility 0s linear 0.3s;'
      ].join('');
      document.body.appendChild(overlay);
    }
    overlay.addEventListener('click', function () {
      closeMobileDrawer();
    });
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      e.preventDefault();
      var isOpen = nav.classList.contains('show');
      if (isOpen) {
        closeMobileDrawer();
      } else {
        openMobileDrawer();
      }
    });
    $$('#mobileNav a.link, .mobile-nav a.link, #mobileNav a, .mobile-nav a').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var href = a.getAttribute('href') || '';
        var handleSpecial = function (targetHash) {
          e.preventDefault();
          closeMobileDrawer();
          if (isHomePage()) {
            var target = document.querySelector(targetHash);
            if (target) {
              setTimeout(function () {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
              }, 200);
            }
          } else {
            setTimeout(function () {
              window.location.href = '/' + targetHash;
            }, 200);
          }
          return true;
        };
        if (href.indexOf('/#features') === 0) {
          handleSpecial('#features');
          return;
        }
        if (href === '/platform' || /平台能力/.test(a.textContent || '')) {
          handleSpecial('#features');
          return;
        }
        closeMobileDrawer();
      });
    });
  }

  function bindPlatformLinks() {
    $$('a').forEach(function (a) {
      var href = a.getAttribute('href') || '';
      var text = a.textContent || '';
      var isPlatformLink = href === '/platform' || href === '/platform/' || /平台能力/.test(text);
      if (!isPlatformLink) return;
      a.addEventListener('click', function (e) {
        if (isHomePage()) {
          var target = document.getElementById('features');
          if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        } else {
          e.preventDefault();
          window.location.href = '/#features';
        }
      });
    });
  }

  function showAnnouncement() {
    var modal = $('#announcementModal') || $('.announcement-modal') || $('.arco-modal-mask');
    if (!modal) return;
    modal.classList.add('show');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
  }

  function hideAnnouncement() {
    var modal = $('#announcementModal') || $('.announcement-modal') || $('.arco-modal-mask');
    if (!modal) return;
    modal.classList.remove('show');
    document.body.style.overflow = '';
  }

  function initAnnouncement() {
    var modal = $('#announcementModal') || $('.announcement-modal') || $('.arco-modal-mask');
    if (!modal) return;
    var hidden = null;
    try {
      hidden = localStorage.getItem(LS.ANN);
    } catch (e) {}
    if (hidden) {
      try {
        var p = typeof hidden === 'object' ? hidden : JSON.parse(hidden);
        if (p && p.timestamp && (Date.now() - p.timestamp) < 3600000) {
          return;
        }
      } catch (e) {}
    }
    setTimeout(function () {
      showAnnouncement();
    }, 300);
    var confirmBtn = $('#amConfirmBtn', modal);
    if (confirmBtn) {
      confirmBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
        try {
          localStorage.setItem(LS.ANN, JSON.stringify({ timestamp: Date.now() }));
        } catch (e) {}
        hideAnnouncement();
      });
    }
    var closeBtn = $('.am-close', modal) || $('#amCloseBtn', modal) || $('.close', modal);
    if (closeBtn) {
      closeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
        hideAnnouncement();
      });
    }
    $$('.arco-modal-close-btn, [data-modal-close]', modal).forEach(function (el) {
      el.addEventListener('click', function (e) {
        e.stopPropagation();
        hideAnnouncement();
      });
    });
    var wrappers = $$('.arco-modal-wrapper, .modal-dialog, .am-dialog', modal);
    if (wrappers.length > 0) {
      modal.addEventListener('click', function (e) {
        for (var i = 0; i < wrappers.length; i++) {
          if (wrappers[i].contains(e.target)) return;
        }
        hideAnnouncement();
      });
    } else {
      modal.addEventListener('click', function (e) {
        if (e.target === modal) hideAnnouncement();
      });
    }
  }

  function initCaptchaRefresh() {
    $$('img.captcha-img').forEach(function (img) {
      img.style.cursor = 'pointer';
      img.title = img.title || '点击刷新验证码';
      img.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        refreshCaptcha(img);
      });
    });
    $$('.captcha-refresh, .refresh-captcha').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var img = $('.captcha-img');
        if (img) refreshCaptcha(img);
      });
    });
  }

  function refreshCaptcha(img) {
    img = img || $('.captcha-img');
    if (!img) return;
    var src = img.getAttribute('src') || img.getAttribute('data-src') || '';
    src = src.replace(/[?&](_t|t|_v|v|timestamp)=[^&]*/g, '');
    var sep = src.indexOf('?') > -1 ? '&' : '?';
    img.setAttribute('src', src + sep + 't=' + Date.now());
  }

  var VALIDATORS = {
    username: function (v) {
      if (!v || v.length === 0) return { valid: false, message: '请输入用户名' };
      if (v.length < 3) return { valid: false, message: '用户名长度至少 3 个字符' };
      if (v.length > 20) return { valid: false, message: '用户名长度不能超过 20 个字符' };
      if (!/^[a-zA-Z0-9_]+$/.test(v)) return { valid: false, message: '用户名只能包含字母、数字和下划线' };
      return { valid: true, message: '' };
    },
    login: function (v) {
      if (!v || v.length === 0) return { valid: false, message: '请输入用户名、邮箱或手机号' };
      return { valid: true, message: '' };
    },
    email: function (v) {
      if (!v || v.length === 0) return { valid: false, message: '请输入邮箱地址' };
      if (!/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,63}$/.test(v)) return { valid: false, message: '请输入有效的邮箱地址' };
      return { valid: true, message: '' };
    },
    phone: function (v) {
      if (!v || v.length === 0) return { valid: false, message: '请输入手机号码' };
      if (!/^1[3-9]\d{9}$/.test(v)) return { valid: false, message: '请输入有效的 11 位手机号码' };
      return { valid: true, message: '' };
    },
    password: function (v, input) {
      if (!v || v.length === 0) return { valid: false, message: '请输入密码', strength: 0 };
      var minLen = 6;
      if (input) {
        var attr = input.getAttribute('data-min-length');
        if (attr) minLen = parseInt(attr, 10) || 6;
      }
      if (v.length < minLen) return { valid: false, message: '密码长度至少 ' + minLen + ' 位', strength: 0 };
      var strength = calcPasswordStrength(v);
      if (v.length < 8) return { valid: true, message: '', strength: strength };
      return { valid: true, message: '', strength: strength };
    },
    confirm: function (v) {
      if (!v || v.length === 0) return { valid: false, message: '请再次输入密码' };
      var pwdInput = document.getElementById('password');
      var pwd = pwdInput ? pwdInput.value : '';
      if (pwd && v !== pwd) return { valid: false, message: '两次输入的密码不一致' };
      return { valid: true, message: '' };
    },
    captcha: function (v) {
      if (!v || v.length === 0) return { valid: false, message: '请输入验证码' };
      if (v.length < 4) return { valid: false, message: '验证码至少 4 个字符' };
      if (v.length > 8) return { valid: false, message: '验证码格式不正确' };
      return { valid: true, message: '' };
    }
  };

  function calcPasswordStrength(v) {
    if (!v) return 0;
    var score = 0;
    if (/[a-z]/.test(v)) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^a-zA-Z0-9]/.test(v)) score++;
    if (v.length >= 10) score++;
    if (v.length >= 14) score++;
    return Math.min(score, 5);
  }

  function getFieldWrapper(input) {
    var parent = input.parentNode;
    var tries = 0;
    while (parent && tries < 5) {
      if (parent.classList && (
        parent.classList.contains('form-group') ||
        parent.classList.contains('field-wrapper') ||
        parent.classList.contains('input-group') ||
        parent.classList.contains('mb-3') ||
        parent.tagName === 'FIELDSET'
      )) {
        return parent;
      }
      parent = parent.parentNode;
      tries++;
    }
    return input.parentNode;
  }

  function ensureFieldMsg(input) {
    var next = input.nextElementSibling;
    if (next && next.classList && next.classList.contains('field-msg')) {
      return next;
    }
    var msg = document.createElement('div');
    msg.className = 'field-msg';
    msg.style.cssText = [
      'margin-top:6px;font-size:12px;line-height:1.5;min-height:16px;',
      'transition:color 0.2s ease;'
    ].join('');
    if (input.nextSibling) {
      input.parentNode.insertBefore(msg, input.nextSibling);
    } else {
      input.parentNode.appendChild(msg);
    }
    return msg;
  }

  function ensureStrengthBar(input) {
    if (input.getAttribute('data-strength-bound')) return document.getElementById(input.getAttribute('data-strength-bound'));
    var bar = document.createElement('div');
    bar.className = 'password-strength';
    bar.style.cssText = [
      'margin-top:8px;display:flex;gap:4px;width:100%;'
    ].join('');
    var segments = [];
    for (var i = 0; i < 4; i++) {
      var s = document.createElement('div');
      s.className = 'strength-seg seg-' + i;
      s.style.cssText = [
        'flex:1;height:4px;border-radius:2px;background:#e5e7eb;',
        'transition:background 0.25s ease;'
      ].join('');
      bar.appendChild(s);
      segments.push(s);
    }
    var label = document.createElement('div');
    label.className = 'strength-text';
    label.style.cssText = 'margin-top:6px;font-size:12px;color:#6b7280;min-height:16px;';
    var wrap = document.createElement('div');
    wrap.className = 'strength-wrap';
    wrap.appendChild(bar);
    wrap.appendChild(label);
    wrap.id = 'strength_' + (input.id || ('pw_' + Math.random().toString(36).slice(2, 8)));
    input.setAttribute('data-strength-bound', wrap.id);
    if (input.nextSibling) {
      var msg = input.nextElementSibling;
      if (msg && msg.classList && msg.classList.contains('field-msg')) {
        input.parentNode.insertBefore(wrap, msg);
      } else {
        input.parentNode.insertBefore(wrap, input.nextSibling);
      }
    } else {
      input.parentNode.appendChild(wrap);
    }
    return wrap;
  }

  function setStrengthVisual(wrap, strength) {
    if (!wrap) return;
    var segs = $$('.strength-seg', wrap);
    var label = $('.strength-text', wrap);
    var level = 0;
    var color = '#e5e7eb';
    var text = '';
    if (strength <= 1) {
      level = 1; color = '#ef4444'; text = '弱';
    } else if (strength <= 2) {
      level = 2; color = '#f59e0b'; text = '中';
    } else if (strength <= 3) {
      level = 3; color = '#10b981'; text = '强';
    } else {
      level = 4; color = '#059669'; text = '非常强';
    }
    for (var i = 0; i < segs.length; i++) {
      segs[i].style.background = i < level ? color : '#e5e7eb';
    }
    if (label) {
      label.textContent = text;
      label.style.color = color;
    }
  }

  function clearStrengthVisual(input) {
    var id = input.getAttribute('data-strength-bound');
    if (!id) return;
    var wrap = document.getElementById(id);
    setStrengthVisual(wrap, 0);
    var label = wrap ? $('.strength-text', wrap) : null;
    if (label) label.textContent = '';
  }

  function validateInput(input) {
    var type = input.getAttribute('data-validate');
    if (!type || !VALIDATORS[type]) return { valid: true, skipped: true };
    var value = input.value;
    var fn = VALIDATORS[type];
    var result = fn(value, input);
    var wrapper = getFieldWrapper(input);
    var msgEl = ensureFieldMsg(input);
    wrapper.classList.remove('field-valid', 'field-invalid');
    input.classList.remove('is-valid', 'is-invalid');
    if (type === 'password') {
      var strengthWrap = ensureStrengthBar(input);
      if (value.length > 0) {
        setStrengthVisual(strengthWrap, result.strength || 0);
      } else {
        clearStrengthVisual(input);
      }
    }
    if (type === 'confirm' || type === 'password') {
      var confirmInput = document.querySelector('input[data-validate="confirm"]');
      if (confirmInput && confirmInput !== input && confirmInput.value.length > 0) {
        var r2 = VALIDATORS.confirm(confirmInput.value);
        var w2 = getFieldWrapper(confirmInput);
        var m2 = ensureFieldMsg(confirmInput);
        w2.classList.remove('field-valid', 'field-invalid');
        confirmInput.classList.remove('is-valid', 'is-invalid');
        if (r2.valid) {
          w2.classList.add('field-valid');
          confirmInput.classList.add('is-valid');
          m2.textContent = '';
          m2.style.color = '#10b981';
        } else {
          w2.classList.add('field-invalid');
          confirmInput.classList.add('is-invalid');
          m2.textContent = r2.message;
          m2.style.color = '#ef4444';
        }
      }
    }
    if (value.length === 0 && type !== 'login') {
      msgEl.textContent = '';
      msgEl.style.color = '#6b7280';
      return { valid: true, empty: true };
    }
    if (result.valid) {
      wrapper.classList.add('field-valid');
      input.classList.add('is-valid');
      msgEl.textContent = '';
      msgEl.style.color = '#10b981';
    } else {
      wrapper.classList.add('field-invalid');
      input.classList.add('is-invalid');
      msgEl.textContent = result.message || '';
      msgEl.style.color = '#ef4444';
    }
    return result;
  }

  function validateAll(scope) {
    scope = scope || document;
    var inputs = $$('input[data-validate], select[data-validate], textarea[data-validate]', scope);
    var firstInvalid = null;
    var allValid = true;
    inputs.forEach(function (input) {
      var result = validateInput(input);
      if (!result.valid) {
        allValid = false;
        if (!firstInvalid) firstInvalid = input;
      }
    });
    if (firstInvalid) {
      try {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(function () { firstInvalid.focus && firstInvalid.focus(); }, 300);
      } catch (e) {}
    }
    return allValid;
  }

  function bindValidationInput(input) {
    if (input._validateBound) return;
    input._validateBound = true;
    var type = input.getAttribute('data-validate');
    input.addEventListener('input', function () {
      if (type === 'password' || input.classList.contains('is-invalid') || input.classList.contains('is-valid')) {
        validateInput(input);
      }
    });
    input.addEventListener('blur', function () {
      validateInput(input);
    });
    input.addEventListener('change', function () {
      validateInput(input);
    });
  }

  function initRealTimeValidate() {
    $$('input[data-validate], select[data-validate], textarea[data-validate]').forEach(function (input) {
      bindValidationInput(input);
    });
    $$('form').forEach(function (form) {
      form.addEventListener('submit', function (e) {
        var validatable = $$('input[data-validate], select[data-validate], textarea[data-validate]', form);
        if (validatable.length === 0) return;
        if (form.getAttribute('data-ajax') === 'true') return;
        var ok = validateAll(form);
        if (!ok) {
          e.preventDefault();
          e.stopPropagation();
          showError('请检查表单中的错误并重试');
          return false;
        }
      });
    });
  }

  function bindAjaxForms() {
    document.addEventListener('submit', function (e) {
      var form = e.target.closest('form[data-ajax="true"]');
      if (!form) form = e.target.closest('form[data-ajax]');
      if (!form) return;
      e.preventDefault();
      e.stopPropagation();
      var ok = true;
      var hasValidatable = $$('input[data-validate], select[data-validate], textarea[data-validate]', form).length > 0;
      if (hasValidatable) {
        ok = validateAll(form);
      }
      if (!ok) {
        showError('请修正表单中的错误后再提交');
        return false;
      }
      var submitBtn = form.querySelector('button[type="submit"], input[type="submit"], .submit-btn');
      var originalText = '';
      var originalHTML = '';
      if (submitBtn) {
        submitBtn.disabled = true;
        if (submitBtn.tagName === 'INPUT') {
          originalText = submitBtn.value;
          submitBtn.value = submitBtn.getAttribute('data-loading') || '提交中...';
        } else {
          originalHTML = submitBtn.innerHTML;
          originalText = submitBtn.textContent;
          submitBtn.innerHTML = ICONS.loading + ' ' + (submitBtn.getAttribute('data-loading') || '提交中...');
        }
      }
      showLoading(submitBtn ? '' : '提交中...');
      var method = (form.getAttribute('method') || 'POST').toUpperCase();
      var action = form.getAttribute('action') || window.location.href;
      var formData = new FormData(form);
      var fetchOptions = {
        method: method,
        credentials: 'same-origin',
        headers: {
          'Accept': 'application/json, text/plain, */*',
          'X-Requested-With': 'XMLHttpRequest'
        }
      };
      if (method === 'GET') {
        var params = new URLSearchParams(formData).toString();
        action = action + (action.indexOf('?') > -1 ? '&' : '?') + params;
      } else {
        fetchOptions.body = formData;
      }
      fetch(action, fetchOptions)
        .then(function (res) {
          return res.text().then(function (txt) {
            var data = null;
            try {
              data = JSON.parse(txt);
            } catch (err) {
              data = { code: -1, msg: '响应格式错误', raw: txt };
            }
            return { status: res.status, ok: res.ok, data: data };
          }).catch(function () {
            return { status: res.status, ok: res.ok, data: { code: -1, msg: '读取响应失败' } };
          });
        })
        .then(function (result) {
          hideLoading();
          if (submitBtn) {
            submitBtn.disabled = false;
            if (submitBtn.tagName === 'INPUT') {
              submitBtn.value = originalText;
            } else {
              submitBtn.innerHTML = originalHTML;
            }
          }
          var data = result.data || {};
          if (data.code === 200 || data.code === 0 || data.code === '200' || data.code === '0') {
            showSuccess(data.msg || '操作成功');
            if (data.data && typeof data.data === 'object') {
              if (data.data.redirect) {
                setTimeout(function () {
                  window.location.href = data.data.redirect;
                }, 1200);
              } else if (data.data.reload) {
                setTimeout(function () {
                  window.location.reload();
                }, 1200);
              }
            }
          } else {
            showError(data.msg || ('操作失败 (code: ' + (data.code || result.status) + ')'));
            if ($$('.captcha-img', form).length > 0) {
              refreshCaptcha($('.captcha-img', form));
            } else if ($('.captcha-img')) {
              refreshCaptcha();
            }
            var pwdInput = document.querySelector('input[data-validate="password"]', form);
            if (pwdInput) {
              pwdInput.value = '';
            }
          }
        })
        .catch(function (err) {
          hideLoading();
          if (submitBtn) {
            submitBtn.disabled = false;
            if (submitBtn.tagName === 'INPUT') {
              submitBtn.value = originalText;
            } else {
              submitBtn.innerHTML = originalHTML;
            }
          }
          showError('网络错误，请检查网络后重试');
        });
      return false;
    });
  }

  function initEntranceAnimations() {
    var targets = $$('[data-animate]');
    if (!('IntersectionObserver' in window)) {
      targets.forEach(function (el) {
        var name = el.getAttribute('data-animate') || 'fadeInUp';
        el.classList.add('animate-' + name);
      });
      return;
    }
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var name = el.getAttribute('data-animate') || 'fadeInUp';
          el.classList.add('animate-' + name);
          observer.unobserve(el);
        }
      });
    }, {
      root: null,
      threshold: 0.1,
      rootMargin: '0px 0px -40px 0px'
    });
    targets.forEach(function (el) {
      el.style.willChange = 'transform,opacity';
      observer.observe(el);
    });
  }

  function closeOtherMenus(except) {
    ['#langMenu', '#themeMenu', '#avatarMenu', '#userMenu'].forEach(function (sel) {
      var m = $(sel);
      if (m && m !== except) m.classList.remove('show');
    });
  }

  function initLangDropdown() {
    var langBtn = $('#langBtn');
    var langMenu = $('#langMenu');
    if (langBtn && langMenu) {
      langBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
        var open = langMenu.classList.toggle('show');
        closeOtherMenus(langMenu);
        langBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }
    $$('#langMenu a[data-lang], #langMenu [data-lang]').forEach(function (el) {
      el.addEventListener('click', function (e) {
        e.stopPropagation();
        var lang = el.getAttribute('data-lang');
        if (lang) {
          setCookie('lang', lang, 365);
          try {
            localStorage.setItem(LS.LANG, lang);
          } catch (err) {}
          setTimeout(function () {
            window.location.reload();
          }, 120);
        }
        var m = el.closest('#langMenu');
        if (m) m.classList.remove('show');
      });
    });
  }

  function initFlashToast() {
    try {
      var meta = document.querySelector('meta[name="flash-message"]');
      if (meta) {
        var mt = meta.getAttribute('data-type') || meta.getAttribute('content-type') || 'info';
        var mm = meta.getAttribute('data-msg') || meta.getAttribute('content') || '';
        if (mm) {
          showToast(mm, mt, 4000);
          if (meta.parentNode) meta.parentNode.removeChild(meta);
          return;
        }
      }
    } catch (e) {}
    try {
      var flash = $('#flash-toast');
      if (flash) {
        var ft = flash.getAttribute('data-type') || 'info';
        var fm = flash.getAttribute('data-msg') || flash.textContent || '';
        if (fm) showToast(fm, ft, 4000);
        if (flash.parentNode) flash.parentNode.removeChild(flash);
      }
    } catch (e) {}
    if (window.location && window.location.search) {
      try {
        var usp = new URLSearchParams(window.location.search);
        var sm = usp.get('success_msg') || usp.get('successMessage');
        var em = usp.get('error_msg') || usp.get('errorMessage');
        var wm = usp.get('warning_msg') || usp.get('warningMessage');
        if (sm) showSuccess(sm, 4000);
        else if (em) showError(em, 4000);
        else if (wm) showWarning(wm, 4000);
      } catch (e) {}
    }
  }

  function initGlobalDocumentEvents() {
    document.addEventListener('click', function (e) {
      if (!e.target.closest('#langBtn') && !e.target.closest('#langMenu')) {
        var lm = $('#langMenu');
        if (lm) lm.classList.remove('show');
      }
      if (!e.target.closest('#themeBtn') && !e.target.closest('#themeMenu')) {
        var tm = $('#themeMenu');
        if (tm) tm.classList.remove('show');
      }
      if (!e.target.closest('#avatarBtn') && !e.target.closest('#avatarMenu') && !e.target.closest('#userMenu')) {
        var am = $('#avatarMenu') || $('#userMenu');
        if (am) am.classList.remove('show');
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        var nav = $('#mobileNav');
        if (nav && nav.classList.contains('show')) {
          closeMobileDrawer();
        }
        var modal = $('#announcementModal') || $('.announcement-modal');
        if (modal && modal.classList.contains('show')) {
          hideAnnouncement();
        }
        ['#langMenu', '#themeMenu', '#avatarMenu', '#userMenu'].forEach(function (sel) {
          var m = $(sel);
          if (m) m.classList.remove('show');
        });
      }
    });
  }

  function exposeGlobals() {
    window.$ = window.$ || $;
    window.$$ = window.$$ || $$;
    window.setCookie = setCookie;
    window.getCookie = getCookie;
    window.deleteCookie = deleteCookie;
    window.showToast = showToast;
    window.showSuccess = showSuccess;
    window.showError = showError;
    window.showWarning = showWarning;
    window.showInfo = showInfo;
    window.showLoading = showLoading;
    window.hideLoading = hideLoading;
    window.showAnnouncement = showAnnouncement;
    window.hideAnnouncement = hideAnnouncement;
    window.toggleTheme = toggleTheme;
    window.applyTheme = applyTheme;
    window.refreshCaptcha = refreshCaptcha;
    window.validateInput = validateInput;
    window.validateAll = validateAll;
    window.closeMobileDrawer = closeMobileDrawer;
    window.openMobileDrawer = openMobileDrawer;
    window.QEEFG = {
      $: $,
      $$: $$,
      showToast: showToast,
      showSuccess: showSuccess,
      showError: showError,
      showWarning: showWarning,
      showInfo: showInfo,
      showLoading: showLoading,
      hideLoading: hideLoading,
      showAnnouncement: showAnnouncement,
      hideAnnouncement: hideAnnouncement,
      toggleTheme: toggleTheme,
      applyTheme: applyTheme,
      refreshCaptcha: refreshCaptcha,
      validateInput: validateInput,
      validateAll: validateAll,
      setCookie: setCookie,
      getCookie: getCookie,
      deleteCookie: deleteCookie,
      closeMobileDrawer: closeMobileDrawer,
      openMobileDrawer: openMobileDrawer,
      calcPasswordStrength: calcPasswordStrength
    };
  }

  function init() {
    exposeGlobals();
    initTheme();
    initSmoothScroll();
    initMobileNav();
    bindPlatformLinks();
    initRealTimeValidate();
    bindAjaxForms();
    initCaptchaRefresh();
    bindThemeToggle();
    initLangDropdown();
    initEntranceAnimations();
    initAnnouncement();
    initFlashToast();
    initGlobalDocumentEvents();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
