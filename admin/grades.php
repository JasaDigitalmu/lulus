<?php
require_once 'header.php';

$classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();

// Filter Logic
$filter_class = $_GET['filter_class'] ?? '';
$query = "SELECT * FROM students";
$params = [];

if ($filter_class) {
    $query .= " WHERE class = ?";
    $params[] = $filter_class;
}

$query .= " ORDER BY class, name";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();

$subjects = $pdo->query("SELECT * FROM subjects")->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Nilai Siswa</h1>
    <div>
        <div class="btn-group me-2">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-printer"></i> Cetak Transkrip
            </button>
            <ul class="dropdown-menu">
                <li><h6 class="dropdown-header">Ukuran A4</h6></li>
                <li><a class="dropdown-item" href="../print_bulk.php?class=<?= urlencode($filter_class) ?>&size=A4" target="_blank"><?= $filter_class ? "Cetak Kelas $filter_class" : "Cetak Semua Data" ?></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><h6 class="dropdown-header">Ukuran Legal (F4)</h6></li>
                <li><a class="dropdown-item" href="../print_bulk.php?class=<?= urlencode($filter_class) ?>&size=Legal" target="_blank"><?= $filter_class ? "Cetak Kelas $filter_class" : "Cetak Semua Data" ?></a></li>
            </ul>
        </div>
        <a href="import_grades.php" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Import Nilai</a>
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
                <td class="text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info" onclick="viewGrades(<?= $student['id'] ?>, '<?= $student['name'] ?>')">
                            <i class="bi bi-eye"></i> Nilai
                        </button>
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-printer"></i> Cetak
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../print_pdf.php?nisn=<?= $student['nisn'] ?>&size=A4" target="_blank">Ukuran A4</a></li>
                            <li><a class="dropdown-item" href="../print_pdf.php?nisn=<?= $student['nisn'] ?>&size=Legal" target="_blank">Ukuran Legal</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal View Grades -->
<div class="modal fade" id="gradeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nilai Siswa: <span id="studentName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="gradeContent">
                Loading...
            </div>
        </div>
    </div>
</div>

<script>
function viewGrades(id, name) {
    document.getElementById('studentName').innerText = name;
    var modal = new bootstrap.Modal(document.getElementById('gradeModal'));
    modal.show();
    
    // Ajax fetch grades (simple implementation: load an IFRAME or fetch HTML)
    // For simplicity, let's load via fetch and inject HTML
    fetch('get_grades.php?student_id=' + id)
    .then(response => response.text())
    .then(html => {
        document.getElementById('gradeContent').innerHTML = html;
    });
}
</script>

<?php require_once 'footer.php'; ?>
