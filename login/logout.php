<?php
// filepath: c:\laragon\www\admin-tb5\login\logout.php
session_start();

// Store user info before destroying session (for redirect logic and logging)
$userType = $_SESSION['user_type'] ?? 'student';
$userId = $_SESSION['user_id'] ?? null;
$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['user_role'] ?? null;

// Optional: Log logout activity in database
if ($userId) {
    try {
        require_once('../db-connect.php');
        
        if ($userType === 'admin' || $userType === 'superadmin' || $userType === 'staff') {
            // Log admin logout with role information
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (UserId, UserType, Action, ActionDetails, CreatedAt) 
                VALUES (?, ?, 'logout', ?, NOW())
            ");
            $stmt->execute([
                $userId, 
                $userType,
                "Admin ({$userName} - {$userRole}) logged out"
            ]);
        } else {
            // Log student logout
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (UserId, UserType, Action, ActionDetails, CreatedAt) 
                VALUES (?, 'student', 'logout', ?, NOW())
            ");
            $stmt->execute([
                $userId,
                "Student ({$userName}) logged out"
            ]);
        }
    } catch(PDOException $e) {
        // Silently fail - logout should still proceed even if logging fails
        error_log("Logout logging failed: " . $e->getMessage());
    }
}

// Clear all session variables (including admin-specific ones)
$_SESSION = array();

// Explicitly unset admin-specific session variables
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);
unset($_SESSION['user_type']);
unset($_SESSION['user_role']);
unset($_SESSION['logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_role']);
unset($_SESSION['admin_permissions']);
unset($_SESSION['last_activity']);

// Delete session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Set success message for login page
session_start();
$_SESSION['logout_success'] = "Goodbye, $userName! You have been successfully logged out.";
$_SESSION['logout_user_type'] = $userType;
session_write_close();

// Redirect to login page (FIXED PATH)
header('Location: /login/login');
exit;
?>