<?php
include 'includes/db.php';
session_start();

// تفعيل عرض الأخطاء للتأكد من سبب المشكلة
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $avatar_path = null;

    // 1. معالجة الصورة
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_name = "user_" . $user_id . "_" . time() . "." . $ext;
            $target_dir = "assets/uploads/avatars/";
            
            // التأكد من وجود المجلد برمجياً
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . $new_name;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $avatar_path = $target_file;
            } else {
                die("خطأ: فشل نقل الملف للمجلد. تأكدي من صلاحيات المجلد.");
            }
        }
    }

    // 2. تحديث قاعدة البيانات
    try {
        if ($avatar_path) {
            $sql = "UPDATE users SET name = ?, bio = ?, avatar = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $bio, $avatar_path, $user_id]);
        } else {
            $sql = "UPDATE users SET name = ?, bio = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $bio, $user_id]);
        }
        
        // إعادة التوجيه للبروفايل بعد النجاح
        header("Location: profile.php?status=updated");
        exit;
    } catch (PDOException $e) {
        die("خطأ في قاعدة البيانات: " . $e->getMessage());
    }
}