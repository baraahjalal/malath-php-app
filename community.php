<?php include 'includes/header.php'; ?>

<style>
/* Community Page Styles - Feminine & Polished */
.community-container {
  display: grid;
  grid-template-columns: 1fr;
  gap: 3rem;
  padding: 3rem 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}

@media (min-width: 1024px) {
  .community-container {
    grid-template-columns: 3.5fr 8.5fr;
  }
}

.sidebar-widget {
  background-color: #FFFFFF;
  border-radius: 1.5rem;
  padding: 2rem;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--outline-variant);
  transition: var(--transition-smooth);
}

.sidebar-widget:hover {
  box-shadow: var(--shadow-md);
}

.widget-title {
  font-family: var(--font-headline);
  font-weight: 800;
  font-size: 1.35rem;
  color: var(--primary-dark);
  margin-bottom: 1.5rem;
  position: relative;
  display: inline-block;
}

.widget-title::after {
  content: '';
  position: absolute;
  bottom: -4px; right: 0;
  width: 50%; height: 2px;
  background: var(--primary-gradient);
  border-radius: 2px;
}

.community-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  border-radius: 1rem;
  transition: var(--transition-fast);
  text-decoration: none;
  margin-bottom: 0.5rem;
}

.community-item:hover {
  background-color: var(--surface-container);
  transform: translateX(-5px); /* Move left in RTL */
}

.community-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary-dark);
  transition: var(--transition-fast);
}

.community-item:hover .community-icon {
  transform: scale(1.1) rotate(5deg);
}

.bg-primary-container { background-color: var(--primary-container); }
.bg-secondary-container { background-color: var(--secondary-container); }
.bg-tertiary-container { background-color: var(--tertiary-light); }

.quote-widget {
  background: var(--primary-gradient);
  color: white;
  border-radius: 1.5rem;
  padding: 2.5rem 2rem;
  position: relative;
  overflow: hidden;
  margin-bottom: 2rem;
  box-shadow: var(--shadow-primary);
}

.quote-icon {
  position: absolute;
  bottom: -2rem;
  left: -1rem;
  font-size: 10rem;
  opacity: 0.1;
  font-family: var(--font-headline);
  transform: rotate(12deg);
  line-height: 1;
}

.feed-header {
  background-color: #ffffff;
  border-radius: 1.5rem;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--outline-variant);
  margin-bottom: 2.5rem;
  transition: var(--transition-smooth);
}

.feed-header:focus-within {
  box-shadow: var(--shadow-md);
  border-color: var(--primary-light);
}

.feed-input {
  flex-grow: 1;
  background-color: var(--surface-container);
  border-radius: 2rem;
  padding: 1rem 1.5rem;
  color: var(--on-surface);
  cursor: text;
  border: none;
  font-family: var(--font-body);
  font-size: 1rem;
  outline: none;
  transition: var(--transition-fast);
}

.feed-input:focus {
  background-color: #FFFFFF;
  box-shadow: inset 0 0 0 2px var(--primary-container);
}

.feed-btn {
  padding: 0;
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 1.25rem;
}

.article-card {
  background-color: #ffffff;
  border-radius: 1.5rem;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--outline-variant);
  transition: var(--transition-smooth);
  margin-bottom: 2.5rem;
}

.article-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-lg);
  border-color: var(--primary-container);
}

.article-image {
  position: relative;
  height: 18rem;
  width: 100%;
  overflow: hidden;
}

.article-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: var(--transition-smooth);
}

.article-card:hover .article-image img {
  transform: scale(1.05);
}

.article-badge {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem; /* RTL display right */
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(8px);
  color: var(--primary-dark);
  padding: 0.5rem 1.25rem;
  border-radius: 2rem;
  font-size: 0.875rem;
  font-weight: 800;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  font-family: var(--font-headline);
}

.article-content {
  padding: 2.5rem 2rem;
}

.article-title {
  font-family: var(--font-headline);
  font-weight: 800;
  font-size: 1.75rem;
  color: var(--primary-dark);
  line-height: 1.3;
  margin-bottom: 1rem;
  transition: var(--transition-fast);
}

.article-card:hover .article-title {
  color: var(--primary);
}

.article-desc {
  color: var(--secondary);
  font-size: 1.1rem;
  line-height: 1.7;
  margin-bottom: 2rem;
}

.article-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1.5rem;
  border-top: 1px solid var(--surface-container-high);
}

.action-buttons button {
  background: none;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--secondary);
  cursor: pointer;
  margin-left: 1.5rem; /* Space between buttons in RTL */
  transition: var(--transition-fast);
  font-family: var(--font-body);
  font-weight: 600;
}

.action-buttons button:hover {
  color: var(--primary);
  transform: translateY(-2px);
}

.action-buttons button.active {
  color: var(--primary);
}

.action-buttons button i {
  font-size: 1.125rem;
}

.btn-read-more {
  background-color: var(--surface-container);
  color: var(--primary-dark);
  padding: 0.6rem 1.5rem;
  border-radius: 2rem;
  font-weight: 700;
  text-decoration: none;
  font-family: var(--font-headline);
  transition: var(--transition-fast);
}

.btn-read-more:hover {
  background: var(--primary-gradient);
  color: white;
  box-shadow: var(--shadow-sm);
}

.discussion-link {
  text-decoration: none;
  color: var(--on-surface);
  display: block;
  margin-bottom: 0.25rem;
  transition: var(--transition-fast);
}

.discussion-link:hover {
  color: var(--primary);
}

</style>

