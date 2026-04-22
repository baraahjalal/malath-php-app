<?php include 'includes/header.php'; ?>

<style>
/* Refined Article Styles */
.article-container {
  padding: 3rem 1.5rem 5rem;
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr;
  gap: 4rem;
}

@media (min-width: 1024px) {
  .article-container {
    grid-template-columns: 8.5fr 3.5fr;
  }
}

.article-header {
  margin-bottom: 3rem;
}

.article-tags {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.tag {
  background-color: var(--primary-container);
  color: var(--primary-dark);
  padding: 0.35rem 1.25rem;
  border-radius: 2rem;
  font-size: 0.875rem;
  font-weight: 800;
  font-family: var(--font-headline);
  box-shadow: var(--shadow-sm);
}

.article-title {
  font-family: var(--font-headline);
  font-size: 2.5rem;
  font-weight: 900;
  line-height: 1.25;
  color: var(--primary-dark);
  margin-bottom: 2rem;
}

@media (min-width: 768px) {
  .article-title {
    font-size: 4rem;
  }
}

.author-info {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1.25rem;
  background-color: #FFFFFF;
  border-radius: 1rem;
  margin-bottom: 3rem;
  border: 1px solid var(--outline-variant);
  box-shadow: var(--shadow-sm);
}

.author-avatar {
  width: 4rem;
  height: 4rem;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--primary-container);
}

.hero-image {
  width: 100%;
  height: 480px;
  border-radius: 1.5rem;
  object-fit: cover;
  margin-bottom: 4rem;
  box-shadow: var(--shadow-lg);
  border: 1px solid rgba(255,255,255,0.6);
}

.article-body {
  font-size: 1.15rem;
  color: var(--on-surface);
  line-height: 1.9;
  font-family: var(--font-body);
}

.article-body p {
  margin-bottom: 1.75rem;
}

.article-body h2 {
  font-family: var(--font-headline);
  font-size: 2rem;
  font-weight: 800;
  color: var(--primary-dark);
  margin-top: 3.5rem;
  margin-bottom: 1.5rem;
  position: relative;
  display: inline-block;
}

.article-body h2::after {
  content: '';
  position: absolute;
  bottom: -4px; right: 0;
  width: 40%; height: 3px;
  background: var(--primary-gradient);
  border-radius: 2px;
}

.blockquote-custom {
  margin: 3.5rem 0;
  padding: 2.5rem 2.5rem 2.5rem 3rem;
  background: linear-gradient(135deg, var(--surface-container) 0%, #FFFFFF 100%);
  border-radius: 1.5rem;
  border-right: 6px solid var(--primary);
  box-shadow: var(--shadow-md);
  position: relative;
}

.blockquote-custom::before {
  content: '\f10d'; /* FontAwesome quote right */
  font-family: 'Font Awesome 6 Free';
  font-weight: 900;
  position: absolute;
  top: 1rem; right: 1.5rem;
  font-size: 3rem;
  color: var(--primary-container);
  opacity: 0.5;
}

.blockquote-custom p {
  font-family: var(--font-headline);
  font-size: 1.75rem;
  font-weight: 800;
  color: var(--primary-dark);
  line-height: 1.5;
  margin: 0;
  position: relative;
  z-index: 1;
}

.social-actions {
  margin-top: 5rem;
  padding-top: 2rem;
  border-top: 1px solid var(--outline-variant);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.social-btn {
  background: none;
  border: none;
  color: var(--secondary);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 1.125rem;
  font-weight: 700;
  font-family: var(--font-body);
  transition: var(--transition-fast);
}

.social-btn:hover { color: var(--primary); transform: translateY(-2px); }
.social-btn.active { color: var(--primary); }

.author-sidebar-card {
  background-color: #FFFFFF;
  border-radius: 1.5rem;
  padding: 2.5rem 2rem;
  text-align: center;
  margin-bottom: 2.5rem;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--outline-variant);
}

.author-sidebar-img {
  width: 7rem;
  height: 7rem;
  border-radius: 50%;
  border: 4px solid var(--surface-container-high);
  box-shadow: var(--shadow-md);
  margin: 0 auto 1.5rem;
  object-fit: cover;
}

.related-articles {
  background-color: #FFFFFF;
  border-radius: 1.5rem;
  padding: 2rem;
  margin-bottom: 2.5rem;
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--outline-variant);
}

.related-articles h4 {
  font-family: var(--font-headline);
  font-size: 1.35rem;
  font-weight: 800;
  color: var(--primary-dark);
  border-right: 4px solid var(--primary);
  padding-right: 1rem;
  margin-bottom: 2rem;
}

.related-article-item {
  display: flex;
  gap: 1.25rem;
  align-items: flex-start;
  margin-bottom: 1.5rem;
  text-decoration: none;
  transition: var(--transition-fast);
}

