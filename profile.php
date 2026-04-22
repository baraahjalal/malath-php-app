<?php
$current_page = 'profile.php';
include 'includes/header.php';
?>

<!-- Profile Header Section -->
<section class="profile-header-section">
    <!-- Cover Image -->
    <div class="profile-cover">
        <img src="https://images.unsplash.com/photo-1579546929518-9e396f3cc809?q=80&w=2070&auto=format&fit=crop" alt="Cover">
        <!-- Overlay -->
        <div class="cover-overlay"></div>
    </div>
    
    <div class="container relative">
        <div class="profile-info-card">
            <div class="profile-avatar-large">
                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCx9oi_CffQzQD7xh703WXtQv5Ljs9d-NL5lYKUeMo6f_XnT7ObVTaLv7KUvY-Y6kgsX9iIBk2MHZeWFh2RE7cUrZtReZauD2YFdiksobrJkfQk1VBEJVSKk7cAOk1Mb32f_jcHEQ_sJxy_L9eQFMEuQPGC7zRbdt4roxItlcBpnzVrGB2m74yODejLFaGS0Mpw5VERpLn_CZf-VaZKdkFvIWXUJB1y-S-BniGp3pGOUz_EF2V-oxPWK6KDfKlqLnAdO9ZPq1-Qx_U" alt="Profile Picture">
                <button class="edit-avatar-btn"><i class="fa-solid fa-camera"></i></button>
            </div>
            
            <div class="profile-details flex justify-between items-center w-full">
                <div>
                    <h1 class="font-headline font-black text-primary-dark" style="font-size: 2rem; margin-bottom: 0.25rem;">براءة عريبي</h1>
                    <p class="text-secondary" style="font-size: 1.1rem;">baraahjalall@email.com</p>
                    <p style="color: var(--on-surface); max-width: 600px; margin-top: 0.75rem; line-height: 1.6;">
                        شغوفة بتطوير الذات والكتابة في مجال الصحة النفسية، أطمح دائماً لمشاركة ما أتعلمه مع مجتمع ملاذ الجميل لنرتقي معاً.
                    </p>
                </div>
                <div class="hidden-mobile" style="display: flex; gap: 1rem;">
                    <div style="text-align: center;">
                        <span class="font-headline font-bold text-primary" style="font-size: 1.5rem; display: block;">١٢</span>
                        <span style="font-size: 0.85rem; color: var(--secondary);">مساهمة</span>
                    </div>
                    <div style="width: 1px; background-color: var(--outline-variant); margin: 0 0.5rem;"></div>
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
            <button class="profile-tab active" data-tab="personal-info">المعلومات الشخصية</button>
            <button class="profile-tab" data-tab="contributions">مساهماتي</button>
            <button class="profile-tab" data-tab="saved-items">عناصري المحفوظة</button>
        </div>
    </div>
</section>

