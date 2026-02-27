<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\add-admin-account.php
session_start();
require_once('../../db-connect.php');

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Only admins can create accounts'
    ]);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!$data || !isset($data['first_name']) || !isset($data['last_name']) || 
    !isset($data['username']) || !isset($data['email']) || 
    !isset($data['role']) || !isset($data['password'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

$firstName = trim($data['first_name']);
$lastName = trim($data['last_name']);
$username = trim($data['username']);
$email = trim($data['email']);
$role = trim($data['role']);
$password = $data['password'];

// Validation
if (empty($firstName) || empty($lastName) || empty($username) || empty($email) || empty($role) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required'
    ]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format'
    ]);
    exit;
}

// Validate password length
if (strlen($password) < 8) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 8 characters long'
    ]);
    exit;
}

// Validate role (must match ENUM values: SuperAdmin, Admin, Staff - no spaces)
$allowedRoles = ['SuperAdmin', 'Admin', 'Staff'];
if (!in_array($role, $allowedRoles)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid role selected'
    ]);
    exit;
}

try {
    // Check if username already exists
    $checkUsername = $pdo->prepare("SELECT Id FROM admins WHERE Username = ?");
    $checkUsername->execute([$username]);
    if ($checkUsername->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists. Please choose a different username.'
        ]);
        exit;
    }
    
    // Check if email already exists
    $checkEmail = $pdo->prepare("SELECT Id FROM admins WHERE Email = ?");
    $checkEmail->execute([$email]);
    if ($checkEmail->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists. Please use a different email address.'
        ]);
        exit;
    }
    
    // Hash password using PASSWORD_DEFAULT (bcrypt)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new admin account
    $stmt = $pdo->prepare("
        INSERT INTO admins (
            FirstName, 
            LastName, 
            Username, 
            Email, 
            Password, 
            Role, 
            Status, 
            CreatedAt,
            UpdatedAt
        ) VALUES (?, ?, ?, ?, ?, ?, 'Active', NOW(), NOW())
    ");
    
    $result = $stmt->execute([
        $firstName,
        $lastName,
        $username,
        $email,
        $hashedPassword,
        $role
    ]);
    
    if ($result) {
        $newAdminId = $pdo->lastInsertId();
        
        // Log activity (optional - create activity_logs table if needed)
        try {
            $logStmt = $pdo->prepare("
                INSERT INTO activity_logs (UserId, UserType, Action, ActionDetails, CreatedAt) 
                VALUES (?, 'admin', 'create_admin', ?, NOW())
            ");
            $adminName = $_SESSION['user_name'] ?? 'Admin';
            $logStmt->execute([
                $_SESSION['user_id'],
                "{$adminName} created new admin account: {$firstName} {$lastName} (Username: {$username}, Role: {$role})"
            ]);
        } catch (PDOException $e) {
            // Activity log failed, but don't fail the whole operation
            error_log("Activity log failed: " . $e->getMessage());
        }
        
        // Display role properly for response
        $roleDisplay = $role === 'SuperAdmin' ? 'Super Admin' : $role;
        
        echo json_encode([
            'success' => true,
            'message' => "Admin account created successfully!",
            'admin_id' => $newAdminId,
            'admin_name' => "{$firstName} {$lastName}",
            'role' => $roleDisplay
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create admin account. Please try again.'
        ]);
    }
    
} catch (PDOException $e) {
    // Log the actual error for debugging
    error_log("Database error in add-admin-account.php: " . $e->getMessage());
    
    // Send user-friendly error
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please contact system administrator.'
    ]);
}
?>