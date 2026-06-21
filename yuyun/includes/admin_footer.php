</main>
<div class="admin-overlay" id="adminOverlay"></div>
<div class="toast-container" id="toastContainer"></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('adminMenuToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminOverlay');
    if (toggle && sidebar) {
        toggle.style.display = 'flex';
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
});
</script>
</body>
</html>
