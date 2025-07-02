<?php
header('Content-Type: application/json');

$serverName = "192.168.3.88\\iisbash";
$uid = "arduino";
$pass = "Root@456123";
$database = "attendance_db";
$connectionOptions = [
    "Database" => $database,
    "Uid" => $uid,
    "PWD" => $pass,
    "CharacterSet" => "UTF-8"
];

try {
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if (!$conn) {
        throw new Exception(print_r(sqlsrv_errors(), true));
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['name'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'];

        $tsql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
        $params = [$username, $password, $email];
        $stmt = sqlsrv_prepare($conn, $tsql, $params);  

        if (sqlsrv_execute($stmt)) {
            echo "تم إنشاء الحساب بنجاح!";
            header('Location: index_login.html');
        } else {
            echo "خطأ: " . print_r(sqlsrv_errors(), true);
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
} finally {
    sqlsrv_close($conn);
}
?>
