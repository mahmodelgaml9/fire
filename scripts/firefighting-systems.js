$(document).ready(function() {
    console.log('Sphinx Fire - Firefighting Systems page loaded! 🔥');
    
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
    });
    
    // ===== Parallax Effect for Hero =====
    $(window).scroll(function() {
        var scrolled = $(window).scrollTop();
        var parallax = scrolled * 0.3;
        $('#hero').css('transform', 'translateY(' + parallax + 'px)');
    });
    
    // ===== Animate on Scroll =====
    function animateOnScroll() {
        $('.feature-card').each(function(index) {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                setTimeout(() => {
                    $(this).addClass('animate');
                }, index * 100); // Staggered animation
            }
        });
        
        // Comparison table animation
        $('.comparison-table').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animate');
            }
        });
    }
    
    $(window).scroll(animateOnScroll);
    animateOnScroll(); // Initial check
    
    // ===== Gallery Lightbox =====
    $('.gallery-item').click(function() {
        const imageSrc = $(this).data('image');
        const imageAlt = $(this).find('img').attr('alt');
        
        $('#lightbox-img').attr('src', imageSrc).attr('alt', imageAlt);
        $('#lightbox').addClass('active');
    });
    
    // ===== CTA Button Actions =====
    $('#header-cta, #talk-consultant-btn').click(function() {
        // Show contact form or redirect to contact page
        alert('Contact form will open here. In real implementation, show modal or redirect to contact page.');
    });
    
    $('#get-quote-btn').click(function() {
        // Scroll to CTA section or show quote form
        $('html, body').animate({
            scrollTop: $('#cta').offset().top - 80
        }, 1000);
    });
    
    $('#download-pdf-btn, #download-checklist-btn').click(function() {
        alert('PDF download started! Check your downloads folder.');
        // In real implementation, trigger actual PDF download
    });
    
    // ===== CTA Pulse Animation Timer =====
    setTimeout(function() {
        $('#talk-consultant-btn').addClass('cta-pulse');
    }, 5000); // Start pulsing after 5 seconds
    
    // ===== Mobile Menu Toggle =====
    $('#mobile-menu-btn').click(function() {
        alert('Mobile menu would open here');
        // In real implementation, show mobile navigation
    });
    
    // ===== Analytics Events =====
    function trackEvent(eventName, data) {
        console.log('Analytics Event:', eventName, data);
        // In real implementation, send to analytics platform
    }
    
    // Track button clicks
    $('button, a[href*="html"]').click(function() {
        const elementText = $(this).text().trim();
        const elementId = $(this).attr('id') || 'unnamed-element';
        trackEvent('element_click', {
            element_id: elementId,
            element_text: elementText,
            page: 'firefighting-systems'
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
                    page: 'firefighting-systems'
                });
            }
        }
    });
    
    // Track gallery interactions
    $('.gallery-item').click(function() {
        const imageAlt = $(this).find('img').attr('alt');
        trackEvent('gallery_view', {
            image: imageAlt,
            page: 'firefighting-systems'
        });
    });
    
    console.log('All firefighting systems page features initialized! 🚀');
});

// ===== Global Functions =====
function closeLightbox() {
    $('#lightbox').removeClass('active');
}

// ===== Keyboard Navigation =====
$(document).keydown(function(e) {
    // Close lightbox with Escape key
    if (e.key === 'Escape') {
        closeLightbox();
    }
});

// ===== Page Load Animation =====
$(window).on('load', function() {
    $('body').addClass('loaded');
    console.log('Firefighting Systems page fully loaded! 🎉');
});