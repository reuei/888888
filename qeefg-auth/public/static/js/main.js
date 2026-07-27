document.addEventListener('DOMContentLoaded', function() {
    initTheme();
    initLang();
    initBurgerMenu();
    initSidebar();
    initModals();
    initForms();
});

function initTheme() {
    const themeToggle = document.getElementById('theme-toggle');
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    document.documentElement.setAttribute('data-theme', currentTheme);
    
    if (themeToggle) {
        themeToggle.innerHTML = currentTheme === 'dark' ? '🌙' : '☀️';
        
        themeToggle.addEventListener('click', function() {
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', newTheme);
            document.documentElement.setAttribute('data-theme', newTheme);
            this.innerHTML = newTheme === 'dark' ? '🌙' : '☀️';
        });
    }
}

function initLang() {
    const langToggle = document.getElementById('lang-toggle');
    const currentLang = localStorage.getItem('lang') || 'zh';
    
    if (langToggle) {
        langToggle.innerHTML = currentLang === 'zh' ? 'CN' : 'EN';
        
        langToggle.addEventListener('click', function() {
            const newLang = currentLang === 'zh' ? 'en' : 'zh';
            localStorage.setItem('lang', newLang);
            this.innerHTML = newLang === 'zh' ? 'CN' : 'EN';
        });
    }
}

function initBurgerMenu() {
    const burger = document.querySelector('.burger-menu');
    const navLinks = document.querySelector('.nav-links');
    
    if (burger && navLinks) {
        burger.addEventListener('click', function() {
            this.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
    }
}

function initSidebar() {
    const sidebarItems = document.querySelectorAll('.sidebar-menu li.has-submenu');
    
    sidebarItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('open');
            const submenu = this.querySelector('.sidebar-submenu');
            if (submenu) {
                submenu.style.display = this.classList.contains('open') ? 'block' : 'none';
            }
        });
    });
    
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
}

function initModals() {
    const modals = document.querySelectorAll('.modal');
    const modalOpens = document.querySelectorAll('[data-modal]');
    const modalCloses = document.querySelectorAll('.modal-close');
    
    modalOpens.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });
    
    modalCloses.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    
    modals.forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
}

function initForms() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#ef4444';
                } else {
                    field.style.borderColor = '#e2e8f0';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showToast('请填写所有必填项', 'error');
            }
        });
    });
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '3000';
    toast.style.maxWidth = '400px';
    toast.innerHTML = message;
    
    document.body.appendChild(toast);
    
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(function() {
            toast.remove();
        }, 300);
    }, 3000);
}

function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        showToast('复制成功', 'success');
    } catch (err) {
        showToast('复制失败', 'error');
    }
    
    document.body.removeChild(textarea);
}

function formatMoney(amount) {
    return parseFloat(amount).toFixed(2);
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('zh-CN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function generateLicenseKey() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let key = '';
    for (let i = 0; i < 5; i++) {
        for (let j = 0; j < 4; j++) {
            key += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        if (i < 4) key += '-';
    }
    return key;
}

function toggleStatus(element, url) {
    const id = element.getAttribute('data-id');
    const currentStatus = element.getAttribute('data-status');
    const newStatus = currentStatus === '1' ? '0' : '1';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ id, status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.code === 200) {
            element.setAttribute('data-status', newStatus);
            element.innerHTML = newStatus === '1' ? 
                '<span class="status-badge status-active">启用</span>' : 
                '<span class="status-badge status-inactive">禁用</span>';
            showToast('状态更新成功', 'success');
        } else {
            showToast(data.msg, 'error');
        }
    })
    .catch(err => {
        showToast('操作失败', 'error');
    });
}