.related-article-item:hover {
  transform: translateX(-5px);
}

.related-article-item:hover h5 {
  color: var(--primary);
}

.related-article-item img {
  width: 5.5rem;
  height: 5.5rem;
  border-radius: 0.75rem;
  object-fit: cover;
  box-shadow: var(--shadow-sm);
}

.newsletter-box {
  background: var(--primary-gradient);
  color: white;
  border-radius: 1.5rem;
  padding: 2.5rem 2rem;
  box-shadow: var(--shadow-primary);
  position: relative;
  overflow: hidden;
}

.newsletter-box::before {
  content: '';
  position: absolute;
  top: -50px; right: -50px;
  width: 150px; height: 150px;
  background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
  border-radius: 50%;
}

.newsletter-box input {
  width: 100%;
  background-color: rgba(255,255,255,0.15);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 2rem;
  padding: 0.85rem 1.25rem;
  color: white;
  margin-bottom: 1rem;
  font-family: var(--font-body);
  transition: var(--transition-fast);
}

.newsletter-box input:focus {
  outline: none;
  background-color: rgba(255,255,255,0.25);
  border-color: white;
}

.newsletter-box input::placeholder { color: rgba(255,255,255,0.8); }

.newsletter-box button {
  width: 100%;
  background-color: white;
  color: var(--primary-dark);
  border: none;
  padding: 0.85rem;
  border-radius: 2rem;
  font-weight: 800;
  font-family: var(--font-headline);
  font-size: 1.05rem;
  cursor: pointer;
  transition: var(--transition-fast);
  box-shadow: var(--shadow-sm);
}

.newsletter-box button:hover { 
  background-color: var(--surface-container); 
  transform: translateY(-2px);
}
</style>

