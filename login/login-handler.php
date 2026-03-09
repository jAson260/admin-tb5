<?php

session_start();
require_once('../db-connect.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Please enter both username and password';
    header('Location: /login');
    exit;
}

try {
    // ── STEP 1: CHECK ADMIN TABLE ─────────────────────────────────────────────
    $adminStmt = $pdo->prepare("
        SELECT Id, Username, Email, Password, FirstName, LastName, Role, Status
        FROM admins
        WHERE (Email = ? OR Username = ?)
        AND Status = 'Active'
        LIMIT 1
    ");
    $adminStmt->execute([$username, $username]);
    $adminUser = $adminStmt->fetch();

    if ($adminUser && password_verify($password, $adminUser['Password'])) {
        // ── Update last login ─────────────────────────────────────────────────
        $pdo->prepare("UPDATE admins SET LastLogin = NOW() WHERE Id = ?")
            ->execute([$adminUser['Id']]);

        // ── Set session ───────────────────────────────────────────────────────
        $_SESSION['logged_in']  = true;
        $_SESSION['user_id']    = $adminUser['Id'];
        $_SESSION['user_type']  = 'admin';
        $_SESSION['user_role']  = $adminUser['Role'];
        $_SESSION['username']   = $adminUser['Username'];
        $_SESSION['user_email'] = $adminUser['Email'];
        $_SESSION['user_name']  = trim($adminUser['FirstName'] . ' ' . $adminUser['LastName']);

        // ── Redirect to admin dashboard ───────────────────────────────────────
        header('Location: /admin-tb5/admin-dashboard');
        exit;
    }

    // ── STEP 2: CHECK STUDENT TABLE ───────────────────────────────────────────
    $studentStmt = $pdo->prepare("
        SELECT Id, FirstName, LastName, MiddleName, Email, ULI,
               Password, Status, Role
        FROM studentinfos
        WHERE Email = ? OR ULI = ?
        LIMIT 1
    ");
    $studentStmt->execute([$username, $username]);
    $studentUser = $studentStmt->fetch();

    if ($studentUser) {
        if (!password_verify($password, $studentUser['Password'])) {
            $_SESSION['login_error'] = 'Invalid email or password.';
            header('Location: /login');
            exit;
        }

        switch ($studentUser['Status']) {
            case 'Approved':
                // ── Update last login ─────────────────────────────────────────
                $pdo->prepare("UPDATE studentinfos SET LastLogin = NOW() WHERE Id = ?")
                    ->execute([$studentUser['Id']]);

                // ── Set session ───────────────────────────────────────────────
                $_SESSION['logged_in']  = true;
                $_SESSION['user_id']    = $studentUser['Id'];
                $_SESSION['user_type']  = 'student';
                $_SESSION['user_role']  = $studentUser['Role'] ?? 'Student';
                $_SESSION['user_name']  = trim($studentUser['FirstName'] . ' ' . $studentUser['LastName']);
                $_SESSION['user_email'] = $studentUser['Email'];
                $_SESSION['uli']        = $studentUser['ULI'];
                $_SESSION['status']     = $studentUser['Status'];

                // ── Redirect to student dashboard ─────────────────────────────
                header('Location: /dashboard');
                exit;

            case 'Pending':
                $_SESSION['login_error'] = 'Your account is pending approval. Please wait for admin verification.';
                break;

            case 'Rejected':
                $_SESSION['login_error'] = 'Your account has been rejected. Please contact the administrator.';
                break;

            case 'Suspended':
                $_SESSION['login_error'] = 'Your account has been suspended. Please contact the administrator.';
                break;

            default:
                $_SESSION['login_error'] = 'Your account status is: ' . htmlspecialchars($studentUser['Status']) . '. Contact admin for assistance.';
                break;
        }

        header('Location: /login');
        exit;
    }

    // ── NO MATCHING USER FOUND ────────────────────────────────────────────────
    $_SESSION['login_error'] = 'Invalid credentials. Please check your email/username and password.';
    header('Location: /login');
    exit;

} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    $_SESSION['login_error'] = 'A server error occurred. Please try again.';
    header('Location: /login');
    exit;
}
?>