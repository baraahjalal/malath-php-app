<?php
// Dashboard Layout (No standard header/footer)
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم | ملاذ</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { 
            background-color: var(--surface); 
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .admin-sidebar {
            width: 18rem;
            background-color: #FFFFFF;
            height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
            border-top-left-radius: 2rem;
            border-bottom-left-radius: 2rem;
            display: flex;
            flex-direction: column;
            padding: 2rem 0;
            z-index: 50;
            box-shadow: -5px 0 30px rgba(197, 58, 98, 0.05); /* Soft left shadow */
            transition: var(--transition-smooth);
        }

        .sidebar-header {
            padding: 0 2rem;
            margin-bottom: 2.5rem;
        }

        .sidebar-nav {
            flex-grow: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 0 1rem;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.85rem 1.5rem;
            border-radius: 1rem;
            text-decoration: none;
            color: var(--on-surface);
            font-weight: 700;
            font-family: var(--font-body);
            transition: var(--transition-fast);
        }

        .admin-nav-link:hover {
            background-color: var(--surface-container);
            color: var(--primary-dark);
            transform: translateX(-5px);
        }

        .admin-nav-link.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 1.5rem 2rem 0;
            border-top: 1px solid var(--outline-variant);
        }

        /* Main Content Styling */
        .admin-main {
            flex-grow: 1;
            margin-right: 18rem;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .admin-header {
            height: 5.5rem;
            position: sticky;
            top: 0;
            background: rgba(255, 250, 252, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 3rem;
            z-index: 40;
            border-bottom: 1px solid var(--surface-container-high);
            box-shadow: 0 4px 20px rgba(197, 58, 98, 0.02);
        }

        .admin-search {
            position: relative;
        }
        
        .admin-search input {
            background-color: #FFFFFF;
            border: 1px solid var(--outline-variant);
            border-radius: 2rem;
            padding: 0.75rem 1.5rem 0.75rem 3rem;
            width: 24rem;
            color: var(--on-surface);
            font-family: var(--font-body);
            transition: var(--transition-fast);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        
        .admin-search input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-container);
        }
        
        .admin-search i {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
        }

        .dashboard-content {
            padding: 2.5rem 3rem;
            flex-grow: 1;
        }

        .stat-card {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--outline-variant);
            transition: var(--transition-smooth);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-4px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .data-table-container {
            background-color: #ffffff;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--outline-variant);
            margin-bottom: 3rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        .data-table th {
            background-color: var(--surface-container);
            padding: 1.5rem 2rem;
            color: var(--secondary);
            font-size: 0.95rem;
            font-weight: 700;
            border-bottom: 1px solid var(--outline-variant);
            font-family: var(--font-headline);
        }

        .data-table td {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--surface-container-high);
            color: var(--on-surface);
            font-size: 1.05rem;
        }

        .data-table tr {
            transition: var(--transition-fast);
        }

        .data-table tr:hover {
            background-color: var(--surface-container-high);
        }

        .report-card {
            background-color: #FFFFFF;
            padding: 2rem;
            border-radius: 1.5rem;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--outline-variant);
            position: relative;
            overflow: hidden;
        }

        .report-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 6px; height: 100%;
        }

        .report-card.error::before { background-color: var(--error); }
        .report-card.warning::before { background-color: #F59E0B; }
        
        .pagination-btn {
            width: 2.75rem; height: 2.75rem;
            border-radius: 50%;
            border: 1px solid var(--outline-variant);
            background: white;
            color: var(--secondary);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition-fast);
        }
        
        .pagination-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .pagination-btn.active {
            background: var(--primary-gradient);
            color: white;
            border: none;
            box-shadow: var(--shadow-sm);
        }

    </style>
</head>
<body>

