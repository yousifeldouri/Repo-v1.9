<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'database.php';

$sql = "SELECT department_id,  department_name FROM departments";
$stmt = sqlsrv_query($conn, $sql);
$departments = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $departments[] = $row;
}
echo json_encode($departments); 