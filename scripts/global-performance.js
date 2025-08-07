// ===== Global Performance Enhancements =====

// ===== Unified Analytics System =====
window.trackEvent = function(eventName, data = {}) {
    // Enhanced analytics with performance data
    const eventData = {
        ...data,
        timestamp: Date.now(),
        page: window.location.pathname,
        user_agent: navigator.userAgent,
        viewport: {
            width: window.innerWidth,
            height: window.innerHeight
        },
        connection: navigator.connection ? {
            effectiveType: navigator.connection.effectiveType,
            downlink: navigator.connection.downlink
        } : null
    };
    
    console.log('📊 Analytics Event:', eventName, eventData);
    
    // Store offline if needed
    if (!navigator.onLine) {
        storeOfflineEvent(eventName, eventData);
    }
    
    // In real implementation, send to analytics platform
    // gtag('event', eventName, eventData);
    // fbq('track', eventName, eventData);
};

// ===== Offline Event Storage =====
function storeOfflineEvent(eventName, data) {
    const offlineEvents = JSON.parse(localStorage.getItem('offline_events') || '[]');
    offlineEvents.push({ eventName, data });
    localStorage.setItem('offline_events', JSON.stringify(offlineEvents));
}

// ===== Sync Offline Events =====
function syncOfflineEvents() {
    const offlineEvents = JSON.parse(localStorage.getItem('offline_events') || '[]');
    
    if (offlineEvents.length > 0) {
        console.log('📤 Syncing offline events:', offlineEvents.length);
        
        // Send events to analytics
        offlineEvents.forEach(({ eventName, data }) => {
            window.trackEvent(eventName, { ...data, offline_sync: true });
        });
        
        // Clear offline storage
        localStorage.removeItem('offline_events');
    }
}

// ===== Connection Status Monitoring =====
window.addEventListener('online', () => {
    console.log('🟢 Connection restored');
    syncOfflineEvents();
    window.trackEvent('connection_restored');
});

window.addEventListener('offline', () => {
    console.log('🔴 Connection lost');
    window.trackEvent('connection_lost');
});

// ===== Optimized Scroll Handler =====
const optimizedScrollHandler = window.PerformanceUtils?.debounce(function() {
    // Update reading progress
    const scrollPercent = Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);
    
    // Update progress bar if exists
    const progressBar = document.querySelector('.reading-progress');
    if (progressBar) {
        progressBar.style.width = Math.min(scrollPercent, 100) + '%';
    }
    
    // Trigger lazy loading check
    if (window.lazyLoader) {
        window.lazyLoader.checkVisibility();
    }
}, 16); // ~60fps

window.addEventListener('scroll', optimizedScrollHandler, { passive: true });

// ===== Optimized Resize Handler =====
const optimizedResizeHandler = window.PerformanceUtils?.throttle(function() {
    // Update viewport dimensions
    window.trackEvent('viewport_change', {
        width: window.innerWidth,
        height: window.innerHeight
    });
    
    // Recalculate layouts if needed
    if (window.layoutManager) {
        window.layoutManager.recalculate();
    }
}, 250);

window.addEventListener('resize', optimizedResizeHandler, { passive: true });

// ===== Critical CSS Loader =====
function loadCriticalCSS() {
    const criticalStyles = `
        /* Critical above-the-fold styles */
        .hero-bg { background-size: cover; background-position: center; }
        .animate-fade-in { opacity: 0; animation: fadeIn 0.8s ease-out forwards; }
        .sticky-header { position: fixed; top: 0; width: 100%; z-index: 50; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        
        /* Loading states */
        .loading { opacity: 0.5; pointer-events: none; }
        .loaded { opacity: 1; transition: opacity 0.3s ease; }
        
        /* Performance optimizations */
        img { will-change: transform; }
        .parallax { will-change: transform; }
        .animate { will-change: transform, opacity; }
    `;
    
    const style = document.createElement('style');
    style.textContent = criticalStyles;
    document.head.appendChild(style);
}

// ===== Font Loading Optimization =====
function optimizeFontLoading() {
    // Preload critical fonts
    const fontPreload = document.createElement('link');
    fontPreload.rel = 'preload';
    fontPreload.as = 'font';
    fontPreload.type = 'font/woff2';
    fontPreload.href = 'https://fonts.gstatic.com/s/inter/v12/UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuLyfAZ9hiA.woff2';
    fontPreload.crossOrigin = 'anonymous';
    document.head.appendChild(fontPreload);
    
    // Font display optimization
    if ('fonts' in document) {
        document.fonts.ready.then(() => {
            console.log('✅ Fonts loaded');
            document.body.classList.add('fonts-loaded');
        });
    }
}

// ===== Image Optimization =====
function optimizeImages() {
    // Convert all images to use lazy loading
    document.querySelectorAll('img:not([data-src])').forEach(img => {
        if (!img.hasAttribute('loading')) {
            img.setAttribute('loading', 'lazy');
        }
        
        // Add proper dimensions to prevent layout shift
        if (!img.hasAttribute('width') && !img.hasAttribute('height')) {
            img.style.aspectRatio = '16/9'; // Default aspect ratio
        }
        
        // Optimize for different screen sizes
        if (!img.hasAttribute('srcset') && img.src.includes('pexels.com')) {
            const baseSrc = img.src.split('?')[0];
            img.srcset = `
                ${baseSrc}?auto=compress&cs=tinysrgb&w=400 400w,
                ${baseSrc}?auto=compress&cs=tinysrgb&w=800 800w,
                ${baseSrc}?auto=compress&cs=tinysrgb&w=1200 1200w
            `;
            img.sizes = '(max-width: 768px) 400px, (max-width: 1200px) 800px, 1200px';
        }
    });
}

