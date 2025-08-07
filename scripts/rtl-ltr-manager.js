// ===== RTL/LTR Language Manager =====
// Handles Arabic/English language switching with proper RTL support

class LanguageManager {
    constructor() {
        this.currentLanguage = 'en';
        this.supportedLanguages = {
            'en': {
                name: 'English',
                direction: 'ltr',
                flag: '🇺🇸',
                locale: 'en-US'
            },
            'ar': {
                name: 'العربية',
                direction: 'rtl',
                flag: '🇪🇬',
                locale: 'ar-EG'
            }
        };
        this.translations = {};
        this.init();
    }
    
    init() {
        // Detect language from URL, localStorage, or browser
        this.detectLanguage();
        
        // Load translations
        this.loadTranslations();
        
        // Apply language settings
        this.applyLanguage();
        
        // Setup language switcher
        this.setupLanguageSwitcher();
        
        console.log(`🌐 Language Manager initialized: ${this.currentLanguage}`);
    }
    
    detectLanguage() {
        // 1. Check site_lang cookie first
        const cookieLang = document.cookie.match(/(?:^|; )site_lang=([^;]+)/);
        if (cookieLang && this.supportedLanguages[cookieLang[1]]) {
            this.currentLanguage = cookieLang[1];
            return;
        }
        // Check URL parameter first
        const urlParams = new URLSearchParams(window.location.search);
        const urlLang = urlParams.get('lang');
        if (urlLang && this.supportedLanguages[urlLang]) {
            this.currentLanguage = urlLang;
            return;
        }
        // Check localStorage
        const storedLang = localStorage.getItem('preferred_language');
        if (storedLang && this.supportedLanguages[storedLang]) {
            this.currentLanguage = storedLang;
            return;
        }
        // Check browser language
        const browserLang = navigator.language.split('-')[0];
        if (this.supportedLanguages[browserLang]) {
            this.currentLanguage = browserLang;
            return;
        }
        // Default to English
        this.currentLanguage = 'en';
    }
    
