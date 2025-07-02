<?php
$serverName = "192.168.3.88\\iisbash";
$uid = "arduino";
$pass = "Root@456123";
$database = "attendance_db"; // اسم قاعدة البيانات

$connectionOptions = [
  "Database" => $database,
  "Uid" => $uid,
  "PWD" => $pass,
  "CharacterSet" => "UTF-8"
];

// إنشاء الاتصال
$conn = sqlsrv_connect($serverName, $connectionOptions);

// التحقق من الاتصال
if (!$conn) {
  die(print_r(sqlsrv_errors(), true));
}
?>
