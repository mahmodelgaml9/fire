// ===== Sphinx Fire CMS Integration =====
// Connects frontend with CMS API for dynamic content

class CMSIntegration {
    constructor() {
        this.apiBaseUrl = '/api';
        this.currentLanguage = 'en';
        this.cache = {};
        this.cacheExpiry = 5 * 60 * 1000; // 5 minutes
        this.init();
    }
    
    init() {
        // Detect language
        this.detectLanguage();
        
        // Initialize content loaders
        this.initContentLoaders();
        
        console.log(`🔄 CMS Integration initialized: ${this.currentLanguage}`);
    }
    
    detectLanguage() {
        // Get language from URL, localStorage, or browser
        const urlParams = new URLSearchParams(window.location.search);
        const urlLang = urlParams.get('lang');
        
        if (urlLang && ['en', 'ar'].includes(urlLang)) {
            this.currentLanguage = urlLang;
            return;
        }
        
        const storedLang = localStorage.getItem('preferred_language');
        if (storedLang && ['en', 'ar'].includes(storedLang)) {
            this.currentLanguage = storedLang;
            return;
        }
        
        const browserLang = navigator.language.split('-')[0];
        if (['en', 'ar'].includes(browserLang)) {
            this.currentLanguage = browserLang;
            return;
        }
        
        this.currentLanguage = 'en'; // Default
    }
    
    initContentLoaders() {
        // Load page-specific content based on current page
        const pageType = this.detectPageType();
        
        switch (pageType) {
            case 'home':
                this.loadHomePageContent();
                break;
            case 'services':
                this.loadServicesContent();
                break;
            case 'service-single':
                this.loadSingleServiceContent();
                break;
            case 'projects':
                this.loadProjectsContent();
                break;
            case 'project-single':
                this.loadSingleProjectContent();
                break;
            case 'blog':
                this.loadBlogContent();
                break;
            case 'blog-single':
                this.loadSingleBlogContent();
                break;
            case 'location':
                this.loadLocationContent();
                break;
            case 'about':
                this.loadAboutContent();
                break;
            case 'contact':
                this.loadContactContent();
                break;
            default:
                this.loadGenericPageContent();
        }
        
        // Load global content for all pages
        this.loadGlobalContent();
    }
    
    detectPageType() {
        const path = window.location.pathname;
        
        if (path === '/' || path === '/index.html') {
            return 'home';
        }
        
        if (path === '/services.html') {
            return 'services';
        }
        
        if (path.match(/\/firefighting-systems\.html/) || 
            path.match(/\/fire-alarm-systems\.html/) ||
            path.match(/\/fire-extinguishers\.html/)) {
            return 'service-single';
        }
        
        if (path === '/projects.html') {
            return 'projects';
        }
        
        if (path.match(/\/project-.*\.html/)) {
            return 'project-single';
        }
        
        if (path === '/blog.html') {
            return 'blog';
        }
        
        if (path.match(/\/blog-article-.*\.html/)) {
            return 'blog-single';
        }
        
        if (path.match(/\/.*-city-fire-protection\.html/)) {
            return 'location';
        }
        
        if (path === '/about.html') {
            return 'about';
        }
        
        if (path === '/contact.html') {
            return 'contact';
        }
        
        return 'generic';
    }
    
    async loadGlobalContent() {
        try {
            // Load global settings
            const settings = await this.fetchData(`/settings?lang=${this.currentLanguage}&public=true`);
            
            // Apply global settings
            this.applyGlobalSettings(settings);
            
        } catch (error) {
            console.error('Failed to load global content:', error);
        }
    }
    
    applyGlobalSettings(settings) {
        // Update site name and contact info
        document.querySelectorAll('[data-cms="site-name"]').forEach(el => {
            el.textContent = settings.site_name || 'Sphinx Fire';
        });
        
        document.querySelectorAll('[data-cms="site-tagline"]').forEach(el => {
            el.textContent = settings.site_tagline || 'Fire Safety Solutions';
        });
        
        document.querySelectorAll('[data-cms="contact-phone"]').forEach(el => {
            el.textContent = settings.contact_phone || '+20 123 456 7890';
            if (el.tagName === 'A') {
                el.href = `tel:${settings.contact_phone.replace(/\s/g, '')}`;
            }
        });
        
        document.querySelectorAll('[data-cms="contact-email"]').forEach(el => {
            el.textContent = settings.contact_email || 'info@sphinxfire.com';
            if (el.tagName === 'A') {
                el.href = `mailto:${settings.contact_email}`;
            }
        });
        
        document.querySelectorAll('[data-cms="office-address"]').forEach(el => {
            el.textContent = settings.office_address || 'Sadat City, Egypt';
        });
        
        // Update social media links
        if (settings.social_media) {
            const socialLinks = JSON.parse(settings.social_media);
            Object.entries(socialLinks).forEach(([platform, url]) => {
                document.querySelectorAll(`[data-cms="social-${platform}"]`).forEach(el => {
                    if (el.tagName === 'A') {
                        el.href = url;
                    }
                });
            });
        }
        
        // Update copyright year
        const currentYear = new Date().getFullYear();
        document.querySelectorAll('[data-cms="copyright-year"]').forEach(el => {
            el.textContent = currentYear;
        });
    }
    
