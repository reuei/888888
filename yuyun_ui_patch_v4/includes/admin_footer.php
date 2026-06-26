</main>
<div class="admin-overlay" id="adminOverlay"></div>
<div class="toast-container" id="toastContainer"></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('adminMenuToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminOverlay');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('active', sidebar.classList.contains('open'));
        });
        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }
    }

    // Flash to toast
    document.querySelectorAll('.flash-data').forEach(function(el) {
        const type = el.dataset.type || 'info';
        const msg = el.dataset.message || '';
        if (typeof showToast === 'function') showToast(msg, type);
        el.remove();
    });

    // Admin theme toggle
    const adminThemeToggle = document.getElementById('adminThemeToggle');
    if (adminThemeToggle) {
        adminThemeToggle.addEventListener('click', function () {
            document.documentElement.classList.toggle('dark');
            try {
                localStorage.setItem('yy_theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            } catch(e){}
        });
    }

    // Admin language popup toggle
    var adminLangBtn = document.getElementById('adminLangSwitcherBtn');
    var adminLangPopup = document.getElementById('adminLangPopup');
    if (adminLangBtn && adminLangPopup) {
        adminLangBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            adminLangPopup.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (!adminLangPopup.contains(e.target) && e.target !== adminLangBtn) {
                adminLangPopup.classList.remove('open');
            }
        });
    }

    // Toast helper
    window.showToast = function (message, type) {
        type = type || 'info';
        var container = document.getElementById('toastContainer');
        if (!container) return;
        var icons = { success: 'check-circle', error: 'alert-circle', info: 'info' };
        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        toast.innerHTML = '<i class="iconfont icon-' + icons[type] + '"></i><span>' + message + '</span>';
        container.appendChild(toast);
        setTimeout(function() { toast.remove(); }, 4000);
    };
});
</script>
</body>
</html>
