<?php
session_start();
header('Content-Type: application/json');

// إعدادات الاتصال بقاعدة البيانات
$serverName = "192.168.3.88\\iisbash";
$uid = "arduino";
$pass = "Root@456123";
$database = "attendance_db";
$connectionOptions = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pass,
    "CharacterSet" => "UTF-8"
];

try { 
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if (!$conn) {
        throw new Exception("فشل الاتصال بقاعدة البيانات: " . json_encode(sqlsrv_errors()));
    }

    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $rfid_tag = $_POST['rfid_tag'] ?? null;

    if (is_null($email) && is_null($password) && is_null($rfid_tag)) {
        throw new Exception("يجب إدخال البريد الإلكتروني وكلمة المرور أو بطاقة NFC.");
    }

    if (!is_null($rfid_tag)) {
        // تسجيل الدخول باستخدام بطاقة NFC
        $employee_sql = "SELECT * FROM employees WHERE rfid_tag = ?";
        $employee_params = [$rfid_tag];
        $employee_stmt = sqlsrv_query($conn, $employee_sql, $employee_params);

        if ($employee_stmt === false) {
            throw new Exception("خطأ في استعلام بيانات الموظف: " . json_encode(sqlsrv_errors()));
        }

        $employee = sqlsrv_fetch_array($employee_stmt, SQLSRV_FETCH_ASSOC);

        if ($employee) {
            $_SESSION['loggedin'] = true;
            $_SESSION['rfid_tag'] = $employee['rfid_tag'];
            $_SESSION['employee_name'] = $employee['name'];

            echo json_encode(['success' => true, 'message' => 'تم تسجيل الدخول بنجاح باستخدام بطاقة NFC.', 'employee_name' => $employee['name']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'لم يتم العثور على بطاقة NFC.']);
        }
    } else {
        if (is_null($email) || is_null($password)) {
            throw new Exception("يجب إدخال البريد الإلكتروني وكلمة المرور.");
        }

        $user_sql = "SELECT * FROM users WHERE email = ?";
        $user_stmt = sqlsrv_query($conn, $user_sql, [$email]);

        if ($user_stmt === false) {
            throw new Exception("خطأ في استعلام المستخدم: " . json_encode(sqlsrv_errors()));
        }

        $user = sqlsrv_fetch_array($user_stmt, SQLSRV_FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $employee_sql = "SELECT * FROM employees WHERE rfid_tag = ?";
            $employee_stmt = sqlsrv_query($conn, $employee_sql, [$user['rfid_tag']]);

            if ($employee_stmt === false) {
                throw new Exception("خطأ في استعلام الموظف: " . json_encode(sqlsrv_errors()));
            }

            $employee = sqlsrv_fetch_array($employee_stmt, SQLSRV_FETCH_ASSOC);

            if ($employee) {
                $_SESSION['loggedin'] = true;
                $_SESSION['employee_name'] = $employee['name'];

                echo json_encode(['success' => true, 'message' => 'تم تسجيل الدخول بنجاح.', 'employee_name' => $employee['name']]);
            } else {
                echo json_encode(['success' => false, 'error' => 'لم يتم العثور على بطاقة NFC.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'بيانات تسجيل الدخول غير صحيحة.']);
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if ($conn) {
        sqlsrv_close($conn);
    }
}
?>