    async loadTranslations() {
        try {
            // In a real implementation, these would be loaded from the database
            // For now, we'll use embedded translations
            this.translations = {
                'en': {
                    // Navigation
                    'nav.home': 'Home',
                    'nav.services': 'Services',
                    'nav.about': 'About',
                    'nav.projects': 'Projects',
                    'nav.blog': 'Blog',
                    'nav.contact': 'Contact',
                    
                    // Common buttons
                    'btn.request_visit': 'Request Site Visit',
                    'btn.get_quote': 'Get Quote',
                    'btn.learn_more': 'Learn More',
                    'btn.contact_us': 'Contact Us',
                    'btn.download': 'Download',
                    'btn.read_more': 'Read More',
                    'btn.view_all': 'View All',
                    'btn.back': 'Back',
                    'btn.next': 'Next',
                    'btn.previous': 'Previous',
                    'btn.submit': 'Submit',
                    'btn.send': 'Send',
                    'btn.cancel': 'Cancel',
                    'btn.close': 'Close',
                    
                    // Hero section
                    'hero.title': 'Fire Protection Systems',
                    'hero.subtitle': 'Complete fire safety solutions for industrial facilities',
                    'hero.description': 'Professional fire protection systems with UL/FM certified equipment and expert installation.',
                    
                    // Services
                    'services.title': 'Our Services',
                    'services.firefighting': 'Firefighting Systems',
                    'services.alarms': 'Fire Alarm Systems',
                    'services.extinguishers': 'Fire Extinguishers',
                    'services.ppe': 'PPE Equipment',
                    'services.consulting': 'Safety Consulting',
                    'services.maintenance': 'Maintenance Services',
                    
                    // Contact
                    'contact.title': 'Contact Us',
                    'contact.address': 'Address',
                    'contact.phone': 'Phone',
                    'contact.email': 'Email',
                    'contact.hours': 'Business Hours',
                    'contact.form.name': 'Full Name',
                    'contact.form.email': 'Email Address',
                    'contact.form.phone': 'Phone Number',
                    'contact.form.company': 'Company Name',
                    'contact.form.message': 'Message',
                    'contact.form.submit': 'Send Message',
                    'contact.form.success': 'Thank you! We will contact you within 24 hours.',
                    
                    // Footer
                    'footer.company_info': 'Your trusted partner for comprehensive fire safety solutions in Egypt.',
                    'footer.quick_links': 'Quick Links',
                    'footer.services': 'Services',
                    'footer.contact_info': 'Contact Information',
                    'footer.follow_us': 'Follow Us',
                    'footer.copyright': '© 2024 Sphinx Fire. All rights reserved.',
                    
                    // Time and dates
                    'time.minutes': 'minutes',
                    'time.hours': 'hours',
                    'time.days': 'days',
                    'time.weeks': 'weeks',
                    'time.months': 'months',
                    'time.years': 'years',
                    'time.ago': 'ago',
                    'time.min_read': 'min read',
                    
                    // Status messages
                    'status.loading': 'Loading...',
                    'status.error': 'An error occurred',
                    'status.success': 'Success!',
                    'status.no_results': 'No results found',
                    'status.try_again': 'Please try again',
                    
                    // Location specific
                    'location.sadat_city': 'Sadat Industrial City',
                    'location.october_city': '6th of October City',
                    'location.quesna': 'Quesna Industrial Zone',
                    'location.badr_city': 'Badr City',
                    'location.ramadan_city': '10th of Ramadan City'
                },
                'ar': {
                    // Navigation
                    'nav.home': 'الرئيسية',
                    'nav.services': 'الخدمات',
                    'nav.about': 'من نحن',
                    'nav.projects': 'المشاريع',
                    'nav.blog': 'المدونة',
                    'nav.contact': 'اتصل بنا',
                    
                    // Common buttons
                    'btn.request_visit': 'طلب زيارة موقع',
                    'btn.get_quote': 'احصل على عرض سعر',
                    'btn.learn_more': 'اعرف المزيد',
                    'btn.contact_us': 'اتصل بنا',
                    'btn.download': 'تحميل',
                    'btn.read_more': 'اقرأ المزيد',
                    'btn.view_all': 'عرض الكل',
                    'btn.back': 'رجوع',
                    'btn.next': 'التالي',
                    'btn.previous': 'السابق',
                    'btn.submit': 'إرسال',
                    'btn.send': 'إرسال',
                    'btn.cancel': 'إلغاء',
                    'btn.close': 'إغلاق',
                    
                    // Hero section
                    'hero.title': 'أنظمة الحماية من الحريق',
                    'hero.subtitle': 'حلول شاملة للسلامة من الحريق للمنشآت الصناعية',
                    'hero.description': 'أنظمة حماية من الحريق احترافية بمعدات معتمدة UL/FM وتركيب خبير.',
                    
                    // Services
                    'services.title': 'خدماتنا',
                    'services.firefighting': 'أنظمة مكافحة الحريق',
                    'services.alarms': 'أنظمة إنذار الحريق',
                    'services.extinguishers': 'طفايات الحريق',
                    'services.ppe': 'معدات الحماية الشخصية',
                    'services.consulting': 'الاستشارات الأمنية',
                    'services.maintenance': 'خدمات الصيانة',
                    
                    // Contact
                    'contact.title': 'اتصل بنا',
                    'contact.address': 'العنوان',
                    'contact.phone': 'الهاتف',
                    'contact.email': 'البريد الإلكتروني',
                    'contact.hours': 'ساعات العمل',
                    'contact.form.name': 'الاسم الكامل',
                    'contact.form.email': 'البريد الإلكتروني',
                    'contact.form.phone': 'رقم الهاتف',
                    'contact.form.company': 'اسم الشركة',
                    'contact.form.message': 'الرسالة',
                    'contact.form.submit': 'إرسال الرسالة',
                    'contact.form.success': 'شكراً لك! سنتواصل معك خلال 24 ساعة.',
                    
                    // Footer
                    'footer.company_info': 'شريكك الموثوق لحلول السلامة من الحريق الشاملة في مصر.',
                    'footer.quick_links': 'روابط سريعة',
                    'footer.services': 'الخدمات',
                    'footer.contact_info': 'معلومات الاتصال',
                    'footer.follow_us': 'تابعنا',
                    'footer.copyright': '© 2024 سفينكس فاير. جميع الحقوق محفوظة.',
                    
                    // Time and dates
                    'time.minutes': 'دقائق',
                    'time.hours': 'ساعات',
                    'time.days': 'أيام',
                    'time.weeks': 'أسابيع',
                    'time.months': 'أشهر',
                    'time.years': 'سنوات',
                    'time.ago': 'منذ',
                    'time.min_read': 'دقيقة قراءة',
                    
                    // Status messages
                    'status.loading': 'جاري التحميل...',
                    'status.error': 'حدث خطأ',
                    'status.success': 'تم بنجاح!',
                    'status.no_results': 'لا توجد نتائج',
                    'status.try_again': 'يرجى المحاولة مرة أخرى',
                    
                    // Location specific
                    'location.sadat_city': 'مدينة السادات الصناعية',
                    'location.october_city': 'مدينة السادس من أكتوبر',
                    'location.quesna': 'المنطقة الصناعية بقويسنا',
                    'location.badr_city': 'مدينة بدر',
                    'location.ramadan_city': 'مدينة العاشر من رمضان'
                }
            };
            
            console.log(`📚 Translations loaded for ${this.currentLanguage}`);
        } catch (error) {
            console.error('❌ Failed to load translations:', error);
        }
    }
    
