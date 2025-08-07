$(document).ready(function() {
    console.log('Sphinx Fire Single Project page loaded! 🔥');
    
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
        $('.approach-step').each(function(index) {
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
        
        // Results cards animation
        $('.results-card').each(function(index) {
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
    }
    
    $(window).scroll(animateOnScroll);
    animateOnScroll(); // Initial check
    
    // ===== Gallery Lightbox =====
    $('.gallery-item').click(function() {
        const imageSrc = $(this).data('image');
        const imageAlt = $(this).find('img').attr('alt');
        
        $('#lightbox-img').attr('src', imageSrc).attr('alt', imageAlt);
        $('#lightbox').addClass('active');
        
        // Track gallery interaction
        trackEvent('gallery_view', {
            image: imageAlt,
            page: 'project_single'
        });
    });
    
    // ===== CTA Button Actions =====
    $('#header-cta, #request-similar-btn').click(function() {
        // Redirect to contact page with project context
        const projectName = 'Delta Paint Manufacturing';
        const projectType = 'Firefighting System + Foam Suppression';
        
        // In real implementation, pass project details to contact form
        window.location.href = 'contact.html?project=' + encodeURIComponent(projectName) + '&type=' + encodeURIComponent(projectType);
        
        trackEvent('similar_project_request', {
            source_project: projectName,
            page: 'project_single'
        });
    });
    
    $('#view-results-btn').click(function() {
        $('html, body').animate({
            scrollTop: $('#results').offset().top - 80
        }, 1000);
    });
    
    $('#talk-engineer-btn').click(function() {
        // Show engineer contact or redirect to contact page
        window.location.href = 'contact.html?request=engineer_consultation';
        
        trackEvent('engineer_consultation_request', {
            source_project: 'Delta Paint Manufacturing',
            page: 'project_single'
        });
    });
    
    // ===== Related Project CTAs =====
    $('#related-projects button').click(function() {
        const projectTitle = $(this).closest('div').find('h3').text();
        alert(`${projectTitle} details will be displayed here`);
        
        trackEvent('related_project_view', {
            project: projectTitle,
            source_project: 'Delta Paint Manufacturing',
            page: 'project_single'
        });
        // In real implementation, navigate to specific project page
    });
    
    // ===== CTA Pulse Animation Timer =====
    setTimeout(function() {
        $('#request-similar-btn').addClass('cta-pulse');
    }, 6000); // Start pulsing after 6 seconds
    
    // ===== Mobile Menu Toggle =====
    $('#mobile-menu-btn').click(function() {
        alert('Mobile menu would open here');
        // In real implementation, show mobile navigation
    });
    
    // ===== Project Statistics Animation =====
    function animateProjectStats() {
        // Animate the results numbers
        $('.results-card .text-3xl').each(function() {
            const $this = $(this);
            const finalText = $this.text();
            const isPercentage = finalText.includes('%');
            const isDays = finalText.includes('Days');
            const isSCADA = finalText.includes('SCADA');
            
            if (!isPercentage && !isDays && !isSCADA) return;
            
            if (isPercentage) {
                const finalNumber = parseInt(finalText);
                $({ countNum: 0 }).animate({
                    countNum: finalNumber
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum) + '%');
                    },
                    complete: function() {
                        $this.text(finalText);
                    }
                });
            } else if (isDays) {
                const finalNumber = parseInt(finalText);
                $({ countNum: 0 }).animate({
                    countNum: finalNumber
                }, {
                    duration: 1500,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.floor(this.countNum) + ' Days');
                    },
                    complete: function() {
                        $this.text(finalText);
                    }
                });
            }
        });
    }
    
    // Trigger stats animation when results section is visible
    $(window).scroll(function() {
        const resultsTop = $('#results').offset().top;
        const resultsBottom = resultsTop + $('#results').outerHeight();
        const viewportTop = $(window).scrollTop();
        const viewportBottom = viewportTop + $(window).height();
        
        if (resultsBottom > viewportTop && resultsTop < viewportBottom && !$('#results').hasClass('animated')) {
            $('#results').addClass('animated');
            animateProjectStats();
        }
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
            page: 'project_single',
            project: 'Delta Paint Manufacturing'
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
                    page: 'project_single',
                    project: 'Delta Paint Manufacturing'
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
            page: 'project_single',
            project: 'Delta Paint Manufacturing'
        });
    });
    
    // Track section views
    const sections = ['#hero', '#snapshot', '#problem', '#approach', '#gallery', '#testimonial', '#results', '#final-cta'];
    sections.forEach(function(section) {
        $(window).scroll(function() {
            const sectionTop = $(section).offset().top;
            const sectionBottom = sectionTop + $(section).outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            if (sectionBottom > viewportTop && sectionTop < viewportBottom) {
                trackEvent('section_view', {
                    section: section.replace('#', ''),
                    page: 'project_single',
                    project: 'Delta Paint Manufacturing'
                });
            }
        });
    });
    
    console.log('All single project page features initialized! 🚀');
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
    
    // Quick navigation shortcuts
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#results').offset().top - 80
        }, 1000);
    }
    
    if (e.ctrlKey && e.key === 'g') {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#gallery').offset().top - 80
        }, 1000);
    }
    
    if (e.ctrlKey && e.key === 'c') {
        e.preventDefault();
        window.location.href = 'contact.html';
    }
});

