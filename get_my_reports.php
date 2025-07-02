<?php
header('Content-Type: application/json');
require_once 'database.php';

$rfid_tag = $_GET['rfid_tag'] ?? '';
$status = $_GET['status'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = ['r.rfid_tag = ?'];
$params = [$rfid_tag];

if ($status) {
    $where[] = 's.status_name = ?';
    $params[] = $status;
}
if ($from) {
    $where[] = 'r.report_date >= ?';
    $params[] = $from;
}
if ($to) {
    $where[] = 'r.report_date <= ?';
    $params[] = $to;
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT r.report_id, r.rfid_tag, r.department, r.section, r.description, r.status, s.status_name, r.comments, r.report_date, r.assigned_to, r.confirmation_code,
    (SELECT name FROM employees WHERE employee_id = r.assigned_to) AS assigned_to_name
FROM reports r
LEFT JOIN statuse s ON r.status = s.status_id
$where_sql
ORDER BY r.report_date DESC";

$stmt = sqlsrv_query($conn, $sql, $params);
$reports = [];
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
echo json_encode([
    'debug_rfid_tag' => $rfid_tag,
    'count' => count($reports),
    'reports' => $reports
]); 