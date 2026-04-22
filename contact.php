<?php
$current_page = 'contact.php';
include 'includes/header.php';
?>

<section style="padding: 10rem 0 5rem; background-color: var(--surface-container-high);">
    <div class="container" style="text-align: center;">
        <h1 class="font-headline font-black text-primary" style="font-size: 3rem; margin-bottom: 1rem;">تواصل معنا</h1>
        <p class="text-secondary" style="font-size: 1.25rem; max-width: 600px; margin: 0 auto;">يسعدنا دائماً الاستماع إليكِ والإجابة على استفساراتك.</p>
    </div>
</section>

<section style="padding: 5rem 0;">
    <div class="container">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
             <form>
                  <div class="input-group">
                       <label class="input-label" for="name">الاسم</label>
                       <input type="text" id="name" class="form-control" placeholder="أدخلي اسمك">
                  </div>
                  <div class="input-group">
                       <label class="input-label" for="email">البريد الإلكتروني</label>
                       <input type="email" id="email" class="form-control" placeholder="أدخلي بريدك الإلكتروني">
                  </div>
                  <div class="input-group">
                       <label class="input-label" for="message">الرسالة</label>
                       <textarea id="message" class="form-control" rows="5" placeholder="كيف يمكننا مساعدتك؟"></textarea>
                  </div>
                  <button type="button" class="btn-primary" style="width: 100%;">إرسال الرسالة</button>
             </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
