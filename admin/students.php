<?php
require_once 'header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    if ($stmt->execute([$_GET['delete']])) {
        echo "<script>alert('Siswa berhasil dihapus!'); window.location='students.php';</script>";
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nisn = $_POST['nisn'];
    $name = $_POST['name'];
    $class = $_POST['class'];
    $gender = $_POST['gender'];
    $pob = $_POST['pob'];
    $dob = $_POST['dob'];
    $status = $_POST['status'];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Edit
        $stmt = $pdo->prepare("UPDATE students SET nisn=?, name=?, class=?, gender=?, pob=?, dob=?, status=? WHERE id=?");
        $stmt->execute([$nisn, $name, $class, $gender, $pob, $dob, $status, $_POST['id']]);
    } else {
        // Add
        $stmt = $pdo->prepare("INSERT INTO students (nisn, name, class, gender, pob, dob, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nisn, $name, $class, $gender, $pob, $dob, $status]);
    }
    echo "<script>alert('Data siswa berhasil disimpan!'); window.location='students.php';</script>";
}

// Filter & Pagination Logic
$filter_class = $_GET['filter_class'] ?? '';
$limit = $_GET['limit'] ?? '25';

$query = "SELECT * FROM students";
$params = [];

if ($filter_class) {
    $query .= " WHERE class = ?";
    $params[] = $filter_class;
}

$query .= " ORDER BY class, name";

if ($limit != 'all') {
    $query .= " LIMIT " . intval($limit);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();

$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Siswa</h1>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentModal" onclick="resetForm()">
            <i class="bi bi-plus-circle"></i> Tambah Siswa
        </button>
        <a href="import_students.php" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Import Excel</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <label class="col-form-label">Filter Kelas:</label>
            </div>
            <div class="col-auto">
                <select name="filter_class" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    <?php foreach($classes as $cls): ?>
                        <option value="<?= htmlspecialchars($cls['name']) ?>" <?= $filter_class == $cls['name'] ? 'selected' : '' ?>><?= htmlspecialchars($cls['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto ms-3">
                <label class="col-form-label">Tampilkan:</label>
            </div>
            <div class="col-auto">
                <select name="limit" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="25" <?= $limit == '25' ? 'selected' : '' ?>>25 Baris</option>
                    <option value="50" <?= $limit == '50' ? 'selected' : '' ?>>50 Baris</option>
                    <option value="all" <?= $limit == 'all' ? 'selected' : '' ?>>Semua</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama Lengkap</th>
                <th>Kelas</th>
                <th>L/P</th>
                <th>TTL</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $index => $student): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td class="text-center"><?= htmlspecialchars($student['nisn']) ?></td>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td class="text-center"><?= htmlspecialchars($student['class']) ?></td>
                <td class="text-center"><?= htmlspecialchars($student['gender']) ?></td>
                <td><?= htmlspecialchars($student['pob']) ?>, <?= htmlspecialchars($student['dob']) ?></td>
                <td class="text-center">
                    <?php if($student['status'] == 'LULUS'): ?>
                        <span class="badge bg-success">LULUS</span>
                    <?php else: ?>
                        <span class="badge bg-danger">TIDAK LULUS</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning" onclick='editStudent(<?= json_encode($student, JSON_HEX_APOS) ?>)'><i class="bi bi-pencil"></i></button>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Hapus siswa ini?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="delete_id" value="<?= $student['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="studentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="studentForm">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="studentId">
                    <div class="mb-3">
                        <label>NISN</label>
                        <input type="text" name="nisn" id="nisn" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Kelas</label>
                        <select name="class" id="class" class="form-select" required>
                            <option value="">Pilih Kelas</option>
                            <?php foreach($classes as $cls): ?>
                                <option value="<?= htmlspecialchars($cls['name']) ?>"><?= htmlspecialchars($cls['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Jenis Kelamin</label>
                        <select name="gender" id="gender" class="form-select" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tempat Lahir</label>
                            <input type="text" name="pob" id="pob" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="dob" id="dob" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="LULUS">LULUS</option>
                            <option value="TIDAK LULUS">TIDAK LULUS</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var studentModal = new bootstrap.Modal(document.getElementById('studentModal'));

function editStudent(data) {
    document.getElementById('studentId').value = data.id;
    document.getElementById('nisn').value = data.nisn;
    document.getElementById('name').value = data.name;
    document.getElementById('class').value = data.class;
    document.getElementById('gender').value = data.gender;
    document.getElementById('pob').value = data.pob;
    document.getElementById('dob').value = data.dob;
    document.getElementById('status').value = data.status;
    studentModal.show();
}

function resetForm() {
    document.getElementById('studentId').value = '';
    document.getElementById('studentForm').reset();
    // studentModal.show(); // Button already has data-bs-toggle="modal"
}
</script>

<?php require_once 'footer.php'; ?>
