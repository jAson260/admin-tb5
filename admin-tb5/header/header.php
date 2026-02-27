<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\header\header.php
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
            min-width: 350px;
            max-width: 400px;
            max-height: 500px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
        }
        
        .mark-all-read:hover {
            text-decoration: underline;
        }
        
        .notification-item {
            padding: 12px 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
            cursor: pointer;
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
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .notification-icon.info {
            background: #cfe2ff;
            color: #0d6efd;
        }
        
        .notification-icon.success {
            background: #d1e7dd;
            color: #198754;
        }
        
        .notification-icon.warning {
            background: #fff3cd;
            color: #ffc107;
        }
        
        .notification-icon.danger {
            background: #f8d7da;
            color: #dc3545;
        }
        
        .notification-content {
            flex: 1;
            min-width: 0;
        }
        
        .notification-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 2px;
            color: #333;
        }
        
        .notification-text {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 4px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .notification-time {
            font-size: 0.75rem;
            color: #adb5bd;
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
        
        /* Custom scrollbar for notification dropdown */
        .notification-dropdown .dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }
        
        .notification-dropdown .dropdown-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .notification-dropdown .dropdown-menu::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        .notification-dropdown .dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #555;
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
                <div class="header-icon" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell-fill"></i>
                    <span class="notification-badge" id="notificationCount">3</span>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <!-- Notification Header -->
                    <div class="notification-header">
                        <h6>Notifications</h6>
                        <a href="#" class="mark-all-read" onclick="markAllAsRead(event)">Mark all as read</a>
                    </div>
                    
                    <!-- Notification List -->
                    <div id="notificationList">
                        <!-- Sample notifications - Replace with dynamic content -->
                        <div class="notification-item unread d-flex gap-3" onclick="markAsRead(this, 1)">
                            <div class="notification-icon success">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">New Student Registered</div>
                                <div class="notification-text">John Doe has registered for Basic Safety Training</div>
                                <div class="notification-time">2 minutes ago</div>
                            </div>
                        </div>
                        
                        <div class="notification-item unread d-flex gap-3" onclick="markAsRead(this, 2)">
                            <div class="notification-icon info">
                                <i class="bi bi-info-circle-fill"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Course Update</div>
                                <div class="notification-text">Maritime Safety course materials have been updated</div>
                                <div class="notification-time">1 hour ago</div>
                            </div>
                        </div>
                        
                        <div class="notification-item unread d-flex gap-3" onclick="markAsRead(this, 3)">
                            <div class="notification-icon warning">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Pending Approval</div>
                                <div class="notification-text">3 students are waiting for account approval</div>
                                <div class="notification-time">3 hours ago</div>
                            </div>
                        </div>
                        
                        <div class="notification-item d-flex gap-3" onclick="markAsRead(this, 4)">
                            <div class="notification-icon danger">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Certificate Expiring</div>
                                <div class="notification-text">5 certificates will expire in 30 days</div>
                                <div class="notification-time">1 day ago</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification Footer -->
                    <div class="notification-footer">
                        <a href="../notifications/notifications.php">View all notifications</a>
                    </div>
                </ul>
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
        // Mark individual notification as read
        function markAsRead(element, notificationId) {
            if (element.classList.contains('unread')) {
                element.classList.remove('unread');
                updateNotificationCount();
                
                // TODO: Send AJAX request to mark notification as read in database
                // Example:
                // fetch('../api/mark-notification-read.php', {
                //     method: 'POST',
                //     headers: { 'Content-Type': 'application/json' },
                //     body: JSON.stringify({ id: notificationId })
                // });
            }
        }
        
        // Mark all notifications as read
        function markAllAsRead(event) {
            event.preventDefault();
            const unreadNotifications = document.querySelectorAll('.notification-item.unread');
            unreadNotifications.forEach(item => {
                item.classList.remove('unread');
            });
            updateNotificationCount();
            
            // TODO: Send AJAX request to mark all as read
            // fetch('../api/mark-all-notifications-read.php', { method: 'POST' });
        }
        
        // Update notification count badge
        function updateNotificationCount() {
            const unreadCount = document.querySelectorAll('.notification-item.unread').length;
            const badge = document.getElementById('notificationCount');
            
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
        
        // Initialize notification count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationCount();
        });
        
        // Optional: Auto-refresh notifications every 30 seconds
        // setInterval(function() {
        //     fetch('../api/get-notifications.php')
        //         .then(response => response.json())
        //         .then(data => {
        //             // Update notification list
        //             updateNotificationList(data);
        //             updateNotificationCount();
        //         });
        // }, 30000);
    </script>
</body>
</html>