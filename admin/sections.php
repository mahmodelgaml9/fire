<?php
require_once '../config.php';
require_once 'includes/auth.php';

// التحقق من تسجيل الدخول
checkAdminLogin();

// الحصول على معلومات المستخدم
$current_user = getCurrentAdminUser();

// معالجة الإجراءات
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

// معالجة حذف السكاشن
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $section_id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM sections WHERE id = ?");
        $stmt->execute([$section_id]);
        $message = 'تم حذف السكاشن بنجاح';
        logAdminAction('delete_section', 'sections', "حذف سكاشن رقم $section_id");
        
        // إعادة التوجيه للقائمة
        header('Location: sections.php?deleted=' . time());
        exit;
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء حذف السكاشن';
        error_log('Delete Section Error: ' . $e->getMessage());
    }
}

// معالجة تغيير حالة السكاشن
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $section_id = (int)$_GET['toggle_status'];
    try {
        $stmt = $pdo->prepare("UPDATE sections SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$section_id]);
        $message = 'تم تحديث حالة السكاشن بنجاح';
        logAdminAction('toggle_section_status', 'sections', "تغيير حالة سكاشن رقم $section_id");
        
        // إعادة التوجيه للقائمة
        header('Location: sections.php?status_updated=' . time());
        exit;
    } catch (PDOException $e) {
        $error = 'حدث خطأ أثناء تحديث حالة السكاشن';
        error_log('Toggle Section Status Error: ' . $e->getMessage());
    }
}

