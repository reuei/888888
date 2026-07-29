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
     2. TOAST / NOTIFICATION SYSTEM (clean, minimal, SVG icons)
     ======================================================================== */

  var TOAST_ICONS = {
    success: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
    error: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
    warning: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
    info: '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
  };

  function getToastContainer() {
    var container = $('.toast-center');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-center';
      document.body.appendChild(container);
    }
    return container;
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
     4. SMOOTH SCROLL
     ======================================================================== */

  function initSmoothScroll() {
    document.documentElement.style.scrollBehavior = 'smooth';

    // Handle anchor links with smooth scroll
    document.addEventListener('click', function (e) {
      var link = e.target.closest('a[href^="#"]');
      if (!link) return;

      var targetId = link.getAttribute('href');
      if (targetId === '#' || targetId === '') return;

      var target = document.querySelector(targetId);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }

  /* ========================================================================
     5. MOBILE NAV (Public pages)
     ======================================================================== */

  function initMobileNav() {
    var hamburger = $('.hamburger-btn');
    var mobileNav = $('.mobile-nav');

    if (!hamburger || !mobileNav) return;

    hamburger.addEventListener('click', function () {
      var isOpen = mobileNav.classList.contains('show');
      if (isOpen) {
        mobileNav.classList.remove('show');
        hamburger.classList.remove('open');
      } else {
        mobileNav.classList.add('show');
        hamburger.classList.add('open');
      }
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && mobileNav.classList.contains('show')) {
        mobileNav.classList.remove('show');
        if (hamburger) hamburger.classList.remove('open');
      }
    });
  }

  /* ========================================================================
     6. HAMBURGER SIDEBAR (User/Admin pages)
     ======================================================================== */

  function initHamburger() {
    var hamburgerBtn = $('.hamburger-btn');
    var overlay = $('.sidebar-overlay');

    if (!hamburgerBtn) return;

    hamburgerBtn.addEventListener('click', function () {
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

    // Submenu toggle: toggle submenu, but allow navigation if href is a real URL
    $$('.has-submenu .menu-link').forEach(function (link) {
      link.addEventListener('click', function (e) {
        var parent = link.closest('.has-submenu');
        if (!parent) return;

        var href = link.getAttribute('href') || '';
        var isPlaceholder = !href || href === '#' || href === 'javascript:void(0)' || href === 'javascript:;';

        if (isPlaceholder) {
          // No real navigation target — just toggle the submenu
          e.preventDefault();
          parent.classList.toggle('open');
        } else {
          // Has a real href — toggle submenu, then navigate
          parent.classList.toggle('open');
        }
      });
    });
  }

  function openSidebar(sidebar, overlay, btn) {
    if (sidebar) sidebar.classList.add('show');
    if (overlay) overlay.classList.add('show');
    if (btn) btn.classList.add('open');
  }

  function closeSidebar(sidebar, overlay, btn) {
    if (sidebar) sidebar.classList.remove('show');
    if (overlay) overlay.classList.remove('show');
    if (btn) btn.classList.remove('open');
  }

  /* ========================================================================
     7. SIDEBAR ACTIVE STATE
     ======================================================================== */

  function initSidebarActive() {
    var currentPath = window.location.pathname;

    // Normalize path: remove trailing slash (except root)
    var normalizedPath = currentPath;
    if (normalizedPath !== '/' && normalizedPath.slice(-1) === '/') {
      normalizedPath = normalizedPath.slice(0, -1);
    }

    function normalizeHref(href) {
      if (!href) return '';
      if (href !== '/' && href.slice(-1) === '/') {
        href = href.slice(0, -1);
      }
      return href;
    }

    function isActive(href) {
      var nhref = normalizeHref(href);
      if (!nhref || nhref === '#') return false;
      // Exact match
      if (normalizedPath === nhref) return true;
      // Sub-path match (e.g., /user/licenses matches /user)
      if (nhref !== '/' && normalizedPath.indexOf(nhref + '/') === 0) return true;
      return false;
    }

    // Highlight top-level menu links
    $$('.menu-link').forEach(function (link) {
      var href = link.getAttribute('href');
      if (isActive(href)) {
        link.classList.add('active');

        // Auto-open parent submenu
        var parentSubmenu = link.closest('.submenu');
        if (parentSubmenu) {
          var parentHasSubmenu = parentSubmenu.closest('.has-submenu');
          if (parentHasSubmenu) {
            parentHasSubmenu.classList.add('open');
          }
          var submenuItem = link.closest('.submenu-item');
          if (submenuItem) {
            submenuItem.classList.add('active');
          }
        }
      }
    });

    // Highlight submenu items
    $$('.submenu-item .menu-link').forEach(function (link) {
      var href = link.getAttribute('href');
      if (isActive(href)) {
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
      if (isActive(href)) {
        item.classList.add('active');
      }
    });

    // Docs sidebar links
    $$('.docs-link').forEach(function (link) {
      var href = link.getAttribute('href');
      if (isActive(href)) {
        link.classList.add('active');
      }
    });
  }

  /* ========================================================================
     8. CAPTCHA REFRESH
     ======================================================================== */

  function refreshCaptcha() {
    var captchaImg = $('.captcha-img');
    if (!captchaImg) return;
    var src = captchaImg.getAttribute('src') || captchaImg.getAttribute('data-src') || '';
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
     9. AJAX FORMS (with page transition loading bar)
     ======================================================================== */

  function initAjaxForms() {
    document.addEventListener('submit', function (e) {
      var form = e.target.closest('form[data-ajax]');
      if (!form) return;

      e.preventDefault();

      var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
      var originalText = submitBtn ? (submitBtn.textContent || submitBtn.value) : '';

      // Show loading state
      if (submitBtn) {
        submitBtn.disabled = true;
        var loadingText = submitBtn.getAttribute('data-loading') || (originalText + '...');
        if (submitBtn.tagName === 'INPUT') {
          submitBtn.value = loadingText;
        } else {
          submitBtn.textContent = loadingText;
        }
      }

      showPageTransition();

      var formData = new FormData(form);
      var method = (form.getAttribute('method') || 'POST').toUpperCase();
      var action = form.getAttribute('action') || window.location.href;

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
            if (result.data.code === 200 || result.data.code == 200) {
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
            if (submitBtn.tagName === 'INPUT') {
              submitBtn.value = originalText;
            } else {
              submitBtn.textContent = originalText;
            }
          }
        });
    });
  }

  /* ========================================================================
     10. CONFIRM LINKS
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
     11. ANNOUNCEMENT MODAL (auto-show on homepage, works on all pages)
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
      closeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        hideAnnouncementModal(modal);
      });
    }

    // Suppress button (don't show again for 1 hour)
    var suppressBtn = $('.am-suppress, [data-suppress]', modal);
    if (suppressBtn) {
      suppressBtn.addEventListener('click', function (e) {
        e.stopPropagation();
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
    } else {
      // If no overlay element, click on modal background closes it
      modal.addEventListener('click', function (e) {
        if (e.target === modal) {
          hideAnnouncementModal(modal);
        }
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
     12. SEARCH
     ======================================================================== */

  function initSearch() {
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

      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
          closeSearch(overlay);
        }
      });

      var input = $('.search-box-input', overlay);
      if (input) {
        input.addEventListener('input', function () {
          performSearch(input.value, $('.search-box-results', overlay));
        });
      }
    }

    // Keyboard shortcut: Ctrl+K or /
    document.addEventListener('keydown', function (e) {
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
     13. PAGE TRANSITION LOADING BAR
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
    // Reset and trigger animation
    var inner = $('.page-loading-bar', bar);
    if (inner) {
      inner.style.width = '0%';
      inner.classList.remove('page-loading-bar--done');
      // Force reflow
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
      inner.classList.add('page-loading-bar--done');
    }
    // Hide after transition completes
    setTimeout(function () {
      bar.style.display = 'none';
      if (inner) {
        inner.style.width = '0%';
        inner.classList.remove('page-loading-bar--done');
      }
    }, 400);
  }

  /* ========================================================================
     14. PAGE LOADING SPINNER (on navigation)
     ======================================================================== */

  function initPageLoadingSpinner() {
    // Show spinner on link clicks that navigate away
    document.addEventListener('click', function (e) {
      var link = e.target.closest('a');
      if (!link) return;

      var href = link.getAttribute('href');
      if (!href) return;

      // Skip: javascript:, #, anchor-only, download, new-tab, mailto, tel
      if (href === '#' || href === '') return;
      if (href.indexOf('javascript:') === 0) return;
      if (link.getAttribute('target') === '_blank') return;
      if (link.getAttribute('download') !== null) return;
      if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return;
      if (link.getAttribute('data-confirm') !== null) return;
      if (link.getAttribute('data-no-spinner') !== null) return;

      // Only show spinner for internal navigation
      if (href.indexOf('://') > -1 && href.indexOf(window.location.origin) !== 0) return;

      showPageTransition();
    });

    // Hide spinner when page finishes loading
    window.addEventListener('pageshow', function () {
      hidePageTransition();
    });

    // Hide spinner on beforeunload (in case of errors)
    window.addEventListener('beforeunload', function () {
      // Reset spinner to prepare for next page
      var bar = $('.page-loading');
      if (bar) {
        bar.style.display = 'block';
      }
    });
  }

  /* ========================================================================
     15. ICON RAIL
     ======================================================================== */

  function initIconRail() {
    var currentPath = window.location.pathname;
    if (currentPath !== '/' && currentPath.slice(-1) === '/') {
      currentPath = currentPath.slice(0, -1);
    }
    $$('.icon-rail-item').forEach(function (item) {
      var href = item.getAttribute('href');
      if (href) {
        if (href !== '/' && href.slice(-1) === '/') {
          href = href.slice(0, -1);
        }
        if (currentPath === href || (href !== '/' && currentPath.indexOf(href + '/') === 0)) {
          item.classList.add('active');
        }
      }
    });
  }

  /* ========================================================================
     16. DOCUMENTS SIDEBAR
     ======================================================================== */

  function initDocsSidebar() {
    $$('.docs-cat-title').forEach(function (title) {
      title.addEventListener('click', function () {
        var body = title.nextElementSibling;
        if (body && body.classList.contains('docs-cat-body')) {
          var isHidden = body.style.display === 'none';
          body.style.display = isHidden ? '' : 'none';
          var arrow = title.querySelector('.docs-cat-arrow');
          if (arrow) {
            arrow.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(-90deg)';
          }
        }
      });
    });
  }

  /* ========================================================================
     17. CLICK OUTSIDE
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
     18. LANGUAGE SWITCH
     ======================================================================== */

  function initLangSwitch() {
    $$('.lang-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var lang = btn.getAttribute('data-lang');
        if (!lang) return;

        $$('.lang-btn').forEach(function (b) {
          b.classList.remove('active');
        });
        btn.classList.add('active');

        localStorage.setItem('qeefg_lang', lang);

        var form = btn.closest('form');
        if (form) {
          form.submit();
        } else {
          var url = new URL(window.location.href);
          url.searchParams.set('lang', lang);
          window.location.href = url.toString();
        }
      });
    });

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
     19. SCROLL ANIMATIONS
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

    checkVisibility();

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
     20. MESSAGE NOTIFICATION BELL
     ======================================================================== */

  function initMessageBell() {
    var bell = $('.msg-bell');
    if (!bell) return;

    var dropdown = $('.msg-dropdown', bell);
    if (!dropdown) return;

    // Toggle dropdown on bell click
    bell.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var isOpen = dropdown.classList.contains('show');
      if (isOpen) {
        dropdown.classList.remove('show');
      } else {
        // Close all other open dropdowns first
        $$('.msg-dropdown.show').forEach(function (d) {
          d.classList.remove('show');
        });
        dropdown.classList.add('show');
        fetchUnreadCount(bell);
      }
    });

    // Click outside to close
    document.addEventListener('click', function (e) {
      if (!bell.contains(e.target)) {
        dropdown.classList.remove('show');
      }
    });

    // Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
      }
    });

    fetchUnreadCount(bell);
  }

  function fetchUnreadCount(bell) {
    var badge = $('.badge-dot', bell);
    if (!badge) return;

    var unreadUrl = bell.getAttribute('data-unread-url');
    if (!unreadUrl) return;

    fetch(unreadUrl, { method: 'GET', headers: { 'Accept': 'application/json' } })
      .then(function (response) {
        return response.json().then(function (data) {
          return { status: response.status, ok: response.ok, data: data };
        });
      })
      .then(function (result) {
        if (result.ok && result.data && result.data.code === 200) {
          var count = result.data.data && result.data.data.count;
          if (count && count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = '';
          } else {
            badge.style.display = 'none';
          }
        }
      })
      .catch(function () {
        badge.style.display = 'none';
      });
  }

  /* ========================================================================
     21. MODAL SYSTEM
     ======================================================================== */

  function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('show');
    var focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable) {
      setTimeout(function () { focusable.focus(); }, 100);
    }
  }

  function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('show');
  }

  function initModals() {
    // Bind close buttons
    $$('.modal-close').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var modal = btn.closest('.modal-overlay');
        if (modal) {
          closeModal(modal.id);
        }
      });
    });

    // Close on overlay click
    $$('.modal-overlay').forEach(function (overlay) {
      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
          closeModal(overlay.id);
        }
      });
    });

    // Escape key closes all open modals
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        var openModals = $$('.modal-overlay.show');
        openModals.forEach(function (modal) {
          closeModal(modal.id);
        });
      }
    });

    // Bind data-modal-trigger buttons
    $$('[data-modal-trigger]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var modalId = btn.getAttribute('data-modal-trigger');
        if (modalId) openModal(modalId);
      });
    });

    // Bind data-modal-close buttons
    $$('[data-modal-close]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var modalId = btn.getAttribute('data-modal-close');
        if (modalId) closeModal(modalId);
      });
    });
  }

  /* ========================================================================
     22. FORM REAL-TIME VALIDATION
     ======================================================================== */

  function initFormValidation() {
    // Validate inputs inside forms with data-validate
    $$('form[data-validate]').forEach(function (form) {
      var inputs = $$('input[data-validate]', form);
      inputs.forEach(function (input) {
        bindValidationEvents(input);
      });

      // Prevent form submission if there are invalid inputs
      form.addEventListener('submit', function (e) {
        var allInputs = $$('input[data-validate]', form);
        var hasError = false;
        allInputs.forEach(function (inp) {
          var result = validateInput(inp);
          if (result && !result.valid) {
            hasError = true;
          }
        });
        if (hasError) {
          e.preventDefault();
          showWarning('请修正表单中的错误');
        }
      });
    });

    // Also validate standalone inputs with data-validate outside forms
    $$('input[data-validate]').forEach(function (input) {
      if (!input.closest('form[data-validate]')) {
        bindValidationEvents(input);
      }
    });
  }

  function bindValidationEvents(input) {
    input.addEventListener('blur', function () {
      validateInput(input);
    });
    input.addEventListener('input', function () {
      if (input.classList.contains('is-invalid') || input.classList.contains('is-valid')) {
        validateInput(input);
      }
    });
  }

  function validateInput(input) {
    var type = input.getAttribute('data-validate');
    var value = input.value;
    var result;

    switch (type) {
      case 'login':
        result = validateLogin(value);
        break;
      case 'email':
        result = validateEmail(value);
        break;
      case 'password':
        result = validatePassword(value, input);
        break;
      case 'phone':
        result = validatePhone(value);
        break;
      case 'username':
        result = validateUsername(value);
        break;
      default:
        return null;
    }

    // Remove existing feedback
    var existingFeedback = input.parentNode.querySelector('.form-feedback');
    if (existingFeedback) {
      existingFeedback.parentNode.removeChild(existingFeedback);
    }

    input.classList.remove('is-invalid', 'is-valid');

    if (value.length === 0) {
      return result;
    }

    if (result.valid) {
      input.classList.add('is-valid');
    } else {
      input.classList.add('is-invalid');
      var feedback = document.createElement('div');
      feedback.className = 'form-feedback error';
      feedback.textContent = result.message;
      input.parentNode.appendChild(feedback);
    }

    return result;
  }

  function validateEmail(value) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      return { valid: false, message: '请输入有效的邮箱地址' };
    }
    return { valid: true };
  }

  function validatePassword(value, input) {
    var minLength = 6;
    if (input) {
      var attrMin = input.getAttribute('data-min-length');
      if (attrMin) {
        minLength = parseInt(attrMin, 10) || 6;
      }
    }
    if (value.length < minLength) {
      return { valid: false, message: '密码长度至少 ' + minLength + ' 位' };
    }
    return { valid: true };
  }

  function validatePhone(value) {
    var phoneRegex = /^1[3-9]\d{9}$/;
    if (!phoneRegex.test(value)) {
      return { valid: false, message: '请输入有效的手机号码' };
    }
    return { valid: true };
  }

  function validateUsername(value) {
    if (value.length < 3) {
      return { valid: false, message: '用户名长度至少 3 个字符' };
    }
    if (value.length > 20) {
      return { valid: false, message: '用户名长度不能超过 20 个字符' };
    }
    return { valid: true };
  }

  function validateLogin(value) {
    if (value.length < 1) {
      return { valid: false, message: '请输入用户名、邮箱或手机号' };
    }
    return { valid: true };
  }

  /* ========================================================================
     23. PAYMENT CHANNEL SELECTION
     ======================================================================== */

  function initPaymentChannel() {
    var items = $$('.payment-channel-item');
    if (items.length === 0) return;

    var hiddenInput = $('input[name="payment_channel"]');

    items.forEach(function (item) {
      item.addEventListener('click', function () {
        items.forEach(function (i) { i.classList.remove('selected'); });
        item.classList.add('selected');

        var channelValue = item.getAttribute('data-channel');
        if (hiddenInput && channelValue) {
          hiddenInput.value = channelValue;
        }
      });
    });

    var hasSelected = $$('.payment-channel-item.selected').length > 0;
    if (!hasSelected && items.length > 0) {
      items[0].click();
    }
  }

  /* ========================================================================
     24. TAB SWITCHING
     ======================================================================== */

  function initTabs() {
    $$('.tab-nav').forEach(function (tabNav) {
      var tabItems = $$('.tab-item', tabNav);
      var container = tabNav.parentNode;
      var tabPanes = $$('.tab-pane', container);

      tabItems.forEach(function (tab) {
        tab.addEventListener('click', function () {
          tabItems.forEach(function (t) { t.classList.remove('active'); });
          tab.classList.add('active');

          var targetId = tab.getAttribute('data-tab');
          if (targetId && tabPanes.length > 0) {
            tabPanes.forEach(function (pane) {
              if (pane.getAttribute('data-tab') === targetId) {
                pane.style.display = '';
              } else {
                pane.style.display = 'none';
              }
            });
          }
        });
      });

      var hasActive = $$('.tab-item.active', tabNav).length > 0;
      if (!hasActive && tabItems.length > 0) {
        tabItems[0].click();
      }
    });
  }

  /* ========================================================================
     25. DEVELOPER APPLICATION
     ======================================================================== */

  function initDeveloperApply() {
    var applyBtn = $('[data-developer-apply]');
    if (!applyBtn) return;

    applyBtn.addEventListener('click', function () {
      if (!confirm('确定要提交开发者申请吗？')) return;

      var url = applyBtn.getAttribute('data-developer-apply') || applyBtn.getAttribute('data-url');
      if (!url) {
        showError('缺少提交地址');
        return;
      }

      applyBtn.disabled = true;
      var originalText = applyBtn.textContent;
      applyBtn.textContent = '提交中...';

      showPageTransition();

      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'Accept': 'application/json'
        }
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return { status: response.status, ok: response.ok, data: data };
          });
        })
        .then(function (result) {
          hidePageTransition();
          if (result.ok && result.data && result.data.code === 200) {
            showSuccess(result.data.msg || '申请已提交');
            if (result.data.data && result.data.data.redirect) {
              setTimeout(function () {
                window.location.href = result.data.data.redirect;
              }, 1000);
            } else {
              setTimeout(function () {
                window.location.reload();
              }, 1000);
            }
          } else {
            showError((result.data && result.data.msg) || '申请提交失败');
          }
        })
        .catch(function () {
          hidePageTransition();
          showError('网络错误，请稍后重试');
        })
        .finally(function () {
          applyBtn.disabled = false;
          applyBtn.textContent = originalText;
        });
    });
  }

  /* ========================================================================
     26. PLUGIN SUBMISSION
     ======================================================================== */

  function initPluginSubmit() {
    var form = $('form[data-plugin-submit]');
    if (!form) return;

    var progressBar = $('.upload-progress', form);
    var progressFill = $('.upload-progress-fill', form);
    var progressText = $('.upload-progress-text', form);

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
      var originalText = submitBtn ? (submitBtn.textContent || submitBtn.value) : '';

      if (submitBtn) {
        submitBtn.disabled = true;
        if (submitBtn.tagName === 'INPUT') {
          submitBtn.value = '上传中...';
        } else {
          submitBtn.textContent = '上传中...';
        }
      }

      if (progressBar) progressBar.style.display = '';

      showPageTransition();

      var formData = new FormData(form);
      var action = form.getAttribute('action') || window.location.href;

      var xhr = new XMLHttpRequest();

      xhr.upload.addEventListener('progress', function (e) {
        if (e.lengthComputable) {
          var percent = Math.round((e.loaded / e.total) * 100);
          if (progressFill) progressFill.style.width = percent + '%';
          if (progressText) progressText.textContent = percent + '%';
        }
      });

      xhr.addEventListener('load', function () {
        hidePageTransition();
        if (progressBar) progressBar.style.display = 'none';

        try {
          var data = JSON.parse(xhr.responseText);
          if (xhr.status >= 200 && xhr.status < 300 && data.code === 200) {
            showSuccess(data.msg || '提交成功');
            if (data.data && data.data.redirect) {
              setTimeout(function () {
                window.location.href = data.data.redirect;
              }, 1000);
            } else {
              setTimeout(function () {
                window.location.reload();
              }, 1000);
            }
          } else {
            showError(data.msg || '提交失败');
          }
        } catch (err) {
          showError('服务器响应异常');
        }

        if (submitBtn) {
          submitBtn.disabled = false;
          if (submitBtn.tagName === 'INPUT') {
            submitBtn.value = originalText;
          } else {
            submitBtn.textContent = originalText;
          }
        }
      });

      xhr.addEventListener('error', function () {
        hidePageTransition();
        if (progressBar) progressBar.style.display = 'none';
        showError('网络错误，请稍后重试');
        if (submitBtn) {
          submitBtn.disabled = false;
          if (submitBtn.tagName === 'INPUT') {
            submitBtn.value = originalText;
          } else {
            submitBtn.textContent = originalText;
          }
        }
      });

      xhr.open('POST', action);
      xhr.send(formData);
    });
  }

  /* ========================================================================
     27. INITIALIZATION
     ======================================================================== */

  function init() {
    initTheme();
    bindThemeToggle();
    initSmoothScroll();
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
    initMessageBell();
    initModals();
    initFormValidation();
    initPaymentChannel();
    initTabs();
    initDeveloperApply();
    initPluginSubmit();
    initPageLoadingSpinner();
  }

  // Run on DOMContentLoaded
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  /* ========================================================================
     28. PUBLIC API
     ======================================================================== */

  window.QEEFG = {
    showToast: showToast,
    showError: showError,
    showSuccess: showSuccess,
    showWarning: showWarning,
    showPageTransition: showPageTransition,
    hidePageTransition: hidePageTransition,
    refreshCaptcha: refreshCaptcha,
    openModal: openModal,
    closeModal: closeModal
  };

})();