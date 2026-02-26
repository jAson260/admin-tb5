<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\course-creation\toggle-course-status.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');
checkAdmin();

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!isset($data['id']) || !isset($data['isActive'])) {
        throw new Exception('Invalid request');
    }
    
    $stmt = $pdo->prepare("UPDATE courses SET IsActive = ? WHERE Id = ?");
    $stmt->execute([intval($data['isActive']), intval($data['id'])]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Status updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Toggle status error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>