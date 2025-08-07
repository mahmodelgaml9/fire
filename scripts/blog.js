$(document).ready(function() {
    console.log('Sphinx Fire Blog page loaded! 🔥');
    
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
        $('.blog-card').each(function(index) {
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
    
    // ===== Article Filtering =====
    $('.filter-tag').click(function() {
        const filter = $(this).data('filter');
        
        // Update active filter
        $('.filter-tag').removeClass('active');
        $(this).addClass('active');
        
        // Filter articles
        if (filter === 'all') {
            $('.blog-card').fadeIn(300);
        } else {
            $('.blog-card').fadeOut(300);
            $(`.blog-card[data-category="${filter}"]`).fadeIn(300);
        }
        
        // Track filter usage
        trackEvent('blog_filter', {
            filter: filter,
            page: 'blog'
        });
    });
    
    // ===== Search Functionality =====
    let searchTimeout;
    const searchData = [
        { title: 'NFPA 20: Fire Pump Installation Standards', category: 'systems', url: '#' },
        { title: 'Fire Extinguisher Selection Guide', category: 'extinguishers', url: '#' },
        { title: 'OSHA Fire Safety Requirements', category: 'osha', url: '#' },
        { title: 'Civil Defense Inspection Guide', category: 'compliance', url: '#' },
        { title: 'Fire Pump Maintenance', category: 'pumps', url: '#' },
        { title: 'PPE Selection Guide', category: 'ppe', url: '#' },
        { title: 'Fire Safety Documentation', category: 'compliance', url: '#' },
        { title: 'Foam Suppression Systems', category: 'systems', url: '#' },
        { title: 'Emergency Response Planning', category: 'compliance', url: '#' }
    ];
    
    $('#search-input').on('input', function() {
        const query = $(this).val().toLowerCase();
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (query.length >= 2) {
                showSearchResults(query);
            } else {
                hideSearchResults();
            }
        }, 300);
    });
    
    function showSearchResults(query) {
        const results = searchData.filter(item => 
            item.title.toLowerCase().includes(query) || 
            item.category.toLowerCase().includes(query)
        );
        
        const resultsContainer = $('#search-results');
        resultsContainer.empty();
        
        if (results.length > 0) {
            results.slice(0, 5).forEach(result => {
                const resultItem = $(`
                    <div class="search-result-item" data-url="${result.url}">
                        <h4 class="font-semibold text-sm">${result.title}</h4>
                        <p class="text-xs text-brand-gray">${result.category}</p>
                    </div>
                `);
                resultsContainer.append(resultItem);
            });
            resultsContainer.show();
        } else {
            resultsContainer.html('<div class="search-result-item">No results found</div>').show();
        }
        
        trackEvent('blog_search', {
            query: query,
            results_count: results.length,
            page: 'blog'
        });
    }
    
    function hideSearchResults() {
        $('#search-results').hide();
    }
    
    // Handle search result clicks
    $(document).on('click', '.search-result-item', function() {
        const url = $(this).data('url');
        if (url && url !== '#') {
            window.location.href = url;
        }
        hideSearchResults();
    });
    
    // Hide search results when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('.search-container').length) {
            hideSearchResults();
        }
    });
    
    // ===== Newsletter Popup =====
    let hasShownPopup = false;
    let popupTimer;
    
    function showNewsletterPopup() {
        if (!hasShownPopup && !sessionStorage.getItem('newsletter_popup_shown')) {
            $('#newsletter-popup').addClass('active');
            hasShownPopup = true;
            sessionStorage.setItem('newsletter_popup_shown', 'true');
            
            trackEvent('newsletter_popup_shown', {
                trigger: 'scroll_60_percent',
                page: 'blog'
            });
        }
    }
    
    // Show popup after 30 seconds or 60% scroll
    setTimeout(showNewsletterPopup, 30000);
    
    $(window).scroll(function() {
        const scrollPercent = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
        if (scrollPercent >= 60) {
            showNewsletterPopup();
        }
    });
    
    // ===== Newsletter Form Submissions =====
    $('#popup-newsletter-form').submit(function(e) {
        e.preventDefault();
        
        const email = $('#popup-email').val();
        console.log('Newsletter subscription:', email);
        
        // Show success message
        $(this).html(`
            <div class="text-center py-4">
                <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                <h4 class="font-semibold text-green-600">Successfully Subscribed!</h4>
                <p class="text-sm text-brand-gray">You'll receive our weekly safety insights.</p>
            </div>
        `);
        
        setTimeout(() => {
            closeNewsletterPopup();
        }, 2000);
        
        trackEvent('newsletter_subscription', {
            source: 'popup',
            email: email,
            page: 'blog'
        });
    });
    
    $('#subscribe-newsletter-btn').click(function() {
        const email = $('#newsletter-email').val();
        
        if (email && validateEmail(email)) {
            console.log('Newsletter subscription:', email);
            
            // Show success message
            $(this).text('✅ Subscribed!').prop('disabled', true);
            $('#newsletter-email').val('').attr('placeholder', 'Thank you for subscribing!');
            
            setTimeout(() => {
                $(this).text('🔴 Subscribe').prop('disabled', false);
                $('#newsletter-email').attr('placeholder', 'Enter your email address');
            }, 3000);
            
            trackEvent('newsletter_subscription', {
                source: 'cta_section',
                email: email,
                page: 'blog'
            });
        } else {
            alert('Please enter a valid email address');
        }
    });
    
    // ===== CTA Button Actions =====
    $('#header-cta, #subscribe-tips-btn').click(function() {
        $('html, body').animate({
            scrollTop: $('#newsletter-cta').offset().top - 80
        }, 1000);
    });
    
    $('#read-latest-btn').click(function() {
        $('html, body').animate({
            scrollTop: $('#articles').offset().top - 80
        }, 1000);
    });
    
    // ===== Featured Article CTA =====
    $('.featured-article button').click(function() {
        alert('Civil Defense Inspection Guide PDF download started!');
        trackEvent('featured_article_download', {
            article: 'Civil Defense Inspection Guide',
            page: 'blog'
        });
        // In real implementation, trigger actual PDF download
    });
    
    // ===== Article Card CTAs =====
    $('.blog-card button').click(function() {
        const articleTitle = $(this).closest('.blog-card').find('h3').text();
        alert(`${articleTitle} will open here`);
        trackEvent('article_read', {
            article: articleTitle,
            page: 'blog'
        });
        // In real implementation, navigate to full article page
    });
    
    // ===== Load More Articles =====
    $('#load-more-btn').click(function() {
        const button = $(this);
        const originalText = button.text();
        
        button.text('Loading...').prop('disabled', true);
        
        // Simulate loading delay
        setTimeout(() => {
            // In real implementation, load more articles from API
            alert('More articles would be loaded here');
            button.text(originalText).prop('disabled', false);
            
            trackEvent('load_more_articles', {
                page: 'blog'
            });
        }, 1000);
    });
    
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
            page: 'blog'
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
                    page: 'blog'
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
            page: 'blog'
        });
    });
    
    // Track article category interest
    $('.blog-card').on('mouseenter', function() {
        const category = $(this).data('category');
        const articleTitle = $(this).find('h3').text();
        trackEvent('article_hover', {
            category: category,
            article: articleTitle,
            page: 'blog'
        });
    });
    
    console.log('All blog page features initialized! 🚀');
});

