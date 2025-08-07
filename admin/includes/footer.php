        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 px-6 mt-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <span class="text-sm text-gray-500">
                    &copy; 2024 Sphinx Fire. جميع الحقوق محفوظة.
                </span>
                <span class="text-gray-300">|</span>
                <span class="text-sm text-gray-500">
                    إصدار 1.0
                </span>
            </div>
            
            <div class="flex items-center space-x-4 space-x-reverse">
                <a href="../index.php" target="_blank" class="text-sm text-primary-600 hover:text-primary-700 transition-colors duration-200">
                    <i class="fas fa-external-link-alt ml-1"></i>
                    عرض الموقع
                </a>
                <span class="text-gray-300">|</span>
                <a href="help.php" class="text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-question-circle ml-1"></i>
                    المساعدة
                </a>
                <span class="text-gray-300">|</span>
                <a href="support.php" class="text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200">
                    <i class="fas fa-headset ml-1"></i>
                    الدعم الفني
                </a>
            </div>
        </div>
    </footer>
    
    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4 space-x-reverse">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
            <span class="text-gray-700">جاري التحميل...</span>
        </div>
    </div>
    
    <!-- Success Toast -->
    <div id="success-toast" class="fixed top-4 left-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center">
            <i class="fas fa-check-circle ml-2"></i>
            <span id="success-message"></span>
        </div>
    </div>
    
    <!-- Error Toast -->
    <div id="error-toast" class="fixed top-4 left-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle ml-2"></i>
            <span id="error-message"></span>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl ml-3"></i>
                <h3 class="text-lg font-semibold text-gray-900">تأكيد الحذف</h3>
            </div>
            <p id="confirmation-message" class="text-gray-600 mb-6"></p>
            <div class="flex space-x-3 space-x-reverse">
                <button id="confirm-yes" class="flex-1 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-colors duration-200">
                    نعم، احذف
                </button>
                <button id="confirm-no" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Global Functions
        window.showLoading = function() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        };
        
        window.hideLoading = function() {
            document.getElementById('loading-overlay').classList.add('hidden');
        };
        
        window.showSuccessToast = function(message) {
            const toast = document.getElementById('success-toast');
            const messageEl = document.getElementById('success-message');
            messageEl.textContent = message;
            toast.classList.remove('translate-x-full');
            
            setTimeout(() => {
                toast.classList.add('translate-x-full');
            }, 3000);
        };
        
        window.showErrorToast = function(message) {
            const toast = document.getElementById('error-toast');
            const messageEl = document.getElementById('error-message');
            messageEl.textContent = message;
            toast.classList.remove('translate-x-full');
            
            setTimeout(() => {
                toast.classList.add('translate-x-full');
            }, 3000);
        };
        
        window.showConfirmation = function(message, callback) {
            const modal = document.getElementById('confirmation-modal');
            const messageEl = document.getElementById('confirmation-message');
            const yesBtn = document.getElementById('confirm-yes');
            const noBtn = document.getElementById('confirm-no');
            
            messageEl.textContent = message;
            modal.classList.remove('hidden');
            
            const hideModal = () => {
                modal.classList.add('hidden');
                yesBtn.removeEventListener('click', handleYes);
                noBtn.removeEventListener('click', handleNo);
            };
            
            const handleYes = () => {
                hideModal();
                if (callback) callback(true);
            };
            
            const handleNo = () => {
                hideModal();
                if (callback) callback(false);
            };
            
            yesBtn.addEventListener('click', handleYes);
            noBtn.addEventListener('click', handleNo);
        };
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-auto-hide');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
        
        // Form validation
        function validateForm(form) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            return isValid;
        }
        
        // AJAX helper
        function makeAjaxRequest(url, method = 'GET', data = null) {
            return new Promise((resolve, reject) => {
                const xhr = new XMLHttpRequest();
                xhr.open(method, url);
                xhr.setRequestHeader('Content-Type', 'application/json');
                
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            resolve(response);
                        } catch (e) {
                            resolve(xhr.responseText);
                        }
                    } else {
                        reject(new Error('Request failed'));
                    }
                };
                
                xhr.onerror = function() {
                    reject(new Error('Network error'));
                };
                
                if (data) {
                    xhr.send(JSON.stringify(data));
                } else {
                    xhr.send();
                }
            });
        }
        
        // Table sorting
        function sortTable(table, columnIndex) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const header = table.querySelector('thead tr').children[columnIndex];
            
            const isAscending = header.classList.contains('sort-asc');
            
            rows.sort((a, b) => {
                const aValue = a.children[columnIndex].textContent.trim();
                const bValue = b.children[columnIndex].textContent.trim();
                
                if (isAscending) {
                    return bValue.localeCompare(aValue, 'ar');
                } else {
                    return aValue.localeCompare(bValue, 'ar');
                }
            });
            
            rows.forEach(row => tbody.appendChild(row));
            
            // Update header classes
            table.querySelectorAll('th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
            });
            
            header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');
        }
        
        // Search functionality
        function filterTable(table, searchTerm) {
            const tbody = table.querySelector('tbody');
            const rows = tbody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matches = text.includes(searchTerm.toLowerCase());
                row.style.display = matches ? '' : 'none';
            });
        }
        
        // Export table to CSV
        function exportTableToCSV(table, filename) {
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            rows.forEach(row => {
                const cols = row.querySelectorAll('td, th');
                const rowData = [];
                
                cols.forEach(col => {
                    rowData.push('"' + col.textContent.replace(/"/g, '""') + '"');
                });
                
                csv.push(rowData.join(','));
            });
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    </script>
    
    <!-- Admin JavaScript -->
    <script src="assets/js/admin.js"></script>
</body>
</html> 