    applyLanguage() {
        const langConfig = this.supportedLanguages[this.currentLanguage];
        
        // Set document attributes
        document.documentElement.lang = this.currentLanguage;
        document.documentElement.dir = langConfig.direction;
        
        // Add language class to body
        document.body.className = document.body.className.replace(/\blang-\w+\b/g, '');
        document.body.classList.add(`lang-${this.currentLanguage}`);
        
        // Add direction class
        document.body.className = document.body.className.replace(/\bdir-\w+\b/g, '');
        document.body.classList.add(`dir-${langConfig.direction}`);
        
        // Update page title if translation exists
        const titleKey = 'page.title.' + this.getPageKey();
        const translatedTitle = this.translate(titleKey);
        if (translatedTitle !== titleKey) {
            document.title = translatedTitle;
        }
        
        // Apply translations to elements
        this.translateElements();
        
        // Apply RTL/LTR specific styles
        this.applyDirectionStyles();
        
        // Store preference
        localStorage.setItem('preferred_language', this.currentLanguage);
        
        console.log(`🌍 Language applied: ${this.currentLanguage} (${langConfig.direction})`);
    }
    
    translateElements() {
        // Translate elements with data-translate attribute
        document.querySelectorAll('[data-translate]').forEach(element => {
            const key = element.getAttribute('data-translate');
            const translation = this.translate(key);
            
            if (element.tagName === 'INPUT' && element.type === 'submit') {
                element.value = translation;
            } else if (element.hasAttribute('placeholder')) {
                element.placeholder = translation;
            } else {
                element.textContent = translation;
            }
        });
        
        // Translate elements with data-translate-html attribute (for HTML content)
        document.querySelectorAll('[data-translate-html]').forEach(element => {
            const key = element.getAttribute('data-translate-html');
            const translation = this.translate(key);
            element.innerHTML = translation;
        });
        
        // Translate alt attributes
        document.querySelectorAll('[data-translate-alt]').forEach(element => {
            const key = element.getAttribute('data-translate-alt');
            const translation = this.translate(key);
            element.alt = translation;
        });
        
        // Translate title attributes
        document.querySelectorAll('[data-translate-title]').forEach(element => {
            const key = element.getAttribute('data-translate-title');
            const translation = this.translate(key);
            element.title = translation;
        });
    }
    
