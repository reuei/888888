document.addEventListener('DOMContentLoaded', function() {
    var mobileToggle = document.getElementById('mobileToggle');
    var mobileMenu = document.getElementById('mobileMenu');
    var mobileOverlay = document.getElementById('mobileOverlay');
    var mobileClose = document.getElementById('mobileClose');

    function openMobileMenu() {
        if (mobileMenu) mobileMenu.classList.add('open');
        if (mobileOverlay) mobileOverlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        if (mobileMenu) mobileMenu.classList.remove('open');
        if (mobileOverlay) mobileOverlay.style.display = 'none';
        document.body.style.overflow = '';
    }

    if (mobileToggle) mobileToggle.addEventListener('click', openMobileMenu);
    if (mobileClose) mobileClose.addEventListener('click', closeMobileMenu);
    if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileMenu);

    var submenuToggles = document.querySelectorAll('.mobile-nav .has-submenu > a');
    submenuToggles.forEach(function(link) {
        link.addEventListener('click', function(e) {
            var submenu = link.nextElementSibling;
            if (submenu && submenu.classList.contains('submenu')) {
                e.preventDefault();
                submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
            }
        });
    });
});
