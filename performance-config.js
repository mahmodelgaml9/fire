// ===== Performance Configuration =====
const PerformanceConfig = {
    // Image optimization settings
    imageOptimization: {
        quality: 85,
        format: 'webp',
        fallback: 'jpeg',
        lazyLoadOffset: 100
    },
    
    // Cache settings
    cache: {
        staticAssets: '1y',
        images: '6M',
        css: '1M',
        js: '1M'
    },
    
    // Compression settings
    compression: {
        gzip: true,
        brotli: true,
        level: 6
    },
    
    // Critical resources
    critical: {
        css: [
            'tailwind-base',
            'fonts',
            'hero-styles'
        ],
        js: [
            'jquery',
            'core-functions'
        ]
    }
};

// ===== Lazy Loading Implementation =====
class LazyLoader {
    constructor() {
        this.imageObserver = null;
        this.sectionObserver = null;
        this.init();
    }
    
    init() {
        this.setupImageLazyLoading();
        this.setupSectionLazyLoading();
    }
    
    setupImageLazyLoading() {
        if ('IntersectionObserver' in window) {
            this.imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        this.loadImage(img);
                        this.imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: `${PerformanceConfig.imageOptimization.lazyLoadOffset}px`
            });
            
            // Observe all lazy images
            document.querySelectorAll('img[data-src]').forEach(img => {
                this.imageObserver.observe(img);
            });
        } else {
            // Fallback for older browsers
            this.loadAllImages();
        }
    }
    
    setupSectionLazyLoading() {
        if ('IntersectionObserver' in window) {
            this.sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const section = entry.target;
                        this.loadSection(section);
                        this.sectionObserver.unobserve(section);
                    }
                });
            }, {
                rootMargin: '50px'
            });
            
            // Observe all lazy sections
            document.querySelectorAll('.lazy-section').forEach(section => {
                this.sectionObserver.observe(section);
            });
        }
    }
    
    loadImage(img) {
        const src = img.getAttribute('data-src');
        const srcset = img.getAttribute('data-srcset');
        
        if (src) {
            img.src = src;
            img.removeAttribute('data-src');
        }
        
        if (srcset) {
            img.srcset = srcset;
            img.removeAttribute('data-srcset');
        }
        
        img.classList.add('loaded');
        
        // Track image loading
        this.trackImageLoad(src);
    }
    
    loadSection(section) {
        section.classList.add('loaded');
        
        // Initialize section-specific functionality
        const sectionType = section.getAttribute('data-section-type');
        if (sectionType) {
            this.initSectionType(sectionType, section);
        }
    }
    
    loadAllImages() {
        document.querySelectorAll('img[data-src]').forEach(img => {
            this.loadImage(img);
        });
    }
    
    initSectionType(type, section) {
        switch (type) {
            case 'testimonials':
                this.initTestimonials(section);
                break;
            case 'gallery':
                this.initGallery(section);
                break;
            case 'stats':
                this.initStatsCounter(section);
                break;
        }
    }
    
    initTestimonials(section) {
        // Initialize testimonial carousel only when visible
        console.log('Initializing testimonials section');
    }
    
    initGallery(section) {
        // Initialize gallery lightbox only when visible
        console.log('Initializing gallery section');
    }
    
    initStatsCounter(section) {
        // Initialize counter animation only when visible
        console.log('Initializing stats counter');
    }
    
    trackImageLoad(src) {
        if (window.trackEvent) {
            window.trackEvent('image_lazy_load', {
                image_src: src,
                timestamp: Date.now()
            });
        }
    }
}

// ===== Resource Preloader =====
class ResourcePreloader {
    constructor() {
        this.preloadQueue = [];
        this.init();
    }
    
    init() {
        this.preloadCriticalResources();
        this.setupPrefetch();
    }
    
