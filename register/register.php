<?php 
include '../includes/header.php'; 
?>

<style>
    .login-page {
        height: auto !important; 
        min-height: 100vh;
        padding: 50px 0;
        display: flex;
        align-items: center; 
        justify-content: center;
    }
    body { background-attachment: fixed; }
    
    /* Fix for unclickable buttons: ensures modal is always on top of backdrops */
    .modal { z-index: 1060 !important; }
    .modal-backdrop { z-index: 1050 !important; }
    
    .form-floating > label {
        pointer-events: none !important;
        width: calc(100% - 50px);
    }
    #pass::-ms-reveal, #pass::-ms-clear { display: none; }
    .rounded-4 { border-radius: 1.2rem !important; }
</style>

<div class="login-page">
    <div class="login-card shadow-lg p-4 p-md-5 mx-3" style="max-width: 850px; background: #fff; border-radius: 1rem;">
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="d-flex justify-content-center gap-2 mb-2">
                <img src="../img/logo1.png" style="height: 60px; width: 60px;" class="rounded-circle shadow-sm">
                <img src="../img/logo2.png" style="height: 60px; width: 60px;" class="rounded-circle shadow-sm">
            </div>
            <h4 class="fw-bold text-dark mb-0">Create Trainee Account</h4>
            <p class="text-muted small">The Big Five Training & Assessment Center Inc.</p>
        </div>

        <form id="registerForm">
            
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
<h6 class="fw-bold text-royal mb-2" style="font-size: 0.95rem;">
    <i class="fas fa-map-marker-alt me-2"></i>Complete Address
</h6>

<div class="row g-2 mb-2">
    <div class="col-md-6">
        <div class="form-floating form-floating-sm">
            <input type="text" name="street" class="form-control" id="streetInput" placeholder="Street" required>
            <label for="streetInput">House/Block/Lot No., Street</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-floating form-floating-sm">
            <select name="region_code" class="form-select" id="regionSelect" required>
                <option value="" selected disabled>Loading regions...</option>
            </select>
            <label for="regionSelect">Region</label>
        </div>
    </div>
</div>

<div class="row g-2 mb-4">
    <div class="col-md-4">
        <div class="form-floating form-floating-sm">
            <select name="province_code" class="form-select" id="provinceSelect" required disabled>
                <option value="" selected disabled>Select region first</option>
            </select>
            <label for="provinceSelect">Province</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating form-floating-sm">
            <select name="city_code" class="form-select" id="citySelect" required disabled>
                <option value="" selected disabled>Select province first</option>
            </select>
            <label for="citySelect">City/Municipality</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating form-floating-sm">
            <select name="barangay_code" class="form-select" id="brgySelect" required disabled>
                <option value="" selected disabled>Select city first</option>
            </select>
            <label for="brgySelect">Barangay</label>
        </div>
    </div>
</div>

<!-- HIDDEN INPUTS: These will store the actual names to be saved in your database -->
<input type="hidden" name="region" id="region_name">
<input type="hidden" name="province" id="province_name">
<input type="hidden" name="city" id="city_name">
<input type="hidden" name="barangay" id="barangay_name">

