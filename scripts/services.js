$(document).ready(function() {
    console.log('Sphinx Fire Services page loaded! 🔥');
    
    // ===== Smooth Scrolling =====
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });
    
    // ===== Sticky Header Effect =====
    $(window).scroll(function() {
        if ($(window).scrollTop() > 50) {
            $('.sticky-header').addClass('scrolled');
        } else {
            $('.sticky-header').removeClass('scrolled');
        }
        
        // Show/hide back to top button
        if ($(window).scrollTop() > 500) {
            $('#back-to-top').addClass('opacity-100 pointer-events-auto').removeClass('opacity-0 pointer-events-none');
        } else {
            $('#back-to-top').addClass('opacity-0 pointer-events-none').removeClass('opacity-100 pointer-events-auto');
        }
        
        // Show floating popup at 70% scroll
        var scrollPercent = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
        if (scrollPercent >= 70 && !sessionStorage.getItem('popupShown')) {
            $('.floating-popup').addClass('show');
            sessionStorage.setItem('popupShown', 'true');
        }
    });
    
    // ===== Hero Carousel =====
    let currentSlide = 0;
    const slides = $('.hero-slide');
    const dots = $('.carousel-dot');
    const totalSlides = slides.length;
    
    function showSlide(index) {
        slides.removeClass('active');
        dots.removeClass('active');
        
        slides.eq(index).addClass('active');
        dots.eq(index).addClass('active');
        
        currentSlide = index;
    }
    
    function nextSlide() {
        const next = (currentSlide + 1) % totalSlides;
        showSlide(next);
    }
    // ===== Accordion Functionality =====
    $('.accordion-header').click(function() {
        const content = $(this).next('.accordion-content');
        const arrow = $(this).find('.accordion-arrow');
        const isActive = content.hasClass('active');
        
        // Close all accordions
        $('.accordion-content').removeClass('active');
        $('.accordion-arrow').removeClass('rotate');
        
        // Open clicked accordion if it wasn't active
        if (!isActive) {
            content.addClass('active');
            arrow.addClass('rotate');
        }
    });
    
    // ===== FAQ Functionality =====
    $('.faq-header').click(function() {
        const content = $(this).next('.faq-content');
        const arrow = $(this).find('.accordion-arrow');
        const isActive = content.hasClass('active');
        
        // Close all FAQs
        $('.faq-content').removeClass('active');
        $('.faq-header .accordion-arrow').removeClass('rotate');
        
        // Open clicked FAQ if it wasn't active
        if (!isActive) {
            content.addClass('active');
            arrow.addClass('rotate');
        }
    });
    
    function animateOnScroll() {
        $('.service-card').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animate-slide-up');
            }
        });
    }
    
    $(window).scroll(animateOnScroll);
    animateOnScroll(); // Initial check
    
    // ===== CTA Button Actions =====
    $('#header-cta, #site-visit-btn').click(function() {
        // Scroll to conversion section or show contact form
        $('html, body').animate({
            scrollTop: $('.py-20.bg-brand-black').offset().top - 80
        }, 1000);
    });
    
    $('#download-catalog-btn').click(function() {
        alert('Catalog download started! Check your downloads folder.');
        // In real implementation, trigger actual PDF download
    });
    
    $('#compare-systems-btn').click(function() {
        $('html, body').animate({
            scrollTop: $('#services').offset().top - 80
        }, 1000);
    });
    
    $('#integration-cta').click(function() {
        alert('Integration guide will be displayed here');
        // In real implementation, show integration details modal or page
    });
    
    $('#custom-offer-btn').click(function() {
        // Add shake animation
        $(this).addClass('cta-shake');
        setTimeout(() => {
            $(this).removeClass('cta-shake');
        }, 500);
        
        // Show contact form or redirect
        alert('Custom offer form will open here');
    });
    
    // ===== Service Card CTAs =====
    $('.service-card button').click(function() {
        const serviceTitle = $(this).closest('.service-card').find('h3').text();
        alert(`${serviceTitle} request form will open here`);
        // In real implementation, show service-specific form
    });
    
    // ===== Back to Top Button =====
    $('#back-to-top').click(function() {
        $('html, body').animate({
            scrollTop: 0
        }, 800);
    });
    
    // ===== Mobile Menu Toggle =====
    $('#mobile-menu-btn').click(function() {
        alert('Mobile menu would open here');
        // In real implementation, show mobile navigation
    });
    
    // ===== CTA Shake Animation Timer =====
    setTimeout(function() {
        $('.cta-main').addClass('cta-shake');
        setTimeout(() => {
            $('.cta-main').removeClass('cta-shake');
        }, 500);
    }, 10000); // Shake after 10 seconds
    
    // ===== Parallax Effect for Hero =====
    $(window).scroll(function() {
        var scrolled = $(window).scrollTop();
        var parallax = scrolled * 0.2;
        $('.hero-slide.active').css('transform', 'translateY(' + parallax + 'px)');
    });
    
    // ===== Analytics Events =====
    function trackEvent(eventName, data) {
        console.log('Analytics Event:', eventName, data);
        // In real implementation, send to analytics platform
    }
    
    // Track button clicks
    $('button').click(function() {
        const buttonText = $(this).text().trim();
        const buttonId = $(this).attr('id') || 'unnamed-button';
        trackEvent('button_click', {
            button_id: buttonId,
            button_text: buttonText,
            page: 'services'
        });
    });
    
    // Track scroll depth
    let maxScroll = 0;
    $(window).scroll(function() {
        const scrollPercent = Math.round(($(window).scrollTop() / ($(document).height() - $(window).height())) * 100);
        if (scrollPercent > maxScroll) {
            maxScroll = scrollPercent;
            if (maxScroll % 25 === 0) {
                trackEvent('scroll_depth', { 
                    percent: maxScroll,
                    page: 'services'
                });
            }
        }
    });
    
    // Track accordion/FAQ interactions
    $('.accordion-header, .faq-header').click(function() {
        const title = $(this).find('span').text();
        trackEvent('accordion_click', {
            title: title,
            page: 'services'
        });
    });
    
    console.log('All services page features initialized! 🚀');
});

// ===== Global Functions =====
function closeFloatingPopup() {
    $('.floating-popup').removeClass('show');
}

// ===== Keyboard Navigation =====
$(document).keydown(function(e) {
    // Close floating popup with Escape key
    if (e.key === 'Escape') {
        closeFloatingPopup();
    }
    
    // Carousel navigation with arrow keys
    if (e.key === 'ArrowLeft') {
        $('.carousel-prev').click();
    } else if (e.key === 'ArrowRight') {
        $('.carousel-next').click();
    }
});

// ===== Page Load Animation =====
$(window).on('load', function() {
    $('body').addClass('loaded');
    console.log('Services page fully loaded! 🎉');
});