<!-- Profile Body -->
<section style="padding: 4rem 0; min-height: 50vh;">
    <div class="container">
        <!-- Tab 1: Personal Info -->
        <div class="tab-content active animate-fade-in-up" id="personal-info">
            <div class="card" style="max-width: 800px; margin: 0 auto;">
                <h3 class="font-headline font-bold text-primary-dark" style="font-size: 1.5rem; margin-bottom: 2rem; border-bottom: 1px solid var(--outline-variant); padding-bottom: 1rem;">البيانات الأساسية</h3>
                
                <form>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="input-group">
                            <label class="input-label">الاسم الأول</label>
                            <input type="text" class="form-control" value="براءة">
                        </div>
                        <div class="input-group">
                            <label class="input-label">الاسم الأخير</label>
                            <input type="text" class="form-control" value="عريبي">
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" value="baraahjalall@email.com" dir="ltr" style="text-align: right;">
                    </div>
                    
                    <div class="input-group">
                        <label class="input-label">نبذة عني (السيرة الذاتية)</label>
                        <textarea class="form-control" rows="4">شغوفة بتطوير الذات والكتابة في مجال الصحة النفسية، أطمح دائماً لمشاركة ما أتعلمه مع مجتمع ملاذ الجميل لنرتقي معاً.</textarea>
                    </div>

                    <h3 class="font-headline font-bold text-primary-dark" style="font-size: 1.5rem; margin: 3rem 0 2rem; border-bottom: 1px solid var(--outline-variant); padding-bottom: 1rem;">تغيير كلمة المرور</h3>
                    
                    <div class="input-group">
                        <label class="input-label">كلمة المرور الحالية</label>
                        <input type="password" class="form-control" placeholder="••••••••">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="input-group">
                            <label class="input-label">كلمة المرور الجديدة</label>
                            <input type="password" class="form-control" placeholder="••••••••">
                        </div>
                        <div class="input-group">
                            <label class="input-label">تأكيد كلمة المرور الجديدة</label>
                            <input type="password" class="form-control" placeholder="••••••••">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 2rem;">
                        <button type="button" class="btn-primary">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab 2: Contributions -->
        <div class="tab-content" id="contributions">
            <div class="card animate-fade-in-up" style="max-width: 800px; margin: 0 auto; background: transparent; border: none; box-shadow: none; padding: 0;">
                
                <!-- Sample Post 1 -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-4 text-primary font-bold">
                            <i class="fa-solid fa-comments"></i> رد في مجتمع "السكينة"
                        </div>
                        <span class="notification-time">١٢ أكتوبر ٢٠٢٤</span>
                    </div>
                    <p style="color: var(--on-surface); line-height: 1.7;">
                        أعتقد أن التدوين اليومي لمشاعرك قبل النوم يساعد جداً في تصفية الذهن. جربتها شخصياً وفرقت معي كثيراً.
                    </p>
                </div>

                <!-- Sample Post 2 -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-4 text-primary font-bold">
                            <i class="fa-solid fa-file-lines"></i> مقال منشور
                        </div>
                        <span class="notification-time">٥ سبتمبر ٢٠٢٤</span>
                    </div>
                    <h3 class="font-headline font-bold text-primary-dark" style="font-size: 1.25rem;">رحلة التعافي والنمو</h3>
                    <p style="color: var(--on-surface); line-height: 1.7; margin-top: 0.5rem;">
                        كل إنسان يمر في حياته بلحظات يشعر فيها بالتوقف، لكن الحقيقة أن هذه اللحظات هي فترات استعداد لانطلاقة أعمق...
                    </p>
                    <a href="article.php" style="color: var(--primary); text-decoration: none; font-weight: bold; display: inline-block; margin-top: 1rem;">قراءة المقال</a>
                </div>

            </div>
        </div>

        <!-- Tab 3: Saved Items -->
        <div class="tab-content" id="saved-items">
             <div class="animate-fade-in-up" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                
                <!-- Saved Article 1 -->
                <div class="card" style="padding: 1.5rem; position: relative;">
                    <button class="icon-button" style="position: absolute; top: 1rem; left: 1rem; background: var(--surface); box-shadow: var(--shadow-sm);"><i class="fa-solid fa-bookmark" style="color: var(--primary);"></i></button>
                    <span class="badge" style="margin-bottom: 1rem; border: none;">صحتكِ أولاً</span>
                    <h3 class="font-headline font-bold text-primary-dark" style="font-size: 1.2rem; margin-bottom: 0.5rem;">أفضل ٥ عادات صباحية لزيادة الإنتاجية</h3>
                    <p class="notification-time" style="margin-bottom: 1rem;">بواسطة: د. ريم فهد</p>
                    <a href="article.php" style="color: var(--primary); text-decoration: none; font-weight: bold; display: flex; align-items: center; gap: 0.25rem;">اقرئي الآن <i class="fa-solid fa-arrow-left" style="font-size: 0.8rem;"></i></a>
                </div>

                <!-- Saved Article 2 -->
                <div class="card" style="padding: 1.5rem; position: relative;">
                    <button class="icon-button" style="position: absolute; top: 1rem; left: 1rem; background: var(--surface); box-shadow: var(--shadow-sm);"><i class="fa-solid fa-bookmark" style="color: var(--primary);"></i></button>
                    <span class="badge" style="margin-bottom: 1rem; border: none;">طموحكِ بلا حدود</span>
                    <h3 class="font-headline font-bold text-primary-dark" style="font-size: 1.2rem; margin-bottom: 0.5rem;">كيف تبنين علامة تجارية شخصية قوية</h3>
                    <p class="notification-time" style="margin-bottom: 1rem;">بواسطة: نورة إبراهيم</p>
                    <a href="article.php" style="color: var(--primary); text-decoration: none; font-weight: bold; display: flex; align-items: center; gap: 0.25rem;">اقرئي الآن <i class="fa-solid fa-arrow-left" style="font-size: 0.8rem;"></i></a>
                </div>

             </div>
        </div>
        
    </div>
</section>

<?php include 'includes/footer.php'; ?>
