# Admin Dashboard Enhancement — Design Spec
**Date:** 2026-06-02  
**Branch:** feature/strict-mvc-100

---

## Goal

Give the admin full visibility and control over articles, posts, and members via AJAX-loaded modals — without full page reloads. Builds on the existing `api/` folder and Fetch API pattern already in the project.

---

## Approach

AJAX modal pattern: lists stay server-rendered; detail views and actions load via `fetch()` into a shared modal component. All admin API endpoints live under `api/admin/` and verify session + admin role before responding.

---

## 1. New API Endpoints

All endpoints live under `api/admin/`. Each file:
- Requires `app/bootstrap.php`
- Checks `$_SESSION['user_id']` and `$_SESSION['user_role'] === 'admin'` — returns 403 JSON if not
- Returns `Content-Type: application/json`

### `api/admin/get_article.php`
**Method:** GET  
**Params:** `?id=X`  
**Returns:**
```json
{
  "id": 1,
  "title": "...",
  "content": "...",
  "image": "path/or/null",
  "status": "pending|approved|rejected",
  "created_at": "...",
  "author_name": "...",
  "author_avatar": "...",
  "community_name": "...",
  "community_slug": "..."
}
```

### `api/admin/get_post.php`
**Method:** GET  
**Params:** `?id=X`  
**Returns:**
```json
{
  "id": 1,
  "content": "...",
  "type": "vent|advice|question|article",
  "created_at": "...",
  "user_name": "...",
  "community_name": "...",
  "likes": 12,
  "comments_count": 4,
  "comments": [
    { "id": 1, "user_name": "...", "content": "...", "created_at": "..." }
  ]
}
```

### `api/admin/get_user.php`
**Method:** GET  
**Params:** `?id=X`  
**Returns:**
```json
{
  "id": 1,
  "name": "...",
  "email": "...",
  "role": "user|admin",
  "avatar": "...",
  "created_at": "...",
  "post_count": 8,
  "article_count": 2,
  "last_active": "2026-05-30 14:22:00"
}
```
`last_active` = MAX(created_at) from posts + articles combined.

### `api/admin/article_action.php`
**Method:** POST  
**CSRF:** checked via `HTTP_X_CSRF_TOKEN` header (same as existing API files)  
**Body params:**
- `article_id` (int)
- `action`: `approve` | `reject` | `delete`

**Behaviour:**
- `approve` → sets status = 'approved', sends email via EmailService
- `reject` → sets status = 'rejected', sends email via EmailService (works on both pending and approved articles)
- `delete` → DELETE FROM articles WHERE id = ?

**Returns:** `{ "success": true }` or `{ "success": false, "error": "..." }`

> Post deletion remains handled by the existing `AdminController::handlePost()` form POST (no change needed).

---

## 2. Articles Tab

### List changes
- `AdminModel::getAllArticles()` replaces `ArticleModel::getPending()` — returns all articles with status, joined with user + community
- Table columns: عنوان، الكاتبة، المجتمع، الحالة (badge)، التاريخ، إجراءات
- Filter bar above table with four buttons: `الكل` / `معلّقة` / `منشورة` / `مرفوضة`
- Each row has `data-status="pending|approved|rejected"` — filter hides/shows rows client-side (no reload)

### Badge colours
| Status | Style |
|--------|-------|
| pending | yellow background |
| approved | green background |
| rejected | red background |

### Action buttons per row (inline, no modal needed)
| Status | Buttons |
|--------|---------|
| pending | ✅ قبول / ❌ رفض |
| approved | 🚫 رفض (→ rejected) / 🗑 حذف |
| rejected | ✅ قبول (→ approved) / 🗑 حذف |

All inline action buttons POST to `api/admin/article_action.php` via fetch, then remove or update the row in the DOM on success.

### Preview modal
- "معاينة" button on each row → `openAdminModal('/malath-php-app/api/admin/get_article.php?id=X')`
- Modal renders: article image (if any), title, full content, author, community, status badge, date
- Action buttons (approve/reject/delete) also available inside the modal — clicking them calls the same `article_action.php` fetch, closes the modal, and updates the list row

---

## 3. Posts Tab

### List changes
- Add a "معاينة" button column (no other changes to list)

### Preview modal content
- Author name + community + post type badge + date
- Full post content
- Stats bar: ❤️ X إعجاب — 💬 X تعليق
- Comments section: each comment shows author name, text, date
- "🗑 حذف المنشور" button at bottom → fetch POST to `api/admin/delete_post.php`, closes modal, removes row from list

---

## 4. Members Tab

### List changes
- Add a "معاينة" button column

### Preview modal content
- Avatar + name + email + role badge
- Activity card:
  - عدد المنشورات: X
  - عدد المقالات المنشورة: X
  - آخر نشاط: YYYY-MM-DD
- "حذف" and "تغيير الدور" buttons inside modal — POST to existing dashboard handler, close modal, update row

---

## 5. Shared Modal Component

Single modal added once to the admin view layout. Managed by a small JS module in `app.js`.

### HTML structure (added to `admin/index.php` once)
```html
<div id="adminModal" class="admin-modal-overlay" style="display:none;">
  <div class="admin-modal-box">
    <button id="adminModalClose" class="admin-modal-close">&times;</button>
    <div id="adminModalBody"><!-- content injected here --></div>
  </div>
</div>
```

### JS API
```js
openAdminModal(url)   // fetch url, show spinner, inject HTML response
closeAdminModal()     // hide overlay, clear body
```

The API endpoints return JSON; the modal JS builds HTML from the JSON response. No server-side HTML fragments needed.

### CSS additions (in admin view `<style>` block)
- `.admin-modal-overlay` — fixed fullscreen dark backdrop
- `.admin-modal-box` — centered white card, max-width 700px, scrollable, RTL
- Spinner while loading
- Close on backdrop click + ESC key

---

## 6. AdminModel changes

New method needed:
```php
AdminModel::getAllArticles(): array
// SELECT all articles joined with users + communities
// ORDER BY created_at DESC, no status filter
```

No other model changes. `ArticleModel` methods (`approve`, `reject`) reused by `article_action.php`.

---

## 7. Files to Create / Modify

| File | Change |
|------|--------|
| `api/admin/get_article.php` | New |
| `api/admin/get_post.php` | New |
| `api/admin/get_user.php` | New |
| `api/admin/article_action.php` | New |
| `api/admin/delete_post.php` | New |
| `app/models/AdminModel.php` | Add `getAllArticles()` |
| `app/controllers/AdminController.php` | Use `getAllArticles()` for articles tab |
| `app/views/admin/index.php` | Articles filter UI + modal HTML + posts/members preview buttons |
| `assets/js/app.js` | Add `openAdminModal`, `closeAdminModal`, filter logic |

---

## 8. Out of Scope

- Pagination for articles/posts/members lists (current 50-row limit stays)
- Comment deletion from within post modal
- Email notifications for status changes from `article_action.php` (EmailService is called — this is in scope)
- Any changes to the public-facing article or post views