// ===== Code Splitting and Module Loading =====
const ModuleLoader = {
    loadedModules: new Set(),
    
    async loadModule(moduleName) {
        if (this.loadedModules.has(moduleName)) {
            return;
        }
        
        try {
            switch (moduleName) {
                case 'lightbox':
                    await this.loadLightbox();
                    break;
                case 'carousel':
                    await this.loadCarousel();
                    break;
                case 'forms':
                    await this.loadForms();
                    break;
                default:
                    console.warn('Unknown module:', moduleName);
            }
            
            this.loadedModules.add(moduleName);
            console.log(`✅ Module loaded: ${moduleName}`);
        } catch (error) {
            console.error(`❌ Failed to load module: ${moduleName}`, error);
        }
    },
    
    async loadLightbox() {
        // Load lightbox functionality only when needed
        window.openLightbox = function(src, alt) {
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            
            if (lightbox && img) {
                img.src = src;
                img.alt = alt;
                lightbox.classList.add('active');
                
                window.trackEvent('lightbox_open', { image: alt });
            }
        };
        
        window.closeLightbox = function() {
            const lightbox = document.getElementById('lightbox');
            if (lightbox) {
                lightbox.classList.remove('active');
            }
        };
    },
    
    async loadCarousel() {
        // Load carousel functionality only when needed
        window.CarouselManager = {
            currentSlide: 0,
            totalSlides: 0,
            
            init(selector) {
                const slides = document.querySelectorAll(`${selector} .slide`);
                this.totalSlides = slides.length;
                this.showSlide(0);
            },
            
            showSlide(index) {
                const slides = document.querySelectorAll('.slide');
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === index);
                });
                this.currentSlide = index;
            },
            
            next() {
                const nextIndex = (this.currentSlide + 1) % this.totalSlides;
                this.showSlide(nextIndex);
            },
            
            prev() {
                const prevIndex = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                this.showSlide(prevIndex);
            }
        };
    },
    
    async loadForms() {
        // Load form validation and submission only when needed
        window.FormManager = {
            validateEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            },
            
            validatePhone(phone) {
                return /^\+20\d{10}$/.test(phone.replace(/\s/g, ''));
            },
            
            submitForm(formData, endpoint) {
                return fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
            }
        };
    }
};

// ===== Performance Monitoring =====
const PerformanceTracker = {
    metrics: {},
    
    init() {
        this.trackCoreWebVitals();
        this.trackResourceTiming();
        this.trackUserInteractions();
    },
    
    trackCoreWebVitals() {
        // Track Largest Contentful Paint (LCP)
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            const lastEntry = entries[entries.length - 1];
            this.metrics.lcp = lastEntry.startTime;
            console.log('📊 LCP:', lastEntry.startTime);
        }).observe({ entryTypes: ['largest-contentful-paint'] });
        
        // Track First Input Delay (FID)
        new PerformanceObserver((entryList) => {
            const firstInput = entryList.getEntries()[0];
            this.metrics.fid = firstInput.processingStart - firstInput.startTime;
            console.log('📊 FID:', this.metrics.fid);
        }).observe({ entryTypes: ['first-input'] });
        
        // Track Cumulative Layout Shift (CLS)
        let clsValue = 0;
        new PerformanceObserver((entryList) => {
            for (const entry of entryList.getEntries()) {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            }
            this.metrics.cls = clsValue;
            console.log('📊 CLS:', clsValue);
        }).observe({ entryTypes: ['layout-shift'] });
    },
    
    trackResourceTiming() {
        window.addEventListener('load', () => {
            const resources = performance.getEntriesByType('resource');
            const slowResources = resources.filter(r => r.duration > 1000);
            
            if (slowResources.length > 0) {
                console.warn('⚠️ Slow resources detected:', slowResources);
                window.trackEvent('slow_resources', {
                    count: slowResources.length,
                    resources: slowResources.map(r => ({ name: r.name, duration: r.duration }))
                });
            }
        });
    },
    
    trackUserInteractions() {
        let interactionCount = 0;
        
        ['click', 'scroll', 'keydown'].forEach(eventType => {
            document.addEventListener(eventType, () => {
                interactionCount++;
            }, { passive: true, once: false });
        });
        
        // Report interaction count periodically
        setInterval(() => {
            if (interactionCount > 0) {
                window.trackEvent('user_interactions', {
                    count: interactionCount,
                    timeframe: '30s'
                });
                interactionCount = 0;
            }
        }, 30000);
    }
};

// ===== Initialize Global Performance =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing global performance optimizations...');
    
    // Load critical CSS immediately
    loadCriticalCSS();
    
    // Optimize font loading
    optimizeFontLoading();
    
    // Optimize images
    optimizeImages();
    
    // Initialize performance tracking
    if (window.PerformanceObserver) {
        PerformanceTracker.init();
    }
    
    // Sync offline events if online
    if (navigator.onLine) {
        syncOfflineEvents();
    }
    
    console.log('✅ Global performance optimizations ready!');
});

// ===== Page Visibility API =====
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        window.trackEvent('page_hidden');
        // Pause non-essential animations
        document.body.classList.add('page-hidden');
    } else {
        window.trackEvent('page_visible');
        // Resume animations
        document.body.classList.remove('page-hidden');
    }
});

// ===== Memory Management =====
window.addEventListener('beforeunload', function() {
    // Clean up event listeners and intervals
    if (window.lazyLoader) {
        window.lazyLoader.cleanup();
    }
    
    // Clear large objects from memory
    window.performanceMonitor = null;
    window.resourcePreloader = null;
});

// ===== Export for global use =====
window.ModuleLoader = ModuleLoader;
window.PerformanceTracker = PerformanceTracker;