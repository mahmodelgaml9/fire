<?php
require_once '../config.php';
require_once 'includes/auth.php';
require_once '../includes/functions.php';

checkAdminLogin();
$current_user = getCurrentAdminUser();

// معالجة الإجراءات
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// معالجة رفع ملف
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['media_file'];
        $type = $_POST['media_type'] ?? 'images';
        $convert_to_webp = isset($_POST['convert_to_webp']);
        
        $result = uploadMediaFile($file, $type, $convert_to_webp);
        
        if ($result) {
            $message = 'تم رفع الملف بنجاح';
            logAdminAction('upload_media', 'media', "رفع ملف: " . $result['filename']);
        } else {
            $error = 'حدث خطأ أثناء رفع الملف';
        }
    } else {
        $error = 'يرجى اختيار ملف صحيح';
    }
}

// معالجة حذف ملف
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $filename = $_GET['delete'];
    $type = $_GET['type'] ?? 'images';
    
    $deleted_files = deleteMediaFile($filename, $type);
    
    if ($deleted_files) {
        $message = 'تم حذف الملف بنجاح';
        logAdminAction('delete_media', 'media', "حذف ملف: $filename");
    } else {
        $error = 'حدث خطأ أثناء حذف الملف';
    }
}

// جلب قائمة الملفات
$media_files = [];
$media_dir = '../uploads/images/' . date('Y') . '/' . date('m');
if (is_dir($media_dir)) {
    $files = glob($media_dir . '/*.{jpg,jpeg,png,gif,webp,pdf,doc,docx}', GLOB_BRACE);
    
    foreach ($files as $file) {
        $filename = basename($file);
        $file_info = getMediaInfo($filename, 'images');
        
        if ($file_info) {
            $media_files[] = [
                'filename' => $filename,
                'path' => $file,
                'url' => getMediaUrl($filename, 'images'),
                'size' => $file_info['size'],
                'width' => $file_info['width'] ?? 0,
                'height' => $file_info['height'] ?? 0,
                'mime_type' => $file_info['mime_type'],
                'extension' => $file_info['extension'],
                'created_at' => filemtime($file)
            ];
        }
    }
    
    // ترتيب الملفات حسب تاريخ الإنشاء
    usort($media_files, function($a, $b) {
        return $b['created_at'] - $a['created_at'];
    });
}

