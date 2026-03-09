<?php

session_start();

// Store user info before destroying session
$userType = $_SESSION['user_type'] ?? 'student';
$userId   = $_SESSION['user_id']   ?? null;
$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['user_role'] ?? null;

// ── Log logout activity ───────────────────────────────────────────────────────
if ($userId) {
    try {
        require_once('../db-connect.php');

        if (in_array($userType, ['admin', 'superadmin', 'staff'])) {
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
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (UserId, UserType, Action, ActionDetails, CreatedAt)
                VALUES (?, 'student', 'logout', ?, NOW())
            ");
            $stmt->execute([
                $userId,
                "Student ({$userName}) logged out"
            ]);
        }
    } catch (PDOException $e) {
        error_log("Logout logging failed: " . $e->getMessage());
    }
}

// ── Wipe all session variables ────────────────────────────────────────────────
$_SESSION = [];
session_unset();

// ── Delete session cookie ─────────────────────────────────────────────────────
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// ── Destroy session ───────────────────────────────────────────────────────────
session_destroy();

// ── No-cache headers — prevent browser restoring cached pages ─────────────────
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');

// ── Start a fresh session just for the logout success message ─────────────────
session_start();
$_SESSION['logout_success']   = "Goodbye, {$userName}! You have been successfully logged out.";
$_SESSION['logout_user_type'] = $userType;
session_write_close();

// ── Redirect to login ─────────────────────────────────────────────────────────
header('Location: /login/login');
exit;
?>