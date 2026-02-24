<?php 
// Go up one level to find includes
include '../includes/header.php'; 
?>

<style>
    /* Senior Dev Fix: Overriding the fixed height to allow scrolling for long forms */
    .login-page {
        height: auto !important; 
        min-height: 100vh;
        padding-top: 50px;
        padding-bottom: 50px;
        display: flex;
        align-items: center; 
        justify-content: center;
    }
    body { background-attachment: fixed; }
</style>

<div class="login-page">
    <div class="login-card shadow-lg p-4 p-md-5 mx-3" style="max-width: 850px;">
        
        <div class="text-center mb-4">
            <div class="d-flex justify-content-center gap-2 mb-2">
                <img src="../img/logo1.png" alt="Logo" class="rounded-circle shadow-sm" style="height: 60px; width: 60px; object-fit: cover; border: 2px solid var(--royal-blue);">
                <img src="../img/logo2.png" alt="Logo" class="rounded-circle shadow-sm" style="height: 60px; width: 60px; object-fit: cover; border: 2px solid var(--royal-blue);">
            </div>
            <h4 class="fw-bold text-dark mb-0">Create Trainee Account</h4>
            <p class="text-muted small">The Big Five Training & Assessment Center Inc.</p>
        </div>

        <form id="registerForm"> <!-- ID added for JavaScript control -->
            
            <!-- SECTION 1: OFFICIAL NAME -->
<h6 class="fw-bold text-royal mb-3"><i class="fas fa-user-tag me-2"></i>Official Name</h6>
<div class="row g-2 mb-4">
    <div class="col-md-4">
        <div class="form-floating">
            <input type="text" name="surname" class="form-control text-uppercase" id="lName" placeholder="Surname" required>
            <label for="lName">Surname</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating">
            <input type="text" name="first_name" class="form-control text-uppercase" id="fName" placeholder="First Name" required>
            <label for="fName">First Name</label>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-floating">
            <input type="text" name="mi" class="form-control text-center text-uppercase" id="mi" placeholder="MI" maxlength="2">
            <label for="mi">M.I.</label>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-floating">
            <select name="extension" class="form-select" id="extSelect">
                <option value="" selected></option>
                <option value="Jr.">Jr.</option>
                <option value="Sr.">Sr.</option>
                <option value="II">II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
                <option value="V">V</option>
            </select>
            <label for="extSelect">Ext.</label>
        </div>
    </div>
