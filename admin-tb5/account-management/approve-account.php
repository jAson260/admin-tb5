<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\approve-account.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');

// Check admin access
checkAdmin();

try {
    // Get JSON input
    $json = file_get_contents('php://input');
    $input = json_decode($json, true);
    
    // Log for debugging
    error_log("Approve Account Input: " . print_r($input, true));
    
    if (!isset($input['id'])) {
        throw new Exception('Account ID is required');
    }
    
    $accountId = intval($input['id']);
    
    if ($accountId <= 0) {
        throw new Exception('Invalid account ID');
    }
    
    // Update status to Approved
    $stmt = $pdo->prepare("UPDATE studentinfos SET Status = 'Approved' WHERE Id = ?");
    
    if ($stmt->execute([$accountId])) {
        // Check if any row was affected
        if ($stmt->rowCount() === 0) {
            throw new Exception('Account not found or already approved');
        }
        
        // Log the action (optional - if you have activity_logs table)
        try {
            $adminId = $_SESSION['user_id'] ?? 0;
            $logStmt = $pdo->prepare("
                INSERT INTO activity_logs (AdminId, Action, TargetTable, TargetId, Details) 
                VALUES (?, 'APPROVE_ACCOUNT', 'studentinfos', ?, 'Account approved by admin')
            ");
            $logStmt->execute([$adminId, $accountId]);
        } catch (Exception $logError) {
            // Log error but don't fail the approval
            error_log("Failed to log activity: " . $logError->getMessage());
        }
        
        echo json_encode([
            'success' => true
        ]);
    } else {
        throw new Exception('Failed to update account status');
    }
    
} catch (Exception $e) {
    error_log("Approve Account Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>