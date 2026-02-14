<?php
require_once 'config/database.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$nisn = $_GET['nisn'] ?? '';

if (!$nisn) die("NISN tidak ditemukan.");

// Fetch Data
$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM students WHERE nisn = ?");
$stmt->execute([$nisn]);
$student = $stmt->fetch();

if (!$student) die("Siswa tidak ditemukan.");

// Fetch Grades
$stmt = $pdo->prepare("
    SELECT g.score, s.name, s.type 
    FROM grades g 
    JOIN subjects s ON g.subject_id = s.id 
    WHERE g.student_id = ? 
    ORDER BY s.type, s.id
");
$stmt->execute([$student['id']]);
$grades = $stmt->fetchAll();

// Group Grades
$groupedGrades = ['General' => [], 'Elective' => [], 'Local' => []];
$totalScore = 0;
$countScore = 0;

foreach ($grades as $g) {
    $groupedGrades[$g['type']][] = $g;
    $totalScore += $g['score'];
    $countScore++;
}

$average = $countScore > 0 ? number_format($totalScore / $countScore, 2, ',', '.') : '0,00';

// Format Date
$gradDate = date('d F Y', strtotime($settings['graduation_date']));
$dob = date('d F Y', strtotime($student['dob']));

// Mapping Bulan Indo
$monthMap = [
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'JULI', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
];
$gradDate = strtr($gradDate, $monthMap);
$dob = strtr($dob, $monthMap);

// Base64 Logo
$logoPath = 'uploads/' . $settings['logo'];
$logoData = '';
if (file_exists($logoPath)) {
    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    $data = file_get_contents($logoPath);
    $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
}

// HTML Layout
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; line-height: 1.1; }
        .header { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; position: relative; }
        .logo { position: absolute; left: 0; top: 0; width: 80px; }
        .school-name { font-size: 12pt; font-weight: bold; }
        .header-text { font-size: 12pt; }
        .title { text-align: center; font-weight: bold; margin-bottom: 5px; font-size: 14pt; }
        .subtitle { text-align: center; margin-bottom: 20px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px; vertical-align: top; }
        .grade-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .grade-table th, .grade-table td { border: 1px solid black; padding: 5px; }
        .grade-table th { text-align: center; font-weight: bold; }
        .footer { width: 100%; margin-top: 50px; }
        .signature { float: right; width: 250px; text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        '. ($logoData ? '<img src="'.$logoData.'" class="logo">' : '') .'
        <div class="header-text">PEMERINTAH PROVINSI DAERAH KHUSUS IBUKOTA JAKARTA</div>
        <div class="header-text">DINAS PENDIDIKAN</div>
        <div class="school-name">'.strtoupper($settings['school_name']).'</div>
        <div class="header-text">'.nl2br($settings['address']).'</div>
        <div class="header-text">Website: '.$settings['website'].'</div>
    </div>

    <div class="title">TRANSKIP NILAI</div>
    <div class="subtitle">No. Surat : '.$settings['letter_number'].'</div>

    <table class="info-table">
        <tr><td width="30%">Nama Lengkap</td><td>: '.strtoupper($student['name']).'</td></tr>
        <tr><td>Tempat, Tanggal Lahir</td><td>: '.strtoupper($student['pob']).', '.$dob.'</td></tr>
        <tr><td>Nomor Induk Siswa Nasional</td><td>: '.$student['nisn'].'</td></tr>
        <tr><td>Satuan Pendidikan</td><td>: '.$settings['school_name'].'</td></tr>
        <tr><td>Nomor Pokok Sekolah Nasional</td><td>: '.$settings['npsn'].'</td></tr>
        <tr><td>Tanggal Kelulusan</td><td>: '.$gradDate.'</td></tr>
    </table>

    <div style="margin-bottom: 10px;">Dengan transkrip nilai sebagai berikut</div>

    <table class="grade-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th>MATA PELAJARAN</th>
                <th width="15%">NILAI</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="3" style="font-weight:bold; background-color: #f0f0f0;">A. Kelompok Mata Pelajaran Umum</td></tr>';
            
            $no = 1;
            foreach ($groupedGrades['General'] as $g) {
                $html .= '<tr><td align="center">'.$no++.'</td><td>'.$g['name'].'</td><td align="center">'.number_format($g['score'], 2, ',', '.').'</td></tr>';
            }

            $html .= '<tr><td colspan="3" style="font-weight:bold; background-color: #f0f0f0;">B. Kelompok Mata Pelajaran Pilihan</td></tr>';
            
            $no = 1;
            foreach ($groupedGrades['Elective'] as $g) {
                $html .= '<tr><td align="center">'.$no++.'</td><td>'.$g['name'].'</td><td align="center">'.number_format($g['score'], 2, ',', '.').'</td></tr>';
            }

            $html .= '<tr><td colspan="3" style="font-weight:bold; background-color: #f0f0f0;">C. Muatan Lokal</td></tr>';
            
            $no = 1;
             if (count($groupedGrades['Local']) > 0) {
                 foreach ($groupedGrades['Local'] as $g) {
                     $html .= '<tr><td align="center">'.$no++.'</td><td>'.$g['name'].'</td><td align="center">'.number_format($g['score'], 2, ',', '.').'</td></tr>';
                 }
             } else {
                 $html .= '<tr><td align="center">-</td><td>-</td><td align="center">-</td></tr>';
             }

$html .= '
            <tr>
                <td colspan="2" align="center" style="font-weight: bold;">RATA-RATA NILAI</td>
                <td align="center" style="font-weight: bold;">'.$average.'</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            Jakarta, '.$gradDate.'<br>
            Kepala Sekolah,<br>
            <br><br><br><br>
            <strong>'.$settings['headmaster_name'].'</strong><br>
            NIP. '.$settings['headmaster_nip'].'
        </div>
    </div>
</body>
</html>';

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$paperSize = $_GET['size'] ?? 'A4';
if (!in_array($paperSize, ['A4', 'Legal'])) {
    $paperSize = 'A4';
}
$dompdf->setPaper($paperSize, 'portrait');
$dompdf->render();
$dompdf->stream("SKL_".$student['nisn'].".pdf", ["Attachment" => false]);
