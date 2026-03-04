<?php

session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

// Accept both 'id' and 'batch_id' for compatibility
$batchId = $_GET['id'] ?? $_GET['batch_id'] ?? 0;

try {
    $stmt = $pdo->prepare("
        SELECT 
            b.Id,
            b.BatchCode,
            b.BatchName,
            b.School,
            b.CourseId,
            b.StartDate,
            b.EndDate,
            b.Status,
            b.Description,
            b.CurrentStudents,
            b.MaxStudents,
            courses.CourseName,
            courses.CourseCode,
            courses.Duration,
            courses.DurationHours
        FROM batches b
        LEFT JOIN courses ON b.CourseId = courses.Id
        WHERE b.Id = ?
    ");
    
    $stmt->execute([$batchId]);
    $batch = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($batch) {
        echo json_encode([
            'success' => true,
            'batch' => $batch
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Batch not found'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
?>