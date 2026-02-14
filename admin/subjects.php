<?php
require_once 'header.php';

$message = '';

// Handle POST requests (Add/Edit/Delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_token(); // Protect

    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $message = '<div class="alert alert-success">Mata Pelajaran dihapus!</div>';
    } else {
        $code = strtoupper($_POST['code']);
        $name = $_POST['name'];
        $type = $_POST['type'];
        if (isset($_POST['class_name']) && is_array($_POST['class_name'])) {
            // Remove empty values (from hidden input)
            $classesInput = array_filter($_POST['class_name']);
            if (empty($classesInput) || in_array('Semua', $classesInput)) {
                $class_name = 'Semua';
            } else {
                $class_name = implode(',', $classesInput);
            }
        } else {
            $class_name = 'Semua';
        }
        
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Edit
            $stmt = $pdo->prepare("UPDATE subjects SET code=?, name=?, type=?, class_name=? WHERE id=?");
            if ($stmt->execute([$code, $name, $type, $class_name, $_POST['id']])) {
                $message = '<div class="alert alert-success">Mata Pelajaran diperbarui!</div>';
            }
        } else {
            // Add
            $stmt = $pdo->prepare("INSERT INTO subjects (code, name, type, class_name) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$code, $name, $type, $class_name])) {
                $message = '<div class="alert alert-success">Mata Pelajaran ditambahkan!</div>';
            }
        }
    }
}

$subjects = $pdo->query("SELECT * FROM subjects ORDER BY type, name")->fetchAll();
$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
$editSubject = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editSubject = $stmt->fetch();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manajemen Mata Pelajaran</h1>
</div>

<?= $message ?>

<div class="card mb-4">
    <div class="card-header"><?= $editSubject ? 'Edit' : 'Tambah' ?> Mata Pelajaran</div>
    <div class="card-body">
        <form method="POST" action="subjects.php">
            <?= csrf_field() ?>
            <?php if ($editSubject): ?>
                <input type="hidden" name="id" value="<?= $editSubject['id'] ?>">
            <?php endif; ?>
            <div class="row g-3">
            <div class="row g-3">
                <div class="col-4 col-md-2">
                    <label class="form-label">Kode</label>
                    <input type="text" name="code" class="form-control" placeholder="Kode" value="<?= $editSubject['code'] ?? '' ?>" required>
                </div>
                <div class="col-8 col-md-3">
                    <label class="form-label">Kelompok</label>
                    <select name="type" class="form-select" required>
                        <option value="General" <?= ($editSubject['type'] ?? '') == 'General' ? 'selected' : '' ?>>Umum (A)</option>
                        <option value="Elective" <?= ($editSubject['type'] ?? '') == 'Elective' ? 'selected' : '' ?>>Pilihan (B)</option>
                        <option value="Local" <?= ($editSubject['type'] ?? '') == 'Local' ? 'selected' : '' ?>>Muatan Lokal</option>
                    </select>
                </div>
                <div class="col-12 col-md-7">
                    <label class="form-label">Nama Mata Pelajaran</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Mapel" value="<?= $editSubject['name'] ?? '' ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Pilih Kelas</label>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle" type="button" id="dropdownClasses" data-bs-toggle="dropdown" aria-expanded="false">
                            Pilih Kelas...
                        </button>
                        <div class="dropdown-menu w-100 p-3 dropdown-menu-prevent-close" aria-labelledby="dropdownClasses" style="max-height: 300px; overflow-y: auto;">
                            <input type="hidden" name="class_name[]" value="">
                            <?php 
                            $selectedClasses = explode(',', $editSubject['class_name'] ?? 'Semua');
                            ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="class_name[]" value="Semua" id="chk_all" <?= in_array('Semua', $selectedClasses) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="chk_all">Semua Kelas</label>
                            </div>
                            <hr class="dropdown-divider">
                            <div class="row">
                                <?php foreach($classes as $cls): ?>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="class_name[]" value="<?= $cls['name'] ?>" id="chk_<?= $cls['id'] ?>" <?= in_array($cls['name'], $selectedClasses) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="chk_<?= $cls['id'] ?>"><?= $cls['name'] ?></label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-text text-muted">Klik tombol untuk memilih kelas.</div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><?= $editSubject ? 'Update Mapel' : 'Simpan Mapel' ?></button>
                    <?php if ($editSubject): ?>
                        <a href="subjects.php" class="btn btn-secondary ms-2">Batal</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Mata Pelajaran</th>
                <th>Kelompok</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $index => $subject): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($subject['code']) ?></td>
                <td class="text-start"><?= htmlspecialchars($subject['name']) ?></td>
                <td>
                    <?php 
                    if($subject['type'] == 'General') echo '<span class="badge bg-primary">Umum</span>';
                    elseif($subject['type'] == 'Elective') echo '<span class="badge bg-success">Pilihan</span>';
                    else echo '<span class="badge bg-warning text-dark">Muatan Lokal</span>';
                    ?>
                </td>
                <td class="text-center">
                    <?php 
                    $classList = explode(',', $subject['class_name'] ?? 'Semua');
                    if(in_array('Semua', $classList)): ?>
                        <span class="badge bg-secondary">Semua</span>
                    <?php else: 
                        foreach($classList as $c): ?>
                            <span class="badge bg-info text-dark mb-1"><?= htmlspecialchars($c) ?></span>
                        <?php endforeach; 
                    endif; ?>
                </td>
                <td>
                    <a href="subjects.php?edit=<?= $subject['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                    <a href="subjects.php?edit=<?= $subject['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Hapus mapel ini?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="delete_id" value="<?= $subject['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
