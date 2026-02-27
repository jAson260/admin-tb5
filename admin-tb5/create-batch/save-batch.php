<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\create-batch\save-batch.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$batchCode = $data['batch_code'] ?? '';
$batchName = $data['batch_name'] ?? '';
$school = $data['school'] ?? '';
$courseId = $data['course_id'] ?? 0;
$startDate = $data['start_date'] ?? '';
$endDate = $data['end_date'] ?? '';
$description = $data['description'] ?? '';
$maxStudents = $data['max_students'] ?? 30;

// Validation
if (empty($batchCode) || empty($batchName) || empty($school) || empty($courseId) || empty($startDate) || empty($endDate)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

try {
    // Check if batch code already exists
    $checkStmt = $pdo->prepare("SELECT Id FROM batches WHERE BatchCode = ?");
    $checkStmt->execute([$batchCode]);
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Batch code already exists']);
        exit;
    }
    
    // Insert batch
    $stmt = $pdo->prepare("
        INSERT INTO batches 
        (BatchCode, BatchName, School, CourseId, StartDate, EndDate, Description, MaxStudents, Status, CreatedBy) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)
    ");
    
    $stmt->execute([
        $batchCode,
        $batchName,
        strtoupper($school),
        $courseId,
        $startDate,
        $endDate,
        $description,
        $maxStudents,
        $_SESSION['admin_id'] ?? null
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Batch created successfully',
        'batch_id' => $pdo->lastInsertId()
    ]);
    
} catch (PDOException $e) {
    error_log('Save Batch Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>