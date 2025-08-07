$(document).ready(function() {
    console.log('Sphinx Fire Contact page loaded! 🔥');
    
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
        $('.contact-card').each(function(index) {
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
        
        // Form container animation
        $('.form-container').each(function() {
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
    
    // ===== Contact Form Submission =====
    $('#contact-form-main').submit(function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.text('🔄 Sending Request...').prop('disabled', true);
        
        // Collect form data
        const formData = {
            name: $('#name').val(),
            company: $('#company').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            facilityType: $('#facility-type').val(),
            requestType: $('#request-type').val(),
            message: $('#message').val(),
            urgent: $('#urgent').is(':checked'),
            timestamp: new Date().toISOString()
        };
        
        console.log('Contact form submitted:', formData);
        
        // Simulate form submission delay
        setTimeout(() => {
            // Show success message
            $('#success-message').show();
            
            // Reset form
            $('#contact-form-main')[0].reset();
            
            // Reset button
            submitBtn.text(originalText).prop('disabled', false);
            
            // Scroll to success message
            $('html, body').animate({
                scrollTop: $('#success-message').offset().top - 100
            }, 500);
            
            // Track successful submission
            trackEvent('form_submit', {
                form_type: 'contact',
                request_type: formData.requestType,
                urgent: formData.urgent,
                page: 'contact'
            });
            
            // If urgent, show additional message
            if (formData.urgent) {
                setTimeout(() => {
                    alert('⚠️ URGENT REQUEST NOTED: Our emergency response team has been notified and will contact you immediately.');
                }, 1000);
            }
            
        }, 2000); // 2 second delay to simulate processing
        
        // In real implementation, send data to server
        // $.ajax({
        //     url: '/api/contact',
        //     method: 'POST',
        //     data: formData,
        //     success: function(response) {
        //         // Handle success
        //     },
        //     error: function(xhr, status, error) {
        //         // Handle error
        //     }
        // });
    });
    
    // ===== Form Field Enhancements =====
    $('.form-field').on('focus', function() {
        $(this).parent().addClass('field-focused');
    }).on('blur', function() {
        $(this).parent().removeClass('field-focused');
    });
    
    // Phone number formatting
    $('#phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.startsWith('20')) {
            value = '+' + value;
        } else if (value.startsWith('0')) {
            value = '+20' + value.substring(1);
        } else if (!value.startsWith('+20')) {
            value = '+20' + value;
        }
        $(this).val(value);
    });
    
    // ===== CTA Button Actions =====
    $('#header-cta, #request-visit-btn, #book-assessment-btn').click(function() {
        $('html, body').animate({
            scrollTop: $('#contact-form').offset().top - 80
        }, 1000);
    });
    
    $('#download-profile-btn').click(function() {
        alert('Company Profile PDF download started! Check your downloads folder.');
        trackEvent('pdf_download', {
            file: 'company_profile',
            page: 'contact'
        });
        // In real implementation, trigger actual PDF download
    });
    
    // ===== CTA Pulse Animation Timer =====
    setTimeout(function() {
        $('#book-assessment-btn').addClass('cta-pulse');
    }, 8000); // Start pulsing after 8 seconds
    
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
            page: 'contact'
        });
    });
    
    // Track form field interactions
    $('.form-field').on('focus', function() {
        trackEvent('form_field_focus', {
            field_name: $(this).attr('name'),
            field_type: $(this).attr('type') || 'text',
            page: 'contact'
        });
    });
    
    // Track urgent checkbox
    $('#urgent').change(function() {
        trackEvent('urgent_request', {
            checked: $(this).is(':checked'),
            page: 'contact'
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
                    page: 'contact'
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
            page: 'contact'
        });
    });
    
    console.log('All contact page features initialized! 🚀');
});

// ===== Global Contact Functions =====
function makeCall() {
    window.location.href = 'tel:+201234567890';
    trackEvent('phone_call', {
        number: '+201234567890',
        page: 'contact'
    });
}

function openWhatsApp() {
    const message = encodeURIComponent('Hello Sphinx Fire! I need help with fire safety for my facility. Can we schedule a consultation?');
    window.open(`https://wa.me/201234567890?text=${message}`, '_blank');
    trackEvent('whatsapp_click', {
        page: 'contact'
    });
}

function sendEmail() {
    const subject = encodeURIComponent('Fire Safety Consultation Request');
    const body = encodeURIComponent('Hello Sphinx Fire Team,\n\nI would like to request a consultation for fire safety solutions for my facility.\n\nCompany: \nFacility Type: \nLocation: \nSpecific Requirements: \n\nPlease contact me to schedule a site visit.\n\nBest regards,');
    window.location.href = `mailto:info@sphinxfire.com?subject=${subject}&body=${body}`;
    trackEvent('email_click', {
        page: 'contact'
    });
}

function openGoogleMaps() {
    const address = encodeURIComponent('Sadat City Industrial Zone, Block 15, Monufia, Egypt');
    window.open(`https://www.google.com/maps/search/${address}`, '_blank');
    trackEvent('maps_click', {
        page: 'contact'
    });
}

// ===== Emergency Contact Handler =====
function handleEmergencyContact() {
    if (confirm('🚨 EMERGENCY CONTACT\n\nThis will immediately call our emergency response team. Continue?')) {
        makeCall();
        trackEvent('emergency_call', {
            page: 'contact'
        });
    }
}

// ===== Form Validation Helpers =====
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^\+20\d{10}$/;
    return re.test(phone.replace(/\s/g, ''));
}

// ===== Real-time Form Validation =====
$(document).ready(function() {
    $('#email').on('blur', function() {
        const email = $(this).val();
        if (email && !validateEmail(email)) {
            $(this).addClass('border-red-500');
            $(this).after('<p class="text-red-500 text-sm mt-1 validation-error">Please enter a valid email address</p>');
        } else {
            $(this).removeClass('border-red-500');
            $(this).siblings('.validation-error').remove();
        }
    });
    
    $('#phone').on('blur', function() {
        const phone = $(this).val();
        if (phone && !validatePhone(phone)) {
            $(this).addClass('border-red-500');
            $(this).after('<p class="text-red-500 text-sm mt-1 validation-error">Please enter a valid Egyptian phone number</p>');
        } else {
            $(this).removeClass('border-red-500');
            $(this).siblings('.validation-error').remove();
        }
    });
});

// ===== Keyboard Navigation =====
$(document).keydown(function(e) {
    // Quick contact shortcuts
    if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        makeCall();
    }
    
    if (e.ctrlKey && e.key === 'w') {
        e.preventDefault();
        openWhatsApp();
    }
    
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        sendEmail();
    }
});

// ===== Page Load Animation =====
$(window).on('load', function() {
    $('body').addClass('loaded');
    console.log('Contact page fully loaded! 🎉');
    
    // Show welcome message after page load
    setTimeout(() => {
        console.log('💬 Pro tip: Use Ctrl+P to call, Ctrl+W for WhatsApp, Ctrl+E for email!');
    }, 2000);
});