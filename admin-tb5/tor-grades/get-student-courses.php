<?php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$studentId = $_GET['student_id'] ?? 0;

try {
    // Just get all available courses since admin will select manually
    $stmt = $pdo->query("
        SELECT 
            Id as id, 
            CourseName as name,
            CourseCode as code
        FROM courses 
        WHERE IsActive = 1
        ORDER BY CourseName
    ");
    
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log('All courses loaded: ' . count($courses));
    echo json_encode($courses);
    
} catch (PDOException $e) {
    error_log('Get Courses Error: ' . $e->getMessage());
    echo json_encode([]);
}
?>