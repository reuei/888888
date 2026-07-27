/**
 * QEEFG授权站 JavaScript
 */

// 汉堡菜单切换
function toggleMenu() {
    const menu = document.getElementById('fullscreen-menu');
    const toggle = document.querySelector('.menu-toggle');
    
    if (menu.classList.contains('active')) {
        closeMenu();
    } else {
        openMenu();
    }
}

function openMenu() {
    const menu = document.getElementById('fullscreen-menu');
    const toggle = document.querySelector('.menu-toggle');
    menu.classList.add('active');
    toggle.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMenu() {
    const menu = document.getElementById('fullscreen-menu');
    const toggle = document.querySelector('.menu-toggle');
    menu.classList.remove('active');
    toggle.classList.remove('active');
    document.body.style.overflow = '';
}

// 二级菜单展开/收起
function toggleSubmenu(element) {
    const menuItem = element.closest('.menu-item');
    if (menuItem) {
        menuItem.classList.toggle('expanded');
        const icon = element.querySelector('.menu-arrow');
        if (icon) {
            icon.textContent = menuItem.classList.contains('expanded') ? '▼' : '▶';
        }
    }
}

// 语言切换
function switchLang(lang) {
    // 更新按钮状态
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`.lang-btn[data-lang="${lang}"]`).classList.add('active');
    
    // 存储语言设置
    localStorage.setItem('language', lang);
    
    // 这里可以添加实际的语言切换逻辑
    console.log('切换语言:', lang);
}

// 昼夜模式切换
function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    
    // 更新按钮图标
    const themeBtn = document.querySelector('.theme-toggle');
    if (themeBtn) {
        themeBtn.innerHTML = isDark ? '☀️' : '🌙';
    }
}

// 初始化主题
function initTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        const themeBtn = document.querySelector('.theme-toggle');
        if (themeBtn) {
            themeBtn.innerHTML = '☀️';
        }
    }
}

// 初始化语言
function initLanguage() {
    const savedLang = localStorage.getItem('language') || 'zh';
    const langBtn = document.querySelector(`.lang-btn[data-lang="${savedLang}"]`);
    if (langBtn) {
        langBtn.classList.add('active');
    }
}

// 表单验证
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            showError(input, '此项为必填项');
        } else {
            clearError(input);
        }
    });
    
    return isValid;
}

// 显示错误信息
function showError(input, message) {
    clearError(input);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#f5222d';
    errorDiv.style.fontSize = '14px';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;
    
    input.parentNode.appendChild(errorDiv);
    input.style.borderColor = '#f5222d';
}

// 清除错误信息
function clearError(input) {
    const error = input.parentNode.querySelector('.error-message');
    if (error) {
        error.remove();
    }
    input.style.borderColor = '#e0e0e0';
}

// 显示消息提示
function showMessage(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// AJAX请求封装
function ajax(url, method = 'GET', data = null) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    resolve(response);
                } catch (e) {
                    reject(e);
                }
            } else {
                reject(new Error('请求失败'));
            }
        };
        
        xhr.onerror = function() {
            reject(new Error('网络错误'));
        };
        
        xhr.send(data ? JSON.stringify(data) : null);
    });
}

// 用户登录
async function userLogin(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        const response = await ajax('/user/dologin', 'POST', {
            username: formData.get('username'),
            password: formData.get('password')
        });
        
        if (response.code === 200) {
            showMessage('登录成功', 'success');
            setTimeout(() => {
                window.location.href = response.data.redirect || '/user/dashboard';
            }, 1000);
        } else {
            showMessage(response.msg || '登录失败', 'error');
        }
    } catch (error) {
        showMessage('登录失败，请稍后重试', 'error');
    }
}

// 用户注册
async function userRegister(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        const response = await ajax('/user/doregister', 'POST', {
            username: formData.get('username'),
            email: formData.get('email'),
            password: formData.get('password'),
            qq: formData.get('qq'),
            phone: formData.get('phone')
        });
        
        if (response.code === 200) {
            showMessage('注册成功', 'success');
            setTimeout(() => {
                window.location.href = response.data.redirect || '/user/login';
            }, 1000);
        } else {
            showMessage(response.msg || '注册失败', 'error');
        }
    } catch (error) {
        showMessage('注册失败，请稍后重试', 'error');
    }
}

// 授权查询
async function queryLicense(event) {
    event.preventDefault();
    
    const form = event.target;
    const licenseKey = form.querySelector('input[name="license_key"]').value;
    
    if (!licenseKey) {
        showMessage('请输入授权密钥', 'error');
        return;
    }
    
    try {
        const response = await ajax('/license-query', 'POST', {
            license_key: licenseKey
        });
        
        if (response.code === 200) {
            displayLicenseInfo(response.data);
        } else {
            showMessage(response.msg || '查询失败', 'error');
        }
    } catch (error) {
        showMessage('查询失败，请稍后重试', 'error');
    }
}

// 显示授权信息
function displayLicenseInfo(data) {
    const resultDiv = document.getElementById('license-result');
    if (!resultDiv) return;
    
    const statusClass = data.license.status ? 'status-success' : 'status-error';
    const statusText = data.license.status ? '正常' : '禁用';
    const expireText = data.license.expires_at || '永久有效';
    
    resultDiv.innerHTML = `
        <div class="card">
            <h3 class="card-title">授权信息</h3>
            <div class="license-info">
                <p><strong>授权密钥:</strong> ${data.license.license_key}</p>
                <p><strong>产品名称:</strong> ${data.product.name}</p>
                <p><strong>用户名:</strong> ${data.user.username}</p>
                <p><strong>状态:</strong> <span class="status ${statusClass}">${statusText}</span></p>
                <p><strong>到期时间:</strong> ${expireText}</p>
                <p><strong>激活时间:</strong> ${data.license.activated_at || '未激活'}</p>
            </div>
        </div>
    `;
    
    resultDiv.style.display = 'block';
}

// 复制到剪贴板
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showMessage('已复制到剪贴板', 'success');
        }).catch(() => {
            showMessage('复制失败', 'error');
        });
    } else {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        showMessage('已复制到剪贴板', 'success');
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', function() {
    // 初始化主题和语言
    initTheme();
    initLanguage();
    
    // 绑定汉堡菜单事件
    const menuToggle = document.querySelector('.menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', toggleMenu);
    }
    
    // 绑定关闭菜单按钮
    const menuClose = document.querySelector('.menu-close');
    if (menuClose) {
        menuClose.addEventListener('click', closeMenu);
    }
    
    // 绑定二级菜单事件
    document.querySelectorAll('.menu-link.has-submenu').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSubmenu(this);
        });
    });
    
    // 绑定主题切换
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
    
    // 绑定语言切换
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            switchLang(this.dataset.lang);
        });
    });
    
    // 绑定登录表单
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', userLogin);
    }
    
    // 绑定注册表单
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', userRegister);
    }
    
    // 绑定授权查询表单
    const licenseQueryForm = document.getElementById('license-query-form');
    if (licenseQueryForm) {
        licenseQueryForm.addEventListener('submit', queryLicense);
    }
    
    // 添加平滑滚动
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});

// 防抖函数
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// 节流函数
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// 导出函数供全局使用
window.toggleMenu = toggleMenu;
window.closeMenu = closeMenu;
window.toggleSubmenu = toggleSubmenu;
window.switchLang = switchLang;
window.toggleTheme = toggleTheme;
window.copyToClipboard = copyToClipboard;