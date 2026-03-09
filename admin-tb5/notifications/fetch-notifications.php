<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\notifications\fetch-notifications.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Return empty array if no read notifications in session
$readNotifications = isset($_SESSION['read_notifications']) ? $_SESSION['read_notifications'] : [];

try {
    // Get notifications
    $query = "
        SELECT * FROM (
            -- Student pending registrations
            SELECT 
                CONCAT('PENDING_', Id) as notification_id,
                'student_pending' as type,
                Id as target_id,
                'Student in Queue' as title,
                CONCAT(FirstName, ' ', LastName, ' is waiting for approval') as message,
                EntryDate as created_at,
                'warning' as status,
                'hourglass-split' as icon,
                FirstName as student_first,
                LastName as student_last,
                Email as student_email,
                NULL as admin_name
            FROM studentinfos 
            WHERE Status = 'Pending'
            
            UNION ALL
            
            -- Recent student approvals (last 7 days)
            SELECT 
                CONCAT('APPROVED_', Id) as notification_id,
                'student_approved' as type,
                Id as target_id,
                'Student Approved' as title,
                CONCAT(FirstName, ' ', LastName, ' has been approved') as message,
                UpdatedAt as created_at,
                'success' as status,
                'check-circle' as icon,
                FirstName as student_first,
                LastName as student_last,
                Email as student_email,
                NULL as admin_name
            FROM studentinfos 
            WHERE Status = 'Approved' 
            AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            
            UNION ALL
            
            -- Recent student rejections (last 7 days)
            SELECT 
                CONCAT('REJECTED_', Id) as notification_id,
                'student_rejected' as type,
                Id as target_id,
                'Student Rejected' as title,
                CONCAT(FirstName, ' ', LastName, ' has been rejected') as message,
                UpdatedAt as created_at,
                'danger' as status,
                'x-circle' as icon,
                FirstName as student_first,
                LastName as student_last,
                Email as student_email,
                NULL as admin_name
            FROM studentinfos 
            WHERE Status = 'Rejected' 
            AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            
            UNION ALL
            
            -- Recent document approvals (last 7 days)
            SELECT 
                CONCAT('DOC_', da.StudentInfoId) as notification_id,
                'document_approved' as type,
                da.StudentInfoId as target_id,
                'Document Approved' as title,
                CONCAT(
                    CASE 
                        WHEN da.PSAApproved = 1 THEN 'PSA Birth Certificate'
                        WHEN da.TORApproved = 1 THEN 'TOR'
                        WHEN da.DiplomaApproved = 1 THEN 'Diploma'
                        WHEN da.MarriageApproved = 1 THEN 'Marriage Certificate'
                    END,
                    ' approved for ', s.FirstName, ' ', s.LastName
                ) as message,
                da.ApprovedDate as created_at,
                'info' as status,
                'file-earmark-check' as icon,
                s.FirstName as student_first,
                s.LastName as student_last,
                s.Email as student_email,
                da.ApprovedBy as admin_name
            FROM document_approvals da
            INNER JOIN studentinfos s ON da.StudentInfoId = s.Id
            WHERE da.ApprovedDate IS NOT NULL 
            AND da.ApprovedDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            AND (da.PSAApproved = 1 OR da.TORApproved = 1 OR da.DiplomaApproved = 1 OR da.MarriageApproved = 1)
            
            UNION ALL
            
            -- Recent admin logins (last 24 hours)
            SELECT 
                CONCAT('LOGIN_', Id) as notification_id,
                'admin_login' as type,
                Id as target_id,
                'Admin Login' as title,
                CONCAT(Username, ' logged in') as message,
                LastLogin as created_at,
                'primary' as status,
                'box-arrow-in-right' as icon,
                NULL as student_first,
                NULL as student_last,
                NULL as student_email,
                Username as admin_name
            FROM admins 
            WHERE LastLogin >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ) as combined
        ORDER BY created_at DESC
        LIMIT 10
    ";

    $stmt = $pdo->query($query);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark as read/unread
    $unreadCount = 0;
    foreach ($notifications as &$notif) {
        $notif['is_read'] = in_array($notif['notification_id'], $readNotifications);
        if (!$notif['is_read']) {
            $unreadCount++;
        }
    }

    // Check if there are new notifications since last fetch
    $lastFetch = isset($_SESSION['last_notification_fetch']) ? $_SESSION['last_notification_fetch'] : null;
    $hasNew = false;

    if ($lastFetch) {
        foreach ($notifications as $notif) {
            if (strtotime($notif['created_at']) > $lastFetch && !$notif['is_read']) {
                $hasNew = true;
                break;
            }
        }
    }

    $_SESSION['last_notification_fetch'] = time();

    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
        'has_new' => $hasNew
    ]);

} catch (Exception $e) {
    // Return error but don't crash
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'notifications' => [],
        'unread_count' => 0,
        'has_new' => false
    ]);
}
?>