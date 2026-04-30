<?php include 'includes/header.php'; ?>

<style>
/* Index Specific Styles - Polished, Feminine & Clean */

/* Hero Section */
.hero-section {
  padding: 6rem 0;
  background: linear-gradient(180deg, var(--surface-container-high) 0%, var(--surface) 100%);
  position: relative;
  overflow: hidden;
  min-height: calc(100vh - 80px);
  display: flex;
  align-items: center;
}

.hero-layout {
  display: grid;
  grid-template-columns: 1fr;
  gap: 3rem;
  align-items: center;
  position: relative;
  z-index: 10;
}

@media (min-width: 992px) {
  .hero-layout { 
    grid-template-columns: 1.2fr 0.8fr; 
    gap: 5rem; 
  }
}

.hero-title {
  font-size: 2.5rem;
  font-weight: 900;
  line-height: 1.3;
  color: var(--primary-dark);
  margin-bottom: 1.5rem;
  font-family: var(--font-headline);
}

@media (min-width: 768px) {
  .hero-title { font-size: 4rem; line-height: 1.2; }
}

.hero-highlight {
  background: var(--primary-gradient);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  position: relative;
  display: inline-block;
  padding: 0 0.2rem;
}

.hero-highlight::after {
  content: '';
  position: absolute;
  bottom: 8px; 
  right: 0;
  width: 100%; 
  height: 15px;
  background-color: var(--primary-container);
  z-index: -1;
  border-radius: 8px;
  opacity: 0.6;
}

.hero-desc {
  font-size: 1.15rem;
  color: var(--secondary);
  margin-bottom: 2.5rem;
  max-width: 35rem;
  line-height: 1.8;
}

/* Image container styling - Soft & Elegant */
.hero-image-container {
    position: relative;
    border-radius: 2rem;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(155, 98, 112, 0.15); /* Soft pinkish shadow */
    transition: var(--transition-smooth);
}

.hero-image-container::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.5);
    pointer-events: none;
}

.hero-image-container img {
    width: 100%;
    height: auto;
    max-height: 600px;
    object-fit: cover;
    display: block;
    transition: transform 0.7s ease;
}

.hero-image-container:hover img {
    transform: scale(1.03);
}

