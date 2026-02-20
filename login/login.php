<?php
session_start();
$loginError = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Big Five Training and Assessment Center</title>
    
    <!-- Use $root for assets so they never break -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --royal-blue: #4169E1; 
            --royal-dark: #2e51b8;
            --header-height: 80px;
            --sidebar-width: 260px;
        }
        body { background-color: #ffffff; overflow-x: hidden; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        .bg-royal { background-color: var(--royal-blue) !important; color: #fff !important; }
        .text-royal { color: var(--royal-blue) !important; }
        
        /* Fixed Header */
        .navbar { height: var(--header-height); background-color: var(--royal-blue) !important; z-index: 1050; }
        .navbar-logo { height: 55px; width: 55px; object-fit: cover; background-color: white; }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            padding-top: 20px;
            background-color: #ffffff;
            border-right: 1px solid #dee2e6;
            z-index: 1040;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link { color: #333; padding: 12px 20px; font-weight: 500; }
        .sidebar .nav-link.active { background-color: var(--royal-blue); color: white !important; }

        /* Content Area */
        .main-content { margin-left: var(--sidebar-width); padding: 30px; transition: all 0.3s ease; min-height: calc(100vh - var(--header-height)); }

        /* Login Backgrounds */
        .login-page {
            background: linear-gradient(135deg, #ffffff 0%, #e3f2fd 40%, #1b77cc 100%);
            background-attachment: fixed;
            height: 100vh; width: 100%; display: flex; align-items: center; justify-content: center; position: relative;
        }
        .login-page::before { content: ""; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(249, 250, 255, 0.45); z-index: 1; }
        .login-card { z-index: 2; width: 100%; max-width: 400px; background: rgba(255, 255, 255, 0.85) !important; backdrop-filter: blur(8px); border-radius: 15px; box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1); }

        /* Mobile Logic */
        @media (max-width: 992px) {
            .sidebar { left: calc(var(--sidebar-width) * -1); }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0; padding: 20px; }
        }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1035; }
        .sidebar-overlay.show { display: block; }

        /* Notifications */
        .notif-unread { border-left: 4px solid var(--royal-blue); background: #f0f4ff; }

        /* This should be in your header.php <style> section */
.btn-royal { 
    background-color: #4169E1 !important; /* Royal Blue */
    color: white !important; 
    border: none;
    transition: 0.3s;
}

.btn-royal:hover { 
    background-color: #2e51b8 !important; /* Royal Dark */
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}
/* Compact Floating Labels */
.form-floating-sm > .form-control,
.form-floating-sm > .form-select {
    height: calc(2.5rem + 2px); /* Normal is 3.5rem, this makes it slimmer */
    padding: 1rem 0.75rem;
    font-size: 0.9rem;
}
/* Alternative: Underline only on hover for a cleaner look */
.text-royal:hover {
    text-decoration: underline !important;
}
.form-floating-sm > label {
    padding: 0.5rem 0.75rem;
    font-size: 1rem;
}

/* Adjust label position when floating */
.form-floating-sm > .form-control:focus ~ label,
.form-floating-sm > .form-control:not(:placeholder-shown) ~ label,
.form-floating-sm > .form-select ~ label {
    transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem);
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

        <?php if ($loginError): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($loginError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Action points to login-handler.php -->
        <form action="login-handler.php" method="POST">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Email or ULI" required>
                <label for="username"><i class="fas fa-user me-2"></i>Email or ULI</label>
            </div>

          <div class="form-floating mb-3 position-relative">
    <!-- 'pe-5' ensures the password text doesn't overlap the icon -->
    <input type="password" class="form-control pe-5" id="loginPassword" name="password" placeholder="Password" required>
    <label for="loginPassword"><i class="fas fa-lock me-2"></i>Password</label>
    
    <!-- LOGIN EYE ICON TOGGLE -->
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
                SIGN IN
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
</script>
</body>
</html>