<?php
require_once 'header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_token(); // Protect
    // Handle Logo Upload
    if (!empty($_FILES['logo']['name'])) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["logo"]["name"]);
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
            $sql = "UPDATE settings SET 
            app_name = ?, school_name = ?, headmaster_name = ?, 
            headmaster_nip = ?, npsn = ?, address = ?, website = ?, 
            graduation_date = ?, letter_number = ?, logo = ? WHERE id = 1";
            $params[] = $_FILES["logo"]["name"]; // Re-bind params if needed? No, wait.
            // The params array structure is tricky here because we are building the SQL dynamically.
            // Let's refactor slightly to be safer.
            
            // Actually, simplest way without full refactor:
            // Execute the base update first.
            // Then update logo if present.
            // Then update favicon if present.
            
            // BUT, I'll stick to the existing pattern but just handle them sequentially.
            // Wait, the existing code replaces $sql. This is bad if both are uploaded.
            
            // Refactored Logic:
            // 1. Base Update
            // 2. Logo Update (separate query)
            // 3. Favicon Update (separate query)
        }
    }
    
    // Let's just do robust updates.
    $stmt = $pdo->prepare("UPDATE settings SET 
            app_name = ?, school_name = ?, headmaster_name = ?, 
            headmaster_nip = ?, npsn = ?, address = ?, website = ?, 
            graduation_date = ?, letter_number = ? WHERE id = 1");
    if ($stmt->execute($params)) {
        // Logo Upload
        if (!empty($_FILES['logo']['name'])) {
            $logoName = time() . '_' . $_FILES['logo']['name'];
            move_uploaded_file($_FILES['logo']['tmp_name'], "../uploads/" . $logoName);
            $pdo->prepare("UPDATE settings SET logo = ? WHERE id = 1")->execute([$logoName]);
        }
        
        $message = '<div class="alert alert-success">Pengaturan berhasil diperbarui!</div>';
        $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
        $appSettings = $stmt->fetch();
    } else {
        $message = '<div class="alert alert-danger">Gagal memperbarui pengaturan.</div>';
    }
}
?>

<h3>Pengaturan Identitas Sekolah</h3>
<?= $message ?>

<form method="POST" enctype="multipart/form-data" class="mt-4">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Nama Aplikasi</label>
            <input type="text" name="app_name" class="form-control" value="<?= htmlspecialchars($appSettings['app_name']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Nama Sekolah</label>
            <input type="text" name="school_name" class="form-control" value="<?= htmlspecialchars($appSettings['school_name']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Nama Kepala Sekolah</label>
            <input type="text" name="headmaster_name" class="form-control" value="<?= htmlspecialchars($appSettings['headmaster_name']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">NIP Kepala Sekolah</label>
            <input type="text" name="headmaster_nip" class="form-control" value="<?= htmlspecialchars($appSettings['headmaster_nip']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">NPSN</label>
            <input type="text" name="npsn" class="form-control" value="<?= htmlspecialchars($appSettings['npsn']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Website Sekolah</label>
            <input type="text" name="website" class="form-control" value="<?= htmlspecialchars($appSettings['website']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Nomor Surat SKL</label>
            <input type="text" name="letter_number" class="form-control" value="<?= htmlspecialchars($appSettings['letter_number']) ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Tanggal Kelulusan</label>
            <input type="date" name="graduation_date" class="form-control" value="<?= htmlspecialchars($appSettings['graduation_date']) ?>" required>
        </div>
        <div class="col-md-12 mb-3">
            <label class="form-label">Alamat Sekolah</label>
            <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($appSettings['address']) ?></textarea>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Logo Sekolah</label>
            <input type="file" name="logo" class="form-control">
            <?php if($appSettings['logo']): ?>
                <img src="../uploads/<?= $appSettings['logo'] ?>" alt="Logo" height="50" class="mt-2">
            <?php endif; ?>
        </div>

    </div>
    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
</form>

<?php require_once 'footer.php'; ?>
