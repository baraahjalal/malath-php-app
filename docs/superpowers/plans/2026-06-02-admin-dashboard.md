# Admin Dashboard Enhancement — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Give the admin full visibility and control over articles (unified tab + filters), posts (full preview with comments), and members (activity summary) via AJAX-loaded modals.

**Architecture:** AJAX modal pattern — lists stay server-rendered; detail views and actions load via `fetch()` into a shared modal. Five new API endpoints under `api/admin/` verify admin session and return JSON. A shared modal component (HTML + vanilla JS) handles all three content types.

**Tech Stack:** PHP 8, PDO, vanilla JS (Fetch API), existing `app/bootstrap.php`, `app/core/Database.php`, `includes/csrf.php`

---

## File Map

| File | Action | Responsibility |
|------|--------|---------------|
| `app/models/AdminModel.php` | Modify | Add `getAllArticles()` |
| `app/controllers/AdminController.php` | Modify | Use `getAllArticles()` for articles tab, pass `$all_articles` |
| `api/admin/get_article.php` | Create | Return full article data as JSON |
| `api/admin/article_action.php` | Create | Handle approve / reject / delete for articles |
| `api/admin/get_post.php` | Create | Return post + stats + comments as JSON |
| `api/admin/delete_post.php` | Create | Delete a post, return JSON |
| `api/admin/get_user.php` | Create | Return user activity summary as JSON |
| `api/admin/user_action.php` | Create | Handle delete / toggle_role for users |
| `app/views/admin/index.php` | Modify | Modal HTML/CSS, filter buttons, preview buttons, articles table |
| `assets/js/app.js` | Modify | Modal open/close, filter logic, all AJAX action handlers |

---

## Task 1 — AdminModel: getAllArticles() + Controller update
**Estimated time: 10 min**

**Files:**
- Modify: `app/models/AdminModel.php`
- Modify: `app/controllers/AdminController.php`

- [ ] **Step 1: Add `getAllArticles()` to AdminModel**

Open `app/models/AdminModel.php` and add this method after `getPosts()`:

```php
public function getAllArticles(): array {
    return $this->query("
        SELECT a.id, a.title, a.content, a.image, a.status, a.created_at,
               u.name AS author_name, u.avatar AS author_avatar,
               c.name AS community_name, c.slug AS community_slug
        FROM articles a
        JOIN users u  ON a.user_id      = u.id
        JOIN communities c ON a.community_id = c.id
        ORDER BY a.created_at DESC
        LIMIT 100
    ")->fetchAll();
}
```

- [ ] **Step 2: Update `AdminController::index()` — articles tab**

In `app/controllers/AdminController.php`, replace the `elseif ($tab === 'articles')` block:

```php
// BEFORE:
} elseif ($tab === 'articles') {
    $pending_articles = $articleModel->getPending();
}

// AFTER:
} elseif ($tab === 'articles') {
    $all_articles = $model->getAllArticles();
}
```

Then add `$all_articles` (defaulting to `[]`) at the top of the method alongside the other defaults:

```php
$all_articles = [];
```

And add it to the `compact()` call:

```php
$this->view('admin.index', compact(
    'tab','total_users','total_posts','total_comments','total_likes',
    'new_users_week','new_posts_week','recent_posts','recent_users','users','posts',
    'pending_articles','pending_count','all_articles'
));
```

> `$pending_articles` and `$pending_count` stay — `$pending_count` is still used for the sidebar badge, and `$pending_articles` can be removed from view later.

- [ ] **Step 3: Verify the page loads**

Open `http://localhost/malath-php-app/dashboard?tab=articles` in the browser (logged in as admin).
Expected: page loads without PHP errors.

- [ ] **Step 4: Commit**

```bash
git add app/models/AdminModel.php app/controllers/AdminController.php
git commit -m "feat(admin): add getAllArticles() to AdminModel, pass to view"
```

---

## Task 2 — API: get_article.php
**Estimated time: 10 min**

**Files:**
- Create: `api/admin/get_article.php`

- [ ] **Step 1: Create the directory**

```bash
mkdir -p /Applications/XAMPP/xamppfiles/htdocs/malath-php-app/api/admin
```

- [ ] **Step 2: Create `api/admin/get_article.php`**

