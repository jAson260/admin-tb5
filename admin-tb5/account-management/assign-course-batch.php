<?php

session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
    exit;
}

$students = $data['students'] ?? [];
$school   = trim($data['school']   ?? '');
$courseId = intval($data['courseId'] ?? 0);
$batchId  = intval($data['batchId']  ?? 0);

if (empty($students)) {
    echo json_encode(['success' => false, 'message' => 'No students selected']);
    exit;
}

if (!$school || !$courseId || !$batchId) {
    echo json_encode(['success' => false, 'message' => 'School, course, and batch are required']);
    exit;
}

try {
    // Verify batch exists and belongs to the course
    $batchCheck = $pdo->prepare("
        SELECT b.Id, b.MaxStudents, b.CurrentStudents, b.BatchCode, b.BatchName,
               c.CourseName, c.School as CourseSchool
        FROM batches b
        INNER JOIN courses c ON b.CourseId = c.Id
        WHERE b.Id = ? AND b.CourseId = ? AND b.IsActive = 1
    ");
    $batchCheck->execute([$batchId, $courseId]);
    $batch = $batchCheck->fetch(PDO::FETCH_ASSOC);

    if (!$batch) {
        echo json_encode(['success' => false, 'message' => 'Invalid batch selected']);
        exit;
    }

    // Check if batch has available slots
    if ($batch['MaxStudents'] > 0) {
        $availableSlots = $batch['MaxStudents'] - ($batch['CurrentStudents'] ?? 0);
        if (count($students) > $availableSlots) {
            echo json_encode([
                'success' => false,
                'message' => "Not enough slots. Only {$availableSlots} slot(s) available in this batch."
            ]);
            exit;
        }
    }

    $assigned = 0;
    $skipped  = 0;
    $errors   = [];

    $pdo->beginTransaction();

    foreach ($students as $studentId) {
        $studentId = intval($studentId);
        if (!$studentId) continue;

        // FIXED: Table is studentinfos not students
        $studentCheck = $pdo->prepare("
            SELECT Id, FirstName, LastName 
            FROM studentinfos 
            WHERE Id = ? AND Status = 'Approved'
        ");
        $studentCheck->execute([$studentId]);
        $student = $studentCheck->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            $skipped++;
            $errors[] = "Student ID {$studentId} not found or not approved";
            continue;
        }

        // Check if already assigned to this batch
        $existingCheck = $pdo->prepare("
            SELECT Id FROM enrollments 
            WHERE StudentId = ? AND BatchId = ? AND CourseId = ?
        ");
        $existingCheck->execute([$studentId, $batchId, $courseId]);
        if ($existingCheck->fetch()) {
            $skipped++;
            $errors[] = "{$student['FirstName']} {$student['LastName']} is already enrolled in this batch";
            continue;
        }

        // Insert enrollment
        $insertStmt = $pdo->prepare("
            INSERT INTO enrollments (StudentId, CourseId, BatchId, School, Status, EnrolledAt)
            VALUES (?, ?, ?, ?, 'Enrolled', NOW())
        ");
        $insertStmt->execute([$studentId, $courseId, $batchId, $school]);

        // Update batch current students count
        $updateBatch = $pdo->prepare("
            UPDATE batches 
            SET CurrentStudents = CurrentStudents + 1 
            WHERE Id = ?
        ");
        $updateBatch->execute([$batchId]);

        $assigned++;
    }

    $pdo->commit();

    $message = "{$assigned} student(s) assigned successfully.";
    if ($skipped > 0) {
        $message .= " {$skipped} skipped (already enrolled or not approved).";
    }

    echo json_encode([
        'success'  => true,
        'message'  => $message,
        'assigned' => $assigned,
        'skipped'  => $skipped,
        'errors'   => $errors
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Assign Course Batch Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>