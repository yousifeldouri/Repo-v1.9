<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'database.php';

// جلب الدور والمجال من الجلسة
$role = $_SESSION['role'] ?? '';
$department = $_SESSION['department'] ?? '';
$section = $_SESSION['section'] ?? '';

// جلب حالات البلاغات
$statusCounts = [
    'جديد' => 0,
    'تحت الإجراء' => 0,
    'مكتمل' => 0,
    'مغلق' => 0
];

// فلترة الاستعلام حسب الدور
if ($role === 'manager' && !empty($department)) {
    $sql = "SELECT s.status_name, COUNT(*) as count FROM reports r JOIN statuse s ON r.status = s.status_id JOIN employees e ON r.rfid_tag = e.rfid_tag WHERE e.department = ? GROUP BY s.status_name";
    $params = [$department];
    $stmt = sqlsrv_query($conn, $sql, $params);
} elseif ($role === 'head' && !empty($section)) {
    $sql = "SELECT s.status_name, COUNT(*) as count FROM reports r JOIN statuse s ON r.status = s.status_id JOIN employees e ON r.rfid_tag = e.rfid_tag WHERE e.section = ? GROUP BY s.status_name";
    $params = [$section];
    $stmt = sqlsrv_query($conn, $sql, $params);
} else {
    // الأدمن أو أي دور آخر: جميع البلاغات
    $sql = "SELECT s.status_name, COUNT(*) as count FROM reports r JOIN statuse s ON r.status = s.status_id GROUP BY s.status_name";
    $stmt = sqlsrv_query($conn, $sql);
}

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $status = $row['status_name'];
    if (isset($statusCounts[$status])) {
        $statusCounts[$status] = (int)$row['count'];
    }
}

// جلب أداء الموظفين
$employeeStats = [];
if ($role === 'manager' && !empty($department)) {
    $sql2 = "SELECT e.name, COUNT(r.report_id) as total, 
                SUM(CASE WHEN s.status_name = 'مكتمل' THEN 1 ELSE 0 END) as completed,
                AVG(CASE WHEN r.report_date IS NOT NULL AND r.recive_date IS NOT NULL THEN DATEDIFF(HOUR, r.report_date, r.recive_date) END) as avg_time
             FROM employees e
             LEFT JOIN reports r ON r.rfid_tag = e.rfid_tag
             LEFT JOIN statuse s ON r.status = s.status_id
             WHERE e.department = ?
             GROUP BY e.name";
    $params2 = [$department];
    $stmt2 = sqlsrv_query($conn, $sql2, $params2);
} elseif ($role === 'head' && !empty($section)) {
    $sql2 = "SELECT e.name, COUNT(r.report_id) as total, 
                SUM(CASE WHEN s.status_name = 'مكتمل' THEN 1 ELSE 0 END) as completed,
                AVG(CASE WHEN r.report_date IS NOT NULL AND r.recive_date IS NOT NULL THEN DATEDIFF(HOUR, r.report_date, r.recive_date) END) as avg_time
             FROM employees e
             LEFT JOIN reports r ON r.rfid_tag = e.rfid_tag
             LEFT JOIN statuse s ON r.status = s.status_id
             WHERE e.section = ?
             GROUP BY e.name";
    $params2 = [$section];
    $stmt2 = sqlsrv_query($conn, $sql2, $params2);
} else {
    $sql2 = "SELECT e.name, COUNT(r.report_id) as total, 
                SUM(CASE WHEN s.status_name = 'مكتمل' THEN 1 ELSE 0 END) as completed,
                AVG(CASE WHEN r.report_date IS NOT NULL AND r.recive_date IS NOT NULL THEN DATEDIFF(HOUR, r.report_date, r.recive_date) END) as avg_time
             FROM employees e
             LEFT JOIN reports r ON r.rfid_tag = e.rfid_tag
             LEFT JOIN statuse s ON r.status = s.status_id
             GROUP BY e.name";
    $stmt2 = sqlsrv_query($conn, $sql2);
}
while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    $employeeStats[] = [
        'name' => $row['name'],
        'total' => (int)$row['total'],
        'completed' => (int)$row['completed'],
        'avg_time' => is_null($row['avg_time']) ? '-' : round((float)$row['avg_time'], 1)
    ];
}

echo json_encode([
    'statusCounts' => $statusCounts,
    'employeeStats' => $employeeStats
]); 