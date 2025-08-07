// Service Landing Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // CTA Button Interactions
    const ctaButtons = document.querySelectorAll('.bg-brand-red, .border-2');
    ctaButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Track button click
            const buttonText = this.textContent.trim();
            console.log('CTA Button clicked:', buttonText);
            
            // Show contact form or redirect based on button type
            if (buttonText.includes('Quote') || buttonText.includes('عرض سعر')) {
                showQuoteForm();
            } else if (buttonText.includes('Consultation') || buttonText.includes('استشارة')) {
                showConsultationForm();
            } else if (buttonText.includes('Brochure') || buttonText.includes('كتيب')) {
                downloadBrochure();
            }
        });
    });

    // Quote Form Function
    function showQuoteForm() {
        // Create modal for quote form
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
                <h3 class="text-2xl font-bold mb-4">Request Free Quote</h3>
                <form id="quoteForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input type="tel" name="phone" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Company</label>
                        <input type="text" name="company" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Service Required</label>
                        <select name="service" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="">Select Service</option>
                            <option value="firefighting-systems">Firefighting Systems</option>
                            <option value="fire-detection">Fire Detection</option>
                            <option value="fire-suppression">Fire Suppression</option>
                            <option value="emergency-lighting">Emergency Lighting</option>
                            <option value="fire-safety-training">Fire Safety Training</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Message</label>
                        <textarea name="message" rows="4" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-brand-red text-white py-2 rounded-lg font-semibold">
                            Submit Request
                        </button>
                        <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg font-semibold">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Handle form submission
        document.getElementById('quoteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitQuoteForm(this);
        });
    }

    // Consultation Form Function
    function showConsultationForm() {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
                <h3 class="text-2xl font-bold mb-4">Schedule Free Consultation</h3>
                <form id="consultationForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input type="tel" name="phone" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Preferred Date</label>
                        <input type="date" name="preferred_date" required class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Preferred Time</label>
                        <select name="preferred_time" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="">Select Time</option>
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="12:00">12:00 PM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Message</label>
                        <textarea name="message" rows="4" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-brand-red text-white py-2 rounded-lg font-semibold">
                            Schedule Consultation
                        </button>
                        <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded-lg font-semibold">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Handle form submission
        document.getElementById('consultationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitConsultationForm(this);
        });
    }

    // Download Brochure Function
    function downloadBrochure() {
        // Simulate brochure download
        console.log('Downloading brochure...');
        
        // Create a temporary link to trigger download
        const link = document.createElement('a');
        link.href = '/brochures/sphinx-fire-brochure.pdf';
        link.download = 'sphinx-fire-brochure.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success message
        showNotification('Brochure download started!', 'success');
    }

    // Submit Quote Form
    function submitQuoteForm(form) {
        const formData = new FormData(form);
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Submitting...';
        submitBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // Reset button
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            
            // Close modal
            closeModal();
            
            // Show success message
            showNotification('Quote request submitted successfully! We will contact you within 24 hours.', 'success');
            
            // Track form submission
            console.log('Quote form submitted:', Object.fromEntries(formData));
        }, 2000);
    }

    // Submit Consultation Form
    function submitConsultationForm(form) {
        const formData = new FormData(form);
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Scheduling...';
        submitBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // Reset button
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            
            // Close modal
            closeModal();
            
            // Show success message
            showNotification('Consultation scheduled successfully! We will confirm your appointment soon.', 'success');
            
            // Track form submission
            console.log('Consultation form submitted:', Object.fromEntries(formData));
        }, 2000);
    }

    // Close Modal Function
    window.closeModal = function() {
        const modal = document.querySelector('.fixed.inset-0');
        if (modal) {
            modal.remove();
        }
    }

    // Show Notification Function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove notification after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);

    // Observe all sections
    const sections = document.querySelectorAll('section');
    sections.forEach(section => {
        observer.observe(section);
    });

    // Add CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hover\\:scale-105:hover {
            transform: scale(1.05);
        }
    `;
    document.head.appendChild(style);

    // Track page views and interactions
    console.log('Service Landing Page loaded');
    
    // Track scroll depth
    let maxScroll = 0;
    window.addEventListener('scroll', () => {
        const scrollPercent = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
        if (scrollPercent > maxScroll) {
            maxScroll = scrollPercent;
            if (maxScroll >= 25 && maxScroll < 50) {
                console.log('User scrolled 25% of the page');
            } else if (maxScroll >= 50 && maxScroll < 75) {
                console.log('User scrolled 50% of the page');
            } else if (maxScroll >= 75) {
                console.log('User scrolled 75% of the page');
            }
        }
    });

    // Track time on page
    let startTime = Date.now();
    window.addEventListener('beforeunload', () => {
        const timeOnPage = Date.now() - startTime;
        console.log(`Time spent on page: ${Math.round(timeOnPage / 1000)} seconds`);
    });
}); 