    async loadHomePageContent() {
        try {
            // Load hero section content
            const heroContent = await this.fetchData(`/pages?slug=home&lang=${this.currentLanguage}`);
            
            if (heroContent && heroContent.length > 0) {
                const homePage = heroContent[0];
                
                // Load sections
                const sections = await this.fetchData(`/pages/${homePage.id}?lang=${this.currentLanguage}`);
                
                if (sections && sections.sections) {
                    // Apply sections content
                    sections.sections.forEach(section => {
                        this.applySectionContent(section);
                    });
                }
            }
            
            // Load featured services
            const featuredServices = await this.fetchData(`/services?lang=${this.currentLanguage}&featured=true`);
            this.renderFeaturedServices(featuredServices);
            
            // Load testimonials
            const testimonials = await this.fetchData(`/testimonials?lang=${this.currentLanguage}&featured=true`);
            this.renderTestimonials(testimonials);
            
            // Load featured projects
            const featuredProjects = await this.fetchData(`/projects?lang=${this.currentLanguage}&featured=true`);
            this.renderFeaturedProjects(featuredProjects);
            
        } catch (error) {
            console.error('Failed to load home page content:', error);
        }
    }
    
    async loadServicesContent() {
        try {
            // Load services page content
            const pageContent = await this.fetchData(`/pages?slug=services&lang=${this.currentLanguage}`);
            
            if (pageContent && pageContent.length > 0) {
                const servicesPage = pageContent[0];
                
                // Update page title and meta
                document.title = servicesPage.meta_title || servicesPage.title;
                
                // Update hero section
                document.querySelectorAll('[data-cms="page-title"]').forEach(el => {
                    el.textContent = servicesPage.title;
                });
                
                document.querySelectorAll('[data-cms="page-description"]').forEach(el => {
                    el.textContent = servicesPage.excerpt;
                });
            }
            
            // Load all service categories
            const categories = await this.fetchData(`/service-categories?lang=${this.currentLanguage}`);
            
            // Load all services
            const services = await this.fetchData(`/services?lang=${this.currentLanguage}`);
            
            // Render services by category
            this.renderServicesByCategory(categories, services);
            
        } catch (error) {
            console.error('Failed to load services content:', error);
        }
    }
    
    async loadSingleServiceContent() {
        try {
            // Extract service slug from URL
            const path = window.location.pathname;
            const slug = path.replace(/^\/|\.html$/g, '');
            
            // Load service details
            const service = await this.fetchData(`/services?slug=${slug}&lang=${this.currentLanguage}`);
            
            if (service) {
                // Update page title and meta
                document.title = service.meta_title || service.name;
                
                // Update service content
                document.querySelectorAll('[data-cms="service-title"]').forEach(el => {
                    el.textContent = service.name;
                });
                
                document.querySelectorAll('[data-cms="service-description"]').forEach(el => {
                    el.innerHTML = service.full_description;
                });
                
                // Update service features
                if (service.features) {
                    const featuresContainer = document.querySelector('[data-cms="service-features"]');
                    if (featuresContainer) {
                        featuresContainer.innerHTML = '';
                        
                        service.features.forEach(feature => {
                            const featureEl = document.createElement('div');
                            featureEl.className = 'feature-item';
                            featureEl.innerHTML = `
                                <div class="flex items-center space-x-4">
                                    <div class="w-8 h-8 bg-brand-red rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-white text-sm"></i>
                                    </div>
                                    <span class="text-lg font-semibold">${feature}</span>
                                </div>
                            `;
                            featuresContainer.appendChild(featureEl);
                        });
                    }
                }
                
                // Update service gallery
                if (service.gallery && service.gallery.length > 0) {
                    const galleryContainer = document.querySelector('[data-cms="service-gallery"]');
                    if (galleryContainer) {
                        galleryContainer.innerHTML = '';
                        
                        service.gallery.forEach(image => {
                            const galleryItem = document.createElement('div');
                            galleryItem.className = 'gallery-item rounded-xl overflow-hidden shadow-lg';
                            galleryItem.setAttribute('data-image', image.url);
                            galleryItem.innerHTML = `
                                <img src="${image.url}" alt="${image.alt || service.name}" class="w-full h-64 object-cover">
                                <div class="p-4 bg-white">
                                    <h4 class="font-semibold mb-2">${image.title || ''}</h4>
                                    <p class="text-sm text-brand-gray">${image.caption || ''}</p>
                                </div>
                            `;
                            galleryContainer.appendChild(galleryItem);
                        });
                        
                        // Initialize lightbox
                        this.initLightbox();
                    }
                }
            }
            
        } catch (error) {
            console.error('Failed to load service content:', error);
        }
    }
    
