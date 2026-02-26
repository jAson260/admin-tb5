<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\course-creation\get-course-details.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');
checkAdmin();

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Course ID is required');
    }
    
    $courseId = intval($_GET['id']);
    
    $stmt = $pdo->prepare("
        SELECT 
            Id, CourseCode, CourseName, Description, School, 
            Duration, DurationHours, Category, MaxStudents, 
            Prerequisites, Tuition, IsActive, CreatedAt, UpdatedAt
        FROM courses
        WHERE Id = ?
    ");
    
    $stmt->execute([$courseId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        throw new Exception('Course not found');
    }
    
    echo json_encode([
        'success' => true,
        'course' => $course
    ]);
    
} catch (Exception $e) {
    error_log("Get course details error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>