// جلب قائمة السكاشنات
$sections = [];
try {
    $stmt = $pdo->query("
        SELECT s.*, 
               p.slug as page_slug,
               pt.title as page_title
        FROM sections s
        LEFT JOIN pages p ON s.page_id = p.id
        LEFT JOIN page_translations pt ON p.id = pt.page_id AND pt.language_id = 1
        ORDER BY s.created_at DESC
    ");
    $sections = $stmt->fetchAll();
    
    // جلب الترجمات لكل سكاشن
    $tableExists = $pdo->query("SHOW TABLES LIKE 'section_translations'")->fetch();
    if ($tableExists) {
        foreach ($sections as &$section) {
            // جلب الترجمة العربية
            $stmt = $pdo->prepare("
                SELECT title, subtitle, content, button_text, button_url, background_image
                FROM section_translations 
                WHERE section_id = ? AND language_id = 1
            ");
            $stmt->execute([$section['id']]);
            $ar_translation = $stmt->fetch();
            
            // جلب الترجمة الإنجليزية
            $stmt = $pdo->prepare("
                SELECT title, subtitle, content, button_text, button_url, background_image
                FROM section_translations 
                WHERE section_id = ? AND language_id = 2
            ");
            $stmt->execute([$section['id']]);
            $en_translation = $stmt->fetch();
            
            $section['ar_title'] = $ar_translation['title'] ?? '';
            $section['ar_subtitle'] = $ar_translation['subtitle'] ?? '';
            $section['ar_content'] = $ar_translation['content'] ?? '';
            $section['ar_button_text'] = $ar_translation['button_text'] ?? '';
            $section['ar_button_url'] = $ar_translation['button_url'] ?? '';
            $section['ar_background_image'] = $ar_translation['background_image'] ?? '';
            
            $section['en_title'] = $en_translation['title'] ?? '';
            $section['en_subtitle'] = $en_translation['subtitle'] ?? '';
            $section['en_content'] = $en_translation['content'] ?? '';
            $section['en_button_text'] = $en_translation['button_text'] ?? '';
            $section['en_button_url'] = $en_translation['button_url'] ?? '';
            $section['en_background_image'] = $en_translation['background_image'] ?? '';
        }
    }
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب السكاشنات: ' . $e->getMessage();
    error_log('Fetch Sections Error: ' . $e->getMessage());
}

// جلب قائمة الصفحات للإضافة
$pages = [];
try {
    $stmt = $pdo->query("
        SELECT p.id, p.slug, pt.title
        FROM pages p
        LEFT JOIN page_translations pt ON p.id = pt.page_id AND pt.language_id = 1
        WHERE p.status = 'published'
        ORDER BY pt.title
    ");
    $pages = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'حدث خطأ أثناء جلب قائمة الصفحات';
}

$page_title = 'إدارة السكاشنات';
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">إدارة السكاشنات</h1>
                <p class="text-gray-600">إدارة أقسام الصفحات ومحتواها</p>
            </div>
            <button onclick="openAddModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                <i class="fas fa-plus ml-2"></i>
                إضافة سكاشن جديد
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
    
    <?php if (isset($_GET['created'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم إضافة السكاشن بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم تحديث السكاشن بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم حذف السكاشن بنجاح!
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['status_updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            تم تحديث حالة السكاشن بنجاح!
        </div>
    <?php endif; ?>
    
    <!-- Sections Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">قائمة السكاشنات</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            السكاشن
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الصفحة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            تاريخ الإنشاء
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($sections)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-layer-group text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد سكاشنات</p>
                                    <p class="text-sm">ابدأ بإضافة سكاشن جديد</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sections as $section): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-layer-group text-green-600"></i>
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($section['ar_title'] ?: $section['en_title'] ?: $section['section_key'] ?: 'بدون عنوان'); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($section['section_type'] ?: ''); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($section['page_title'] ?: $section['page_slug'] ?: 'غير محدد'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($section['is_active']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            نشط
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            غير نشط
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $section['created_at'] ? date('Y/m/d', strtotime($section['created_at'])) : 'غير محدد'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($section)); ?>)" 
                                                class="text-primary-600 hover:text-primary-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="sections.php?toggle_status=<?php echo $section['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-yellow-600 hover:text-yellow-900"
                                           onclick="return confirm('هل أنت متأكد من تغيير حالة هذا السكاشن؟')">
                                            <i class="fas fa-toggle-on"></i>
                                        </a>
                                        <a href="sections.php?delete=<?php echo $section['id']; ?>&t=<?php echo time(); ?>" 
                                           class="text-red-600 hover:text-red-900"
                                           onclick="return confirm('هل أنت متأكد من حذف هذا السكاشن؟')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">إضافة سكاشن جديد</h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="addSectionForm" method="POST" action="sections.php">
                <input type="hidden" name="action" value="create">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Page Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الصفحة</label>
                        <select name="page_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر الصفحة</option>
                            <?php foreach ($pages as $page): ?>
                                <option value="<?php echo $page['id']; ?>">
                                    <?php echo htmlspecialchars($page['title'] ?: $page['slug']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Section Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع السكاشن</label>
                        <input type="text" name="section_type" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="مثال: hero, about, services">
                    </div>
                    
                    <!-- Section Key -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">مفتاح السكاشن</label>
                        <input type="text" name="section_key" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="مثال: home-hero, about-vision">
                    </div>
                    
                    <!-- Sort Order -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">ترتيب العرض</label>
                        <input type="number" name="sort_order" value="0" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-3 space-x-reverse">
                    <button type="button" onclick="closeModal('addModal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                        إضافة السكاشن
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">تعديل السكاشن</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editSectionForm" method="POST" action="sections.php">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="section_id" id="edit_section_id">
                
                <!-- Section Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">معلومات السكاشن</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="font-medium">المفتاح:</span> 
                            <span id="edit_section_key"></span>
                        </div>
                        <div>
                            <span class="font-medium">النوع:</span> 
                            <span id="edit_section_type"></span>
                        </div>
                        <div>
                            <span class="font-medium">الترتيب:</span> 
                            <span id="edit_sort_order"></span>
                        </div>
                        <div>
                            <span class="font-medium">الحالة:</span> 
                            <span id="edit_is_active"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Arabic Content -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">المحتوى العربي</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                            <input type="text" name="ar_title" id="edit_ar_title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">العنوان الفرعي</label>
                            <input type="text" name="ar_subtitle" id="edit_ar_subtitle" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نص الزر</label>
                            <input type="text" name="ar_button_text" id="edit_ar_button_text" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">رابط الزر</label>
                            <input type="url" name="ar_button_url" id="edit_ar_button_url" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">صورة الخلفية</label>
                            <input type="text" name="ar_background_image" id="edit_ar_background_image" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                   placeholder="مسار الصورة">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">المحتوى</label>
                            <textarea name="ar_content" id="edit_ar_content" rows="4" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                      placeholder="محتوى السكاشن..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- English Content -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">المحتوى الإنجليزي</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="en_title" id="edit_en_title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                            <input type="text" name="en_subtitle" id="edit_en_subtitle" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                            <input type="text" name="en_button_text" id="edit_en_button_text" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Button URL</label>
                            <input type="url" name="en_button_url" id="edit_en_button_url" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Background Image</label>
                            <input type="text" name="en_background_image" id="edit_en_background_image" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                   placeholder="Image path">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                            <textarea name="en_content" id="edit_en_content" rows="4" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                      placeholder="Section content..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end space-x-3 space-x-reverse">
                    <button type="button" onclick="closeModal('editModal')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200">
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function openEditModal(sectionData) {
    // تعبئة البيانات في النموذج
    document.getElementById('edit_section_id').value = sectionData.id;
    document.getElementById('edit_section_key').textContent = sectionData.section_key;
    document.getElementById('edit_section_type').textContent = sectionData.section_type;
    document.getElementById('edit_sort_order').textContent = sectionData.sort_order;
    document.getElementById('edit_is_active').textContent = sectionData.is_active ? 'نشط' : 'غير نشط';
    
    // تعبئة المحتوى العربي
    document.getElementById('edit_ar_title').value = sectionData.ar_title || '';
    document.getElementById('edit_ar_subtitle').value = sectionData.ar_subtitle || '';
    document.getElementById('edit_ar_content').value = sectionData.ar_content || '';
    document.getElementById('edit_ar_button_text').value = sectionData.ar_button_text || '';
    document.getElementById('edit_ar_button_url').value = sectionData.ar_button_url || '';
    document.getElementById('edit_ar_background_image').value = sectionData.ar_background_image || '';
    
    // تعبئة المحتوى الإنجليزي
    document.getElementById('edit_en_title').value = sectionData.en_title || '';
    document.getElementById('edit_en_subtitle').value = sectionData.en_subtitle || '';
    document.getElementById('edit_en_content').value = sectionData.en_content || '';
    document.getElementById('edit_en_button_text').value = sectionData.en_button_text || '';
    document.getElementById('edit_en_button_url').value = sectionData.en_button_url || '';
    document.getElementById('edit_en_background_image').value = sectionData.en_background_image || '';
    
    document.getElementById('editModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// إغلاق النوافذ عند النقر خارجها
window.onclick = function(event) {
    if (event.target.classList.contains('fixed')) {
        event.target.classList.add('hidden');
    }
}
</script>

<?php
// معالجة النماذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        // إضافة سكاشن جديد
        $page_id = (int)$_POST['page_id'];
        $section_type = $_POST['section_type'];
        $section_key = $_POST['section_key'];
        $sort_order = (int)$_POST['sort_order'];
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO sections (page_id, section_type, section_key, sort_order, is_active, settings)
                VALUES (?, ?, ?, ?, 1, '{}')
            ");
            $stmt->execute([$page_id, $section_type, $section_key, $sort_order]);
            $section_id = $pdo->lastInsertId();
            
            $message = 'تم إضافة السكاشن بنجاح';
            logAdminAction('create_section', 'sections', "إضافة سكاشن جديد: $section_key");
            
            // إعادة التوجيه للقائمة
            header('Location: sections.php?created=' . time());
            exit;
        } catch (PDOException $e) {
            $error = 'حدث خطأ أثناء إضافة السكاشن: ' . $e->getMessage();
            error_log('Create Section Error: ' . $e->getMessage());
        }
    } elseif ($action === 'edit') {
        // تحديث سكاشن موجود
        $section_id = (int)$_POST['section_id'];
        $ar_title = $_POST['ar_title'] ?? '';
        $ar_subtitle = $_POST['ar_subtitle'] ?? '';
        $ar_content = $_POST['ar_content'] ?? '';
        $ar_button_text = $_POST['ar_button_text'] ?? '';
        $ar_button_url = $_POST['ar_button_url'] ?? '';
        $ar_background_image = $_POST['ar_background_image'] ?? '';
        
        $en_title = $_POST['en_title'] ?? '';
        $en_subtitle = $_POST['en_subtitle'] ?? '';
        $en_content = $_POST['en_content'] ?? '';
        $en_button_text = $_POST['en_button_text'] ?? '';
        $en_button_url = $_POST['en_button_url'] ?? '';
        $en_background_image = $_POST['en_background_image'] ?? '';
        
        try {
            // التحقق من وجود جدول section_translations
            $tableExists = $pdo->query("SHOW TABLES LIKE 'section_translations'")->fetch();
            
            if ($tableExists) {
                // تحديث الترجمة العربية
                $stmt = $pdo->prepare("
                    INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, button_url, background_image)
                    VALUES (?, 1, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    title = VALUES(title), subtitle = VALUES(subtitle), content = VALUES(content),
                    button_text = VALUES(button_text), button_url = VALUES(button_url), background_image = VALUES(background_image)
                ");
                $stmt->execute([$section_id, $ar_title, $ar_subtitle, $ar_content, $ar_button_text, $ar_button_url, $ar_background_image]);
                
                // تحديث الترجمة الإنجليزية
                $stmt = $pdo->prepare("
                    INSERT INTO section_translations (section_id, language_id, title, subtitle, content, button_text, button_url, background_image)
                    VALUES (?, 2, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    title = VALUES(title), subtitle = VALUES(subtitle), content = VALUES(content),
                    button_text = VALUES(button_text), button_url = VALUES(button_url), background_image = VALUES(background_image)
                ");
                $stmt->execute([$section_id, $en_title, $en_subtitle, $en_content, $en_button_text, $en_button_url, $en_background_image]);
            }
            
            $message = 'تم تحديث السكاشن بنجاح';
            logAdminAction('update_section', 'sections', "تحديث سكاشن رقم $section_id");
            
            // إعادة التوجيه للقائمة
            header('Location: sections.php?updated=' . time());
            exit;
        } catch (PDOException $e) {
            $error = 'حدث خطأ أثناء تحديث السكاشن: ' . $e->getMessage();
            error_log('Update Section Error: ' . $e->getMessage());
        }
    }
}
?>

<?php include 'includes/footer.php'; ?> 