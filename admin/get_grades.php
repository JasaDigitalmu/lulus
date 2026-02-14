<?php
require_once '../config/database.php';
require_once '../helpers/csrf.php';

if (!isset($_GET['student_id'])) die('Invalid Request');

$student_id = $_GET['student_id'];
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY type, name")->fetchAll();
$grades = $pdo->prepare("SELECT * FROM grades WHERE student_id = ?");
$grades->execute([$student_id]);
$gradeMap = [];
foreach ($grades->fetchAll() as $g) {
    $gradeMap[$g['subject_id']] = $g;
}
?>

<form action="save_grades.php" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="student_id" value="<?= $student_id ?>">
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-sm table-bordered">
            <thead class="table-light sticky-top">
                <tr>
                    <th rowspan="2" class="align-middle">Mapel</th>
                    <th colspan="6" class="text-center">Nilai Semester</th>
                    <th rowspan="2" class="align-middle">US</th>
                    <th rowspan="2" class="align-middle">Akhir</th>
                </tr>
                <tr>
                    <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subjects as $subj): 
                    $g = $gradeMap[$subj['id']] ?? [];
                ?>
                <tr>
                    <td><small><?= htmlspecialchars($subj['name']) ?></small></td>
                    <td><input type="number" step="0.01" name="grades[<?= $subj['id'] ?>][s1]" class="form-control form-control-sm px-1" value="<?= $g['sem1'] ?? 0 ?>" style="width: 45px;"></td>
                    <td><input type="number" step="0.01" name="grades[<?= $subj['id'] ?>][s2]" class="form-control form-control-sm px-1" value="<?= $g['sem2'] ?? 0 ?>" style="width: 45px;"></td>
                    <td><input type="number" step="0.01" name="grades[<?= $subj['id'] ?>][s3]" class="form-control form-control-sm px-1" value="<?= $g['sem3'] ?? 0 ?>" style="width: 45px;"></td>
                    <td><input type="number" step="0.01" name="grades[<?= $subj['id'] ?>][s4]" class="form-control form-control-sm px-1" value="<?= $g['sem4'] ?? 0 ?>" style="width: 45px;"></td>
                    <td><input type="number" step="0.01" name="grades[<?= $subj['id'] ?>][s5]" class="form-control form-control-sm px-1" value="<?= $g['sem5'] ?? 0 ?>" style="width: 45px;"></td>
                    <td><input type="number" step="0.01" name="grades[<?= $subj['id'] ?>][s6]" class="form-control form-control-sm px-1" value="<?= $g['sem6'] ?? 0 ?>" style="width: 45px;"></td>
                    <td><input type="number" step="0.01" name="grades[<?= $subj['id'] ?>][us]" class="form-control form-control-sm px-1 bg-warning bg-opacity-10" value="<?= $g['school_exam'] ?? 0 ?>" style="width: 45px;"></td>
                    <td class="bg-light text-center fw-bold"><?= $g['score'] ?? '-' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="d-grid gap-2 mt-3">
        <button type="submit" class="btn btn-primary">Simpan & Hitung Nilai Akhir</button>
    </div>
</form>
