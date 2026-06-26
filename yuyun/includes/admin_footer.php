</main>
<div class="admin-overlay" id="adminOverlay"></div>
<div class="toast-container" id="toastContainer"></div>
<script src="<?php echo YUYUN_URL ?>/assets/js/main.js"></script>
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
});
</script>
</body>
</html>
