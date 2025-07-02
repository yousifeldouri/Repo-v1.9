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
    echo json_encode(['error' => 'فشل الاتصال بقاعدة البيانات.']);
    exit;
}

$rfid_tag = $_GET['rfid_tag'] ?? ($_SESSION['rfid_tag'] ?? '');
if (!$rfid_tag) {
    echo json_encode([]);
    sqlsrv_close($conn);
    exit;
}

// جلب employee_id من rfid_tag
$emp_sql = "SELECT employee_id FROM employees WHERE rfid_tag = ?";
$emp_stmt = sqlsrv_query($conn, $emp_sql, [$rfid_tag]);
$emp = sqlsrv_fetch_array($emp_stmt, SQLSRV_FETCH_ASSOC);
if (!$emp) {
    echo json_encode([]);
    sqlsrv_close($conn);
    exit;
}
$employee_id = $emp['employee_id'];

// جلب البلاغات التي يشارك فيها الموظف
$sql = "SELECT r.*, s.status_name
        FROM report_assignments ra
        JOIN reports r ON ra.report_id = r.report_id
        JOIN statuse s ON r.status = s.status_id
        WHERE ra.employee_id = ?
        ORDER BY r.report_date DESC";
$stmt = sqlsrv_query($conn, $sql, [$employee_id]);
$reports = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // جلب المشاركين
        $participants = [];
        $assign_stmt = sqlsrv_query($conn, "SELECT e.name FROM report_assignments ra JOIN employees e ON ra.employee_id = e.employee_id WHERE ra.report_id = ?", [$row['report_id']]);
        if ($assign_stmt) {
            while ($assign_row = sqlsrv_fetch_array($assign_stmt, SQLSRV_FETCH_ASSOC)) {
                $participants[] = $assign_row['name'];
            }
        }
        $row['participants'] = $participants;
        $reports[] = $row;
    }
}
echo json_encode($reports);
sqlsrv_close($conn); 