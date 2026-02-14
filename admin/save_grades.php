<?php
require_once '../config/database.php';
require_once '../helpers/csrf.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['student_id'])) {
    verify_csrf_token(); // Protect
    $student_id = $_POST['student_id'];
    $gradesInput = $_POST['grades']; // Array [subject_id => [s1, s2...]]

    foreach ($gradesInput as $subject_id => $sems) {
        $s1 = floatval($sems['s1']);
        $s2 = floatval($sems['s2']);
        $s3 = floatval($sems['s3']);
        $s4 = floatval($sems['s4']);
        $s5 = floatval($sems['s5']);
        $s6 = floatval($sems['s6']);
        $us = floatval($sems['us']); // School Exam
        
        $avgSem = ($s1 + $s2 + $s3 + $s4 + $s5 + $s6) / 6;
        $finalScore = ($avgSem + $us) / 2;

        // Upsert
        $check = $pdo->prepare("SELECT id FROM grades WHERE student_id = ? AND subject_id = ?");
        $check->execute([$student_id, $subject_id]);
        if ($check->fetch()) {
            $sql = "UPDATE grades SET sem1=?, sem2=?, sem3=?, sem4=?, sem5=?, sem6=?, school_exam=?, score=? WHERE student_id=? AND subject_id=?";
            $pdo->prepare($sql)->execute([$s1, $s2, $s3, $s4, $s5, $s6, $us, $finalScore, $student_id, $subject_id]);
        } else {
            $sql = "INSERT INTO grades (sem1, sem2, sem3, sem4, sem5, sem6, school_exam, score, student_id, subject_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$s1, $s2, $s3, $s4, $s5, $s6, $us, $finalScore, $student_id, $subject_id]);
        }
    }
    
    header("Location: grades.php?msg=saved");
} else {
    header("Location: grades.php");
}
