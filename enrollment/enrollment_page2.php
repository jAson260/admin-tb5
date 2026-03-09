<?php 
// 1. Initialize Session and Database Connection
session_start();
require_once('../db-connect.php'); // Path to your PDO connector
include '../includes/header.php'; 

// 2. LOGIC: Check existing documents to set "LOCKED" state
$uploaded = [
    'psa' => false, 
    'tor' => false, 
    'diploma' => false, 
    'marriage' => false
];

try {
    // We check based on the current logged in student
    $stmt = $pdo->prepare("SELECT PSAPath, TORPath, DiplomaPath, MarriageCertificatePath FROM documents WHERE StudentInfoId = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($doc) {
        if (!empty($doc['PSAPath'])) $uploaded['psa'] = true;
        if (!empty($doc['TORPath'])) $uploaded['tor'] = true;
        if (!empty($doc['DiplomaPath'])) $uploaded['diploma'] = true;
        if (!empty($doc['MarriageCertificatePath'])) $uploaded['marriage'] = true;
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
}
?>

<style>
    /* Senior Dev Adjustment: Institutional Balanced Layout */
    .main-content {
        margin-left: 0 !important; 
        padding-top: 100px !important; 
        padding-left: 80px !important; 
        padding-right: 80px !important;
        transition: none !important;
    }
    .enrollment-container { max-width: 900px; }

    .file-upload-wrapper {
        border: 1px solid #057cf2;
        padding: 20px;
        border-radius: 12px;
        background-color: #fcfcfc;
        transition: 0.3s;
        position: relative;
    }

    /* Style for the "LOCKED / UPLOADED" State */
    .locked-card {
        border-color: #28a745 !important;
        background-color: #f0fff4 !important;
        opacity: 0.85;
    }

    .file-upload-wrapper:hover:not(.locked-card) {
        border-color: var(--royal-blue);
        background-color: #f2f6ff;
    }

    .badge-lock {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .main-content { padding-left: 20px !important; padding-right: 20px !important; }
    }
</style>

<div class="main-content">
    <div class="container-fluid enrollment-container">
        
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12 text-center text-md-start">
                <h3 class="fw-bold text-dark"><i class="fas fa-upload me-2 text-royal"></i>Upload Documents</h3>
                <p class="text-muted small">Final Step of Enrollment. Scanned documents will be reviewed by the administration for approval.</p>
            </div>
        </div>

        <!-- Uploaded Documents Status -->
        <?php 
            $uploadedCount = ($uploaded['psa'] ? 1 : 0) + ($uploaded['tor'] ? 1 : 0) + ($uploaded['diploma'] ? 1 : 0);
            $allComplete = $uploadedCount === 3 && $uploaded['marriage'];
            $totalRequired = 3;
            $progressPercent = ($uploadedCount / $totalRequired) * 100;
        ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background-color: #f8f9fa;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fas fa-file-check me-2 text-success"></i>Document Upload Status
                    </h6>
                    <span class="badge bg-primary"><?php echo $uploadedCount; ?>/<?php echo $totalRequired; ?> Required</span>
                </div>
                
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progressPercent; ?>%" aria-valuenow="<?php echo $uploadedCount; ?>" aria-valuemin="0" aria-valuemax="3"></div>
                </div>

                <div class="row g-2 small">
                    <div class="col-md-6">
                        <span class="d-inline-block">
                            <?php if($uploaded['psa']): ?>
                                <i class="fas fa-check-circle text-success me-1"></i><strong>PSA Birth Certificate</strong>
                            <?php else: ?>
                                <i class="fas fa-circle text-muted me-1" style="font-size: 8px;"></i><span class="text-muted">PSA Birth Certificate</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <span class="d-inline-block">
                            <?php if($uploaded['tor']): ?>
                                <i class="fas fa-check-circle text-success me-1"></i><strong>Transcript of Records</strong>
                            <?php else: ?>
                                <i class="fas fa-circle text-muted me-1" style="font-size: 8px;"></i><span class="text-muted">Transcript of Records</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <span class="d-inline-block">
                            <?php if($uploaded['diploma']): ?>
                                <i class="fas fa-check-circle text-success me-1"></i><strong>Diploma</strong>
                            <?php else: ?>
                                <i class="fas fa-circle text-muted me-1" style="font-size: 8px;"></i><span class="text-muted">Diploma</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="col-md-6">
                        <span class="d-inline-block">
                            <?php if($uploaded['marriage']): ?>
                                <i class="fas fa-check-circle text-success me-1"></i><strong>Marriage Certificate</strong> <span class="badge bg-light text-muted fw-normal" style="font-size: 9px;">Optional</span>
                            <?php else: ?>
                                <i class="fas fa-circle text-muted me-1" style="font-size: 8px;"></i><span class="text-muted">Marriage Certificate</span> <span class="badge bg-light text-muted fw-normal" style="font-size: 9px;">Optional</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-body p-4 p-md-5">
                
                <!-- The Core Upload Form -->
                <form id="uploadForm" enctype="multipart/form-data">
                    
                    <div class="row g-4">
                        
                        <!-- 1. PSA Birth Certificate -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">
                                <i class="fas fa-baby me-2 text-primary"></i>PSA Birth Certificate
                            </label>
                            <div class="file-upload-wrapper <?php echo $uploaded['psa'] ? 'locked-card' : ''; ?>">
                                <?php if($uploaded['psa']): ?>
                                    <span class="badge bg-success badge-lock rounded-pill"><i class="fas fa-check-circle"></i> VERIFIED</span>
                                <?php endif; ?>
                                
                                <input type="file" name="psa_file" class="form-control border-0 bg-transparent" accept=".pdf,.jpg,.jpeg,.png" 
                                       <?php echo $uploaded['psa'] ? 'disabled' : 'required'; ?>>
                                <div class="form-text mt-1" style="font-size: 11px;">
                                    <?php echo $uploaded['psa'] ? "Current document is secured." : "Official PSA copy is required."; ?>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Transcript of Records (TOR) -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">
                                <i class="fas fa-file-alt me-2 text-primary"></i>Transcript of Records (TOR)
                            </label>
                            <div class="file-upload-wrapper <?php echo $uploaded['tor'] ? 'locked-card' : ''; ?>">
                                <?php if($uploaded['tor']): ?>
                                    <span class="badge bg-success badge-lock rounded-pill"><i class="fas fa-check-circle"></i> VERIFIED</span>
                                <?php endif; ?>
                                
                                <input type="file" name="tor_file" class="form-control border-0 bg-transparent" accept=".pdf,.jpg,.jpeg,.png" 
                                       <?php echo $uploaded['tor'] ? 'disabled' : 'required'; ?>>
                                <div class="form-text mt-1" style="font-size: 11px;">
                                    <?php echo $uploaded['tor'] ? "Current document is secured." : "Clear copy of your latest TOR."; ?>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Diploma -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">
                                <i class="fas fa-graduation-cap me-2 text-primary"></i>Diploma
                            </label>
                            <div class="file-upload-wrapper <?php echo $uploaded['diploma'] ? 'locked-card' : ''; ?>">
                                <?php if($uploaded['diploma']): ?>
                                    <span class="badge bg-success badge-lock rounded-pill"><i class="fas fa-check-circle"></i> VERIFIED</span>
                                <?php endif; ?>
                                
                                <input type="file" name="diploma_file" class="form-control border-0 bg-transparent" accept=".pdf,.jpg,.jpeg,.png" 
                                       <?php echo $uploaded['diploma'] ? 'disabled' : 'required'; ?>>
                                <div class="form-text mt-1" style="font-size: 11px;">
                                    <?php echo $uploaded['diploma'] ? "Current document is secured." : "High School or College Diploma."; ?>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Marriage Certificate (Optional) -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold text-dark"><i class="fas fa-ring me-2 text-primary"></i>Marriage Certificate</label>
                                <span class="badge bg-light text-muted fw-normal" style="font-size: 10px;">Optional</span>
                            </div>
                            <div class="file-upload-wrapper <?php echo $uploaded['marriage'] ? 'locked-card' : ''; ?>">
                                <?php if($uploaded['marriage']): ?>
                                    <span class="badge bg-success badge-lock rounded-pill"><i class="fas fa-check-circle"></i> VERIFIED</span>
                                <?php endif; ?>
                                
                                <input type="file" name="marriage_cert" class="form-control border-0 bg-transparent" accept=".pdf,.jpg,.jpeg,.png" 
                                       <?php echo $uploaded['marriage'] ? 'disabled' : ''; ?>>
                                <div class="form-text mt-1" style="font-size: 11px;">
                                    Only for married female applicants.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Note -->
                    <div class="mt-5 border-top pt-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="p-3 rounded-3 bg-light border-start border-primary border-4 mb-3">
                                    <small class="text-muted d-block fw-bold">
                                        <i class="fas fa-info-circle me-1 text-primary"></i> 
                                        Note: Please ensure files are clear. Supported: .pdf, .jpg, .png (Max 5MB)
                                    </small>
                                </div>

                                <!-- DATA PRIVACY SECTION -->
                                <div class="form-check d-flex align-items-start p-3 rounded-3 border shadow-sm transition-all" 
                                     style="background-color: #f0f4ff; border-left: 5px solid var(--royal-blue, #4169E1) !important;">
                                    <input class="form-check-input me-3 ms-0 flex-shrink-0" type="checkbox" id="privacyCheck" 
                                           style="width: 22px; height: 22px; cursor: pointer; border-color: #4169E1;">
                                    <label class="form-check-label small text-dark mb-0" for="privacyCheck" style="cursor: pointer; line-height: 1.5;">
                                        I agree to the <strong>Terms & Conditions</strong> and understand my data will be kept confidential for verification only under data privacy guidelines.
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Warning indicator -->
                        <div class="text-end">
                            <p id="privacyWarning" class="text-danger small mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i> Please check the privacy agreement to proceed.
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Footer Buttons -->
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <a href="enrollment.php" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted text-decoration-none">
                            <i class="fas fa-chevron-left me-2"></i> Back
                        </a>
                        <button type="submit" id="btnCompleteEnrollment" class="btn btn-royal rounded-pill px-5 py-2 fw-bold shadow-sm opacity-50" disabled>
                            Complete Enrollment <i class="fas fa-check-double ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- FIRST SUBMISSION MODAL (3 Documents) -->
<div class="modal fade" id="enrollFirstSubmitModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-file-check text-success" style="font-size: 80px;"></i>
                </div>
                <h3 class="fw-bold text-dark">Initial Documents Received!</h3>
                <p class="text-muted mb-4">Your PSA, TOR, and Diploma have been successfully submitted. You can optionally upload your Marriage Certificate anytime.</p>
                <button type="button" class="btn btn-royal rounded-pill px-5 py-2 fw-bold shadow-sm" data-bs-dismiss="modal">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SUCCESS NOTIFICATION MODAL -->
<div class="modal fade" id="enrollSuccessModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                </div>
                <h3 class="fw-bold text-dark">Enrollment Successfully Sent!</h3>
                <p class="text-muted mb-4">Please wait for Admin approval. Your files have been received by the Registrar.</p>
                <button type="button" class="btn btn-royal rounded-pill px-5 py-2 fw-bold shadow-sm" onclick="window.location.href='../dashboard/dashboard.php'">
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
    const enrollBtn = document.getElementById('btnCompleteEnrollment');
    const warningText = document.getElementById('privacyWarning');
    const firstSubmitModal = new bootstrap.Modal(document.getElementById('enrollFirstSubmitModal'));
    const successModal = new bootstrap.Modal(document.getElementById('enrollSuccessModal'));

    // File inputs
    const psaInput = document.querySelector('input[name="psa_file"]');
    const torInput = document.querySelector('input[name="tor_file"]');
    const diplomaInput = document.querySelector('input[name="diploma_file"]');
    const marriageInput = document.querySelector('input[name="marriage_cert"]');

    // Store PHP upload status (documents already in database)
    const phpUploadStatus = {
        psa: <?php echo json_encode($uploaded['psa']); ?>,
        tor: <?php echo json_encode($uploaded['tor']); ?>,
        diploma: <?php echo json_encode($uploaded['diploma']); ?>,
        marriage: <?php echo json_encode($uploaded['marriage']); ?>
    };

    // Check if document is complete (either from DB or newly selected)
    function isDocumentComplete(type) {
        const input = type === 'psa' ? psaInput : type === 'tor' ? torInput : type === 'diploma' ? diplomaInput : marriageInput;
        return phpUploadStatus[type] || input.files.length > 0;
    }

    // Check if all 3 required documents are available
    function areRequiredDocumentsAvailable() {
        return isDocumentComplete('psa') && 
               isDocumentComplete('tor') && 
               isDocumentComplete('diploma');
    }

    // Check if all 4 documents are complete
    function areAllDocumentsComplete() {
        return isDocumentComplete('psa') && 
               isDocumentComplete('tor') && 
               isDocumentComplete('diploma') && 
               isDocumentComplete('marriage');
    }

    // Lock the 3 required inputs
    function lockRequiredInputs() {
        if(phpUploadStatus.psa) psaInput.disabled = true;
        if(phpUploadStatus.tor) torInput.disabled = true;
        if(phpUploadStatus.diploma) diplomaInput.disabled = true;
    }

    // Disable all form inputs
    function disableAllFormInputs() {
        psaInput.disabled = true;
        torInput.disabled = true;
        diplomaInput.disabled = true;
        marriageInput.disabled = true;
        privacyCheck.disabled = true;
    }

    // Update button state based on document completion
    function updateButtonState() {
        const privacyChecked = privacyCheck.checked;
        const requiredAvailable = areRequiredDocumentsAvailable();
        const allDocumentsAvailable = areAllDocumentsComplete();
        const marriageSelected = marriageInput.files.length > 0;
        
        // If all 4 documents complete AND privacy checked, enable to submit final
        if(allDocumentsAvailable && privacyChecked) {
            enrollBtn.disabled = false;
            enrollBtn.classList.remove('opacity-50');
            warningText.classList.add('d-none');
        }
        // If 3 required documents available AND marriage cert is added AND privacy checked
        else if(requiredAvailable && marriageSelected && privacyChecked) {
            enrollBtn.disabled = false;
            enrollBtn.classList.remove('opacity-50');
            warningText.classList.add('d-none');
        }
        // If only 3 required documents available (no marriage yet) AND privacy checked
        else if(requiredAvailable && !marriageSelected && privacyChecked) {
            enrollBtn.disabled = false;
            enrollBtn.classList.remove('opacity-50');
            warningText.classList.add('d-none');
        }
        // Otherwise disable
        else {
            enrollBtn.disabled = true;
            enrollBtn.classList.add('opacity-50');
            if(!privacyChecked) {
                warningText.classList.remove('d-none');
            } else {
                warningText.classList.add('d-none');
            }
        }
    }

    // Initialize on page load
    lockRequiredInputs();
    
    // If all 4 documents already complete, lock everything immediately
    if(areAllDocumentsComplete()) {
        disableAllFormInputs();
        enrollBtn.disabled = true;
        enrollBtn.classList.add('opacity-50');
        enrollBtn.innerHTML = 'You Have Already uploaded your documents <i class="fas fa-lock ms-2"></i>';
        warningText.classList.add('d-none');
    } else {
        updateButtonState();
    }

    // PRIVACY CHECKBOX LOGIC
    privacyCheck.addEventListener('change', function() {
        updateButtonState();
    });

    // FILE INPUT CHANGE LISTENERS
    psaInput.addEventListener('change', updateButtonState);
    torInput.addEventListener('change', updateButtonState);
    diplomaInput.addEventListener('change', updateButtonState);
    marriageInput.addEventListener('change', updateButtonState);

    // AJAX SUBMISSION
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Loading state
        enrollBtn.disabled = true;
        enrollBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i>Uploading...';

        const formData = new FormData(this);

        // Submit to handler
        fetch('upload-documents-handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update PHP status after successful submission
                if(psaInput.files.length > 0) phpUploadStatus.psa = true;
                if(torInput.files.length > 0) phpUploadStatus.tor = true;
                if(diplomaInput.files.length > 0) phpUploadStatus.diploma = true;
                if(marriageInput.files.length > 0) phpUploadStatus.marriage = true;
                
                // Check if all 4 documents are now complete
                if(areAllDocumentsComplete()) {
                    // All documents complete - show modal (user clicks OK to redirect)
                    successModal.show();
                    // Disable form after complete submission
                    disableAllFormInputs();
                    enrollBtn.disabled = true;
                    enrollBtn.classList.add('opacity-50');
                    enrollBtn.innerHTML = 'You Have Already uploaded your documents <i class="fas fa-lock ms-2"></i>';
                } else {
                    // Only required documents submitted
                    // Lock the uploaded required inputs
                    lockRequiredInputs();
                    
                    // Update PHP status for the locked documents
                    phpUploadStatus.psa = true;
                    phpUploadStatus.tor = true;
                    phpUploadStatus.diploma = true;
                    
                    // Reset button and show success message
                    enrollBtn.disabled = true;
                    enrollBtn.classList.add('opacity-50');
                    enrollBtn.innerHTML = 'Complete Enrollment <i class="fas fa-check-double ms-2"></i>';
                    
                    // Show first submission modal
                    firstSubmitModal.show();
                    
                    // Clear file inputs for next submission
                    psaInput.value = '';
                    torInput.value = '';
                    diplomaInput.value = '';
                    marriageInput.value = '';
                    
                    updateButtonState();
                }
            } else {
                alert('Upload failed: ' + data.message);
                // Reset button if failed
                enrollBtn.disabled = false;
                enrollBtn.innerHTML = 'Complete Enrollment <i class="fas fa-check-double ms-2"></i>';
                updateButtonState();
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('A system error occurred. Please refresh and try again.');
            enrollBtn.disabled = false;
            enrollBtn.innerHTML = 'Complete Enrollment <i class="fas fa-check-double ms-2"></i>';
            updateButtonState();
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>