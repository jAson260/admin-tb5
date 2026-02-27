<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\create-batch\get-courses-by-school.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$school = $_GET['school'] ?? '';

if (empty($school)) {
    echo json_encode([
        'success' => false,
        'message' => 'School parameter is required',
        'courses' => []
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            Id,
            CourseCode,
            CourseName,
            Category,
            Duration,
            DurationHours,
            MaxStudents,
            Description
        FROM courses
        WHERE School = ? AND IsActive = 1
        ORDER BY CourseName ASC
    ");
    
    $stmt->execute([strtoupper($school)]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Courses query for school '{$school}': " . $stmt->rowCount() . " results");
    
    echo json_encode([
        'success' => true,
        'courses' => $courses
    ]);
    
} catch (PDOException $e) {
    error_log('Get Courses Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'courses' => []
    ]);
}
?>