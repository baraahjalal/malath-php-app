# MVC Migration Log — malath-php-app
Branch: `feature/full-mvc`
Status: ✅ COMPLETE

## Core Layer Status (app/)

| File | Status | Notes |
|------|--------|-------|
| app/core/Database.php | ✅ | Singleton PDO |
| app/core/Model.php | ✅ | Base query/findById |
| app/core/Controller.php | ✅ | view/redirect/json/requireAuth/requireAdmin |
| app/bootstrap.php | ✅ | ROOT_PATH, session, autoload, CSRF |
| app/autoload.php | ✅ | PSR-4 for App\Core, App\Models, App\Controllers |

## Models

| Model | Status | Key Methods |
|-------|--------|-------------|
| UserModel | ✅ | findByEmail, create, emailExists, update, getSavedCount, getContributionsCount, getActivities, getSavedPosts |
| PostModel | ✅ | getPostsForFeed, create, deleteOwned, updateContent, getComments, getArticles, getArticleById |
| CommunityModel | ✅ | getAll, getUserCommunities, join, leave |
| AdminModel | ✅ NEW | getStats, getRecentPosts, getRecentUsers, getUsers, getPosts, deletePost, deleteUser, setUserRole |

## Controllers

| Controller | Status | Methods |
|------------|--------|---------|
| ArticleController | ✅ | index, show |
| AuthController | ✅ | showLogin, handleLogin, showRegister, handleRegister, logout |
| CommunityController | ✅ | index, handlePost |
| ProfileController | ✅ NEW | index, handlePostAction, handleUpdate |
| AdminController | ✅ NEW | index, handlePost |
| PageController | ✅ NEW | index, about, contact, faq, privacy, terms |

## Page Migration — Final Status

| Entry Point | Before | After | SQL in Controller? | SQL in View? |
|------------|--------|-------|--------------------|--------------|
| articles.php | Flat PHP | ✅ Controller→Model→View | No (Model) | No |
| articles-single.php | Flat PHP | ✅ Controller→Model→View | No (Model) | No |
| login.php | Flat PHP + SQL | ✅ Controller→Model→View | No (Model) | No |
| register.php | Flat PHP + SQL | ✅ Controller→Model→View | No (Model) | No |
| logout.php | Flat PHP | ✅ Controller→View | — | — |
| community.php | Flat PHP + SQL | ✅ Controller→Model→View | No (Model) | No |
| profile.php | Flat PHP + SQL | ✅ Controller→Model→View | No (Model) | No |
| update_profile.php | Flat PHP + SQL | ✅ Controller→Model | — | — |
| dashboard.php | Flat PHP + SQL | ✅ Controller→Model→View | No (Model) | No |
| index.php | Flat PHP | ✅ Controller→View | — | — |
| about.php | Flat PHP | ✅ Controller→View | — | — |
| contact.php | Flat PHP | ✅ Controller→View | — | — |
| faq.php | Flat PHP | ✅ Controller→View | — | — |
| privacy.php | Flat PHP | ✅ Controller→View | — | — |
| terms.php | Flat PHP | ✅ Controller→View | — | — |
| api/toggle_like.php | includes/db.php | ✅ bootstrap + Database::getInstance() | n/a (JSON API) | n/a |
| api/toggle_save.php | includes/db.php | ✅ bootstrap + Database::getInstance() | n/a | n/a |
| api/submit_comment.php | includes/db.php | ✅ bootstrap + Database::getInstance() | n/a | n/a |
| api/notifications.php | includes/db.php | ✅ bootstrap + Database::getInstance() | n/a | n/a |

## Audit Results

```
grep SELECT/INSERT/UPDATE/DELETE/prepare/$pdo in all root *.php → 0 results
grep SELECT/INSERT/UPDATE/DELETE/prepare/$pdo in app/views/**  → 0 results
```

**Zero SQL queries outside Models or api/ endpoints.**
**Zero HTML output outside Views.**
**Every page passes through: entry point (3-5 lines) → Controller → Model → View.**

## New View Files Created

- app/views/auth/login.php
- app/views/auth/register.php
- app/views/community/index.php
- app/views/profile/index.php
- app/views/admin/index.php
- app/views/pages/index.php
- app/views/pages/about.php
- app/views/pages/contact.php
- app/views/pages/faq.php
- app/views/pages/privacy.php
- app/views/pages/terms.php
- app/views/articles/index.php
- app/views/articles/single.php
- app/views/articles/create.php