    applyDirectionStyles() {
        const isRTL = this.supportedLanguages[this.currentLanguage].direction === 'rtl';
        
        // Update CSS custom properties for direction-aware styling
        document.documentElement.style.setProperty('--text-align-start', isRTL ? 'right' : 'left');
        document.documentElement.style.setProperty('--text-align-end', isRTL ? 'left' : 'right');
        document.documentElement.style.setProperty('--margin-start', isRTL ? 'margin-right' : 'margin-left');
        document.documentElement.style.setProperty('--margin-end', isRTL ? 'margin-left' : 'margin-right');
        document.documentElement.style.setProperty('--padding-start', isRTL ? 'padding-right' : 'padding-left');
        document.documentElement.style.setProperty('--padding-end', isRTL ? 'padding-left' : 'padding-right');
        document.documentElement.style.setProperty('--border-start', isRTL ? 'border-right' : 'border-left');
        document.documentElement.style.setProperty('--border-end', isRTL ? 'border-left' : 'border-right');
        document.documentElement.style.setProperty('--transform-x', isRTL ? 'scaleX(-1)' : 'scaleX(1)');
        
        // Update Tailwind direction classes
        if (isRTL) {
            document.body.classList.add('rtl');
            document.body.classList.remove('ltr');
        } else {
            document.body.classList.add('ltr');
            document.body.classList.remove('rtl');
        }
        
        // Update navigation arrows and icons
        document.querySelectorAll('.direction-aware').forEach(element => {
            if (isRTL) {
                element.style.transform = 'scaleX(-1)';
            } else {
                element.style.transform = 'scaleX(1)';
            }
        });
        
        // Update carousel and slider directions
        this.updateCarouselDirection();
        
        // Update form layouts
        this.updateFormLayouts();
    }
    
    updateCarouselDirection() {
        const isRTL = this.supportedLanguages[this.currentLanguage].direction === 'rtl';
        
        // Update carousel controls
        document.querySelectorAll('.carousel-prev, .carousel-next').forEach(button => {
            const isPrev = button.classList.contains('carousel-prev');
            const icon = button.querySelector('i');
            
            if (icon) {
                if (isRTL) {
                    // In RTL, previous should point right, next should point left
                    icon.className = isPrev ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
                } else {
                    // In LTR, previous should point left, next should point right
                    icon.className = isPrev ? 'fas fa-chevron-left' : 'fas fa-chevron-right';
                }
            }
        });
        
        // Update swipe directions for touch events
        if (window.heroCarousel) {
            window.heroCarousel.updateDirection(isRTL);
        }
    }
    
    updateFormLayouts() {
        const isRTL = this.supportedLanguages[this.currentLanguage].direction === 'rtl';
        
        // Update form field alignments
        document.querySelectorAll('input, textarea, select').forEach(field => {
            if (isRTL) {
                field.style.textAlign = 'right';
            } else {
                field.style.textAlign = 'left';
            }
        });
        
        // Update checkbox and radio button layouts
        document.querySelectorAll('.form-check').forEach(check => {
            if (isRTL) {
                check.style.flexDirection = 'row-reverse';
            } else {
                check.style.flexDirection = 'row';
            }
        });
    }
    
    setupLanguageSwitcher() {
        // Create language switcher if it doesn't exist
        let switcher = document.getElementById('language-switcher');
        if (!switcher) {
            switcher = this.createLanguageSwitcher();
        }
        
        // Update switcher content
        this.updateLanguageSwitcher(switcher);
        
        // Add event listeners
        switcher.addEventListener('click', (e) => {
            if (e.target.classList.contains('lang-option')) {
                const newLang = e.target.getAttribute('data-lang');
                this.switchLanguage(newLang);
            }
        });
    }
    
    createLanguageSwitcher() {
        const switcher = document.createElement('div');
        switcher.id = 'language-switcher';
        switcher.className = 'language-switcher relative';
        
        // Add to header or create floating switcher
        const header = document.querySelector('header nav');
        if (header) {
            header.appendChild(switcher);
        } else {
            // Create floating switcher
            switcher.className += ' fixed top-4 right-4 z-50';
            document.body.appendChild(switcher);
        }
        
        return switcher;
    }
    
    updateLanguageSwitcher(switcher) {
        const currentLang = this.supportedLanguages[this.currentLanguage];
        const nextLang = this.currentLanguage === 'ar' ? 'en' : 'ar';
        const nextLangName = this.supportedLanguages[nextLang].name;
        switcher.innerHTML = `
            <button id="lang-toggle-navbar" class="lang-btn-navbar bg-brand-red text-white px-4 py-2 rounded-lg font-semibold hover:bg-red-700 transition-colors">
                ${this.currentLanguage === 'ar' ? 'English' : 'العربية'}
            </button>
        `;
        // زر واحد فقط يبدل اللغة
        const btn = switcher.querySelector('#lang-toggle-navbar');
        btn.addEventListener('click', () => {
            // غيّر الكوكي ثم أعد تحميل الصفحة
            document.cookie = 'site_lang=' + nextLang + ';path=/;max-age=' + (60*60*24*365) + ';SameSite=Lax';
            location.reload();
        });
    }
    
