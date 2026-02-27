<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\create-batch\get-batch-details.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$batchId = $_GET['batch_id'] ?? 0;

try {
    $stmt = $pdo->prepare("
        SELECT 
            b.*,
            c.CourseName,
            c.CourseCode,
            c.Duration,
            c.DurationHours
        FROM batches b
        LEFT JOIN courses c ON b.CourseId = c.Id
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
    error_log('Get Batch Details Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
?>