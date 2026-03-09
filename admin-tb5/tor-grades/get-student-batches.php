<?php

session_start();
require_once('../../db-connect.php');
header('Content-Type: application/json');

$studentId = intval($_GET['student_id'] ?? 0);
if (!$studentId) {
    echo json_encode(['success' => false, 'message' => 'No student ID provided']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            e.BatchId,
            b.BatchCode,
            b.BatchName,
            b.School,
            b.Status         AS BatchStatus,
            c.Id             AS CourseId,
            c.CourseCode,
            c.CourseName
        FROM enrollments e
        JOIN batches b ON b.Id = e.BatchId
        JOIN courses c ON c.Id = e.CourseId
        WHERE e.StudentId = ?
        ORDER BY b.StartDate DESC
    ");
    $stmt->execute([$studentId]);
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'batches' => $batches]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>