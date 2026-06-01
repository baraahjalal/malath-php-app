<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم | ملاذ</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/malath-php-app/assets/css/style.css">
    <style>
        body { background-color:var(--surface); display:flex; min-height:100vh; overflow-x:hidden; }
        .admin-sidebar { width:17rem; background:#fff; height:100vh; position:fixed; right:0; top:0; border-top-left-radius:2rem; border-bottom-left-radius:2rem; display:flex; flex-direction:column; padding:2rem 0; z-index:50; box-shadow:-5px 0 30px rgba(197,58,98,0.06); }
        .sidebar-header { padding:0 2rem; margin-bottom:2rem; }
        .sidebar-nav { flex-grow:1; overflow-y:auto; display:flex; flex-direction:column; gap:.4rem; padding:0 1rem; }
        .admin-nav-link { display:flex; align-items:center; gap:.9rem; padding:.8rem 1.4rem; border-radius:1rem; text-decoration:none; color:var(--on-surface); font-weight:700; font-family:var(--font-body); transition:all .2s; }
        .admin-nav-link:hover { background:var(--surface-container); color:var(--primary-dark); transform:translateX(-4px); }
        .admin-nav-link.active { background:var(--primary-gradient); color:white; box-shadow:var(--shadow-sm); }
        .admin-nav-link i { width:20px; text-align:center; }
        .sidebar-footer { margin-top:auto; padding:1.5rem 2rem 0; border-top:1px solid var(--outline-variant); }
        .admin-main { flex-grow:1; margin-right:17rem; display:flex; flex-direction:column; min-width:0; }
        .admin-header { height:5rem; position:sticky; top:0; background:rgba(255,250,252,.85); backdrop-filter:blur(16px); display:flex; align-items:center; justify-content:space-between; padding:0 2.5rem; z-index:40; border-bottom:1px solid var(--surface-container-high); }
        .dashboard-content { padding:2rem 2.5rem; flex-grow:1; }
        .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1.2rem; margin-bottom:2.5rem; }
        .stat-card { background:#fff; padding:1.8rem; border-radius:1.5rem; box-shadow:var(--shadow-sm); border:1px solid var(--outline-variant); transition:transform .25s,box-shadow .25s; }
        .stat-card:hover { transform:translateY(-4px); box-shadow:var(--shadow-md); }
        .stat-icon { width:3rem; height:3rem; border-radius:.8rem; background:var(--surface-container); display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:1.3rem; }
        .data-card { background:#fff; border-radius:1.5rem; overflow:hidden; box-shadow:var(--shadow-sm); border:1px solid var(--outline-variant); margin-bottom:2rem; }
        .data-card-header { display:flex; justify-content:space-between; align-items:center; padding:1.5rem 2rem; border-bottom:1px solid var(--outline-variant); }
        .data-table { width:100%; border-collapse:collapse; text-align:right; }
        .data-table th { background:var(--surface-container); padding:1rem 1.5rem; color:var(--secondary); font-size:.9rem; font-weight:700; border-bottom:1px solid var(--outline-variant); }
        .data-table td { padding:1rem 1.5rem; border-bottom:1px solid var(--surface-container-high); font-size:.95rem; }
        .data-table tr:hover td { background:var(--surface-container-high); }
        .data-table tr:last-child td { border-bottom:none; }
        .badge-role-admin { background:#fce7f3; color:#be185d; padding:.25rem .8rem; border-radius:1rem; font-size:.8rem; font-weight:700; }
        .badge-role-user  { background:var(--surface-container); color:var(--secondary); padding:.25rem .8rem; border-radius:1rem; font-size:.8rem; font-weight:700; }
        .btn-sm { padding:.3rem .9rem; border-radius:1rem; font-size:.8rem; font-weight:700; border:none; cursor:pointer; transition:all .2s; font-family:inherit; }
        .btn-danger { background:#fee2e2; color:#b91c1c; }
        .btn-danger:hover { background:#fca5a5; }
        .btn-info { background:#e0f2fe; color:#0369a1; }
        .btn-info:hover { background:#bae6fd; }
        .search-box { display:flex; gap:.8rem; padding:1.2rem 2rem; border-bottom:1px solid var(--outline-variant); }
        .search-box input { flex-grow:1; padding:.6rem 1.2rem; border:1px solid var(--outline-variant); border-radius:2rem; font-family:inherit; font-size:.95rem; outline:none; }
        .search-box input:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(197,58,98,.08); }
        .alert-banner { padding:.9rem 1.5rem; border-radius:1rem; margin-bottom:1.5rem; font-weight:600; font-size:.95rem; }
        .alert-success { background:#dcfce7; color:#166534; }
        .badge-type { padding:.25rem .7rem; border-radius:1rem; font-size:.75rem; font-weight:700; }
        .badge-vent    { background:#fce7f3; color:#be185d; }
        .badge-advice  { background:#fef3c7; color:#d97706; }
        .badge-question{ background:#e0f2fe; color:#0284c7; }
        .badge-article { background:var(--primary-container); color:var(--primary-dark); }
        .post-preview { max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--secondary); font-size:.9rem; }
    </style>
</head>
<body>

<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="/malath-php-app/index.php" class="font-headline font-black text-primary" style="font-size:2.2rem;text-decoration:none;">ملاذ</a>
        <div style="font-size:.8rem;color:var(--secondary);margin-top:.2rem;">لوحة التحكم</div>
    </div>
    <nav class="sidebar-nav">
        <a href="/malath-php-app/dashboard.php?tab=overview" class="admin-nav-link <?= $tab==='overview'?'active':'' ?>"><i class="fa-solid fa-house"></i> الإحصائيات</a>
        <a href="/malath-php-app/dashboard.php?tab=users"    class="admin-nav-link <?= $tab==='users'?'active':'' ?>"><i class="fa-solid fa-users"></i> إدارة العضوات</a>
        <a href="/malath-php-app/dashboard.php?tab=posts"    class="admin-nav-link <?= $tab==='posts'?'active':'' ?>"><i class="fa-solid fa-comments"></i> إدارة المنشورات</a>
        <a href="/malath-php-app/dashboard.php?tab=articles" class="admin-nav-link <?= $tab==='articles'?'active':'' ?>" style="position:relative;">
            <i class="fa-solid fa-newspaper"></i> مقالات معلّقة
            <?php if($pending_count > 0): ?>
            <span style="background:#e03052;color:#fff;font-size:.7rem;font-weight:800;padding:.15rem .55rem;border-radius:2rem;margin-right:.4rem;"><?= $pending_count ?></span>
            <?php endif; ?>
        </a>
        <a href="/malath-php-app/index.php" class="admin-nav-link"><i class="fa-solid fa-globe"></i> الموقع الرئيسي</a>
    </nav>
    <div class="sidebar-footer">
        <a href="/malath-php-app/logout.php" class="admin-nav-link" style="color:var(--error);"><i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل الخروج</a>
    </div>
</aside>

<main class="admin-main">
    <header class="admin-header">
        <h2 class="font-headline font-bold text-primary-dark" style="font-size:1.3rem;margin:0;">
            <?= ['overview'=>'لوحة الإحصائيات','users'=>'إدارة العضوات','posts'=>'إدارة المنشورات','articles'=>'مقالات معلّقة'][$tab] ?? 'لوحة التحكم' ?>
        </h2>
        <div style="display:flex;align-items:center;gap:1rem;">
            <span style="font-size:.9rem;color:var(--secondary);">مرحباً،</span>
            <strong class="font-headline text-primary"><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
        </div>
    </header>

    <div class="dashboard-content">
        <?php if(isset($_GET['msg'])): ?>
        <div class="alert-banner alert-success">
            <?= ['post_deleted'=>'✅ تم حذف المنشور.','user_deleted'=>'✅ تم حذف العضو.','article_approved'=>'✅ تمت الموافقة — المقالة منشورة الآن.','article_rejected'=>'🗑 تم رفض المقالة.'][$_GET['msg']] ?? '' ?>
        </div>
        <?php endif; ?>

        <?php if($tab === 'overview'): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                    <span style="color:var(--secondary);font-weight:700;font-size:1rem;">إجمالي العضوات</span>
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                </div>
                <div class="font-headline font-black text-primary-dark" style="font-size:2.2rem;"><?= number_format($total_users) ?></div>
                <div style="font-size:.85rem;color:#10b981;margin-top:.5rem;font-weight:600;">+<?= $new_users_week ?> هذا الأسبوع</div>
            </div>
            <div class="stat-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                    <span style="color:var(--secondary);font-weight:700;font-size:1rem;">إجمالي المنشورات</span>
                    <div class="stat-icon"><i class="fa-solid fa-pen-nib"></i></div>
                </div>
                <div class="font-headline font-black text-primary-dark" style="font-size:2.2rem;"><?= number_format($total_posts) ?></div>
                <div style="font-size:.85rem;color:#10b981;margin-top:.5rem;font-weight:600;">+<?= $new_posts_week ?> هذا الأسبوع</div>
            </div>
            <div class="stat-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                    <span style="color:var(--secondary);font-weight:700;font-size:1rem;">التعليقات</span>
                    <div class="stat-icon"><i class="fa-solid fa-comments"></i></div>
                </div>
                <div class="font-headline font-black text-primary-dark" style="font-size:2.2rem;"><?= number_format($total_comments) ?></div>
            </div>
            <div class="stat-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                    <span style="color:var(--secondary);font-weight:700;font-size:1rem;">الإعجابات</span>
                    <div class="stat-icon"><i class="fa-solid fa-heart"></i></div>
                </div>
                <div class="font-headline font-black text-primary-dark" style="font-size:2.2rem;"><?= number_format($total_likes) ?></div>
            </div>
        </div>

        <div class="data-card">
            <div class="data-card-header">
                <h3 class="font-headline font-bold text-primary-dark" style="margin:0;font-size:1.2rem;">آخر المنشورات</h3>
                <a href="/malath-php-app/dashboard.php?tab=posts" class="btn-sm btn-info">عرض الكل</a>
            </div>
            <table class="data-table">
                <thead><tr><th>المستخدمة</th><th>المجتمع</th><th>النوع</th><th>المحتوى</th><th>التاريخ</th></tr></thead>
                <tbody>
                <?php foreach($recent_posts as $r): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['user_name']) ?></strong></td>
                    <td><?= htmlspecialchars($r['community_name']) ?></td>
                    <td><span class="badge-type badge-<?= $r['type'] ?>"><?= ['vent'=>'فضفضة','advice'=>'نصيحة','question'=>'سؤال','article'=>'مقالة'][$r['type']] ?? $r['type'] ?></span></td>
                    <td class="post-preview"><?= htmlspecialchars(mb_substr($r['content'],0,60)) ?>…</td>
                    <td style="color:var(--secondary);font-size:.85rem;"><?= date('Y-m-d', strtotime($r['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="data-card">
            <div class="data-card-header">
                <h3 class="font-headline font-bold text-primary-dark" style="margin:0;font-size:1.2rem;">آخر التسجيلات</h3>
                <a href="/malath-php-app/dashboard.php?tab=users" class="btn-sm btn-info">إدارة الكل</a>
            </div>
            <table class="data-table">
                <thead><tr><th>الاسم</th><th>البريد</th><th>الدور</th><th>تاريخ التسجيل</th></tr></thead>
                <tbody>
                <?php foreach($recent_users as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                    <td style="color:var(--secondary);"><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge-role-<?= $u['role'] ?>"><?= $u['role']==='admin'?'مشرفة':'عضوة' ?></span></td>
                    <td style="color:var(--secondary);font-size:.85rem;"><?= date('Y-m-d', strtotime($u['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php elseif($tab === 'users'): ?>
        <div class="data-card">
            <div class="data-card-header">
                <h3 class="font-headline font-bold text-primary-dark" style="margin:0;font-size:1.2rem;">العضوات (<?= $total_users ?>)</h3>
            </div>
            <form method="GET" class="search-box">
                <input type="hidden" name="tab" value="users">
                <input type="text" name="search" placeholder="ابحثي باسم أو بريد..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit" class="btn-sm btn-info" style="padding:.6rem 1.4rem;">بحث</button>
            </form>
            <table class="data-table">
                <thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>الدور</th><th>تاريخ التسجيل</th><th>إجراءات</th></tr></thead>
                <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td style="color:var(--secondary);"><?= $u['id'] ?></td>
                    <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                    <td style="color:var(--secondary);"><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge-role-<?= $u['role'] ?>"><?= $u['role']==='admin'?'مشرفة':'عضوة' ?></span></td>
                    <td style="color:var(--secondary);font-size:.85rem;"><?= date('Y-m-d', strtotime($u['created_at'])) ?></td>
                    <td>
                        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" action="/malath-php-app/dashboard.php" style="display:inline;">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="new_role" value="<?= $u['role']==='admin'?'user':'admin' ?>">
                                <button type="submit" name="toggle_role" class="btn-sm btn-info"><?= $u['role']==='admin'?'إزالة المشرفة':'ترقية لمشرفة' ?></button>
                            </form>
                            <form method="POST" action="/malath-php-app/dashboard.php" style="display:inline;" onsubmit="return confirm('حذف هذه العضو نهائياً؟');">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                                <button type="submit" name="delete_user" class="btn-sm btn-danger">حذف</button>
                            </form>
                            <?php else: ?>
                            <span style="font-size:.8rem;color:var(--secondary);">أنتِ</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php elseif($tab === 'articles'): ?>
        <?php if(empty($pending_articles)): ?>
        <div class="data-card" style="text-align:center;padding:4rem 2rem;">
            <i class="fa-solid fa-circle-check" style="font-size:3rem;color:#10b981;margin-bottom:1rem;display:block;"></i>
            <h3 style="font-family:var(--font-headline);color:var(--secondary);margin:0;">لا توجد مقالات معلّقة — كل شيء راجعتِه ✅</h3>
        </div>
        <?php else: ?>
        <div style="display:grid;gap:1.5rem;">
            <?php foreach($pending_articles as $a): ?>
            <div class="data-card">
                <div style="padding:1.5rem 2rem;">
                    <div style="display:flex;align-items:flex-start;gap:1.2rem;margin-bottom:1rem;">
                        <img src="/malath-php-app/<?= htmlspecialchars($a['author_avatar'] ?: 'assets/images/default-avatar.png') ?>"
                             style="width:2.6rem;height:2.6rem;border-radius:50%;object-fit:cover;flex-shrink:0;" alt="">
                        <div style="flex-grow:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;margin-bottom:.4rem;">
                                <strong style="font-family:var(--font-headline);color:var(--primary-dark);"><?= htmlspecialchars($a['author_name']) ?></strong>
                                <span class="badge-type badge-article"><?= htmlspecialchars($a['community_name']) ?></span>
                                <span style="color:var(--secondary);font-size:.8rem;"><?= date('Y-m-d', strtotime($a['created_at'])) ?></span>
                            </div>
                            <h3 style="font-family:var(--font-headline);font-size:1.1rem;font-weight:900;color:var(--on-surface);margin:0 0 .6rem;">
                                <?= htmlspecialchars($a['title'] ?: mb_substr($a['content'], 0, 70) . '…') ?>
                            </h3>
                            <p style="color:var(--secondary);font-size:.9rem;line-height:1.65;margin:0;">
                                <?= htmlspecialchars(mb_substr(strip_tags($a['content']), 0, 200)) ?>…
                            </p>
                        </div>
                    </div>
                    <div style="display:flex;gap:.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid var(--outline-variant);">
                        <form method="POST" action="/malath-php-app/dashboard.php" style="display:inline;">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="article_id" value="<?= $a['id'] ?>">
                            <button type="submit" name="approve_article" class="btn-sm" style="background:#dcfce7;color:#166534;padding:.5rem 1.6rem;font-size:.88rem;">
                                ✅ موافقة — نشر الآن
                            </button>
                        </form>
                        <form method="POST" action="/malath-php-app/dashboard.php" style="display:inline;"
                              onsubmit="return confirm('رفض هذه المقالة نهائياً؟');">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="article_id" value="<?= $a['id'] ?>">
                            <button type="submit" name="reject_article" class="btn-sm btn-danger" style="padding:.5rem 1.6rem;font-size:.88rem;">
                                ❌ رفض
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php elseif($tab === 'posts'): ?>
        <div class="data-card">
            <div class="data-card-header">
                <h3 class="font-headline font-bold text-primary-dark" style="margin:0;font-size:1.2rem;">المنشورات (<?= $total_posts ?>)</h3>
            </div>
            <table class="data-table">
                <thead><tr><th>الكاتبة</th><th>المجتمع</th><th>النوع</th><th>المحتوى</th><th>❤ إعجاب</th><th>💬 تعليق</th><th>التاريخ</th><th>حذف</th></tr></thead>
                <tbody>
                <?php foreach($posts as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['user_name']) ?></strong></td>
                    <td><?= htmlspecialchars($p['community_name']) ?></td>
                    <td><span class="badge-type badge-<?= $p['type'] ?>"><?= ['vent'=>'فضفضة','advice'=>'نصيحة','question'=>'سؤال','article'=>'مقالة'][$p['type']] ?? $p['type'] ?></span></td>
                    <td class="post-preview"><?php if($p['title']): ?><strong><?= htmlspecialchars(mb_substr($p['title'],0,30)) ?> — </strong><?php endif; ?><?= htmlspecialchars(mb_substr($p['content'],0,50)) ?>…</td>
                    <td style="text-align:center;"><?= $p['likes'] ?></td>
                    <td style="text-align:center;"><?= $p['comments'] ?></td>
                    <td style="color:var(--secondary);font-size:.85rem;"><?= date('Y-m-d', strtotime($p['created_at'])) ?></td>
                    <td>
                        <form method="POST" action="/malath-php-app/dashboard.php" style="display:inline;" onsubmit="return confirm('حذف هذا المنشور نهائياً؟');">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="post_id" value="<?= $p['id'] ?>">
                            <button type="submit" name="delete_post" class="btn-sm btn-danger">حذف</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</main>

<script src="/malath-php-app/assets/js/app.js"></script>
</body>
</html>