// ===== Page Load Animation =====
$(window).on('load', function() {
    $('body').addClass('loaded');
    console.log('Single Project page fully loaded! 🎉');
    
    // Show keyboard shortcuts tip
    setTimeout(() => {
        console.log('💡 Pro tip: Use Ctrl+R for results, Ctrl+G for gallery, Ctrl+C for contact!');
    }, 3000);
});

// ===== Project Comparison Feature =====
function compareWithSimilarProjects() {
    const currentProject = {
        name: 'Delta Paint Manufacturing',
        type: 'Firefighting System + Foam Suppression',
        duration: 12,
        compliance: '100%',
        cost_saving: '15%'
    };
    
    console.log('Current Project Data:', currentProject);
    // In real implementation, show comparison modal or redirect to comparison page
}

// ===== Share Project Feature =====
function shareProject() {
    const projectUrl = window.location.href;
    const projectTitle = 'Delta Paint Manufacturing - Fire Safety Case Study';
    
    if (navigator.share) {
        navigator.share({
            title: projectTitle,
            text: 'Check out this fire safety project by Sphinx Fire',
            url: projectUrl
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(projectUrl).then(() => {
            alert('Project link copied to clipboard!');
        });
    }
    
    trackEvent('project_share', {
        project: 'Delta Paint Manufacturing',
        method: navigator.share ? 'native' : 'clipboard',
        page: 'project_single'
    });
}

// ===== Download Project PDF =====
function downloadProjectPDF() {
    alert('Project case study PDF download started!');
    trackEvent('project_pdf_download', {
        project: 'Delta Paint Manufacturing',
        page: 'project_single'
    });
    // In real implementation, trigger actual PDF download
}

// ===== Request Similar Quote =====
function requestSimilarQuote() {
    const projectSpecs = {
        type: 'Firefighting System + Foam Suppression',
        industry: 'Paint Manufacturing',
        features: ['UL/FM Certified Pumps', 'Foam Suppression', 'SCADA Integration'],
        timeline: '12 days',
        compliance: 'Civil Defense + NFPA'
    };
    
    console.log('Quote Request Specs:', projectSpecs);
    
    // In real implementation, pre-fill contact form with project specifications
    window.location.href = 'contact.html?quote=similar&project=delta-paint';
    
    trackEvent('similar_quote_request', {
        source_project: 'Delta Paint Manufacturing',
        page: 'project_single'
    });
}

// ===== Emergency Contact for Similar Projects =====
function emergencyProjectContact() {
    if (confirm('🚨 URGENT PROJECT REQUEST\n\nThis will immediately contact our project team for emergency fire safety needs. Continue?')) {
        window.location.href = 'tel:+201234567890';
        trackEvent('emergency_project_contact', {
            source_project: 'Delta Paint Manufacturing',
            page: 'project_single'
        });
    }
}