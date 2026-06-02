// app.js
document.addEventListener('DOMContentLoaded', () => {
    console.log('Malath PHP App Loaded Successfully');

    // Profile Tabs
    const profileTabs = document.querySelectorAll('.profile-tab');
    const tabContents = document.querySelectorAll('.tab-content');

    if (profileTabs.length > 0) {
        profileTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and contents
                profileTabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                // Add active class to the clicked tab
                tab.classList.add('active');

                // Show corresponding content
                const targetId = tab.getAttribute('data-tab');
                const targetContent = document.getElementById(targetId);
                if(targetContent) {
                    targetContent.classList.add('active');
                    // Retrigger animation
                    targetContent.classList.remove('animate-fade-in-up');
                    void targetContent.offsetWidth; // Trigger reflow
                    targetContent.classList.add('animate-fade-in-up');
                }
            });
        });
    }
});

// ── Admin Modal ──────────────────────────────────────────────────────────────

const _csrfToken  = () => document.querySelector('meta[name="csrf-token"]')?.content  ?? '';
const _currentUid = () => parseInt(document.querySelector('meta[name="current-user-id"]')?.content ?? '0');

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function openAdminModal(url) {
    const modal = document.getElementById('adminModal');
    const body  = document.getElementById('adminModalBody');
    body.innerHTML = '<div class="admin-modal-spinner"></div>';
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';

    fetch(url, { credentials: 'same-origin' })
        .then(r => r.json())
        .then(data => { body.innerHTML = _buildModalContent(url, data); })
        .catch(() => { body.innerHTML = '<p style="padding:2rem;color:var(--error);">حدث خطأ أثناء التحميل.</p>'; });
}

