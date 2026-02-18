<?php 
// 1. Path detection to reach the root includes
include '../includes/header.php'; 
?>

<div class="login-page">
    <div class="login-card shadow-lg p-4 p-md-5 mx-3" style="max-width: 550px;">
        
        <div class="text-center mb-4">
            <!-- Institutional Logos -->
            <div class="d-flex justify-content-center gap-2 mb-3">
                <img src="../img/logo1.png" alt="Logo" class="rounded-circle shadow-sm" style="height: 65px; width: 65px; object-fit: cover; border: 2px solid var(--royal-blue);">
                <img src="../img/logo2.png" alt="Logo" class="rounded-circle shadow-sm" style="height: 65px; width: 65px; object-fit: cover; border: 2px solid var(--royal-blue);">
            </div>
            <h4 class="fw-bold text-dark mb-0">Recovery Request</h4>
            <p class="text-muted small">Verification details for manual issuance</p>
        </div>

        <form id="recoveryForm">
            <!-- Identity Credentials -->
            <div class="form-floating mb-3">
                <input type="text" name="full_name" class="form-control" id="fName" placeholder="Full Name" required>
                <label for="fName"><i class="fas fa-user me-2"></i>Full Name</label>
            </div>

           <div class="form-floating mb-3">
    <!-- Type set to email and ID updated to reflect the new data purpose -->
    <input type="email" name="email" class="form-control" id="emailInput" placeholder="name@example.com" required>
    <label for="emailInput"><i class="fas fa-envelope me-2"></i>Email Address</label>
</div>

            
            <!-- BUTTON: Changed to type="button" to handle Modal logic -->
            <button type="button" id="btnTriggerModal" class="btn btn-primary w-100 py-3 bg-royal border-0 shadow-sm fw-bold rounded-pill">
                SUBMIT REQUEST TO ADMIN
            </button>

            <!-- Navigation Links -->
            <div class="mt-4 text-center">
                <a href="../index.php" class="text-royal fw-bold text-decoration-underline small">
                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

<!-- SUCCESS MODAL (STATIC) -->
<div class="modal fade" id="requestSentModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body text-center p-5">
                <!-- Large Success Icon -->
                <div class="mb-4">
                    <div class="mx-auto bg-success bg-opacity-10 d-flex align-items-center justify-content-center rounded-circle" style="width: 100px; height: 100px;">
                        <i class="fas fa-paper-plane text-success" style="font-size: 50px;"></i>
                    </div>
                </div>
                
                <h3 class="fw-bold text-dark">Request Submitted!</h3>
                <p class="text-muted">Successfully sent your verification details. Please wait for the Admin to issue your new password.</p>
                
                <button type="button" class="btn btn-royal rounded-pill px-5 py-2 fw-bold shadow-sm" id="btnOkRedirect">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const myModal = new bootstrap.Modal(document.getElementById('requestSentModal'));
        const triggerBtn = document.getElementById('btnTriggerModal');
        const confirmBtn = document.getElementById('btnOkRedirect');

        // Logic: When clicking Submit, show the confirmation popup
        triggerBtn.addEventListener('click', function() {
            // Check form validity before showing modal
            const form = document.getElementById('recoveryForm');
            if (form.checkValidity()) {
                myModal.show();
            } else {
                form.reportValidity(); // Shows required field errors
            }
        });

        // Logic: When clicking OK, redirect to root Login (index.php)
        confirmBtn.addEventListener('click', function() {
            window.location.href = '../index.php';
        });
    });
</script>

<?php include '../includes/footer.php'; ?>