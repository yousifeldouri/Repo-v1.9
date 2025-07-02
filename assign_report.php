<?php
require_once 'database.php';
header('Content-Type: application/json; charset=utf-8');
$data = json_decode(file_get_contents('php://input'), true);
$report_id = $data['report_id'] ?? null;
$employee_id = $data['employee_id'] ?? null;

if (!$report_id || !$employee_id) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير مكتملة']);
    exit;
}

$sql = "UPDATE reports SET assigned_to = ? WHERE report_id = ?";
$stmt = sqlsrv_query($conn, $sql, [$employee_id, $report_id]);

if ($stmt) {
    echo json_encode(['success' => true, 'message' => 'تم تسليم البلاغ!']);
} else {
    echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء التسليم']);
} 