</div>

            <!-- SECTION 2: CONTACT & SECURITY -->
            <h6 class="fw-bold text-royal mb-3"><i class="fas fa-shield-alt me-2"></i>Contact & Security</h6>
            <div class="row g-2 mb-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" name="phone" class="form-control" id="phone" placeholder="09..." required>
                        <label for="phone"><i class="fas fa-phone me-1"></i> Cellphone Number</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control" name="email" id="emailAdd" placeholder="Email" required>
                        <label for="emailAdd"><i class="fas fa-envelope me-1"></i> Email Address</label>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: PERSONAL DETAILS -->
            <h6 class="fw-bold text-royal mb-3"><i class="fas fa-info-circle me-2"></i>Personal Details</h6>
            <div class="row g-2 mb-4"> 
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <select name="sex" class="form-select" id="sexSelect" required>
                            <option value="" selected disabled></option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <label for="sexSelect">Sex</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating form-floating-sm">
                        <input type="date" name="dob" class="form-control" id="dobInput" required>
                        <label for="dobInput">Date of Birth</label>
                    </div>
                </div>
                <div class="col-md-5">
    <div class="form-floating form-floating-sm">
        <input type="text" name="uli" class="form-control" id="uliInput" placeholder="ULI" required maxlength="16">
        <label for="uliInput">ULI (Unique Learner's Identifier)</label>
    </div>
</div>
            </div>

            <!-- SECTION 4: ADDRESS -->
            <h6 class="fw-bold text-royal mb-2" style="font-size: 0.95rem;"><i class="fas fa-map-marker-alt me-2"></i>Complete Address</h6>
            <div class="row g-2 mb-4">
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <select name="region" class="form-select" id="regionSelect" required>
                            <option value="" selected disabled></option>
                            <option value="NCR">NCR</option>
                            <option value="Region IV-A">Region IV-A</option>
                        </select>
                        <label for="regionSelect">Region</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <select name="province" class="form-select" id="provinceSelect" required>
                            <option value="" selected disabled></option>
                            <option value="Batangas">Batangas</option>
                            <option value="Laguna">Laguna</option>
                        </select>
                        <label for="provinceSelect">Province</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <select name="city" class="form-select" id="citySelect" required>
                            <option value="" selected disabled></option>
                            <option value="San Pablo">San Pablo</option>
                            <option value="Calamba">Calamba</option>
                        </select>
                        <label for="citySelect">City</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <select name="barangay" class="form-select" id="brgySelect" required>
                            <option value="" selected disabled></option>
                            <option value="Brgy VII-A">Brgy VII-A</option>
                        </select>
                        <label for="brgySelect">Barangay</label>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: EDUCATION -->
            <h6 class="fw-bold text-royal mb-3"><i class="fas fa-graduation-cap me-2"></i>Educational Background</h6>
            <div class="row g-2 mb-2">
                <div class="col-md-9">
                    <div class="form-floating form-floating-sm">
                        <input type="text" name="secondary_school" class="form-control" id="secSchool" placeholder="Secondary" required>
                        <label for="secSchool">Secondary School Attended</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <input type="number" name="secondary_year" class="form-control" id="secYear" placeholder="Year" required>
                        <label for="secYear">Year Completed</label>
                    </div>
                </div>
            </div>
            <div class="row g-2 mb-4">
                <div class="col-md-9">
                    <div class="form-floating form-floating-sm">
                        <input type="text" name="tertiary_school" class="form-control" id="tertSchool" placeholder="Tertiary">
                        <label for="tertSchool">Tertiary School (College/Vocational)</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-sm">
                        <input type="number" name="tertiary_year" class="form-control" id="tertYear" placeholder="Year">
                        <label for="tertYear">Year Completed</label>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: PASSWORD -->
<h6 class="fw-bold text-royal mb-3"><i class="fas fa-key me-2"></i>Account Security</h6>
<div class="form-floating mb-4 position-relative">
    <input type="password" name="password" class="form-control" id="pass" placeholder="Password" required>
    <label for="pass">Password</label>
    
    <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0" id="togglePassword" style="z-index: 10; background: transparent;">
        <i class="fas fa-eye text-muted" id="eyeIcon" style="font-size: 1.2rem;"></i>
    </button>
</div>

<!-- Action Button -->
<button type="button" id="btnRegister" class="btn btn-royal w-100 py-3 border-0 shadow-sm fw-bold rounded-pill">
    REGISTER ACCOUNT
</button>

<div class="mt-4 text-center">
    <a href="/login/login" class="text-royal fw-bold text-decoration-none small">
        <i class="fas fa-arrow-left me-1"></i> Back to Login
    </a>
</div>
</form>
    </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal fade" id="regSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <div class="mx-auto bg-success bg-opacity-10 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px;">
                        <i class="fas fa-check-circle text-success" style="font-size: 50px;"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark">Registration Successful!</h4>
                <p class="text-muted small">Registration successfully submitted. Wait for the admin to review your account.</p>
                <button type="button" class="btn btn-royal rounded-pill px-5 py-2 mt-2 shadow-sm fw-bold" id="confirmOk">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Logic -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        console.log('DOM loaded, initializing registration form...');
        
        const form = document.getElementById('registerForm');
        const regBtn = document.getElementById('btnRegister');
        const successModal = new bootstrap.Modal(document.getElementById('regSuccessModal'));
        const okBtn = document.getElementById('confirmOk');
        
        // Check if elements exist
        if (!form || !regBtn) {
            console.error('Form or button not found!');
            return;
        }
        
        console.log('Form and button found:', form, regBtn);

        // Toggle Password Visibility
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#pass');
        const eyeIcon = document.querySelector('#eyeIcon');

if (togglePassword && passwordInput && eyeIcon) {
    togglePassword.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Toggle password visibility
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle eye icon
        if (type === 'text') {
            // Password is visible
            eyeIcon.classList.remove('fa-eye', 'text-muted');
            eyeIcon.classList.add('fa-eye-slash', 'text-royal');
        } else {
            // Password is hidden
            eyeIcon.classList.remove('fa-eye-slash', 'text-royal');
            eyeIcon.classList.add('fa-eye', 'text-muted');
        }
        
        console.log('Password visibility toggled to:', type);
    });
    console.log('Password toggle initialized');
}
        // Register Button Click Handler
        regBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Register button clicked!');
            
            // Check browser native validation
            if (!form.checkValidity()) {
                console.log('Form invalid, showing validation errors');
                form.reportValidity();
                return;
            }
            
            console.log('Form valid, proceeding with submission...');
            
            // Disable button to prevent double submission
            regBtn.disabled = true;
            regBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>PROCESSING...';
            
            // Prepare form data
            const formData = new FormData(form);
            
            // Debug: Log form data
            console.log('=== Form Data ===');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Send AJAX request
            fetch('register-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received:', response.status, response.statusText);
                
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                
                return response.text();
            })
            .then(text => {
                console.log('Raw response text:', text);
                
                // Try to parse as JSON
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed JSON:', data);
                    
                    if (data.success) {
                        console.log('Registration successful!');
                        form.reset();
                        successModal.show();
                    } else {
                        console.error('Registration failed:', data.message);
                        alert('Registration Error: ' + data.message);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response was:', text);
                    alert('Server returned invalid response. Check console.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message + '\n\nCheck:\n1. Is register-handler.php accessible?\n2. Check browser console for details');
            })
            .finally(() => {
                // Re-enable button
                regBtn.disabled = false;
                regBtn.innerHTML = 'REGISTER ACCOUNT';
            });
        });

        // OK Button in Success Modal
        okBtn.addEventListener('click', function() {
            console.log('Redirecting to index...');
            window.location.href = '../index.php';
        });
        
        console.log('All event listeners registered successfully');
    });
</script>


