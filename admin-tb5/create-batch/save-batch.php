<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\create-batch\save-batch.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $batchId = $input['batchId'] ?? null;
    $batchName = $input['batchName'] ?? null;
    $school = $input['school'] ?? null;
    $courseId = $input['courseId'] ?? null;
    $courseCode = $input['courseCode'] ?? null;
    $courseName = $input['courseName'] ?? null;
    $startDate = $input['startDate'] ?? null;
    $endDate = $input['endDate'] ?? null;
    $description = $input['description'] ?? '';
    $editingId = $input['editingId'] ?? null;
    
    if (!$batchId || !$batchName || !$school || !$courseId || !$startDate || !$endDate) {
        throw new Exception('All required fields must be filled');
    }
    
    if (strtotime($endDate) < strtotime($startDate)) {
        throw new Exception('End date must be after start date');
    }
    
    if (!$editingId) {
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM batches WHERE BatchCode = ?");
        $checkStmt->execute([$batchId]);
        if ($checkStmt->fetchColumn() > 0) {
            throw new Exception('Batch ID already exists. Please use a different ID.');
        }
    }
    
    if (!$courseName) {
        $courseStmt = $pdo->prepare("SELECT CourseName FROM courses WHERE Id = ?");
        $courseStmt->execute([$courseId]);
        $courseData = $courseStmt->fetch(PDO::FETCH_ASSOC);
        $courseName = $courseData['CourseName'] ?? 'Unknown Course';
    }
    
    if ($editingId) {
        $stmt = $pdo->prepare("
            UPDATE batches 
            SET BatchCode = ?,
                BatchName = ?,
                CourseId = ?,
                StartDate = ?,
                EndDate = ?,
                Description = ?,
                Status = 'Active',
                IsActive = 1,
                UpdatedAt = NOW()
            WHERE Id = ?
        ");
        
        $stmt->execute([
            $batchId,
            $batchName,
            $courseId,
            $startDate,
            $endDate,
            $description,
            $editingId
        ]);
        
        echo json_encode([
            'success' => true,
            'batchId' => $editingId,
            'isUpdate' => true
        ]);
        
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO batches (
                BatchCode,
                BatchName,
                CourseId,
                StartDate,
                EndDate,
                Description,
                Status,
                MaxStudents,
                CurrentStudents,
                IsActive,
                CreatedAt
            ) VALUES (?, ?, ?, ?, ?, ?, 'Active', 30, 0, 1, NOW())
        ");
        
        $stmt->execute([
            $batchId,
            $batchName,
            $courseId,
            $startDate,
            $endDate,
            $description
        ]);
        
        $newBatchId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'batchId' => $newBatchId,
            'isUpdate' => false
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>