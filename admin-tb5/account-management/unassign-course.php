<?php

session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id    = intval($input['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid student ID.']);
    exit;
}

require_once('../../includes/db.php');

try {
    // Delete from enrollments table (the actual enrollment record)
    $stmt = $pdo->prepare("
        DELETE FROM enrollments
        WHERE StudentId = ?
        AND Status NOT IN ('Completed', 'Dropped', 'Failed')
    ");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'No active enrollment found for this student.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Student unassigned successfully.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}