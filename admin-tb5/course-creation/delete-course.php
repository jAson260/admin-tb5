<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\course-creation\delete-course.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');
checkAdmin();

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!isset($data['id'])) {
        throw new Exception('Course ID is required');
    }
    
    $courseId = intval($data['id']);
    
    // Check if course has enrollments (if you have enrollment table)
    // Uncomment if you have student_course_enrollments table
    /*
    $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM student_course_enrollments WHERE CourseId = ?");
    $checkStmt->execute([$courseId]);
    $result = $checkStmt->fetch();
    
    if ($result['count'] > 0) {
        throw new Exception('Cannot delete course with existing enrollments');
    }
    */
    
    $stmt = $pdo->prepare("DELETE FROM courses WHERE Id = ?");
    $stmt->execute([$courseId]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Course not found');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Course deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Delete course error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>