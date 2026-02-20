<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../db-connect.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate inputs
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Please enter both username and password';
        header('Location: login.php');
        exit;
    }
    
    try {
        // Check if user exists by email or ULI
        $stmt = $pdo->prepare("
            SELECT Id, FirstName, LastName, MiddleName, Email, Password, Status 
            FROM studentinfos 
            WHERE Email = ? OR ULI = ?
            LIMIT 1
        ");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Verify password
            if (password_verify($password, $user['Password'])) {
                // Check if account is approved
                if ($user['Status'] === 'Approved') {
                    // Set session variables
                    $_SESSION['user_id'] = $user['Id'];
                    $_SESSION['user_name'] = $user['FirstName'] . ' ' . $user['LastName'];
                    $_SESSION['user_email'] = $user['Email'];
                    $_SESSION['logged_in'] = true;
                    
                    // Redirect to dashboard
                    header('Location: ../dashboard/dashboard.php');
                    exit;
                } else if ($user['Status'] === 'Pending') {
                    $_SESSION['login_error'] = 'Your account is pending approval. Please wait for admin verification.';
                    header('Location: login.php');
                    exit;
                } else {
                    $_SESSION['login_error'] = 'Your account has been ' . strtolower($user['Status']) . '. Contact admin for assistance.';
                    header('Location: login.php');
                    exit;
                }
            } else {
                $_SESSION['login_error'] = 'Invalid username or password';
                header('Location: login.php');
                exit;
            }
        } else {
            $_SESSION['login_error'] = 'Invalid username or password';
            header('Location: login.php');
            exit;
        }
        
    } catch(PDOException $e) {
        $_SESSION['login_error'] = 'Database error: ' . $e->getMessage();
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
?>