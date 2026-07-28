/* ==========================================================================
   QEEFG Auth Station - 熵云 Software License Management Platform
   Main JavaScript
   ========================================================================== */

(function () {
  'use strict';

  /* ========================================================================
     1. HELPER FUNCTIONS
     ======================================================================== */

  var $ = function (selector, context) {
    return (context || document).querySelector(selector);
  };

  var $$ = function (selector, context) {
    return Array.prototype.slice.call((context || document).querySelectorAll(selector));
  };

  /* ========================================================================
     2. TOAST SYSTEM
     ======================================================================== */

  function getToastContainer() {
    var container = $('.toast-center');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-center';
      document.body.appendChild(container);
    }
    return container;
  }

  var TOAST_ICONS = {
    success: '&#10003;',
    error: '&#10007;',
    warning: '&#9888;',
    info: '&#8505;'
  };

  function showToast(msg, type) {
    type = type || 'info';
    var container = getToastContainer();

    var item = document.createElement('div');
    item.className = 'toast-center-item';

    var toast = document.createElement('div');
    toast.className = 'toast toast-' + type;

    var icon = document.createElement('span');
    icon.className = 'toast-icon';
    icon.innerHTML = TOAST_ICONS[type] || TOAST_ICONS.info;

    var body = document.createElement('span');
    body.className = 'toast-body';
    body.textContent = msg;

    toast.appendChild(icon);
    toast.appendChild(body);
    item.appendChild(toast);
    container.appendChild(item);

    // Force reflow then show
    void toast.offsetWidth;
    toast.classList.add('show');

    // Auto-remove after 3s
    var timer = setTimeout(function () {
      removeToast(toast, item);
    }, 3000);

    // Store timer for potential early removal
    toast._timer = timer;
    toast._item = item;

    // Click to dismiss early
    toast.addEventListener('click', function () {
      clearTimeout(timer);
      removeToast(toast, item);
    });
  }

  function removeToast(toast, item) {
    if (toast._removed) return;
    toast._removed = true;
    clearTimeout(toast._timer);
    toast.classList.remove('show');
    toast.classList.add('hide');
    setTimeout(function () {
      if (item.parentNode) {
        item.parentNode.removeChild(item);
      }
    }, 300);
  }

  function showError(msg) {
    showToast(msg, 'error');
  }

  function showSuccess(msg) {
    showToast(msg, 'success');
  }

  function showWarning(msg) {
    showToast(msg, 'warning');
  }

  /* ========================================================================
     3. THEME TOGGLE
     ======================================================================== */

  var THEME_KEY = 'qeefg_theme';

  function initTheme() {
    var savedTheme = localStorage.getItem(THEME_KEY);
    if (savedTheme === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    } else if (savedTheme === 'light') {
      document.documentElement.removeAttribute('data-theme');
    }
    // If no saved theme, respect system preference
    if (!savedTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  }

  function toggleTheme() {
    var current = document.documentElement.getAttribute('data-theme');
    if (current === 'dark') {
      document.documentElement.removeAttribute('data-theme');
      localStorage.setItem(THEME_KEY, 'light');
    } else {
      document.documentElement.setAttribute('data-theme', 'dark');
      localStorage.setItem(THEME_KEY, 'dark');
    }
  }

  function bindThemeToggle() {
    var btn = $('.theme-toggle');
    if (btn) {
      btn.addEventListener('click', toggleTheme);
    }

    // Ctrl+T shortcut
    document.addEventListener('keydown', function (e) {
      if (e.ctrlKey && e.key === 't') {
        e.preventDefault();
        toggleTheme();
      }
    });
  }

  /* ========================================================================
     4. MOBILE NAV (Public pages)
     ======================================================================== */

  function initMobileNav() {
    var hamburger = $('.hamburger-btn');
    var mobileNav = $('.mobile-nav');

    if (!hamburger || !mobileNav) return;

    hamburger.addEventListener('click', function () {
      var isOpen = mobileNav.classList.contains('show');
      if (isOpen) {
        mobileNav.classList.remove('show');
        hamburger.innerHTML = '&#9776;';
      } else {
        mobileNav.classList.add('show');
        hamburger.innerHTML = '&#10005;';
      }
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && mobileNav.classList.contains('show')) {
        mobileNav.classList.remove('show');
        if (hamburger) hamburger.innerHTML = '&#9776;';
      }
    });
  }

  /* ========================================================================
     5. HAMBURGER SIDEBAR (User/Admin pages)
     ======================================================================== */

  function initHamburger() {
    var hamburgerBtn = $('.hamburger-btn');
    var sidebar = $('.user-sidebar') || $('.admin-sidebar');
    var overlay = $('.sidebar-overlay');

    if (!hamburgerBtn) return;

    hamburgerBtn.addEventListener('click', function () {
      // Re-query sidebar in case layout changed
      var sb = $('.user-sidebar') || $('.admin-sidebar');
      var ov = $('.sidebar-overlay');

      if (sb) {
        var isOpen = sb.classList.contains('show');
        if (isOpen) {
          closeSidebar(sb, ov, hamburgerBtn);
        } else {
          openSidebar(sb, ov, hamburgerBtn);
        }
      }
    });

    // Close button inside sidebar
    var closeBtn = $('.sidebar-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        var sb = $('.user-sidebar') || $('.admin-sidebar');
        var ov = $('.sidebar-overlay');
        closeSidebar(sb, ov, hamburgerBtn);
      });
    }

    // Overlay click
    if (overlay) {
      overlay.addEventListener('click', function () {
        var sb = $('.user-sidebar') || $('.admin-sidebar');
        closeSidebar(sb, overlay, hamburgerBtn);
      });
    }

    // Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        var sb = $('.user-sidebar.show') || $('.admin-sidebar.show');
        if (sb) {
          var ov = $('.sidebar-overlay');
          closeSidebar(sb, ov, hamburgerBtn);
        }
      }
    });

    // Resize handler: close sidebar on large screens
    window.addEventListener('resize', function () {
      if (window.innerWidth > 1024) {
        var sb = $('.user-sidebar.show') || $('.admin-sidebar.show');
        if (sb) {
          var ov = $('.sidebar-overlay');
          closeSidebar(sb, ov, hamburgerBtn);
        }
      }
    });

    // Submenu toggle
    $$('.has-submenu .menu-link').forEach(function (link) {
      link.addEventListener('click', function (e) {
        var parent = link.closest('.has-submenu');
        if (parent) {
          e.preventDefault();
          parent.classList.toggle('open');
        }
      });
    });
  }

  function openSidebar(sidebar, overlay, btn) {
    if (sidebar) sidebar.classList.add('show');
    if (overlay) overlay.classList.add('show');
    if (btn) btn.innerHTML = '&#10005;';
  }

  function closeSidebar(sidebar, overlay, btn) {
    if (sidebar) sidebar.classList.remove('show');
    if (overlay) overlay.classList.remove('show');
    if (btn) btn.innerHTML = '&#9776;';
  }

  /* ========================================================================
     6. SIDEBAR ACTIVE STATE
     ======================================================================== */

  function initSidebarActive() {
    var currentPath = window.location.pathname;

    // Highlight active menu link
    $$('.menu-link').forEach(function (link) {
      var href = link.getAttribute('href');
      if (href && currentPath === href) {
        link.classList.add('active');

        // Auto-open parent submenu
        var parentSubmenu = link.closest('.submenu');
        if (parentSubmenu) {
          var parentHasSubmenu = parentSubmenu.closest('.has-submenu');
          if (parentHasSubmenu) {
            parentHasSubmenu.classList.add('open');
          }

          // Also mark the submenu item as active
          var submenuItem = link.closest('.submenu-item');
          if (submenuItem) {
            submenuItem.classList.add('active');
          }
        }
      }
    });

    // Also check for submenu items active
    $$('.submenu-item .menu-link').forEach(function (link) {
      var href = link.getAttribute('href');
      if (href && currentPath === href) {
        link.classList.add('active');
        var item = link.closest('.submenu-item');
        if (item) item.classList.add('active');
        var parent = link.closest('.has-submenu');
        if (parent) parent.classList.add('open');
      }
    });

    // Hamburger sidebar items
    $$('.hs-item').forEach(function (item) {
      var href = item.getAttribute('href');
      if (href && currentPath === href) {
        item.classList.add('active');
      }
    });

    // Docs sidebar links
    $$('.docs-link').forEach(function (link) {
      var href = link.getAttribute('href');
      if (href && currentPath === href) {
        link.classList.add('active');
      }
    });
  }

  /* ========================================================================
     7. CAPTCHA REFRESH
     ======================================================================== */

  function refreshCaptcha() {
    var captchaImg = $('.captcha-img');
    if (!captchaImg) return;
    var src = captchaImg.getAttribute('src') || captchaImg.getAttribute('data-src') || '';
    // Remove existing timestamp param
    src = src.replace(/[?&]_t=\d+/, '');
    var separator = src.indexOf('?') > -1 ? '&' : '?';
    captchaImg.setAttribute('src', src + separator + '_t=' + Date.now());
  }

  function initCaptcha() {
    var captchaImg = $('.captcha-img');
    if (captchaImg) {
      captchaImg.addEventListener('click', refreshCaptcha);
    }
  }

  /* ========================================================================
     8. AJAX FORMS
     ======================================================================== */

  function initAjaxForms() {
    document.addEventListener('submit', function (e) {
      var form = e.target.closest('form[data-ajax]');
      if (!form) return;

      e.preventDefault();

      var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
      var originalText = submitBtn ? submitBtn.textContent || submitBtn.value : '';

      // Show loading state
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = submitBtn.getAttribute('data-loading') || (originalText + '...');
        submitBtn.value = submitBtn.getAttribute('data-loading') || (originalText + '...');
      }

      showPageTransition();

      var formData = new FormData(form);
      var method = (form.getAttribute('method') || 'POST').toUpperCase();
      var action = form.getAttribute('action') || window.location.href;

      // Convert FormData to URLSearchParams for POST
      var body;
      var headers = {};

      if (method === 'GET') {
        var params = new URLSearchParams(formData);
        action = action + (action.indexOf('?') > -1 ? '&' : '?') + params.toString();
        body = null;
      } else {
        body = new URLSearchParams(formData);
        headers['Content-Type'] = 'application/x-www-form-urlencoded';
      }

      fetch(action, {
        method: method,
        headers: headers,
        body: body
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return { status: response.status, ok: response.ok, data: data };
          }).catch(function () {
            return { status: response.status, ok: response.ok, data: null };
          });
        })
        .then(function (result) {
          hidePageTransition();

          if (result.ok && result.data) {
            if (result.data.code === 200) {
              var msg = result.data.msg || '操作成功';
              showSuccess(msg);

              if (result.data.data && result.data.data.redirect) {
                setTimeout(function () {
                  window.location.href = result.data.data.redirect;
                }, 1000);
              } else if (result.data.data && result.data.data.reload) {
                setTimeout(function () {
                  window.location.reload();
                }, 1000);
              }
            } else {
              showError(result.data.msg || '操作失败');
            }
          } else {
            showError('请求失败 (' + result.status + ')');
          }
        })
        .catch(function (err) {
          hidePageTransition();
          showError('网络错误，请稍后重试');
          console.error('AJAX form error:', err);
        })
        .finally(function () {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            submitBtn.value = originalText;
          }
        });
    });
  }

  /* ========================================================================
     9. CONFIRM LINKS
     ======================================================================== */

  function initConfirmLinks() {
    document.addEventListener('click', function (e) {
      var link = e.target.closest('a[data-confirm]');
      if (!link) return;

      var message = link.getAttribute('data-confirm') || '确定要执行此操作吗？';
      if (!confirm(message)) {
        e.preventDefault();
        e.stopPropagation();
      }
    });
  }

  /* ========================================================================
     10. ANNOUNCEMENT MODAL
     ======================================================================== */

  var ANNOUNCEMENT_KEY = 'qeefg_announcement_hidden';

  function initAnnouncement() {
    var modal = $('.announcement-modal');
    if (!modal) return;

    var hiddenData = localStorage.getItem(ANNOUNCEMENT_KEY);
    if (hiddenData) {
      try {
        var parsed = JSON.parse(hiddenData);
        var now = Date.now();
        // 1 hour expiry
        if (now - parsed.timestamp < 3600000) {
          return; // Still suppressed
        }
      } catch (e) {
        // Invalid data, show modal
      }
    }

    // Show modal
    modal.classList.add('show');

    // Close button
    var closeBtn = $('.am-close', modal);
    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        hideAnnouncementModal(modal);
      });
    }

    // Suppress button
    var suppressBtn = $('.am-suppress, [data-suppress]', modal);
    if (suppressBtn) {
      suppressBtn.addEventListener('click', function () {
        localStorage.setItem(ANNOUNCEMENT_KEY, JSON.stringify({
          timestamp: Date.now()
        }));
        hideAnnouncementModal(modal);
      });
    }

    // Overlay click
    var overlay = $('.am-overlay', modal);
    if (overlay) {
      overlay.addEventListener('click', function () {
        hideAnnouncementModal(modal);
      });
    }

    // Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.classList.contains('show')) {
        hideAnnouncementModal(modal);
      }
    });
  }

  function hideAnnouncementModal(modal) {
    modal.classList.remove('show');
  }

  /* ========================================================================
     11. SEARCH
     ======================================================================== */

  function initSearch() {
    // Create search overlay if not exists
    var overlay = $('.search-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'search-overlay';
      overlay.innerHTML =
        '<div class="search-box">' +
        '<input type="text" class="search-box-input" placeholder="搜索..." autocomplete="off">' +
        '<div class="search-box-results"></div>' +
        '</div>';
      document.body.appendChild(overlay);

      // Click overlay background to close
      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
          closeSearch(overlay);
        }
      });

      // Input handler
      var input = $('.search-box-input', overlay);
      if (input) {
        input.addEventListener('input', function () {
          performSearch(input.value, $('.search-box-results', overlay));
        });
      }
    }

    // Keyboard shortcut: Ctrl+K or /
    document.addEventListener('keydown', function (e) {
      // Don't trigger when typing in inputs
      if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
        return;
      }

      if ((e.ctrlKey && e.key === 'k') || e.key === '/') {
        e.preventDefault();
        openSearch(overlay);
      }

      if (e.key === 'Escape' && overlay.classList.contains('show')) {
        closeSearch(overlay);
      }
    });

    // Search trigger button
    var searchTrigger = $('[data-search-trigger]');
    if (searchTrigger) {
      searchTrigger.addEventListener('click', function () {
        openSearch(overlay);
      });
    }
  }

  function openSearch(overlay) {
    overlay.classList.add('show');
    var input = $('.search-box-input', overlay);
    if (input) {
      setTimeout(function () {
        input.focus();
        input.value = '';
        var results = $('.search-box-results', overlay);
        if (results) results.innerHTML = '';
      }, 100);
    }
  }

  function closeSearch(overlay) {
    overlay.classList.remove('show');
  }

  function performSearch(query, resultsContainer) {
    if (!resultsContainer) return;
    resultsContainer.innerHTML = '';

    if (!query || query.trim().length === 0) {
      return;
    }

    var q = query.toLowerCase().trim();
    var items = $$('[data-search-item]');
    var found = [];

    items.forEach(function (item) {
      var title = (item.getAttribute('data-search-title') || item.textContent || '').toLowerCase();
      var href = item.getAttribute('data-search-href') || item.getAttribute('href') || '#';
      var desc = (item.getAttribute('data-search-desc') || '').toLowerCase();

      if (title.indexOf(q) > -1 || desc.indexOf(q) > -1) {
        found.push({
          title: item.getAttribute('data-search-title') || item.textContent || '',
          href: href,
          desc: item.getAttribute('data-search-desc') || ''
        });
      }
    });

    if (found.length === 0) {
      var empty = document.createElement('div');
      empty.className = 'search-result-item';
      empty.textContent = '未找到相关结果';
      empty.style.color = 'var(--text-muted)';
      resultsContainer.appendChild(empty);
      return;
    }

    found.forEach(function (item) {
      var el = document.createElement('a');
      el.className = 'search-result-item';
      el.href = item.href;

      var text = document.createElement('span');
      text.textContent = item.title;
      el.appendChild(text);

      if (item.desc) {
        var descEl = document.createElement('span');
        descEl.textContent = item.desc;
        descEl.style.fontSize = '12px';
        descEl.style.color = 'var(--text-muted)';
        el.appendChild(descEl);
      }

      el.addEventListener('click', function () {
        closeSearch($('.search-overlay'));
      });

      resultsContainer.appendChild(el);
    });
  }

  /* ========================================================================
     12. PAGE TRANSITION
     ======================================================================== */

  function showPageTransition() {
    var bar = $('.page-loading');
    if (!bar) {
      bar = document.createElement('div');
      bar.className = 'page-loading';
      bar.innerHTML = '<div class="page-loading-bar"></div>';
      document.body.appendChild(bar);
    }
    bar.style.display = 'block';
  }

  function hidePageTransition() {
    var bar = $('.page-loading');
    if (bar) {
      bar.style.display = 'none';
    }
  }

  /* ========================================================================
     13. ICON RAIL
     ======================================================================== */

  function initIconRail() {
    var currentPath = window.location.pathname;
    $$('.icon-rail-item').forEach(function (item) {
      var href = item.getAttribute('href');
      if (href && currentPath === href) {
        item.classList.add('active');
      }
    });
  }

  /* ========================================================================
     14. DOCUMENTS SIDEBAR
     ======================================================================== */

  function initDocsSidebar() {
    $$('.docs-cat-title').forEach(function (title) {
      title.addEventListener('click', function () {
        var body = title.nextElementSibling;
        if (body && body.classList.contains('docs-cat-body')) {
          var isHidden = body.style.display === 'none';
          body.style.display = isHidden ? '' : 'none';
          // Toggle arrow indicator
          var arrow = title.querySelector('.docs-cat-arrow');
          if (arrow) {
            arrow.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(-90deg)';
          }
        }
      });
    });
  }

  /* ========================================================================
     15. CLICK OUTSIDE
     ======================================================================== */

  function initClickOutside() {
    document.addEventListener('click', function (e) {
      // Close hamburger sidebar when clicking outside
      if (window.innerWidth <= 1024) {
        var sidebar = $('.user-sidebar.show') || $('.admin-sidebar.show');
        var hamburger = $('.hamburger-btn');

        if (sidebar && hamburger) {
          var clickedInsideSidebar = sidebar.contains(e.target);
          var clickedHamburger = hamburger.contains(e.target);

          if (!clickedInsideSidebar && !clickedHamburger) {
            var overlay = $('.sidebar-overlay');
            closeSidebar(sidebar, overlay, hamburger);
          }
        }
      }
    });
  }

  /* ========================================================================
     16. LANGUAGE SWITCH
     ======================================================================== */

  function initLangSwitch() {
    $$('.lang-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var lang = btn.getAttribute('data-lang');
        if (!lang) return;

        // Update active state
        $$('.lang-btn').forEach(function (b) {
          b.classList.remove('active');
        });
        btn.classList.add('active');

        // Store preference
        localStorage.setItem('qeefg_lang', lang);

        // If there's a form, submit it
        var form = btn.closest('form');
        if (form) {
          form.submit();
        } else {
          // Otherwise reload with lang param
          var url = new URL(window.location.href);
          url.searchParams.set('lang', lang);
          window.location.href = url.toString();
        }
      });
    });

    // Set initial active state from localStorage
    var savedLang = localStorage.getItem('qeefg_lang');
    if (savedLang) {
      $$('.lang-btn').forEach(function (btn) {
        if (btn.getAttribute('data-lang') === savedLang) {
          btn.classList.add('active');
        } else {
          btn.classList.remove('active');
        }
      });
    }
  }

  /* ========================================================================
     17. SCROLL ANIMATIONS
     ======================================================================== */

  function initScrollAnimations() {
    var animatedElements = $$('[data-animate]');
    if (animatedElements.length === 0) return;

    function checkVisibility() {
      var windowHeight = window.innerHeight;

      animatedElements.forEach(function (el) {
        if (el.classList.contains('animated')) return;

        var rect = el.getBoundingClientRect();
        var threshold = el.getAttribute('data-animate-threshold');
        var offset = threshold ? parseInt(threshold, 10) : 100;

        if (rect.top < windowHeight - offset && rect.bottom > 0) {
          el.classList.add('animated');
          var animation = el.getAttribute('data-animate') || 'fadeIn';
          el.style.animation = animation + ' 0.6s ease forwards';
        }
      });
    }

    // Check on load
    checkVisibility();

    // Check on scroll (throttled)
    var ticking = false;
    window.addEventListener('scroll', function () {
      if (!ticking) {
        window.requestAnimationFrame(function () {
          checkVisibility();
          ticking = false;
        });
        ticking = true;
      }
    });
  }

  /* ========================================================================
     18. INITIALIZATION
     ======================================================================== */

  function init() {
    initTheme();
    bindThemeToggle();
    initMobileNav();
    initHamburger();
    initSidebarActive();
    initCaptcha();
    initAjaxForms();
    initConfirmLinks();
    initAnnouncement();
    initSearch();
    initIconRail();
    initDocsSidebar();
    initClickOutside();
    initLangSwitch();
    initScrollAnimations();
  }

  // Run on DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  /* ========================================================================
     19. PUBLIC API
     ======================================================================== */

  window.QEEFG = {
    showToast: showToast,
    showError: showError,
    showSuccess: showSuccess,
    showWarning: showWarning,
    showPageTransition: showPageTransition,
    hidePageTransition: hidePageTransition,
    refreshCaptcha: refreshCaptcha
  };

})();