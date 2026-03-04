<?php
session_start();

// Authentication Check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../db-connect.php'); 

$showSuccessModal = false;
$message = "";

// --- BACKEND LOGIC: Processing with Filename Detection (BEFORE ANY HTML OUTPUT) ---
if (isset($_POST['modal_upload_submit'])) {
    $fieldName = $_POST['field_name']; 
    $targetDir = "../uploads/documents/";
    if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }
    
    $file = $_FILES["modalFile"];
    $originalName = strtoupper($file['name']);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // START: SENIOR DEV AUTOMATIC DETECTION & VALIDATION LOGIC
    $isValidType = false;

    if (in_array($fieldName, ["TORPath", "DiplomaPath", "Form137Path", "ALSCertificatePath"])) {
        if (strpos($originalName, "TOR") !== false || strpos($originalName, "DIPLOMA") !== false || strpos($originalName, "137") !== false || strpos($originalName, "ALS") !== false) {
            $isValidType = true;
        } else {
            $_SESSION['upload_error'] = "Upload Rejected: The file for Academic Records must have 'TOR', 'DIPLOMA', '137', or 'ALS' in its name.";
        }
    } elseif ($fieldName == "PSAPath") {
        if (strpos($originalName, "PSA") !== false || strpos($originalName, "BIRTH") !== false) {
            $isValidType = true;
        } else {
            $_SESSION['upload_error'] = "Upload Rejected: Birth identity files must contain 'PSA' or 'BIRTH' in the filename.";
        }
    } elseif ($fieldName == "MarriageCertificatePath") {
        if (strpos($originalName, "MARRIAGE") !== false) {
            $isValidType = true;
        } else {
            $_SESSION['upload_error'] = "Upload Rejected: Marriage credentials must contain the word 'MARRIAGE' in the filename.";
        }
    } elseif ($fieldName == "BarangayIndigencyPath") {
        if (strpos($originalName, "INDIGENCY") !== false || strpos($originalName, "BARANGAY") !== false) {
            $isValidType = true;
        } else {
            $_SESSION['upload_error'] = "Upload Rejected: Barangay Indigency must contain 'INDIGENCY' or 'BARANGAY' in the filename.";
        }
    } elseif ($fieldName == "CertificateOfResidencyPath") {
        if (strpos($originalName, "RESIDENCY") !== false || strpos($originalName, "RESIDENCE") !== false) {
            $isValidType = true;
        } else {
            $_SESSION['upload_error'] = "Upload Rejected: Certificate of Residency must contain 'RESIDENCY' or 'RESIDENCE' in the filename.";
        }
    }

    // Process upload only if the file passes naming verification
    if ($isValidType) {
        if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
            try {
                // Delete old file if exists
                $checkStmt = $pdo->prepare("SELECT $fieldName FROM documents WHERE StudentInfoId = ?");
                $checkStmt->execute([$_SESSION['user_id']]);
                $oldData = $checkStmt->fetch();
                
                if ($oldData && !empty($oldData[$fieldName])) {
                    $oldFile = $targetDir . $oldData[$fieldName];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                
                // Create new filename
                $fileName = strtolower(str_replace(['Path', '_'], '', $fieldName)) . '_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
                
                if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
                    // Get the status field name
                    $statusField = str_replace('Path', 'Status', $fieldName);
                    
                    // Update document and reset status to pending
                    $updateSql = "UPDATE documents SET $fieldName = ?, $statusField = 'pending' WHERE StudentInfoId = ?";
                    $stmt = $pdo->prepare($updateSql);
                    if ($stmt->execute([$fileName, $_SESSION['user_id']])) {
                        // Set success flag in session and redirect
                        $_SESSION['upload_success'] = true;
                        header('Location: upload.php');
                        exit;
                    } else {
                        $_SESSION['upload_error'] = "Database update failed.";
                    }
                } else { 
                    $_SESSION['upload_error'] = "Upload failed. Check permissions."; 
                }
            } catch(PDOException $e) { 
                error_log("Database error: " . $e->getMessage());
                $_SESSION['upload_error'] = "Database Sync Error."; 
            }
        } else { 
            $_SESSION['upload_error'] = "Invalid file type. (PDF/JPG/PNG only)"; 
        }
    }
    
    // If there was an error, redirect to show it
    if (isset($_SESSION['upload_error'])) {
        header('Location: upload.php');
        exit;
    }
}

// Check if redirected after successful upload
if (isset($_SESSION['upload_success'])) {
    $showSuccessModal = true;
    unset($_SESSION['upload_success']);
}

// Check if there's an error message
if (isset($_SESSION['upload_error'])) {
    $message = $_SESSION['upload_error'];
    unset($_SESSION['upload_error']);
}