```php
<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'forbidden']); exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) { echo json_encode(['success' => false, 'error' => 'invalid_id']); exit; }

$pdo  = Database::getInstance()->getPdo();
$stmt = $pdo->prepare("
    SELECT a.id, a.title, a.content, a.image, a.status, a.created_at,
           u.name AS author_name, u.avatar AS author_avatar,
           c.name AS community_name, c.slug AS community_slug
    FROM articles a
    JOIN users u  ON a.user_id      = u.id
    JOIN communities c ON a.community_id = c.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) { echo json_encode(['success' => false, 'error' => 'not_found']); exit; }

echo json_encode(['success' => true, 'article' => $article]);
```

- [ ] **Step 3: Test with curl**

```bash
# First get a valid article ID from the DB, then:
curl -b "PHPSESSID=<your_session_id>" \
  "http://localhost/malath-php-app/api/admin/get_article.php?id=1"
```

Expected (article exists): `{"success":true,"article":{"id":1,"title":"...","status":"pending",...}}`
Expected (no session): `{"success":false,"error":"forbidden"}`

- [ ] **Step 4: Commit**

```bash
git add api/admin/get_article.php
git commit -m "feat(admin): add get_article API endpoint"
```

---

## Task 3 — API: article_action.php
**Estimated time: 15 min**

**Files:**
- Create: `api/admin/article_action.php`

- [ ] **Step 1: Create `api/admin/article_action.php`**

```php
<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;
use App\Core\EmailService;
use App\Models\ArticleModel;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'forbidden']); exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'csrf']); exit;
}

$id     = intval($_POST['article_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$id || !in_array($action, ['approve', 'reject', 'delete'], true)) {
    echo json_encode(['success' => false, 'error' => 'invalid_params']); exit;
}

$pdo          = Database::getInstance()->getPdo();
$articleModel = new ArticleModel();

if ($action === 'delete') {
    $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true, 'action' => 'delete']); exit;
}

// approve or reject — fetch author info for email
$stmt = $pdo->prepare("
    SELECT a.title, u.name AS author_name, u.email AS author_email
    FROM articles a JOIN users u ON a.user_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) { echo json_encode(['success' => false, 'error' => 'not_found']); exit; }

if ($action === 'approve') {
    $articleModel->approve($id);
    EmailService::sendArticleStatus(
        $article['author_email'], $article['author_name'], $article['title'], 'approved'
    );
    echo json_encode(['success' => true, 'action' => 'approve', 'new_status' => 'approved']); exit;
}

// reject
$articleModel->reject($id);
EmailService::sendArticleStatus(
    $article['author_email'], $article['author_name'], $article['title'], 'rejected'
);
echo json_encode(['success' => true, 'action' => 'reject', 'new_status' => 'rejected']);
```

- [ ] **Step 2: Test approve action with curl**

```bash
curl -X POST -b "PHPSESSID=<session>" \
  -d "csrf_token=<token>&article_id=1&action=approve" \
  "http://localhost/malath-php-app/api/admin/article_action.php"
```

Expected: `{"success":true,"action":"approve","new_status":"approved"}`

- [ ] **Step 3: Commit**

```bash
git add api/admin/article_action.php
git commit -m "feat(admin): add article_action API endpoint (approve/reject/delete)"
```

---

## Task 4 — API: get_post.php + delete_post.php
**Estimated time: 15 min**

**Files:**
- Create: `api/admin/get_post.php`
- Create: `api/admin/delete_post.php`

- [ ] **Step 1: Create `api/admin/get_post.php`**

```php
<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'forbidden']); exit;
}

$id  = intval($_GET['id'] ?? 0);
if (!$id) { echo json_encode(['success' => false, 'error' => 'invalid_id']); exit; }

$pdo = Database::getInstance()->getPdo();

$stmt = $pdo->prepare("
    SELECT p.id, p.content, p.type, p.title, p.created_at,
           u.name AS user_name,
           c.name AS community_name,
           (SELECT COUNT(*) FROM post_likes    WHERE post_id = p.id) AS likes,
           (SELECT COUNT(*) FROM post_comments WHERE post_id = p.id) AS comments_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN communities c ON p.community_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) { echo json_encode(['success' => false, 'error' => 'not_found']); exit; }

$cStmt = $pdo->prepare("
    SELECT pc.id, pc.content, pc.created_at, u.name AS user_name
    FROM post_comments pc JOIN users u ON pc.user_id = u.id
    WHERE pc.post_id = ? ORDER BY pc.created_at ASC
");
$cStmt->execute([$id]);
$post['comments'] = $cStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'post' => $post]);
```