// ===== Global Functions =====
function closeNewsletterPopup() {
    $('#newsletter-popup').removeClass('active');
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// ===== Keyboard Navigation =====
$(document).keydown(function(e) {
    // Close popup with Escape key
    if (e.key === 'Escape') {
        closeNewsletterPopup();
    }
    
    // Search shortcut
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        $('#search-input').focus();
    }
    
    // Filter shortcuts
    if (e.key === '1') {
        $('.filter-tag[data-filter="all"]').click();
    } else if (e.key === '2') {
        $('.filter-tag[data-filter="compliance"]').click();
    } else if (e.key === '3') {
        $('.filter-tag[data-filter="systems"]').click();
    } else if (e.key === '4') {
        $('.filter-tag[data-filter="extinguishers"]').click();
    }
    
    // Newsletter shortcut
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        $('#newsletter-popup').addClass('active');
    }
});

// ===== Page Load Animation =====
$(window).on('load', function() {
    $('body').addClass('loaded');
    console.log('Blog page fully loaded! 🎉');
    
    // Show keyboard shortcuts tip
    setTimeout(() => {
        console.log('💡 Pro tip: Use Ctrl+F to search, number keys 1-4 for filters, Ctrl+N for newsletter!');
    }, 3000);
});

// ===== Social Sharing Functions =====
function shareArticle(title, url) {
    if (navigator.share) {
        navigator.share({
            title: title,
            text: 'Check out this fire safety article from Sphinx Fire',
            url: url
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('Article link copied to clipboard!');
        });
    }
    
    trackEvent('article_share', {
        article: title,
        method: navigator.share ? 'native' : 'clipboard',
        page: 'blog'
    });
}

