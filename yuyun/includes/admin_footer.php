</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('adminMenuToggle');
    const sidebar = document.getElementById('adminSidebar');
    if (toggle && sidebar) {
        toggle.style.display = 'flex';
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    }
});
</script>
</body>
</html>
