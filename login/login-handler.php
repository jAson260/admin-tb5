<?php
// filepath: c:\laragon\www\admin-tb5\login\login-handler.php
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
        // ==================== STEP 1: CHECK ADMIN TABLE FIRST ====================
        $adminStmt = $pdo->prepare("
            SELECT 
                Id, Username, Email, Password, FirstName, LastName, 
                Role, Status
            FROM admins 
            WHERE (Email = ? OR Username = ?) 
            AND Status = 'Active'
            LIMIT 1
        ");
        $adminStmt->execute([$username, $username]);
        $adminUser = $adminStmt->fetch();
        
        if ($adminUser && password_verify($password, $adminUser['Password'])) {
            // ==================== ADMIN LOGIN SUCCESS ====================
            
            // Update last login
            $updateStmt = $pdo->prepare("UPDATE admins SET LastLogin = NOW() WHERE Id = ?");
            $updateStmt->execute([$adminUser['Id']]);
            
            // Set admin session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $adminUser['Id'];
            $_SESSION['user_type'] = 'admin';
            $_SESSION['user_role'] = $adminUser['Role']; // SuperAdmin, Admin, Staff
            $_SESSION['username'] = $adminUser['Username'];
            $_SESSION['user_email'] = $adminUser['Email'];
            $_SESSION['user_name'] = trim($adminUser['FirstName'] . ' ' . $adminUser['LastName']);
            
            // Redirect to admin dashboard
            header('Location: ../admin-tb5/admin-dashboard/admin-dashboard.php');
            exit;
        }
        
        // ==================== STEP 2: CHECK STUDENT TABLE ====================
        $studentStmt = $pdo->prepare("
            SELECT 
                Id, FirstName, LastName, MiddleName, Email, ULI, 
                Password, Status, Role 
            FROM studentinfos 
            WHERE Email = ? OR ULI = ?
            LIMIT 1
        ");
        $studentStmt->execute([$username, $username]);
        $studentUser = $studentStmt->fetch();
        
        if ($studentUser) {
            // Verify password
            if (password_verify($password, $studentUser['Password'])) {
                // Check if account is approved
                if ($studentUser['Status'] === 'Approved') {
                    // ==================== STUDENT LOGIN SUCCESS ====================
                    
                    // Update last login
                    $updateStmt = $pdo->prepare("UPDATE studentinfos SET LastLogin = NOW() WHERE Id = ?");
                    $updateStmt->execute([$studentUser['Id']]);
                    
                    // Set student session variables
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $studentUser['Id'];
                    $_SESSION['user_type'] = 'student';
                    $_SESSION['user_role'] = $studentUser['Role'] ?? 'Student'; // Student, Trainee
                    $_SESSION['user_name'] = trim($studentUser['FirstName'] . ' ' . $studentUser['LastName']);
                    $_SESSION['user_email'] = $studentUser['Email'];
                    $_SESSION['uli'] = $studentUser['ULI'];
                    $_SESSION['status'] = $studentUser['Status'];
                    
                    // Redirect to student dashboard
                    header('Location: ../dashboard/dashboard.php');
                    exit;
                    
                } else if ($studentUser['Status'] === 'Pending') {
                    $_SESSION['login_error'] = 'Your account is pending approval. Please wait for admin verification.';
                    header('Location: login.php');
                    exit;
                } else if ($studentUser['Status'] === 'Rejected') {
                    $_SESSION['login_error'] = 'Your account has been rejected. Please contact the administrator for more information.';
                    header('Location: login.php');
                    exit;
                } else if ($studentUser['Status'] === 'Suspended') {
                    $_SESSION['login_error'] = 'Your account has been suspended. Please contact the administrator.';
                    header('Location: login.php');
                    exit;
                } else {
                    $_SESSION['login_error'] = 'Your account status is: ' . $studentUser['Status'] . '. Contact admin for assistance.';
                    header('Location: login.php');
                    exit;
                }
            } else {
                // Wrong password for student account
                $_SESSION['login_error'] = 'Invalid email or password';
                header('Location: login.php');
                exit;
            }
        }
        
        // ==================== NO MATCHING USER FOUND ====================
        $_SESSION['login_error'] = 'Invalid credentials. Please check your email/username and password.';
        header('Location: login.php');
        exit;
        
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