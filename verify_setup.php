<?php
require_once 'config/database.php';

echo "Running Verification...\n";

// 1. Check Database Connection
if ($pdo) {
    echo "[OK] Database Connection Successful.\n";
} else {
    echo "[FAIL] Database Connection Failed.\n";
    exit;
}

// 2. Check Tables
$tables = ['settings', 'users', 'subjects', 'students', 'grades'];
foreach ($tables as $t) {
    $check = $pdo->query("SHOW TABLES LIKE '$t'")->rowCount();
    if ($check > 0) {
        echo "[OK] Table '$t' exists.\n";
    } else {
        echo "[FAIL] Table '$t' MISSING!\n";
    }
}

// 3. Check Critical Files
$files = [
    'index.php', 'result.php', 'print_pdf.php', 'login.php',
    'admin/index.php', 'admin/students.php', 'admin/settings.php',
    'helpers/csrf.php',
    'vendor/autoload.php'
];
foreach ($files as $f) {
    if (file_exists($f)) {
        echo "[OK] File '$f' exists.\n";
    } else {
        echo "[FAIL] File '$f' MISSING!\n";
    }
}

// 4. Check Sample Data
$student = $pdo->query("SELECT * FROM students LIMIT 1")->fetch();
if ($student) {
    echo "[OK] Sample Student found: " . $student['name'] . "\n";
} else {
    echo "[WARN] No students found.\n";
}

$grades = $pdo->query("SELECT COUNT(*) FROM grades")->fetchColumn();
echo "[OK] Total Grades records: $grades\n";

echo "Verification Complete.\n";
