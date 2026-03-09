<?php

session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    $school = trim($_GET['school'] ?? '');

    if (!in_array($school, ['TB5', 'BBI'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid school.', 'courses' => []]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT Id, CourseCode, CourseName
        FROM courses
        WHERE School = ? AND IsActive = 1
        ORDER BY CourseName ASC
    ");
    $stmt->execute([$school]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'courses' => $courses]);

} catch (PDOException $e) {
    error_log("get-courses-by-school.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'courses' => []]);
}