<!-- Sidebar embedded for standalone showcase -->
<aside class="admin-sidebar animate-fade-in-up">
    <div class="sidebar-header">
        <a href="index.php" class="font-headline font-black text-primary" style="font-size: 2.5rem; text-decoration: none;">ملاذ</a>
    </div>
    
    <nav class="sidebar-nav">
        <a href="#" class="admin-nav-link active">
            <i class="fa-solid fa-house" style="width: 20px; text-align: center;"></i>
            لوحة الإحصائيات
        </a>
        <a href="#" class="admin-nav-link">
            <i class="fa-solid fa-users" style="width: 20px; text-align: center;"></i>
            إدارة العضوات
        </a>
        <a href="#" class="admin-nav-link">
            <i class="fa-solid fa-file-lines" style="width: 20px; text-align: center;"></i>
            المقالات والمحتوى
        </a>
        <a href="#" class="admin-nav-link">
            <i class="fa-solid fa-comments" style="width: 20px; text-align: center;"></i>
            المنتدى والنقاشات
        </a>
        <a href="#" class="admin-nav-link">
            <i class="fa-solid fa-shield-halved" style="width: 20px; text-align: center;"></i>
            البلاغات والرقابة
        </a>
        <a href="#" class="admin-nav-link">
            <i class="fa-solid fa-chart-pie" style="width: 20px; text-align: center;"></i>
            تحليلات الأداء
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="#" class="admin-nav-link" style="color: var(--error);">
            <i class="fa-solid fa-arrow-right-from-bracket" style="width: 20px; text-align: center;"></i>
            تسجيل الخروج
        </a>
    </div>
</aside>

