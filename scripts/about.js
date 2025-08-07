$(document).ready(function() {
    console.log('Sphinx Fire About page loaded! 🔥');
    
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
        $('.value-card').each(function(index) {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                setTimeout(() => {
                    $(this).addClass('animate');
                }, index * 150); // Staggered animation
            }
        });
        
        // Process steps animation
        $('.process-step').each(function(index) {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                setTimeout(() => {
                    $(this).addClass('animate');
                }, index * 200); // Staggered animation
            }
        });
        
        // Team cards animation
        $('.team-card').each(function(index) {
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
    }
    
    $(window).scroll(animateOnScroll);
    animateOnScroll(); // Initial check
    
    // ===== CTA Button Actions =====
    $('#header-cta, #request-consultation-btn').click(function() {
        // Show contact form or redirect to contact page
        alert('Contact form will open here. In real implementation, show modal or redirect to contact page.');
    });
    
    $('#explore-services-btn').click(function() {
        // Redirect to services page
        window.location.href = 'services.html';
    });
    
    $('#download-profile-btn').click(function() {
        alert('Company Profile PDF download started! Check your downloads folder.');
        // In real implementation, trigger actual PDF download
    });
    
    // ===== CTA Pulse Animation Timer =====
    setTimeout(function() {
        $('#request-consultation-btn').addClass('cta-pulse');
    }, 5000); // Start pulsing after 5 seconds
    
    // ===== Mobile Menu Toggle =====
    $('#mobile-menu-btn').click(function() {
        alert('Mobile menu would open here');
        // In real implementation, show mobile navigation
    });
    
    // ===== Partner Logo Hover Effects =====
    $('.partner-logo').hover(
        function() {
            $(this).addClass('scale-110 shadow-lg');
        },
        function() {
            $(this).removeClass('scale-110 shadow-lg');
        }
    );
    
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
            page: 'about'
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
                    page: 'about'
                });
            }
        }
    });
    
    // Track section views
    const sections = ['#hero', '#overview', '#values', '#advantages', '#process', '#team', '#partnerships', '#cta'];
    sections.forEach(function(section) {
        $(window).scroll(function() {
            const sectionTop = $(section).offset().top;
            const sectionBottom = sectionTop + $(section).outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            if (sectionBottom > viewportTop && sectionTop < viewportBottom) {
                trackEvent('section_view', {
                    section: section.replace('#', ''),
                    page: 'about'
                });
            }
        });
    });
    
    console.log('All about page features initialized! 🚀');
});

// ===== Keyboard Navigation =====
$(document).keydown(function(e) {
    // Scroll to top with Home key
    if (e.key === 'Home') {
        $('html, body').animate({ scrollTop: 0 }, 800);
    }
    
    // Scroll to bottom with End key
    if (e.key === 'End') {
        $('html, body').animate({ scrollTop: $(document).height() }, 800);
    }
});

// ===== Page Load Animation =====
$(window).on('load', function() {
    $('body').addClass('loaded');
    console.log('About page fully loaded! 🎉');
});