<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
require_once '../config/database.php';
require_once '../helpers/csrf.php';

// Fetch School Info for Title
$stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
$appSettings = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appSettings['app_name'] ?? 'Admin Panel') ?></title>
    <?php if (!empty($appSettings['logo'])): ?>
    <link rel="icon" href="../uploads/<?= $appSettings['logo'] ?>" type="image/x-icon">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { min-height: 100vh; overflow-x: hidden; }
        .sidebar { 
            min-height: 100vh; 
            background: #343a40; 
            color: white; 
            width: 250px; 
            transition: margin-left 0.3s;
            flex-shrink: 0;
        }
        .sidebar.collapsed { margin-left: -250px; }
        .sidebar a { color: rgba(255,255,255,.8); text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { background: #495057; color: white; }
        .content { width: 100%; padding: 20px; transition: all 0.3s; }
        @media (max-width: 768px) {
            .sidebar { margin-left: -250px; position: absolute; z-index: 1000; height: 100%; }
            .sidebar.show { margin-left: 0; }
        }
    </style>
</head>
<body>
<div class="d-flex" id="wrapper">
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white" id="sidebar">
        <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class="fs-4">Admin Panel</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li><a href="index.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
            <li><a href="settings.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>"><i class="bi bi-gear me-2"></i> Pengaturan Sekolah</a></li>
            <li><a href="subjects.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'subjects.php' ? 'active' : '' ?>"><i class="bi bi-book me-2"></i> Mata Pelajaran</a></li>
            <li><a href="classes.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'classes.php' ? 'active' : '' ?>"><i class="bi bi-building me-2"></i> Data Kelas</a></li>
            <li><a href="students.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : '' ?>"><i class="bi bi-people me-2"></i> Data Siswa</a></li>
            <li><a href="grades.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'grades.php' ? 'active' : '' ?>"><i class="bi bi-card-checklist me-2"></i> Nilai Siswa</a></li>
            <li><a href="users.php" class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>"><i class="bi bi-person-lock me-2"></i> Manajemen User</a></li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
            </ul>
        </div>
    </div>
    <div class="content flex-grow-1">
        <button class="btn btn-dark mb-3" id="sidebarToggle"><i class="bi bi-list"></i> Menu</button>
