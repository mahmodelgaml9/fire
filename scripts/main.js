$(document).ready(function() {
    console.log('Sphinx Fire website loaded successfully! 🔥');
    
    // Initialize performance optimizations
    if (window.lazyLoader) {
        window.lazyLoader.init();
    }
    
    // ===== Smooth Scrolling =====
    $('a[href^="#"]').on('click', window.PerformanceUtils?.debounce(function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    }, 100) || function(event) { /* fallback */ });
    
    // ===== Sticky Header Effect =====
    $(window).scroll(window.PerformanceUtils?.debounce(function() {
        if ($(window).scrollTop() > 50) {
            $('.sticky-header').addClass('scrolled');
        } else {
            $('.sticky-header').removeClass('scrolled');
        }
        
        // Show/hide sticky CTA button
        if ($(window).scrollTop() > 500) {
            $('.sticky-cta').addClass('show');
        } else {
            $('.sticky-cta').removeClass('show');
        }
    }, 16) || function() { /* fallback */ });
    
    // ===== Animate on Scroll =====
    function animateOnScroll() {
        $('.advantage-card, .service-card, .project-card').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animate-slide-up');
            }
        });
        
        // Badge reveal animation
        $('.badge-reveal').each(function(index) {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                setTimeout(() => {
                    $(this).addClass('active');
                }, index * 200);
            }
        });
    }
    
    $(window).scroll(animateOnScroll);
    animateOnScroll(); // Initial check
    
    // ===== Hero Carousel =====
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    const totalSlides = slides.length;
    
    function showSlide(index) {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        if (slides[index]) slides[index].classList.add('active');
        if (dots[index]) dots[index].classList.add('active');
        
        currentSlide = index;
        
        // Load image for current slide if not loaded
        const currentImg = slides[index]?.querySelector('img[data-src]');
        if (currentImg && window.lazyLoader) {
            window.lazyLoader.loadImage(currentImg);
        }
    }
    
    function nextSlide() {
        const next = (currentSlide + 1) % totalSlides;
        showSlide(next);
    }
    
    function prevSlide() {
        const prev = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(prev);
    }
    
    // Carousel navigation
    document.querySelectorAll('.carousel-next').forEach(btn => {
        btn.addEventListener('click', nextSlide);
    });
    
    document.querySelectorAll('.carousel-prev').forEach(btn => {
        btn.addEventListener('click', prevSlide);
    });
    
    // Dot navigation
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
        });
    });
    
    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    const heroSection = document.querySelector('#hero');
    if (heroSection) {
        heroSection.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        heroSection.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
    }
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                nextSlide(); // Swipe left - next slide
            } else {
                prevSlide(); // Swipe right - previous slide
            }
        }
    }
    
    // Preload next slide images
    function preloadNextSlide() {
        const nextIndex = (currentSlide + 1) % totalSlides;
        const nextImg = slides[nextIndex]?.querySelector('img[data-src]');
        if (nextImg && window.lazyLoader) {
            window.lazyLoader.loadImage(nextImg);
        }
    }
    
    // Auto-advance carousel with pause on hover
    let carouselInterval = setInterval(() => {
        nextSlide();
        preloadNextSlide();
    }, 6000);
    
    // Pause on hover
    if (heroSection) {
        heroSection.addEventListener('mouseenter', () => {
            clearInterval(carouselInterval);
        });
        
        heroSection.addEventListener('mouseleave', () => {
            carouselInterval = setInterval(() => {
                nextSlide();
                preloadNextSlide();
            }, 6000);
        });
    }
    
    // Pause when page is not visible
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(carouselInterval);
        } else {
            carouselInterval = setInterval(() => {
                nextSlide();
                preloadNextSlide();
            }, 6000);
        }
    });
    
    // Initialize first slide
    showSlide(0);
    
    // Load modules on demand
    function loadModuleOnDemand(moduleName, trigger) {
        const element = document.querySelector(trigger);
        if (element && window.ModuleLoader) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        window.ModuleLoader.loadModule(moduleName);
                        observer.unobserve(entry.target);
                    }
                });
            });
            observer.observe(element);
        }
    }
    
    // Load modules when needed
    loadModuleOnDemand('lightbox', '.gallery-item');
    loadModuleOnDemand('forms', 'form');
    
    // Optimize third-party scripts loading
    function loadThirdPartyScripts() {
        // Load analytics after user interaction
        let userInteracted = false;
        
        const loadAnalytics = () => {
            if (!userInteracted) {
                userInteracted = true;
                // Load Google Analytics, Facebook Pixel, etc.
                console.log('📊 Loading analytics scripts after user interaction');
            }
        };
        
        ['click', 'scroll', 'keydown', 'touchstart'].forEach(event => {
            document.addEventListener(event, loadAnalytics, { once: true, passive: true });
        });
        
        // Fallback: load after 3 seconds
        setTimeout(loadAnalytics, 3000);
    }
    
    loadThirdPartyScripts();
    
    // Enhanced error handling
    window.addEventListener('error', (e) => {
        console.error('JavaScript Error:', e.error);
        window.trackEvent('javascript_error', {
            message: e.message,
            filename: e.filename,
            lineno: e.lineno,
            colno: e.colno
        });
    });
    
    // Enhanced performance monitoring
    if ('PerformanceObserver' in window) {
        // Monitor long tasks
        const longTaskObserver = new PerformanceObserver((list) => {
            list.getEntries().forEach((entry) => {
                if (entry.duration > 50) {
                    console.warn('⚠️ Long task detected:', entry.duration + 'ms');
                    window.trackEvent('long_task', {
                        duration: entry.duration,
                        startTime: entry.startTime
                    });
                }
            });
        });
        
        longTaskObserver.observe({ entryTypes: ['longtask'] });
    }
    
    console.log('All interactive features initialized! 🚀');
});


