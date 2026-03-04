<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\add-account.php
session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();
require_once('../../db-connect.php');

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
    exit;
}

$firstName = trim($data['firstName'] ?? '');
$lastName  = trim($data['lastName'] ?? '');
$username  = trim($data['username'] ?? '');
$email     = trim($data['email'] ?? '');
$password  = $data['password'] ?? '';
$role      = $data['role'] ?? 'Admin';
$status    = $data['status'] ?? 'Active';

// Validate required fields
if (!$firstName || !$lastName || !$username || !$email || !$password || !$role) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled in']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

// Validate role against exact enum values in DB
$allowedRoles = ['SuperAdmin', 'Admin', 'Staff'];
if (!in_array($role, $allowedRoles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role selected']);
    exit;
}

// Validate status against exact enum values in DB
$allowedStatuses = ['Active', 'Inactive', 'Suspended'];
if (!in_array($status, $allowedStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status selected']);
    exit;
}

try {
    // Check if username already exists
    $checkUsername = $pdo->prepare("SELECT Id FROM admins WHERE Username = ? LIMIT 1");
    $checkUsername->execute([$username]);
    if ($checkUsername->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit;
    }

    // Check if email already exists
    $checkEmail = $pdo->prepare("SELECT Id FROM admins WHERE Email = ? LIMIT 1");
    $checkEmail->execute([$email]);
    if ($checkEmail->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into admins table using exact column names from DB
    $stmt = $pdo->prepare("
        INSERT INTO admins 
            (Username, Email, Password, FirstName, LastName, Role, Status)
        VALUES 
            (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $username,
        $email,
        $hashedPassword,
        $firstName,
        $lastName,
        $role,
        $status
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Admin account created successfully'
    ]);

} catch (PDOException $e) {
    error_log("Add Admin Account Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>