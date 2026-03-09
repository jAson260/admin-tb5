<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\header\header.php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection for notifications
require_once('../../db-connect.php');

// Function to get unread notification count
function getUnreadNotificationCount($pdo) {
    // You can store read notifications in session or database
    // For now, we'll use a simple session-based approach
    $readNotifications = isset($_SESSION['read_notifications']) ? $_SESSION['read_notifications'] : [];
    
    // Get recent logs (last 7 days) that qualify as notifications
    $query = "
        SELECT COUNT(*) as total FROM (
            -- Student pending registrations
            SELECT 
                CONCAT('PENDING_', Id) as notification_id,
                'pending' as type
            FROM studentinfos 
            WHERE Status = 'Pending'
            
            UNION ALL
            
            -- Recent student approvals (last 7 days)
            SELECT 
                CONCAT('APPROVED_', Id) as notification_id,
                'approved' as type
            FROM studentinfos 
            WHERE Status = 'Approved' 
            AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            
            UNION ALL
            
            -- Recent student rejections (last 7 days)
            SELECT 
                CONCAT('REJECTED_', Id) as notification_id,
                'rejected' as type
            FROM studentinfos 
            WHERE Status = 'Rejected' 
            AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            
            UNION ALL
            
            -- Recent document approvals (last 7 days)
            SELECT 
                CONCAT('DOC_', StudentInfoId) as notification_id,
                'document' as type
            FROM document_approvals 
            WHERE ApprovedDate IS NOT NULL 
            AND ApprovedDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            
            UNION ALL
            
            -- Recent admin logins (last 24 hours)
            SELECT 
                CONCAT('LOGIN_', Id) as notification_id,
                'login' as type
            FROM admins 
            WHERE LastLogin >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ) as notifications
    ";
    
    $stmt = $pdo->query($query);
    $totalNotifications = $stmt->fetch()['total'];
    
    // Subtract read notifications
    $unreadCount = $totalNotifications - count($readNotifications);
    return max(0, $unreadCount);
}

// Function to get notifications
function getNotifications($pdo, $limit = 10) {
    $readNotifications = isset($_SESSION['read_notifications']) ? $_SESSION['read_notifications'] : [];
    
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
                FirstName as student_first,
                LastName as student_last,
                Email as student_email,
                NULL as admin_name
            FROM studentinfos 
            WHERE Status = 'Pending'
            
            UNION ALL
            
            -- Recent student approvals
            SELECT 
                CONCAT('APPROVED_', Id) as notification_id,
                'student_approved' as type,
                Id as target_id,
                'Student Approved' as title,
                CONCAT(FirstName, ' ', LastName, ' has been approved') as message,
                UpdatedAt as created_at,
                'success' as status,
                FirstName as student_first,
                LastName as student_last,
                Email as student_email,
                NULL as admin_name
            FROM studentinfos 
            WHERE Status = 'Approved' 
            AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            
            UNION ALL
            
            -- Recent student rejections
            SELECT 
                CONCAT('REJECTED_', Id) as notification_id,
                'student_rejected' as type,
                Id as target_id,
                'Student Rejected' as title,
                CONCAT(FirstName, ' ', LastName, ' has been rejected') as message,
                UpdatedAt as created_at,
                'danger' as status,
                FirstName as student_first,
                LastName as student_last,
                Email as student_email,
                NULL as admin_name
            FROM studentinfos 
            WHERE Status = 'Rejected' 
            AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            
            UNION ALL
            
            -- Recent document approvals
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
            
            -- Recent admin logins
            SELECT 
                CONCAT('LOGIN_', Id) as notification_id,
                'admin_login' as type,
                Id as target_id,
                'Admin Login' as title,
                CONCAT(Username, ' logged in') as message,
                LastLogin as created_at,
                'primary' as status,
                NULL as student_first,
                NULL as student_last,
                NULL as student_email,
                Username as admin_name
            FROM admins 
            WHERE LastLogin >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ) as combined
        ORDER BY created_at DESC
        LIMIT $limit
    ";
    
    $stmt = $pdo->query($query);
    $notifications = $stmt->fetchAll();
    
    // Mark as read/unread
    foreach ($notifications as &$notif) {
        $notif['is_read'] = in_array($notif['notification_id'], $readNotifications);
    }
    
    return $notifications;
}

