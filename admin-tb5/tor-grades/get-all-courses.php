<?php

session_start();
require_once('../../db-connect.php');
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT
            c.Id,
            c.CourseCode,
            c.CourseName,
            COUNT(DISTINCT e.StudentId) AS StudentCount
        FROM courses c
        LEFT JOIN enrollments e ON e.CourseId = c.Id
        GROUP BY c.Id
        ORDER BY c.CourseName
    ");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'courses' => $courses]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>