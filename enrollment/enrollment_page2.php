<form action="upload-documents-handler.php" method="POST" enctype="multipart/form-data" id="uploadForm">

    <?php 
// Go up one level to find the includes folder
include '../includes/header.php'; 
// Sidebar is removed for a focused enrollment experience as per Step 2
?>

<style>
    /* Senior Dev Adjustment: Maintains the same left-aligned balanced layout from Step 2 */
    .main-content {
        margin-left: 0 !important; 
        padding-top: 100px !important; 
        padding-left: 80px !important; 
        padding-right: 80px !important;
        transition: none !important;
    }

    .enrollment-container {
        max-width: 900px; /* Slightly narrower for a cleaner file upload list */
    }

    /* Custom style for the file input area */
    .file-upload-wrapper {
        border: 1px solid #057cf2;
        padding: 20px;
        border-radius: 12px;
        background-color: #fcfcfc;
        transition: 0.3s;
    }
    .file-upload-wrapper:hover {
        border-color: var(--royal-blue);
        background-color: #8c9ced;
    }

    @media (max-width: 768px) {
        .main-content {
            padding-left: 20px !important;
            padding-right: 20px !important; 
        }
    }
</style>

<!-- Main content area -->
<div class="main-content">
    <div class="container-fluid enrollment-container">
        
        <!-- Page Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold text-dark">
                    <i class="fas fa-upload me-2 text-royal"></i>Upload Documents
                </h3>
                <p class="text-muted small">Final Step of Enrollment - Please upload clear scanned copies of your documents (PDF, JPG, or PNG).</p>
            </div>
        </div>

        <!-- Enrollment Form Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-body p-4 p-md-5">
                <!-- IMPORTANT: Add id="uploadForm" here -->
                <form id="uploadForm" enctype="multipart/form-data">
                    
                    <div class="row g-4">
                        
                        <!-- 1. PSA Birth Certificate -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark"><i class="fas fa-baby me-2 text-primary"></i>PSA Birth Certificate</label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="psa_file" class="form-control border-0 bg-transparent" accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="form-text mt-1" style="font-size: 11px;">Official PSA copy is required.</div>
                            </div>
                        </div>

                        <!-- 2. Transcript of Records (TOR) -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark"><i class="fas fa-file-alt me-2 text-primary"></i>Transcript of Records (TOR)</label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="tor_file" class="form-control border-0 bg-transparent" accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="form-text mt-1" style="font-size: 11px;">Clear copy of your latest TOR.</div>
                            </div>
                        </div>

                        <!-- 3. Diploma -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark"><i class="fas fa-graduation-cap me-2 text-primary"></i>Diploma</label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="diploma_file" class="form-control border-0 bg-transparent" accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="form-text mt-1" style="font-size: 11px;">High School or College Diploma.</div>
                            </div>
                        </div>

                        <!-- 4. Marriage Certificate -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <label class="form-label small fw-bold text-dark"><i class="fas fa-ring me-2 text-primary"></i>Marriage Certificate</label>
                                <span class="badge bg-light text-muted fw-normal" style="font-size: 10px;">Optional</span>
                            </div>
                            <div class="file-upload-wrapper">
                                <input type="file" name="marriage_cert" class="form-control border-0 bg-transparent" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text mt-1" style="font-size: 11px;">Only for married female applicants.</div>
                            </div>
                        </div>

<!-- SUBMISSION & PRIVACY AREA -->
<div class="mt-5 border-top pt-4">
    
    <!-- Instruction Note & Data Privacy Container -->
    <div class="row g-3">
        <div class="col-12">
            <!-- Instruction Note -->
            <div class="p-3 rounded-3 bg-light border-start border-primary border-4 mb-3">
                <small class="text-muted d-block">
                    <i class="fas fa-info-circle me-1 text-primary"></i> 
                    <strong>Note:</strong> Please ensure files are not larger than 5MB each. Supported formats: <b class="text-dark">.pdf, .jpg, .png</b>
                </small>
            </div>

            <!-- DATA PRIVACY SECTION -->
            <div class="form-check d-flex align-items-start p-3 rounded-3 border shadow-sm transition-all" 
                 style="background-color: #f0f4ff; border-left: 5px solid var(--royal-blue, #002366) !important;">
                
                <!-- Responsive Checkbox: Added flex-shrink-0 to prevent squishing on mobile -->
                <input class="form-check-input me-3 ms-0 flex-shrink-0" type="checkbox" id="privacyCheck" 
                       style="width: 22px; height: 22px; cursor: pointer; border-color: var(--royal-blue, #002366);">
                
                <label class="form-check-label small text-dark mb-0" for="privacyCheck" style="cursor: pointer; line-height: 1.5;">
                    <i class="fas fa-user-shield text-royal me-1"></i>
                    I agree to the <strong>Terms & Conditions</strong> and understand that my personal details, valid ID's and Certificates will be kept confidential and used only for verification purposes following data privacy guidelines.
                </label>
            </div>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="text-end mt-4">
       
        <p id="privacyWarning" class="text-danger small mt-2">
            <i class="fas fa-exclamation-triangle me-1"></i> Please check the privacy agreement to proceed.
        </p>
    </div>
