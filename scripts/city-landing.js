// ===== Analytics Events =====
function trackEvent(eventName, data) {
    console.log('Analytics Event:', eventName, data);
    // In real implementation, send to analytics platform
}

$(document).ready(function() {
    console.log('Sphinx Fire City Landing page loaded! 🔥');
    
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
        var parallax = scrolled * 0.2;
        $('#hero').css('transform', 'translateY(' + parallax + 'px)');
    });
    
    // ===== Animate on Scroll =====
    function animateOnScroll() {
        $('.advantage-card').each(function(index) {
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
        
        $('.service-card').each(function(index) {
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
    
    // ===== Stats Counter Animation =====
    function animateCounters() {
        $('.stats-counter').each(function() {
            const $this = $(this);
            const countTo = $this.text();
            const isPercentage = countTo.includes('%');
            const finalNumber = parseInt(countTo);
            
            if (!isNaN(finalNumber)) {
                $({ countNum: 0 }).animate({
                    countNum: finalNumber
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum) + (isPercentage ? '%' : ''));
                    },
                    complete: function() {
                        $this.text(countTo);
                    }
                });
            }
        });
    }
    
    // Trigger counter animation when testimonial section is visible
    $(window).scroll(function() {
        const testimonialTop = $('#local-project').offset().top;
        const testimonialBottom = testimonialTop + $('#local-project').outerHeight();
        const viewportTop = $(window).scrollTop();
        const viewportBottom = viewportTop + $(window).height();
        
        if (testimonialBottom > viewportTop && testimonialTop < viewportBottom && !$('#local-project').hasClass('animated')) {
            $('#local-project').addClass('animated');
            animateCounters();
        }
    });
    
    // ===== CTA Button Actions =====
    $('#header-cta, #book-assessment-btn, #book-local-visit-btn').click(function() {
        // Redirect to contact page with city context
        const cityName = 'Sadat City';
        window.location.href = 'contact.html?city=' + encodeURIComponent(cityName) + '&service=site_assessment';
        
        trackEvent('local_assessment_request', {
            city: cityName,
            source: $(this).attr('id'),
            page: 'city_landing'
        });
    });
    
    $('#compliance-guide-btn').click(function() {
        // Redirect to blog article about civil defense
        window.location.href = 'blog-article-civil-defense.html';
        
        trackEvent('compliance_guide_view', {
            city: 'Sadat City',
            source: 'hero_cta',
            page: 'city_landing'
        });
    });
    
    $('#talk-consultant-btn').click(function() {
        // Show consultant contact options
        const cityName = 'Sadat City';
        window.location.href = 'contact.html?city=' + encodeURIComponent(cityName) + '&request=consultant';
        
        trackEvent('consultant_request', {
            city: cityName,
            source: 'final_cta',
            page: 'city_landing'
        });
    });
    
    // ===== Service Card CTAs =====
    $('.service-card button').click(function() {
        const serviceTitle = $(this).closest('.service-card').find('h3').text();
        const cityName = 'Sadat City';
        
        // In real implementation, show service-specific form or redirect
        alert(`${serviceTitle} request for ${cityName} will be processed here`);
        
        trackEvent('service_request', {
            service: serviceTitle,
            city: cityName,
            page: 'city_landing'
        });
    });
    
    // ===== CTA Pulse Animation Timer =====
    setTimeout(function() {
        $('#book-local-visit-btn').addClass('cta-pulse');
    }, 6000); // Start pulsing after 6 seconds
    
    // ===== Mobile Menu Toggle =====
    $('#mobile-menu-btn').click(function() {
        alert('Mobile menu would open here');
        // In real implementation, show mobile navigation
    });
    
    // Track button clicks
    $('button, a[href*="html"]').click(function() {
        const elementText = $(this).text().trim();
        const elementId = $(this).attr('id') || 'unnamed-element';
        trackEvent('element_click', {
            element_id: elementId,
            element_text: elementText,
            city: 'Sadat City',
            page: 'city_landing'
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
                    city: 'Sadat City',
                    page: 'city_landing'
                });
            }
        }
    });
    
    // Track time spent on page
    const startTime = Date.now();
    $(window).on('beforeunload', function() {
        const timeSpent = Math.round((Date.now() - startTime) / 1000);
        trackEvent('time_on_page', {
            seconds: timeSpent,
            city: 'Sadat City',
            page: 'city_landing'
        });
    });
    
    // Track local advantage card interactions
    $('.advantage-card').on('mouseenter', function() {
        const advantageTitle = $(this).find('h3').text();
        trackEvent('advantage_hover', {
            advantage: advantageTitle,
            city: 'Sadat City',
            page: 'city_landing'
        });
    });
    
    // Track service interest
    $('.service-card').on('mouseenter', function() {
        const serviceTitle = $(this).find('h3').text();
        trackEvent('service_interest', {
            service: serviceTitle,
            city: 'Sadat City',
            page: 'city_landing'
        });
    });
    
    console.log('All city landing page features initialized! 🚀');
});

// ===== Global Functions =====
function openGoogleMaps() {
    const address = encodeURIComponent('Sadat Industrial City, Block 15, Monufia, Egypt');
    window.open(`https://www.google.com/maps/search/${address}`, '_blank');
    
    trackEvent('maps_click', {
        city: 'Sadat City',
        page: 'city_landing'
    });
}

