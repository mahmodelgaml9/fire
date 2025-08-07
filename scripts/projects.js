$(document).ready(function() {
    console.log('Sphinx Fire Projects page loaded! 🔥');
    
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
        $('.project-card').each(function(index) {
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
    
    // ===== Project Filtering =====
    $('.filter-tag').click(function() {
        const filter = $(this).data('filter');
        
        // Update active filter
        $('.filter-tag').removeClass('active');
        $(this).addClass('active');
        
        // Filter projects
        if (filter === 'all') {
            $('.project-card').fadeIn(300);
        } else {
            $('.project-card').fadeOut(300);
            $(`.project-card[data-category="${filter}"]`).fadeIn(300);
        }
        
        // Track filter usage
        trackEvent('project_filter', {
            filter: filter,
            page: 'projects'
        });
    });
    
    // ===== Stats Counter Animation =====
    function animateCounters() {
        $('.stats-counter').each(function() {
            const $this = $(this);
            const countTo = $this.text();
            
            $({ countNum: 0 }).animate({
                countNum: parseInt(countTo)
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum) + (countTo.includes('+') ? '+' : ''));
                },
                complete: function() {
                    $this.text(countTo);
                }
            });
        });
    }
    
    // Trigger counter animation when stats section is visible
    $(window).scroll(function() {
        const statsTop = $('#stats').offset().top;
        const statsBottom = statsTop + $('#stats').outerHeight();
        const viewportTop = $(window).scrollTop();
        const viewportBottom = viewportTop + $(window).height();
        
        if (statsBottom > viewportTop && statsTop < viewportBottom && !$('#stats').hasClass('animated')) {
            $('#stats').addClass('animated');
            animateCounters();
        }
    });
    
    // ===== CTA Button Actions =====
    $('#header-cta, #talk-engineers-btn, #request-site-visit-btn').click(function() {
        // Redirect to contact page or show contact form
        window.location.href = 'contact.html';
    });
    
    $('#download-portfolio-btn').click(function() {
        alert('Company Portfolio PDF download started! Check your downloads folder.');
        trackEvent('portfolio_download', {
            source: 'projects_hero',
            page: 'projects'
        });
        // In real implementation, trigger actual PDF download
    });
    
    $('#custom-design-btn').click(function() {
        alert('Custom design quote form will open here');
        trackEvent('custom_design_request', {
            source: 'projects_cta',
            page: 'projects'
        });
        // In real implementation, show quote form or redirect
    });
    
    // ===== Project Card CTAs =====
    $('.project-card button').click(function() {
        const projectTitle = $(this).closest('.project-card').find('h3').text();
        alert(`${projectTitle} details will be displayed here`);
        trackEvent('project_details_view', {
            project: projectTitle,
            page: 'projects'
        });
        // In real implementation, show project details modal or page
    });
    
    // ===== CTA Pulse Animation Timer =====
    setTimeout(function() {
        $('#request-site-visit-btn').addClass('cta-pulse');
    }, 8000); // Start pulsing after 8 seconds
    
    // ===== Mobile Menu Toggle =====
    $('#mobile-menu-btn').click(function() {
        alert('Mobile menu would open here');
        // In real implementation, show mobile navigation
    });
    
    // ===== Project Card Hover Effects =====
    $('.project-card').hover(
        function() {
            $(this).find('.project-image img').addClass('scale-110');
        },
        function() {
            $(this).find('.project-image img').removeClass('scale-110');
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
            page: 'projects'
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
                    page: 'projects'
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
            page: 'projects'
        });
    });
    
    // Track project category interest
    $('.project-card').on('mouseenter', function() {
        const category = $(this).data('category');
        const projectTitle = $(this).find('h3').text();
        trackEvent('project_hover', {
            category: category,
            project: projectTitle,
            page: 'projects'
        });
    });
    
    console.log('All projects page features initialized! 🚀');
});

// ===== Keyboard Navigation =====
$(document).keydown(function(e) {
    // Filter shortcuts
    if (e.key === '1') {
        $('.filter-tag[data-filter="all"]').click();
    } else if (e.key === '2') {
        $('.filter-tag[data-filter="manufacturing"]').click();
    } else if (e.key === '3') {
        $('.filter-tag[data-filter="chemical"]').click();
    } else if (e.key === '4') {
        $('.filter-tag[data-filter="retail"]').click();
    } else if (e.key === '5') {
        $('.filter-tag[data-filter="warehouse"]').click();
    } else if (e.key === '6') {
        $('.filter-tag[data-filter="food"]').click();
    }
    
    // Quick contact
    if (e.ctrlKey && e.key === 'c') {
        e.preventDefault();
        window.location.href = 'contact.html';
    }
});

// ===== Page Load Animation =====
$(window).on('load', function() {
    $('body').addClass('loaded');
    console.log('Projects page fully loaded! 🎉');
    
    // Show keyboard shortcuts tip
    setTimeout(() => {
        console.log('💡 Pro tip: Use number keys 1-6 to filter projects, Ctrl+C for contact!');
    }, 3000);
});

// ===== Search Functionality (Optional Enhancement) =====
function searchProjects(query) {
    const searchTerm = query.toLowerCase();
    $('.project-card').each(function() {
        const projectText = $(this).text().toLowerCase();
        if (projectText.includes(searchTerm)) {
            $(this).fadeIn(300);
        } else {
            $(this).fadeOut(300);
        }
    });
}

// ===== Project Statistics =====
function updateProjectStats() {
    const totalProjects = $('.project-card').length;
    const completedProjects = $('.project-card .completion-badge').length;
    const ongoingProjects = $('.project-card .ongoing-badge').length;
    const certifiedProjects = $('.project-card .certified-badge').length;
    
    console.log('Project Statistics:', {
        total: totalProjects,
        completed: completedProjects,
        ongoing: ongoingProjects,
        certified: certifiedProjects
    });
}

// Update stats on page load
$(document).ready(function() {
    updateProjectStats();
});

// ===== Dynamic Content Loading (Future Enhancement) =====
function loadMoreProjects() {
    // Placeholder for loading additional projects
    console.log('Loading more projects...');
    // In real implementation, fetch from API and append to grid
}

// ===== Project Comparison Feature =====
let selectedProjects = [];

function toggleProjectSelection(projectCard) {
    const projectId = $(projectCard).data('project-id');
    
    if (selectedProjects.includes(projectId)) {
        selectedProjects = selectedProjects.filter(id => id !== projectId);
        $(projectCard).removeClass('selected');
    } else if (selectedProjects.length < 3) {
        selectedProjects.push(projectId);
        $(projectCard).addClass('selected');
    } else {
        alert('You can compare up to 3 projects at a time');
    }
    
    updateCompareButton();
}

function updateCompareButton() {
    if (selectedProjects.length >= 2) {
        $('#compare-projects-btn').show();
    } else {
        $('#compare-projects-btn').hide();
    }
}

// ===== Export Projects Data =====
function exportProjectsData() {
    const projectsData = [];
    $('.project-card').each(function() {
        const project = {
            title: $(this).find('h3').text(),
            category: $(this).data('category'),
            type: $(this).find('.text-brand-red').text(),
            description: $(this).find('.text-brand-gray').text(),
            status: $(this).find('.project-badge').text()
        };
        projectsData.push(project);
    });
    
    console.log('Projects Data Export:', projectsData);
    // In real implementation, convert to CSV/PDF and download
}