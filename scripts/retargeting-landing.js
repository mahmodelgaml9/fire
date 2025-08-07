// ===== Retargeting Landing Page Script =====
$(document).ready(function() {
    console.log('Sphinx Fire Retargeting Landing page loaded! 🔥');
    
    // ===== UTM Parameter Handling =====
    function getUTMParameters() {
        const params = new URLSearchParams(window.location.search);
        const utmParams = {};
        
        ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(param => {
            if (params.has(param)) {
                utmParams[param] = params.get(param);
                // Update meta tags
                const metaTag = document.querySelector(`meta[name="${param}"]`);
                if (metaTag) {
                    metaTag.setAttribute('content', params.get(param));
                }
            }
        });
        
        // Store UTM parameters in session storage
        if (Object.keys(utmParams).length > 0) {
            sessionStorage.setItem('utm_params', JSON.stringify(utmParams));
        }
        
        return utmParams;
    }
    
    // Initialize UTM tracking
    const utmParams = getUTMParameters();
    console.log('UTM Parameters:', utmParams);
    
    // ===== Countdown Timer =====
    function updateCountdown() {
        // Set the date we're counting down to (3 days from now)
        const countDownDate = new Date();
        countDownDate.setDate(countDownDate.getDate() + 3);
        
        // Update the countdown every 1 second
        const x = setInterval(function() {
            // Get current date and time
            const now = new Date().getTime();
            
            // Find the distance between now and the countdown date
            const distance = countDownDate - now;
            
            // Time calculations for days, hours, minutes and seconds
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Display the result
            document.getElementById("days").innerHTML = days.toString().padStart(2, '0');
            document.getElementById("hours").innerHTML = hours.toString().padStart(2, '0');
            document.getElementById("minutes").innerHTML = minutes.toString().padStart(2, '0');
            document.getElementById("seconds").innerHTML = seconds.toString().padStart(2, '0');
            
            // Update sticky countdown
            document.querySelector(".sticky-days").innerHTML = days.toString().padStart(2, '0');
            document.querySelector(".sticky-hours").innerHTML = hours.toString().padStart(2, '0');
            document.querySelector(".sticky-minutes").innerHTML = minutes.toString().padStart(2, '0');
            
            // If the countdown is finished, display expired message
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("days").innerHTML = "00";
                document.getElementById("hours").innerHTML = "00";
                document.getElementById("minutes").innerHTML = "00";
                document.getElementById("seconds").innerHTML = "00";
                
                // Show expired message
                $('.limited-spots').text('OFFER EXPIRED').removeClass('pulse');
                $('button[type="submit"]').text('Request Regular Assessment').prop('disabled', false);
            }
        }, 1000);
    }
    
    updateCountdown();
    
    // ===== Smooth Scrolling =====
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if (target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
            
            // Track scroll to form
            if (this.getAttribute('href') === '#offer-form') {
                trackConversion('InitiateCheckout', {
                    content_name: '20% Assessment Discount',
                    content_category: 'Special Offer'
                });
            }
        }
    });
    
    // ===== Sticky CTA =====
    $(window).scroll(function() {
        if ($(window).scrollTop() > 300) {
            $('.sticky-cta').addClass('show');
        } else {
            $('.sticky-cta').removeClass('show');
        }
    });
    
    // ===== FAQ Accordion =====
    $('.faq-header').click(function() {
        $(this).next('.faq-content').toggleClass('hidden');
        $(this).find('.accordion-arrow').toggleClass('transform rotate-180');
        
        // Track FAQ interaction
        const faqTitle = $(this).find('span').text();
        trackConversion('ViewContent', {
            content_name: faqTitle,
            content_category: 'FAQ'
        });
    });
    
    // ===== Form Validation =====
    function validateForm() {
        let isValid = true;
        
        // Validate name
        if ($('#name').val().trim() === '') {
            $('#name').addClass('border-red-500');
            isValid = false;
        } else {
            $('#name').removeClass('border-red-500');
        }
        
        // Validate company
        if ($('#company').val().trim() === '') {
            $('#company').addClass('border-red-500');
            isValid = false;
        } else {
            $('#company').removeClass('border-red-500');
        }
        
        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test($('#email').val())) {
            $('#email').addClass('border-red-500');
            isValid = false;
        } else {
            $('#email').removeClass('border-red-500');
        }
        
        // Validate phone
        const phoneRegex = /^\+20\d{10}$/;
        if (!phoneRegex.test($('#phone').val().replace(/\s/g, ''))) {
            $('#phone').addClass('border-red-500');
            isValid = false;
        } else {
            $('#phone').removeClass('border-red-500');
        }
        
        // Validate facility type
        if ($('#facility-type').val() === '') {
            $('#facility-type').addClass('border-red-500');
            isValid = false;
        } else {
            $('#facility-type').removeClass('border-red-500');
        }
        
        return isValid;
    }
    
    // ===== Form Submission =====
    $('#conversion-form').submit(function(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            // Track validation error
            trackConversion('FormError', {
                content_name: '20% Assessment Discount',
                content_category: 'Special Offer',
                error_type: 'validation'
            });
            return;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.text('Processing...').prop('disabled', true);
        
        // Collect form data
        const formData = {
            name: $('#name').val(),
            company: $('#company').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            facilityType: $('#facility-type').val(),
            preferredDate: $('#preferred-date').val() || null,
            urgent: $('#urgent').is(':checked'),
            timestamp: new Date().toISOString(),
            campaign: 'retargeting-20-off',
            ...utmParams
        };
        
        console.log('Form submitted:', formData);
        
        // Track lead conversion
        trackConversion('Lead', {
            content_name: '20% Assessment Discount',
            content_category: 'Special Offer',
            value: 12000,
            currency: 'EGP',
            status: 'submitted'
        });
        
        // Simulate form submission delay
        setTimeout(() => {
            // Show success message
            $('#success-message').show();
            $('#conversion-form').hide();
            
            // Scroll to success message
            $('html, body').animate({
                scrollTop: $('#success-message').offset().top - 100
            }, 500);
            
            // Track successful submission
            trackConversion('Lead', {
                content_name: '20% Assessment Discount',
                content_category: 'Special Offer',
                value: 12000,
                currency: 'EGP',
                status: 'success'
            });
            
            // In real implementation, send data to server
            // $.ajax({
            //     url: '/api/campaign-leads',
            //     method: 'POST',
            //     data: formData,
            //     success: function(response) {
            //         // Handle success
            //     },
            //     error: function(xhr, status, error) {
            //         // Handle error
            //     }
            // });
            
        }, 1500);
    });
    
    // ===== Exit Intent Modal =====
    let showExitModal = true;
    let exitIntentShown = false;
    
    function handleExitIntent(e) {
        // Only show exit intent if:
        // 1. We haven't shown it before
        // 2. Mouse is leaving from the top of the page
        // 3. Form hasn't been submitted yet
        if (!exitIntentShown && e.clientY < 50 && $('#conversion-form').is(':visible')) {
            $('#exit-modal').addClass('show');
            exitIntentShown = true;
            
            // Track exit intent
            trackConversion('ExitIntent', {
                content_name: '20% Assessment Discount',
                content_category: 'Special Offer'
            });
        }
    }
    
    // Only add exit intent for desktop
    if (window.innerWidth > 768) {
        document.addEventListener('mouseleave', handleExitIntent);
    }
    
    // Close modal
    $('#close-modal, #no-thanks').click(function() {
        $('#exit-modal').removeClass('show');
        
        // Track modal close
        trackConversion('CloseModal', {
            content_name: '20% Assessment Discount',
            content_category: 'Special Offer',
            action: $(this).attr('id') === 'no-thanks' ? 'decline' : 'close'
        });
    });
    
    // Modal CTA click
    $('#exit-modal a[href="#offer-form"]').click(function() {
        $('#exit-modal').removeClass('show');
        
        // Track modal CTA click
        trackConversion('ClickCTA', {
            content_name: '20% Assessment Discount',
            content_category: 'Special Offer',
            source: 'exit_modal'
        });
    });
    
    // ===== Phone number formatting =====
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
    
    // ===== Form Field Focus Tracking =====
    $('.form-field').on('focus', function() {
        const fieldName = $(this).attr('name');
        trackConversion('FormInteraction', {
            content_name: '20% Assessment Discount',
            content_category: 'Special Offer',
            field_name: fieldName,
            action: 'focus'
        });
    });
    
    // ===== Scroll Depth Tracking =====
    let scrollDepthTracked = {
        25: false,
        50: false,
        75: false,
        90: false
    };
    
    $(window).scroll(function() {
        const scrollPercent = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
        
        Object.keys(scrollDepthTracked).forEach(depth => {
            if (scrollPercent >= depth && !scrollDepthTracked[depth]) {
                scrollDepthTracked[depth] = true;
                
                trackConversion('ScrollDepth', {
                    content_name: '20% Assessment Discount',
                    content_category: 'Special Offer',
                    depth: depth + '%'
                });
            }
        });
    });
    
    // ===== Time on Page Tracking =====
    let timeOnPage = 0;
    const timeInterval = setInterval(function() {
        timeOnPage += 30;
        
        // Track time on page every 2 minutes
        if (timeOnPage % 120 === 0) {
            trackConversion('TimeOnPage', {
                content_name: '20% Assessment Discount',
                content_category: 'Special Offer',
                seconds: timeOnPage
            });
        }
    }, 30000); // Check every 30 seconds
    
    // ===== WhatsApp Click Tracking =====
    $('.whatsapp-float a').click(function() {
        trackConversion('Contact', {
            content_name: '20% Assessment Discount',
            content_category: 'Special Offer',
            method: 'whatsapp'
        });
    });
    
    // ===== Feature Card Interaction =====
    $('.feature-card').hover(
        function() {
            const featureTitle = $(this).find('h3').text();
            trackConversion('FeatureInterest', {
                content_name: featureTitle,
                content_category: 'Feature'
            });
        }
    );
    
    // ===== Testimonial Interaction =====
    $('.testimonial-card').hover(
        function() {
            const testimonialAuthor = $(this).find('.font-semibold').text();
            trackConversion('TestimonialInterest', {
                content_name: testimonialAuthor,
                content_category: 'Testimonial'
            });
        }
    );
    
    // ===== Track page load time =====
    $(window).on('load', function() {
        const loadTime = performance.now();
        trackConversion('PagePerformance', {
            content_name: '20% Assessment Discount',
            content_category: 'Special Offer',
            load_time_ms: Math.round(loadTime)
        });
    });
    
    // ===== Track conversion events =====
    function trackConversion(eventName, data = {}) {
        // Add UTM parameters to event data
        const utmParams = JSON.parse(sessionStorage.getItem('utm_params') || '{}');
        const eventData = { ...data, ...utmParams };
        
        // Facebook Pixel
        if (typeof fbq === 'function') {
            fbq('track', eventName, eventData);
        }
        
        // Google Analytics
        if (typeof gtag === 'function') {
            gtag('event', eventName, eventData);
        }
        
        console.log('Conversion tracked:', eventName, eventData);
        
        // In real implementation, also send to server
        // $.ajax({
        //     url: '/api/track-event',
        //     method: 'POST',
        //     data: {
        //         event_name: eventName,
        //         event_data: eventData
        //     }
        // });
    }
    
    // ===== Initialize page =====
    // Track page view
    trackConversion('ViewContent', {
        content_name: '20% Assessment Discount',
        content_category: 'Special Offer'
    });
    
    // Show sticky CTA after 2 seconds
    setTimeout(function() {
        $('.sticky-cta').addClass('show');
    }, 2000);
    
    console.log('Retargeting landing page initialized! 🚀');
});