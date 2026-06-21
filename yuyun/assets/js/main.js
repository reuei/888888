document.addEventListener('DOMContentLoaded', function () {
    // Hamburger
    const hamburger = document.getElementById('hamburger');
    const mainNav = document.getElementById('mainNav');
    if (hamburger && mainNav) {
        hamburger.addEventListener('click', function () {
            mainNav.classList.toggle('open');
        });
    }

    // Hero slider
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    if (slides.length > 0) {
        let current = 0;
        const show = (idx) => {
            slides.forEach((s, i) => {
                s.classList.toggle('active', i === idx);
            });
            dots.forEach((d, i) => {
                d.classList.toggle('active', i === idx);
            });
        };
        dots.forEach((d, i) => d.addEventListener('click', () => { current = i; show(current); }));
        setInterval(() => {
            current = (current + 1) % slides.length;
            show(current);
        }, 5000);
    }

    // Back to top
    const backTop = document.getElementById('backTop');
    if (backTop) {
        backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    // Modals
    document.querySelectorAll('[data-modal]').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-modal');
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('active');
        });
    });
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('active');
        });
    });
    document.querySelectorAll('.modal-close').forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('.modal-overlay').classList.remove('active');
        });
    });

    // Product detail modal injection
    window.openProductModal = function (title, detail) {
        document.getElementById('modalProductTitle').textContent = title;
        document.getElementById('modalProductBody').innerHTML = detail;
        document.getElementById('productModal').classList.add('active');
    };

    // Sticky header shadow
    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.style.boxShadow = window.scrollY > 10 ? '0 2px 12px rgba(0,0,0,.08)' : 'none';
        });
    }
});