- [ ] **Step 2: Create `api/admin/delete_post.php`**

```php
<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'forbidden']); exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'csrf']); exit;
}

$id = intval($_POST['post_id'] ?? 0);
if (!$id) { echo json_encode(['success' => false, 'error' => 'invalid_id']); exit; }

Database::getInstance()->getPdo()
    ->prepare("DELETE FROM posts WHERE id = ?")
    ->execute([$id]);

echo json_encode(['success' => true]);
```

- [ ] **Step 3: Test get_post with curl**

```bash
curl -b "PHPSESSID=<session>" \
  "http://localhost/malath-php-app/api/admin/get_post.php?id=1"
```

Expected: `{"success":true,"post":{"id":1,"content":"...","likes":3,"comments_count":2,"comments":[...]}}`

- [ ] **Step 4: Commit**

```bash
git add api/admin/get_post.php api/admin/delete_post.php
git commit -m "feat(admin): add get_post and delete_post API endpoints"
```

---

## Task 5 — API: get_user.php + user_action.php
**Estimated time: 15 min**

**Files:**
- Create: `api/admin/get_user.php`
- Create: `api/admin/user_action.php`

- [ ] **Step 1: Create `api/admin/get_user.php`**

```php
<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'forbidden']); exit;
}

$id  = intval($_GET['id'] ?? 0);
if (!$id) { echo json_encode(['success' => false, 'error' => 'invalid_id']); exit; }

$pdo  = Database::getInstance()->getPdo();

$stmt = $pdo->prepare(
    "SELECT id, name, email, role, avatar, created_at FROM users WHERE id = ?"
);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) { echo json_encode(['success' => false, 'error' => 'not_found']); exit; }

$pCount = $pdo->prepare("SELECT COUNT(*) FROM posts    WHERE user_id = ?");
$aCount = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE user_id = ? AND status = 'approved'");
$pCount->execute([$id]);
$aCount->execute([$id]);

// last activity = latest created_at across posts and articles
$lastStmt = $pdo->prepare("
    SELECT MAX(ts) FROM (
        SELECT MAX(created_at) AS ts FROM posts    WHERE user_id = ?
        UNION ALL
        SELECT MAX(created_at) AS ts FROM articles WHERE user_id = ?
    ) t
");
$lastStmt->execute([$id, $id]);

$user['post_count']    = (int)$pCount->fetchColumn();
$user['article_count'] = (int)$aCount->fetchColumn();
$user['last_active']   = $lastStmt->fetchColumn() ?: null;

echo json_encode(['success' => true, 'user' => $user]);
```

- [ ] **Step 2: Create `api/admin/user_action.php`**

```php
<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../app/bootstrap.php';
use App\Core\Database;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'forbidden']); exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'csrf']); exit;
}

$targetId = intval($_POST['user_id'] ?? 0);
$action   = $_POST['action'] ?? '';

if (!$targetId || !in_array($action, ['delete', 'toggle_role'], true)) {
    echo json_encode(['success' => false, 'error' => 'invalid_params']); exit;
}

if ($targetId === (int)$_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'cannot_modify_self']); exit;
}

$pdo = Database::getInstance()->getPdo();

if ($action === 'delete') {
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$targetId]);
    echo json_encode(['success' => true, 'action' => 'delete']); exit;
}

// toggle_role
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$targetId]);
$current = $stmt->fetchColumn();
if (!$current) { echo json_encode(['success' => false, 'error' => 'not_found']); exit; }

$newRole = ($current === 'admin') ? 'user' : 'admin';
$pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$newRole, $targetId]);
echo json_encode(['success' => true, 'action' => 'toggle_role', 'new_role' => $newRole]);
```

- [ ] **Step 3: Test get_user with curl**

```bash
curl -b "PHPSESSID=<session>" \
  "http://localhost/malath-php-app/api/admin/get_user.php?id=2"
```

Expected: `{"success":true,"user":{"id":2,"name":"...","post_count":5,"article_count":1,"last_active":"2026-05-30 14:22:00"}}`

- [ ] **Step 4: Commit**

```bash
git add api/admin/get_user.php api/admin/user_action.php
git commit -m "feat(admin): add get_user and user_action API endpoints"
```

---

## Task 6 — Shared Modal: HTML + CSS + JS
**Estimated time: 25 min**

**Files:**
- Modify: `app/views/admin/index.php`
- Modify: `assets/js/app.js`