// ===== Keyboard Navigation =====
$(document).keydown(function(e) {
    // Quick contact shortcuts
    if (e.ctrlKey && e.key === 'c') {
        e.preventDefault();
        window.location.href = 'contact.html?city=Sadat City';
    }
    
    // Quick service request
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#services').offset().top - 80
        }, 1000);
    }
    
    // View local project
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#local-project').offset().top - 80
        }, 1000);
    }
});

// ===== Page Load Animation =====
$(window).on('load', function() {
    $('body').addClass('loaded');
    console.log('City Landing page fully loaded! 🎉');
    
    // Show keyboard shortcuts tip
    setTimeout(() => {
        console.log('💡 Pro tip: Use Ctrl+C for contact, Ctrl+S for services, Ctrl+P for local projects!');
    }, 3000);
});

// ===== Local SEO Enhancements =====
function enhanceLocalSEO() {
    // Add city-specific meta information
    const cityName = 'Sadat City';
    const metaDescription = document.querySelector('meta[name="description"]');
    
    if (metaDescription) {
        console.log(`Local SEO optimized for ${cityName}`);
    }
    
    // Track local search intent
    trackEvent('local_seo_view', {
        city: cityName,
        page: 'city_landing',
        user_agent: navigator.userAgent
    });
}

// Initialize local SEO enhancements
$(document).ready(function() {
    enhanceLocalSEO();
});

// ===== Emergency Contact Handler =====
function handleEmergencyContact() {
    const cityName = 'Sadat City';
    const emergencyMessage = `🚨 EMERGENCY FIRE SAFETY REQUEST from ${cityName}`;
    
    if (confirm(`${emergencyMessage}\n\nThis will immediately contact our local emergency response team. Continue?`)) {
        window.location.href = 'tel:+201234567890';
        
        trackEvent('emergency_contact', {
            city: cityName,
            page: 'city_landing'
        });
    }
}

// ===== Local Weather Integration (Optional Enhancement) =====
function checkLocalWeather() {
    // In real implementation, integrate with weather API
    // to show weather conditions that might affect fire safety
    console.log('Local weather check for Sadat City would be implemented here');
}

// ===== Competitor Analysis Tracking =====
function trackCompetitorInterest() {
    // Track if users are comparing with other providers
    const referrer = document.referrer;
    
    if (referrer.includes('google') || referrer.includes('search')) {
        trackEvent('search_arrival', {
            city: 'Sadat City',
            referrer: referrer,
            page: 'city_landing'
        });
    }
}

$(document).ready(function() {
    trackCompetitorInterest();
});

// ===== Local Business Hours Display =====
function displayBusinessHours() {
    const now = new Date();
    const currentHour = now.getHours();
    const currentDay = now.getDay(); // 0 = Sunday, 1 = Monday, etc.
    
    // Business hours: Saturday-Thursday 9AM-6PM (Egyptian work week)
    const isBusinessDay = currentDay >= 6 || currentDay <= 4; // Sat-Thu
    const isBusinessHour = currentHour >= 9 && currentHour < 18;
    
    if (isBusinessDay && isBusinessHour) {
        console.log('🟢 Currently open for Sadat City services');
    } else {
        console.log('🔴 Currently closed - Emergency services available');
    }
}

$(document).ready(function() {
    displayBusinessHours();
});

// ===== Local Testimonial Rotation =====
let testimonialIndex = 0;
const localTestimonials = [
    {
        quote: "Sphinx Fire installed our complete firefighting network in just 7 days. Being local made all the difference.",
        author: "Mohamed El-Shamy",
        company: "Sadat Plastics Industries"
    },
    {
        quote: "Fast response when our alarm system needed repair. They were here in 15 minutes.",
        author: "Fatma Hassan",
        company: "Sadat Textiles Factory"
    },
    {
        quote: "Professional civil defense preparation. We passed inspection on the first try.",
        author: "Ahmed Mahmoud",
        company: "Sadat Chemical Industries"
    }
];

function rotateTestimonials() {
    // In real implementation, rotate through local testimonials
    console.log('Rotating local testimonials:', localTestimonials[testimonialIndex]);
    testimonialIndex = (testimonialIndex + 1) % localTestimonials.length;
}

// Rotate testimonials every 10 seconds
setInterval(rotateTestimonials, 10000);

// ===== Local Event Tracking =====
function trackLocalEvents() {
    // Track local industrial events, inspections, etc.
    const localEvents = {
        'civil_defense_inspection_season': 'March-April',
        'industrial_safety_week': 'September',
        'fire_prevention_month': 'October'
    };
    
    console.log('Local events for Sadat City:', localEvents);
}

// ===== Performance Monitoring for Local Page =====
$(window).on('load', function() {
    const loadTime = performance.now();
    trackEvent('page_performance', {
        load_time: Math.round(loadTime),
        city: 'Sadat City',
        page: 'city_landing'
    });
    
    // Track if page loads fast enough for local users
    if (loadTime < 3000) {
        console.log('✅ Fast loading for local users');
    } else {
        console.log('⚠️ Slow loading - optimize for local users');
    }
});