<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\get-account-details.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

checkAdmin();

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null;

if (!$id || !$type) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters - ID and Type required']);
    exit;
}

try {
    if ($type === 'admin') {
        $stmt = $pdo->prepare("
            SELECT 
                Id,
                CONCAT(FirstName, ' ', LastName) as FullName,
                FirstName,
                LastName,
                Email,
                Role,
                Status,
                LastLogin,
                CreatedAt,
                UpdatedAt
            FROM admins WHERE Id = ?
        ");
    } else {
        $stmt = $pdo->prepare("
            SELECT 
                Id,
                ULI,
                CONCAT(FirstName, ' ', LastName) as FullName,
                FirstName,
                LastName,
                Email,
                Role,
                Status,
                LastLogin,
                EntryDate as CreatedAt,
                UpdatedAt,
                ContactNo,
                BirthDate,
                Sex,
                CivilStatus,
                Address,
                Province,
                City,
                Barangay,
                ZipCode
            FROM studentinfos WHERE Id = ?
        ");
    }
    
    $stmt->execute([$id]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($account) {
        echo json_encode([
            'success' => true, 
            'account' => $account
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