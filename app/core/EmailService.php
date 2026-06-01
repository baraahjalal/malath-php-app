<?php
namespace App\Core;

require_once ROOT_PATH . '/includes/phpmailer/Exception.php';
require_once ROOT_PATH . '/includes/phpmailer/PHPMailer.php';
require_once ROOT_PATH . '/includes/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {

    private static function buildMailer(): PHPMailer {
        require_once ROOT_PATH . '/includes/email_config.php';

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        return $mail;
    }

    public static function sendWelcome(string $name, string $email): void {
        try {
            $mail = self::buildMailer();
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'مرحباً بك في ملاذ!';
            $mail->Body    = "
                <div dir='rtl' style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
                    <h2 style='color:#c53a62;'>مرحباً {$name}! 🌸</h2>
                    <p>يسعدنا انضمامك إلى <strong>ملاذ</strong> — مجتمعنا الرقمي الآمن للمرأة.</p>
                    <p>يمكنك الآن:</p>
                    <ul>
                        <li>التفاعل مع مجتمعاتنا المتنوعة</li>
                        <li>نشر مقالاتك ومشاركة أفكارك</li>
                        <li>التواصل مع أخوات يشاركنك الاهتمامات</li>
                    </ul>
                    <p style='margin-top:2rem;color:#888;font-size:0.85rem;'>مع تحيات فريق ملاذ ❤️</p>
                </div>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log('EmailService::sendWelcome failed: ' . $e->getMessage());
        }
    }

    public static function sendNewUserNotification(string $name, string $email): void {
        try {
            require_once ROOT_PATH . '/includes/email_config.php';
            $mail = self::buildMailer();
            $mail->addAddress(ADMIN_EMAIL, 'المدير');
            $mail->isHTML(true);
            $mail->Subject = 'مستخدمة جديدة انضمت إلى ملاذ';
            $mail->Body    = "
                <div dir='rtl' style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
                    <h2 style='color:#c53a62;'>انضمام مستخدمة جديدة 📋</h2>
                    <p><strong>الاسم:</strong> {$name}</p>
                    <p><strong>البريد:</strong> {$email}</p>
                    <p><strong>التاريخ:</strong> " . date('Y-m-d H:i') . "</p>
                </div>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log('EmailService::sendNewUserNotification failed: ' . $e->getMessage());
        }
    }

    public static function sendArticleStatus(
        string $authorEmail,
        string $authorName,
        string $articleTitle,
        string $status
    ): void {
        try {
            $mail = self::buildMailer();
            $mail->addAddress($authorEmail, $authorName);
            $mail->isHTML(true);

            if ($status === 'approved') {
                $mail->Subject = 'تم قبول مقالتك في ملاذ ✅';
                $statusText    = 'تم <strong style="color:green;">قبول</strong>';
                $emoji         = '🎉';
                $extra         = '<p>مقالتك متاحة الآن للقراء في قسم المقالات.</p>';
            } else {
                $mail->Subject = 'تحديث حول مقالتك في ملاذ';
                $statusText    = 'تم <strong style="color:#c53a62;">رفض</strong>';
                $emoji         = '📝';
                $extra         = '<p>يمكنك تعديل مقالتك وإعادة إرسالها للمراجعة.</p>';
            }

            $mail->Body = "
                <div dir='rtl' style='font-family:Arial,sans-serif;max-width:600px;margin:auto;'>
                    <h2 style='color:#c53a62;'>{$emoji} تحديث حالة المقالة</h2>
                    <p>مرحباً {$authorName}،</p>
                    <p>{$statusText} مقالتك: <strong>" . htmlspecialchars($articleTitle) . "</strong></p>
                    {$extra}
                    <p style='margin-top:2rem;color:#888;font-size:0.85rem;'>فريق ملاذ ❤️</p>
                </div>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log('EmailService::sendArticleStatus failed: ' . $e->getMessage());
        }
    }
}