<div class="community-container">
    <!-- Sidebar -->
    <aside style="order: 1;" class="animate-fade-in-up delay-100">
        <div class="sidebar-widget">
            <h3 class="widget-title">أبرز المجتمعات</h3>
            <div>
                <a href="#" class="community-item">
                    <div class="community-icon bg-primary-container"><i class="fa-solid fa-brain" style="font-size: 1.25rem;"></i></div>
                    <div style="flex-grow: 1;">
                        <div class="font-bold text-on-surface" style="color: var(--primary-dark);">الصحة النفسية</div>
                        <div style="font-size: 0.85rem; color: var(--secondary);">١.٢ ألف عضوة</div>
                    </div>
                </a>
                <a href="#" class="community-item">
                    <div class="community-icon bg-tertiary-container"><i class="fa-solid fa-briefcase" style="font-size: 1.25rem;"></i></div>
                    <div style="flex-grow: 1;">
                        <div class="font-bold text-on-surface" style="color: var(--primary-dark);">ريادة الأعمال</div>
                        <div style="font-size: 0.85rem; color: var(--secondary);">٨٥٠ عضوة</div>
                    </div>
                </a>
                <a href="#" class="community-item">
                    <div class="community-icon bg-secondary-container"><i class="fa-solid fa-palette" style="font-size: 1.25rem;"></i></div>
                    <div style="flex-grow: 1;">
                        <div class="font-bold text-on-surface" style="color: var(--primary-dark);">الفن والتصميم</div>
                        <div style="font-size: 0.85rem; color: var(--secondary);">٢.٤ ألف عضوة</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="quote-widget">
            <div class="quote-icon">"</div>
            <h4 class="font-headline font-bold mb-4 position-relative" style="font-size: 1.25rem; z-index: 10;">مساحة إلهام</h4>
            <p style="font-size: 1.125rem; line-height: 1.8; z-index: 10; position: relative;">
                "تذكري دائماً أن لطفكِ مع نفسكِ هو أول خطوات التعافي. كوني صديقة لروحك وتألقي."
            </p>
            <div style="margin-top: 1.5rem; font-size: 0.875rem; opacity: 0.9; font-weight: 700;">— رسالة اليوم</div>
        </div>

        <div class="sidebar-widget">
            <h3 class="widget-title">مناقشات ساخنة</h3>
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div style="border-right: 4px solid var(--primary-light); padding-right: 1.25rem;">
                    <a href="#" class="font-bold discussion-link">كيف نوازن بين العمل والحياة الخاصة؟</a>
                    <span style="font-size: 0.75rem; color: var(--secondary);">٤٥ تعليق • قبل ساعتين</span>
                </div>
                <div style="border-right: 4px solid var(--outline); padding-right: 1.25rem;">
                    <a href="#" class="font-bold discussion-link">أفضل الكتب في تطوير الذات لعام ٢٠٢٤</a>
                    <span style="font-size: 0.75rem; color: var(--secondary);">١٢٠ تعليق • قبل ٥ ساعات</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Feed -->
    <div style="order: 2;">
        <div class="feed-header animate-fade-in-up">
            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=256&auto=format&fit=crop" alt="Avatar" class="profile-avatar" style="width: 3.5rem; height: 3.5rem;">
            <input type="text" class="feed-input" placeholder="بماذا تفكرين اليوم يا براءة؟ شاركينا...">
            <button class="btn-primary feed-btn"><i class="fa-solid fa-paper-plane" style="margin-right: -4px;"></i></button>
        </div>

        <div>
            <!-- Article 1 -->
            <article class="article-card animate-fade-in-up delay-100">
                <div class="article-image">
                    <img src="https://images.unsplash.com/photo-1499209974431-9dddcece7f88?q=80&w=1470&auto=format&fit=crop" alt="Article 1">
                    <div class="article-badge">تطوير الذات</div>
                </div>
                <div class="article-content">
                    <h2 class="article-title">كيف تبنين روتيناً صباحياً يعزز طاقتك الإيجابية؟</h2>
                    <p class="article-desc">الصباح هو المساحة الأكثر سكوناً في يومك. في هذا المقال، نستعرض خطوات عملية لبناء طقوس صباحية هادئة تساعدك على التركيز والإنتاجية بنعومة وحب...</p>
                    <div class="article-footer">
                        <div class="action-buttons">
                            <button><i class="fa-regular fa-heart"></i> <span>١٢٤</span></button>
                            <button><i class="fa-regular fa-comment"></i> <span>١٨</span></button>
                        </div>
                        <a href="article.php?id=1" class="btn-read-more">اقرئي المزيد</a>
                    </div>
                </div>
            </article>

            <!-- Article 2 -->
            <article class="article-card animate-fade-in-up delay-200">
                <div class="article-image">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1470&auto=format&fit=crop" alt="Article 2">
                    <div class="article-badge">ريادة أعمال</div>
                </div>
                <div class="article-content">
                    <h2 class="article-title">رحلة من الفكرة إلى التنفيذ: قصة نجاح ملهمة</h2>
                    <p class="article-desc">تحدثنا مع رائدة الأعمال براءة العتيبي حول التحديات التي واجهتها في إطلاق مشروعها التقني وكيف استطاعت بناء فريق عمل متناغم يشاركها الشغف...</p>
                    <div class="article-footer">
                        <div class="action-buttons">
                            <button class="active"><i class="fa-solid fa-heart"></i> <span>٢٥٦</span></button>
                            <button><i class="fa-regular fa-comment"></i> <span>٤٢</span></button>
                        </div>
                        <a href="article.php?id=2" class="btn-read-more">اقرئي المزيد</a>
                    </div>
                </div>
            </article>
        </div>

        <div style="display: flex; justify-content: center; margin-top: 3rem; margin-bottom: 2rem;">
            <button class="btn-outline animate-fade-in-up delay-300" style="display: flex; align-items: center; gap: 0.75rem; background: #FFFFFF;">
                تحميل المزيد 
                <i class="fa-solid fa-chevron-down"></i>
            </button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