<main class="admin-main">
    <header class="admin-header">
        <div style="visibility: hidden;">Spacer</div> <!-- For flex distribution -->
        
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div class="admin-search">
                <input type="text" placeholder="البحث في لوحة التحكم...">
                <i class="fa-solid fa-search"></i>
            </div>
            
            <button class="icon-button" style="position: relative; background: #FFFFFF; border: 1px solid var(--outline-variant);">
                <i class="fa-regular fa-bell" style="color: var(--secondary);"></i>
                <span class="pulse-dot" style="position: absolute; top: 10px; right: 10px; width: 10px; height: 10px; background: var(--error);"></span>
            </button>
        </div>

        <div style="display: flex; align-items: center; gap: 1rem; border-right: 1px solid var(--outline-variant); padding-right: 1.5rem;">
            <div style="text-align: left;">
                <div class="font-headline font-bold text-primary" style="font-size: 1.15rem;">سارة القحطاني</div>
                <div style="font-size: 0.85rem; color: var(--secondary); font-weight: 600;">مديرة المنصة</div>
            </div>
            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=256&auto=format&fit=crop" alt="Avatar" class="profile-avatar" style="width: 3.5rem; height: 3.5rem; border-color: var(--primary-container);">
        </div>
    </header>

    <div class="dashboard-content">
        <!-- Stats Grid -->
        <section class="stats-grid">
            <div class="stat-card animate-fade-in-up delay-100">
                <span style="color: var(--secondary); font-weight: 700; font-family: var(--font-headline); font-size: 1.1rem;">إجمالي العضوات</span>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                    <span class="font-headline font-black text-primary-dark" style="font-size: 2.25rem;">١٢,٨٤٠</span>
                    <div style="width: 3.5rem; height: 3.5rem; border-radius: 1rem; background-color: var(--surface-container); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.5rem;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up delay-200">
                <span style="color: var(--secondary); font-weight: 700; font-family: var(--font-headline); font-size: 1.1rem;">المقالات المنشورة</span>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                    <span class="font-headline font-black text-primary-dark" style="font-size: 2.25rem;">٨٤٢</span>
                    <div style="width: 3.5rem; height: 3.5rem; border-radius: 1rem; background-color: var(--surface-container); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.5rem;">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                </div>
            </div>
            <div class="stat-card animate-fade-in-up delay-300" style="grid-column: span 2; background: var(--primary-gradient); color: white; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow-primary); border: none; position: relative; overflow: hidden;">
                <!-- Decorative background elements -->
                <div style="position: absolute; width: 200px; height: 200px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%); top: -50px; left: -50px;"></div>
                
                <div style="position: relative; z-index: 10;">
                    <div style="opacity: 0.9; margin-bottom: 0.5rem; font-family: var(--font-headline); font-size: 1.25rem; font-weight: 700;">نمو المجتمع الرقمي</div>
                    <div class="font-headline font-black" style="font-size: 2.75rem; margin-bottom: 0.25rem;">+١٥%</div>
                    <div style="font-size: 0.9rem; opacity: 0.8; font-weight: 600;">ارتفاع إيجابي مقارنة بالشهر الماضي</div>
                </div>
                <div style="display: flex; align-items: flex-end; gap: 0.35rem; height: 4.5rem; position: relative; z-index: 10;">
                    <div style="width: 0.6rem; background-color: rgba(255,255,255,0.2); height: 1.5rem; border-radius: 4px;"></div>
                    <div style="width: 0.6rem; background-color: rgba(255,255,255,0.4); height: 2.5rem; border-radius: 4px;"></div>
                    <div style="width: 0.6rem; background-color: rgba(255,255,255,0.6); height: 2rem; border-radius: 4px;"></div>
                    <div style="width: 0.6rem; background-color: rgba(255,255,255,0.8); height: 3.5rem; border-radius: 4px;"></div>
                    <div style="width: 0.6rem; background-color: white; height: 4.5rem; border-radius: 4px;"></div>
                </div>
            </div>
        </section>

        <!-- Articles Table -->
        <section style="margin-bottom: 3.5rem;" class="animate-fade-in-up">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="font-headline font-bold text-primary-dark" style="font-size: 1.75rem;">أحدث المقالات المراجعة</h2>
                <div style="display: flex; gap: 1rem;">
                    <button class="btn-outline" style="padding: 0.6rem 1.5rem; font-size: 0.95rem; border-radius: 2rem; display: flex; align-items: center; gap: 0.5rem; background: #FFFFFF;">
                        <i class="fa-solid fa-filter"></i> تصفية
                    </button>
                </div>
            </div>
            
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>العنوان</th>
                            <th>الكاتبة</th>
                            <th>التصنيف</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1.25rem;">
                                    <img src="https://images.unsplash.com/photo-1499209974431-9dddcece7f88?q=80&w=256&auto=format&fit=crop" style="width: 3.5rem; height: 3.5rem; border-radius: 0.75rem; object-fit: cover;">
                                    <span style="font-weight: 800; font-family: var(--font-headline); color: var(--on-surface);">كيفية الموازنة بين العمل والحياة الخاصة</span>
                                </div>
                            </td>
                            <td style="font-weight: 600;">نورة محمد</td>
                            <td><span class="badge" style="background-color: var(--primary-container); color: var(--primary-dark); font-size: 0.8rem; border: none;">تطوير الذات</span></td>
                            <td><span style="color: #10B981; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;"><span style="width: 10px; height: 10px; background-color: #10B981; border-radius: 50%;"></span> منشور</span></td>
                            <td style="color: var(--secondary); font-size: 0.95rem;">١٢ مايو ٢٠٢٤</td>
                            <td>
                                <div style="display: flex; gap: 1rem;">
                                    <button style="border: none; background: var(--surface-container); width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; color: var(--primary); cursor: pointer; transition: var(--transition-fast);"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button style="border: none; background: #FEE2E2; width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; color: var(--error); cursor: pointer; transition: var(--transition-fast);"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1.25rem;">
                                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=256&auto=format&fit=crop" style="width: 3.5rem; height: 3.5rem; border-radius: 0.75rem; object-fit: cover;">
                                    <span style="font-weight: 800; font-family: var(--font-headline); color: var(--on-surface);">بناء فريق نسائي ناجح</span>
                                </div>
                            </td>
                            <td style="font-weight: 600;">ليلى أحمد</td>
                            <td><span class="badge" style="background-color: var(--primary-container); color: var(--primary-dark); font-size: 0.8rem; border: none;">ريادة أعمال</span></td>
                            <td><span style="color: #F59E0B; font-weight: 700; display: flex; align-items: center; gap: 0.5rem;"><span style="width: 10px; height: 10px; background-color: #F59E0B; border-radius: 50%;"></span> قيد المراجعة</span></td>
                            <td style="color: var(--secondary); font-size: 0.95rem;">١٠ مايو ٢٠٢٤</td>
                            <td>
                                <div style="display: flex; gap: 1rem;">
                                    <button style="border: none; background: var(--surface-container); width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; color: var(--primary); cursor: pointer; transition: var(--transition-fast);"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <button style="border: none; background: #FEE2E2; width: 2.5rem; height: 2.5rem; border-radius: 0.5rem; color: var(--error); cursor: pointer; transition: var(--transition-fast);"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div style="display: flex; justify-content: center; gap: 0.5rem;">
                <button class="pagination-btn"><i class="fa-solid fa-chevron-right"></i></button>
                <button class="pagination-btn active">١</button>
                <button class="pagination-btn">٢</button>
                <button class="pagination-btn">٣</button>
                <span style="color: var(--secondary); display: flex; align-items: center; justify-content: center; width: 2.5rem;">...</span>
                <button class="pagination-btn"><i class="fa-solid fa-chevron-left"></i></button>
            </div>
        </section>

        <!-- Reports Section -->
        <section class="animate-fade-in-up">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="font-headline font-bold text-primary-dark" style="font-size: 1.75rem;">إشعارات وبلاغات سريعة</h2>
                <span class="badge" style="background-color: var(--surface-container-high); color: var(--primary-dark); border: 1px solid var(--outline-variant); font-size: 0.85rem;"><span class="pulse-dot"></span> ٢ تنبيه جديد</span>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="report-card error">
                    <div style="display: flex; gap: 1.25rem;">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?q=80&w=256&auto=format&fit=crop" style="width: 3.5rem; height: 3.5rem; border-radius: 50%; object-fit: cover; border: 2px solid white; box-shadow: var(--shadow-sm);">
                        <div>
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                <span style="font-weight: 800; font-family: var(--font-headline); font-size: 1.2rem; color: var(--on-surface);">مريم الكندي</span>
                                <span style="font-size: 0.85rem; color: var(--secondary); font-weight: 600;">منذ ساعتين</span>
                            </div>
                            <p style="color: var(--on-surface); font-size: 1.05rem; max-width: 48rem; line-height: 1.6;">تنبيه آلي: تم رصد محتوى قد يخالف إرشادات المجتمع (لغة حادة) في منتدى 'ريادة الأعمال'. يرجى المراجعة واتخاذ الإجراء اللازم للحفاظ على بيئتنا الإيجابية.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button class="btn-primary" style="padding: 0.6rem 1.5rem; font-size: 0.95rem; border-radius: 2rem;">مراجعة المحتوى</button>
                    </div>
                </div>

                <div class="report-card warning">
                    <div style="display: flex; gap: 1.25rem;">
                        <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=256&auto=format&fit=crop" style="width: 3.5rem; height: 3.5rem; border-radius: 50%; object-fit: cover; border: 2px solid white; box-shadow: var(--shadow-sm);">
                        <div>
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                <span style="font-weight: 800; font-family: var(--font-headline); font-size: 1.2rem; color: var(--on-surface);">تحديث النظام</span>
                                <span style="font-size: 0.85rem; color: var(--secondary); font-weight: 600;">منذ ٥ ساعات</span>
                            </div>
                            <p style="color: var(--on-surface); font-size: 1.05rem; max-width: 48rem; line-height: 1.6;">تم اكتمال النسخ الاحتياطي لقاعدة البيانات بنجاح للحفاظ على سير العمل. تذكرين أننا مستمرون في تقديم الدعم لكِ.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button class="btn-outline" style="padding: 0.6rem 1.5rem; font-size: 0.95rem; border-radius: 2rem; background: #FFFFFF;">عرض التفاصيل</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Admin Footer -->
        <footer style="margin-top: 4rem; padding-top: 2.5rem; border-top: 1px solid var(--outline-variant); text-align: center; color: var(--secondary); font-size: 0.95rem; font-weight: 600;">
            <p>ملاذ - مساحة آمنة للتميز، صممت بحب. © ٢٠٢٤ جميع الحقوق محفوظة.</p>
        </footer>
    </div>
</main>

<script>
    // Just mock js for interactive feel when required
</script>
</body>
</html>
