<?php
require_once 'config/database.php';

$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$appSettings = $stmt->fetch();

$nisn = $_GET['nisn'] ?? '';
$student = null;
if ($nisn) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE nisn = ?");
    $stmt->execute([$nisn]);
    $student = $stmt->fetch();
} else {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kelulusan - <?= htmlspecialchars($student['name'] ?? 'Tidak Ditemukan') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .status-box { padding: 20px; border-radius: 10px; text-align: center; color: white; margin-bottom: 20px; }
        .status-lulus { background-color: #28a745; }
        .status-gagal { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-4">
            <?php if($appSettings['logo']): ?>
                <img src="uploads/<?= $appSettings['logo'] ?>" alt="Logo" height="80">
            <?php endif; ?>
            <h3 class="mt-2"><?= htmlspecialchars($appSettings['school_name']) ?></h3>
        </div>

        <?php if ($student): ?>
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="card-title text-center mb-4">Data Peserta Didik</h4>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nama Lengkap</th>
                                    <td>: <?= htmlspecialchars($student['name']) ?></td>
                                </tr>
                                <tr>
                                    <th>NISN</th>
                                    <td>: <?= htmlspecialchars($student['nisn']) ?></td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td>: <?= htmlspecialchars($student['class']) ?></td>
                                </tr>
                            </table>

                            <div class="status-box <?= $student['status'] == 'LULUS' ? 'status-lulus' : 'status-gagal' ?>">
                                <h4>ANDA DINYATAKAN</h4>
                                <h1><?= htmlspecialchars($student['status']) ?></h1>
                            </div>

                            <?php if ($student['status'] == 'LULUS'): ?>
                                <div class="text-center">
                                    <div class="mb-3 d-inline-block text-start">
                                        <label class="form-label fw-bold">Pilih Ukuran Kertas:</label>
                                        <select id="paperSize" class="form-select">
                                            <option value="A4">A4</option>
                                            <option value="Legal">Legal (F4)</option>
                                        </select>
                                    </div>
                                    <br>
                                    <button onclick="printSKL('<?= $student['nisn'] ?>')" class="btn btn-primary btn-lg">
                                        <i class="bi bi-printer"></i> Cetak Surat Keterangan Lulus (SKL)
                                    </button>
                                </div>
                                <script>
                                function printSKL(nisn) {
                                    var size = document.getElementById('paperSize').value;
                                    window.open('print_pdf.php?nisn=' + nisn + '&size=' + size, '_blank');
                                }
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>

        <?php else: ?>
            <div class="alert alert-danger text-center">
                Data siswa dengan NISN <strong><?= htmlspecialchars($nisn) ?></strong> tidak ditemukan.
                <br><br>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