---

## Articles System (نظام المقالات)

### Design Decisions
- Uses the existing `articles` table (status: pending → approved/rejected), separate from community `posts`.
- Any registered user can suggest an article — it enters as `status='pending'` and only appears publicly after admin approval.
- Saves/bookmarks use the existing `bookmarks` table which already has `article_id` + UNIQUE(user_id, article_id). No migrations needed.
- No comments or likes on articles — editorial, newspaper-style content only.

### Database Tables
| Table | Role |
|-------|------|
| `articles` | id, user_id, community_id, title, content, image, status (pending/approved/rejected), created_at |
| `bookmarks` | user_id, article_id, post_id — saves articles for later reading |
| `communities` | Provides the category filter (slug used for filtering) |

### New Files
| File | Purpose |
|------|---------|
| `article-create.php` | Entry point: GET→create(), POST→handleCreate() |
| `articles-single.php` | Entry point: show() |
| `api/toggle_article_save.php` | AJAX: save/unsave an article (uses bookmarks table) |
| `app/models/ArticleModel.php` | All article data methods (see below) |
| `app/views/articles/index.php` | Grid of approved articles + community filter tabs + pagination |
| `app/views/articles/single.php` | Full article view + AJAX save button |
| `app/views/articles/create.php` | Suggestion form + success confirmation page |

### ArticleModel Methods
| Method | Notes |
|--------|-------|
| `createSuggestion(userId, communityId, title, content, image)` | Inserts with status='pending' |
| `getApproved(?communitySlug, limit, offset)` | Filters by slug, JOINs users+communities |
| `countApproved(?communitySlug)` | For pagination |
| `countPending()` | For admin badge count |
| `getById(id)` | Approved-only enforced in query |
| `getPending()` | Admin only — oldest first |
| `approve(id)` / `reject(id)` | Admin actions |
| `toggleSave(userId, articleId)` | Returns bool: true=saved, false=unsaved |
| `isSaved(userId, articleId)` | For initial save button state |
| `getSavedArticles(userId)` | User profile saved articles list |

### Full Request Path
```
[User] → articles.php
         → ArticleController::index()
         → ArticleModel::getApproved() + CommunityModel::getAll()
         → app/views/articles/index.php

[User] → article-create.php (GET)
         → ArticleController::create()
         → app/views/articles/create.php (form)

[User] → article-create.php (POST)
         → ArticleController::handleCreate()
         → validates + uploads image to assets/uploads/articles/
         → ArticleModel::createSuggestion()
         → redirect ?sent=1

[Admin] → dashboard.php?tab=articles
          → AdminController::index() → ArticleModel::getPending()
          → admin view: approve ✅ / reject ❌ forms

[Admin] → dashboard.php (POST approve_article)
          → AdminController::handlePost()
          → ArticleModel::approve(id)
          → redirect ?msg=article_approved

[User] → articles-single.php?id=X
         → ArticleController::show()
         → ArticleModel::getById() + isSaved()
         → app/views/articles/single.php

[AJAX] → api/toggle_article_save.php (POST)
         → ArticleModel::toggleSave()
         → {success, saved}
```

### Cleanup Done (Phase 5)
- Deleted `article.php` (dead static HTML file)
- Removed `<option value="article">` from community post form
- Added type whitelist in `CommunityController::handlePost()` — only vent/advice/question accepted
- `PostModel` still contains dead `getArticles/countArticles/getArticleById` methods — safe to remove in future cleanup

### Updated Models Table
| Model | Status | Key Methods |
|-------|--------|-------------|
| UserModel | ✅ | findByEmail, create (sets default avatar), emailExists, update, getSavedCount, getContributionsCount, getActivities, getSavedPosts |
| PostModel | ✅ | getPostsForFeed, create, deleteOwned, deleteComment, updateContent, getComments |
| ArticleModel | ✅ NEW | createSuggestion, getApproved, countApproved, countPending, getById, getPending, approve, reject, toggleSave, isSaved, getSavedArticles |
| CommunityModel | ✅ | getAll, getUserCommunities, join, leave |
| AdminModel | ✅ | getStats, getRecentPosts, getRecentUsers, getUsers, getPosts, deletePost, deleteUser, setUserRole |