// --- FETCH DATA FROM DATABASE ---
try {
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE StudentInfoId = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $docData = $stmt->fetch();
    
    // Create document record if doesn't exist
    if (!$docData) {
        $insertStmt = $pdo->prepare("INSERT INTO documents (StudentInfoId) VALUES (?)");
        $insertStmt->execute([$_SESSION['user_id']]);
        
        $stmt->execute([$_SESSION['user_id']]);
        $docData = $stmt->fetch();
    }
} catch(PDOException $e) { 
    error_log("Connection error: " . $e->getMessage());
    $message = "Connection error."; 
}

$groupedRequirements = [
    [
        "display_name" => "Academic Record (TOR / DIPLOMA / Form 137 / ALS)",
        "fields" => ["TORPath", "DiplomaPath", "Form137Path", "ALSCertificatePath"],
        "primary_field" => "TORPath", 
        "status_fields" => ["TORStatus", "DiplomaStatus", "Form137Status", "ALSCertificateStatus"], 
        "icon" => "fa-graduation-cap"
    ],
    [
        "display_name" => "Birth Identity (PSA / BIRTH CERTIFICATE)",
        "fields" => ["PSAPath"],
        "primary_field" => "PSAPath",
        "status_fields" => ["PSAStatus"],
        "icon" => "fa-baby"
    ],
    [
        "display_name" => "Status Credential (MARRIAGE CERTIFICATE)",
        "fields" => ["MarriageCertificatePath"],
        "primary_field" => "MarriageCertificatePath",
        "status_fields" => ["MarriageCertificateStatus"],
        "icon" => "fa-ring"
    ],
    [
        "display_name" => "Barangay Documents (Indigency / Residency)",
        "fields" => ["BarangayIndigencyPath", "CertificateOfResidencyPath"],
        "primary_field" => "BarangayIndigencyPath",
        "status_fields" => ["BarangayIndigencyStatus", "CertificateOfResidencyStatus"],
        "icon" => "fa-home"
    ]
];

