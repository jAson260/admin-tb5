<?php
session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();
require_once('../../db-connect.php');

header('Content-Type: application/json');

try {
    $raw   = file_get_contents('php://input');
    $input = json_decode($raw, true);
    $id    = (int)($input['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid subject ID.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM subjects WHERE Id = :id");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Subject deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Subject not found or already deleted.']);
    }

} catch (PDOException $e) {
    error_log("delete-subject.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}