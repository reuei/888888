document.addEventListener('DOMContentLoaded', function() {
    // Header scroll
    const header = document.querySelector('.header');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // Mobile menu
    const hamburger = document.querySelector('.hamburger');
    const mobileNav = document.querySelector('.mobile-nav');
    if (hamburger && mobileNav) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            mobileNav.classList.toggle('active');
        });
        mobileNav.querySelectorAll('a').forEach(function(a) {
            a.addEventListener('click', function() {
                hamburger.classList.remove('active');
                mobileNav.classList.remove('active');
            });
        });
    }

    // Hero slider
    const slider = document.querySelector('.hero-slider');
    if (slider) {
        const slides = slider.querySelectorAll('.hero-slide');
        const dots = slider.querySelectorAll('.hero-dot');
        const prev = slider.querySelector('.hero-prev');
        const next = slider.querySelector('.hero-next');
        let current = 0;
        let timer = null;

        function showSlide(index) {
            slides.forEach(function(s, i) {
                s.classList.toggle('active', i === index);
            });
            dots.forEach(function(d, i) {
                d.classList.toggle('active', i === index);
            });
            current = index;
        }

        function nextSlide() {
            showSlide((current + 1) % slides.length);
        }

        function prevSlide() {
            showSlide((current - 1 + slides.length) % slides.length);
        }

        function startAuto() {
            if (timer) clearInterval(timer);
            timer = setInterval(nextSlide, 5000);
        }

        if (slides.length > 1) {
            if (prev) prev.addEventListener('click', function() { prevSlide(); startAuto(); });
            if (next) next.addEventListener('click', function() { nextSlide(); startAuto(); });
            dots.forEach(function(d, i) {
                d.addEventListener('click', function() { showSlide(i); startAuto(); });
            });
            startAuto();
        }
    }

    // Modal
    const modalOverlay = document.getElementById('globalModal');
    const modalTitle = document.getElementById('globalModalTitle');
    const modalBody = document.getElementById('globalModalBody');

    window.openModal = function(title, html) {
        if (!modalOverlay) return;
        if (modalTitle) modalTitle.textContent = title;
        if (modalBody) modalBody.innerHTML = html;
        modalOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeModal = function() {
        if (!modalOverlay) return;
        modalOverlay.classList.remove('active');
        document.body.style.overflow = '';
    };

    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) closeModal();
        });
    }

    // Product detail modals
    document.querySelectorAll('[data-product-detail]').forEach(function(el) {
        el.addEventListener('click', function() {
            const title = this.dataset.title || '产品详情';
            const detail = this.dataset.detail || '';
            const image = this.dataset.image || '';
            let html = '';
            if (image) {
                html += '<img src="' + image + '" alt="' + title + '" style="width:100%;border-radius:8px;margin-bottom:16px;">';
            }
            html += '<p style="line-height:1.8;color:#555;">' + detail + '</p>';
            openModal(title, html);
        });
    });

    // Certificate lightbox
    document.querySelectorAll('[data-cert]').forEach(function(el) {
        el.addEventListener('click', function() {
            const title = this.dataset.title || '';
            const image = this.dataset.image || '';
            const desc = this.dataset.desc || '';
            let html = '';
            if (image) {
                html += '<img src="' + image + '" alt="' + title + '" style="width:100%;border-radius:8px;margin-bottom:16px;">';
            }
            html += '<p style="line-height:1.8;color:#555;">' + desc + '</p>';
            openModal(title, html);
        });
    });

    // Popup modal (home page)
    const popup = document.getElementById('homePopup');
    if (popup) {
        const shown = sessionStorage.getItem('yuyun_popup_shown');
        if (!shown) {
            setTimeout(function() {
                popup.classList.add('active');
                sessionStorage.setItem('yuyun_popup_shown', '1');
            }, 1500);
        }
        const popupClose = popup.querySelector('.modal-close, .popup-close');
        if (popupClose) {
            popupClose.addEventListener('click', function() {
                popup.classList.remove('active');
            });
        }
        popup.addEventListener('click', function(e) {
            if (e.target === popup) popup.classList.remove('active');
        });
    }

    // Toast helper
    window.showToast = function(message, type) {
        type = type || 'success';
        let toast = document.getElementById('globalToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'globalToast';
            toast.className = 'toast toast-' + type;
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        toast.className = 'toast toast-' + type;
        setTimeout(function() { toast.classList.add('show'); }, 10);
        setTimeout(function() { toast.classList.remove('show'); }, 3000);
    };

    // Contact form AJAX
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(contactForm);
            fetch('ajax/message.php', {
                method: 'POST',
                body: formData
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast(data.message || '提交成功', 'success');
                    contactForm.reset();
                } else {
                    showToast(data.message || '提交失败', 'error');
                }
            })
            .catch(function() {
                showToast('网络错误，请稍后重试', 'error');
            });
        });
    }

    // Scroll reveal
    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        revealEls.forEach(function(el) { observer.observe(el); });
    }

    // Float buttons
    const floatServiceBtn = document.getElementById('floatServiceBtn');
    const floatPanel = document.querySelector('.float-panel');
    if (floatServiceBtn && floatPanel) {
        floatServiceBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            floatPanel.classList.toggle('active');
        });
        document.addEventListener('click', function(e) {
            if (!floatPanel.contains(e.target) && e.target !== floatServiceBtn) {
                floatPanel.classList.remove('active');
            }
        });
    }

    const floatTopBtn = document.getElementById('floatTopBtn');
    if (floatTopBtn) {
        floatTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        window.addEventListener('scroll', function() {
            floatTopBtn.style.opacity = window.scrollY > 300 ? '1' : '0';
            floatTopBtn.style.pointerEvents = window.scrollY > 300 ? 'auto' : 'none';
        });
        floatTopBtn.style.opacity = '0';
        floatTopBtn.style.pointerEvents = 'none';
    }

    const qrModal = document.getElementById('qrModal');
    const floatWechatBtn = document.getElementById('floatWechatBtn');
    const qrClose = document.getElementById('qrClose');
    if (qrModal && floatWechatBtn) {
        floatWechatBtn.addEventListener('click', function() {
            qrModal.classList.add('active');
        });
        if (qrClose) qrClose.addEventListener('click', function() { qrModal.classList.remove('active'); });
        qrModal.addEventListener('click', function(e) { if (e.target === qrModal) qrModal.classList.remove('active'); });
    }
});
