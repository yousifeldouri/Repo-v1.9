<?php
header('Content-Type: application/json');
require_once 'database.php';
$section_id = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;
$sql = "SELECT e.employee_id, e.name FROM employees e JOIN sections s ON e.section = s.section_name WHERE s.section_id = ?";
$stmt = sqlsrv_query($conn, $sql, [$section_id]);
$employees = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $employees[] = $row;
}
echo json_encode($employees); 