<?php include 'includes/header.php'; ?>

<div class="login-page">
    <div class="login-card shadow-lg p-4 mx-3">
        
        <div class="text-center mb-4">
            <!-- Logos -->
            <img src="img/logo1.png" alt="Logo" style="height: 70px; width: 70px;">
            <img src="img/logo2.png" alt="Logo" style="height: 70px; width: 70px;">
            <h4 class="fw-bold text-dark mb-0 mt-2">Portal Login</h4>
            <p class="text-muted small">The Big Five Training & Assessment Center</p>
        </div>

        <!-- Action points into the dashboard folder -->
        <form action="dashboard/dashboard.php" method="POST">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <label for="username"><i class="fas fa-user me-2"></i>Username</label>
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
                <!-- FIXED: Pointing into the folder from root -->
                <a href="forgotpassword/forgotpassword.php" class="text-decoration-underline small text-royal fw-bold">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-royal w-100 py-3 border-0 shadow-sm fw-bold">
                SIGN IN
            </button>

            <div class="mt-4 text-center">
                <p class="mb-0 small text-muted">Don't have an account?</p>
                <!-- FIXED: Pointing into the folder from root -->
                <a href="register/register.php" class="text-royal fw-bold text-decoration-none">Create an Account</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>