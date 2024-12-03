<?php
require_once('./db_connect.php');
require_once('./vendor/autoload.php'); // Make sure you have PhpSpreadsheet installed

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
}

// Get the exported data
$export_data = json_decode($_POST['export_data'], true);
$faculty_id = $_POST['faculty_id'];
$subject_id = $_POST['subject_id'];
$class_id = $_POST['class_id'];

// Get subject and faculty details
$query = "SELECT f.name as faculty_name, s.subject, s.code 
          FROM faculty_list f 
          JOIN subject_list s ON s.id = ? 
          WHERE f.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $subject_id, $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$details = $result->fetch_assoc();

// Create new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set headers and styling
$sheet->setCellValue('A1', 'Faculty Evaluation Report');
$sheet->mergeCells('A1:G1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

// Add details
$sheet->setCellValue('A3', 'Faculty Name:');
$sheet->setCellValue('B3', $details['faculty_name']);
$sheet->setCellValue('A4', 'Subject:');
$sheet->setCellValue('B4', $details['code'] . ' - ' . $details['subject']);
$sheet->setCellValue('A5', 'Academic Year:');
$sheet->setCellValue('B5', $export_data['academicYear']);
$sheet->setCellValue('A6', 'Total Evaluations:');
$sheet->setCellValue('B6', $export_data['totalEvaluations']);
$sheet->setCellValue('A7', 'Overall Rating:');
$sheet->setCellValue('B7', $export_data['overallRating'] . '/5');

// Add questions and ratings
$row = 9;
$sheet->setCellValue('A' . $row, 'Criteria');
$sheet->setCellValue('B' . $row, 'Question');
$sheet->setCellValue('C' . $row, 'Rating 1');
$sheet->setCellValue('D' . $row, 'Rating 2');
$sheet->setCellValue('E' . $row, 'Rating 3');
$sheet->setCellValue('F' . $row, 'Rating 4');
$sheet->setCellValue('G' . $row, 'Rating 5');

$sheet->getStyle('A' . $row . ':G' . $row)->getFont()->setBold(true);
$row++;

foreach ($export_data['questions'] as $question) {
    $sheet->setCellValue('A' . $row, $question['criteria']);
    $sheet->setCellValue('B' . $row, $question['question']);
    for ($i = 0; $i < 5; $i++) {
        $sheet->setCellValue(chr(67 + $i) . $row, $question['ratings'][$i] . '%');
    }
    $row++;
}

// Add comments
$row += 2;
$sheet->setCellValue('A' . $row, 'Comments');
$sheet->getStyle('A' . $row)->getFont()->setBold(true);
$row++;

foreach ($export_data['comments'] as $comment) {
    $sheet->setCellValue('A' . $row, $comment);
    $sheet->mergeCells('A' . $row . ':G' . $row);
    $row++;
}

// Auto-size columns
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Create the Excel file
$filename = 'Evaluation_Report_' . date('Y-m-d_His') . '.xlsx';
$writer = new Xlsx($spreadsheet);
$filepath = '../temp/' . $filename;

// Ensure temp directory exists
if (!file_exists('../temp')) {
    mkdir('../temp', 0777, true);
}

$writer->save($filepath);

// Return the file URL
echo json_encode([
    'status' => 'success',
    'file_url' => '../temp/' . $filename,
    'filename' => $filename
]);

// Schedule file deletion after 5 minutes
register_shutdown_function(function() use ($filepath) {
    sleep(300); // 5 minutes
    if (file_exists($filepath)) {
        unlink($filepath);
    }
});
