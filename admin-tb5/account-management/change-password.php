<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\change-password.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

checkAdmin();

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$id = $input['id'] ?? null;
$type = $input['type'] ?? null;
$password = $input['password'] ?? null;

if (!$id || !$type || !$password) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
    exit;
}

try {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    if ($type === 'admin') {
        $stmt = $pdo->prepare("UPDATE admins SET Password = ?, UpdatedAt = NOW() WHERE Id = ?");
    } else {
        $stmt = $pdo->prepare("UPDATE studentinfos SET Password = ?, UpdatedAt = NOW() WHERE Id = ?");
    }
    
    $stmt->execute([$hashedPassword, $id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true, 
            'message' => 'Password changed successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Account not found or password unchanged'
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