- [ ] **Step 1: Add CSRF meta tag to admin view `<head>`**

In `app/views/admin/index.php`, add inside `<head>` after the existing meta tags:

```html
<meta name="csrf-token" content="<?= htmlspecialchars(csrf_generate()) ?>">
<meta name="current-user-id" content="<?= (int)$_SESSION['user_id'] ?>">
```

- [ ] **Step 2: Add modal HTML to admin view body**

In `app/views/admin/index.php`, add immediately after the opening `<body>` tag (before `<div class="admin-overlay"...>`):

```html
<div id="adminModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:200;overflow-y:auto;padding:2rem 1rem;" onclick="if(event.target===this)closeAdminModal()">
    <div style="background:#fff;border-radius:1.5rem;max-width:700px;margin:0 auto;position:relative;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <button onclick="closeAdminModal()" style="position:absolute;top:1rem;left:1rem;background:var(--surface-container);border:none;border-radius:50%;width:2.2rem;height:2.2rem;cursor:pointer;font-size:1.1rem;display:flex;align-items:center;justify-content:center;color:var(--secondary);z-index:10;">&times;</button>
        <div id="adminModalBody" style="min-height:200px;display:flex;align-items:center;justify-content:center;">
            <div class="admin-modal-spinner"></div>
        </div>
    </div>
</div>
```

- [ ] **Step 3: Add modal CSS to the admin view `<style>` block**

Append inside the existing `<style>` tag in `app/views/admin/index.php`:

```css
.admin-modal-spinner { width:2.5rem;height:2.5rem;border:3px solid var(--surface-container-high);border-top-color:var(--primary);border-radius:50%;animation:spin .7s linear infinite; }
@keyframes spin { to { transform:rotate(360deg); } }
```

- [ ] **Step 4: Add JS module to `assets/js/app.js`**

Append at the end of `assets/js/app.js`:

```js
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
    const statusLabel = { pending:'معلّقة', approved:'منشورة', rejected:'مرفوضة' }[a.status] ?? a.status;
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
    const typeLabel = { vent:'فضفضة', advice:'نصيحة', question:'سؤال', article:'مقالة' }[p.type] ?? p.type;
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
        badge.className = `badge-role-${json.new_role}`;
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
        btn.style.background = isActive ? 'var(--primary-gradient)' : 'var(--surface-container)';
        btn.style.color      = isActive ? '#fff' : 'var(--on-surface)';
    });
    document.querySelectorAll('tr[data-article-id]').forEach(row => {
        row.style.display = (filter === 'all' || row.dataset.status === filter) ? '' : 'none';
    });
}
```

- [ ] **Step 5: Verify modal opens without errors**

Open browser console on `http://localhost/malath-php-app/dashboard?tab=posts`.
Run: `openAdminModal('/malath-php-app/api/admin/get_post.php?id=1')`
Expected: modal appears with spinner, then populates with post data. No console errors.

- [ ] **Step 6: Commit**

```bash
git add app/views/admin/index.php assets/js/app.js
git commit -m "feat(admin): add shared admin modal component (HTML + CSS + JS)"
```

---

## Task 7 — Articles Tab UI
**Estimated time: 20 min**

**Files:**
- Modify: `app/views/admin/index.php`

- [ ] **Step 1: Replace the `$tab === 'articles'` section in the view**

Find the entire `<?php elseif($tab === 'articles'): ?>` block (lines ~249–298) and replace it with:

