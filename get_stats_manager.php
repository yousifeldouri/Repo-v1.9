<?php
header('Content-Type: application/json');
require_once 'database.php';

$manager_id = $_GET['manager_id'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

if (!$manager_id) {
    echo json_encode(['labels' => [], 'counts' => []]);
    exit;
}

// جلب اسم الإدارة الخاصة بالمدير
$sql_dep = "SELECT department FROM employees WHERE employee_id = ?";
$stmt_dep = sqlsrv_query($conn, $sql_dep, [$manager_id]);
$row_dep = sqlsrv_fetch_array($stmt_dep, SQLSRV_FETCH_ASSOC);
$department = $row_dep ? $row_dep['department'] : '';

$where = ["r.department = ?", "r.status = (SELECT status_id FROM statuse WHERE status_name = 'completed')"];
$params = [$department];
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