/* Pillar section */
.pillars-section {
  padding: 6rem 0;
  background-color: var(--surface);
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-title {
    font-size: 2.5rem;
    color: var(--primary-dark);
    font-weight: 900;
    font-family: var(--font-headline);
    margin-bottom: 1rem;
}

@media (min-width: 768px) {
  .section-title { font-size: 3rem; }
}

.section-desc {
    color: var(--secondary);
    max-width: 45rem;
    margin: 0 auto;
    font-size: 1.125rem;
    line-height: 1.8;
}

.pillars-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

@media (min-width: 768px) {
  .pillars-grid { grid-template-columns: repeat(2, 1fr); gap: 2.5rem; }
}

@media (min-width: 1024px) {
  .pillars-grid { grid-template-columns: repeat(4, 1fr); gap: 2rem; }
}

.pillar-card {
  background-color: #ffffff;
  padding: 2.5rem 2rem;
  border-radius: 1.5rem;
  border: 1px solid rgba(0,0,0,0.04);
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.pillar-card::before {
  content: '';
  position: absolute;
  top: 0; right: 0; width: 100%; height: 4px;
  background: var(--primary-gradient);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.pillar-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 30px rgba(155, 98, 112, 0.08);
  border-color: rgba(197, 58, 98, 0.1);
}

.pillar-card:hover::before {
  opacity: 1;
}

.pillar-icon-wrapper {
  width: 4rem;
  height: 4rem;
  background-color: var(--surface-container);
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
  transition: all 0.3s ease;
  color: var(--primary);
}

.pillar-card:hover .pillar-icon-wrapper {
  background: var(--primary);
  color: white;
  transform: scale(1.05);
}

.pillar-icon-wrapper i {
  font-size: 1.75rem;
}

.pillar-title {
  font-size: 1.35rem;
  font-weight: 800;
  color: var(--primary-dark);
  margin-bottom: 1rem;
  font-family: var(--font-headline);
}

.pillar-desc {
  color: var(--secondary);
  margin-bottom: 1.5rem;
  line-height: 1.7;
  font-size: 0.95rem;
  flex-grow: 1; /* Pushes the link to the bottom */
}

.pillar-link {
    color: var(--primary);
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    transition: gap 0.3s ease;
}

.pillar-card:hover .pillar-link {
    gap: 0.8rem;
}

/* Decorative circle */
.bg-circle {
  position: absolute;
  width: 600px;
  height: 600px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(197, 58, 98, 0.05) 0%, rgba(255,255,255,0) 70%);
  top: -100px;
  right: -100px;
  z-index: 0;
}
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="bg-circle"></div>
    <div class="container hero-layout">
        
        <!-- Text content -->
        <div class="animate-fade-in-up">
            <div class="badge delay-100" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(197, 58, 98, 0.1); color: var(--primary); padding: 6px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; margin-bottom: 1.5rem;">
                <span class="pulse-dot" style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; display: inline-block;"></span>
                مساحتك الخاصة، بأنوثتك
            </div>
            
            <h1 class="hero-title animate-fade-in-up delay-200">
                ملاذكِ الآمن <br />
                <span class="hero-highlight">للنمو والدعم</span>
            </h1>
            
            <p class="hero-desc animate-fade-in-up delay-300">
                نحن نؤمن بأن كل امرأة تستحق مساحة هادئة تكتشف فيها قوتها، وتنمي فيها ذاتها، وتجد الدعم الذي تحتاجه في رحلتها نحو التألق والتميز في بيئة خالية من الأحكام.
            </p>
            
            <div class="flex gap-4 animate-fade-in-up delay-300" style="flex-wrap: wrap; margin-top: 2rem;">
                <a href="register.php" class="btn-primary" style="text-decoration: none; padding: 12px 30px; font-size: 1.1rem;">ابدئي رحلتكِ الآن</a>
                <a href="about.php" class="btn-outline" style="text-decoration: none; padding: 12px 30px; font-size: 1.1rem;">اكتشفي برامجنا</a>
            </div>
        </div>
        
        <!-- Image content -->
        <div class="hidden-mobile animate-fade-in-up delay-200">
            <div class="hero-image-container">
                <img src="assets/images/womanPic.jpg" alt="امرأة تتأمل في هدوء - ملاذ">
            </div>
        </div>
        
    </div>
</section>

<!-- Pillars Section -->
<section class="pillars-section">
    <div class="container">
        <div class="section-header animate-fade-in-up">
            <h2 class="section-title">ركائز ملاذ الأربعة</h2>
            <p class="section-desc">بنينا ملاذ على أسس متينة تغطي كافة جوانب حياتك لضمان تجربة نمو متكاملة، آمنة، ومصممة خصيصاً لكِ.</p>
        </div>
        
        <div class="pillars-grid">
            <!-- Pillar 1 -->
            <div class="pillar-card animate-fade-in-up delay-100">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-seedling"></i>
                </div>
                <h3 class="pillar-title">صحتكِ أولاً</h3>
                <p class="pillar-desc">برامج متكاملة للعناية بصحتكِ الجسدية، تغذيتكِ، وأسلوب حياتكِ الصحي بحب واهتمام.</p>
                <a href="community.php?id=health" class="pillar-link">اكتشفي المزيد <i class="fa-solid fa-arrow-left"></i></a>
            </div>
            
            <!-- Pillar 2 -->
            <div class="pillar-card animate-fade-in-up delay-200">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <h3 class="pillar-title">سكينتكِ النفسية</h3>
                <p class="pillar-desc">جلسات استشارية ومجتمعات آمنة للتعبير عن الذات وتجاوز التحديات النفسية بكل طمأنينة وسرية.</p>
                <a href="community.php?id=psychology" class="pillar-link">اكتشفي المزيد <i class="fa-solid fa-arrow-left"></i></a>
            </div>
            
            <!-- Pillar 3 -->
            <div class="pillar-card animate-fade-in-up delay-300">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h3 class="pillar-title">نوركِ الروحي</h3>
                <p class="pillar-desc">محتوى ديني وسطي يعزز من قيمكِ الروحية ويوفر لكِ الفهم العميق لمكانتكِ السامية المجيدة.</p>
                <a href="community.php?id=religion" class="pillar-link">اكتشفي المزيد <i class="fa-solid fa-arrow-left"></i></a>
            </div>
            
            <!-- Pillar 4 -->
            <div class="pillar-card animate-fade-in-up delay-400">
                <div class="pillar-icon-wrapper">
                    <i class="fa-solid fa-star"></i>
                </div>
                <h3 class="pillar-title">طموحكِ بلا حدود</h3>
                <p class="pillar-desc">تمكين أكاديمي ومهني يساعدكِ في تحقيق أهدافكِ الدراسية وتطوير شغفكِ وإمكانياتك.</p>
                <a href="community.php?id=academic" class="pillar-link">اكتشفي المزيد <i class="fa-solid fa-arrow-left"></i></a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>