function closeAdminModal() {
    document.getElementById('adminModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAdminModal(); });

function _buildModalContent(url, data) {
    if (!data.success) return '<p style="padding:2rem;color:var(--error);">تعذّر تحميل البيانات.</p>';
    if (data.article) return _articleModalHtml(data.article);
    if (data.post)    return _postModalHtml(data.post);
    if (data.user)    return _userModalHtml(data.user);
    return '';
}

// ── Article modal ────────────────────────────────────────────────────────────

function _articleActions(a) {
    const id = a.id;
    if (a.status === 'pending') return `
        <button class="btn-sm" style="background:#dcfce7;color:#166534;padding:.5rem 1.4rem;" onclick="adminArticleAction(${id},'approve')">✅ قبول</button>
        <button class="btn-sm btn-danger" style="padding:.5rem 1.4rem;" onclick="adminArticleAction(${id},'reject')">❌ رفض</button>`;
    if (a.status === 'approved') return `
        <button class="btn-sm" style="background:#fee2e2;color:#b91c1c;padding:.5rem 1.4rem;" onclick="adminArticleAction(${id},'reject')">🚫 رفض</button>
        <button class="btn-sm btn-danger" style="padding:.5rem 1.4rem;" onclick="adminArticleAction(${id},'delete')">🗑 حذف</button>`;
    return `
        <button class="btn-sm" style="background:#dcfce7;color:#166534;padding:.5rem 1.4rem;" onclick="adminArticleAction(${id},'approve')">✅ قبول</button>
        <button class="btn-sm btn-danger" style="padding:.5rem 1.4rem;" onclick="adminArticleAction(${id},'delete')">🗑 حذف</button>`;
}

function _articleModalHtml(a) {
    const statusLabel = { pending:'معلّقة', approved:'منشورة', rejected:'مرفوضة' }[a.status] ?? escHtml(a.status);
    const statusColor = { pending:'#d97706', approved:'#16a34a', rejected:'#b91c1c' }[a.status] ?? '#888';
    return `
    <div style="padding:2rem;">
        ${a.image ? `<img src="/malath-php-app/${escHtml(a.image)}" style="width:100%;max-height:280px;object-fit:cover;border-radius:1rem;margin-bottom:1.5rem;">` : ''}
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
            <img src="/malath-php-app/${escHtml(a.author_avatar || 'assets/images/default-avatar.png')}" style="width:2.4rem;height:2.4rem;border-radius:50%;object-fit:cover;">
            <strong>${escHtml(a.author_name)}</strong>
            <span style="background:var(--surface-container);padding:.2rem .7rem;border-radius:1rem;font-size:.8rem;">${escHtml(a.community_name)}</span>
            <span style="background:${statusColor};color:#fff;padding:.2rem .7rem;border-radius:1rem;font-size:.8rem;font-weight:700;">${statusLabel}</span>
            <span style="color:var(--secondary);font-size:.85rem;">${escHtml(a.created_at?.substring(0,10))}</span>
        </div>
        <h2 style="font-family:var(--font-headline);font-size:1.2rem;font-weight:900;margin:0 0 1rem;">${escHtml(a.title || '')}</h2>
        <div style="color:var(--on-surface);line-height:1.8;white-space:pre-wrap;font-size:.95rem;">${escHtml(a.content)}</div>
        <div style="display:flex;gap:.75rem;justify-content:flex-end;padding-top:1.5rem;margin-top:1.5rem;border-top:1px solid var(--outline-variant);">
            ${_articleActions(a)}
        </div>
    </div>`;
}

async function adminArticleAction(id, action) {
    const labels = { approve:'قبول هذه المقالة', reject:'رفض هذه المقالة', delete:'حذف هذه المقالة نهائياً' };
    if (!confirm(labels[action] + '؟')) return;
    const fd = new FormData();
    fd.append('article_id', id);
    fd.append('action', action);
    fd.append('csrf_token', _csrfToken());
    const res  = await fetch('/malath-php-app/api/admin/article_action.php', { method:'POST', body:fd, credentials:'same-origin' });
    const json = await res.json();
    if (!json.success) { alert('حدث خطأ. حاولي مجدداً.'); return; }
    closeAdminModal();
    const row = document.querySelector(`tr[data-article-id="${id}"]`);
    if (!row) return;
    if (action === 'delete') { row.remove(); return; }
    row.dataset.status = json.new_status;
    const badge = row.querySelector('.article-status-badge');
    if (badge) {
        badge.textContent = { approved:'منشورة', rejected:'مرفوضة' }[json.new_status];
        badge.style.background = { approved:'#dcfce7', rejected:'#fee2e2' }[json.new_status];
        badge.style.color      = { approved:'#166534', rejected:'#b91c1c' }[json.new_status];
    }
    _updateArticleActionBtns(row, json.new_status, id);
    applyArticleFilter(document.querySelector('.article-filter-btn.active')?.dataset.filter ?? 'all');
}

function _updateArticleActionBtns(row, status, id) {
    const cell = row.querySelector('.article-action-cell');
    if (!cell) return;
    if (status === 'approved') {
        cell.innerHTML = `
            <button class="btn-sm" style="background:#fee2e2;color:#b91c1c;" onclick="adminArticleAction(${id},'reject')">🚫 رفض</button>
            <button class="btn-sm btn-danger" onclick="adminArticleAction(${id},'delete')">🗑 حذف</button>`;
    } else {
        cell.innerHTML = `
            <button class="btn-sm" style="background:#dcfce7;color:#166534;" onclick="adminArticleAction(${id},'approve')">✅ قبول</button>
            <button class="btn-sm btn-danger" onclick="adminArticleAction(${id},'delete')">🗑 حذف</button>`;
    }
}

// ── Post modal ───────────────────────────────────────────────────────────────

function _postModalHtml(p) {
    const typeLabel = { vent:'فضفضة', advice:'نصيحة', question:'سؤال', article:'مقالة' }[p.type] ?? escHtml(p.type);
    const typeClass = { vent:'badge-vent', advice:'badge-advice', question:'badge-question', article:'badge-article' }[p.type] ?? '';
    const commentsHtml = (p.comments ?? []).map(c => `
        <div style="padding:.7rem 0;border-bottom:1px solid var(--surface-container-high);">
            <div style="display:flex;justify-content:space-between;margin-bottom:.25rem;">
                <strong style="font-size:.88rem;">${escHtml(c.user_name)}</strong>
                <span style="color:var(--secondary);font-size:.8rem;">${escHtml(c.created_at?.substring(0,10))}</span>
            </div>
            <p style="margin:0;font-size:.88rem;">${escHtml(c.content)}</p>
        </div>`).join('');
    return `
    <div style="padding:2rem;">
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.2rem;flex-wrap:wrap;">
            <strong>${escHtml(p.user_name)}</strong>
            <span style="color:var(--secondary);">${escHtml(p.community_name)}</span>
            <span class="badge-type ${typeClass}">${typeLabel}</span>
            <span style="color:var(--secondary);font-size:.85rem;">${escHtml(p.created_at?.substring(0,10))}</span>
        </div>
        ${p.title ? `<h3 style="font-family:var(--font-headline);margin:0 0 .75rem;">${escHtml(p.title)}</h3>` : ''}
        <div style="line-height:1.8;white-space:pre-wrap;margin-bottom:1.5rem;">${escHtml(p.content)}</div>
        <div style="display:flex;gap:1.5rem;padding:.75rem 1rem;background:var(--surface-container);border-radius:.75rem;margin-bottom:1.5rem;">
            <span>❤️ ${p.likes} إعجاب</span>
            <span>💬 ${p.comments_count} تعليق</span>
        </div>
        ${p.comments?.length > 0
            ? `<h4 style="font-family:var(--font-headline);margin:0 0 .75rem;">التعليقات</h4>
               <div style="max-height:240px;overflow-y:auto;">${commentsHtml}</div>`
            : `<p style="color:var(--secondary);">لا توجد تعليقات.</p>`}
        <div style="display:flex;justify-content:flex-end;padding-top:1.5rem;margin-top:1rem;border-top:1px solid var(--outline-variant);">
            <button class="btn-sm btn-danger" onclick="adminDeletePost(${p.id})" style="padding:.5rem 1.6rem;">🗑 حذف المنشور</button>
        </div>
    </div>`;
}

async function adminDeletePost(id) {
    if (!confirm('حذف هذا المنشور نهائياً؟')) return;
    const fd = new FormData();
    fd.append('post_id', id);
    fd.append('csrf_token', _csrfToken());
    const res  = await fetch('/malath-php-app/api/admin/delete_post.php', { method:'POST', body:fd, credentials:'same-origin' });
    const json = await res.json();
    if (!json.success) { alert('حدث خطأ. حاولي مجدداً.'); return; }
    closeAdminModal();
    document.querySelector(`tr[data-post-id="${id}"]`)?.remove();
}

// ── User modal ───────────────────────────────────────────────────────────────

function _userModalHtml(u) {
    const isSelf = u.id == _currentUid();
    const lastActive = u.last_active ? u.last_active.substring(0,10) : '—';
    return `
    <div style="padding:2rem;">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
            <img src="/malath-php-app/${escHtml(u.avatar || 'assets/images/default-avatar.png')}" style="width:4rem;height:4rem;border-radius:50%;object-fit:cover;flex-shrink:0;">
            <div>
                <div style="font-size:1.1rem;font-family:var(--font-headline);font-weight:900;">${escHtml(u.name)}</div>
                <div style="color:var(--secondary);font-size:.9rem;">${escHtml(u.email)}</div>
                <span class="badge-role-${escHtml(u.role)}" style="display:inline-block;margin-top:.3rem;">${u.role === 'admin' ? 'مشرفة' : 'عضوة'}</span>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;">
            <div style="background:var(--surface-container);border-radius:1rem;padding:1rem;text-align:center;">
                <div style="font-size:1.4rem;font-weight:900;font-family:var(--font-headline);color:var(--primary-dark);">${u.post_count}</div>
                <div style="font-size:.8rem;color:var(--secondary);">منشور</div>
            </div>
            <div style="background:var(--surface-container);border-radius:1rem;padding:1rem;text-align:center;">
                <div style="font-size:1.4rem;font-weight:900;font-family:var(--font-headline);color:var(--primary-dark);">${u.article_count}</div>
                <div style="font-size:.8rem;color:var(--secondary);">مقالة</div>
            </div>
            <div style="background:var(--surface-container);border-radius:1rem;padding:1rem;text-align:center;">
                <div style="font-size:.95rem;font-weight:700;color:var(--primary-dark);">${lastActive}</div>
                <div style="font-size:.8rem;color:var(--secondary);">آخر نشاط</div>
            </div>
        </div>
        ${isSelf ? `<p style="text-align:center;color:var(--secondary);font-size:.85rem;">أنتِ</p>` : `
        <div style="display:flex;gap:.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid var(--outline-variant);">
            <button class="btn-sm btn-info" onclick="adminToggleRole(${u.id},'${u.role === 'admin' ? 'user' : 'admin'}')">${u.role === 'admin' ? 'إزالة المشرفة' : 'ترقية لمشرفة'}</button>
            <button class="btn-sm btn-danger" onclick="adminDeleteUser(${u.id})">حذف العضوة</button>
        </div>`}
    </div>`;
}

async function adminToggleRole(id, newRole) {
    if (!confirm((newRole === 'admin' ? 'ترقية' : 'إزالة') + ' هذه العضوة؟')) return;
    const fd = new FormData();
    fd.append('user_id', id);
    fd.append('action', 'toggle_role');
    fd.append('csrf_token', _csrfToken());
    const res  = await fetch('/malath-php-app/api/admin/user_action.php', { method:'POST', body:fd, credentials:'same-origin' });
    const json = await res.json();
    if (!json.success) { alert('حدث خطأ. حاولي مجدداً.'); return; }
    closeAdminModal();
    const row  = document.querySelector(`tr[data-user-id="${id}"]`);
    if (!row) { location.reload(); return; }
    const badge = row.querySelector('.user-role-badge');
    if (badge) {
        badge.className = `badge-role-${json.new_role} user-role-badge`;
        badge.textContent = json.new_role === 'admin' ? 'مشرفة' : 'عضوة';
    }
    const toggleBtn = row.querySelector('.user-toggle-btn');
    if (toggleBtn) toggleBtn.textContent = json.new_role === 'admin' ? 'إزالة المشرفة' : 'ترقية لمشرفة';
}

async function adminDeleteUser(id) {
    if (!confirm('حذف هذه العضوة نهائياً؟')) return;
    const fd = new FormData();
    fd.append('user_id', id);
    fd.append('action', 'delete');
    fd.append('csrf_token', _csrfToken());
    const res  = await fetch('/malath-php-app/api/admin/user_action.php', { method:'POST', body:fd, credentials:'same-origin' });
    const json = await res.json();
    if (!json.success) { alert('حدث خطأ. حاولي مجدداً.'); return; }
    closeAdminModal();
    document.querySelector(`tr[data-user-id="${id}"]`)?.remove();
}

// ── Article filter ───────────────────────────────────────────────────────────

function applyArticleFilter(filter) {
    document.querySelectorAll('.article-filter-btn').forEach(btn => {
        const isActive = btn.dataset.filter === filter;
        btn.classList.toggle('active', isActive);
        btn.style.background = isActive ? 'var(--primary-gradient)' : 'var(--surface-container)';
        btn.style.color      = isActive ? '#fff' : 'var(--on-surface)';
    });
    document.querySelectorAll('tr[data-article-id]').forEach(row => {
        row.style.display = (filter === 'all' || row.dataset.status === filter) ? '' : 'none';
    });
}
