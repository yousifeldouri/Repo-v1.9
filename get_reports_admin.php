<?php
header('Content-Type: application/json');
require_once 'database.php';

$where = [];
$params = [];

if (!empty($_GET['department_id'])) {
    $where[] = 'r.department = (SELECT department_name FROM departments WHERE department_id = ?)';
    $params[] = intval($_GET['department_id']);
}
if (!empty($_GET['section_id'])) {
    $where[] = 'r.section = (SELECT section_name FROM sections WHERE section_id = ?)';
    $params[] = intval($_GET['section_id']);
}
if (!empty($_GET['employee_id'])) {
    $where[] = 'r.assigned_to = ?';
    $params[] = intval($_GET['employee_id']);
}
if (!empty($_GET['status'])) {
    $where[] = 's.status_name = ?';
    $params[] = $_GET['status'];
}

$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT r.report_id, r.department, r.section, r.description, r.status, s.status_name, r.comments, r.report_date, r.assigned_to, r.rfid_tag,
    (SELECT department_name FROM departments WHERE department_name = r.department) AS department_name,
    (SELECT section_name FROM sections WHERE section_name = r.section) AS section_name,
    (SELECT name FROM employees WHERE employee_id = r.assigned_to) AS assigned_to_name
FROM reports r
LEFT JOIN statuse s ON r.status = s.status_id
$where_sql
ORDER BY r.report_date DESC";

$stmt = sqlsrv_query($conn, $sql, $params);
$reports = [];
$stats = [
    'new' => 0,
    'in_progress' => 0,
    'in_wating' => 0,
    'completed' => 0,
    'closed' => 0
];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $reports[] = $row;
    if (isset($row['status_name'])) {
        $stats[$row['status_name']] = isset($stats[$row['status_name']]) ? $stats[$row['status_name']] + 1 : 1;
    }
}
echo json_encode(['reports' => $reports, 'stats' => $stats]); 