```php
<?php elseif($tab === 'articles'): ?>
<div class="data-card">
    <div class="data-card-header">
        <h3 class="font-headline font-bold text-primary-dark" style="margin:0;font-size:1.2rem;">
            المقالات (<?= count($all_articles) ?>)
        </h3>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <button class="btn-sm article-filter-btn" data-filter="all"     onclick="applyArticleFilter('all')"     style="background:var(--primary-gradient);color:#fff;">الكل</button>
            <button class="btn-sm article-filter-btn" data-filter="pending"  onclick="applyArticleFilter('pending')"  style="background:var(--surface-container);color:var(--on-surface);">معلّقة</button>
            <button class="btn-sm article-filter-btn" data-filter="approved" onclick="applyArticleFilter('approved')" style="background:var(--surface-container);color:var(--on-surface);">منشورة</button>
            <button class="btn-sm article-filter-btn" data-filter="rejected" onclick="applyArticleFilter('rejected')" style="background:var(--surface-container);color:var(--on-surface);">مرفوضة</button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>العنوان</th>
                    <th>الكاتبة</th>
                    <th>المجتمع</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>معاينة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($all_articles as $a): ?>
            <?php
                $statusLabel = ['pending'=>'معلّقة','approved'=>'منشورة','rejected'=>'مرفوضة'][$a['status']] ?? $a['status'];
                $statusBg    = ['pending'=>'#fef3c7','approved'=>'#dcfce7','rejected'=>'#fee2e2'][$a['status']] ?? '#eee';
                $statusColor = ['pending'=>'#d97706','approved'=>'#166534','rejected'=>'#b91c1c'][$a['status']] ?? '#333';
            ?>
            <tr data-article-id="<?= $a['id'] ?>" data-status="<?= htmlspecialchars($a['status']) ?>">
                <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    <strong><?= htmlspecialchars(mb_substr($a['title'] ?: mb_substr($a['content'],0,40), 0, 40)) ?></strong>
                </td>
                <td><?= htmlspecialchars($a['author_name']) ?></td>
                <td><?= htmlspecialchars($a['community_name']) ?></td>
                <td>
                    <span class="article-status-badge" style="background:<?= $statusBg ?>;color:<?= $statusColor ?>;padding:.25rem .75rem;border-radius:1rem;font-size:.8rem;font-weight:700;">
                        <?= $statusLabel ?>
                    </span>
                </td>
                <td style="color:var(--secondary);font-size:.85rem;"><?= date('Y-m-d', strtotime($a['created_at'])) ?></td>
                <td>
                    <button class="btn-sm btn-info"
                        onclick="openAdminModal('/malath-php-app/api/admin/get_article.php?id=<?= $a['id'] ?>')">
                        معاينة
                    </button>
                </td>
                <td class="article-action-cell" style="white-space:nowrap;">
                    <?php if($a['status'] === 'pending'): ?>
                        <button class="btn-sm" style="background:#dcfce7;color:#166534;" onclick="adminArticleAction(<?= $a['id'] ?>,'approve')">✅ قبول</button>
                        <button class="btn-sm btn-danger" onclick="adminArticleAction(<?= $a['id'] ?>,'reject')">❌ رفض</button>
                    <?php elseif($a['status'] === 'approved'): ?>
                        <button class="btn-sm" style="background:#fee2e2;color:#b91c1c;" onclick="adminArticleAction(<?= $a['id'] ?>,'reject')">🚫 رفض</button>
                        <button class="btn-sm btn-danger" onclick="adminArticleAction(<?= $a['id'] ?>,'delete')">🗑 حذف</button>
                    <?php else: ?>
                        <button class="btn-sm" style="background:#dcfce7;color:#166534;" onclick="adminArticleAction(<?= $a['id'] ?>,'approve')">✅ قبول</button>
                        <button class="btn-sm btn-danger" onclick="adminArticleAction(<?= $a['id'] ?>,'delete')">🗑 حذف</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
```

- [ ] **Step 2: Verify articles tab in browser**

Open `http://localhost/malath-php-app/dashboard?tab=articles`.
Expected:
- Table shows all articles with coloured status badges
- Filter buttons at top-right: clicking "معلّقة" hides non-pending rows
- "معاينة" button opens modal with full article content
- Approve/reject/delete buttons work and update the row in-place

- [ ] **Step 3: Commit**

```bash
git add app/views/admin/index.php
git commit -m "feat(admin): implement unified articles tab with filters and AJAX modal"
```

---

## Task 8 — Posts Tab UI: Preview Button
**Estimated time: 10 min**

**Files:**
- Modify: `app/views/admin/index.php`

- [ ] **Step 1: Update the posts table**

In the `$tab === 'posts'` section, make two changes:

**a)** Add `data-post-id` to each `<tr>`:
```php
// BEFORE:
<tr>

// AFTER:
<tr data-post-id="<?= $p['id'] ?>">
```

**b)** Add a "معاينة" column header after "التاريخ":
```php
// BEFORE:
<thead><tr><th>الكاتبة</th><th>المجتمع</th><th>النوع</th><th>المحتوى</th><th>❤ إعجاب</th><th>💬 تعليق</th><th>التاريخ</th><th>حذف</th></tr></thead>

// AFTER:
<thead><tr><th>الكاتبة</th><th>المجتمع</th><th>النوع</th><th>المحتوى</th><th>❤ إعجاب</th><th>💬 تعليق</th><th>التاريخ</th><th>معاينة</th><th>حذف</th></tr></thead>
```

