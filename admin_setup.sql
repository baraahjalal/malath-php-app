-- تشغيل هذا الملف مرة واحدة فقط في phpMyAdmin أو MySQL CLI

-- 1. إضافة عمود role للمستخدمين
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS `role` ENUM('user','admin') NOT NULL DEFAULT 'user';

-- لتحويل مستخدم معين لمشرف (غيّر الرقم):
-- UPDATE users SET role = 'admin' WHERE id = 1;

-- 2. جدول الإشعارات
CREATE TABLE IF NOT EXISTS `notifications` (
    `id`          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED   NOT NULL,
    `actor_id`    INT UNSIGNED   NOT NULL,
    `type`        ENUM('like','comment','join') NOT NULL,
    `post_id`     INT UNSIGNED   DEFAULT NULL,
    `is_read`     TINYINT(1)     NOT NULL DEFAULT 0,
    `created_at`  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_read` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