    switchLanguage(newLang) {
        if (newLang === this.currentLanguage || !this.supportedLanguages[newLang]) {
            return;
        }
        
        console.log(`🔄 Switching language from ${this.currentLanguage} to ${newLang}`);
        
        this.currentLanguage = newLang;
        
        // Apply new language
        this.applyLanguage();
        
        // Update URL without reload
        const url = new URL(window.location);
        url.searchParams.set('lang', newLang);
        window.history.pushState({}, '', url);
        
        // Trigger language change event
        window.dispatchEvent(new CustomEvent('languageChanged', {
            detail: {
                oldLanguage: this.currentLanguage,
                newLanguage: newLang,
                direction: this.supportedLanguages[newLang].direction
            }
        }));
        
        // Track language change
        if (window.trackEvent) {
            window.trackEvent('language_change', {
                from: this.currentLanguage,
                to: newLang,
                direction: this.supportedLanguages[newLang].direction
            });
        }
        
        // Reload dynamic content if needed
        this.reloadDynamicContent();
    }
    
    translate(key, params = {}) {
        const langTranslations = this.translations[this.currentLanguage] || this.translations['en'];
        let translation = langTranslations[key] || key;
        
        // Replace parameters in translation
        Object.entries(params).forEach(([param, value]) => {
            translation = translation.replace(`{${param}}`, value);
        });
        
        return translation;
    }
    
    formatNumber(number, options = {}) {
        const locale = this.supportedLanguages[this.currentLanguage].locale;
        return new Intl.NumberFormat(locale, options).format(number);
    }
    
    formatDate(date, options = {}) {
        const locale = this.supportedLanguages[this.currentLanguage].locale;
        return new Intl.DateTimeFormat(locale, options).format(date);
    }
    
