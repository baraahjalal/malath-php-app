<?php include 'includes/header.php'; ?>

<style>
/* Index Specific Styles - Polished & Feminine */
.hero-title {
  font-size: 3rem;
  font-weight: 900;
  line-height: 1.2;
  color: var(--primary-dark);
  margin-bottom: 1.5rem;
  font-family: var(--font-headline);
}

.hero-highlight {
  background: var(--primary-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  position: relative;
  display: inline-block;
  padding: 0 0.5rem;
}

.hero-highlight::after {
  content: '';
  position: absolute;
  bottom: 5px; 
  right: 0; /* RTL */
  width: 100%; 
  height: 25%;
  background-color: var(--primary-container);
  z-index: -1;
  border-radius: 4px;
}

@media (min-width: 768px) {
  .hero-title { font-size: 4.5rem; }
}

.hero-desc {
  font-size: 1.25rem;
  color: var(--secondary);
  margin-bottom: 2.5rem;
  max-width: 38rem;
  line-height: 1.8;
}

/* Image container styling - Soft & Floating */
.hero-image-container {
    position: relative;
    border-radius: 2rem 4rem 2rem 4rem;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    transform: rotate(2deg) translateY(0);
    transition: var(--transition-smooth);
    border: 8px solid rgba(255,255,255,0.7);
}

.hero-image-container:hover {
    transform: rotate(0deg) translateY(-10px);
    box-shadow: 0 30px 60px -15px rgba(197, 58, 98, 0.25);
}

.hero-image-container img {
    width: 100%;
    height: 600px;
    object-fit: cover;
    transition: var(--transition-smooth);
}

.hero-image-container:hover img {
    transform: scale(1.05);
}

/* Pillar section */
.pillars-section {
  padding: 8rem 0;
  background-color: var(--surface);
  position: relative;
}

.pillars-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2.5rem;
}

@media (min-width: 768px) {
  .pillars-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (min-width: 1024px) {
  .pillars-grid { grid-template-columns: repeat(4, 1fr); }
}

.pillar-card {
  background-color: #ffffff;
  padding: 3rem 2rem;
  border-radius: 1.5rem;
  border: 1px solid var(--outline-variant);
  transition: var(--transition-smooth);
  box-shadow: var(--shadow-sm);
  position: relative;
  overflow: hidden;
  z-index: 1;
}

.pillar-card::before {
  content: '';
  position: absolute;
  top: 0; right: 0; width: 100%; height: 4px;
  background: var(--primary-gradient);
  opacity: 0;
  transition: var(--transition-smooth);
}

.pillar-card:hover {
  transform: translateY(-10px);
  box-shadow: var(--shadow-md);
  border-color: var(--outline);
}

.pillar-card:hover::before {
  opacity: 1;
}

.pillar-icon-wrapper {
  width: 4.5rem;
  height: 4.5rem;
  background-color: var(--surface-container);
  border-radius: 1.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 2rem;
  transition: var(--transition-smooth);
  color: var(--primary);
}

.pillar-card:hover .pillar-icon-wrapper {
  background: var(--primary-gradient);
  color: white;
  transform: rotate(5deg) scale(1.1);
  box-shadow: var(--shadow-sm);
}

.pillar-icon-wrapper i {
  font-size: 2rem;
}

.pillar-title {
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--primary-dark);
  margin-bottom: 1rem;
  font-family: var(--font-headline);
}

.pillar-desc {
  color: var(--on-surface);
  margin-bottom: 1.5rem;
  line-height: 1.7;
}

.hero-gradient-bg {
  background: linear-gradient(180deg, var(--surface-container-high) 0%, var(--surface) 100%);
  position: relative;
  overflow: hidden;
}

/* Decorative circle */
.bg-circle {
  position: absolute;
  width: 600px;
  height: 600px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(228,94,134,0.1) 0%, rgba(255,255,255,0) 70%);
  top: -100px;
  right: -200px;
  z-index: 0;
}
</style>

