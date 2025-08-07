// ===== Analytics Events =====
function trackEvent(eventName, data) {
    console.log('Analytics Event:', eventName, data);
    // In real implementation, send to analytics platform
}

$(document).ready(function() {
    console.log('Sphinx Fire Blog Article page loaded! 🔥');
    
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
    
    // ===== Reading Progress Bar =====
    function updateReadingProgress() {
        const scrollTop = $(window).scrollTop();
        const docHeight = $(document).height() - $(window).height();
        const scrollPercent = (scrollTop / docHeight) * 100;
        
        $('#reading-progress').css('width', Math.min(scrollPercent, 100) + '%');
    }
    
    $(window).scroll(updateReadingProgress);
    updateReadingProgress(); // Initial call
    
    // ===== CTA Button Actions =====
    $('#header-cta').click(function() {
        // Scroll to final CTA or show contact form
        $('html, body').animate({
            scrollTop: $('.py-16.bg-brand-black').offset().top - 80
        }, 1000);
    });
    
    // ===== Mobile Menu Toggle =====
    $('#mobile-menu-btn').click(function() {
        alert('Mobile menu would open here');
        // In real implementation, show mobile navigation
    });
    
    // ===== CTA Pulse Animation Timer =====
    setTimeout(function() {
        $('.cta-pulse').addClass('cta-pulse');
    }, 3000); // Start pulsing after 3 seconds
    
    // Track button clicks
    $('button, a[href*="html"]').click(function() {
        const elementText = $(this).text().trim();
        const elementId = $(this).attr('id') || 'unnamed-element';
        trackEvent('element_click', {
            element_id: elementId,
            element_text: elementText,
            page: 'blog_article',
            article: 'civil_defense_inspection'
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
                    page: 'blog_article',
                    article: 'civil_defense_inspection'
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
            page: 'blog_article',
            article: 'civil_defense_inspection'
        });
    });
    
    // Track CTA interactions
    $('.cta-box button').click(function() {
        const ctaType = $(this).text().includes('Book') ? 'consultation' : 'download';
        trackEvent('mid_article_cta', {
            type: ctaType,
            position: 'mid_article',
            article: 'civil_defense_inspection'
        });
    });
    
    // Track related article clicks
    $('.grid a[href*="blog-article"]').click(function() {
        const articleTitle = $(this).closest('div').find('h3').text();
        trackEvent('related_article_click', {
            target_article: articleTitle,
            source_article: 'civil_defense_inspection'
        });
    });
    
    console.log('All blog article page features initialized! 🚀');
});

// ===== Global Functions =====
function openLightbox(src, alt) {
    $('#lightbox-img').attr('src', src).attr('alt', alt);
    $('#lightbox').addClass('active');
    
    trackEvent('image_lightbox', {
        image_alt: alt,
        article: 'civil_defense_inspection'
    });
}

function closeLightbox() {
    $('#lightbox').removeClass('active');
}

function bookConsultation() {
    alert('Free consultation booking form will open here');
    trackEvent('consultation_request', {
        source: 'mid_article_cta',
        article: 'civil_defense_inspection'
    });
    // In real implementation, show booking modal or redirect to contact
}

function downloadGuide() {
    alert('Pre-inspection checklist PDF download started!');
    trackEvent('guide_download', {
        guide_type: 'pre_inspection_checklist',
        source: 'mid_article_cta',
        article: 'civil_defense_inspection'
    });
    // In real implementation, trigger actual PDF download
}

function requestConsultation() {
    alert('Consultation request form will open here');
    trackEvent('consultation_request', {
        source: 'final_cta',
        article: 'civil_defense_inspection'
    });
    // In real implementation, show contact form or redirect
}

function talkToConsultant() {
    alert('Direct consultant contact will be initiated');
    trackEvent('consultant_contact', {
        source: 'final_cta',
        article: 'civil_defense_inspection'
    });
    // In real implementation, show contact options or direct call
}

// ===== Social Sharing Functions =====
function shareLinkedIn() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('How to Pass Civil Defense Inspection in Egypt - Complete Guide');
    const summary = encodeURIComponent('Essential guide for industrial facilities. Documentation checklist, common mistakes, and expert consultation to ensure 100% compliance.');
    
    window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}&title=${title}&summary=${summary}`, '_blank', 'width=600,height=400');
    
    trackEvent('social_share', {
        platform: 'linkedin',
        article: 'civil_defense_inspection'
    });
}

function shareWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('Check out this comprehensive guide on passing Civil Defense inspection in Egypt: ');
    
    window.open(`https://wa.me/?text=${text}${url}`, '_blank');
    
    trackEvent('social_share', {
        platform: 'whatsapp',
        article: 'civil_defense_inspection'
    });
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        alert('Article link copied to clipboard!');
        
        trackEvent('social_share', {
            platform: 'copy_link',
            article: 'civil_defense_inspection'
        });
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = window.location.href;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('Article link copied to clipboard!');
    });
}