    formatCurrency(amount, currency = 'EGP') {
        const locale = this.supportedLanguages[this.currentLanguage].locale;
        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: currency
        }).format(amount);
    }
    
    getPageKey() {
        const path = window.location.pathname;
        if (path === '/' || path === '/index.html') return 'home';
        return path.replace(/^\/|\.html$/g, '').replace(/\//g, '.');
    }
    
    reloadDynamicContent() {
        // Reload content that needs to be fetched in the new language
        // This would typically involve API calls to get translated content
        
        // Example: Reload blog posts, services, etc.
        if (window.blogManager) {
            window.blogManager.reloadContent(this.currentLanguage);
        }
        
        if (window.servicesManager) {
            window.servicesManager.reloadContent(this.currentLanguage);
        }
        
        // Update any dynamic timestamps
        this.updateTimestamps();
    }
    
    updateTimestamps() {
        document.querySelectorAll('[data-timestamp]').forEach(element => {
            const timestamp = parseInt(element.getAttribute('data-timestamp'));
            const date = new Date(timestamp);
            const format = element.getAttribute('data-format') || 'relative';
            
            if (format === 'relative') {
                element.textContent = this.getRelativeTime(date);
            } else {
                element.textContent = this.formatDate(date, { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }
        });
    }
    
    getRelativeTime(date) {
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        const intervals = [
            { unit: 'years', seconds: 31536000 },
            { unit: 'months', seconds: 2592000 },
            { unit: 'weeks', seconds: 604800 },
            { unit: 'days', seconds: 86400 },
            { unit: 'hours', seconds: 3600 },
            { unit: 'minutes', seconds: 60 }
        ];
        
        for (const interval of intervals) {
            const count = Math.floor(diffInSeconds / interval.seconds);
            if (count > 0) {
                return `${count} ${this.translate(`time.${interval.unit}`)} ${this.translate('time.ago')}`;
            }
        }
        
        return this.translate('time.just_now');
    }
    
    // Public API methods
    getCurrentLanguage() {
        return this.currentLanguage;
    }
    
    getCurrentDirection() {
        return this.supportedLanguages[this.currentLanguage].direction;
    }
    
    isRTL() {
        return this.getCurrentDirection() === 'rtl';
    }
    
    getSupportedLanguages() {
        return this.supportedLanguages;
    }
    
    // Utility method to add translations dynamically
    addTranslations(language, translations) {
        if (!this.translations[language]) {
            this.translations[language] = {};
        }
        
        Object.assign(this.translations[language], translations);
    }
}

// ===== CSS for RTL/LTR Support =====
const rtlLtrStyles = `
/* RTL/LTR Direction Support */
.dir-rtl {
    direction: rtl;
    text-align: right;
}

.dir-ltr {
    direction: ltr;
    text-align: left;
}

/* Language-specific font adjustments */
.lang-ar {
    font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.8;
}

.lang-en {
    font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
}

/* Direction-aware margins and paddings (all common sizes) */
.dir-rtl .ml-1 { margin-left: 0 !important; margin-right: 0.25rem !important; }
.dir-rtl .ml-2 { margin-left: 0 !important; margin-right: 0.5rem !important; }
.dir-rtl .ml-3 { margin-left: 0 !important; margin-right: 0.75rem !important; }
.dir-rtl .ml-4 { margin-left: 0 !important; margin-right: 1rem !important; }
.dir-rtl .ml-5 { margin-left: 0 !important; margin-right: 1.25rem !important; }
.dir-rtl .ml-6 { margin-left: 0 !important; margin-right: 1.5rem !important; }
.dir-rtl .ml-8 { margin-left: 0 !important; margin-right: 2rem !important; }
.dir-rtl .ml-10 { margin-left: 0 !important; margin-right: 2.5rem !important; }
.dir-rtl .ml-12 { margin-left: 0 !important; margin-right: 3rem !important; }
.dir-rtl .ml-16 { margin-left: 0 !important; margin-right: 4rem !important; }
.dir-rtl .ml-20 { margin-left: 0 !important; margin-right: 5rem !important; }
.dir-rtl .ml-24 { margin-left: 0 !important; margin-right: 6rem !important; }
.dir-rtl .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
.dir-rtl .mr-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
.dir-rtl .mr-3 { margin-right: 0 !important; margin-left: 0.75rem !important; }
.dir-rtl .mr-4 { margin-right: 0 !important; margin-left: 1rem !important; }
.dir-rtl .mr-5 { margin-right: 0 !important; margin-left: 1.25rem !important; }
.dir-rtl .mr-6 { margin-right: 0 !important; margin-left: 1.5rem !important; }
.dir-rtl .mr-8 { margin-right: 0 !important; margin-left: 2rem !important; }
.dir-rtl .mr-10 { margin-right: 0 !important; margin-left: 2.5rem !important; }
.dir-rtl .mr-12 { margin-right: 0 !important; margin-left: 3rem !important; }
.dir-rtl .mr-16 { margin-right: 0 !important; margin-left: 4rem !important; }
.dir-rtl .mr-20 { margin-right: 0 !important; margin-left: 5rem !important; }
.dir-rtl .mr-24 { margin-right: 0 !important; margin-left: 6rem !important; }
.dir-rtl .pl-1 { padding-left: 0 !important; padding-right: 0.25rem !important; }
.dir-rtl .pl-2 { padding-left: 0 !important; padding-right: 0.5rem !important; }
.dir-rtl .pl-3 { padding-left: 0 !important; padding-right: 0.75rem !important; }
.dir-rtl .pl-4 { padding-left: 0 !important; padding-right: 1rem !important; }
.dir-rtl .pl-5 { padding-left: 0 !important; padding-right: 1.25rem !important; }
.dir-rtl .pl-6 { padding-left: 0 !important; padding-right: 1.5rem !important; }
.dir-rtl .pl-8 { padding-left: 0 !important; padding-right: 2rem !important; }
.dir-rtl .pl-10 { padding-left: 0 !important; padding-right: 2.5rem !important; }
.dir-rtl .pl-12 { padding-left: 0 !important; padding-right: 3rem !important; }
.dir-rtl .pl-16 { padding-left: 0 !important; padding-right: 4rem !important; }
.dir-rtl .pl-20 { padding-left: 0 !important; padding-right: 5rem !important; }
.dir-rtl .pl-24 { padding-left: 0 !important; padding-right: 6rem !important; }
.dir-rtl .pr-1 { padding-right: 0 !important; padding-left: 0.25rem !important; }
.dir-rtl .pr-2 { padding-right: 0 !important; padding-left: 0.5rem !important; }
.dir-rtl .pr-3 { padding-right: 0 !important; padding-left: 0.75rem !important; }
.dir-rtl .pr-4 { padding-right: 0 !important; padding-left: 1rem !important; }
.dir-rtl .pr-5 { padding-right: 0 !important; padding-left: 1.25rem !important; }
.dir-rtl .pr-6 { padding-right: 0 !important; padding-left: 1.5rem !important; }
.dir-rtl .pr-8 { padding-right: 0 !important; padding-left: 2rem !important; }
.dir-rtl .pr-10 { padding-right: 0 !important; padding-left: 2.5rem !important; }
.dir-rtl .pr-12 { padding-right: 0 !important; padding-left: 3rem !important; }
.dir-rtl .pr-16 { padding-right: 0 !important; padding-left: 4rem !important; }
.dir-rtl .pr-20 { padding-right: 0 !important; padding-left: 5rem !important; }
.dir-rtl .pr-24 { padding-right: 0 !important; padding-left: 6rem !important; }

/* Direction-aware text alignment */
.dir-rtl .text-left { text-align: right; }
.dir-rtl .text-right { text-align: left; }

/* Direction-aware flexbox */
.dir-rtl .flex-row { flex-direction: row-reverse; }
.dir-rtl .justify-start { justify-content: flex-end; }
.dir-rtl .justify-end { justify-content: flex-start; }

/* Language switcher styles */
.language-switcher {
    position: relative;
    z-index: 1000;
}

.language-switcher .lang-dropdown {
    min-width: 150px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.language-switcher .lang-option:first-child {
    border-radius: 0.5rem 0.5rem 0 0;
}

.language-switcher .lang-option:last-child {
    border-radius: 0 0 0.5rem 0.5rem;
}

/* RTL-specific animations */
.dir-rtl .animate-slide-in-left {
    animation: slideInRight 0.5s ease-out;
}

.dir-rtl .animate-slide-in-right {
    animation: slideInLeft 0.5s ease-out;
}

@keyframes slideInRight {
    from { transform: translateX(-100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideInLeft {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Form adjustments for RTL */
.dir-rtl input, .dir-rtl textarea, .dir-rtl select {
    text-align: right;
}

.dir-rtl .form-check {
    flex-direction: row-reverse;
}

.dir-rtl .form-check input {
    margin-left: 0.5rem;
    margin-right: 0;
}

/* Navigation adjustments */
.dir-rtl .breadcrumb {
    flex-direction: row-reverse;
}

.dir-rtl .breadcrumb-separator::before {
    content: "\\";
}

/* Table adjustments */
.dir-rtl table {
    direction: rtl;
}

.dir-rtl th, .dir-rtl td {
    text-align: right;
}

/* Carousel adjustments */
.dir-rtl .carousel-prev {
    right: 10px;
    left: auto;
}

.dir-rtl .carousel-next {
    left: 10px;
    right: auto;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .language-switcher {
        position: fixed;
        top: 1rem;
        right: 1rem;
    }
    
    .dir-rtl .language-switcher {
        right: auto;
        left: 1rem;
    }
    
    .lang-ar {
        font-size: 1.1em;
    }
}

/* Print styles */
@media print {
    .language-switcher {
        display: none;
    }
    
    .dir-rtl {
        direction: rtl;
    }
}
`;

// ===== Initialize Language Manager =====
document.addEventListener('DOMContentLoaded', function() {
    // Add RTL/LTR styles
    const styleSheet = document.createElement('style');
    styleSheet.textContent = rtlLtrStyles;
    document.head.appendChild(styleSheet);
    
    // Initialize language manager
    window.languageManager = new LanguageManager();
    
    // Make translate function globally available
    window.t = (key, params) => window.languageManager.translate(key, params);
    
    console.log('🌐 RTL/LTR Language Manager ready!');
});

// ===== Export for module systems =====
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LanguageManager;
}