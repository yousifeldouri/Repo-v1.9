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
    // الاتصال بقاعدة البيانات
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if (!$conn) {
        throw new Exception("فشل الاتصال بقاعدة البيانات: " . print_r(sqlsrv_errors(), true));
    }
    if (!$conn) {
        echo json_encode(['success' => false, 'error' => 'فشل الاتصال بقاعدة البيانات: ' . print_r(sqlsrv_errors(), true)]);
        exit;
    }
    

    // جلب المدخلات من الطلب
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $rfid_tag = $_POST['rfid_tag'] ?? null; // تسجيل الدخول باستخدام بطاقة NFC

    // التحقق من المدخلات
    if (is_null($email) && is_null($password) && is_null($rfid_tag)) {
        throw new Exception("يجب إدخال البريد الإلكتروني وكلمة المرور أو بطاقة NFC.");
    }

    if (!is_null($rfid_tag)) {
        // تسجيل الدخول باستخدام بطاقة NFC
        $employee_sql = "SELECT * FROM employees WHERE rfid_tag = ?";
        $employee_params = [$rfid_tag];
        $employee_stmt = sqlsrv_query($conn, $employee_sql, $employee_params);

        if ($employee_stmt === false) {
            throw new Exception("خطأ في استعلام بيانات الموظف: " . print_r(sqlsrv_errors(), true));
        }

        $employee = sqlsrv_fetch_array($employee_stmt, SQLSRV_FETCH_ASSOC);

        if ($employee) {
            // تسجيل الدخول ناجح باستخدام بطاقة NFC
            $_SESSION['loggedin'] = true;
            $_SESSION['rfid_tag'] = $employee['rfid_tag'];
            $_SESSION['employee_name'] = $employee['name'];
            $_SESSION['role'] = $employee['role'];
            $_SESSION['employee_id'] = $employee['employee_id'];

            echo json_encode([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح باستخدام بطاقة NFC.',
                'rfid_tag' => $employee['rfid_tag'],
                'employee_name' => $employee['name'],
                'role' => $employee['role']
            ]);
        } else {
            // الموظف غير موجود
            echo json_encode(['success' => false, 'error' => "لم يتم العثور على موظف مرتبط ببطاقة NFC: $rfid_tag."]);
        }
    } else {
        // تسجيل الدخول باستخدام البريد الإلكتروني وكلمة المرور
        if (is_null($email) || is_null($password)) {
            throw new Exception("يجب إدخال البريد الإلكتروني وكلمة المرور.");
        }

        $tsql = "SELECT * FROM users WHERE email = ?";
        $params = [$email];
        $stmt = sqlsrv_query($conn, $tsql, $params);

        if ($stmt === false) {
            throw new Exception("خطأ في استعلام المستخدم: " . print_r(sqlsrv_errors(), true));
        }

        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            $rfid_tag = $user['rfid_tag'];

            $employee_sql = "SELECT * FROM employees WHERE rfid_tag = ?";
            $employee_params = [$rfid_tag];
            $employee_stmt = sqlsrv_query($conn, $employee_sql, $employee_params);

            if ($employee_stmt === false) {
                throw new Exception("خطأ في استعلام بيانات الموظف: " . print_r(sqlsrv_errors(), true));
            }

            $employee = sqlsrv_fetch_array($employee_stmt, SQLSRV_FETCH_ASSOC);

            if ($employee) {
                // تسجيل الدخول ناجح باستخدام البريد الإلكتروني وكلمة المرور
                $_SESSION['loggedin'] = true;
                $_SESSION['rfid_tag'] = $employee['rfid_tag'];
                $_SESSION['employee_name'] = $employee['name'];
                $_SESSION['role'] = $employee['role'];
                $_SESSION['employee_id'] = $employee['employee_id'];

                echo json_encode([
                    'success' => true,
                    'message' => 'تم تسجيل الدخول بنجاح باستخدام البريد الإلكتروني.',
                    'rfid_tag' => $employee['rfid_tag'],
                    'employee_name' => $employee['name'],
                    'role' => $employee['role']
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => "لم يتم العثور على موظف مرتبط ببطاقة NFC: $rfid_tag."]);
            }
        } else {
            $error_msg = $user ? 'كلمة المرور غير صحيحة.' : 'البريد الإلكتروني غير صحيح.';
            echo json_encode(['success' => false, 'error' => $error_msg]);
        }
    }
} catch (Exception $e) {
    // عرض الأخطاء
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => "خطأ: " . $e->getMessage()]);
} finally {
    // إغلاق الاتصال بقاعدة البيانات
    sqlsrv_close($conn);
}
?>
