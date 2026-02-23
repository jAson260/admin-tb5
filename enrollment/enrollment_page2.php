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
                <!-- IMPORTANT: enctype="multipart/form-data" is required for file uploads -->
                <form action="enrollment_summary.php" method="POST" enctype="multipart/form-data">
                    
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

                    </div>

            <!-- Instruction Note -->
                    <div class="mt-5 p-3 rounded-3 bg-light border-start border-primary border-4">
                        <small class="text-muted d-block">
                            <i class="fas fa-info-circle me-1 text-primary"></i> 
                            Please ensure files are not larger than 5MB each. Supported formats: <b>.pdf, .jpg, .png</b>
                        </small>
                    </div>

                    <!-- DATA PRIVACY SECTION -->
                    <div class="mt-4 mb-2">
                        <div class="form-check d-flex align-items-start p-3 rounded-3 border shadow-sm" style="background-color: #f0f4ff; border-left: 5px solid var(--royal-blue) !important;">
                            <input class="form-check-input me-3 ms-0" type="checkbox" id="privacyCheck" style="width: 25px; height: 18px; cursor: pointer; border-color: var(--royal-blue);">
                            <label class="form-check-label small text-dark mb-0" for="privacyCheck" style="cursor: pointer; line-height: 1.5;">
                                <i class="fas fa-user-shield text-royal me-1"></i>
                                I agree to the <strong>Terms & Conditions</strong> and understand that my personal details, valid ID's and Certificates will be kept confidential and used only for verification purposes following data privacy guidelines.
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="mt-5 d-flex justify-content-between border-top pt-4">
                        <a href="enrollment.php" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted">
                            <i class="fas fa-chevron-left me-2"></i> Back
                        </a>
                        
                        <button type="submit" class="btn btn-royal rounded-pill px-5 py-3 shadow fw-bold" id="completeBtn">
                            Complete Enrollment <i class="fas fa-check-circle ms-2"></i>
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

<!-- JavaScript for Privacy Check and Redirection -->
<script>
    // Logic to enable/disable button based on Privacy Checkbox
    const privacyCheck = document.getElementById('privacyCheck');
    const completeBtn = document.getElementById('completeBtn');

    privacyCheck.addEventListener('change', function() {
        if(this.checked) {
            completeBtn.disabled = false;
        } else {
            completeBtn.disabled = true;
        }
    });

    // Redirect Logic
    document.getElementById('confirmRedirect').addEventListener('click', function() {
        window.location.href = '../dashboard/dashboard.php';
    });
</script>

                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include '../includes/footer.php'; 
?>