</div>

<!-- JAVASCRIPT FOR LOGIC -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const privacyCheck = document.getElementById('privacyCheck');
    const enrollBtn = document.getElementById('btnCompleteEnrollment');
    const warningText = document.getElementById('privacyWarning');

    privacyCheck.addEventListener('change', function() {
        if (this.checked) {
            // Enable button
            enrollBtn.disabled = false;
            enrollBtn.classList.remove('opacity-50');
            warningText.classList.add('d-none');
            enrollBtn.style.cursor = "pointer";
        } else {
            // Disable button
            enrollBtn.disabled = true;
            enrollBtn.classList.add('opacity-50');
            warningText.classList.remove('d-none');
            enrollBtn.style.cursor = "not-allowed";
        }
    });
});
</script>

<style>
/* Smooth hover effect for the privacy box */
.transition-all {
    transition: all 0.3s ease;
}
.form-check:hover {
    background-color: #e8eeff !important;
}
/* Ensure the button looks disabled clearly */
.btn-royal:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>
                    <!-- Navigation Buttons -->
                    <div class="mt-5 d-flex justify-content-between border-top pt-4">
                        <a href="/enrollment" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted">
                            <i class="fas fa-chevron-left me-2"></i> Back
                        </a>
                       <!-- Button starts as DISABLED -->
        <button type="submit" id="btnCompleteEnrollment" class="btn btn-royal rounded-pill px-5 py-2 fw-bold shadow-sm opacity-50" disabled>
    Complete Enrollment <i class="fas fa-check-double ms-2"></i>
    </button>
                    </div>

<!-- SUCCESS MODAL (Remains the same) -->
<div class="modal fade" id="enrollSuccessModal" tabindex="-1" aria-labelledby="enrollSuccessModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                </div>
                <h3 class="fw-bold text-dark mb-3">Enrollment Successfully Sent!</h3>
                <p class="text-muted mb-4">Your application has been received. Please wait for the Admin's approval before proceeding with your training.</p>
                <button type="button" class="btn btn-royal rounded-pill px-5 py-2 fw-bold shadow-sm" id="confirmRedirect">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    const privacyCheck = document.getElementById('privacyCheck');
    const completeBtn = document.getElementById('btnCompleteEnrollment');
    const warningText = document.getElementById('privacyWarning');
    const successModal = new bootstrap.Modal(document.getElementById('enrollSuccessModal'));

    // 1. Checkbox Logic
    privacyCheck.addEventListener('change', function() {
        if(this.checked) {
            completeBtn.disabled = false;
            completeBtn.classList.remove('opacity-50');
            warningText.classList.add('d-none');
        } else {
            completeBtn.disabled = true;
            completeBtn.classList.add('opacity-50');
            warningText.classList.remove('d-none');
        }
    });

    // 2. Form Submission via AJAX
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevents the raw JSON black screen

        completeBtn.disabled = true;
        completeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';

        const formData = new FormData(this);

        fetch('upload-documents-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successModal.show();
            } else {
                alert('Error: ' + data.message);
                resetBtn();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
            resetBtn();
        });
    });

    function resetBtn() {
        completeBtn.disabled = false;
        completeBtn.innerHTML = 'Complete Enrollment <i class="fas fa-check-double ms-2"></i>';
    }

    // 3. Simple Redirect
    document.getElementById('confirmRedirect').addEventListener('click', function() {
        window.location.href = '../dashboard/dashboard.php';
    });
});
</script>

<?php include '../includes/footer.php'; ?>