<?php
require_once 'header.php';

$message = '';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf_token();
    
    if (isset($_POST['delete_id'])) {
        // Delete
        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
        if ($stmt->execute([$_POST['delete_id']])) {
            $message = '<div class="alert alert-success">Kelas berhasil dihapus!</div>';
        }
    } else {
        // Add / Edit
        $name = trim($_POST['name']);
        
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Edit
            $stmt = $pdo->prepare("UPDATE classes SET name=? WHERE id=?");
            if ($stmt->execute([$name, $_POST['id']])) {
                $message = '<div class="alert alert-success">Nama kelas diperbarui!</div>';
            }
        } else {
            // Add
            if (!empty($name)) {
                $stmt = $pdo->prepare("INSERT INTO classes (name) VALUES (?)");
                if ($stmt->execute([$name])) {
                    $message = '<div class="alert alert-success">Kelas baru ditambahkan!</div>';
                }
            }
        }
    }
}

$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
$editClass = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editClass = $stmt->fetch();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manajemen Kelas</h1>
</div>

<?= $message ?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header"><?= $editClass ? 'Edit' : 'Tambah' ?> Kelas</div>
            <div class="card-body">
                <form method="POST" action="classes.php">
                    <?= csrf_field() ?>
                    <?php if ($editClass): ?>
                        <input type="hidden" name="id" value="<?= $editClass['id'] ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: 12-F1" value="<?= $editClass['name'] ?? '' ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><?= $editClass ? 'Update' : 'Simpan' ?></button>
                    <?php if ($editClass): ?>
                        <a href="classes.php" class="btn btn-secondary w-100 mt-2">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Daftar Kelas</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Kelas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classes as $index => $cls): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($cls['name']) ?></td>
                                <td>
                                    <a href="classes.php?edit=<?= $cls['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Hapus kelas ini?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="delete_id" value="<?= $cls['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
