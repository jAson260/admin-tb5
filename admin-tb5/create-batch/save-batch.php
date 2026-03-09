<?php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$batchId     = trim($data['batchId']     ?? '');
$editingId   = intval($data['editingId'] ?? 0);
$batchName   = trim($data['batchName']   ?? '');
$school      = trim($data['school']      ?? '');
$courseId    = intval($data['courseId']  ?? 0);
$startDate   = $data['startDate']        ?? '';
$endDate     = $data['endDate']          ?? '';
$description = trim($data['description'] ?? '');
$maxStudents = isset($data['maxStudents']) && $data['maxStudents'] !== null
                ? intval($data['maxStudents'])
                : null;
$status      = in_array($data['status'] ?? '', ['Active','Pending','Completed'])
                ? $data['status']
                : 'Active';

if (!$batchName || !$school || !$courseId || !$startDate || !$endDate) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

try {
    if ($editingId) {
        // ── UPDATE ────────────────────────────────────────────────────────────
        $stmt = $pdo->prepare("
            UPDATE batches SET
                BatchName   = ?,
                School      = ?,
                CourseId    = ?,
                StartDate   = ?,
                EndDate     = ?,
                MaxStudents = ?,
                Status      = ?,
                Description = ?
            WHERE Id = ?
        ");
        $stmt->execute([
            $batchName, $school, $courseId,
            $startDate, $endDate,
            $maxStudents, $status,
            $description, $editingId
        ]);
        echo json_encode(['success' => true, 'message' => 'Batch updated.']);

    } else {
        // ── Auto-generate BatchCode if not provided ────────────────────────
        if (!$batchId) {
            $year    = date('Y');
            $random  = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            $batchId = "BATCH-{$year}-{$random}";
        }

        // ── Check duplicate BatchCode ──────────────────────────────────────
        $check = $pdo->prepare("SELECT Id FROM batches WHERE BatchCode = ?");
        $check->execute([$batchId]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Batch ID already exists.']);
            exit;
        }

        // ── INSERT ────────────────────────────────────────────────────────────
        $stmt = $pdo->prepare("
            INSERT INTO batches
                (BatchCode, BatchName, School, CourseId,
                 StartDate, EndDate, MaxStudents, Status, Description, CurrentStudents)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
        ");
        $stmt->execute([
            $batchId, $batchName, $school, $courseId,
            $startDate, $endDate,
            $maxStudents, $status,
            $description
        ]);
        echo json_encode(['success' => true, 'message' => 'Batch created.', 'batchId' => $batchId]);
    }

} catch (PDOException $e) {
    error_log('save-batch.php error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>