<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\documents-approval\get-courses-filter.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$school = $_GET['school'] ?? '';

if (empty($school)) {
    echo json_encode(['success' => false, 'message' => 'School parameter required']);
    exit;
}

try {
    // Fetch active courses for the selected school
    $stmt = $pdo->prepare("
        SELECT 
            Id,
            CourseCode,
            CourseName,
            School
        FROM courses 
        WHERE School = ? AND IsActive = 1 
        ORDER BY CourseName ASC
    ");
    
    $stmt->execute([strtoupper($school)]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'courses' => $courses
    ]);
    
} catch (PDOException $e) {
    error_log('Get Courses Filter Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>