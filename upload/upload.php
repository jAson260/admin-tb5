<?php
// filepath: c:\laragon\www\admin-tb5\upload\upload.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../db-connect.php');

// 1. Unified path detection for nested folders
include '../includes/header.php'; 
include '../includes/sidebar.php'; 

// Variables for Post-Submission logic
$showSuccessModal = false;
$message = "";

// --- BACKEND LOGIC: Handling Resubmission ---
if (isset($_POST['modal_upload_submit'])) {
    $docID = $_POST['doc_id'];
    $docName = $_POST['doc_name_label'];
    $fieldName = $_POST['field_name']; // Which field to update
    
    $targetDir = "../uploads/documents/";
    if (!file_exists($targetDir)) { 
        mkdir($targetDir, 0777, true); 
    }
    
    // Allowed file types
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    
    $file = $_FILES["modalFile"];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Validate file size
        if ($file['size'] > $maxFileSize) {
            $message = "File is too large. Maximum size is 5MB.";
        } else {
            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (in_array($mimeType, $allowedTypes) && in_array($extension, $allowedExtensions)) {
                // Generate unique filename
                $fileName = strtolower(str_replace(' ', '_', $fieldName)) . '_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
                $targetPath = $targetDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    try {
                        // Update database - set status back to pending
                        $updateSql = "UPDATE documents SET {$fieldName} = ?, {$fieldName}Status = 'pending' WHERE Id = ? AND StudentInfoId = ?";
                        $stmt = $pdo->prepare($updateSql);
                        
                        if ($stmt->execute([$fileName, $docID, $_SESSION['user_id']])) {
                            $showSuccessModal = true;
                        } else {
                            $message = "Failed to update database record.";
                        }
                    } catch(PDOException $e) {
                        $message = "Database error: " . $e->getMessage();
                    }
                } else {
                    $message = "Failed to upload file. Please check server permissions.";
                }
            } else {
                $message = "Invalid file type. Only PDF, JPG, and PNG are allowed.";
            }
        }
    } else {
        $message = "Upload error: " . $file['error'];
    }
}

// --- FETCH DATA FROM DATABASE ---
$uploadedDocs = [];