// ===== Keyboard Navigation =====
$(document).keydown(function(e) {
    // Close lightbox with Escape key
    if (e.key === 'Escape') {
        closeLightbox();
    }
    
    // Share shortcuts
    if (e.ctrlKey && e.key === 'l') {
        e.preventDefault();
        shareLinkedIn();
    }
    
    if (e.ctrlKey && e.key === 'w') {
        e.preventDefault();
        shareWhatsApp();
    }
    
    if (e.ctrlKey && e.key === 'c') {
        e.preventDefault();
        copyLink();
    }
    
    // Quick navigation
    if (e.key === 'Home') {
        $('html, body').animate({ scrollTop: 0 }, 800);
    }
    
    if (e.key === 'End') {
        $('html, body').animate({ scrollTop: $(document).height() }, 800);
    }
});

// ===== Page Load Animation =====
$(window).on('load', function() {
    $('body').addClass('loaded');
    console.log('Blog Article page fully loaded! 🎉');
    
    // Show keyboard shortcuts tip
    setTimeout(() => {
        console.log('💡 Pro tip: Use Ctrl+L for LinkedIn, Ctrl+W for WhatsApp, Ctrl+C to copy link!');
    }, 3000);
});

// ===== Article Engagement Tracking =====
let engagementEvents = {
    scrollMilestones: [25, 50, 75, 90],
    timeOnPage: 0,
    ctaClicks: 0,
    socialShares: 0
};

// Track scroll milestones
$(window).scroll(function() {
    const scrollPercent = Math.round(($(window).scrollTop() / ($(document).height() - $(window).height())) * 100);
    
    engagementEvents.scrollMilestones.forEach((milestone, index) => {
        if (scrollPercent >= milestone && engagementEvents.scrollMilestones[index] !== null) {
            trackEvent('scroll_milestone', {
                milestone: milestone,
                article: 'civil_defense_inspection'
            });
            engagementEvents.scrollMilestones[index] = null; // Mark as tracked
        }
    });
});

// Track time milestones
setInterval(() => {
    engagementEvents.timeOnPage += 30;
    
    if (engagementEvents.timeOnPage % 120 === 0) { // Every 2 minutes
        trackEvent('time_milestone', {
            seconds: engagementEvents.timeOnPage,
            article: 'civil_defense_inspection'
        });
    }
}, 30000); // Check every 30 seconds

// ===== Content Interaction Tracking =====
$('.article-content h2, .article-content h3').on('click', function() {
    const headingText = $(this).text();
    trackEvent('heading_click', {
        heading: headingText,
        article: 'civil_defense_inspection'
    });
});

$('.article-content a').on('click', function() {
    const linkText = $(this).text();
    const linkHref = $(this).attr('href');
    trackEvent('internal_link_click', {
        link_text: linkText,
        link_href: linkHref,
        article: 'civil_defense_inspection'
    });
});

// ===== Reading Behavior Analysis =====
let readingBehavior = {
    fastScrolling: 0,
    slowScrolling: 0,
    backtracking: 0,
    lastScrollTop: 0
};

$(window).scroll(function() {
    const currentScrollTop = $(window).scrollTop();
    const scrollDiff = Math.abs(currentScrollTop - readingBehavior.lastScrollTop);
    
    if (scrollDiff > 100) {
        readingBehavior.fastScrolling++;
    } else if (scrollDiff > 0 && scrollDiff <= 50) {
        readingBehavior.slowScrolling++;
    }
    
    if (currentScrollTop < readingBehavior.lastScrollTop) {
        readingBehavior.backtracking++;
    }
    
    readingBehavior.lastScrollTop = currentScrollTop;
});

// Send reading behavior data when user leaves
$(window).on('beforeunload', function() {
    trackEvent('reading_behavior', {
        fast_scrolling: readingBehavior.fastScrolling,
        slow_scrolling: readingBehavior.slowScrolling,
        backtracking: readingBehavior.backtracking,
        article: 'civil_defense_inspection'
    });
});

// ===== Content Feedback System =====
function showFeedbackPrompt() {
    // Show after user has been on page for 3 minutes and scrolled 75%
    setTimeout(() => {
        if (maxScroll >= 75) {
            console.log('💬 Article feedback prompt would show here');
            // In real implementation, show feedback modal
        }
    }, 180000); // 3 minutes
}

showFeedbackPrompt();

// ===== Related Content Recommendations =====
function trackContentInterest() {
    const categories = ['compliance', 'fire-systems', 'documentation'];
    const currentCategory = 'compliance';
    
    // Track category interest based on time spent
    setTimeout(() => {
        trackEvent('content_interest', {
            category: currentCategory,
            engagement_level: 'high',
            article: 'civil_defense_inspection'
        });
    }, 120000); // After 2 minutes
}

trackContentInterest();

// ===== Performance Monitoring =====
$(window).on('load', function() {
    const loadTime = performance.now();
    trackEvent('page_performance', {
        load_time: Math.round(loadTime),
        article: 'civil_defense_inspection'
    });
});

// ===== Accessibility Enhancements =====
$(document).ready(function() {
    // Add skip to content link
    $('body').prepend('<a href="#hero" class="sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 bg-brand-red text-white p-2 z-50">Skip to content</a>');
    
    // Enhance keyboard navigation for images
    $('.article-content img').attr('tabindex', '0').on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            openLightbox($(this).attr('src'), $(this).attr('alt'));
        }
    });
});