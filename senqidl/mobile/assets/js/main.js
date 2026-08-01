(function() {
  'use strict';

  var MobileApp = {
    init: function() {
      this.initCarousel();
      this.initMenu();
      this.initTabBar();
      this.initScrollAnimations();
      this.initBackToTop();
      this.initLazyLoad();
      this.initFaq();
      this.initContactForm();
    },

    initCarousel: function() {
      var carousel = document.getElementById('carousel');
      if (!carousel) return;

      var track = carousel.querySelector('.carousel-track');
      var items = carousel.querySelectorAll('.carousel-item');
      var dots = carousel.querySelectorAll('.carousel-dots .dot');
      var currentIndex = 0;
      var total = items.length;
      var autoPlayTimer = null;
      var touchStartX = 0;
      var touchEndX = 0;
      var isTransitioning = false;

      function goToSlide(index) {
        if (isTransitioning || index === currentIndex) return;
        isTransitioning = true;
        currentIndex = (index + total) % total;
        if (track) {
          track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
        }
        dots.forEach(function(dot, i) {
          dot.classList.toggle('active', i === currentIndex);
        });
        setTimeout(function() { isTransitioning = false; }, 500);
      }

      function nextSlide() { goToSlide(currentIndex + 1); }
      function prevSlide() { goToSlide(currentIndex - 1); }

      function startAutoPlay() {
        stopAutoPlay();
        autoPlayTimer = setInterval(nextSlide, 4000);
      }

      function stopAutoPlay() {
        if (autoPlayTimer) {
          clearInterval(autoPlayTimer);
          autoPlayTimer = null;
        }
      }

      carousel.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
        stopAutoPlay();
      }, { passive: true });

      carousel.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        var diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 50) {
          if (diff > 0) {
            nextSlide();
          } else {
            prevSlide();
          }
        }
        startAutoPlay();
      }, { passive: true });

      carousel.addEventListener('touchmove', function(e) {
        var currentX = e.changedTouches[0].screenX;
        var diff = touchStartX - currentX;
        if (track) {
          track.style.transition = 'none';
          track.style.transform = 'translateX(calc(-' + (currentIndex * 100) + '% + ' + (diff * 0.3) + 'px))';
        }
      }, { passive: true });

      carousel.addEventListener('touchend', function() {
        if (track) {
          track.style.transition = '';
          track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
        }
      });

      startAutoPlay();
      carousel.addEventListener('mouseenter', stopAutoPlay);
      carousel.addEventListener('mouseleave', startAutoPlay);

      var playPauseBtn = document.getElementById('carouselPlayPause');
      if (playPauseBtn) {
        playPauseBtn.addEventListener('click', function() {
          if (autoPlayTimer) {
            stopAutoPlay();
          } else {
            startAutoPlay();
          }
        });
      }
    },

    initMenu: function() {
      var hamburger = document.getElementById('hamburger');
      var menu = document.getElementById('mobileMenu');
      var backdrop = document.getElementById('mobileMenuBackdrop');
      if (!hamburger || !menu) return;

      function toggleMenu(show) {
        var shouldShow = show !== undefined ? show : !menu.classList.contains('active');
        hamburger.classList.toggle('active', shouldShow);
        menu.classList.toggle('active', shouldShow);
        if (backdrop) backdrop.classList.toggle('active', shouldShow);
        document.body.style.overflow = shouldShow ? 'hidden' : '';
      }

      hamburger.addEventListener('click', function() { toggleMenu(); });
      if (backdrop) backdrop.addEventListener('click', function() { toggleMenu(false); });

      var menuLinks = menu.querySelectorAll('a');
      menuLinks.forEach(function(link) {
        link.addEventListener('click', function() { toggleMenu(false); });
      });
    },

    initTabBar: function() {
      var tabs = document.querySelectorAll('.tab-item');
      tabs.forEach(function(tab) {
        tab.addEventListener('click', function(e) {
          var href = tab.getAttribute('href');
          if (href && href !== '#' && href !== '') {
            return;
          }
          e.preventDefault();
          tabs.forEach(function(t) { t.classList.remove('active'); });
          tab.classList.add('active');
        });
      });
    },

    initScrollAnimations: function() {
      var reveals = document.querySelectorAll('.reveal');
      if (!reveals.length) return;

      if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
          entries.forEach(function(entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('visible');
              observer.unobserve(entry.target);
            }
          });
        }, { threshold: 0.1 });

        reveals.forEach(function(el) { observer.observe(el); });
      } else {
        reveals.forEach(function(el) { el.classList.add('visible'); });
      }
    },

    initBackToTop: function() {
      var btn = document.getElementById('backToTop');
      if (!btn) return;

      window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
          btn.classList.add('show');
        } else {
          btn.classList.remove('show');
        }
      }, { passive: true });

      btn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    },

    initLazyLoad: function() {
      var lazyImages = document.querySelectorAll('img[data-src]');
      if (!lazyImages.length) return;

      if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
          entries.forEach(function(entry) {
            if (entry.isIntersecting) {
              var img = entry.target;
              img.src = img.getAttribute('data-src');
              img.removeAttribute('data-src');
              img.classList.remove('lazy-img');
              observer.unobserve(img);
            }
          });
        }, { rootMargin: '100px' });

        lazyImages.forEach(function(img) { observer.observe(img); });
      } else {
        lazyImages.forEach(function(img) {
          img.src = img.getAttribute('data-src');
          img.classList.remove('lazy-img');
        });
      }
    },

    initFaq: function() {
      var questions = document.querySelectorAll('.faq-question');
      questions.forEach(function(btn) {
        btn.addEventListener('click', function() {
          var item = btn.parentElement;
          var answer = item.querySelector('.faq-answer');
          var icon = btn.querySelector('.faq-icon');
          var isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';

          if (isOpen) {
            answer.style.maxHeight = '0';
            answer.style.paddingTop = '0';
            answer.style.paddingBottom = '0';
            if (icon) icon.textContent = '+';
          } else {
            answer.style.maxHeight = answer.scrollHeight + 'px';
            answer.style.paddingTop = '0';
            answer.style.paddingBottom = '14px';
            if (icon) icon.textContent = '−';
          }
        });
      });
    },

    initContactForm: function() {
      var form = document.querySelector('.validate-form');
      if (!form) return;

      form.addEventListener('submit', function(e) {
        var required = form.querySelectorAll('[data-required]');
        var valid = true;
        required.forEach(function(input) {
          if (!input.value || !input.value.trim()) {
            valid = false;
            input.style.borderColor = '#e74c3c';
          } else {
            input.style.borderColor = '';
          }
        });

        var emailField = form.querySelector('input[type="email"]');
        if (emailField && emailField.value) {
          var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRe.test(emailField.value)) {
            valid = false;
            emailField.style.borderColor = '#e74c3c';
          }
        }

        var textarea = form.querySelector('textarea[data-required]');
        if (textarea && textarea.value && textarea.value.length < 10) {
          valid = false;
          textarea.style.borderColor = '#e74c3c';
        }

        if (!valid) {
          e.preventDefault();
        }
      });

      var inputs = form.querySelectorAll('.form-control');
      inputs.forEach(function(input) {
        input.addEventListener('input', function() {
          input.style.borderColor = '';
        });
      });
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { MobileApp.init(); });
  } else {
    MobileApp.init();
  }

  window.MobileApp = MobileApp;
})();