**c)** Add the preview `<td>` before the delete `<td>` in each row:
```php
// Add before the delete <td>:
<td>
    <button class="btn-sm btn-info"
        onclick="openAdminModal('/malath-php-app/api/admin/get_post.php?id=<?= $p['id'] ?>')">
        معاينة
    </button>
</td>
```

- [ ] **Step 2: Verify posts tab in browser**

Open `http://localhost/malath-php-app/dashboard?tab=posts`.
Expected:
- "معاينة" column appears
- Clicking it opens modal with full post content, stats (likes/comments count), and list of comments
- "🗑 حذف المنشور" button in modal removes the row from the table

- [ ] **Step 3: Commit**

```bash
git add app/views/admin/index.php
git commit -m "feat(admin): add AJAX preview modal to posts tab"
```

---

## Task 9 — Members Tab UI: Preview Button
**Estimated time: 10 min**

**Files:**
- Modify: `app/views/admin/index.php`

- [ ] **Step 1: Update the users table**

In the `$tab === 'users'` section, make three changes:

**a)** Add `data-user-id` to each `<tr>`:
```php
// BEFORE:
<tr>

// AFTER:
<tr data-user-id="<?= $u['id'] ?>">
```

**b)** Add `class="user-role-badge"` to the role badge span:
```php
// BEFORE:
<span class="badge-role-<?= $u['role'] ?>"><?= $u['role']==='admin'?'مشرفة':'عضوة' ?></span>

// AFTER:
<span class="badge-role-<?= $u['role'] ?> user-role-badge"><?= $u['role']==='admin'?'مشرفة':'عضوة' ?></span>
```

**c)** Add `class="user-toggle-btn"` to the toggle role button:
```php
// BEFORE:
<button type="submit" name="toggle_role" class="btn-sm btn-info">

// AFTER:
<button type="submit" name="toggle_role" class="btn-sm btn-info user-toggle-btn">
```

**d)** Add "معاينة" column header:
```php
// BEFORE:
<thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>الدور</th><th>تاريخ التسجيل</th><th>إجراءات</th></tr></thead>

// AFTER:
<thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>الدور</th><th>تاريخ التسجيل</th><th>معاينة</th><th>إجراءات</th></tr></thead>
```

**e)** Add the preview `<td>` before the actions `<td>`:
```php
// Add before the <td> containing the action forms:
<td>
    <button class="btn-sm btn-info"
        onclick="openAdminModal('/malath-php-app/api/admin/get_user.php?id=<?= $u['id'] ?>')">
        معاينة
    </button>
</td>
```

- [ ] **Step 2: Verify members tab in browser**

Open `http://localhost/malath-php-app/dashboard?tab=users`.
Expected:
- "معاينة" column appears
- Clicking it opens modal with member avatar, name, email, role, post count, article count, last activity
- "ترقية لمشرفة" / "إزالة المشرفة" updates badge in both modal and table row without page reload
- "حذف العضوة" removes the row from the table
- Own account shows "أنتِ" with no action buttons

- [ ] **Step 3: Final browser check — all three tabs**

Verify each scenario:
1. Articles tab → filter by "معلّقة" → approve one → row updates to "منشورة", filter re-applies
2. Articles tab → open preview modal → reject from inside modal → row updates
3. Posts tab → open preview → see comments → delete from modal → row removed
4. Members tab → open preview → toggle role → badge updates in table

- [ ] **Step 4: Commit**

```bash
git add app/views/admin/index.php
git commit -m "feat(admin): add AJAX preview modal to members tab"
```

---

## Summary

| Task | Files | Time |
|------|-------|------|
| 1 — AdminModel + Controller | 2 modified | 10 min |
| 2 — get_article.php | 1 new | 10 min |
| 3 — article_action.php | 1 new | 15 min |
| 4 — get_post.php + delete_post.php | 2 new | 15 min |
| 5 — get_user.php + user_action.php | 2 new | 15 min |
| 6 — Shared modal HTML + JS | 2 modified | 25 min |
| 7 — Articles tab UI | 1 modified | 20 min |
| 8 — Posts tab UI | 1 modified | 10 min |
| 9 — Members tab UI | 1 modified | 10 min |
| **Total** | **6 new, 4 modified** | **~130 min** |