try {
    $stmt = $pdo->prepare("
        SELECT 
            Id,
            PSAPath,
            PSAStatus,
            TORPath,
            DiplomaPath,
            MarriageCertificatePath,
            Form137Path,
            ALSCertificatePath,
            BarangayIndigencyPath,
            CertificateOfResidencyPath,
            Form137Status,
            MarriageCertificateStatus,
            ALSCertificateStatus,
            BarangayIndigencyStatus,
            CertificateOfResidencyStatus,
            UploadedAt
        FROM documents 
        WHERE StudentInfoId = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $docData = $stmt->fetch();
    
    if ($docData) {
        // Map database fields to display array
        $uploadedDocs = [
            [
                "id" => $docData['Id'],
                "doc_name" => "PSA Birth Certificate",
                "field_name" => "PSAPath",
                "file" => $docData['PSAPath'] ?? 'None',
                "file_path" => $docData['PSAPath'] ? "../uploads/documents/" . $docData['PSAPath'] : "",
                "date" => $docData['UploadedAt'] ?? "-",
                "status" => $docData['PSAStatus'] ?? 'Not Uploaded'
            ],
            [
                "id" => $docData['Id'],
                "doc_name" => "Transcript of Records (TOR)",
                "field_name" => "TORPath",
                "file" => $docData['TORPath'] ?? 'None',
                "file_path" => $docData['TORPath'] ? "../uploads/documents/" . $docData['TORPath'] : "",
                "date" => $docData['UploadedAt'] ?? "-",
                "status" => $docData['PSAStatus'] ?? 'Not Uploaded'
            ],
            [
                "id" => $docData['Id'],
                "doc_name" => "Diploma",
                "field_name" => "DiplomaPath",
                "file" => $docData['DiplomaPath'] ?? 'None',
                "file_path" => $docData['DiplomaPath'] ? "../uploads/documents/" . $docData['DiplomaPath'] : "",
                "date" => $docData['UploadedAt'] ?? "-",
                "status" => $docData['PSAStatus'] ?? 'Not Uploaded'
            ],
            [
                "id" => $docData['Id'],
                "doc_name" => "Marriage Certificate",
                "field_name" => "MarriageCertificatePath",
                "file" => $docData['MarriageCertificatePath'] ?? 'None',
                "file_path" => $docData['MarriageCertificatePath'] ? "../uploads/documents/" . $docData['MarriageCertificatePath'] : "",
                "date" => $docData['UploadedAt'] ?? "-",
                "status" => $docData['MarriageCertificateStatus'] ?? 'Not Uploaded'
            ],
            [
                "id" => $docData['Id'],
                "doc_name" => "Form 137",
                "field_name" => "Form137Path",
                "file" => $docData['Form137Path'] ?? 'None',
                "file_path" => $docData['Form137Path'] ? "../uploads/documents/" . $docData['Form137Path'] : "",
                "date" => $docData['UploadedAt'] ?? "-",
                "status" => $docData['Form137Status'] ?? 'Not Uploaded'
            ],
            [
                "id" => $docData['Id'],
                "doc_name" => "ALS Certificate",
                "field_name" => "ALSCertificatePath",
                "file" => $docData['ALSCertificatePath'] ?? 'None',
                "file_path" => $docData['ALSCertificatePath'] ? "../uploads/documents/" . $docData['ALSCertificatePath'] : "",
                "date" => $docData['UploadedAt'] ?? "-",
                "status" => $docData['ALSCertificateStatus'] ?? 'Not Uploaded'
            ],
            [
                "id" => $docData['Id'],
                "doc_name" => "Barangay Indigency Certificate",
                "field_name" => "BarangayIndigencyPath",
                "file" => $docData['BarangayIndigencyPath'] ?? 'None',
                "file_path" => $docData['BarangayIndigencyPath'] ? "../uploads/documents/" . $docData['BarangayIndigencyPath'] : "",
                "date" => $docData['UploadedAt'] ?? "-",
                "status" => $docData['BarangayIndigencyStatus'] ?? 'Not Uploaded'
            ],
            [
                "id" => $docData['Id'],
                "doc_name" => "Certificate of Residency",
                "field_name" => "CertificateOfResidencyPath",
                "file" => $docData['CertificateOfResidencyPath'] ?? 'None',
                "file_path" => $docData['CertificateOfResidencyPath'] ? "../uploads/documents/" . $docData['CertificateOfResidencyPath'] : "",
                "date" => $docData['UploadedAt'] ?? "-",
                "status" => $docData['CertificateOfResidencyStatus'] ?? 'Not Uploaded'
            ]
        ];
    } else {
        // No documents uploaded yet - show empty state
        $uploadedDocs = [
            ["id" => 0, "doc_name" => "PSA Birth Certificate", "field_name" => "PSAPath", "file" => "None", "file_path" => "", "date" => "-", "status" => "Not Uploaded"],
            ["id" => 0, "doc_name" => "Transcript of Records (TOR)", "field_name" => "TORPath", "file" => "None", "file_path" => "", "date" => "-", "status" => "Not Uploaded"],
            ["id" => 0, "doc_name" => "Diploma", "field_name" => "DiplomaPath", "file" => "None", "file_path" => "", "date" => "-", "status" => "Not Uploaded"],
            ["id" => 0, "doc_name" => "Marriage Certificate", "field_name" => "MarriageCertificatePath", "file" => "None", "file_path" => "", "date" => "-", "status" => "Not Uploaded"],
            ["id" => 0, "doc_name" => "Form 137", "field_name" => "Form137Path", "file" => "None", "file_path" => "", "date" => "-", "status" => "Not Uploaded"],
            ["id" => 0, "doc_name" => "ALS Certificate", "field_name" => "ALSCertificatePath", "file" => "None", "file_path" => "", "date" => "-", "status" => "Not Uploaded"],
            ["id" => 0, "doc_name" => "Barangay Indigency", "field_name" => "BarangayIndigencyPath", "file" => "None", "file_path" => "", "date" => "-", "status" => "Not Uploaded"],
            ["id" => 0, "doc_name" => "Certificate of Residency", "field_name" => "CertificateOfResidencyPath", "file" => "None", "file_path" => "", "date" => "-", "status" => "Not Uploaded"]
        ];
    }
    
} catch(PDOException $e) {
    $message = "Error loading documents: " . $e->getMessage();
}
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- HEADER SECTION -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-dark mb-1"><i class="fas fa-file-shield me-2 text-royal"></i>DOCUMENT MANAGEMENT</h2>
                <p class="text-muted small text-uppercase">The Big Five Training & Assessment Center Inc.</p>
                <div class="mx-auto mt-2" style="width: 60px; height: 3px; background-color: var(--royal-blue); border-radius: 2px;"></div>
            </div>
        </div>

        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-white p-3 shadow-sm rounded-3">
                <li class="breadcrumb-item"><a href="../dashboard/dashboard.php" class="text-decoration-none text-royal small fw-bold">Dashboard</a></li>
                <li class="breadcrumb-item active small">Documents you Uploaded</li>
            </ol>
        </nav>

        <?php if($message != ""): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm small" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-royal"><i class="fas fa-list-check me-2"></i>Official Requirements Checklist</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase">
                            <tr style="font-size: 11px;">
                                <th class="ps-4">Requirement Name</th>
                                <th>Date Modified</th>
                                <th>Status</th>
                                <th class="text-center">View</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($uploadedDocs as $doc): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light p-2 rounded me-3 text-royal"><i class="fas fa-file-signature"></i></div>
                                        <div>
                                            <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($doc['doc_name']); ?></span>
                                            <small class="text-muted italic"><?php echo htmlspecialchars($doc['file']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    <?php echo ($doc['date'] != "-") ? date("M d, Y", strtotime($doc['date'])) : "-"; ?>
                                </td>
                                <td>
                                    <?php 
                                        $statusUpper = ucfirst(strtolower($doc['status']));
                                        $badge = ($statusUpper == 'Approved') ? 'bg-success' : 
                                                (($statusUpper == 'Rejected') ? 'bg-danger' : 
                                                (($statusUpper == 'Not Uploaded') ? 'bg-secondary' : 'bg-warning text-dark'));
                                    ?>
                                    <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2 shadow-sm" style="font-size: 10px;"><?php echo htmlspecialchars($statusUpper); ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if($doc['status'] != 'Not Uploaded' && !empty($doc['file_path'])): ?>
                                        <button class="btn btn-sm btn-link text-royal p-0" onclick="viewDocument('<?php echo htmlspecialchars($doc['file_path'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($doc['doc_name'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-eye fa-lg"></i>
                                        </button>
                                    <?php else: ?>
                                        <i class="fas fa-eye-slash text-muted"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($doc['id'] > 0): ?>
                                        <button class="btn btn-sm btn-outline-primary border-0 rounded-circle p-2" 
                                                onclick="openUploadModal('<?php echo htmlspecialchars($doc['doc_name'], ENT_QUOTES); ?>', <?php echo $doc['id']; ?>, '<?php echo htmlspecialchars($doc['field_name'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary border-0 rounded-circle p-2" disabled>
                                            <i class="fas fa-ban"></i>
                                        </button>
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
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold" id="modalTitle">Resubmit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body px-4">
                    <input type="hidden" name="doc_id" id="modalDocID">
                    <input type="hidden" name="doc_name_label" id="modalDocNameLabel">
                    <input type="hidden" name="field_name" id="modalFieldName">
                    <div class="p-4 border-2 border-dashed rounded-3 bg-light text-center">
                        <i class="fas fa-cloud-upload-alt text-royal fa-3x mb-3"></i>
                        <input type="file" name="modalFile" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="form-text mt-2">Max size: 5MB. Formats: PDF, JPG, PNG</div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="modal_upload_submit" class="btn btn-royal rounded-pill px-5">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- VIEWER MODAL -->
<div class="modal fade" id="viewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden border-0">
            <div class="modal-header p-3 border-bottom">
                <h6 class="fw-bold mb-0" id="viewerTitle"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-dark d-flex justify-content-center" style="min-height: 500px;">
                <div id="viewerContent" class="w-100 text-center"></div>
            </div>
        </div>
    </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal fade" id="uploadSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">
                <i class="fas fa-check-circle text-success mb-4" style="font-size: 50px;"></i>
                <h4 class="fw-bold">Success!</h4>
                <p class="text-muted small">Successfully sent request to Admin. Wait for the admin to approve the document/file.</p>
                <button type="button" class="btn btn-royal rounded-pill px-5" onclick="window.location.reload()">OK</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const uModal = new bootstrap.Modal(document.getElementById('uploadModal'));
        const vModal = new bootstrap.Modal(document.getElementById('viewerModal'));
        const sModal = new bootstrap.Modal(document.getElementById('uploadSuccessModal'));

        window.openUploadModal = function(name, id, fieldName) {
            document.getElementById('modalTitle').innerText = 'Resubmit ' + name;
            document.getElementById('modalDocNameLabel').value = name;
            document.getElementById('modalDocID').value = id;
            document.getElementById('modalFieldName').value = fieldName;
            uModal.show();
        };

        window.viewDocument = function(path, name) {
            const extension = path.split('.').pop().toLowerCase();
            document.getElementById('viewerTitle').innerText = "Viewing: " + name;
            const content = (extension === 'pdf') 
                ? `<iframe src="${path}" width="100%" height="600px" style="border:none;"></iframe>` 
                : `<img src="${path}" class="img-fluid p-3" style="max-height: 600px;">`;
            document.getElementById('viewerContent').innerHTML = content;
            vModal.show();
        };

        <?php if ($showSuccessModal): ?>
            sModal.show();
        <?php endif; ?>
    });
</script>

<style>.border-dashed { border-style: dashed !important; border-color: #d1d9e6 !important; }</style>    