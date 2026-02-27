<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\create-batch\get-batch-statistics.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    // Total batches
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM batches");
    $totalBatches = $totalStmt->fetch()['total'] ?? 0;
    
    // Active batches
    $activeStmt = $pdo->query("SELECT COUNT(*) as total FROM batches WHERE Status = 'Active'");
    $activeBatches = $activeStmt->fetch()['total'] ?? 0;
    
    // Total students
    $studentsStmt = $pdo->query("SELECT SUM(CurrentStudents) as total FROM batches");
    $totalStudents = $studentsStmt->fetch()['total'] ?? 0;
    
    // Completed batches
    $completedStmt = $pdo->query("SELECT COUNT(*) as total FROM batches WHERE Status = 'Completed'");
    $completedBatches = $completedStmt->fetch()['total'] ?? 0;
    
    echo json_encode([
        'success' => true,
        'statistics' => [
            'total' => $totalBatches,
            'active' => $activeBatches,
            'students' => $totalStudents,
            'completed' => $completedBatches
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Get Statistics Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error loading statistics'
    ]);
}
?>