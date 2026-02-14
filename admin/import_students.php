<?php
require_once 'header.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    verify_csrf_token(); // Protect
    $file = $_FILES['file']['tmp_name'];
    
    try {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        $count = 0;
        // Assume Row 1 is Header
        foreach ($rows as $key => $row) {
            if ($key == 0) continue; // Skip header
            
            // Excel Columns: A=NISN, B=Nama, C=Kelas, D=JK, E=Tempat, F=Tgl Lahir (YYYY-MM-DD), G=Status
            $nisn = $row[0];
            $name = $row[1];
            $class = $row[2];
            $gender = $row[3];
            $pob = $row[4];
            $dob = $row[5]; // Make sure Excel format is Text or Date usable
             // Handle Date object from Excel if necessary, but assuming text YYYY-MM-DD for simplicity first, or convert
            if (is_numeric($dob)) {
                 $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dob)->format('Y-m-d');
            }
            $status = strtoupper($row[6]);

            if (!empty($nisn)) {
                // Check if exist
                $stmt = $pdo->prepare("SELECT id FROM students WHERE nisn = ?");
                $stmt->execute([$nisn]);
                $exist = $stmt->fetch();

                if ($exist) {
                    $sql = "UPDATE students SET name=?, class=?, gender=?, pob=?, dob=?, status=? WHERE nisn=?";
                    $pdo->prepare($sql)->execute([$name, $class, $gender, $pob, $dob, $status, $nisn]);
                } else {
                    $sql = "INSERT INTO students (nisn, name, class, gender, pob, dob, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute([$nisn, $name, $class, $gender, $pob, $dob, $status]);
                }
                $count++;
            }
        }
        $message = '<div class="alert alert-success">Berhasil import '.$count.' data siswa!</div>';
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">Error: '.$e->getMessage().'</div>';
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Import Data Siswa via Excel</h1>
    <a href="students.php" class="btn btn-secondary">Kembali</a>
</div>

<?= $message ?>

<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Upload File Excel (.xlsx / .xls)</label>
                <?= csrf_field() ?>
                <input type="file" name="file" class="form-control" required accept=".xlsx, .xls">
                <div class="form-text">
                    Format Kolom Excel: <br>
                    <strong>A: NISN | B: Nama | C: Kelas | D: JK (L/P) | E: Tempat Lahir | F: Tgl Lahir (YYYY-MM-DD) | G: Status (LULUS/TIDAK LULUS)</strong>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Import Data</button>
            <a href="download_template_siswa.php" class="btn btn-outline-primary">Download Template (Manual)</a>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
