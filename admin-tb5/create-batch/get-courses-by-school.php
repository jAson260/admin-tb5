<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\create-batch\get-courses-by-school.php
require_once('../../db-connect.php');

header('Content-Type: application/json');

$school = trim($_GET['school'] ?? '');

if (empty($school)) {
    echo json_encode(['success' => false, 'message' => 'School parameter is required', 'courses' => []]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT Id, CourseCode, CourseName, Category, Duration, DurationHours, MaxStudents
        FROM courses
        WHERE School = ?
        AND IsActive = 1
        ORDER BY CourseName ASC
    ");
    $stmt->execute([$school]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'courses' => $courses
    ]);

} catch (PDOException $e) {
    error_log('Get Courses Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage(), 'courses' => []]);
}
?>