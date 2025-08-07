// === Sphinx Fire - Consolidated Main JS ===

// Sticky Header
window.addEventListener('scroll', function() {
    const header = document.querySelector('.sticky-header');
    if (header) {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
});

// Mobile Menu Toggle
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const nav = document.querySelector('nav');
if (mobileMenuBtn && nav) {
    mobileMenuBtn.addEventListener('click', () => {
        nav.classList.toggle('hidden');
    });
}

// Lightbox (for gallery images)
function openLightbox(src, alt) {
    const lightbox = document.getElementById('lightbox');
    const img = document.getElementById('lightbox-img');
    if (lightbox && img) {
        img.src = src;
        img.alt = alt || '';
        lightbox.classList.add('active');
    }
}
function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    if (lightbox) {
        lightbox.classList.remove('active');
    }
}
// Attach click event to gallery items
const galleryItems = document.querySelectorAll('.gallery-item');
galleryItems.forEach(item => {
    item.addEventListener('click', function() {
        const img = this.querySelector('img');
        if (img) openLightbox(img.src, img.alt);
    });
});

// Universal Carousel Handler for all .hero-slide carousels
(function() {
    document.querySelectorAll('.hero-slide').forEach((slide, idx, arr) => {
        if (!slide.classList.contains('active')) {
            slide.style.opacity = 0;
            slide.style.display = 'none';
        } else {
            slide.style.opacity = 1;
            slide.style.display = 'flex';
        }
    });
    const carousels = document.querySelectorAll('.hero-slide').length > 0;
    if (!carousels) return;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.carousel-dot');
    const prev = document.querySelector('.carousel-prev');
    const next = document.querySelector('.carousel-next');
    let current = 0;
    let isTransitioning = false;
    function showSlide(idx) {
        if (isTransitioning || idx === current) return;
        isTransitioning = true;
        slides[current].classList.remove('active');
        dots[current]?.classList.remove('active');
        setTimeout(() => {
            slides[current].style.opacity = 0;
            slides[current].style.display = 'none';
            slides[idx].classList.add('active');
            dots[idx]?.classList.add('active');
            slides[idx].style.display = 'flex';
            setTimeout(() => {
                slides[idx].style.opacity = 1;
                current = idx;
                setTimeout(() => { isTransitioning = false; }, 700);
            }, 50);
        }, 100);
    }
    if (slides.length > 0 && dots.length > 0) {
        dots.forEach((dot, i) => {
            dot.addEventListener('click', () => showSlide(i));
        });
        if (prev && next) {
            prev.addEventListener('click', () => showSlide((current - 1 + slides.length) % slides.length));
            next.addEventListener('click', () => showSlide((current + 1) % slides.length));
        }
        setInterval(() => showSlide((current + 1) % slides.length), 8000);
    }
})();

// Accordion (FAQ, Technical Details)
document.querySelectorAll('.faq-header, .accordion-header').forEach(header => {
    header.addEventListener('click', function() {
        const content = this.nextElementSibling;
        if (content) {
            content.classList.toggle('active');
            const arrow = this.querySelector('.accordion-arrow');
            if (arrow) arrow.classList.toggle('rotate');
        }
    });
});

// Back to Top Button
const backToTop = document.getElementById('back-to-top');
if (backToTop) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 400) {
            backToTop.classList.add('opacity-100', 'pointer-events-auto');
        } else {
            backToTop.classList.remove('opacity-100', 'pointer-events-auto');
        }
    });
    backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

// WhatsApp Floating Button (optional: can be extended)
// No extra JS needed unless for tracking

// Newsletter Popup (Blog)
const newsletterPopup = document.getElementById('newsletter-popup');
if (newsletterPopup) {
    setTimeout(() => newsletterPopup.classList.add('active'), 10000);
    newsletterPopup.querySelector('button[onclick*="closeNewsletterPopup"]').addEventListener('click', () => {
        newsletterPopup.classList.remove('active');
    });
}

// General: Smooth Scroll for anchor links
const anchorLinks = document.querySelectorAll('a[href^="#"]');
anchorLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    [
        '.advantage-card',
        '.feature-card',
        '.service-card',
        '.project-card',
        '.testimonial-card',
        '.value-card',
        '.team-card',
        '.approach-step',
        '.results-card',
        '.contact-card',
        '.faq-item',
        '.blog-card'
    ].forEach(function(selector) {
        document.querySelectorAll(selector).forEach(function(card) {
            card.classList.add('animate');
        });
    });

    // Newsletter Popup logic
    var popup = document.querySelector('.newsletter-popup');
    if (popup) {
        setTimeout(function() {
            popup.classList.add('active');
        }, 10000); // 10 ثواني
        // إغلاق البوب-أب عند الضغط على زر الإغلاق
        popup.addEventListener('click', function(e) {
            if (e.target.classList.contains('newsletter-close') || e.target.closest('.newsletter-close')) {
                popup.classList.remove('active');
            }
        });
    }
});

// === END ===