// ===== Hero Section CTAs =====
$('#free-visit-btn').click(function() {
    $('html, body').animate({
        scrollTop: $('#contact').offset().top - 80
    }, 1000);
});

$('#download-pdf-btn').click(function() {
    // Trigger PDF download
    alert('Company Profile PDF download started!');
    // In real implementation, trigger actual PDF download
});

// New carousel CTA buttons
$('#systems-quote-btn, #detection-demo-btn').click(function() {
    $('html, body').animate({
        scrollTop: $('#contact').offset().top - 80
    }, 1000);
});

$('#systems-catalog-btn, #detection-specs-btn, #ppe-catalog-btn').click(function() {
    alert('Catalog download started!');
    // In real implementation, trigger actual PDF download
});

$('#ppe-training-btn').click(function() {
    alert('Training programs information will be displayed here');
    // In real implementation, show training details or redirect
});

// ===== WhatsApp Modal (8-second idle) =====
let idleTimer;
let hasShownWhatsAppModal = false;

function resetIdleTimer() {
    clearTimeout(idleTimer);
    if (!hasShownWhatsAppModal) {
        idleTimer = setTimeout(function() {
            $('#whatsapp-modal').removeClass('hidden');
            hasShownWhatsAppModal = true;
        }, 8000);
    }
}

// Track user activity
$(document).on('mousemove keypress scroll', resetIdleTimer);
resetIdleTimer();

// ===== Lead Form Modal (70% scroll) =====
let hasShownLeadModal = false;

$(window).scroll(function() {
    if (!hasShownLeadModal) {
        var scrollPercent = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
        
        if (scrollPercent >= 70) {
            $('#lead-modal').removeClass('hidden');
            hasShownLeadModal = true;
        }
    }
});

// Exit intent detection
$(document).on('mouseleave', function(e) {
    if (e.clientY <= 0 && !hasShownLeadModal) {
        $('#lead-modal').removeClass('hidden');
        hasShownLeadModal = true;
    }
});

// ===== Form Submissions =====
$('#lead-form').submit(function(e) {
    e.preventDefault();
    
    // Collect form data
    var formData = {
        name: $('#name').val(),
        company: $('#company').val(),
        email: $('#email').val(),
        facilityType: $('#facility-type').val()
    };
    
    console.log('Lead form submitted:', formData);
    
    // Show success message
    alert('Thank you! Your consultation request has been submitted. We will contact you within 24 hours.');
    
    // Reset form
    this.reset();
    
    // In real implementation, send data to server
});