    async loadBlogContent() {
        try {
            // Load blog page content
            const pageContent = await this.fetchData(`/pages?slug=blog&lang=${this.currentLanguage}`);
            
            if (pageContent && pageContent.length > 0) {
                const blogPage = pageContent[0];
                
                // Update page title and meta
                document.title = blogPage.meta_title || blogPage.title;
                
                // Update hero section
                document.querySelectorAll('[data-cms="page-title"]').forEach(el => {
                    el.textContent = blogPage.title;
                });
                
                document.querySelectorAll('[data-cms="page-description"]').forEach(el => {
                    el.textContent = blogPage.excerpt;
                });
            }
            
            // Load blog categories
            const categories = await this.fetchData(`/blog-categories?lang=${this.currentLanguage}`);
            this.renderBlogCategories(categories);
            
            // Load blog posts
            const posts = await this.fetchData(`/blog?lang=${this.currentLanguage}&limit=9`);
            this.renderBlogPosts(posts.data, posts.meta);
            
            // Load featured article
            const featuredPost = await this.fetchData(`/blog?lang=${this.currentLanguage}&featured=true&limit=1`);
            if (featuredPost && featuredPost.data.length > 0) {
                this.renderFeaturedArticle(featuredPost.data[0]);
            }
            
        } catch (error) {
            console.error('Failed to load blog content:', error);
        }
    }
    
    async loadSingleBlogContent() {
        try {
            // Extract blog post slug from URL
            const path = window.location.pathname;
            const slug = path.replace(/^\/blog-article-|\.html$/g, '');
            
            // Load blog post details
            const post = await this.fetchData(`/blog?slug=${slug}&lang=${this.currentLanguage}`);
            
            if (post) {
                // Update page title and meta
                document.title = post.meta_title || post.title;
                
                // Update post content
                document.querySelectorAll('[data-cms="article-title"]').forEach(el => {
                    el.textContent = post.title;
                });
                
                document.querySelectorAll('[data-cms="article-content"]').forEach(el => {
                    el.innerHTML = post.content;
                });
                
                document.querySelectorAll('[data-cms="article-date"]').forEach(el => {
                    const date = new Date(post.published_at);
                    el.textContent = date.toLocaleDateString(
                        this.currentLanguage === 'ar' ? 'ar-EG' : 'en-US',
                        { year: 'numeric', month: 'long', day: 'numeric' }
                    );
                });
                
                document.querySelectorAll('[data-cms="article-author"]').forEach(el => {
                    el.textContent = `${post.first_name} ${post.last_name}`;
                });
                
                document.querySelectorAll('[data-cms="article-category"]').forEach(el => {
                    el.textContent = post.category_name;
                });
                
                document.querySelectorAll('[data-cms="article-reading-time"]').forEach(el => {
                    el.textContent = `${post.reading_time} min read`;
                });
                
                // Render related posts
                if (post.related_posts && post.related_posts.length > 0) {
                    this.renderRelatedPosts(post.related_posts);
                }
            }
            
        } catch (error) {
            console.error('Failed to load blog post content:', error);
        }
    }
    
