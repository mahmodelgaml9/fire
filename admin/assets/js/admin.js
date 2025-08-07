// Admin Panel JavaScript Functions

// Utility Functions
const AdminUtils = {
    // Show loading spinner
    showLoading: function(element) {
        if (element) {
            element.innerHTML = '<div class="spinner"></div>';
            element.disabled = true;
        }
    },

    // Hide loading spinner
    hideLoading: function(element, originalText) {
        if (element) {
            element.innerHTML = originalText;
            element.disabled = false;
        }
    },

    // Show notification
    showNotification: function(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type}`;
        notification.innerHTML = message;
        
        // Add close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.className = 'float-right font-bold text-lg cursor-pointer';
        closeBtn.onclick = () => notification.remove();
        notification.appendChild(closeBtn);
        
        // Insert at top of page
        document.body.insertBefore(notification, document.body.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    },

    // Confirm action
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },

    // Format date
    formatDate: function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-EG', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    // Format file size
    formatFileSize: function(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    // Generate slug from title
    generateSlug: function(title) {
        return title
            .toLowerCase()
            .replace(/[^\u0600-\u06FF\u0750-\u077F\u08A0-\u08FF\uFB50-\uFDFF\uFE70-\uFEFFa-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
    },

    // Auto-generate slug from title
    autoGenerateSlug: function(titleInput, slugInput) {
        titleInput.addEventListener('input', function() {
            if (!slugInput.value) {
                slugInput.value = AdminUtils.generateSlug(this.value);
            }
        });
    },

    // Image preview
    previewImage: function(input, previewElement) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewElement.src = e.target.result;
                previewElement.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    },

    // Table filter
    filterTable: function(tableId, searchTerm, columnIndex = 0) {
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const cell = rows[i].getElementsByTagName('td')[columnIndex];
            if (cell) {
                const text = cell.textContent || cell.innerText;
                if (text.toLowerCase().indexOf(searchTerm.toLowerCase()) > -1) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
    },

    // Sort table
    sortTable: function(tableId, columnIndex, type = 'text') {
        const table = document.getElementById(tableId);
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = Array.from(tbody.getElementsByTagName('tr'));
        
        rows.sort((a, b) => {
            const aValue = a.getElementsByTagName('td')[columnIndex].textContent;
            const bValue = b.getElementsByTagName('td')[columnIndex].textContent;
            
            if (type === 'number') {
                return parseFloat(aValue) - parseFloat(bValue);
            } else if (type === 'date') {
                return new Date(aValue) - new Date(bValue);
            } else {
                return aValue.localeCompare(bValue, 'ar');
            }
        });
        
        // Reorder rows
        rows.forEach(row => tbody.appendChild(row));
    },

    // Modal functions
    openModal: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    },

    closeModal: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    },

    // Close modal when clicking outside
    setupModalOutsideClick: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    AdminUtils.closeModal(modalId);
                }
            });
        }
    },

    // Form validation
    validateForm: function(formId) {
        const form = document.getElementById(formId);
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('border-red-500');
                isValid = false;
            } else {
                input.classList.remove('border-red-500');
            }
        });
        
        return isValid;
    },

    // AJAX request
    ajax: function(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        };
        
        const config = { ...defaultOptions, ...options };
        
        return fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            });
    },

    // File upload with progress
    uploadFile: function(file, url, onProgress, onSuccess, onError) {
        const formData = new FormData();
        formData.append('file', file);
        
        const xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable && onProgress) {
                const percentComplete = (e.loaded / e.total) * 100;
                onProgress(percentComplete);
            }
        });
        
        xhr.addEventListener('load', function() {
            if (xhr.status === 200 && onSuccess) {
                onSuccess(JSON.parse(xhr.responseText));
            } else if (onError) {
                onError(xhr.statusText);
            }
        });
        
        xhr.addEventListener('error', function() {
            if (onError) {
                onError('Network error');
            }
        });
        
        xhr.open('POST', url);
        xhr.send(formData);
    },

    // Debounce function
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

    // Throttle function
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
    }
};

// Blog specific functions
const BlogManager = {
    // Initialize blog editors
    initEditors: function() {
        // Auto-generate slug from title
        const titleInputs = document.querySelectorAll('input[name="ar_title"], input[name="en_title"]');
        const slugInput = document.querySelector('input[name="slug"]');
        
        titleInputs.forEach(input => {
            AdminUtils.autoGenerateSlug(input, slugInput);
        });
        
        // Image preview
        const imageInput = document.querySelector('input[name="featured_image"]');
        const imagePreview = document.querySelector('#image-preview');
        
        if (imageInput && imagePreview) {
            imageInput.addEventListener('change', function() {
                AdminUtils.previewImage(this, imagePreview);
            });
        }
    },

    // Save blog post
    savePost: function(formId) {
        const form = document.getElementById(formId);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        if (!AdminUtils.validateForm(formId)) {
            AdminUtils.showNotification('يرجى ملء جميع الحقول المطلوبة', 'danger');
            return;
        }
        
        AdminUtils.showLoading(submitBtn);
        
        const formData = new FormData(form);
        
        // Add TinyMCE content
        if (typeof tinymce !== 'undefined') {
            const arEditor = tinymce.get('ar_content');
            const enEditor = tinymce.get('en_content');
            
            if (arEditor) {
                formData.set('ar_content', arEditor.getContent());
            }
            if (enEditor) {
                formData.set('en_content', enEditor.getContent());
            }
        }
        
        fetch('blog.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            AdminUtils.hideLoading(submitBtn, originalText);
            AdminUtils.showNotification('تم حفظ المقال بنجاح', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        })
        .catch(error => {
            AdminUtils.hideLoading(submitBtn, originalText);
            AdminUtils.showNotification('حدث خطأ أثناء حفظ المقال', 'danger');
            console.error('Error:', error);
        });
    },

    // Delete blog post
    deletePost: function(postId) {
        AdminUtils.confirm('هل أنت متأكد من حذف هذا المقال؟', function() {
            window.location.href = `blog.php?delete=${postId}&t=${Date.now()}`;
        });
    },

    // Toggle post status
    toggleStatus: function(postId) {
        AdminUtils.confirm('هل أنت متأكد من تغيير حالة هذا المقال؟', function() {
            window.location.href = `blog.php?toggle_status=${postId}&t=${Date.now()}`;
        });
    },

    // Toggle featured status
    toggleFeatured: function(postId) {
        AdminUtils.confirm('هل أنت متأكد من تغيير حالة التميز لهذا المقال؟', function() {
            window.location.href = `blog.php?toggle_featured=${postId}&t=${Date.now()}`;
        });
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize blog manager
    BlogManager.initEditors();
    
    // Setup modal outside click
    const modals = document.querySelectorAll('[id$="Modal"]');
    modals.forEach(modal => {
        AdminUtils.setupModalOutsideClick(modal.id);
    });
    
    // Setup form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!AdminUtils.validateForm(this.id)) {
                e.preventDefault();
                AdminUtils.showNotification('يرجى ملء جميع الحقول المطلوبة', 'danger');
            }
        });
    });
    
    // Setup search functionality
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        const debouncedSearch = AdminUtils.debounce(function() {
            const tableId = input.getAttribute('data-table');
            const columnIndex = input.getAttribute('data-column') || 0;
            AdminUtils.filterTable(tableId, this.value, columnIndex);
        }, 300);
        
        input.addEventListener('input', debouncedSearch);
    });
    
    // Setup sort functionality
    const sortHeaders = document.querySelectorAll('.sort-header');
    sortHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const tableId = this.getAttribute('data-table');
            const columnIndex = this.getAttribute('data-column');
            const sortType = this.getAttribute('data-sort-type') || 'text';
            AdminUtils.sortTable(tableId, columnIndex, sortType);
        });
    });
});

// Export for global use
window.AdminUtils = AdminUtils;
window.BlogManager = BlogManager; 