<div class="article-container">
    <!-- Main Content -->
    <div style="order: 1;" class="animate-fade-in-up">
        <header class="article-header">
            <div class="article-tags">
                <span class="tag">صحة نفسية</span>
                <span style="color: var(--secondary); font-size: 0.95rem; font-weight: 500;">١٥ مايو ٢٠٢٤ • ٨ دقائق قراءة</span>
            </div>
            
            <h1 class="article-title">فن الهدوء في عالم متسارع: كيف تجدين ملاذك الخاص بكِ؟</h1>
            
            <div class="author-info">
                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=256&auto=format&fit=crop" alt="Author" class="author-avatar">
                <div>
                    <div style="font-weight: 800; font-family: var(--font-headline); color: var(--primary-dark); font-size: 1.15rem;">د. ليلى المنصور</div>
                    <div style="font-size: 0.95rem; color: var(--secondary);">مستشارة نفسية وكاتبة في شؤون المرأة</div>
                </div>
            </div>
            
            <img src="https://images.unsplash.com/photo-1499209974431-9dddcece7f88?q=80&w=1470&auto=format&fit=crop" alt="Hero" class="hero-image">
        </header>

        <article class="article-body">
            <p>في خضم الضجيج الرقمي والمسؤوليات المتراكمة، ننسى أحياناً أن الهدوء ليس ترفاً، بل هو ضرورة قصوى للحفاظ على توازننا النفسي. الهدوء الذي نتحدث عنه هنا ليس مجرد صمت خارجي، بل هو حالة من السلام الداخلي الهادئ تسمح لنا برؤية الأمور بوضوح واتخاذ قرارات نابعة من وعي حقيقي واهتمام بالذات.</p>
            
            <h2>لماذا نحتاج إلى الملاذ؟</h2>
            <p>أظهرت الدراسات الحديثة أن العقل البشري يحتاج إلى فترات من "فك الارتباط" الكامل ليعيد ترميم الخلايا العصبية المسؤولة عن التركيز والإبداع. بالنسبة للمرأة العصرية، التي توازن بين طموحاتها ودورها المحوري في الأسرة، يصبح البحث عن هذا الملاذ رحلة استكشافية للذات تُثمر عن صحة نفسية متوهجة.</p>
            
            <div class="blockquote-custom">
                <p>"الهدوء هو القوة التي تمنحك السيادة على عالمك الداخلي، حينها فقط يتوقف العالم الخارجي عن كونه مصدراً للتوتر، وينبض قلبك بالسكينة."</p>
            </div>
            
            <p>لبناء ملاذك الخاص، ابدأي بخطوات بسيطة:</p>
            <ul style="list-style-type: none; margin-bottom: 3rem; padding-right: 0;">
                <li style="margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 1rem;">
                  <div style="color: var(--primary); margin-top: 4px;"><i class="fa-solid fa-heart"></i></div> 
                  <div>تخصيص ١٥ دقيقة صباحية هادئة بدون أي أجهزة إلكترونية لتبدأي يومك بصفاء.</div>
                </li>
                <li style="margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 1rem;">
                  <div style="color: var(--primary); margin-top: 4px;"><i class="fa-solid fa-heart"></i></div> 
                  <div>ممارسة التنفس الواعي (الشهيق والزفير العميق) عند الشعور بضغط المهام المتسارعة.</div>
                </li>
                <li style="margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 1rem;">
                  <div style="color: var(--primary); margin-top: 4px;"><i class="fa-solid fa-heart"></i></div> 
                  <div>خلق ركن مخصص لكِ في المنزل يبعث على الراحة البصرية، زينيه بالشموع أو النباتات العطرية الخفيفة.</div>
                </li>
            </ul>
            
            <p>تذكري دائماً أن العناية بنفسك ليست أنانية على الإطلاق، بل هي أفضل استثمار يمكنك تقديمه لمن تحبين. فعندما تملأين بئرك الخاص بالحب والهدوء، تفيض طاقتك إشراقاً وعطاءً لكل من حولك.</p>
        </article>

        <div class="social-actions" style="margin-bottom: 2rem;">
            <div style="display: flex; gap: 2rem;">
                <button class="social-btn active"><i class="fa-solid fa-heart"></i> ١,٢٤٠</button>
                <button class="social-btn"><i class="fa-regular fa-comment"></i> ٤٥</button>
                <button class="social-btn"><i class="fa-regular fa-bookmark"></i> حفظ</button>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 1rem; color: var(--secondary); font-weight: 700;">شاركيه بحب:</span>
                <button class="social-btn" style="background-color: var(--surface-container); width: 3rem; height: 3rem; border-radius: 50%; justify-content: center; box-shadow: var(--shadow-sm);"><i class="fa-solid fa-share-nodes"></i></button>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <aside style="order: 2;" class="animate-fade-in-up delay-200">
        <div class="author-sidebar-card">
            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=256&auto=format&fit=crop" alt="Author" class="author-sidebar-img">
            <h4 class="font-headline font-bold" style="font-size: 1.35rem; color: var(--primary-dark);">د. ليلى المنصور</h4>
            <p style="font-size: 0.95rem; color: var(--secondary); margin: 0.75rem 0 2rem; line-height: 1.6;">متخصصة في تمكين المرأة والتوازن النفسي، تسعى لنشر الوعي بحب واهتمام بخبرة تمتد لأكثر من ١٠ سنوات.</p>
            <button class="btn-outline" style="width: 100%; border-radius: 2rem; padding: 0.75rem;">متابعة الكاتبة</button>
        </div>

        <div class="related-articles">
            <h4>مواضيع ذات صلة</h4>
            
            <a href="#" class="related-article-item">
                <img src="https://images.unsplash.com/photo-1512413913410-6bc14e41ba92?q=80&w=256&auto=format&fit=crop" alt="Related">
                <div>
                    <h5 style="color: var(--on-surface); margin-bottom: 0.35rem; font-family: var(--font-headline); font-size: 1.1rem; line-height: 1.4;">العناية بالذات في زحام العمل</h5>
                    <span style="font-size: 0.85rem; color: var(--secondary); font-weight: 600;">٥ دقائق قراءة</span>
                </div>
            </a>
            
            <a href="#" class="related-article-item" style="margin-bottom: 0;">
                <img src="https://images.unsplash.com/photo-1506126613408-eca07ce68773?q=80&w=256&auto=format&fit=crop" alt="Related">
                <div>
                    <h5 style="color: var(--on-surface); margin-bottom: 0.35rem; font-family: var(--font-headline); font-size: 1.1rem; line-height: 1.4;">خطوات التأمل الواعي للمبتدئات</h5>
                    <span style="font-size: 0.85rem; color: var(--secondary); font-weight: 600;">١٢ دقيقة قراءة</span>
                </div>
            </a>
        </div>

        <div class="newsletter-box">
            <h4 class="font-headline font-bold" style="font-size: 1.75rem; margin-bottom: 1rem; position: relative; z-index: 10;">انضمي لنشرتنا</h4>
            <p style="font-size: 0.95rem; opacity: 0.95; margin-bottom: 2rem; line-height: 1.6; position: relative; z-index: 10;">تصلكِ أفضل المقالات والنصائح النفسية والاجتماعية التي تلامس قلبك أسبوعياً.</p>
            <form action="#" style="position: relative; z-index: 10;">
                <input type="email" placeholder="بريدك الإلكتروني" required>
                <button type="submit">اشتركي الآن</button>
            </form>
        </div>
    </aside>
</div>

<?php include 'includes/footer.php'; ?>