$page_title = 'إدارة الوسائط';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إدارة الوسائط</h1>
                <p class="text-gray-600">إدارة ملفات الوسائط والصور</p>
            </div>
            <button onclick="document.getElementById('upload-modal').classList.remove('hidden')" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <i class="fas fa-upload ml-2"></i>
                رفع ملف جديد
            </button>
        </div>
    </div>

    <!-- Display Messages -->
    <?php if ($message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">الفلاتر</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الملف</label>
                    <select id="file_type_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الأنواع</option>
                        <option value="image">صور</option>
                        <option value="document">مستندات</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحجم</label>
                    <select id="size_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">جميع الأحجام</option>
                        <option value="small">صغير (&lt; 1MB)</option>
                        <option value="medium">متوسط (1-5MB)</option>
                        <option value="large">كبير (&gt; 5MB)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                    <input type="text" id="search_filter" placeholder="البحث في أسماء الملفات" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الترتيب</label>
                    <select id="sort_filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="date_desc">الأحدث أولاً</option>
                        <option value="date_asc">الأقدم أولاً</option>
                        <option value="name_asc">الاسم (أ-ي)</option>
                        <option value="name_desc">الاسم (ي-أ)</option>
                        <option value="size_desc">الحجم (الأكبر)</option>
                        <option value="size_asc">الحجم (الأصغر)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">الملفات المرفوعة</h3>
        </div>

        <div class="p-6">
            <?php if (empty($media_files)): ?>
                <div class="text-center py-12">
                    <div class="text-gray-500">
                        <i class="fas fa-images text-4xl mb-4"></i>
                        <p class="text-lg font-medium">لا توجد ملفات</p>
                        <p class="text-sm">ابدأ برفع ملف جديد</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4" id="media-grid">
                    <?php foreach ($media_files as $file): ?>
                        <div class="media-item" 
                             data-type="<?php echo strpos($file['mime_type'], 'image') !== false ? 'image' : 'document'; ?>"
                             data-size="<?php echo $file['size'] < 1024*1024 ? 'small' : ($file['size'] < 5*1024*1024 ? 'medium' : 'large'); ?>"
                             data-name="<?php echo strtolower($file['filename']); ?>"
                             data-date="<?php echo $file['created_at']; ?>"
                             data-size-bytes="<?php echo $file['size']; ?>">
                            
                            <div class="relative group">
                                <?php if (strpos($file['mime_type'], 'image') !== false): ?>
                                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                        <img src="<?php echo $file['url']; ?>" 
                                             alt="<?php echo htmlspecialchars($file['filename']); ?>"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                    </div>
                                <?php else: ?>
                                    <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Overlay -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex space-x-2 space-x-reverse">
                                        <button onclick="previewFile('<?php echo $file['url']; ?>', '<?php echo htmlspecialchars($file['filename']); ?>')" 
                                                class="bg-white text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors duration-200">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="copyFileUrl('<?php echo $file['url']; ?>')" 
                                                class="bg-white text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors duration-200">
                                            <i class="fas fa-link"></i>
                                        </button>
                                        <button onclick="deleteFile('<?php echo $file['filename']; ?>')" 
                                                class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors duration-200">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <p class="text-xs text-gray-600 truncate" title="<?php echo htmlspecialchars($file['filename']); ?>">
                                    <?php echo htmlspecialchars($file['filename']); ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?php echo formatFileSize($file['size']); ?>
                                    <?php if ($file['width'] && $file['height']): ?>
                                        • <?php echo $file['width']; ?>×<?php echo $file['height']; ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">رفع ملف جديد</h3>
            <button onclick="document.getElementById('upload-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">اختر الملف</label>
                <input type="file" name="media_file" required accept="image/*,.pdf,.doc,.docx" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع الوسائط</label>
                <select name="media_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="images">صور</option>
                    <option value="documents">مستندات</option>
                    <option value="videos">فيديوهات</option>
                </select>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="convert_to_webp" id="convert_to_webp" checked class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="convert_to_webp" class="mr-2 block text-sm text-gray-900">تحويل الصور لـ WebP</label>
            </div>
            
            <div class="flex space-x-3 space-x-reverse">
                <button type="submit" name="upload" class="flex-1 bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                    رفع الملف
                </button>
                <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="preview-title"></h3>
            <button onclick="document.getElementById('preview-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="text-center">
            <img id="preview-image" src="" alt="" class="max-w-full max-h-96 mx-auto">
        </div>
    </div>
</div>

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const fileTypeFilter = document.getElementById('file_type_filter');
    const sizeFilter = document.getElementById('size_filter');
    const searchFilter = document.getElementById('search_filter');
    const sortFilter = document.getElementById('sort_filter');
    const mediaItems = document.querySelectorAll('.media-item');

    function filterAndSort() {
        const fileTypeValue = fileTypeFilter.value;
        const sizeValue = sizeFilter.value;
        const searchValue = searchFilter.value.toLowerCase();
        const sortValue = sortFilter.value;

        let visibleItems = [];

        mediaItems.forEach(item => {
            const fileType = item.getAttribute('data-type');
            const size = item.getAttribute('data-size');
            const name = item.getAttribute('data-name');

            let showItem = true;

            if (fileTypeValue && fileType !== fileTypeValue) showItem = false;
            if (sizeValue && size !== sizeValue) showItem = false;
            if (searchValue && !name.includes(searchValue)) showItem = false;

            if (showItem) {
                item.style.display = '';
                visibleItems.push(item);
            } else {
                item.style.display = 'none';
            }
        });

        // Sort visible items
        visibleItems.sort((a, b) => {
            switch (sortValue) {
                case 'date_desc':
                    return b.getAttribute('data-date') - a.getAttribute('data-date');
                case 'date_asc':
                    return a.getAttribute('data-date') - b.getAttribute('data-date');
                case 'name_asc':
                    return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                case 'name_desc':
                    return b.getAttribute('data-name').localeCompare(a.getAttribute('data-name'));
                case 'size_desc':
                    return b.getAttribute('data-size-bytes') - a.getAttribute('data-size-bytes');
                case 'size_asc':
                    return a.getAttribute('data-size-bytes') - b.getAttribute('data-size-bytes');
                default:
                    return 0;
            }
        });

        // Reorder items in DOM
        const grid = document.getElementById('media-grid');
        visibleItems.forEach(item => {
            grid.appendChild(item);
        });
    }

    fileTypeFilter.addEventListener('change', filterAndSort);
    sizeFilter.addEventListener('change', filterAndSort);
    searchFilter.addEventListener('input', filterAndSort);
    sortFilter.addEventListener('change', filterAndSort);
});

// Preview file
function previewFile(url, filename) {
    const modal = document.getElementById('preview-modal');
    const image = document.getElementById('preview-image');
    const title = document.getElementById('preview-title');
    
    title.textContent = filename;
    image.src = url;
    modal.classList.remove('hidden');
}

// Copy file URL
function copyFileUrl(url) {
    navigator.clipboard.writeText(url).then(() => {
        showSuccessToast('تم نسخ الرابط بنجاح');
    }).catch(() => {
        showErrorToast('حدث خطأ أثناء نسخ الرابط');
    });
}

// Delete file
function deleteFile(filename) {
    showConfirmation('هل أنت متأكد من حذف هذا الملف؟', (confirmed) => {
        if (confirmed) {
            window.location.href = `media.php?delete=${encodeURIComponent(filename)}&type=images`;
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?> 