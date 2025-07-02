<?php
require 'vendor/autoload.php';
require 'database.php'; // استيراد ملف الاتصال بقاعدة البيانات
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// جلب التعليقات وحالة البلاغ من فورم الواجهة الأمامية
$comments = $_POST['comments'] ?? '';
$status = $_POST['statuse'] ?? '';
$report_id = $_POST['report_id'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($report_id)) {
    // جلب البيانات من جدول reports بناءً على report_id
    $sql = "SELECT * FROM reports WHERE report_id = ?";
    $stmt = sqlsrv_query($conn, $sql, [$report_id]);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $report = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($report) {
        $rfid_tag = $report['rfid_tag'];

        // البحث عن المستخدم صاحب البلاغ من جدول users باستخدام rfid_tag
        $sql_user = "SELECT * FROM users WHERE rfid_tag = ?";
        $stmt_user = sqlsrv_query($conn, $sql_user, [$rfid_tag]);

        if ($stmt_user === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $user = sqlsrv_fetch_array($stmt_user, SQLSRV_FETCH_ASSOC);
        if ($user) {
            $user_email = $user['email'];
            $username = $user['username'];

            // تحديث حالة البلاغ والتعليقات في جدول reports
            $sql_update = "UPDATE reports SET statuse = ?, comments = ? WHERE report_id = ?";
            $params = [$status, $comments, $report_id];
            $stmt_update = sqlsrv_query($conn, $sql_update, $params);

            if ($stmt_update === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            // إرسال بريد إلكتروني للمستخدم حول حالة البلاغ
            $mail = new PHPMailer(true);

            try {
                // إعدادات SMTP لإرسال البريد
                $mail->isSMTP();
                $mail->Host = 'smtp.example.com'; // استبدل هذا بمزود SMTP الخاص بك
                $mail->SMTPAuth = true;
                $mail->Username = 'your_email@example.com'; // بريدك الإلكتروني
                $mail->Password = 'your_password'; // كلمة المرور لبريدك
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // إعدادات البريد الإلكتروني
                $mail->setFrom('your_email@example.com', 'Incident Reporting System');
                $mail->addAddress($user_email, $username); // إرسال البريد إلى صاحب البلاغ

                // محتوى البريد الإلكتروني
                $mail->isHTML(true);
                $mail->Subject = 'تحديث حالة بلاغك';
                $mail->Body = "
                    <p>مرحباً {$username},</p>
                    <p>تم تحديث حالة بلاغك:</p>
                    <ul>
                        <li><strong>حالة البلاغ:</strong> {$status}</li>
                        <li><strong>تعليقات:</strong> {$comments}</li>
                    </ul>
                    <p>شكرًا لاستخدامك نظام البلاغات!</p>
                ";

                $mail->send();
                echo 'تم إرسال تحديث حالة البلاغ إلى البريد الإلكتروني بنجاح.';
            } catch (Exception $e) {
                echo "لم يتم إرسال البريد الإلكتروني. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "لم يتم العثور على المستخدم صاحب البلاغ.";
        }
    } else {
        echo "لم يتم العثور على البلاغ.";
    }
}

sqlsrv_close($conn);
?>
