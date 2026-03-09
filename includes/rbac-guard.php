<?php


// ── No-cache headers — call before any output ─────────────────────────────────
function setNoCache() {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
}

// ── Check if request is AJAX ──────────────────────────────────────────────────
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// ── Redirect helper (AJAX-aware) ──────────────────────────────────────────────
function redirectTo($url) {
    if (isAjax()) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
        exit;
    }
    header('Location: ' . $url);
    exit;
}

function checkLogin() {
    setNoCache();
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        redirectTo('/login/login');
    }
}

function checkAdmin() {
    checkLogin();
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        redirectTo('/dashboard/dashboard');
    }
}

function checkStudent() {
    checkLogin();
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
        redirectTo('/admin-tb5/admin-tb5/admin-dashboard/');
    }
}

function checkRole($allowedRoles = []) {
    checkLogin();
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowedRoles)) {
        http_response_code(403);
        redirectTo('/admin-tb5/403.php');
    }
}

function isSuperAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'SuperAdmin';
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function isStudent() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'student';
}

function getUserRole() {
    return $_SESSION['user_role'] ?? 'Guest';
}

function getUserType() {
    return $_SESSION['user_type'] ?? 'guest';
}
?>