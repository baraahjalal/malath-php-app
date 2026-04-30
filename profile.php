<?php
include 'includes/db.php'; 
include 'includes/header.php';

// 1. حماية الصفحة: إذا لم تكن مسجلة دخول، يتم توجيهها للـ Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 2. جلب بيانات المستخدمة الحقيقية من قاعدة البيانات
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// 3. (اختياري) جلب عدد المساهمات أو العناصر المحفوظة لاحقاً من جداولها الخاصة
$contributions_count = 12; // رقم تجريبي حالياً حتى تبرمجي جدول المنشورات
?>

<!-- Profile Header Section -->
<section class="profile-header-section">
    <div class="profile-cover">
        <!-- غلاف افتراضي أنيق لملاذ -->
        <img src="https://images.unsplash.com/photo-1579546929518-9e396f3cc809?q=80&w=2070&auto=format&fit=crop" alt="Cover">
        <div class="cover-overlay"></div>
    </div>
    
    <div class="container relative">
        <div class="profile-info-card animate-fade-in-up">
            <div class="profile-avatar-large">
                <!-- عرض الصورة الشخصية أو صورة افتراضية -->
<img src="<?= !empty($user['avatar']) ? $user['avatar'] : 'assets/images/default-avatar.png'; ?>" alt="Profile Picture">                <button class="edit-avatar-btn" onclick="alert('قريباً: رفع الصور الشخصية')">
                    <i class="fa-solid fa-camera"></i>
                </button>
            </div>
            
            <div class="profile-details flex justify-between items-center w-full">
                <div>
                    <h1 class="font-headline font-black text-primary-dark" style="font-size: 2.2rem; margin-bottom: 0.25rem;">
                        <?= htmlspecialchars($user['name']); ?>
                    </h1>
                    <p class="text-secondary" style="font-size: 1.1rem;"><?= htmlspecialchars($user['email']); ?></p>
                    <p id="user-bio-display" style="color: var(--on-surface); max-width: 600px; margin-top: 0.75rem; line-height: 1.6;">
                        <?= !empty($user['bio']) ? htmlspecialchars($user['bio']) : "مرحباً بكِ في مساحتكِ الخاصة في ملاذ. يمكنكِ إضافة نبذة عنكِ من الإعدادات."; ?>
                    </p>
                </div>
                
                <div class="hidden-mobile" style="display: flex; gap: 1.5rem; background: var(--surface-container-low); padding: 1rem 2rem; border-radius: 1.5rem;">
                    <div style="text-align: center;">
                        <span class="font-headline font-bold text-primary" style="font-size: 1.5rem; display: block;"><?= $contributions_count; ?></span>
                        <span style="font-size: 0.85rem; color: var(--secondary);">مساهمة</span>
                    </div>
                    <div style="width: 1px; background-color: var(--outline-variant);"></div>
                    <div style="text-align: center;">
                        <span class="font-headline font-bold text-primary" style="font-size: 1.5rem; display: block;">٤٥</span>
                        <span style="font-size: 0.85rem; color: var(--secondary);">محفوظة</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Tabs Navigation -->
<section style="background-color: #FFFFFF; border-bottom: 1px solid var(--outline-variant); position: sticky; top: 4.5rem; z-index: 30; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
    <div class="container">
        <div class="profile-tabs flex gap-8">
            <button class="profile-tab active" onclick="switchTab('personal-info')">إعدادات الحساب</button>
            <button class="profile-tab" onclick="switchTab('contributions')">نشاطاتي في المجتمع</button>
            <button class="profile-tab" onclick="switchTab('saved-items')">المكتبة الخاصة</button>
        </div>
    </div>
</section>

<!-- Profile Body -->
<section style="padding: 3rem 0; min-height: 60vh; background-color: var(--surface);">
    <div class="container">
        
        <!-- Tab 1: Personal Info & Settings -->
        <div class="tab-content active animate-fade-in-up" id="personal-info">
            <div class="card" style="max-width: 800px; margin: 0 auto; border-radius: 2rem;">
                <h3 class="font-headline font-bold text-primary-dark" style="font-size: 1.4rem; margin-bottom: 1.5rem;">تعديل الملف الشخصي</h3>
                
                <!-- سنوجه الفورم لنفس الصفحة لمعالجة التحديث لاحقاً -->
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">

<!-- قسم الصورة الشخصية داخل الفورم -->
    <div class="profile-avatar-large" style="margin-bottom: 2rem;">
        <img src="<?= !empty($user['avatar']) ? $user['avatar'] : 'assets/images/default-avatar.png'; ?>" id="avatar-preview" alt="Profile">
        
        <!-- حقل اختيار الملف مخفي ويتم تفعيله بالضغط على الأيقونة -->
        <input type="file" name="profile_image" id="profile_image_input" accept="image/png, image/jpeg, image/jpg" style="display: none;" onchange="previewImage(this)">
        
        <button type="button" class="edit-avatar-btn" onclick="document.getElementById('profile_image_input').click()">
            <i class="fa-solid fa-camera"></i>
        </button>
    </div>

                    <div class="input-group">
                        <label class="input-label">الاسم الكامل</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">البريد الإلكتروني (لا يمكن تغييره حالياً)</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" disabled style="background-color: var(--surface-container-high);">
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">نبذة عني (سيظهر في المجتمع)</label>
                        <textarea name="bio" class="form-control" rows="3" placeholder="اكتبي شيئاً عن نفسكِ..."><?= htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>

                    <div style="border-top: 1px dashed var(--outline-variant); margin: 2rem 0;"></div>
                    
                    <h3 class="font-headline font-bold text-primary-dark" style="font-size: 1.2rem; margin-bottom: 1rem;">تأمين الحساب</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="input-group">
                            <label class="input-label">كلمة مرور جديدة</label>
                            <input type="password" name="new_password" class="form-control" placeholder="اتركيه فارغاً إذا لا ترغبين بالتغيير">
                        </div>
                        <div class="input-group">
                            <label class="input-label">تأكيد كلمة المرور</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="أعيدي كتابتها">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 2rem; gap: 1rem;">
                         <button type="submit" class="btn-primary" style="padding: 0.8rem 2.5rem;">تحديث البيانات</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab 2: Contributions (Placeholder) -->
        <div class="tab-content" id="contributions" style="display:none;">
            <div style="max-width: 800px; margin: 0 auto; text-align: center; padding: 4rem 0;">
                <i class="fa-solid fa-feather-pointed" style="font-size: 3rem; color: var(--outline-variant); margin-bottom: 1rem;"></i>
                <h3 style="color: var(--secondary);">لم تقومي بنشر أي منشورات بعد.</h3>
                <a href="community.php" class="btn-outline" style="margin-top: 1rem; display: inline-block;">اذهبي للمجتمع وشاركي الآن</a>
            </div>
        </div>

    </div>
</section>

<script>


// كود بسيط لعرض الصورة فور اختيارها من الجهاز وقبل الرفع
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// وظيفة بسيطة للتنقل بين التبويبات بدون تحميل الصفحة
function switchTab(tabId) {
    // إخفاء كل المحتويات
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    // إزالة الصف الفعال من الأزرار
    document.querySelectorAll('.profile-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // إظهار المحتوى المختار
    document.getElementById(tabId).style.display = 'block';
    // تفعيل الزر المختار
    event.currentTarget.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>