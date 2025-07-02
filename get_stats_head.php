<?php
header('Content-Type: application/json');
require_once 'database.php';

$head_id = $_GET['head_id'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

if (!$head_id) {
    echo json_encode(['labels' => [], 'counts' => []]);
    exit;
}

// جلب اسم القسم الخاص برئيس القسم
$sql_sec = "SELECT section FROM employees WHERE employee_id = ?";
$stmt_sec = sqlsrv_query($conn, $sql_sec, [$head_id]);
$row_sec = sqlsrv_fetch_array($stmt_sec, SQLSRV_FETCH_ASSOC);
$section = $row_sec ? $row_sec['section'] : '';

$where = ["r.section = ?", "r.status = (SELECT status_id FROM statuse WHERE status_name = 'completed')"];
$params = [$section];
if ($from) {
    $where[] = 'r.report_date >= ?';
    $params[] = $from;
}
if ($to) {
    $where[] = 'r.report_date <= ?';
    $params[] = $to;
}
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT e.name, COUNT(*) as count
        FROM reports r
        JOIN employees e ON r.assigned_to = e.employee_id
        $where_sql
        GROUP BY e.name
        ORDER BY count DESC";
$stmt = sqlsrv_query($conn, $sql, $params);
$labels = [];
$counts = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $labels[] = $row['name'];
    $counts[] = intval($row['count']);
}
echo json_encode(['labels' => $labels, 'counts' => $counts]); 