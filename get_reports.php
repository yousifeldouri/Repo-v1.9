<?php
session_start();
file_put_contents('debug.txt', "\n==== NEW REQUEST ====\n" . print_r(
    [
        'GET' => $_GET,
        'SESSION' => $_SESSION
    ], true
) . "\n", FILE_APPEND);
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
        throw new Exception("Connection failed: " . print_r(sqlsrv_errors(), true));
    }

    $status_name = isset($_GET['status_name']) ? $_GET['status_name'] : 'all';
    $rfid_tag = $_GET['rfid_tag'] ?? ($_SESSION['rfid_tag'] ?? '');
    $role = $_GET['role'] ?? ($_SESSION['role'] ?? '');
    $department = $_GET['department'] ?? ($_SESSION['department'] ?? '');
    $section = $_GET['section'] ?? ($_SESSION['section'] ?? '');

    if (empty($rfid_tag) || empty($role)) {
        throw new Exception("المستخدم غير معرف أو الجلسة منتهية.");
    }

    // بناء الاستعلام حسب الدور
    if ($role === 'admin' || $role === 'supervisor') {
        // كل البلاغات
        $sql = "SELECT report_id, r.section AS target_section, e.section AS author_section, description, iso_values, s.status_name, comments, report_date, recive_date
                FROM [attendance_db].[dbo].[reports] as r
                JOIN [attendance_db].[dbo].[statuse] as s ON r.status = s.status_id
                JOIN [attendance_db].[dbo].[employees] as e on r.[rfid_tag] = e.rfid_tag
                WHERE 1=1";
        $params = [];
        if ($status_name !== 'all') {
            $sql .= " AND s.status_name = ?";
            $params[] = $status_name;
        }
    } elseif ($role === 'manager') {
        // بلاغات الإدارة
        $sql = "SELECT report_id, r.section AS target_section, e.section AS author_section, description, iso_values, s.status_name, comments, report_date, recive_date
                FROM [attendance_db].[dbo].[reports] as r
                JOIN [attendance_db].[dbo].[statuse] as s ON r.status = s.status_id
                JOIN [attendance_db].[dbo].[employees] as e on r.[rfid_tag] = e.rfid_tag
                WHERE e.department = ?";
        $params = [$department];
        if ($status_name !== 'all') {
            $sql .= " AND s.status_name = ?";
            $params[] = $status_name;
        }
    } elseif ($role === 'head') {
        // بلاغات القسم
        $sql = "SELECT report_id, r.section AS target_section, e.section AS author_section, description, iso_values, s.status_name, comments, report_date, recive_date
                FROM [attendance_db].[dbo].[reports] as r
                JOIN [attendance_db].[dbo].[statuse] as s ON r.status = s.status_id
                JOIN [attendance_db].[dbo].[employees] as e on r.rfid_tag = e.rfid_tag
                WHERE e.section = ?";
        $params = [$section];
        if ($status_name !== 'all') {
            $sql .= " AND s.status_name = ?";
            $params[] = $status_name;
        }
    } else {
        // موظف: بلاغاته فقط
        $sql = "SELECT report_id,r.section,e1.section,e1.name , description, iso_values, s.status_name, comments, report_date, recive_date
                FROM [attendance_db].[dbo].[reports] as r
                JOIN [attendance_db].[dbo].[statuse] as s ON r.status = s.status_id
                JOIN [attendance_db].[dbo].[employees] as e on r.[section] = e.section
				left JOIN [attendance_db].[dbo].[employees] as e1 on r.[employee_id] = e1.[employee_id]
                WHERE e.rfid_tag = ?";
        $params = [$rfid_tag];
        if ($status_name !== 'all') {
            $sql .= " AND s.status_name = ?";
            $params[] = $status_name;
        }
    }

    $stmt = sqlsrv_query($conn, $sql, $params);
    $reports = [];
    if ($stmt !== false) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $row['iso_values'] = json_encode(json_decode($row['iso_values'], true), JSON_UNESCAPED_UNICODE);
             // جلب المشاركين
             $participants = [];
             $assign_stmt = sqlsrv_query($conn, "SELECT e.name FROM report_assignments ra JOIN employees e ON ra.employee_id = e.employee_id WHERE ra.report_id = ?", [$row['report_id']]);
             if ($assign_stmt) {
                 while ($assign_row = sqlsrv_fetch_array($assign_stmt, SQLSRV_FETCH_ASSOC)) {
                     $participants[] = $assign_row['name'];
                 }
             }
             $row['participants'] = $participants;
            // تعديل القسم ليكون قسم كاتب البلاغ
            if (isset($row['author_section'])) {
                $row['section'] = $row['author_section'];
            }
            $reports[] = $row;
        }
        echo json_encode($reports);
    } else {
        throw new Exception("Failed to fetch reports: " . print_r(sqlsrv_errors(), true));
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
} finally {
    sqlsrv_close($conn);
}
?>
