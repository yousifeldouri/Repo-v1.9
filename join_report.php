<?php
session_start();
file_put_contents('debug.txt', "\n==== JOIN_REPORT SESSION ====\n" . print_r($_SESSION, true) . "\n", FILE_APPEND);
header('Content-Type: application/json');

// التحقق من تسجيل الدخول
if (!isset($_SESSION['rfid_tag']) || !isset($_SESSION['employee_id'])) {
    echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول أولاً.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$report_id = $input['report_id'] ?? null;
$employee_id = $_SESSION['employee_id'];

if (!$report_id || !$employee_id) {
    echo json_encode(['success' => false, 'message' => 'بيانات ناقصة.']);
    exit;
}

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

// تحقق إذا كان الموظف مشاركًا بالفعل
$check_sql = "SELECT 1 FROM report_assignments WHERE report_id = ? AND employee_id = ?";
$check_stmt = sqlsrv_query($conn, $check_sql, [$report_id, $employee_id]);
if ($check_stmt && sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
    echo json_encode(['success' => false, 'message' => 'أنت بالفعل مشارك في هذا البلاغ.']);
    sqlsrv_close($conn);
    exit;
}

// أضف المشاركة
$insert_sql = "INSERT INTO report_assignments (report_id, employee_id) VALUES (?, ?)";
$insert_stmt = sqlsrv_query($conn, $insert_sql, [$report_id, $employee_id]);
if ($insert_stmt) {
    // توليد رقم تحقق إذا لم يكن موجودًا
    $otp_sql = "SELECT confirmation_code FROM reports WHERE report_id = ?";
    $otp_stmt = sqlsrv_query($conn, $otp_sql, [$report_id]);
    $otp_row = sqlsrv_fetch_array($otp_stmt, SQLSRV_FETCH_ASSOC);
    if (!$otp_row || empty($otp_row['confirmation_code'])) {
        $otp = mt_rand(100000, 999999);
        $update_otp_sql = "UPDATE reports SET confirmation_code = ? WHERE report_id = ?";
        sqlsrv_query($conn, $update_otp_sql, [$otp, $report_id]);
    }
    echo json_encode(['success' => true, 'message' => 'تمت إضافتك كمشارك في البلاغ.']);
} else {
    echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء إضافة المشاركة.']);
}
sqlsrv_close($conn); 