<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\notifications\mark-read.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Initialize read notifications array in session if not exists
if (!isset($_SESSION['read_notifications'])) {
    $_SESSION['read_notifications'] = [];
}

$response = ['success' => false, 'unread_count' => 0];

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        // If no JSON input, return success (for preflight requests)
        echo json_encode(['success' => true, 'unread_count' => 0]);
        exit;
    }

    if (isset($input['mark_all']) && $input['mark_all'] === true) {
        // Get all current notification IDs
        $query = "
            SELECT notification_id FROM (
                SELECT CONCAT('PENDING_', Id) as notification_id FROM studentinfos WHERE Status = 'Pending'
                UNION ALL
                SELECT CONCAT('APPROVED_', Id) FROM studentinfos WHERE Status = 'Approved' AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                UNION ALL
                SELECT CONCAT('REJECTED_', Id) FROM studentinfos WHERE Status = 'Rejected' AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                UNION ALL
                SELECT CONCAT('DOC_', StudentInfoId) FROM document_approvals WHERE ApprovedDate IS NOT NULL AND ApprovedDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                UNION ALL
                SELECT CONCAT('LOGIN_', Id) FROM admins WHERE LastLogin >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ) as all_notifications
        ";
        
        $stmt = $pdo->query($query);
        $allNotifications = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Mark all as read
        $_SESSION['read_notifications'] = $allNotifications;
        $response['success'] = true;
        $response['unread_count'] = 0;
        
    } elseif (isset($input['notification_id'])) {
        $notificationId = $input['notification_id'];
        
        // Add to read notifications if not already there
        if (!in_array($notificationId, $_SESSION['read_notifications'])) {
            $_SESSION['read_notifications'][] = $notificationId;
        }
        
        // Calculate new unread count
        $query = "SELECT COUNT(*) as total FROM (
            SELECT CONCAT('PENDING_', Id) as notification_id FROM studentinfos WHERE Status = 'Pending'
            UNION ALL
            SELECT CONCAT('APPROVED_', Id) FROM studentinfos WHERE Status = 'Approved' AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            UNION ALL
            SELECT CONCAT('REJECTED_', Id) FROM studentinfos WHERE Status = 'Rejected' AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            UNION ALL
            SELECT CONCAT('DOC_', StudentInfoId) FROM document_approvals WHERE ApprovedDate IS NOT NULL AND ApprovedDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            UNION ALL
            SELECT CONCAT('LOGIN_', Id) FROM admins WHERE LastLogin >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ) as all_notifications";
        
        $stmt = $pdo->query($query);
        $totalRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalNotifications = $totalRow ? $totalRow['total'] : 0;
        $unreadCount = $totalNotifications - count($_SESSION['read_notifications']);
        
        $response['success'] = true;
        $response['unread_count'] = max(0, $unreadCount);
    }

} catch (Exception $e) {
    // Log error but don't expose to client
    error_log('Notification mark-read error: ' . $e->getMessage());
    $response['success'] = false;
    $response['error'] = 'Internal server error';
}

echo json_encode($response);
?>