// ===== Reading Progress Indicator =====
function updateReadingProgress() {
    const scrollTop = $(window).scrollTop();
    const docHeight = $(document).height() - $(window).height();
    const scrollPercent = (scrollTop / docHeight) * 100;
    
    // Update progress bar if exists
    $('.reading-progress').css('width', scrollPercent + '%');
}

$(window).scroll(updateReadingProgress);

// ===== Article Bookmarking =====
let bookmarkedArticles = JSON.parse(localStorage.getItem('bookmarked_articles') || '[]');

function toggleBookmark(articleTitle, articleUrl) {
    const article = { title: articleTitle, url: articleUrl };
    const index = bookmarkedArticles.findIndex(item => item.title === articleTitle);
    
    if (index > -1) {
        bookmarkedArticles.splice(index, 1);
        console.log('Article removed from bookmarks');
    } else {
        bookmarkedArticles.push(article);
        console.log('Article added to bookmarks');
    }
    
    localStorage.setItem('bookmarked_articles', JSON.stringify(bookmarkedArticles));
    
    trackEvent('article_bookmark', {
        article: articleTitle,
        action: index > -1 ? 'remove' : 'add',
        page: 'blog'
    });
}

// ===== Content Recommendations =====
function getRecommendedArticles(currentCategory) {
    const recommendations = {
        'compliance': ['OSHA Fire Safety Requirements', 'Fire Safety Documentation'],
        'systems': ['NFPA 20: Fire Pump Installation Standards', 'Foam Suppression Systems'],
        'extinguishers': ['Fire Extinguisher Selection Guide', 'PPE Selection Guide'],
        'osha': ['OSHA Fire Safety Requirements', 'Emergency Response Planning'],
        'civil-defense': ['Civil Defense Inspection Guide', 'Fire Safety Documentation'],
        'pumps': ['Fire Pump Maintenance', 'NFPA 20: Fire Pump Installation Standards'],
        'ppe': ['PPE Selection Guide', 'Emergency Response Planning']
    };
    
    return recommendations[currentCategory] || [];
}

// ===== Article Reading Time Calculator =====
function calculateReadingTime(content) {
    const wordsPerMinute = 200;
    const wordCount = content.split(' ').length;
    const readingTime = Math.ceil(wordCount / wordsPerMinute);
    return readingTime;
}

// ===== Newsletter Preferences =====
function updateNewsletterPreferences(preferences) {
    localStorage.setItem('newsletter_preferences', JSON.stringify(preferences));
    
    trackEvent('newsletter_preferences_updated', {
        preferences: preferences,
        page: 'blog'
    });
}

// ===== Article View Tracking =====
function trackArticleView(articleTitle, category, readingTime) {
    trackEvent('article_view', {
        article: articleTitle,
        category: category,
        reading_time: readingTime,
        page: 'blog'
    });
}

// ===== Content Performance Analytics =====
function getContentPerformance() {
    const performance = {
        most_viewed: 'Civil Defense Inspection Guide',
        most_shared: 'Fire Extinguisher Selection Guide',
        most_downloaded: 'NFPA 20 Standards',
        average_time_on_page: '4:32',
        bounce_rate: '23%'
    };
    
    console.log('Content Performance:', performance);
    return performance;
}

// ===== Export Functions for External Use =====
window.BlogFunctions = {
    shareArticle,
    toggleBookmark,
    getRecommendedArticles,
    calculateReadingTime,
    updateNewsletterPreferences,
    trackArticleView,
    getContentPerformance
};