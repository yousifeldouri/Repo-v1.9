<?php
header('Content-Type: application/json');

// إعداد الاتصال بقاعدة البيانات
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
    // بدء الإخراج المؤقت لتجنب أي نص غير متوقع في الاستجابة
    ob_start();
    
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if (!$conn) {
        error_log("Connection error: " . print_r(sqlsrv_errors(), true));
        echo json_encode(["error" => "Database connection error"]);
        exit;
    }

    // استقبال البيانات من POST أو JSON
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $rfidTag = $_POST['rfid_tag'] ?? '';
        $reportType = $_POST['reportType'] ?? '';
        $description = $_POST['description'] ?? '';
        $section = $_POST['section'] ?? '';
        $department = $_POST['department'] ?? '';
        $isoValues = isset($_POST['isoValues']) ? json_decode($_POST['isoValues'], true) : [];
        // معالجة الملفات المرفقة
        $uploadedFiles = [];
        if (!empty($_FILES['attachments']['name'][0])) {
            $uploadDir = __DIR__ . '/uploads/reports/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            foreach ($_FILES['attachments']['name'] as $i => $name) {
                $tmpName = $_FILES['attachments']['tmp_name'][$i];
                $error = $_FILES['attachments']['error'][$i];
                $size = $_FILES['attachments']['size'][$i];
                if ($error === UPLOAD_ERR_OK && $size > 0) {
                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $safeName = uniqid('report_', true) . '.' . strtolower($ext);
                    $dest = $uploadDir . $safeName;
                    if (move_uploaded_file($tmpName, $dest)) {
                        $uploadedFiles[] = $safeName;
                    }
                }
            }
        }
        if (!empty($uploadedFiles)) {
            $isoValues['attachments'] = $uploadedFiles;
        }
    } else {
        // دعم الاستقبال القديم (JSON)
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            error_log("Invalid input data: " . file_get_contents('php://input'));
            echo json_encode(["error" => "Invalid input"]);
            exit;
        }
        $rfidTag = $data['rfid_tag'];
        $reportType = $data['reportType'];
        $description = $data['description'];
        $section = $data['section'];
        $department = $data['department'];
        $isoValues = $data['isoValues'];
    }

    // الحصول على التاريخ والوقت باستخدام PHP
    $reportDate = date('Y-m-d H:i:s'); // تنسيق التاريخ والوقت

    // التحقق من صحة البيانات قبل إدخالها في قاعدة البيانات
    if (empty($rfidTag) || empty($reportType) || empty($description) || empty($section) || empty($department)) {
        error_log("Missing required fields: " . print_r($data, true));
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    // إدخال البيانات في قاعدة البيانات
    $tsql = "INSERT INTO reports (employee_id, rfid_tag, report_date, description, report_type, status, section, department, iso_values) 
             VALUES ((SELECT employee_id FROM employees WHERE rfid_tag = ?), ?, ?, ?, ?, '1', ?, ?, ?)";
    $params = [$rfidTag, $rfidTag, $reportDate, $description, $reportType, $section, $department, json_encode($isoValues)]; // تخزين الفورمات كـ JSON
    $stmt = sqlsrv_query($conn, $tsql, $params);

    if ($stmt) {
        // تنظيف الإخراج المؤقت
        ob_end_clean();
        echo json_encode(["message" => "تم إرسال البلاغ بنجاح"]);
    } else {
        error_log("Query error: " . print_r(sqlsrv_errors(), true));
        ob_end_clean();
        echo json_encode(["error" => "فشل في إرسال البلاغ"]);
    }
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage()); // سجل الأخطاء العامة
    ob_end_clean();
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        sqlsrv_free_stmt($stmt);
    }
    sqlsrv_close($conn);
}
?>
