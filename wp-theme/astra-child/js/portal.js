/**
 * Gornji Milanovac Portal - Main JavaScript
 *
 * @package GM_Portal
 */

(function($) {
    'use strict';

    /**
     * Hero Slider
     */
    const HeroSlider = {
        $slider: null,
        $slides: null,
        $dots: null,
        currentSlide: 0,
        slideCount: 0,
        autoplayInterval: null,
        autoplayDelay: 5000,

        init: function() {
            this.$slider = $('#gm-hero-slider');
            if (!this.$slider.length) return;

            this.$slides = this.$slider.find('.gm-hero-slide');
            this.$dots = $('.gm-hero-dot');
            this.slideCount = this.$slides.length;

            if (this.slideCount <= 1) return;

            this.bindEvents();
            this.startAutoplay();
        },

        bindEvents: function() {
            const self = this;

            // Navigation buttons
            $('.gm-hero-prev').on('click', function() {
                self.prevSlide();
            });

            $('.gm-hero-next').on('click', function() {
                self.nextSlide();
            });

            // Dot navigation
            this.$dots.on('click', function() {
                const index = $(this).data('slide');
                self.goToSlide(index);
            });

            // Pause autoplay on hover
            this.$slider.on('mouseenter', function() {
                self.stopAutoplay();
            }).on('mouseleave', function() {
                self.startAutoplay();
            });

            // Touch support
            let touchStartX = 0;
            let touchEndX = 0;

            this.$slider.on('touchstart', function(e) {
                touchStartX = e.originalEvent.touches[0].clientX;
            });

            this.$slider.on('touchend', function(e) {
                touchEndX = e.originalEvent.changedTouches[0].clientX;
                const diff = touchStartX - touchEndX;

                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        self.nextSlide();
                    } else {
                        self.prevSlide();
                    }
                }
            });

            // Keyboard navigation
            $(document).on('keydown', function(e) {
                if (!self.$slider.is(':visible')) return;

                if (e.key === 'ArrowLeft') {
                    self.prevSlide();
                } else if (e.key === 'ArrowRight') {
                    self.nextSlide();
                }
            });
        },

        goToSlide: function(index) {
            if (index < 0) index = this.slideCount - 1;
            if (index >= this.slideCount) index = 0;

            this.$slides.removeClass('active').eq(index).addClass('active');
            this.$dots.removeClass('active').eq(index).addClass('active');
            this.currentSlide = index;

            // Reset autoplay
            this.stopAutoplay();
            this.startAutoplay();
        },

        nextSlide: function() {
            this.goToSlide(this.currentSlide + 1);
        },

        prevSlide: function() {
            this.goToSlide(this.currentSlide - 1);
        },

        startAutoplay: function() {
            const self = this;
            this.autoplayInterval = setInterval(function() {
                self.nextSlide();
            }, this.autoplayDelay);
        },

        stopAutoplay: function() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
                this.autoplayInterval = null;
            }
        }
    };

    /**
     * Mobile Menu
     */
    const MobileMenu = {
        $toggle: null,
        $menu: null,
        isOpen: false,

        init: function() {
            this.$toggle = $('.gm-nav-toggle');
            this.$menu = $('.gm-nav-menu');

            if (!this.$toggle.length) return;

            this.bindEvents();
        },

        bindEvents: function() {
            const self = this;

            this.$toggle.on('click', function() {
                self.toggle();
            });

            // Close menu on link click
            this.$menu.find('a').on('click', function() {
                if (window.innerWidth < 992) {
                    self.close();
                }
            });

            // Close menu on outside click
            $(document).on('click', function(e) {
                if (self.isOpen && !$(e.target).closest('.gm-nav').length) {
                    self.close();
                }
            });

            // Close menu on escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && self.isOpen) {
                    self.close();
                }
            });

            // Handle dropdown in mobile
            $('.gm-nav-item.has-dropdown > a').on('click', function(e) {
                if (window.innerWidth < 992) {
                    e.preventDefault();
                    $(this).parent().toggleClass('open');
                }
            });

            // Handle resize
            $(window).on('resize', function() {
                if (window.innerWidth >= 992 && self.isOpen) {
                    self.close();
                }
            });
        },

        toggle: function() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        },

        open: function() {
            this.$toggle.addClass('active').attr('aria-expanded', 'true');
            this.$menu.addClass('active');
            this.isOpen = true;
            $('body').css('overflow', 'hidden');
        },

        close: function() {
            this.$toggle.removeClass('active').attr('aria-expanded', 'false');
            this.$menu.removeClass('active');
            this.isOpen = false;
            $('body').css('overflow', '');
        }
    };

    /**
     * Category Tabs
     */
    const CategoryTabs = {
        init: function() {
            const $tabs = $('.gm-cat-tab');
            if (!$tabs.length) return;

            $tabs.on('click', function() {
                const category = $(this).data('category');
                $tabs.removeClass('active');
                $(this).addClass('active');

                CategoryTabs.loadPosts(category);
            });
        },

        loadPosts: function(category) {
            const $container = $('#gm-cat-posts');
            if (!$container.length) return;

            $container.css('opacity', '0.5');

            $.ajax({
                url: gmPortal.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'gm_load_category_posts',
                    category: category,
                    nonce: gmPortal.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $container.html(response.data).css('opacity', '1');
                    }
                },
                error: function() {
                    $container.css('opacity', '1');
                    console.error('Failed to load posts');
                }
            });
        }
    };

    /**
     * Newsletter Form
     */
    const NewsletterForm = {
        init: function() {
            $('#gm-newsletter-form, .gm-newsletter-form').on('submit', function(e) {
                e.preventDefault();
                NewsletterForm.submit($(this));
            });
        },

        submit: function($form) {
            const $button = $form.find('button[type="submit"]');
            const $input = $form.find('input[type="email"]');
            const originalText = $button.text();

            $button.prop('disabled', true).text(gmPortal.strings.loading);

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $input.val('');
                        $button.text('Uspešno!').addClass('success');
                        setTimeout(function() {
                            $button.text(originalText).removeClass('success').prop('disabled', false);
                        }, 3000);
                    } else {
                        $button.text('Greška').addClass('error');
                        setTimeout(function() {
                            $button.text(originalText).removeClass('error').prop('disabled', false);
                        }, 3000);
                    }
                },
                error: function() {
                    $button.text('Greška').addClass('error');
                    setTimeout(function() {
                        $button.text(originalText).removeClass('error').prop('disabled', false);
                    }, 3000);
                }
            });
        }
    };

    /**
     * Smooth Scroll
     */
    const SmoothScroll = {
        init: function() {
            $('a[href*="#"]:not([href="#"])').on('click', function(e) {
                const target = $(this.hash);
                if (target.length) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 500);
                }
            });
        }
    };

    /**
     * Sticky Header
     */
    const StickyHeader = {
        $header: null,
        lastScroll: 0,

        init: function() {
            this.$header = $('.gm-header');
            if (!this.$header.length) return;

            $(window).on('scroll', this.handleScroll.bind(this));
        },

        handleScroll: function() {
            const currentScroll = $(window).scrollTop();

            if (currentScroll > 200) {
                this.$header.addClass('scrolled');

                if (currentScroll > this.lastScroll) {
                    // Scrolling down
                    this.$header.addClass('hidden');
                } else {
                    // Scrolling up
                    this.$header.removeClass('hidden');
                }
            } else {
                this.$header.removeClass('scrolled hidden');
            }

            this.lastScroll = currentScroll;
        }
    };

    /**
     * Lazy Loading Images
     */
    const LazyLoad = {
        init: function() {
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                                observer.unobserve(img);
                            }
                        }
                    });
                }, {
                    rootMargin: '50px'
                });

                document.querySelectorAll('img[data-src]').forEach(function(img) {
                    observer.observe(img);
                });
            } else {
                // Fallback for older browsers
                document.querySelectorAll('img[data-src]').forEach(function(img) {
                    img.src = img.dataset.src;
                });
            }
        }
    };

    /**
     * Back to Top Button
     */
    const BackToTop = {
        init: function() {
            const $button = $('<button>', {
                'class': 'gm-back-to-top',
                'aria-label': 'Nazad na vrh',
                'html': '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>'
            }).appendTo('body');

            $(window).on('scroll', function() {
                if ($(this).scrollTop() > 500) {
                    $button.addClass('visible');
                } else {
                    $button.removeClass('visible');
                }
            });

            $button.on('click', function() {
                $('html, body').animate({ scrollTop: 0 }, 500);
            });

            // Add styles dynamically
            $('<style>')
                .text(`
                    .gm-back-to-top {
                        position: fixed;
                        bottom: 2rem;
                        right: 2rem;
                        width: 50px;
                        height: 50px;
                        background-color: var(--gm-primary);
                        color: white;
                        border: none;
                        border-radius: 50%;
                        cursor: pointer;
                        opacity: 0;
                        visibility: hidden;
                        transform: translateY(20px);
                        transition: all 0.3s ease;
                        z-index: 999;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: var(--gm-shadow-lg);
                    }
                    .gm-back-to-top:hover {
                        background-color: var(--gm-primary-dark);
                        transform: translateY(-2px);
                    }
                    .gm-back-to-top.visible {
                        opacity: 1;
                        visibility: visible;
                        transform: translateY(0);
                    }
                `)
                .appendTo('head');
        }
    };

    /**
     * Initialize all modules
     */
    $(document).ready(function() {
        HeroSlider.init();
        MobileMenu.init();
        CategoryTabs.init();
        NewsletterForm.init();
        SmoothScroll.init();
        StickyHeader.init();
        LazyLoad.init();
        BackToTop.init();
    });

})(jQuery);
