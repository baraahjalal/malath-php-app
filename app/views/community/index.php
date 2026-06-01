<?php include ROOT_PATH . '/includes/header.php'; ?>

<style>
.community-header {
    background: linear-gradient(180deg, var(--surface-container-high) 0%, var(--surface) 100%);
    padding: 3rem 0 2rem;
    text-align: center;
    border-bottom: 1px solid rgba(197, 58, 98, 0.05);
}
.community-title {
    font-size: 2.25rem;
    font-weight: 900;
    color: var(--primary-dark);
    font-family: var(--font-headline);
    margin-bottom: 1rem;
}
.community-tabs-container {
    display: flex;
    justify-content: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 2rem;
}
.community-tab {
    padding: 0.75rem 1.5rem;
    border-radius: 2rem;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    color: var(--secondary);
    background-color: var(--surface-container);
    transition: all 0.3s ease;
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.community-tab:hover { background-color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.05); color: var(--primary); }
.community-tab.active { background: var(--primary-gradient); color: white; box-shadow: 0 4px 15px rgba(197,58,98,0.2); }
.community-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
    padding: 3rem 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
}
@media (min-width: 1024px) { .community-layout { grid-template-columns: 3.5fr 8.5fr; } }
.create-post-box {
    background-color: #ffffff;
    border-radius: 1.5rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    border: 1px solid var(--outline-variant);
    margin-bottom: 2.5rem;
}
.create-post-top { display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1rem; }
.create-post-input {
    flex-grow: 1;
    background-color: var(--surface-container-high);
    border-radius: 1.2rem;
    padding: 1.2rem 1.5rem;
    color: var(--on-surface);
    border: 1px solid transparent;
    transition: all 0.3s ease;
    font-family: inherit;
    resize: none;
    min-height: 80px;
    font-size: 1rem;
}
.create-post-input:focus { background-color: #ffffff; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(197,58,98,0.08); outline: none; }
.create-post-actions { display: flex; flex-wrap: wrap; gap: 0.8rem; align-items: center; padding-top: 1.2rem; border-top: 1px solid var(--outline-variant); }
.create-post-select, .create-post-text {
    background-color: var(--surface-container);
    border: 1px solid transparent;
    padding: 0.6rem 1.2rem;
    border-radius: 2rem;
    font-size: 0.9rem;
    color: var(--secondary);
    font-family: inherit;
    transition: all 0.3s ease;
    outline: none;
}
.create-post-text { flex-grow: 1; min-width: 150px; }
.create-post-select:focus, .create-post-text:focus { background-color: #ffffff; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(197,58,98,0.08); }
.create-post-btn { padding: 0.6rem 1.8rem; border-radius: 2rem; font-weight: 800; margin-right: auto; }
.post-success-msg { background-color: #ecfdf5; color: #059669; padding: 0.8rem 1.2rem; border-radius: 1rem; margin-bottom: 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; }
.feed-card {
    background-color: #ffffff;
    border-radius: 1.5rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    border: 1px solid rgba(0,0,0,0.04);
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    scroll-margin-top: 100px;
}
.feed-card:hover { box-shadow: 0 10px 30px rgba(155,98,112,0.08); }
.post-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
.user-info { display: flex; align-items: center; gap: 1rem; }
.user-avatar { width: 3rem; height: 3rem; border-radius: 50%; object-fit: cover; }
.user-name { font-weight: 800; color: var(--primary-dark); font-family: var(--font-headline); text-decoration: none; }
.post-meta { font-size: 0.8rem; color: var(--secondary); margin-top: 0.2rem; }
.badge-type { padding: 0.3rem 0.8rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; }
.badge-question { background: #e0f2fe; color: #0284c7; }
.badge-vent { background: #fce7f3; color: #be185d; }
.badge-advice { background: #fef3c7; color: #d97706; }
.badge-article { background: var(--primary-container); color: var(--primary-dark); }
.post-content { color: var(--on-surface); line-height: 1.7; margin-bottom: 1.5rem; font-size: 1.05rem; }
.interaction-btn {
    background: none;
    border: none;
    color: var(--secondary);
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    padding: 0.4rem 0.8rem;
    border-radius: 2rem;
    font-family: inherit;
}
.interaction-btn:hover { background-color: var(--surface-container); color: var(--primary); }
.interaction-btn.active-like { color: #e11d48; }
.interaction-btn.active-like:hover { background-color: #ffe4e6; }
.interaction-btn.active-save { color: #0284c7; }
.interaction-btn.active-save:hover { background-color: #e0f2fe; }
.sidebar-widget {
    background-color: #ffffff;
    border-radius: 1.5rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    border: 1px solid rgba(0,0,0,0.04);
    margin-bottom: 2rem;
}
.widget-title { font-size: 1.2rem; font-weight: 800; color: var(--primary-dark); margin-bottom: 1.5rem; font-family: var(--font-headline); }
.community-list-item { display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid var(--outline-variant); text-decoration: none; color: var(--on-surface); transition: all 0.2s ease; }
.community-list-item:last-child { border-bottom: none; }
.community-list-item:hover { color: var(--primary); padding-right: 0.5rem; }
.rules-list { list-style: none; padding: 0; margin: 0; }
.rules-list li { position: relative; padding-right: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; color: var(--secondary); line-height: 1.6; }
.rules-list li::before { content: '•'; position: absolute; right: 0; color: var(--primary); font-weight: bold; }
.btn-join { background-color: var(--primary-container,#fce7f3); color: var(--primary-dark,#be185d); border: 1px solid transparent; padding: 0.4rem 1.2rem; border-radius: 2rem; font-size: 0.85rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; font-family: inherit; }
.btn-join:hover { background-color: var(--primary,#db2777); color: white; box-shadow: 0 4px 12px rgba(219,39,119,0.2); transform: translateY(-1px); }
.btn-leave { background-color: var(--surface-container,#f3f4f6); color: var(--secondary,#4b5563); border: 1px solid var(--outline-variant,#e5e7eb); padding: 0.4rem 1.2rem; border-radius: 2rem; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: inherit; }
.btn-leave:hover { background-color: #fee2e2; color: #ef4444; border-color: #fca5a5; }

/* Mobile Responsiveness for Community */
@media (max-width: 768px) {
    .community-title { font-size: 1.75rem; }
    .community-tabs-container { 
        flex-wrap: nowrap; 
        overflow-x: auto; 
        justify-content: flex-start; 
        padding-bottom: 0.5rem; 
        -webkit-overflow-scrolling: touch; 
        scroll-snap-type: x mandatory;
    }
    .community-tab { white-space: nowrap; scroll-snap-align: start; }
    .community-layout { padding: 1.5rem 1rem; gap: 2rem; }
    
    .create-post-top { gap: 0.75rem; }
    .create-post-actions { flex-direction: column; align-items: stretch; gap: 0.75rem; }
    .create-post-select, .create-post-text, .create-post-btn { width: 100%; margin: 0; text-align: center; }
    
    .feed-card { padding: 1.25rem 1rem; margin-bottom: 1.5rem; }
    .post-interaction-bar { flex-direction: column; gap: 1rem; align-items: stretch !important; }
    .post-interaction-bar > div { display: flex; justify-content: space-between; gap: 0.5rem; }
    .post-interaction-bar button { flex: 1; justify-content: center; padding: 0.6rem; border-radius: 1rem; background: var(--surface-container-high); }
}
</style>

<div class="community-header">
  <div class="container animate-fade-in-up">
    <h1 class="community-title">مجتمعات ملاذ</h1>
    <p>مساحتك الآمنة للتفاعل، التعلم، ومشاركة تجاربك في بيئة نسائية داعمة ومحفزة.</p>
    <div class="community-tabs-container">
      <a href="/malath-php-app/community?c=all" class="community-tab<?= $current_slug === 'all' ? ' active' : '' ?>">الأحدث</a>
      <?php foreach($communities as $c): ?>
        <a href="/malath-php-app/community?c=<?=htmlspecialchars($c['slug'])?>" class="community-tab<?= $current_slug === $c['slug'] ? ' active' : '' ?>">
          <?=htmlspecialchars($c['name'])?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="community-layout">
  <aside>
    <div class="sidebar-widget">
      <h3 class="widget-title">كل المجتمعات</h3>
      <?php foreach($communities as $c): ?>
        <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
          <a href="/malath-php-app/community?c=<?=htmlspecialchars($c['slug'])?>" class="community-list-item" style="flex:1;">
            <?=htmlspecialchars($c['name'])?>
          </a>
          <?php if($user_id): ?>
            <form method="post" action="/malath-php-app/community?c=<?=htmlspecialchars($current_slug)?>" style="display:inline;">
              <?php csrf_field(); ?>
              <input type="hidden" name="community_id" value="<?=$c['id']?>">
              <?php if (!in_array($c['id'], $user_communities)): ?>
                <button type="submit" name="join_community" class="btn-join">انضمام</button>
              <?php else: ?>
                <button type="submit" name="leave_community" class="btn-leave">انسحاب</button>
              <?php endif; ?>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="sidebar-widget">
      <h3 class="widget-title">مجتمعاتي (المنضمة لها)</h3>
      <?php if($user_id && $user_communities): ?>
        <?php foreach($communities as $c):
          if(!in_array($c['id'], $user_communities)) continue; ?>
          <a href="/malath-php-app/community?c=<?=htmlspecialchars($c['slug'])?>" class="community-list-item">
            <?=htmlspecialchars($c['name'])?>
          </a>
        <?php endforeach; ?>
      <?php elseif(!$user_id): ?>
        <p style="font-size:0.95rem; color:#a78;">سجلي دخولك لتخصيص تجربتك والانضمام للمجتمعات.</p>
        <a href="/malath-php-app/login?redirect=community.php" class="btn-outline">تسجيل الدخول</a>
      <?php else: ?>
        <div>لم تنضمي لأي مجتمع بعد.</div>
      <?php endif; ?>
    </div>

    <div class="sidebar-widget" style="background:var(--surface-container-high); border:none;">
      <h3 class="widget-title"><i class="fa-solid fa-shield-heart" style="color:var(--primary);"></i> مساحة آمنة</h3>
      <ul class="rules-list">
        <li>احترمي آراء الأخريات وتجنبي الأحكام.</li>
        <li>يُمنع نشر أي محتوى يسيء للأديان أو الأشخاص.</li>
        <li>حافظي على سرية الفضفضة والقصص المطروحة هنا.</li>
        <li>المقالات يجب أن تُنشر في مجتمعها الصحيح.</li>
      </ul>
    </div>
  </aside>

  <main>
    <?php if($user_id && $user_communities): ?>
      <div class="create-post-box animate-fade-in-up">
        <?php if($post_message): ?>
          <div class="post-success-msg"><i class="fa-solid fa-circle-check"></i> <?=htmlspecialchars($post_message)?></div>
        <?php endif; ?>
        <form method="post" action="/malath-php-app/community?c=<?=htmlspecialchars($current_slug)?>">
          <?php csrf_field(); ?>
          <div class="create-post-top">
            <img src="<?=htmlspecialchars($user_avatar ?: 'assets/images/default-avatar.png')?>" alt="Profile" class="user-avatar" style="width:3rem;height:3rem;">
            <textarea name="content" class="create-post-input" required placeholder="بم تفكرين؟ شاركي أفكارك، أسئلتك، أو تجربتك هنا..."></textarea>
          </div>
          <div class="create-post-actions">
            <select name="type" class="create-post-select" required>
              <option value="vent">🗣 فضفضة</option>
              <option value="advice">💡 طلب نصيحة</option>
              <option value="question">❓ سؤال</option>
            </select>
            <select name="post_community_id" class="create-post-select" required>
              <?php foreach($communities as $c): if(in_array($c['id'], $user_communities)): ?>
                <option value="<?=$c['id']?>"<?= ($current_community && $c['id']==$current_community['id']) ? ' selected' : '' ?>>
                  <?=htmlspecialchars($c['name'])?>
                </option>
              <?php endif; endforeach;?>
            </select>
            <input type="text" name="post_title" class="create-post-text" placeholder="عنوان المقال (اختياري)">
            <button type="submit" name="submit_post" class="btn-primary create-post-btn">نشر الآن</button>
          </div>
        </form>
      </div>
    <?php elseif($user_id): ?>
      <div class="create-post-box" style="text-align:center; opacity:.85;">
        <b>انضمي لمجتمع أولاً لتتمكني من النشر فيه</b>
      </div>
    <?php else: ?>
      <div class="create-post-box" style="text-align:center; padding: 2.5rem 1.5rem;">
        <h4 style="margin-bottom: 1.5rem; color: var(--secondary); font-weight: normal;">لتتمكني من النشر والتفاعل، يرجى تسجيل الدخول أو إنشاء حساب جديد.</h4>
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
          <a href="/malath-php-app/login?redirect=community.php" class="btn-primary" style="padding: 0.6rem 1.5rem; border-radius: 2rem; text-decoration: none; font-weight: bold;">تسجيل الدخول</a>
          <a href="/malath-php-app/register" class="btn-outline" style="padding: 0.6rem 1.5rem; border-radius: 2rem; border: 1px solid var(--primary); color: var(--primary); text-decoration: none; font-weight: bold; background: transparent;">إنشاء حساب جديد</a>
        </div>
      </div>
    <?php endif; ?>

    <div class="feed-list">
      <?php if($posts): ?>
        <?php foreach($posts as $post):
            $comments = $post['comments'];
        ?>
          <div class="feed-card animate-fade-in-up" id="post-<?=$post['id']?>" style="margin-bottom:2.3rem;">
            <div class="post-header">
              <div class="user-info">
                <img src="<?=htmlspecialchars($post['user_avatar'] ?: 'assets/images/default-avatar.png')?>" alt="U" class="user-avatar">
                <div>
                  <b class="user-name"><?=htmlspecialchars($post['user_name'])?></b>
                  <div class="post-meta">
                    نُشر في <b><?=htmlspecialchars($post['community_name'])?></b>
                    • <?=date('Y-m-d H:i', strtotime($post['created_at']))?>
                  </div>
                </div>
              </div>
              <span class="badge-type badge-<?=htmlspecialchars($post['type'])?>">
                <?= ['vent'=>'فضفضة','advice'=>'نصيحة','question'=>'سؤال','article'=>'مقالة'][$post['type']] ?? $post['type'] ?>
              </span>
            </div>
            <?php if($post['title']): ?>
              <h3 style="margin-bottom:.6rem;"><?=htmlspecialchars($post['title'])?></h3>
            <?php endif;?>
            <div class="post-content"><?=nl2br(htmlspecialchars($post['content']))?></div>

            <div class="post-interaction-bar" style="display:flex; justify-content:space-between; align-items:center; margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--outline-variant);">
                <div style="display:flex; gap:1rem;">
                    <button type="button"
                        class="interaction-btn <?= $post['is_liked'] ? 'active-like' : '' ?>"
                        id="like-btn-<?=$post['id']?>"
                        data-post="<?=$post['id']?>"
                        onclick="<?= $user_id ? 'ajaxLike(this)' : "location.href='/malath-php-app/login'" ?>">
                        <i class="<?= $post['is_liked'] ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                        <span id="like-count-<?=$post['id']?>"><?= $post['likes_count'] ?> إعجاب</span>
                    </button>
                    <button type="button" class="interaction-btn" onclick="toggleComments(<?=$post['id']?>)">
                        <i class="fa-regular fa-comment"></i>
                        <span id="comment-count-<?=$post['id']?>"><?= $post['comments_count'] ?> تعليق</span>
                    </button>
                </div>
                <button type="button"
                    class="interaction-btn <?= $post['is_saved'] ? 'active-save' : '' ?>"
                    id="save-btn-<?=$post['id']?>"
                    data-post="<?=$post['id']?>"
                    onclick="<?= $user_id ? 'ajaxSave(this)' : "location.href='/malath-php-app/login'" ?>">
                    <i class="<?= $post['is_saved'] ? 'fa-solid' : 'fa-regular' ?> fa-bookmark"></i>
                    <span id="save-label-<?=$post['id']?>"><?= $post['is_saved'] ? 'مُحفوظ' : 'حفظ' ?></span>
                </button>
            </div>

            <div id="comments-section-<?=$post['id']?>" style="display:none; margin-top:1rem; background-color:var(--surface-container-high); padding:1.5rem; border-radius:1.5rem;">
                <div id="comments-list-<?=$post['id']?>">
                <?php if(!$comments): ?>
                    <p class="no-comments-msg" style="color:var(--secondary); font-size:0.9rem; text-align:center; margin-bottom:1rem;">لا توجد تعليقات بعد. كوني أول من يعلق!</p>
                <?php else: ?>
                    <?php foreach($comments as $comm): ?>
                        <div data-comment-id="<?=$comm['id']?>" style="display:flex; gap:1rem; margin-bottom:1rem;">
                            <img src="<?=htmlspecialchars($comm['user_avatar'] ?: 'assets/images/default-avatar.png')?>" alt="U" style="width:2.5rem; height:2.5rem; border-radius:50%; object-fit:cover;">
                            <div style="background-color:#ffffff; padding:1rem 1.2rem; border-radius:1.5rem; border-top-right-radius:0; flex-grow:1; box-shadow:0 2px 8px rgba(0,0,0,0.02);">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                                    <b style="font-size:0.9rem; color:var(--primary-dark); font-family:var(--font-headline); font-weight:800;"><?=htmlspecialchars($comm['user_name'])?></b>
                                    <div style="display:flex; align-items:center; gap:0.5rem;">
                                        <small style="color:var(--secondary); font-size:0.75rem;"><?=date('Y-m-d H:i', strtotime($comm['created_at']))?></small>
                                        <?php if($user_id && $comm['user_id'] == $user_id): ?>
                                        <button type="button" onclick="ajaxDeleteComment(<?=$comm['id']?>, <?=$post['id']?>, this)" title="مسح التعليق" style="background:none;border:none;color:#be185d;cursor:pointer;font-size:0.75rem;padding:0.2rem 0.4rem;border-radius:0.4rem;transition:background 0.2s;line-height:1;" onmouseover="this.style.backgroundColor='#fce7f3'" onmouseout="this.style.backgroundColor='transparent'"><i class="fa-solid fa-trash-can"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="font-size:0.95rem; color:var(--on-surface); line-height:1.6;"><?=nl2br(htmlspecialchars($comm['content']))?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>

                <?php if($user_id): ?>
                <div style="display:flex; gap:0.8rem; margin-top:1.5rem; align-items:flex-start;">
                    <img src="<?=htmlspecialchars($user_avatar ?: 'assets/images/default-avatar.png')?>" alt="U" style="width:2.5rem; height:2.5rem; border-radius:50%; object-fit:cover;">
                    <div style="flex-grow:1; position:relative;">
                        <textarea id="comment-input-<?=$post['id']?>" placeholder="اكتبي تعليقاً لطيفاً داعماً..." style="width:100%; border-radius:1.5rem; border:1px solid var(--outline-variant); padding:0.8rem 1.2rem; background-color:#ffffff; font-family:inherit; font-size:0.95rem; resize:none; min-height:50px;"></textarea>
                    </div>
                    <button type="button" class="btn-primary" onclick="ajaxComment(<?=$post['id']?>)" style="border-radius:50%; width:2.8rem; height:2.8rem; display:flex; align-items:center; justify-content:center; padding:0; flex-shrink:0;"><i class="fa-solid fa-paper-plane" style="margin-right:-3px;"></i></button>
                </div>
                <?php else: ?>
                <div style="text-align:center; color:var(--secondary); font-size:0.9rem; margin-top:1rem; padding:1rem; background:#fff; border-radius:1rem;">سجلي دخولك لتتمكني من التعليق.</div>
                <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else:?>
        <div style="text-align:center; color:#888;">لا توجد منشورات بعد في هذا المجتمع.</div>
      <?php endif;?>
    </div>

<script>
const CSRF_TOKEN = '<?= htmlspecialchars(csrf_generate()) ?>';

function toggleComments(postId) {
    const s = document.getElementById('comments-section-' + postId);
    s.style.display = (s.style.display === 'none') ? 'block' : 'none';
}

async function ajaxLike(btn) {
    const postId = btn.dataset.post;
    const fd = new FormData();
    fd.append('post_id', postId);
    const res = await fetch('/malath-php-app/api/toggle_like.php', { method:'POST', body:fd, headers:{'X-CSRF-Token':CSRF_TOKEN} });
    const data = await res.json();
    if (!data.success) { console.error('[Like] failed:', data); return; }
    const icon = btn.querySelector('i');
    if (data.liked) { btn.classList.add('active-like'); icon.className = 'fa-solid fa-heart'; }
    else { btn.classList.remove('active-like'); icon.className = 'fa-regular fa-heart'; }
    document.getElementById('like-count-' + postId).textContent = data.count + ' إعجاب';
}

async function ajaxSave(btn) {
    const postId = btn.dataset.post;
    const fd = new FormData();
    fd.append('post_id', postId);
    const res = await fetch('/malath-php-app/api/toggle_save.php', { method:'POST', body:fd, headers:{'X-CSRF-Token':CSRF_TOKEN} });
    const data = await res.json();
    if (!data.success) { console.error('[Save] failed:', data); return; }
    const icon = btn.querySelector('i');
    const label = document.getElementById('save-label-' + postId);
    if (data.saved) { btn.classList.add('active-save'); icon.className = 'fa-solid fa-bookmark'; label.textContent = 'مُحفوظ'; }
    else { btn.classList.remove('active-save'); icon.className = 'fa-regular fa-bookmark'; label.textContent = 'حفظ'; }
}

async function ajaxDeleteComment(commentId, postId, btn) {
    if (!confirm('هل أنتِ متأكدة من مسح هذا التعليق؟')) return;
    const fd = new FormData();
    fd.append('comment_id', commentId);
    const res = await fetch('/malath-php-app/api/delete_comment.php', { method:'POST', body:fd, headers:{'X-CSRF-Token':CSRF_TOKEN} });
    const data = await res.json();
    if (!data.success) { console.error('[Delete Comment] failed:', data); return; }
    const commentDiv = btn.closest('[data-comment-id]');
    commentDiv.remove();
    const countEl = document.getElementById('comment-count-' + postId);
    if (countEl) countEl.textContent = (Math.max(0, parseInt(countEl.textContent) - 1)) + ' تعليق';
}

async function ajaxComment(postId) {
    const textarea = document.getElementById('comment-input-' + postId);
    const content = textarea.value.trim();
    if (!content) return;
    const fd = new FormData();
    fd.append('post_id', postId);
    fd.append('content', content);
    const res = await fetch('/malath-php-app/api/submit_comment.php', { method:'POST', body:fd, headers:{'X-CSRF-Token':CSRF_TOKEN} });
    const data = await res.json();
    if (!data.success) { console.error('[Comment] failed:', data); return; }
    textarea.value = '';
    const list = document.getElementById('comments-list-' + postId);
    const noMsg = list.querySelector('.no-comments-msg');
    if (noMsg) noMsg.remove();
    const html = `<div data-comment-id="${data.comment_id}" style="display:flex;gap:1rem;margin-bottom:1rem;">
        <img src="${data.user_avatar}" alt="" style="width:2.5rem;height:2.5rem;border-radius:50%;object-fit:cover;">
        <div style="background:#fff;padding:1rem 1.2rem;border-radius:1.5rem;border-top-right-radius:0;flex-grow:1;box-shadow:0 2px 8px rgba(0,0,0,0.02);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem;">
                <b style="font-size:.9rem;color:var(--primary-dark);font-family:var(--font-headline);font-weight:800;">${data.user_name}</b>
                <div style="display:flex;align-items:center;gap:.5rem;">
                    <small style="color:var(--secondary);font-size:.75rem;">${data.created_at}</small>
                    <button type="button" onclick="ajaxDeleteComment(${data.comment_id}, ${postId}, this)" title="مسح التعليق" style="background:none;border:none;color:#be185d;cursor:pointer;font-size:.75rem;padding:.2rem .4rem;border-radius:.4rem;transition:background .2s;line-height:1;" onmouseover="this.style.backgroundColor='#fce7f3'" onmouseout="this.style.backgroundColor='transparent'"><i class="fa-solid fa-trash-can"></i></button>
                </div>
            </div>
            <div style="font-size:.95rem;color:var(--on-surface);line-height:1.6;">${data.content.replace(/\n/g,'<br>')}</div>
        </div>
    </div>`;
    list.insertAdjacentHTML('beforeend', html);
    const countEl = document.getElementById('comment-count-' + postId);
    if (countEl) countEl.textContent = (parseInt(countEl.textContent) + 1) + ' تعليق';
}
</script>

  </main>
</div>

<?php include ROOT_PATH . '/includes/footer.php'; ?>
