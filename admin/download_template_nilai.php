<?php
require_once '../config/database.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$subject_id = $_GET['subject_id'] ?? null;
$class_name = $_GET['class_name'] ?? '';

if (!$subject_id) {
    die("Error: Subject ID required.");
}

$stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->execute([$subject_id]);
$subject = $stmt->fetch();

if (!$subject) {
    die("Error: Subject not found.");
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set Header Title
$title = 'MATA PELAJARAN: ' . strtoupper($subject['name']);
if (!empty($class_name) && $class_name !== 'Semua') {
    $title .= ' - KELAS: ' . strtoupper($class_name);
}
$sheet->setCellValue('A1', $title);
$sheet->mergeCells('A1:J1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Table Headers
$headers = [
    'NO', 'NISN', 'NAMA SISWA', 
    'SEM 1', 'SEM 2', 'SEM 3', 'SEM 4', 'SEM 5', 'SEM 6', 
    'UJIAN SEKOLAH'
];

$row = 3;
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . $row, $h);
    $sheet->getStyle($col . $row)->getFont()->setBold(true);
    $sheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('YYE0E0E0');
    $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $col++;
}

// Pre-fill Students
if (!empty($class_name) && $class_name !== 'Semua') {
    $stmt = $pdo->prepare("SELECT nisn, name FROM students WHERE class = ? ORDER BY name");
    $stmt->execute([$class_name]);
    $students = $stmt->fetchAll();
} else {
    $students = $pdo->query("SELECT nisn, name FROM students ORDER BY class, name")->fetchAll();
}

$row = 4;
$no = 1;
foreach ($students as $student) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $student['nisn']);
    $sheet->setCellValue('C' . $row, $student['name']);
    
    // Set Center Alignment for scores
    $sheet->getStyle('D'.$row.':J'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $row++;
}

// Auto-size columns
foreach (range('A', 'J') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);
$cleanName = preg_replace('/[^A-Za-z0-9]/', '_', $subject['name']);
if (!empty($class_name) && $class_name !== 'Semua') {
    $cleanName .= '_' . preg_replace('/[^A-Za-z0-9]/', '_', $class_name);
}
$filename = 'Template_Nilai_' . $cleanName . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
$writer->save('php://output');
exit;
