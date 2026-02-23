<?php
// filepath: c:\laragon\www\admin-tb5\includes\rbac-guard.php

function checkLogin() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: /login/login');
        exit;
    }
}

function checkAdmin() {
    checkLogin();
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        header('Location: /dashboard/dashboard');
        exit;
    }
}

function checkStudent() {
    checkLogin();
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
        header('Location: /admin-tb5/admin-tb5/admin-dashboard/');
        exit;
    }
}

function checkRole($allowedRoles = []) {
    checkLogin();
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowedRoles)) {
        http_response_code(403);
        header('Location: /admin-tb5/403.php');
        exit;
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