<!-- ADDRESS API SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiBase = "https://psgc.gitlab.io/api";
    
    const regionSelect = document.getElementById('regionSelect');
    const provinceSelect = document.getElementById('provinceSelect');
    const citySelect = document.getElementById('citySelect');
    const brgySelect = document.getElementById('brgySelect');

    // Hidden fields to store names
    const regionName = document.getElementById('region_name');
    const provinceName = document.getElementById('province_name');
    const cityName = document.getElementById('city_name');
    const brgyName = document.getElementById('barangay_name');

    // 1. Initial Load: Regions
    fetch(`${apiBase}/regions/`)
        .then(response => response.json())
        .then(data => {
            regionSelect.innerHTML = '<option value="" selected disabled>Select Region</option>';
            data.sort((a, b) => a.name.localeCompare(b.name));
            data.forEach(region => {
                let opt = document.createElement('option');
                opt.value = region.code;
                opt.text = region.name;
                regionSelect.add(opt);
            });
        })
        .catch(err => {
            regionSelect.innerHTML = '<option value="" disabled>Error loading regions</option>';
            console.error("API Error:", err);
        });

    // 2. Region Change
    regionSelect.addEventListener('change', function() {
        regionName.value = this.options[this.selectedIndex].text; // Store Name
        
        // Reset and Disable children
        provinceSelect.disabled = false;
        provinceSelect.innerHTML = '<option value="" selected disabled>Loading...</option>';
        citySelect.disabled = true;
        citySelect.innerHTML = '<option value="" selected disabled>Select province first</option>';
        brgySelect.disabled = true;
        brgySelect.innerHTML = '<option value="" selected disabled>Select city first</option>';

        // NCR Special Handling (NCR has no provinces)
        if (this.value === "130000000") { 
            let opt = document.createElement('option');
            opt.value = "130000000";
            opt.text = "Metro Manila";
            provinceSelect.innerHTML = '';
            provinceSelect.add(opt);
            provinceSelect.value = "130000000";
            provinceSelect.dispatchEvent(new Event('change')); // Trigger city load
        } else {
            fetch(`${apiBase}/regions/${this.value}/provinces/`)
                .then(response => response.json())
                .then(data => {
                    provinceSelect.innerHTML = '<option value="" selected disabled>Select Province</option>';
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    data.forEach(prov => {
                        let opt = document.createElement('option');
                        opt.value = prov.code;
                        opt.text = prov.name;
                        provinceSelect.add(opt);
                    });
                });
        }
    });

    // 3. Province Change
    provinceSelect.addEventListener('change', function() {
        provinceName.value = this.options[this.selectedIndex].text; // Store Name
        
        citySelect.disabled = false;
        citySelect.innerHTML = '<option value="" selected disabled>Loading...</option>';
        brgySelect.disabled = true;
        brgySelect.innerHTML = '<option value="" selected disabled>Select city first</option>';

        // Load Cities based on Region (if NCR) or Province
        let fetchUrl = (this.value === "130000000") 
            ? `${apiBase}/regions/${this.value}/cities-municipalities/`
            : `${apiBase}/provinces/${this.value}/cities-municipalities/`;

        fetch(fetchUrl)
            .then(response => response.json())
            .then(data => {
                citySelect.innerHTML = '<option value="" selected disabled>Select City/Mun.</option>';
                data.sort((a, b) => a.name.localeCompare(b.name));
                data.forEach(city => {
                    let opt = document.createElement('option');
                    opt.value = city.code;
                    opt.text = city.name;
                    citySelect.add(opt);
                });
            });
    });

    // 4. City Change
    citySelect.addEventListener('change', function() {
        cityName.value = this.options[this.selectedIndex].text; // Store Name
        
        brgySelect.disabled = false;
        brgySelect.innerHTML = '<option value="" selected disabled>Loading...</option>';

        fetch(`${apiBase}/cities-municipalities/${this.value}/barangays/`)
            .then(response => response.json())
            .then(data => {
                brgySelect.innerHTML = '<option value="" selected disabled>Select Barangay</option>';
                data.sort((a, b) => a.name.localeCompare(b.name));
                data.forEach(brgy => {
                    let opt = document.createElement('option');
                    opt.value = brgy.code;
                    opt.text = brgy.name;
                    brgySelect.add(opt);
                });
            });
    });

    // 5. Barangay Change
    brgySelect.addEventListener('change', function() {
        brgyName.value = this.options[this.selectedIndex].text; // Store Name
    });
});
</script>

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
                <input type="password" name="password" class="form-control pe-5" id="pass" placeholder="Password" required>
                <label for="pass">Password</label>
                <span class="position-absolute top-50 end-0 translate-middle-y me-3" id="togglePassword" style="cursor: pointer; z-index: 100;">
                    <i class="fas fa-eye text-muted" id="eyeIcon" style="font-size: 1.1rem;"></i>
                </span>
            </div>

            <button type="button" id="btnRegister" class="btn btn-primary w-100 py-3 border-0 shadow-sm fw-bold rounded-pill">
                REGISTER ACCOUNT
            </button>

            <div class="mt-4 text-center">
                <a href="../login.php" class="text-decoration-none small fw-bold text-primary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal fade" id="regSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 60px;"></i>
                </div>
                <h4 class="fw-bold">Registration Successful!</h4>
                <p class="text-muted small">Registration successfully submitted. Wait for the admin to review your account.</p>
                <button type="button" class="btn btn-primary rounded-pill px-5 py-2 mt-2 fw-bold" id="confirmOk">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ERROR MODAL -->
<div class="modal fade" id="regErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">
                <i class="fas fa-exclamation-triangle text-danger mb-4" style="font-size: 50px;"></i>
                <h4 class="fw-bold">Registration Error</h4>
                <p class="text-muted small" id="errorMessageText"></p>
                <button type="button" class="btn btn-secondary rounded-pill px-5 fw-bold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('registerForm');
    const regBtn = document.getElementById('btnRegister');
    
    // Initialize modals properly once
    const successModal = new bootstrap.Modal(document.getElementById('regSuccessModal'));
    const errorModal = new bootstrap.Modal(document.getElementById('regErrorModal'));

    // 1. Password Toggle
    const togglePassword = document.querySelector('#togglePassword');
    const passInput = document.getElementById('pass');
    const eyeIcon = document.getElementById('eyeIcon');

    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passInput.type === 'password' ? 'text' : 'password';
            passInput.type = type;
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    }

    // 2. Form Submission logic
    regBtn.addEventListener('click', function(e) {
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // UI State: Loading
        regBtn.disabled = true;
        const originalText = regBtn.innerHTML;
        regBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>PROCESSING...';

        fetch('register-handler.php', {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                // Show success and wait for OK click
                successModal.show();
            } else {
                document.getElementById('errorMessageText').innerText = data.message || 'Error occurred.';
                errorModal.show();
                resetBtn();
            }
        })
        .catch(error => {
            document.getElementById('errorMessageText').innerText = 'Connection error. Please try again.';
            errorModal.show();
            resetBtn();
        });

        function resetBtn() {
            regBtn.disabled = false;
            regBtn.innerHTML = originalText;
        }
    });

    // 3. Success OK Button Redirect
    document.getElementById('confirmOk').addEventListener('click', function() {
        window.location.href = '../login.php';
    });
});

</script>