// NOW include HTML files AFTER all processing is complete
include '../includes/header.php'; 
include '../includes/sidebar.php'; 
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4 text-center">
            <div class="col-12">
                <h2 class="fw-bold text-dark mb-1"><i class="fas fa-file-shield me-2 text-royal"></i>DOCUMENT MANAGEMENT</h2>
                <p class="text-muted small">Verification Logic: Filenames must match document category.</p>
                <div class="mx-auto mt-2" style="width: 60px; height: 3px; background-color: var(--royal-blue); border-radius: 2px;"></div>
            </div>
        </div>

        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-white p-3 shadow-sm rounded-3">
                <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-royal small fw-bold">Dashboard</a></li>
                <li class="breadcrumb-item active small">Official Requirements Checklist</li>
            </ol>
        </nav>

        <?php if($message != ""): ?>
            <div class="alert alert-danger shadow-sm border-0 small py-3 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
                    <div><strong>Detection Error:</strong> <?php echo htmlspecialchars($message); ?></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- UNIFIED GROUPED TABLE -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-royal"><i class="fas fa-list-check me-2"></i>Documents</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="docsTable">
                        <thead class="bg-light text-uppercase">
                            <tr style="font-size: 11px;">
                                <th class="ps-4" style="width: 45%;">Type of Documents</th>
                                <th>Overall Status</th>
                                <th class="text-center">View</th>
                                <th class="text-center">Resubmit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupedRequirements as $rowGroup): 
                                $fileToView = "";
                                $hasRejected = false;
                                $hasUploaded = false;
                                
                                foreach ($rowGroup['fields'] as $field) {
                                    if (!empty($docData[$field])) {
                                        $fileToView = $docData[$field];
                                        $hasUploaded = true;
                                        break; 
                                    }
                                }

                                $groupStatus = "Not Uploaded";
                                foreach ($rowGroup['status_fields'] as $sf) {
                                    $curr = strtolower($docData[$sf] ?? '');
                                    if ($curr == 'rejected') { 
                                        $groupStatus = "Rejected"; 
                                        $hasRejected = true;
                                        break; 
                                    }
                                    if ($curr == 'pending') { $groupStatus = "Pending"; }
                                    if ($curr == 'approved' && $groupStatus != "Pending") { $groupStatus = "Approved"; }
                                }

                                $badge = ($groupStatus == 'Approved') ? 'bg-success' : (($groupStatus == 'Rejected') ? 'bg-danger' : (($groupStatus == 'Pending') ? 'bg-warning text-dark' : 'bg-secondary'));
                            ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-2 rounded text-royal me-3 shadow-sm"><i class="fas <?php echo $rowGroup['icon']; ?> fa-fw"></i></div>
                                        <div>
                                            <span class="fw-bold text-dark d-block" style="font-size: 13px;"><?php echo $rowGroup['display_name']; ?></span>
                                            <small class="text-muted italic"><?php echo (!empty($fileToView)) ? $fileToView : "None selected"; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2 shadow-sm text-uppercase" style="font-size: 9px;"><?php echo $groupStatus; ?></span></td>
                                <td class="text-center">
                                    <?php if(!empty($fileToView)): ?>
                                        <button class="btn btn-sm btn-link text-royal p-0" onclick="viewDocument('../uploads/documents/<?php echo $fileToView; ?>', '<?php echo addslashes($rowGroup['display_name']); ?>')"><i class="fas fa-eye fa-lg"></i></button>
                                    <?php else: ?>
                                        <i class="fas fa-eye-slash text-muted small opacity-50"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($hasRejected): ?>
                                        <button class="btn btn-sm btn-outline-danger border-0 rounded-circle shadow-sm" onclick="openUploadModal('<?php echo addslashes($rowGroup['display_name']); ?>', 0, '<?php echo $rowGroup['primary_field']; ?>')" title="Resubmit rejected document">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    <?php elseif (!$hasUploaded): ?>
                                        <button class="btn btn-sm btn-outline-primary border-0 rounded-circle shadow-sm" onclick="openUploadModal('<?php echo addslashes($rowGroup['display_name']); ?>', 0, '<?php echo $rowGroup['primary_field']; ?>')" title="Upload document">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    <?php elseif ($groupStatus == 'Approved'): ?>
                                        <span class="text-success" title="Approved">
                                            <i class="fas fa-check-circle fa-lg"></i>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-warning" title="Pending approval">
                                            <i class="fas fa-clock fa-lg"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- UPLOAD MODAL -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Resubmit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body px-4">
                    <input type="hidden" name="field_name" id="modalFieldName">
                    <div class="p-4 border-2 border-dashed rounded-3 bg-light text-center">
                        <i class="fas fa-cloud-upload-alt text-royal fa-3x mb-3"></i>
                        <p class="mb-2 fw-bold">Choose file to upload</p>
                        <p class="text-muted small mb-3">PDF, JPG, JPEG, or PNG (Max 5MB)</p>
                        <input type="file" name="modalFile" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Your document will replace the existing one and will be sent to admin for approval.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="modal_upload_submit" class="btn btn-royal rounded-pill px-5 shadow">Submit to Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VIEWER MODAL -->
<div class="modal fade" id="viewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 overflow-hidden shadow-lg">
            <div class="modal-header p-3 border-bottom d-flex justify-content-between">
                <h6 class="modal-title fw-bold mb-0" id="viewerTitle">Preview</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-dark d-flex align-items-center justify-content-center" style="min-height:550px;">
                <div id="viewerContent" class="w-100 text-center"></div>
            </div>
        </div>
    </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal fade" id="uploadSuccessModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5 text-center p-5">
            <i class="fas fa-check-circle text-success mb-3" style="font-size: 60px;"></i>
            <h4 class="fw-bold">Request Sent!</h4>
            <p class="text-muted px-4">Successfully sent request to Admin. Wait for approval.</p>
            <button onclick="closeSuccessModal()" class="btn btn-royal rounded-pill px-5">OK</button>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
let successModal;

document.addEventListener("DOMContentLoaded", function() {
    const uModal = new bootstrap.Modal(document.getElementById('uploadModal'));
    const vModal = new bootstrap.Modal(document.getElementById('viewerModal'));
    successModal = new bootstrap.Modal(document.getElementById('uploadSuccessModal'));
    
    window.openUploadModal = (name, id, fieldName) => { 
        document.getElementById('modalTitle').innerText = 'Upload ' + name; 
        document.getElementById('modalFieldName').value = fieldName; 
        uModal.show(); 
    };
    
    window.viewDocument = (path, name) => { 
        document.getElementById('viewerTitle').innerText = name; 
        const ext = path.split('.').pop().toLowerCase(); 
        const content = (ext === 'pdf') ? `<iframe src="${path}" width="100%" height="600px" style="border:none;"></iframe>` : `<img src="${path}" class="img-fluid p-3">`; 
        document.getElementById('viewerContent').innerHTML = content; 
        vModal.show(); 
    };
    
    window.closeSuccessModal = function() {
        successModal.hide();
    };
    
    // Show success modal if flag is set
    <?php if ($showSuccessModal): ?>
        successModal.show();
    <?php endif; ?>
});
</script>
