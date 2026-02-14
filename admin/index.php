<?php
require_once 'header.php';

// Count Data
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$passedStudents = $pdo->query("SELECT COUNT(*) FROM students WHERE status = 'LULUS'")->fetchColumn();
$totalSubjects = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
?>

<h3>Dashboard</h3>
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Total Siswa</div>
            <div class="card-body">
                <h5 class="card-title"><?= $totalStudents ?> Siswa</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Siswa Lulus</div>
            <div class="card-body">
                <h5 class="card-title"><?= $passedStudents ?> Siswa</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">Mata Pelajaran</div>
            <div class="card-body">
                <h5 class="card-title"><?= $totalSubjects ?> Mapel</h5>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info mt-3">
    Selamat Datang, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> di Aplikasi Pengumuman Kelulusan.
</div>

<?php require_once 'footer.php'; ?>
