<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\create-batch\delete-batch.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$batchId = $data['batch_id'] ?? 0;

if (!$batchId) {
    echo json_encode(['success' => false, 'message' => 'Invalid batch ID']);
    exit;
}

try {
    // Check if batch has students
    $checkStmt = $pdo->prepare("SELECT CurrentStudents FROM batches WHERE Id = ?");
    $checkStmt->execute([$batchId]);
    $batch = $checkStmt->fetch();
    
    if ($batch && $batch['CurrentStudents'] > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete batch with enrolled students'
        ]);
        exit;
    }
    
    // Delete batch
    $stmt = $pdo->prepare("DELETE FROM batches WHERE Id = ?");
    $stmt->execute([$batchId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Batch deleted successfully'
    ]);
    
} catch (PDOException $e) {
    error_log('Delete Batch Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
?>