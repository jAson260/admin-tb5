<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\delete-account.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

checkAdmin();

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$id = $input['id'] ?? null;
$type = $input['type'] ?? null;

if (!$id || !$type) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Prevent deleting yourself (current admin)
    if ($type === 'admin' && isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $id) {
        echo json_encode([
            'success' => false, 
            'message' => 'You cannot delete your own account'
        ]);
        exit;
    }
    
    if ($type === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM admins WHERE Id = ?");
    } else {
        $stmt = $pdo->prepare("DELETE FROM studentinfos WHERE Id = ?");
    }
    
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Account deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Account not found'
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
