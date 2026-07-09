const App = {
  config: {
    apiBase: '',
    csrfToken: '',
  },

  init() {
    this.initPageLoader();
    this.initScrollAnimations();
    this.initHeaderScroll();
    this.initToastContainer();
  },

  initPageLoader() {
    const loader = document.getElementById('pageLoader');
    if (!loader) return;
    window.addEventListener('load', () => {
      setTimeout(() => {
        loader.classList.add('hidden');
      }, 300);
    });
    setTimeout(() => {
      loader.classList.add('hidden');
    }, 2000);
  },

  initScrollAnimations() {
    const elements = document.querySelectorAll('.fade-in, .slide-up');
    if (!elements.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    elements.forEach(el => observer.observe(el));
  },

  initHeaderScroll() {
    const header = document.querySelector('.header');
    if (!header) return;
    window.addEventListener('scroll', () => {
      if (window.scrollY > 20) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  },

  initToastContainer() {
    if (!document.querySelector('.toast-container')) {
      const container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
  },

  toast(title, msg, type = 'info', duration = 3000) {
    const container = document.querySelector('.toast-container');
    if (!container) return;

    const icons = {
      success: '✓',
      error: '✕',
      warning: '!',
      info: 'i'
    };

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
      <div class="toast-icon">${icons[type] || icons.info}</div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        ${msg ? `<div class="toast-msg">${msg}</div>` : ''}
      </div>
    `;

    container.appendChild(toast);
    requestAnimationFrame(() => {
      toast.classList.add('show');
    });

    if (duration > 0) {
      setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
      }, duration);
    }

    return toast;
  },

  toastSuccess(msg) {
    this.toast('操作成功', msg, 'success');
  },

  toastError(msg) {
    this.toast('操作失败', msg, 'error');
  },

  toastWarning(msg) {
    this.toast('温馨提示', msg, 'warning');
  },

  toastInfo(msg) {
    this.toast('提示', msg, 'info');
  },

  async request(url, options = {}) {
    const defaults = {
      method: 'GET',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest',
      },
    };

    const config = { ...defaults, ...options };
    config.headers = { ...defaults.headers, ...options.headers };

    if (config.data) {
      if (config.method === 'GET') {
        const params = new URLSearchParams(config.data).toString();
        url += (url.includes('?') ? '&' : '?') + params;
        delete config.data;
      } else if (config.headers['Content-Type'] === 'application/json') {
        config.body = JSON.stringify(config.data);
      } else {
        config.body = new URLSearchParams(config.data).toString();
      }
      delete config.data;
    }

    try {
      const response = await fetch(this.config.apiBase + url, config);
      const result = await response.json();
      return result;
    } catch (e) {
      return { code: -1, msg: '网络错误，请稍后重试' };
    }
  },

  async post(url, data = {}) {
    return this.request(url, { method: 'POST', data });
  },

  async get(url, data = {}) {
    return this.request(url, { method: 'GET', data });
  },

  setButtonLoading(btn, loading) {
    if (!btn) return;
    if (loading) {
      btn.classList.add('btn-loading');
      btn.disabled = true;
    } else {
      btn.classList.remove('btn-loading');
      btn.disabled = false;
    }
  },

  initSliderCaptcha(container, onSuccess) {
    const sliderContainer = typeof container === 'string' ? document.querySelector(container) : container;
    if (!sliderContainer) return;

    let isDragging = false;
    let startX = 0;
    let moveX = 0;
    let verified = false;
    let token = '';

    const track = sliderContainer.querySelector('.slider-track');
    const fill = sliderContainer.querySelector('.slider-fill');
    const btn = sliderContainer.querySelector('.slider-btn');
    const text = sliderContainer.querySelector('.slider-text');

    const getSliderCaptcha = async () => {
      const result = await App.get('/slider/captcha');
      if (result.code === 0 && result.data) {
        token = result.data.token;
      }
    };

    getSliderCaptcha();

    const handleStart = (e) => {
      if (verified) return;
      isDragging = true;
      startX = (e.touches ? e.touches[0].clientX : e.clientX);
      btn.style.transition = 'none';
      fill.style.transition = 'none';
    };

    const handleMove = (e) => {
      if (!isDragging || verified) return;
      e.preventDefault();
      const clientX = (e.touches ? e.touches[0].clientX : e.clientX);
      moveX = Math.max(0, Math.min(track.offsetWidth - btn.offsetWidth, clientX - startX));
      btn.style.left = moveX + 'px';
      fill.style.width = (moveX + btn.offsetWidth / 2) + 'px';
    };

    const handleEnd = async () => {
      if (!isDragging || verified) return;
      isDragging = false;

      const result = await App.post('/slider/verify', {
        token: token,
        x: Math.round(moveX)
      });

      if (result.code === 0) {
        verified = true;
        track.classList.add('success');
        text.textContent = '验证成功';
        btn.style.left = (track.offsetWidth - btn.offsetWidth) + 'px';
        fill.style.width = '100%';
        if (onSuccess) onSuccess(token);
      } else {
        btn.style.transition = 'left 0.3s ease';
        fill.style.transition = 'width 0.3s ease';
        btn.style.left = '0px';
        fill.style.width = '0px';
        moveX = 0;
        App.toastError('验证失败，请重试');
        getSliderCaptcha();
      }
    };

    btn.addEventListener('mousedown', handleStart);
    document.addEventListener('mousemove', handleMove);
    document.addEventListener('mouseup', handleEnd);

    btn.addEventListener('touchstart', handleStart, { passive: false });
    document.addEventListener('touchmove', handleMove, { passive: false });
    document.addEventListener('touchend', handleEnd);

    sliderContainer.refresh = () => {
      verified = false;
      moveX = 0;
      track.classList.remove('success');
      text.textContent = '向右滑动完成验证';
      btn.style.left = '0px';
      fill.style.width = '0px';
      btn.style.transition = '';
      fill.style.transition = '';
      getSliderCaptcha();
    };

    sliderContainer.isVerified = () => verified;
    sliderContainer.getToken = () => token;

    return sliderContainer;
  },

  validateForm(form, rules) {
    let isValid = true;
    const formEl = typeof form === 'string' ? document.querySelector(form) : form;
    if (!formEl) return false;

    Object.entries(rules).forEach(([name, rule]) => {
      const input = formEl.querySelector(`[name="${name}"]`);
      const errorEl = formEl.querySelector(`[name="${name}"] + .form-error`);
      if (!input) return;

      const value = input.value.trim();
      let error = '';

      if (rule.required && !value) {
        error = rule.message || '此项不能为空';
      } else if (value && rule.minLength && value.length < rule.minLength) {
        error = rule.message || `最少${rule.minLength}个字符`;
      } else if (value && rule.maxLength && value.length > rule.maxLength) {
        error = rule.message || `最多${rule.maxLength}个字符`;
      } else if (value && rule.pattern && !rule.pattern.test(value)) {
        error = rule.message || '格式不正确';
      } else if (rule.validator) {
        error = rule.validator(value);
      }

      if (error) {
        isValid = false;
        input.classList.add('error');
        if (errorEl) {
          errorEl.textContent = error;
          errorEl.classList.add('show');
        }
      } else {
        input.classList.remove('error');
        if (errorEl) {
          errorEl.classList.remove('show');
        }
      }
    });

    return isValid;
  },

  initLiveValidation(form, rules) {
    const formEl = typeof form === 'string' ? document.querySelector(form) : form;
    if (!formEl) return;

    Object.entries(rules).forEach(([name, rule]) => {
      const input = formEl.querySelector(`[name="${name}"]`);
      if (!input) return;

      let timer;
      input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
          const value = input.value.trim();
          const errorEl = formEl.querySelector(`[name="${name}"] + .form-error`);
          let error = '';

          if (rule.required && !value) {
            error = rule.message || '此项不能为空';
          } else if (value && rule.minLength && value.length < rule.minLength) {
            error = rule.message || `最少${rule.minLength}个字符`;
          } else if (value && rule.maxLength && value.length > rule.maxLength) {
            error = rule.message || `最多${rule.maxLength}个字符`;
          } else if (value && rule.pattern && !rule.pattern.test(value)) {
            error = rule.message || '格式不正确';
          } else if (value && rule.validator) {
            error = rule.validator(value);
          }

          if (error) {
            input.classList.add('error');
            if (errorEl) {
              errorEl.textContent = error;
              errorEl.classList.add('show');
            }
          } else {
            input.classList.remove('error');
            if (errorEl) {
              errorEl.classList.remove('show');
            }
          }
        }, 300);
      });

      input.addEventListener('blur', () => {
        clearTimeout(timer);
        if (!input.value.trim() && rule.required) {
          input.classList.add('error');
          const errorEl = formEl.querySelector(`[name="${name}"] + .form-error`);
          if (errorEl) {
            errorEl.textContent = rule.message || '此项不能为空';
            errorEl.classList.add('show');
          }
        }
      });
    });
  },

  formatMoney(amount) {
    return parseFloat(amount).toFixed(2);
  },

  formatDate(date, format = 'YYYY-MM-DD HH:mm:ss') {
    const d = new Date(date);
    const pad = (n) => n.toString().padStart(2, '0');
    return format
      .replace('YYYY', d.getFullYear())
      .replace('MM', pad(d.getMonth() + 1))
      .replace('DD', pad(d.getDate()))
      .replace('HH', pad(d.getHours()))
      .replace('mm', pad(d.getMinutes()))
      .replace('ss', pad(d.getSeconds()));
  },

  copyToClipboard(text) {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text);
    } else {
      const textarea = document.createElement('textarea');
      textarea.value = text;
      textarea.style.position = 'fixed';
      textarea.style.opacity = '0';
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand('copy');
      document.body.removeChild(textarea);
    }
    this.toastSuccess('已复制到剪贴板');
  },
};

document.addEventListener('DOMContentLoaded', () => App.init());