<!-- Hero Section -->
<section class="hero-gradient-bg" style="min-height: 870px; display: flex; align-items: center;">
    <div class="bg-circle"></div>
    <div class="container hero-content animate-fade-in-up">
        <!-- Text content -->
        <div style="z-index: 10;">
            <div class="badge delay-100">
                <span class="pulse-dot"></span>
                مساحتك الخاصة، بأنوثتك
            </div>
            
            <h1 class="hero-title animate-fade-in-up delay-200">
                ملاذكِ الآمن <br />
                <span class="hero-highlight">للنمو والدعم</span>
            </h1>
            
            <p class="hero-desc animate-fade-in-up delay-300">
                نحن نؤمن بأن كل امرأة تستحق مساحة هادئة تكتشف فيها قوتها، وتنمي فيها ذاتها، وتجد الدعم الذي تحتاجه في رحلتها نحو التألق والتميز في بيئة خالية من الأحكام.
            </p>
            
            <div class="flex gap-4 animate-fade-in-up delay-300" style="flex-wrap: wrap;">
                <button class="btn-primary">ابدئي رحلتكِ الآن</button>
                <button class="btn-outline">اكتشفي برامجنا</button>
            </div>
        </div>
        
        <!-- Image content (hidden on mobile) -->
        <div class="hidden-mobile animate-fade-in-up delay-200" style="position: relative; z-index: 10;">
            <div class="hero-image-container">
                <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=1288&auto=format&fit=crop" alt="Empowered Woman">
            </div>
        </div>
    </div>
</section>

<!-- Pillars Section -->
<section class="pillars-section">
    <div class="container">
        <div class="animate-fade-in-up" style="text-align: center; margin-bottom: 5rem;">
            <h2 class="text-primary font-black font-headline" style="font-size: 3.5rem; margin-bottom: 1rem;">ركائز ملاذ الأربعة</h2>
            <p class="text-stone-500" style="max-width: 42rem; margin: 0 auto; font-size: 1.125rem;">بنينا ملاذ على أسس متينة تغطي كافة جوانب حياتك لضمان تجربة نمو متكاملة، آمنة، ومصممة خصيصاً لكِ.</p>
        </div>
        
        <div class="pillars-grid">
            <div class="pillar-card animate-fade-in-up delay-100">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-seedling"></i>
                </div>
                <h3 class="pillar-title">صحتكِ أولاً</h3>
                <p class="pillar-desc">برامج متكاملة للعناية بصحتكِ الجسدية، تغذيتكِ، وأسلوب حياتكِ الصحي بحب واهتمام.</p>
                <a href="#" class="text-primary font-bold" style="text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">اكتشفي المزيد <i class="fa-solid fa-arrow-left"></i></a>
            </div>
            
            <div class="pillar-card animate-fade-in-up delay-200" style="margin-top: 2rem;">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <h3 class="pillar-title">سكينتكِ النفسية</h3>
                <p class="pillar-desc">جلسات استشارية ومجتمعات آمنة للتعبير عن الذات وتجاوز التحديات النفسية بكل طمأنينة وسرية.</p>
                <a href="#" class="text-primary font-bold" style="text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">اكتشفي المزيد <i class="fa-solid fa-arrow-left"></i></a>
            </div>
            
            <div class="pillar-card animate-fade-in-up delay-300">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h3 class="pillar-title">نوركِ الروحي</h3>
                <p class="pillar-desc">محتوى ديني وسطي يعزز من قيمكِ الروحية ويوفر لكِ الفهم العميق لمكانتكِ السامية المجيدة.</p>
                <a href="#" class="text-primary font-bold" style="text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">اكتشفي المزيد <i class="fa-solid fa-arrow-left"></i></a>
            </div>
            
            <div class="pillar-card animate-fade-in-up delay-100" style="margin-top: 2rem;">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-star"></i>
                </div>
                <h3 class="pillar-title">طموحكِ بلا حدود</h3>
                <p class="pillar-desc">تمكين أكاديمي ومهني يساعدكِ في تحقيق أهدافكِ الدراسية وتطوير شغفكِ وإمكانياتك.</p>
                <a href="#" class="text-primary font-bold" style="text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">اكتشفي المزيد <i class="fa-solid fa-arrow-left"></i></a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
