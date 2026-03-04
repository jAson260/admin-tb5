<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\create-batch\get-batches-by-course.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    $courseId = $_GET['courseId'] ?? null;
    
    error_log("Get Batches by Course - Received courseId: " . var_export($courseId, true));
    
    if (!$courseId) {
        throw new Exception('Course ID is required');
    }
    
    // Query to get active batches for the selected course
    $stmt = $pdo->prepare("
        SELECT 
            b.Id, 
            b.BatchCode, 
            b.BatchName, 
            b.Status,
            b.StartDate,
            b.EndDate,
            b.MaxStudents,
            b.CurrentStudents,
            c.CourseName,
            c.CourseCode,
            c.School
        FROM batches b
        INNER JOIN courses c ON b.CourseId = c.Id
        WHERE b.CourseId = ? AND b.Status = 'Active'
        ORDER BY b.BatchName ASC
    ");
    
    $stmt->execute([$courseId]);
    $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Query executed for courseId: $courseId, Found batches: " . count($batches));
    
    if (count($batches) === 0) {
        // Check if there are any batches for this course (even inactive ones)
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN Status = 'Active' THEN 1 ELSE 0 END) as active_count
            FROM batches 
            WHERE CourseId = ?
        ");
        $checkStmt->execute([$courseId]);
        $check = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("Batch check for courseId $courseId: Total=" . $check['total'] . ", Active=" . $check['active_count']);
    }
    
    echo json_encode([
        'success' => true,
        'batches' => $batches,
        'count' => count($batches)
    ]);
    
} catch (Exception $e) {
    error_log('Get Batches Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'batches' => []
    ]);
}
?>