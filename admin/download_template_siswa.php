<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$headers = ['NISN', 'NAMA LENGKAP', 'KELAS', 'JENIS KELAMIN (L/P)', 'TEMPAT LAHIR', 'TANGGAL LAHIR (YYYY-MM-DD)', 'STATUS (LULUS/TIDAK LULUS)'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

// Sample Data (Optional)
$sheet->setCellValue('A2', '0012345678');
$sheet->setCellValue('B2', 'Siswa Contoh');
$sheet->setCellValue('C2', 'XII IPA 1');
$sheet->setCellValue('D2', 'L');
$sheet->setCellValue('E2', 'Jakarta');
$sheet->setCellValue('F2', '2006-05-20');
$sheet->setCellValue('G2', 'LULUS');

$writer = new Xlsx($spreadsheet);
$filename = 'template_siswa.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
$writer->save('php://output');
exit;
