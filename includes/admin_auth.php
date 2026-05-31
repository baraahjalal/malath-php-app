<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/csrf.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// تحقق من صلاحية المشرف
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    include __DIR__ . '/../includes/header.php';
    echo '<div style="text-align:center;padding:5rem;"><h2>403 — غير مصرح لك بالوصول</h2><a href="../index.php" class="btn-primary">العودة للرئيسية</a></div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}
