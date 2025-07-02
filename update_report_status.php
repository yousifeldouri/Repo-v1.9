<?php
header('Content-Type: application/json');
require_once 'database.php';
$data = json_decode(file_get_contents('php://input'), true);
$report_id = intval($data['report_id'] ?? 0);
$action = $data['action'] ?? '';

if (!$report_id || !$action) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير مكتملة']);
    exit;
}

if ($action === 'confirm') {
    // الحالة: في انتظار تأكيد صاحب البلاغ
    $sql = "UPDATE reports SET status = (SELECT status_id FROM statuse WHERE status_name = 'in_wating'), owner_confirmation = 0 WHERE report_id = ?";
    $msg = 'تم تحويل البلاغ إلى انتظار تأكيد صاحب البلاغ.';
} elseif ($action === 'force_complete') {
    // إكمال البلاغ مباشرة
    $sql = "UPDATE reports SET status = (SELECT status_id FROM statuse WHERE status_name = 'completed'), owner_confirmation = 1 WHERE report_id = ?";
    $msg = 'تم إكمال البلاغ مباشرة.';
} elseif ($action === 'owner_confirm') {
    // تأكيد صاحب البلاغ
    $sql = "UPDATE reports SET status = (SELECT status_id FROM statuse WHERE status_name = 'completed'), owner_confirmation = 1 WHERE report_id = ?";
    $msg = 'تم تأكيد حل البلاغ بنجاح.';
} else {
    echo json_encode(['success' => false, 'message' => 'إجراء غير معروف']);
    exit;
}

$stmt = sqlsrv_query($conn, $sql, [$report_id]);
if ($stmt) {
    echo json_encode(['success' => true, 'message' => $msg]);
} else {
    echo json_encode(['success' => false, 'message' => 'فشل في تحديث البلاغ']);
} 