    preloadCriticalResources() {
        // Preload critical fonts
        this.preloadFont('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        // Preload hero images
        this.preloadImage('https://images.pexels.com/photos/2219024/pexels-photo-2219024.jpeg?auto=compress&cs=tinysrgb&w=1920&h=1080&fit=crop');
        
        // Preload critical CSS
        this.preloadCSS('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
    }
    
    preloadFont(href) {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'style';
        link.href = href;
        link.onload = () => {
            link.rel = 'stylesheet';
        };
        document.head.appendChild(link);
    }
    
    preloadImage(src) {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'image';
        link.href = src;
        document.head.appendChild(link);
    }
    
    preloadCSS(href) {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'style';
        link.href = href;
        document.head.appendChild(link);
    }
    
    setupPrefetch() {
        // Prefetch likely next pages
        this.prefetchPage('services.html');
        this.prefetchPage('contact.html');
    }
    
    prefetchPage(href) {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = href;
        document.head.appendChild(link);
    }
}

// ===== Performance Monitor =====
class PerformanceMonitor {
    constructor() {
        this.metrics = {};
        this.init();
    }
    
    init() {
        this.measurePageLoad();
        this.measureResourceTiming();
        this.setupPerformanceObserver();
    }
    
    measurePageLoad() {
        window.addEventListener('load', () => {
            const navigation = performance.getEntriesByType('navigation')[0];
            
            this.metrics.pageLoad = {
                domContentLoaded: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart,
                loadComplete: navigation.loadEventEnd - navigation.loadEventStart,
                totalTime: navigation.loadEventEnd - navigation.fetchStart
            };
            
            this.reportMetrics();
        });
    }
    
    measureResourceTiming() {
        const resources = performance.getEntriesByType('resource');
        
        this.metrics.resources = resources.map(resource => ({
            name: resource.name,
            duration: resource.duration,
            size: resource.transferSize,
            type: this.getResourceType(resource.name)
        }));
    }
    
    setupPerformanceObserver() {
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                list.getEntries().forEach(entry => {
                    if (entry.entryType === 'largest-contentful-paint') {
                        this.metrics.lcp = entry.startTime;
                    }
                    
                    if (entry.entryType === 'first-input') {
                        this.metrics.fid = entry.processingStart - entry.startTime;
                    }
                    
                    if (entry.entryType === 'layout-shift') {
                        this.metrics.cls = (this.metrics.cls || 0) + entry.value;
                    }
                });
            });
            
            observer.observe({ entryTypes: ['largest-contentful-paint', 'first-input', 'layout-shift'] });
        }
    }
    
    getResourceType(url) {
        if (url.includes('.css')) return 'css';
        if (url.includes('.js')) return 'js';
        if (url.match(/\.(jpg|jpeg|png|gif|webp)$/)) return 'image';
        if (url.includes('font')) return 'font';
        return 'other';
    }
    
    reportMetrics() {
        console.log('Performance Metrics:', this.metrics);
        
        // Send to analytics in real implementation
        if (window.trackEvent) {
            window.trackEvent('performance_metrics', this.metrics);
        }
    }
    
    getScore() {
        const scores = {
            lcp: this.metrics.lcp < 2500 ? 100 : this.metrics.lcp < 4000 ? 50 : 0,
            fid: this.metrics.fid < 100 ? 100 : this.metrics.fid < 300 ? 50 : 0,
            cls: this.metrics.cls < 0.1 ? 100 : this.metrics.cls < 0.25 ? 50 : 0
        };
        
        return (scores.lcp + scores.fid + scores.cls) / 3;
    }
}

// ===== Cache Manager =====
class CacheManager {
    constructor() {
        this.cacheName = 'sphinx-fire-v1';
        this.init();
    }
    
    init() {
        if ('serviceWorker' in navigator) {
            this.registerServiceWorker();
        }
        
        this.setupLocalStorage();
    }
    
    registerServiceWorker() {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('Service Worker registered:', registration);
            })
            .catch(error => {
                console.log('Service Worker registration failed:', error);
            });
    }
    
    setupLocalStorage() {
        // Cache frequently accessed data
        this.cacheData('company-info', {
            name: 'Sphinx Fire',
            phone: '+20 123 456 7890',
            email: 'info@sphinxfire.com',
            location: 'Sadat City, Egypt'
        });
    }
    
    cacheData(key, data, expiry = 24 * 60 * 60 * 1000) { // 24 hours default
        const item = {
            data: data,
            timestamp: Date.now(),
            expiry: expiry
        };
        
        localStorage.setItem(key, JSON.stringify(item));
    }
    
    getCachedData(key) {
        const item = localStorage.getItem(key);
        
        if (!item) return null;
        
        const parsed = JSON.parse(item);
        
        if (Date.now() - parsed.timestamp > parsed.expiry) {
            localStorage.removeItem(key);
            return null;
        }
        
        return parsed.data;
    }
}

// ===== Global Performance Utilities =====
window.PerformanceUtils = {
    // Debounce function for scroll events
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Throttle function for resize events
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },
    
    // Optimize images
    optimizeImage: function(img) {
        // Add loading="lazy" if not present
        if (!img.hasAttribute('loading')) {
            img.setAttribute('loading', 'lazy');
        }
        
        // Add proper alt text if missing
        if (!img.hasAttribute('alt')) {
            img.setAttribute('alt', 'Sphinx Fire - Fire Safety Solutions');
        }
    },
    
    // Preload next page
    preloadNextPage: function(href) {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = href;
        document.head.appendChild(link);
    }
};

// ===== Initialize Performance Optimizations =====
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all performance modules
    window.lazyLoader = new LazyLoader();
    window.resourcePreloader = new ResourcePreloader();
    window.performanceMonitor = new PerformanceMonitor();
    window.cacheManager = new CacheManager();
    
    console.log('🚀 Performance optimizations initialized!');
});

// ===== Export for module systems =====
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        LazyLoader,
        ResourcePreloader,
        PerformanceMonitor,
        CacheManager,
        PerformanceConfig
    };
}