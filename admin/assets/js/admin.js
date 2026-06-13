/**
 * 语云科技官网 - 后台管理系统脚本
 * Admin Panel JavaScript
 */

(function() {
  'use strict';

  const API_BASE = '/api';
  let authToken = localStorage.getItem('yuyun_admin_token') || '';

  // ========== API 工具 ==========
  async function adminFetch(endpoint, options = {}) {
    const headers = {
      'Content-Type': 'application/json',
      ...(authToken ? { 'Authorization': `Bearer ${authToken}` } : {}),
      ...options.headers
    };

    try {
      const res = await fetch(`${API_BASE}${endpoint}`, { ...options, headers });
      const data = await res.json();

      if (data.code === 401) {
        // Token失效，跳转登录
        logout();
        return null;
      }
      return data;
    } catch (e) {
      showToast('网络请求失败，请重试', 'error');
      return null;
    }
  }

  // ========== Toast 提示 ==========
  function showToast(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(40px)';
      toast.style.transition = 'all 0.3s';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  }
  window.showToast = showToast;

  // ========== 认证模块 ==========
  async function login(username, password) {
    const data = await adminFetch('/admin/login', {
      method: 'POST',
      body: JSON.stringify({ username, password })
    });

    if (data?.code === 200) {
      authToken = data.data.token;
      localStorage.setItem('yuyun_admin_token', authToken);
      showToast('登录成功，正在跳转...', 'success');
      setTimeout(() => showDashboard(), 800);
      return true;
    } else {
      showToast(data?.error || '登录失败，请检查用户名和密码', 'error');
      return false;
    }
  }

  async function logout() {
    await adminFetch('/admin/logout', { method: 'POST' });
    authToken = '';
    localStorage.removeItem('yuyun_admin_token');
    showLogin();
  }

  function isLoggedIn() {
    return !!authToken;
  }

  // ========== 页面切换 ==========
  function showLogin() {
    document.getElementById('loginPage').style.display = 'flex';
    document.getElementById('dashboardPage').style.display = 'none';
  }

  function showDashboard() {
    document.getElementById('loginPage').style.display = 'none';
    document.getElementById('dashboardPage').style.display = 'flex';
    loadDashboardData();
  }

  // ========== 加载仪表盘数据 ==========
  async function loadDashboardData() {
    try {
      const [home, products, partners] = await Promise.all([
        adminFetch('/home'),
        adminFetch('/products'),
        adminFetch('/partners')
      ]);

      if (home) updateStat('carouselCount', home.carousel?.length || 0);
      if (products) updateStat('productsCount', products.products?.length || 0);
      if (partners) updateStat('partnersCount', partners.partners?.length || 0);

      // 渲染各管理区域
      renderCarouselTable(home?.carousel || []);
      renderProductsTable(products?.products || []);
      renderPartnersTable(partners?.partners || []);
      await renderSettingsForm();
    } catch (e) {
      console.error('加载仪表盘数据失败:', e);
    }
  }

  function updateStat(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
  }

  // ========== 轮播图管理 ==========
  function renderCarouselTable(items) {
    const tbody = document.getElementById('carouselTableBody');
    if (!tbody) return;

    if (items.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">暂无轮播图数据</td></tr>';
      return;
    }

    tbody.innerHTML = items.map((item, i) => `
      <tr>
        <td>${i + 1}</td>
        <td><strong>${item.title}</strong></td>
        <td><span class="status-badge ${item.enabled ? 'success' : 'danger'}">${item.enabled ? '已启用' : '已禁用'}</span></td>
        <td style="font-size:0.8125rem;color:#888;">${item.order}</td>
        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${item.image || '-'}</td>
        <td>
          <div class="table-actions">
            <button class="btn-admin btn-admin-outline btn-admin-sm" onclick="editCarouselItem(${i})">编辑</button>
            <button class="btn-admin btn-admin-danger btn-admin-sm" onclick="deleteCarouselItem('${item.id}')">删除</button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  window.editCarouselItem = function(index) {
    showToast('请在下方表单中修改后保存', 'info');
  };

  window.deleteCarouselItem = function(id) {
    if (confirm('确定要删除此轮播项吗？')) {
      showToast('删除功能需要在完整环境中操作', 'info');
    }
  };

  // ========== 产品管理 ==========
  function renderProductsTable(products) {
    const tbody = document.getElementById('productsTableBody');
    if (!tbody) return;

    if (products.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#999;padding:32px;">暂无产品数据</td></tr>';
      return;
    }

    tbody.innerHTML = products.map((p, i) => `
      <tr>
        <td>${i + 1}</td>
        <td><strong>${p.name}</strong></td>
        <td><span class="tag">${p.category}</span></td>
        <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.8125rem;color:#888;">${p.description}</td>
        <td><span class="status-badge ${p.enabled ? 'success' : 'danger'}">${p.enabled ? '启用' : '禁用'}</span></td>
        <td>
          <div class="table-actions">
            <button class="btn-admin btn-admin-outline btn-admin-sm">编辑</button>
            <button class="btn-admin btn-admin-danger btn-admin-sm">删除</button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  // ========== 合作伙伴管理 ==========
  function renderPartnersTable(partners) {
    const tbody = document.getElementById('partnersTableBody');
    if (!tbody) return;

    if (partners.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#999;padding:32px;">暂无合作伙伴数据</td></tr>';
      return;
    }

    tbody.innerHTML = partners.map((p, i) => `
      <tr>
        <td>${i + 1}</td>
        <td><strong>${p.name}</strong></td>
        <td style="max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.8125rem;color:#888;">${p.description || '-'}</td>
        <td style="font-size:0.8125rem;">${p.order || 0}</td>
        <td>
          <div class="table-actions">
            <button class="btn-admin btn-admin-outline btn-admin-sm">编辑</button>
            <button class="btn-admin btn-admin-danger btn-admin-sm">删除</button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  // ========== 全局设置渲染 ==========
  async function renderSettingsForm() {
    const settings = await adminFetch('/settings');
    if (!settings) return;

    // 公告设置
    const annInput = document.getElementById('announcementContent');
    if (annInput && settings.announcement?.content) {
      annInput.value = settings.announcement.content;
    }
    const annTitle = document.getElementById('announcementTitle');
    if (annTitle && settings.announcement?.title) {
      annTitle.value = settings.announcement.title;
    }

    // 页脚设置
    const phoneInput = document.getElementById('footerPhone');
    if (phoneInput && settings.footer?.salesPhone) {
      phoneInput.value = settings.footer.salesPhone;
    }
    const icpInput = document.getElementById('footerIcp');
    if (icpInput && settings.footer?.icp) {
      icpInput.value = settings.footer.icp;
    }
    const policeInput = document.getElementById('footerPolice');
    if (policeInput && settings.footer?.policeCode) {
      policeInput.value = settings.footer.policeCode;
    }
    const licenseInput = document.getElementById('footerLicense');
    if (licenseInput && settings.footer?.license) {
      licenseInput.value = settings.footer.license;
    }
    const declInput = document.getElementById('footerDeclaration');
    if (declInput && settings.footer?.declaration) {
      declInput.value = settings.footer.declaration;
    }
    const intlUrlInput = document.getElementById('internationalUrl');
    if (intlUrlInput && settings.internationalUrl) {
      intlUrlInput.value = settings.internationalUrl;
    }
  }

  // ========== 保存设置 ==========
  window.saveSettings = async function(section) {
    const settings = {};

    if (section === 'announcement') {
      settings.announcement = {
        enabled: document.getElementById('announcementEnabled')?.checked ?? true,
        title: document.getElementById('announcementTitle')?.value || '',
        content: document.getElementById('announcementContent')?.value || '',
        headerColor: document.getElementById('headerColor')?.value || '#0052D9',
        buttonColor: document.getElementById('buttonColor')?.value || '#FF6B00',
        showOnceDaily: document.getElementById('showOnceDaily')?.checked ?? true
      };
    } else if (section === 'footer') {
      settings.footer = {
        salesPhone: document.getElementById('footerPhone')?.value || '',
        icp: document.getElementById('footerIcp')?.value || '',
        icpUrl: document.getElementById('footerIcpUrl')?.value || '',
        policeCode: document.getElementById('footerPolice')?.value || '',
        policeUrl: document.getElementById('footerPoliceUrl')?.value || '',
        license: document.getElementById('footerLicense')?.value || '',
        declaration: document.getElementById('footerDeclaration')?.value || ''
      };
    } else if (section === 'international') {
      settings.internationalUrl = document.getElementById('internationalUrl')?.value || '';
    }

    const result = await adminFetch('/admin/settings', {
      method: 'PUT',
      body: JSON.stringify({ settings })
    });

    if (result?.code === 200) {
      showToast('设置保存成功！', 'success');
    } else {
      showToast(result?.error || '保存失败', 'error');
    }
  };

  // ========== 标签页切换 ==========
  window.switchTab = function(tabId) {
    document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.admin-tab-content').forEach(c => c.classList.remove('active'));

    event.target.classList.add('active');
    document.getElementById(tabId)?.classList.add('active');
  };

  // ========== 侧边栏导航切换 ==========
  window.switchSection = function(sectionId) {
    document.querySelectorAll('.sidebar-nav-item').forEach(item => item.classList.remove('active'));
    event.target.closest('.sidebar-nav-item')?.classList.add('active');

    document.querySelectorAll('.admin-section').forEach(s => s.style.display = 'none');
    const section = document.getElementById(sectionId);
    if (section) {
      section.style.display = 'block';
      const titleEl = document.querySelector('.admin-header-left h2');
      if (titleEl) titleEl.textContent = event.target.textContent.trim();
    }
  };

  // ========== DOM Ready ==========
  document.addEventListener('DOMContentLoaded', () => {
    // 检查登录状态
    if (isLoggedIn()) {
      showDashboard();
    } else {
      showLogin();
    }

    // 登录表单提交
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
      loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const username = document.getElementById('loginUsername').value.trim();
        const password = document.getElementById('loginPassword').value.trim();

        if (!username || !password) {
          document.getElementById('loginError').classList.add('show');
          document.getElementById('loginError').textContent = '请输入用户名和密码';
          return;
        }

        document.getElementById('loginError').classList.remove('show');

        const success = await login(username, password);
        if (!success) {
          document.getElementById('loginError').classList.add('show');
          document.getElementById('loginError').textContent = '用户名或密码错误';
        }
      });
    }

    // 登出按钮
    document.getElementById('logoutBtn')?.addEventListener('click', () => {
      if (confirm('确定要退出登录吗？')) {
        logout();
      }
    });

    // 默认显示仪表盘
    document.getElementById('sectionDashboard')?.style.setProperty('display', 'block', 'important');
  });

})();
