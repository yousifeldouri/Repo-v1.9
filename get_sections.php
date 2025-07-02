<?php
header('Content-Type: application/json');
require_once 'database.php';
$department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;
$sql = "SELECT section_id, section_name FROM sections WHERE department_id = ?";
$stmt = sqlsrv_query($conn, $sql, [$department_id]);
$sections = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $sections[] = $row;
}
echo json_encode($sections); 