    async loadLocationContent() {
        try {
            // Extract location slug from URL
            const path = window.location.pathname;
            const slug = path.replace(/-fire-protection\.html$/, '').replace(/^\//, '');
            
            // Load location details
            const location = await this.fetchData(`/locations?slug=${slug}&lang=${this.currentLanguage}`);
            
            if (location) {
                // Update page title and meta
                document.title = location.meta_title || `Fire Protection in ${location.name}`;
                
                // Update location content
                document.querySelectorAll('[data-cms="location-name"]').forEach(el => {
                    el.textContent = location.name;
                });
                
                document.querySelectorAll('[data-cms="location-description"]').forEach(el => {
                    el.textContent = location.description;
                });
                
                // Update hero section
                document.querySelectorAll('[data-cms="hero-title"]').forEach(el => {
                    el.innerHTML = el.innerHTML.replace(/\{location\}/g, location.name);
                });
                
                document.querySelectorAll('[data-cms="hero-subtitle"]').forEach(el => {
                    el.innerHTML = el.innerHTML.replace(/\{location\}/g, location.name);
                });
                
                // Render location advantages
                if (location.content && location.content.advantage) {
                    this.renderLocationAdvantages(location.content.advantage);
                }
                
                // Render location services
                if (location.content && location.content.service_highlight) {
                    this.renderLocationServices(location.content.service_highlight);
                }
                
                // Render location testimonials
                if (location.testimonials && location.testimonials.length > 0) {
                    this.renderLocationTestimonials(location.testimonials);
                }
                
                // Render location projects
                if (location.projects && location.projects.length > 0) {
                    this.renderLocationProjects(location.projects);
                }
            }
            
        } catch (error) {
            console.error('Failed to load location content:', error);
        }
    }
    
    async loadContactContent() {
        try {
            // Load contact page content
            const pageContent = await this.fetchData(`/pages?slug=contact&lang=${this.currentLanguage}`);
            
            if (pageContent && pageContent.length > 0) {
                const contactPage = pageContent[0];
                
                // Update page title and meta
                document.title = contactPage.meta_title || contactPage.title;
                
                // Update hero section
                document.querySelectorAll('[data-cms="page-title"]').forEach(el => {
                    el.textContent = contactPage.title;
                });
                
                document.querySelectorAll('[data-cms="page-description"]').forEach(el => {
                    el.textContent = contactPage.excerpt;
                });
            }
            
            // Load contact form
            const contactForm = await this.fetchData(`/contact-forms?key=general_contact&lang=${this.currentLanguage}`);
            
            if (contactForm) {
                this.renderContactForm(contactForm);
            }
            
            // Initialize form submission
            this.initContactForm();
            
        } catch (error) {
            console.error('Failed to load contact content:', error);
        }
    }
    
    // ===== RENDERING METHODS =====
    
    applySectionContent(section) {
        const sectionContainer = document.querySelector(`[data-section="${section.section_key}"]`);
        if (!sectionContainer) return;
        
        // Update section title
        const titleEl = sectionContainer.querySelector('[data-cms="section-title"]');
        if (titleEl && section.title) {
            titleEl.textContent = section.title;
        }
        
        // Update section subtitle
        const subtitleEl = sectionContainer.querySelector('[data-cms="section-subtitle"]');
        if (subtitleEl && section.subtitle) {
            subtitleEl.textContent = section.subtitle;
        }
        
        // Update section content
        const contentEl = sectionContainer.querySelector('[data-cms="section-content"]');
        if (contentEl && section.content) {
            contentEl.innerHTML = section.content;
        }
        
        // Update section button
        const buttonEl = sectionContainer.querySelector('[data-cms="section-button"]');
        if (buttonEl && section.button_text) {
            buttonEl.textContent = section.button_text;
            if (section.button_url) {
                buttonEl.href = section.button_url;
            }
        }
        
        // Update section background
        if (section.background_image) {
            sectionContainer.style.backgroundImage = `url(${section.background_image})`;
        }
        
        // Handle section-specific content
        switch (section.section_type) {
            case 'hero':
                this.applyHeroSection(sectionContainer, section);
                break;
            case 'services':
                this.applyServicesSection(sectionContainer, section);
                break;
            case 'testimonials':
                this.applyTestimonialsSection(sectionContainer, section);
                break;
            case 'projects':
                this.applyProjectsSection(sectionContainer, section);
                break;
            case 'stats':
                this.applyStatsSection(sectionContainer, section);
                break;
            case 'cta':
                this.applyCtaSection(sectionContainer, section);
                break;
        }
    }
    
    applyHeroSection(container, section) {
        // Apply hero-specific content
        const settings = JSON.parse(section.settings || '{}');
        
        // Update hero image if provided
        if (section.background_image) {
            container.style.backgroundImage = `linear-gradient(rgba(31, 41, 55, 0.8), rgba(31, 41, 55, 0.7)), url(${section.background_image})`;
        }
        
        // Update CTA buttons
        const primaryCta = container.querySelector('[data-cms="primary-cta"]');
        if (primaryCta && settings.primary_cta_text) {
            primaryCta.textContent = settings.primary_cta_text;
            if (settings.primary_cta_url) {
                primaryCta.href = settings.primary_cta_url;
            }
        }
        
        const secondaryCta = container.querySelector('[data-cms="secondary-cta"]');
        if (secondaryCta && settings.secondary_cta_text) {
            secondaryCta.textContent = settings.secondary_cta_text;
            if (settings.secondary_cta_url) {
                secondaryCta.href = settings.secondary_cta_url;
            }
        }
    }
    
    renderFeaturedServices(services) {
        const container = document.querySelector('[data-cms="featured-services"]');
        if (!container || !services || !services.length) return;
        
        container.innerHTML = '';
        
        services.forEach(service => {
            const serviceCard = document.createElement('div');
            serviceCard.className = 'service-card bg-white rounded-xl p-8 shadow-lg';
            serviceCard.innerHTML = `
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="${service.icon || 'fas fa-fire-extinguisher'} text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-semibold mb-4 text-center">${service.name}</h3>
                <p class="text-brand-gray mb-6 text-center">
                    ${service.short_description}
                </p>
                <div class="text-center">
                    <a href="${service.slug}.html" class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        ${this.translate('btn.learn_more')}
                    </a>
                </div>
            `;
            container.appendChild(serviceCard);
        });
    }
    
    renderTestimonials(testimonials) {
        const container = document.querySelector('[data-cms="testimonials"]');
        if (!container || !testimonials || !testimonials.length) return;
        
        container.innerHTML = '';
        
        testimonials.forEach(testimonial => {
            const testimonialCard = document.createElement('div');
            testimonialCard.className = 'testimonial-card bg-white rounded-xl p-8 shadow-lg';
            testimonialCard.innerHTML = `
                <div class="mb-6">
                    <div class="flex justify-center">
                        ${Array(testimonial.rating || 5).fill().map(() => 
                            '<i class="fas fa-star text-yellow-400 text-xl mx-1"></i>'
                        ).join('')}
                    </div>
                </div>
                <blockquote class="text-xl text-brand-gray italic mb-6">
                    "${testimonial.content}"
                </blockquote>
                <div class="flex items-center justify-center space-x-4">
                    ${testimonial.client_avatar ? 
                        `<div class="w-16 h-16 rounded-full overflow-hidden">
                            <img src="${testimonial.client_avatar}" alt="${testimonial.client_name}" class="w-full h-full object-cover">
                        </div>` :
                        `<div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-gray-600 text-2xl"></i>
                        </div>`
                    }
                    <div class="text-left">
                        <p class="font-semibold text-lg">${testimonial.client_name}</p>
                        <p class="text-brand-gray">${testimonial.client_position}${testimonial.client_company ? `, ${testimonial.client_company}` : ''}</p>
                    </div>
                </div>
            `;
            container.appendChild(testimonialCard);
        });
    }
    
    renderFeaturedProjects(projects) {
        const container = document.querySelector('[data-cms="featured-projects"]');
        if (!container || !projects || !projects.length) return;
        
        container.innerHTML = '';
        
        projects.forEach(project => {
            const projectCard = document.createElement('div');
            projectCard.className = 'project-card bg-white rounded-xl shadow-lg overflow-hidden';
            projectCard.innerHTML = `
                <div class="project-image relative">
                    <img src="${project.featured_image}" alt="${project.title}" class="w-full h-64 object-cover">
                    <div class="project-badge completion-badge">${this.translate(project.status.toUpperCase())}</div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2">${project.title}</h3>
                    <p class="text-brand-red font-semibold mb-2">${project.subtitle}</p>
                    <p class="text-brand-gray mb-4">
                        ${project.description}
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-brand-gray">
                            <i class="fas fa-calendar mr-1"></i> ${this.formatDate(project.project_date)}
                        </div>
                        <a href="project-${project.slug}.html" class="bg-brand-red text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            ${this.translate('btn.view_details')}
                        </a>
                    </div>
                </div>
            `;
            container.appendChild(projectCard);
        });
    }
    
    renderBlogCategories(categories) {
        const container = document.querySelector('[data-cms="blog-categories"]');
        if (!container || !categories || !categories.length) return;
        
        container.innerHTML = `
            <button class="filter-tag active bg-gray-200 text-brand-black px-4 py-2 rounded-full font-semibold" data-filter="all">
                ${this.translate('blog.all_articles')}
            </button>
        `;
        
        categories.forEach(category => {
            const categoryBtn = document.createElement('button');
            categoryBtn.className = 'filter-tag bg-gray-200 text-brand-black px-4 py-2 rounded-full font-semibold';
            categoryBtn.setAttribute('data-filter', category.slug);
            categoryBtn.textContent = category.name;
            container.appendChild(categoryBtn);
        });
        
        // Add event listeners
        container.querySelectorAll('.filter-tag').forEach(btn => {
            btn.addEventListener('click', () => {
                container.querySelectorAll('.filter-tag').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const filter = btn.getAttribute('data-filter');
                this.filterBlogPosts(filter);
            });
        });
    }
    
    renderBlogPosts(posts, meta) {
        const container = document.querySelector('[data-cms="blog-posts"]');
        if (!container || !posts || !posts.length) return;
        
        container.innerHTML = '';
        
        posts.forEach(post => {
            const postCard = document.createElement('div');
            postCard.className = 'blog-card bg-white rounded-xl shadow-lg overflow-hidden';
            postCard.setAttribute('data-category', post.category_slug);
            
            postCard.innerHTML = `
                <div class="relative">
                    <img src="${post.featured_image}" alt="${post.title}" class="w-full h-48 object-cover">
                    ${post.is_featured ? '<div class="trending-badge">TRENDING</div>' : ''}
                    ${this.isNewPost(post.published_at) ? '<div class="new-badge">NEW</div>' : ''}
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4 mb-3">
                        <span class="article-category">${post.category_name}</span>
                        <span class="reading-time">
                            <i class="fas fa-clock mr-1"></i> ${post.reading_time} ${this.translate('time.min_read')}
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-3">${post.title}</h3>
                    <p class="text-brand-gray mb-4 text-sm">
                        ${post.excerpt}
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-brand-gray">
                            <i class="fas fa-calendar mr-1"></i> ${this.formatDate(post.published_at)}
                        </div>
                        <a href="blog-article-${post.slug}.html" class="bg-brand-red text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                            ${this.translate('btn.read_more')}
                        </a>
                    </div>
                </div>
            `;
            
            container.appendChild(postCard);
        });
        
        // Add load more button if needed
        const loadMoreBtn = document.querySelector('[data-cms="load-more"]');
        if (loadMoreBtn) {
            if (meta.offset + meta.limit < meta.total) {
                loadMoreBtn.style.display = 'block';
                loadMoreBtn.setAttribute('data-offset', meta.offset + meta.limit);
            } else {
                loadMoreBtn.style.display = 'none';
            }
        }
    }
    
    renderFeaturedArticle(article) {
        const container = document.querySelector('[data-cms="featured-article"]');
        if (!container || !article) return;
        
        container.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <div class="flex items-center space-x-4 mb-4">
                        <span class="article-category">${article.category_name}</span>
                        <span class="reading-time">
                            <i class="fas fa-clock mr-1"></i> ${article.reading_time} ${this.translate('time.min_read')}
                        </span>
                    </div>
                    
                    <h3 class="text-3xl font-bold mb-4">
                        ${article.title}
                    </h3>
                    
                    <p class="text-brand-gray mb-6 text-lg leading-relaxed">
                        ${article.excerpt}
                    </p>
                    
                    <div class="flex items-center space-x-6 mb-6">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-download text-brand-red"></i>
                            <span class="text-sm text-brand-gray">${article.views_count} views</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-star text-yellow-400"></i>
                            <span class="text-sm text-brand-gray">4.9/5 rating</span>
                        </div>
                    </div>
                    
                    <a href="blog-article-${article.slug}.html" class="bg-brand-red text-white px-8 py-3 rounded-lg font-bold hover:bg-red-700 transition-colors cta-pulse">
                        🔴 ${this.translate('btn.read_full_article')}
                    </a>
                </div>
                
                <div class="relative">
                    <img src="${article.featured_image}" 
                         alt="${article.title}" 
                         class="w-full h-80 object-cover rounded-lg shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-black/50 to-transparent rounded-lg"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <p class="text-sm">${this.formatDate(article.published_at)}</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    renderRelatedPosts(posts) {
        const container = document.querySelector('[data-cms="related-articles"]');
        if (!container || !posts || !posts.length) return;
        
        container.innerHTML = '';
        
        posts.forEach(post => {
            const postCard = document.createElement('div');
            postCard.className = 'bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300';
            
            postCard.innerHTML = `
                <img src="${post.featured_image}" 
                     alt="${post.title}" 
                     class="w-full h-48 object-cover">
                <div class="p-6">
                    <div class="text-brand-red text-sm font-semibold mb-2">${post.category_name}</div>
                    <h3 class="text-lg font-bold mb-3">${post.title}</h3>
                    <p class="text-brand-gray text-sm mb-4">
                        ${post.excerpt}
                    </p>
                    <a href="blog-article-${post.slug}.html" class="text-brand-red font-semibold hover:underline">
                        ${this.translate('btn.read_more')} →
                    </a>
                </div>
            `;
            
            container.appendChild(postCard);
        });
    }
    
    renderLocationAdvantages(advantages) {
        const container = document.querySelector('[data-cms="location-advantages"]');
        if (!container || !advantages || !advantages.length) return;
        
        container.innerHTML = '';
        
        advantages.forEach((advantage, index) => {
            const advantageCard = document.createElement('div');
            advantageCard.className = 'advantage-card bg-gray-50 rounded-xl p-8 text-center shadow-lg';
            
            const data = JSON.parse(advantage.data || '{}');
            
            advantageCard.innerHTML = `
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="${data.icon || 'fas fa-check'} text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-4">${advantage.title}</h3>
                <p class="text-brand-gray mb-4">
                    ${advantage.description}
                </p>
                <div class="text-brand-red font-bold text-lg">${data.highlight || ''}</div>
            `;
            
            container.appendChild(advantageCard);
        });
    }
    
    renderLocationServices(services) {
        const container = document.querySelector('[data-cms="location-services"]');
        if (!container || !services || !services.length) return;
        
        container.innerHTML = '';
        
        services.forEach((service, index) => {
            const serviceCard = document.createElement('div');
            serviceCard.className = 'service-card bg-white rounded-xl p-8 shadow-lg';
            
            const data = JSON.parse(service.data || '{}');
            
            serviceCard.innerHTML = `
                <div class="w-16 h-16 bg-brand-red rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="${data.icon || 'fas fa-fire-extinguisher'} text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-semibold mb-4 text-center">${service.title}</h3>
                <p class="text-brand-gray mb-6 text-center">
                    ${service.description}
                </p>
                <div class="text-center">
                    <button class="bg-brand-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        ${this.translate('btn.request_service')}
                    </button>
                </div>
            `;
            
            container.appendChild(serviceCard);
        });
    }
    
    renderLocationTestimonials(testimonials) {
        const container = document.querySelector('[data-cms="location-testimonial"]');
        if (!container || !testimonials || !testimonials.length) return;
        
        // Use the first testimonial
        const testimonial = testimonials[0];
        
        container.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <blockquote class="text-2xl text-brand-gray italic mb-6">
                        "${testimonial.content}"
                    </blockquote>
                    
                    <div class="mb-6">
                        <p class="font-semibold text-lg">${testimonial.client_name}</p>
                        <p class="text-brand-gray">${testimonial.client_position}${testimonial.client_company ? `, ${testimonial.client_company}` : ''}</p>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-6 mb-8">
                        <div class="text-center">
                            <div class="stats-counter">7</div>
                            <div class="text-sm text-brand-gray">${this.translate('stats.days_to_complete')}</div>
                        </div>
                        <div class="text-center">
                            <div class="stats-counter">100%</div>
                            <div class="text-sm text-brand-gray">${this.translate('stats.first_try_pass')}</div>
                        </div>
                        <div class="text-center">
                            <div class="stats-counter">15%</div>
                            <div class="text-sm text-brand-gray">${this.translate('stats.cost_savings')}</div>
                        </div>
                    </div>
                    
                    <button class="bg-brand-red text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                        ${this.translate('btn.get_similar_results')} →
                    </button>
                </div>
                
                <div class="relative">
                    <img src="${testimonial.client_avatar || 'https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=800&h=600&fit=crop'}" 
                         alt="${testimonial.client_name}" 
                         class="w-full h-80 object-cover rounded-lg shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-black/50 to-transparent rounded-lg"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <p class="text-sm">${testimonial.client_company || ''}</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    renderContactForm(form) {
        const container = document.querySelector('[data-cms="contact-form"]');
        if (!container || !form) return;
        
        const fields = JSON.parse(form.fields || '[]');
        
        let formHtml = `
            <form id="contact-form-main" class="space-y-6" data-form-id="${form.form_key}">
        `;
        
        // Group fields into rows
        const rows = [];
        let currentRow = [];
        
        fields.forEach(field => {
            if (field.type === 'textarea' || field.fullWidth) {
                if (currentRow.length > 0) {
                    rows.push([...currentRow]);
                    currentRow = [];
                }
                rows.push([field]);
            } else {
                currentRow.push(field);
                if (currentRow.length === 2) {
                    rows.push([...currentRow]);
                    currentRow = [];
                }
            }
        });
        
        if (currentRow.length > 0) {
            rows.push([...currentRow]);
        }
        
        // Generate form HTML
        rows.forEach(row => {
            if (row.length === 1) {
                // Full width field
                const field = row[0];
                formHtml += this.renderFormField(field);
            } else {
                // Two column row
                formHtml += `<div class="grid grid-cols-1 md:grid-cols-2 gap-6">`;
                row.forEach(field => {
                    formHtml += `<div>${this.renderFormField(field)}</div>`;
                });
                formHtml += `</div>`;
            }
        });
        
        // Add submit button
        formHtml += `
            <button type="submit" 
                    class="w-full bg-brand-red text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-red-700 transition-colors">
                🔴 ${this.translate('contact.form.submit')}
            </button>
            
            <p class="text-xs text-brand-gray text-center">
                ${this.translate('contact.form.disclaimer')}
            </p>
        </form>
        
        <!-- Success Message -->
        <div id="success-message" class="success-message bg-green-50 border border-green-200 rounded-lg p-6 mt-6" style="display: none;">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-white"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-green-800">${this.translate('contact.form.success_title')}</h4>
                    <p class="text-green-700 text-sm">${form.success_message}</p>
                </div>
            </div>
        </div>
        `;
        
        container.innerHTML = formHtml;
    }
    
    renderFormField(field) {
        let html = '';
        
        switch (field.type) {
            case 'text':
            case 'email':
            case 'tel':
                html = `
                    <label for="${field.name}" class="block text-sm font-semibold text-brand-black mb-2">${field.label}${field.required ? ' *' : ''}</label>
                    <input type="${field.type}" id="${field.name}" name="${field.name}" ${field.required ? 'required' : ''} 
                           class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20"
                           placeholder="${field.placeholder || ''}">
                `;
                break;
                
            case 'textarea':
                html = `
                    <label for="${field.name}" class="block text-sm font-semibold text-brand-black mb-2">${field.label}${field.required ? ' *' : ''}</label>
                    <textarea id="${field.name}" name="${field.name}" rows="${field.rows || 4}" ${field.required ? 'required' : ''} 
                              class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20"
                              placeholder="${field.placeholder || ''}"></textarea>
                `;
                break;
                
            case 'select':
                html = `
                    <label for="${field.name}" class="block text-sm font-semibold text-brand-black mb-2">${field.label}${field.required ? ' *' : ''}</label>
                    <select id="${field.name}" name="${field.name}" ${field.required ? 'required' : ''} 
                            class="form-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-red focus:ring-2 focus:ring-brand-red/20">
                        <option value="">${field.placeholder || this.translate('form.select_option')}</option>
                        ${field.options.map(option => `<option value="${option}">${option}</option>`).join('')}
                    </select>
                `;
                break;
                
            case 'checkbox':
                html = `
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" id="${field.name}" name="${field.name}" class="w-4 h-4 text-brand-red border-gray-300 rounded focus:ring-brand-red">
                        <label for="${field.name}" class="text-sm text-brand-gray">${field.label}</label>
                    </div>
                `;
                break;
        }
        
        return html;
    }
    
    initContactForm() {
        const form = document.getElementById('contact-form-main');
        if (!form) return;
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = '🔄 ' + this.translate('status.sending');
            submitBtn.disabled = true;
            
            // Collect form data
            const formData = {};
            new FormData(form).forEach((value, key) => {
                formData[key] = value;
            });
            
            // Add form ID
            formData.form_id = form.getAttribute('data-form-id');
            
            try {
                // Submit form
                const response = await this.postData('/contact', formData);
                
                // Show success message
                document.getElementById('success-message').style.display = 'block';
                
                // Reset form
                form.reset();
                
                // Scroll to success message
                document.getElementById('success-message').scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Track form submission
                if (window.trackEvent) {
                    window.trackEvent('form_submit', {
                        form_id: formData.form_id,
                        page: window.location.pathname
                    });
                }
                
            } catch (error) {
                console.error('Form submission error:', error);
                alert(this.translate('contact.form.error'));
            } finally {
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }
    
    filterBlogPosts(category) {
        const posts = document.querySelectorAll('[data-cms="blog-posts"] .blog-card');
        
        posts.forEach(post => {
            if (category === 'all' || post.getAttribute('data-category') === category) {
                post.style.display = 'block';
            } else {
                post.style.display = 'none';
            }
        });
        
        // Track filter usage
        if (window.trackEvent) {
            window.trackEvent('blog_filter', {
                filter: category,
                page: 'blog'
            });
        }
    }
    
    initLightbox() {
        document.querySelectorAll('.gallery-item').forEach(item => {
            item.addEventListener('click', () => {
                const imageSrc = item.getAttribute('data-image');
                const imageAlt = item.querySelector('img').getAttribute('alt');
                
                const lightbox = document.getElementById('lightbox');
                const lightboxImg = document.getElementById('lightbox-img');
                
                if (lightbox && lightboxImg) {
                    lightboxImg.src = imageSrc;
                    lightboxImg.alt = imageAlt;
                    lightbox.classList.add('active');
                }
            });
        });
    }
    
    // ===== UTILITY METHODS =====
    
    async fetchData(endpoint, options = {}) {
        const url = `${this.apiBaseUrl}${endpoint}`;
        const cacheKey = url;
        
        // Check cache first
        if (this.cache[cacheKey] && this.cache[cacheKey].expiry > Date.now()) {
            return this.cache[cacheKey].data;
        }
        
        try {
            const response = await fetch(url, {
                method: options.method || 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    ...options.headers
                },
                body: options.body ? JSON.stringify(options.body) : undefined
            });
            
            if (!response.ok) {
                throw new Error(`API error: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Cache the response
            this.cache[cacheKey] = {
                data: data.data,
                expiry: Date.now() + this.cacheExpiry
            };
            
            return data.data;
            
        } catch (error) {
            console.error(`Error fetching ${url}:`, error);
            throw error;
        }
    }
    
    async postData(endpoint, data) {
        const url = `${this.apiBaseUrl}${endpoint}`;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                throw new Error(`API error: ${response.status}`);
            }
            
            return await response.json();
            
        } catch (error) {
            console.error(`Error posting to ${url}:`, error);
            throw error;
        }
    }
    
    translate(key, params = {}) {
        // Use language manager if available
        if (window.languageManager) {
            return window.languageManager.translate(key, params);
        }
        
        // Fallback translations
        const translations = {
            'en': {
                'btn.learn_more': 'Learn More',
                'btn.read_more': 'Read More',
                'btn.view_details': 'View Details',
                'btn.get_similar_results': 'Get Similar Results',
                'btn.request_service': 'Request This Service',
                'btn.read_full_article': 'Read Full Article',
                'blog.all_articles': 'All Articles',
                'contact.form.submit': 'Send Request',
                'contact.form.success_title': 'Request Submitted Successfully!',
                'contact.form.error': 'An error occurred. Please try again.',
                'contact.form.disclaimer': 'By submitting this form, you agree to be contacted regarding your fire safety requirements.',
                'form.select_option': 'Select an option',
                'status.sending': 'Sending Request...',
                'time.min_read': 'min read',
                'stats.days_to_complete': 'Days to Complete',
                'stats.first_try_pass': 'First-Try Pass',
                'stats.cost_savings': 'Cost Savings',
                'COMPLETED': 'COMPLETED',
                'ONGOING': 'ONGOING',
                'PLANNED': 'PLANNED'
            },
            'ar': {
                'btn.learn_more': 'اعرف المزيد',
                'btn.read_more': 'اقرأ المزيد',
                'btn.view_details': 'عرض التفاصيل',
                'btn.get_similar_results': 'احصل على نتائج مماثلة',
                'btn.request_service': 'اطلب هذه الخدمة',
                'btn.read_full_article': 'قراءة المقال كاملاً',
                'blog.all_articles': 'جميع المقالات',
                'contact.form.submit': 'إرسال الطلب',
                'contact.form.success_title': 'تم إرسال الطلب بنجاح!',
                'contact.form.error': 'حدث خطأ. يرجى المحاولة مرة أخرى.',
                'contact.form.disclaimer': 'بإرسال هذا النموذج، فإنك توافق على الاتصال بك بخصوص متطلبات السلامة من الحريق.',
                'form.select_option': 'اختر خيارًا',
                'status.sending': 'جاري إرسال الطلب...',
                'time.min_read': 'دقيقة قراءة',
                'stats.days_to_complete': 'أيام للإنجاز',
                'stats.first_try_pass': 'نجاح من أول محاولة',
                'stats.cost_savings': 'توفير التكاليف',
                'COMPLETED': 'مكتمل',
                'ONGOING': 'جاري',
                'PLANNED': 'مخطط'
            }
        };
        
        const langTranslations = translations[this.currentLanguage] || translations['en'];
        let translation = langTranslations[key] || key;
        
        // Replace parameters in translation
        Object.entries(params).forEach(([param, value]) => {
            translation = translation.replace(`{${param}}`, value);
        });
        
        return translation;
    }
    
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString(
            this.currentLanguage === 'ar' ? 'ar-EG' : 'en-US',
            { year: 'numeric', month: 'long', day: 'numeric' }
        );
    }
    
    isNewPost(dateString) {
        const postDate = new Date(dateString);
        const now = new Date();
        const diffDays = Math.floor((now - postDate) / (1000 * 60 * 60 * 24));
        return diffDays < 7; // Consider posts newer than 7 days as "new"
    }
}

// ===== Initialize CMS Integration =====
document.addEventListener('DOMContentLoaded', function() {
    window.cmsIntegration = new CMSIntegration();
    
    console.log('🔄 CMS Integration ready!');
});

// ===== Export for module systems =====
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CMSIntegration;
}