<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\reject-account.php
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
    error_log("Reject Account Input: " . print_r($input, true));
    
    if (!isset($input['id'])) {
        throw new Exception('Account ID is required');
    }
    
    if (!isset($input['reason']) || trim($input['reason']) === '') {
        throw new Exception('Rejection reason is required');
    }
    
    $accountId = intval($input['id']);
    $reason = trim($input['reason']);
    
    if ($accountId <= 0) {
        throw new Exception('Invalid account ID');
    }
    
    // Update status to Rejected
    $stmt = $pdo->prepare("UPDATE studentinfos SET Status = 'Rejected' WHERE Id = ?");
    
    if ($stmt->execute([$accountId])) {
        // Check if any row was affected
        if ($stmt->rowCount() === 0) {
            throw new Exception('Account not found');
        }
        
        // Log the action with reason (optional - if you have activity_logs table)
        try {
            $adminId = $_SESSION['user_id'] ?? 0;
            $details = "Account rejected by admin. Reason: " . $reason;
            
            $logStmt = $pdo->prepare("
                INSERT INTO activity_logs (AdminId, Action, TargetTable, TargetId, Details) 
                VALUES (?, 'REJECT_ACCOUNT', 'studentinfos', ?, ?)
            ");
            $logStmt->execute([$adminId, $accountId, $details]);
        } catch (Exception $logError) {
            // Log error but don't fail the rejection
            error_log("Failed to log activity: " . $logError->getMessage());
        }
        
        // TODO: Send email notification to student with rejection reason
        
        echo json_encode([
            'success' => true
        ]);
    } else {
        throw new Exception('Failed to update account status');
    }
    
} catch (Exception $e) {
    error_log("Reject Account Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>