<?php
session_start();
header('Content-Type: application/json');

// جلب rfid_tag من الجلسة
if (isset($_SESSION['rfid_tag'])) {
    echo json_encode(['success' => true, 'rfid_tag' => $_SESSION['rfid_tag']]);
    exit;
}
// جلب rfid_tag من sessionStorage إذا أرسلت عبر POST (اختياري، إذا أرسلت من JS)
$input = json_decode(file_get_contents('php://input'), true);
if (isset($input['rfid_tag'])) {
    echo json_encode(['success' => true, 'rfid_tag' => $input['rfid_tag']]);
    exit;
}
echo json_encode(['success' => false, 'error' => 'الجلسة غير موجودة.']);
?>
