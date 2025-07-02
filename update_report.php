<?php
session_start();
header('Content-Type: application/json');

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

$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'فشل الاتصال بقاعدة البيانات.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$report_id = $input['report_id'] ?? $_POST['report_id'] ?? null;
$employee_id = $input['employee_id'] ?? $_POST['employee_id'] ?? null;
$status_name = $input['status_name'] ?? $_POST['status_name'] ?? null;
$comments = $input['comments'] ?? $_POST['comments'] ?? null;
$confirmation_code = $input['confirmation_code'] ?? $_POST['confirmation_code'] ?? null;

// 1. عند تعيين موظف جديد للبلاغ: توليد رمز تحقق وتخزينه
if ($employee_id && $report_id && !$status_name) {
    $otp = mt_rand(100000, 999999);
    $sql = "UPDATE reports SET assigned_to = ?, confirmation_code = ? WHERE report_id = ?";
    $stmt = sqlsrv_query($conn, $sql, [$employee_id, $otp, $report_id]);
    if ($stmt) {
        echo json_encode(['success' => true, 'message' => 'تم تعيين الموظف وتوليد رمز التحقق.', 'confirmation_code' => $otp]);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في تعيين الموظف.']);
    }
    sqlsrv_close($conn);
    exit;
}

// 2. عند إنهاء العمل وإدخال رمز التحقق
if ($report_id && $confirmation_code && $status_name === 'in_wating') {
    // تحقق من الرمز
    $check_sql = "SELECT confirmation_code FROM reports WHERE report_id = ?";
    $check_stmt = sqlsrv_query($conn, $check_sql, [$report_id]);
    $row = sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC);
    if ($row && trim($row['confirmation_code']) == trim($confirmation_code)) {
        // الرمز صحيح: حدث الحالة
        $sql = "UPDATE reports SET status = 3, comments = ? WHERE report_id = ?";
        $stmt = sqlsrv_query($conn, $sql, [$comments, $report_id]);
        if ($stmt) {
            echo json_encode(['success' => true, 'message' => 'تم تحويل البلاغ إلى انتظار التأكيد من صاحب البلاغ.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'فشل في تحديث البلاغ.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'رمز التحقق غير صحيح.']);
    }
    sqlsrv_close($conn);
    exit;
}

// 3. تحديث الحالة والتعليق (تأكيد صاحب البلاغ أو الأدمن)
if ($report_id && $status_name) {
    $status_id = null;
    switch ($status_name) {
        case 'completed': $status_id = 4; break;
        case 'closed': $status_id = 5; break;
        case 'in_progress': $status_id = 2; break;
        case 'new': $status_id = 1; break;
        case 'in_wating': $status_id = 3; break;
    }
    if ($status_id) {
        $sql = "UPDATE reports SET status = ?, comments = ? WHERE report_id = ?";
        $stmt = sqlsrv_query($conn, $sql, [$status_id, $comments, $report_id]);
        if ($stmt) {
            echo json_encode(['success' => true, 'message' => 'تم تحديث حالة البلاغ.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'فشل في تحديث البلاغ.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'حالة غير معروفة.']);
    }
    sqlsrv_close($conn);
    exit;
}

echo json_encode(['success' => false, 'message' => 'طلب غير صالح.']);
sqlsrv_close($conn);
