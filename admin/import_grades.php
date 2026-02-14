<?php
require_once 'header.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$message = '';
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY type, name")->fetchAll();
$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    verify_csrf_token();
    
    $file = $_FILES['file']['tmp_name'];
    $subject_id = $_POST['subject_id'];
    
    if (empty($subject_id)) {
        $message = '<div class="alert alert-danger">Error: Pilih Mata Pelajaran!</div>';
    } else {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            $count = 0;
            // Row 1: Title, Row 2: Empty/Merged?, Row 3: Headers. Data starts Row 4.
            // Check headers in Row 3 to be sure, but assuming template structure:
            // Col A: No, B: NISN, C: Name, D: S1, E: S2, F: S3, G: S4, H: S5, I: S6, J: US
            
            foreach ($rows as $key => $row) {
                if ($key < 3) continue; // Skip Header Rows (0, 1, 2)
                
                $nisn = trim($row[1]); // Col B
                
                if (!empty($nisn)) {
                    // Find Student
                    $stmt = $pdo->prepare("SELECT id FROM students WHERE nisn = ?");
                    $stmt->execute([$nisn]);
                    $student = $stmt->fetch();
                    
                    if ($student) {
                        $s1 = floatval($row[3]); // D
                        $s2 = floatval($row[4]); // E
                        $s3 = floatval($row[5]); // F
                        $s4 = floatval($row[6]); // G
                        $s5 = floatval($row[7]); // H
                        $s6 = floatval($row[8]); // I
                        $us = floatval($row[9]); // J (School Exam)
                        
                        // Calculate Average Semester
                        // Check if all are 0? Use 0.
                        $avgSem = ($s1 + $s2 + $s3 + $s4 + $s5 + $s6) / 6;
                        
                        // Calculate Final Score
                        // Formula: (RataRapor + US) / 2
                        // If US is 0, maybe just RataRapor? Standard usually implies US is mandatory.
                        // We will follow the formula strictly.
                        $finalScore = ($avgSem + $us) / 2;
                        
                        // Upsert
                        $check = $pdo->prepare("SELECT id FROM grades WHERE student_id = ? AND subject_id = ?");
                        $check->execute([$student['id'], $subject_id]);
                        
                        if ($check->fetch()) {
                            $sql = "UPDATE grades SET sem1=?, sem2=?, sem3=?, sem4=?, sem5=?, sem6=?, school_exam=?, score=? WHERE student_id=? AND subject_id=?";
                            $pdo->prepare($sql)->execute([$s1, $s2, $s3, $s4, $s5, $s6, $us, $finalScore, $student['id'], $subject_id]);
                        } else {
                            $sql = "INSERT INTO grades (sem1, sem2, sem3, sem4, sem5, sem6, school_exam, score, student_id, subject_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $pdo->prepare($sql)->execute([$s1, $s2, $s3, $s4, $s5, $s6, $us, $finalScore, $student['id'], $subject_id]);
                        }
                        $count++;
                    }
                }
            }
            $message = '<div class="alert alert-success">Berhasil import nilai untuk '.$count.' siswa!</div>';
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Error: '.$e->getMessage().'</div>';
        }
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Import Nilai Per Mapel</h1>
    <a href="grades.php" class="btn btn-secondary">Kembali</a>
</div>

<?= $message ?>

<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Pilih Mata Pelajaran</label>
                <select name="subject_id" id="subject_id" class="form-select" required onchange="updateDownloadLink()">
                    <option value="">-- Pilih Mapel --</option>
                    <?php foreach($subjects as $sub): ?>
                        <option value="<?= $sub['id'] ?>"><?= $sub['name'] ?> (<?= $sub['type'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Pilih Kelas (Opsional)</label>
                <select name="class_name" id="class_name" class="form-select" onchange="updateDownloadLink()">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach($classes as $cls): ?>
                        <option value="<?= $cls['name'] ?>"><?= $cls['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Pilih kelas untuk memfilter siswa di template.</div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Download Template</label><br>
                <a href="#" id="downloadBtn" class="btn btn-outline-primary disabled">Download Template Excel</a>
                <div class="form-text">Pilih mapel terlebih dahulu untuk download template.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload File Excel Hasil Template</label>
                <input type="file" name="file" class="form-control" required accept=".xlsx, .xls">
            </div>

            <button type="submit" class="btn btn-success">Import Nilai</button>
        </form>
    </div>
</div>

<script>
function updateDownloadLink() {
    var subjectId = document.getElementById('subject_id').value;
    var className = document.getElementById('class_name').value;
    var btn = document.getElementById('downloadBtn');
    
    if (subjectId) {
        var url = 'download_template_nilai.php?subject_id=' + subjectId;
        if (className) {
            url += '&class_name=' + encodeURIComponent(className);
        }
        btn.href = url;
        btn.classList.remove('disabled');
        btn.innerHTML = 'Download Template';
        if (className) {
            btn.innerHTML += ' (' + className + ')';
        }
    } else {
        btn.href = '#';
        btn.classList.add('disabled');
        btn.innerHTML = 'Download Template Excel';
    }
}
</script>

<?php require_once 'footer.php'; ?>
