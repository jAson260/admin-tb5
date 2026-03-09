<?php
session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    $raw      = file_get_contents('php://input');
    $input    = json_decode($raw, true);
    $id       = (int)($input['id']       ?? 0);
    $isActive = (int)($input['isActive'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid subject ID.']);
        exit;
    }

    if (!in_array($isActive, [0, 1])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE subjects
        SET IsActive  = :isActive,
            UpdatedAt = NOW()
        WHERE Id = :id
    ");
    $stmt->execute([':isActive' => $isActive, ':id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Subject not found.']);
    }

} catch (PDOException $e) {
    error_log("toggle-subject-status.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}