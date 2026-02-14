<?php
require_once 'config/database.php';
$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$appSettings = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Kelulusan - <?= htmlspecialchars($appSettings['school_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .logo-img {
            max-height: 100px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card p-5 text-center">
                    <?php if($appSettings['logo']): ?>
                        <img src="uploads/<?= $appSettings['logo'] ?>" alt="Logo Sekolah" class="logo-img mx-auto d-block">
                    <?php endif; ?>
                    <h3 class="mb-2">Pengumuman Kelulusan</h3>
                    <h5 class="text-muted mb-4"><?= htmlspecialchars($appSettings['school_name']) ?></h5>
                    
                    <form action="result.php" method="GET">
                        <div class="mb-3">
                            <input type="text" name="nisn" class="form-control form-control-lg text-center" placeholder="Masukkan NISN Anda" required autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Cek Kelulusan</button>
                    </form>
                    
                    <div class="mt-4 text-muted small">
                        &copy; <?= date('Y') ?> <?= htmlspecialchars($appSettings['app_name']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
