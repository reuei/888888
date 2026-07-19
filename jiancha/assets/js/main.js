/* 人民检察 V8 - Interaction */
(function() {
  'use strict';
  var d = document;

  /* Mobile Menu */
  function initMobile() {
    var btn = d.getElementById('menuBtn');
    var menu = d.getElementById('mobileMenu');
    var close = d.getElementById('mobileClose');
    if (!btn || !menu) return;
    btn.onclick = function() { menu.classList.add('open'); };
    close.onclick = function() { menu.classList.remove('open'); };
    menu.onclick = function(e) { if (e.target === menu) menu.classList.remove('open'); };
  }

  /* Slider */
  function initSlider() {
    var sl = d.querySelector('.slider');
    if (!sl) return;
    var track = sl.querySelector('.slider-track');
    var items = sl.querySelectorAll('.slider-item');
    var dots = sl.querySelectorAll('.slider-dots span');
    var bar = sl.querySelector('.slider-progress-bar');
    var prev = sl.querySelector('.slider-arrow.prev');
    var next = sl.querySelector('.slider-arrow.next');
    if (items.length < 2) return;

    var idx = 0, start = 0, dur = 5000, paused = false, timer = null;

    function go(n) {
      idx = (n + items.length) % items.length;
      track.style.transform = 'translateX(-' + (idx * 100) + '%)';
      dots.forEach(function(d, i) { d.classList.toggle('on', i === idx); });
      start = Date.now();
      if (bar) bar.style.width = '0';
    }
    function tick() {
      if (paused) return;
      var el = Date.now() - start;
      if (el >= dur) { go(idx + 1); } else { if (bar) bar.style.width = (el / dur * 100) + '%'; }
      timer = requestAnimationFrame(tick);
    }
    function play() { paused = false; start = Date.now() - (bar ? parseFloat(bar.style.width) / 100 * dur : 0); timer = requestAnimationFrame(tick); }
    function stop() { paused = true; if (timer) cancelAnimationFrame(timer); }

    prev.onclick = function() { go(idx - 1); };
    next.onclick = function() { go(idx + 1); };
    dots.forEach(function(d, i) { d.onclick = function() { go(i); }; });
    sl.addEventListener('mouseenter', stop);
    sl.addEventListener('mouseleave', play);
    var sx = 0;
    sl.addEventListener('touchstart', function(e) { sx = e.touches[0].clientX; stop(); });
    sl.addEventListener('touchend', function(e) {
      var ex = e.changedTouches[0].clientX;
      if (Math.abs(ex - sx) > 40) go(idx + (ex < sx ? 1 : -1));
      play();
    });
    go(0); play();
  }

  /* Form validation */
  function initForms() {
    var validators = {
      username: function(v) {
        if (!v) return { ok: false, msg: '请输入用户名' };
        if (v.length < 3) return { ok: false, msg: '至少3个字符' };
        if (v.length > 20) return { ok: false, msg: '不能超过20字符' };
        if (!/^[\w\u4e00-\u9fa5]+$/.test(v)) return { ok: false, msg: '含非法字符' };
        return { ok: true, msg: '格式正确' };
      },
      password: function(v) {
        if (!v) return { ok: false, msg: '请输入密码' };
        if (v.length < 6) return { ok: false, msg: '至少6位' };
        if (/^\d+$/.test(v)) return { ok: false, msg: '不能纯数字' };
        return { ok: true, msg: '密码可用' };
      },
      confirm_password: function(v) {
        var p = d.querySelector('input[name=password]');
        if (!v) return { ok: false, msg: '请再次输入' };
        if (p && p.value !== v) return { ok: false, msg: '两次不一致' };
        return { ok: true, msg: '密码一致' };
      },
      email: function(v) {
        if (!v) return { ok: true, msg: '' };
        if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v)) return { ok: false, msg: '邮箱格式错误' };
        return { ok: true, msg: '邮箱格式正确' };
      },
      phone: function(v) {
        if (!v) return { ok: true, msg: '' };
        if (!/^1[3-9]\d{9}$/.test(v)) return { ok: false, msg: '11位手机号' };
        return { ok: true, msg: '手机号格式正确' };
      },
      title: function(v) {
        if (!v) return { ok: false, msg: '请输入标题' };
        if (v.length < 4) return { ok: false, msg: '至少4字符' };
        return { ok: true, msg: '符合要求' };
      },
      content: function(v) {
        if (!v) return { ok: false, msg: '请输入内容' };
        if (v.length < 10) return { ok: false, msg: '至少10字符' };
        return { ok: true, msg: '内容长度合适' };
      }
    };

    d.querySelectorAll('input[data-v], textarea[data-v]').forEach(function(el) {
      var tip = el.parentNode.querySelector('.auth-tip');
      if (!tip) { tip = d.createElement('div'); tip.className = 'auth-tip'; el.parentNode.appendChild(tip); }
      el.addEventListener('input', function() {
        var v = validators[el.getAttribute('data-v')];
        if (!v) return;
        var r = v(el.value);
        el.classList.remove('ok', 'err');
        tip.classList.remove('ok', 'err');
        if (el.value) {
          if (r.ok) { el.classList.add('ok'); tip.classList.add('ok'); } else { el.classList.add('err'); tip.classList.add('err'); }
          tip.textContent = r.msg;
        } else { tip.textContent = ''; }
      });
    });
  }

  /* Toast */
  window.showToast = function(msg, type) {
    var t = d.createElement('div');
    t.className = 'toast ' + (type || '');
    t.textContent = msg;
    d.body.appendChild(t);
    setTimeout(function() { t.style.opacity = '0'; t.style.transition = '.3s'; setTimeout(function() { t.remove(); }, 300); }, 2500);
  };

  /* Modal */
  window.showModal = function(title, body) {
    var m = d.createElement('div');
    m.className = 'modal';
    m.innerHTML = '<div class="modal-box"><div class="modal-head"><h3>' + title + '</h3><button class="modal-close" onclick="this.closest(\'.modal\').remove()">&times;</button></div><div class="modal-body">' + body + '</div><div class="modal-foot"><button class="btn btn-primary btn-sm" onclick="this.closest(\'.modal\').remove()">确定</button></div></div>';
    d.body.appendChild(m);
    m.onclick = function(e) { if (e.target === m) m.remove(); };
  };

  /* Admin mobile */
  function initAdmin() {
    var btn = d.getElementById('adminMenuBtn');
    var side = d.querySelector('.admin-side');
    if (!btn || !side) return;
    btn.onclick = function() { side.classList.toggle('open'); };
    d.addEventListener('click', function(e) {
      if (side.classList.contains('open') && !side.contains(e.target) && e.target !== btn) side.classList.remove('open');
    });
  }

  d.addEventListener('DOMContentLoaded', function() {
    initMobile();
    initSlider();
    initForms();
    initAdmin();
  });
})();