// Get notifications for display
$notifications = getNotifications($pdo);
$unreadCount = getUnreadNotificationCount($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Big Five Training and Assessment Center Inc.</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon.ico">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <style>
        :root {
            --primary-blue: #4169E1;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        
        .header {
            background: linear-gradient(135deg, #4169E1 0%, #5B7FEB 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 70px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }
        
        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .header-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .header-icon {
            color: white;
            font-size: 1.3rem;
            cursor: pointer;
            transition: transform 0.2s;
            position: relative;
        }
        
        .header-icon:hover {
            transform: scale(1.1);
        }
        
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
            border: 2px solid white;
        }
        
        .notification-dropdown .dropdown-menu {
            width: 380px;
            max-height: 500px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 0;
        }
        
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        
        .notification-header h6 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }
        
        .mark-all-read {
            font-size: 0.85rem;
            color: var(--primary-blue);
            cursor: pointer;
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
        }
        
        .mark-all-read:hover {
            text-decoration: underline;
        }
        
        .mark-all-read:disabled {
            color: #6c757d;
            cursor: not-allowed;
            text-decoration: none;
        }
        
        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            padding: 12px 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
            cursor: pointer;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        
        .notification-item:hover {
            background: #f8f9fa;
        }
        
        .notification-item.unread {
            background: #e7f3ff;
        }
        
        .notification-item.unread:hover {
            background: #d0e7ff;
        }
        
        .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        
        .notification-icon.warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .notification-icon.success {
            background: #d4edda;
            color: #155724;
        }
        
        .notification-icon.danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .notification-icon.info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .notification-icon.primary {
            background: #cfe2ff;
            color: #084298;
        }
        
        .notification-content {
            flex: 1;
            min-width: 0;
        }
        
        .notification-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 4px;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-title .time {
            font-size: 0.7rem;
            color: #6c757d;
            font-weight: normal;
        }
        
        .notification-text {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .notification-footer {
            padding: 10px 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
            position: sticky;
            bottom: 0;
        }
        
        .notification-footer a {
            font-size: 0.85rem;
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
        }
        
        .notification-footer a:hover {
            text-decoration: underline;
        }
        
        .no-notifications {
            padding: 40px 20px;
            text-align: center;
            color: #6c757d;
        }
        
        .no-notifications i {
            font-size: 3rem;
            opacity: 0.3;
            margin-bottom: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--primary-blue);
            font-size: 1.2rem;
        }
        
        .user-dropdown .dropdown-toggle::after,
        .notification-dropdown .dropdown-toggle::after {
            display: none;
        }
        
        .user-dropdown .dropdown-menu {
            min-width: 180px;
        }
        
        .content-wrapper {
            margin-top: 70px;
            margin-left: var(--sidebar-width);
            min-height: calc(100vh - 70px);
        }
        
        .main-content {
            padding: 30px;
        }
        
        /* Custom scrollbar — all scrollable areas */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f4ff;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #4169E1;
            border-radius: 10px;
            transition: background 0.2s;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #2e51b8;
        }

        ::-webkit-scrollbar-corner {
            background: #f1f4ff;
        }

        /* Firefox scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: #4169E1 #f1f4ff;
        }

        /* Notification list specific scrollbar (override old one) */
        .notification-list::-webkit-scrollbar {
            width: 6px;
        }

        .notification-list::-webkit-scrollbar-track {
            background: #f1f4ff;
            border-radius: 10px;
        }

        .notification-list::-webkit-scrollbar-thumb {
            background: #4169E1;
            border-radius: 10px;
        }

        .notification-list::-webkit-scrollbar-thumb:hover {
            background: #2e51b8;
        }
    </style>

    <!-- jQuery (MUST be loaded first) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS (after jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS (after jQuery) -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <a href="../admin-dashboard/admin-dashboard.php" class="logo">
                <img src="../assets/img/tb5-logo.png" alt="The Big Five Training and Assessment Center Logo">
            </a>
            <a href="../admin-dashboard/admin-dashboard.php" class="logo">
                <img src="../assets/img/bbi-logo.png" alt="Big Blossom Institute Inc. Logo">
            </a>
            <a href="../admin-dashboard/admin-dashboard.php" style="text-decoration: none; color: white;">
                <h1 class="header-title">The Big Five Training and Assessment Center Inc.</h1>
            </a>
        </div>
        <div class="header-right">
            <!-- Notification Dropdown -->
            <div class="dropdown notification-dropdown">
                <div class="header-icon" data-bs-toggle="dropdown" aria-expanded="false" id="notificationBell">
                    <i class="bi bi-bell-fill"></i>
                    <span class="notification-badge" id="notificationCount" <?php echo $unreadCount > 0 ? '' : 'style="display:none;"'; ?>><?php echo $unreadCount; ?></span>
                </div>
                <div class="dropdown-menu dropdown-menu-end" id="notificationDropdown">
                    <!-- Notification Header -->
                    <div class="notification-header">
                        <h6>Notifications</h6>
                        <button class="mark-all-read" onclick="markAllAsRead(event)" <?php echo $unreadCount > 0 ? '' : 'disabled'; ?> id="markAllReadBtn">
                            Mark all as read
                        </button>
                    </div>
                    
                    <!-- Notification List -->
                    <div class="notification-list" id="notificationList">
                        <?php if (count($notifications) > 0): ?>
                            <?php foreach($notifications as $notif): 
                                $icon = 'bell';
                                $statusClass = $notif['status'];
                                
                                switch($notif['type']) {
                                    case 'student_pending':
                                        $icon = 'hourglass-split';
                                        break;
                                    case 'student_approved':
                                        $icon = 'check-circle';
                                        break;
                                    case 'student_rejected':
                                        $icon = 'x-circle';
                                        break;
                                    case 'document_approved':
                                        $icon = 'file-earmark-check';
                                        break;
                                    case 'admin_login':
                                        $icon = 'box-arrow-in-right';
                                        break;
                                }
                                
                                $timeAgo = '';
                                $created = new DateTime($notif['created_at']);
                                $now = new DateTime();
                                $diff = $now->diff($created);
                                
                                if ($diff->d > 0) {
                                    $timeAgo = $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
                                } elseif ($diff->h > 0) {
                                    $timeAgo = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                                } elseif ($diff->i > 0) {
                                    $timeAgo = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
                                } else {
                                    $timeAgo = 'just now';
                                }
                            ?>
                            <div class="notification-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>" 
                                 data-notification-id="<?php echo $notif['notification_id']; ?>"
                                 onclick="markAsRead('<?php echo $notif['notification_id']; ?>', this)">
                                <div class="notification-icon <?php echo $statusClass; ?>">
                                    <i class="bi bi-<?php echo $icon; ?>"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">
                                        <?php echo htmlspecialchars($notif['title']); ?>
                                        <span class="time"><?php echo $timeAgo; ?></span>
                                    </div>
                                    <div class="notification-text">
                                        <?php echo htmlspecialchars($notif['message']); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-notifications">
                                <i class="bi bi-bell-slash"></i>
                                <p>No notifications</p>
                                <small class="text-muted">You're all caught up!</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Notification Footer -->
                    <div class="notification-footer">
                        <a href="../logs/logs.php">View all activity logs</a>
                    </div>
                </div>
            </div>
            
            <!-- User Dropdown -->
            <div class="dropdown user-dropdown">
                <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-fill"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="/admin-tb5/profile">
                            <i class="bi bi-person me-2"></i>Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="/login/logout">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <script>
        // Mark notification as read
        function markAsRead(notificationId, element) {
            if (!element.classList.contains('unread')) {
                return;
            }
            
            // Send AJAX request to mark as read
            fetch('../notifications/mark-read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    notification_id: notificationId,
                    mark_single: true 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    element.classList.remove('unread');
                    updateNotificationCount();
                    
                    // Disable mark all button if no unread
                    if (data.unread_count === 0) {
                        document.getElementById('markAllReadBtn').disabled = true;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Mark all notifications as read
        function markAllAsRead(event) {
            event.preventDefault();
            
            // Send AJAX request to mark all as read
            fetch('../notifications/mark-read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ mark_all: true })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove unread class from all notifications
                    document.querySelectorAll('.notification-item.unread').forEach(item => {
                        item.classList.remove('unread');
                    });
                    
                    // Hide notification badge
                    document.getElementById('notificationCount').style.display = 'none';
                    
                    // Disable mark all button
                    document.getElementById('markAllReadBtn').disabled = true;
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Update notification count badge
        function updateNotificationCount() {
            const unreadCount = document.querySelectorAll('.notification-item.unread').length;
            const badge = document.getElementById('notificationCount');
            const markAllBtn = document.getElementById('markAllReadBtn');
            
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.style.display = 'flex';
                markAllBtn.disabled = false;
            } else {
                badge.style.display = 'none';
                markAllBtn.disabled = true;
            }
        }
        
        // Fetch new notifications
        function fetchNewNotifications() {
            fetch('/admin-tb5/notifications/fetch-notifications.php')
                .then(response => response.json())
                .then(data => {
                    if (data.has_new) {
                        updateNotificationList(data.notifications);
                        updateNotificationCount();
                        
                        // Show browser notification if supported and page is not visible
                        if (!document.hasFocus() && 'Notification' in window && Notification.permission === 'granted') {
                            new Notification('New Notification', {
                                body: 'You have new notifications',
                                icon: '../assets/img/favicon.ico'
                            });
                        }
                    }
                    
                    // Update badge count
                    if (data.unread_count > 0) {
                        document.getElementById('notificationCount').textContent = data.unread_count;
                        document.getElementById('notificationCount').style.display = 'flex';
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }
        
        // Update notification list HTML
        function updateNotificationList(notifications) {
            const list = document.getElementById('notificationList');
            
            if (notifications.length === 0) {
                list.innerHTML = `
                    <div class="no-notifications">
                        <i class="bi bi-bell-slash"></i>
                        <p>No notifications</p>
                        <small class="text-muted">You're all caught up!</small>
                    </div>
                `;
                return;
            }
            
            let html = '';
            notifications.forEach(notif => {
                const isUnread = notif.is_read ? '' : 'unread';
                const timeAgo = getTimeAgo(notif.created_at);
                
                html += `
                    <div class="notification-item ${isUnread}" 
                         data-notification-id="${notif.notification_id}"
                         onclick="markAsRead('${notif.notification_id}', this)">
                        <div class="notification-icon ${notif.status}">
                            <i class="bi bi-${notif.icon}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">
                                ${escapeHtml(notif.title)}
                                <span class="time">${timeAgo}</span>
                            </div>
                            <div class="notification-text">
                                ${escapeHtml(notif.message)}
                            </div>
                        </div>
                    </div>
                `;
            });
            
            list.innerHTML = html;
        }
        
        // Helper function to get time ago string
        function getTimeAgo(datetime) {
            const now = new Date();
            const created = new Date(datetime);
            const diff = Math.floor((now - created) / 1000); // seconds
            
            if (diff < 60) return 'just now';
            if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
            if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
            return Math.floor(diff / 86400) + ' days ago';
        }
        
        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Request notification permission on page load
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationCount();
        });
        
        // Auto-refresh notifications every 30 seconds
        let refreshInterval = setInterval(fetchNewNotifications, 30000);
        
        // Clear interval when navigating away
        window.addEventListener('beforeunload', function() {
            clearInterval(refreshInterval);
        });
        
        // Refresh notifications when dropdown is opened
        document.getElementById('notificationBell').addEventListener('click', function() {
            fetchNewNotifications();
        });
    </script>
</body>
</html>