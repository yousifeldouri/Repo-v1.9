<?php
require 'database.php'; // الاتصال بقاعدة البيانات

$section = $_GET['section'] ?? 'all';

$sql = "SELECT * FROM reports";
$params = [];
if ($section !== 'all') {
  $sql .= " WHERE statuse = ?";
  $params[] = $section;
}

$stmt = sqlsrv_query($conn, $sql, $params);

$reports = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $reports[] = $row;
}

echo json_encode($reports);
?>
