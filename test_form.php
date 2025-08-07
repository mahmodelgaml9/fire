<?php
echo "=== اختبار إرسال النموذج ===\n\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "✅ تم استلام POST request\n";
    echo "البيانات المرسلة:\n";
    print_r($_POST);
    
    if (isset($_POST['ar_title'])) {
        echo "\n✅ العنوان العربي: " . $_POST['ar_title'] . "\n";
    }
    
    if (isset($_POST['en_title'])) {
        echo "✅ العنوان الإنجليزي: " . $_POST['en_title'] . "\n";
    }
    
    if (isset($_POST['post_id']) && $_POST['post_id']) {
        echo "✅ ID المقال: " . $_POST['post_id'] . " (تحديث)\n";
    } else {
        echo "✅ مقال جديد (إضافة)\n";
    }
    
    echo "\n✅ النموذج يعمل بشكل صحيح!\n";
} else {
    echo "📝 انتظر إرسال النموذج...\n";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار النموذج</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">اختبار إرسال النموذج</h1>
        
        <form method="POST" class="bg-white p-6 rounded-lg shadow">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">العنوان العربي</label>
                <input type="text" name="ar_title" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="عنوان تجريبي">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">العنوان الإنجليزي</label>
                <input type="text" name="en_title" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="Test Title">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">ID المقال (اختياري)</label>
                <input type="text" name="post_id" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="اتركه فارغاً للمقال الجديد">
            </div>
            
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                إرسال النموذج
            </button>
        </form>
    </div>
</body>
</html> 