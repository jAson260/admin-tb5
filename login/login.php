<?php
// filepath: c:\laragon\www\admin-tb5\login\login.php
session_start();

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
        header('Location: ../admin-tb5/admin-dashboard/admin-dashboard.php');
    } else {
        header('Location: ../dashboard/dashboard.php');
    }
    exit;
}

$loginError = $_SESSION['login_error'] ?? '';
$logoutSuccess = $_SESSION['logout_success'] ?? '';
unset($_SESSION['login_error']);
unset($_SESSION['logout_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Big Five Training and Assessment Center</title>
    <link rel="icon" type="image/x-icon" href="../img/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --royal-blue: #4169E1; 
            --royal-dark: #2e51b8;
        }
        body { 
            background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 40%, #1b77cc 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }
        
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .login-page::before { 
            content: ""; 
            position: absolute; 
            top: 0; left: 0; right: 0; bottom: 0; 
            background: rgba(249, 250, 255, 0.45); 
            z-index: 1; 
        }
        
        .login-card { 
            z-index: 2; 
            width: 100%; 
            max-width: 450px; 
            background: rgba(255, 255, 255, 0.95) !important; 
            backdrop-filter: blur(10px); 
            border-radius: 20px; 
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.15); 
        }
        
        .btn-royal { 
            background-color: var(--royal-blue) !important;
            color: white !important; 
            border: none;
            transition: 0.3s;
        }
        
        .btn-royal:hover { 
            background-color: var(--royal-dark) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        
        .text-royal { color: var(--royal-blue) !important; }
        
        .text-royal:hover {
            text-decoration: underline !important;
        }
        
        @media (max-width: 576px) {
            .login-card {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="login-page">
    <div class="login-card shadow-lg p-4 mx-3">
        
        <div class="text-center mb-4">
            <!-- Logos -->
            <img src="../img/logo1.png" alt="Logo" style="height: 70px; width: 70px;">
            <img src="../img/logo2.png" alt="Logo" style="height: 70px; width: 70px;">
            <h4 class="fw-bold text-dark mb-0 mt-2">Portal Login</h4>
            <p class="text-muted small">The Big Five Training & Assessment Center</p>
        </div>

        <!-- Logout Success Message -->
        <?php if ($logoutSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($logoutSuccess); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Login Error Message -->
        <?php if ($loginError): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($loginError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="login-handler.php" method="POST">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Email or Username" required>
                <label for="username"><i class="fas fa-user me-2"></i>Email</label>
            </div>

            <div class="form-floating mb-3 position-relative">
                <input type="password" class="form-control pe-5" id="loginPassword" name="password" placeholder="Password" required>
                <label for="loginPassword"><i class="fas fa-lock me-2"></i>Password</label>
                
                <!-- Password Toggle Icon -->
                <span class="position-absolute top-50 end-0 translate-middle-y me-3" id="toggleLoginPassword" style="cursor: pointer; z-index: 10;">
                    <i class="fas fa-eye text-muted" id="loginEyeIcon"></i>
                </span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label small" for="remember">Remember me</label>
                </div>
                <a href="../forgotpassword/forgotpassword.php" class="text-decoration-underline small text-royal fw-bold">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-royal w-100 py-3 border-0 shadow-sm fw-bold">
                <i class="fas fa-sign-in-alt me-2"></i>SIGN IN
            </button>

            <div class="mt-4 text-center">
                <p class="mb-0 small text-muted">Don't have an account?</p>
                <a href="../register/register.php" class="text-royal fw-bold text-decoration-none">Create an Account</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle password visibility
    document.getElementById('toggleLoginPassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('loginPassword');
        const eyeIcon = document.getElementById('loginEyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
            eyeIcon.classList.add('text-royal');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
            eyeIcon.classList.remove('text-royal');
        }
    });

    // Auto-hide success message after 5 seconds
    <?php if ($logoutSuccess): ?>
    setTimeout(function() {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(function() {
                alert.remove();
            }, 150);
        }
    }, 5000);
    <?php endif; ?>
</script>
</body>
</html>