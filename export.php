<?php
require 'vendor/autoload.php';
require 'database.php'; // استيراد ملف الاتصال بقاعدة البيانات
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TCPDF\TCPDF;

session_start();
$rfid_tag = $_SESSION['rfid_tag'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // التحقق من وجود RFID_TAG
    if (!$rfid_tag) {
        die("RFID Tag not found. Please log in again.");
    }

    if (isset($_POST['export_excel'])) {
        // جلب البيانات من قاعدة البيانات بناءً على RFID
        $sql = "SELECT * FROM reports WHERE rfid_tag = ?";
        $stmt = sqlsrv_query($conn, $sql, [$rfid_tag]);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // إنشاء ملف Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Report ID');
        $sheet->setCellValue('B1', 'RFID Tag');
        $sheet->setCellValue('C1', 'Report Type');
        $sheet->setCellValue('D1', 'Department');
        $sheet->setCellValue('E1', 'Section');
        $sheet->setCellValue('F1', 'Description');
        $sheet->setCellValue('G1', 'Statuse');
        $sheet->setCellValue('H1', 'Comments');

        $row = 2;
        while ($report = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $sheet->setCellValue('A' . $row, $report['report_id']);
            $sheet->setCellValue('B' . $row, $report['rfid_tag']);
            $sheet->setCellValue('C' . $row, $report['report_type']);
            $sheet->setCellValue('D' . $row, $report['department']);
            $sheet->setCellValue('E' . $row, $report['section']);
            $sheet->setCellValue('F' . $row, $report['description']);
            $sheet->setCellValue('G' . $row, $report['statuse']);
            $sheet->setCellValue('H' . $row, $report['comments']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="reports.xlsx"');
        $writer->save('php://output'); // إلغاء كتابة الملف على القرص
        exit;
    }

    if (isset($_POST['export_pdf'])) {
        // جلب البيانات من قاعدة البيانات
        $sql = "SELECT * FROM reports WHERE rfid_tag = ?";
        $stmt = sqlsrv_query($conn, $sql, [$rfid_tag]);

        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // إنشاء ملف PDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        $html = '<h1>Reports</h1><table border="1"><tr><th>Report ID</th><th>RFID Tag</th><th>Report Type</th><th>Department</th><th>Section</th><th>Description</th><th>Statuse</th><th>Comments</th></tr>';
        
        while ($report = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $html .= '<tr><td>' . htmlspecialchars($report['report_id']) . '</td><td>' . htmlspecialchars($report['rfid_tag']) . '</td><td>' . htmlspecialchars($report['report_type']) . '</td><td>' . htmlspecialchars($report['department']) . '</td><td>' . htmlspecialchars($report['section']) . '</td><td>' . htmlspecialchars($report['description']) . '</td><td>' . htmlspecialchars($report['statuse']) . '</td><td>' . htmlspecialchars($report['comments']) . '</td></tr>';
        }
        $html .= '</table>';
        
        $pdf->writeHTML($html);
        $pdf->Output('reports.pdf', 'D'); // تنزيل ملف PDF
        exit;
    }
}

sqlsrv_close($conn);
?>