$('#modal-lead-form').submit(function(e) {
    e.preventDefault();
    
    console.log('Modal lead form submitted');
    
    // Show success message
    alert('Thank you! Your PDF has been sent to your email. We will contact you soon.');
    
    // Close modal
    $('#lead-modal').addClass('hidden');
    
    // Reset form
    this.reset();
});

// ===== Mobile Menu Toggle =====
$('#mobile-menu-btn').click(function() {
    // Toggle mobile menu (simple implementation)
    alert('Mobile menu would open here');
});

// ===== Service Cards Hover Effect =====
$('.service-card').hover(
    function() {
        $(this).addClass('scale-105 shadow-2xl');
    },
    function() {
        $(this).removeClass('scale-105 shadow-2xl');
    }
);

// ===== Request References Button =====
$('#request-references-btn').click(function() {
    alert('References request form would open here');
});

// ===== Final CTA Button =====
$('#final-cta-btn').click(function() {
    $('html, body').animate({
        scrollTop: $('#contact').offset().top - 80
    }, 1000);
});

// ===== Sticky CTA Button =====
$('.sticky-cta button').click(function() {
    $('html, body').animate({
        scrollTop: $('#contact').offset().top - 80
    }, 1000);
});

// ===== Parallax Effect for Hero Video =====
$(window).scroll(function() {
    var scrolled = $(window).scrollTop();
    var parallax = scrolled * 0.3;
    $('.hero-slide.active .hero-video').css('transform', 'translateY(' + parallax + 'px)');
});

// ===== Testimonials Carousel (simple) =====
let testimonialIndex = 0;
const testimonials = $('.testimonial-carousel .grid > div');

function rotateTestimonials() {
    testimonials.addClass('opacity-0');
    setTimeout(() => {
        testimonials.addClass('hidden');
        testimonials.eq(testimonialIndex).removeClass('hidden opacity-0');
        testimonialIndex = (testimonialIndex + 1) % testimonials.length;
    }, 300);
}

// Auto-rotate testimonials every 5 seconds
setInterval(rotateTestimonials, 5000);

// ===== Performance Optimization =====
// Lazy load images (simple implementation)
$('img[data-src]').each(function() {
    var img = $(this);
    img.attr('src', img.attr('data-src'));
    img.removeAttr('data-src');
});

// ===== Analytics Events (placeholder) =====
function trackEvent(eventName, data) {
    console.log('Analytics Event:', eventName, data);
    // In real implementation, send to analytics platform
}

// Track CTA clicks
$('button[id$="-btn"]').click(function() {
    trackEvent('cta_click', {
        button_id: $(this).attr('id'),
        button_text: $(this).text()
    });
});

// Track form submissions
$('form').submit(function() {
    trackEvent('form_submit', {
        form_id: $(this).attr('id')
    });
});

// Track scroll depth
let maxScroll = 0;
$(window).scroll(function() {
    const scrollPercent = Math.round(($(window).scrollTop() / ($(document).height() - $(window).height())) * 100);
    if (scrollPercent > maxScroll) {
        maxScroll = scrollPercent;
        if (maxScroll % 25 === 0) { // Track every 25%
            trackEvent('scroll_depth', { percent: maxScroll });
        }
    }
});

console.log('All interactive features initialized! 🚀');

// ===== Modal Control Functions =====
function closeWhatsAppModal() {
    $('#whatsapp-modal').addClass('hidden');
}

function closeLeadModal() {
    $('#lead-modal').addClass('hidden');
}

// ===== Keyboard Navigation =====
$(document).keydown(function(e) {
    // Close modals with Escape key
    if (e.key === 'Escape') {
        $('.fixed.inset-0').addClass('hidden');
    }
});

// ===== Smooth Loading Animation =====
$(window).on('load', function() {
    $('body').addClass('loaded');
    console.log('Page fully loaded! 🎉');
});