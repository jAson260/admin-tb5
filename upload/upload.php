<?php
// filepath: c:\laragon\www\admin-tb5\upload\upload.php
session_start();

// Authentication Check
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../db-connect.php'); // Your tb5enrollmentsystemdb connection
include '../includes/header.php'; 
include '../includes/sidebar.php'; 

$showSuccessModal = false;
$message = "";

// --- BACKEND LOGIC (Rest of code preserved as requested) ---
if (isset($_POST['modal_upload_submit'])) {
    $fieldName = $_POST['field_name']; 
    $targetDir = "../uploads/documents/";
    if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }
    
    $file = $_FILES["modalFile"];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
        $fileName = strtolower($fieldName) . '_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
        
        if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
            try {
                $updateSql = "UPDATE documents SET $fieldName = ?, {$fieldName}Status = 'pending' WHERE StudentInfoId = ?";
                $stmt = $pdo->prepare($updateSql);
                if ($stmt->execute([$fileName, $_SESSION['user_id']])) {
                    $showSuccessModal = true;
                }
            } catch(PDOException $e) { $message = "Database Sync Error."; }
        } else { $message = "Upload failed. Check permissions."; }
    } else { $message = "Invalid file type. (PDF/JPG/PNG only)"; }
}

// --- FETCH ALL DOCUMENTS FOR THE LOGGED IN USER ---
try {
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE StudentInfoId = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $docData = $stmt->fetch();
} catch(PDOException $e) { $message = "Connection error."; }

// --- REFINED GROUPING LOGIC (The core task for today) ---
$groupedRequirements = [
    [
        "display_name" => "Academic Record (TOR / DIPLOMA / Form 137 / ALS)",
        "fields" => ["TORPath", "DiplomaPath", "Form137Path", "ALSCertificatePath"],
        "primary_field" => "TORPath", // Primary field for "Update" button
        "status_fields" => ["PSAStatus", "Form137Status", "ALSCertificateStatus"], // Common statuses checked
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
    ]
];
?>

<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4 text-center">
            <div class="col-12">
                <h2 class="fw-bold text-dark mb-1"><i class="fas fa-file-shield me-2 text-royal"></i>DOCUMENT MANAGEMENT</h2>
                <p class="text-muted small">Grouped Requirements Repository & Verification</p>
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
            <div class="alert alert-danger shadow-sm border-0 small" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- UNIFIED GROUPED TABLE -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-royal"><i class="fas fa-list-check me-2"></i>Required Document Groupings</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="docsTable">
                        <thead class="bg-light text-uppercase">
                            <tr style="font-size: 11px;">
                                <th class="ps-4" style="width: 45%;">Grouping</th>
                                <th>Overall Status</th>
                                <th class="text-center">View</th>
                                <th class="text-center">Resubmit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupedRequirements as $rowGroup): 
                                // Aggregate file data for viewing
                                $fileToView = "";
                                foreach ($rowGroup['fields'] as $field) {
                                    if (!empty($docData[$field])) {
                                        $fileToView = $docData[$field];
                                        break; 
                                    }
                                }

                                // Aggregate status for the group badge
                                $groupStatus = "Not Uploaded";
                                foreach ($rowGroup['status_fields'] as $sf) {
                                    $curr = strtolower($docData[$sf] ?? '');
                                    if ($curr == 'rejected') { $groupStatus = "Rejected"; break; }
                                    if ($curr == 'pending') { $groupStatus = "Pending"; }
                                    if ($curr == 'approved' && $groupStatus != "Pending") { $groupStatus = "Approved"; }
                                }

                                // Style calculation
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
                                        <button class="btn btn-sm btn-link text-royal p-0" onclick="viewDocument('../uploads/documents/<?php echo $fileToView; ?>', '<?php echo $rowGroup['display_name']; ?>')"><i class="fas fa-eye fa-lg"></i></button>
                                    <?php else: ?>
                                        <i class="fas fa-eye-slash text-muted small opacity-50"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary border-0 rounded-circle shadow-sm" onclick="openUploadModal('<?php echo $rowGroup['display_name']; ?>', 0, '<?php echo $rowGroup['primary_field']; ?>')"><i class="fas fa-sync-alt"></i></button>
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

<!-- (REMAINDER OF THE CODE IS UNCHANGED AS REQUESTED: MODALS AND SCRIPTS) -->

<!-- MODAL FOR RESUBMITTING -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow-lg"><div class="modal-header border-0 p-4 pb-0"><h5 class="modal-title fw-bold" id="modalTitle">Resubmit</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form action="upload.php" method="POST" enctype="multipart/form-data"><div class="modal-body px-4"><input type="hidden" name="field_name" id="modalFieldName"><div class="p-4 border-2 border-dashed rounded-3 bg-light text-center"><i class="fas fa-cloud-upload-alt text-royal fa-3x mb-3"></i><input type="file" name="modalFile" class="form-control" required></div></div><div class="modal-footer border-0 p-4"><button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button><button type="submit" name="modal_upload_submit" class="btn btn-royal rounded-pill px-5 shadow">Submit to Admin</button></div></form></div></div></div>
<div class="modal fade" id="viewerModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content rounded-4 border-0 overflow-hidden shadow-lg"><div class="modal-header p-3 border-bottom d-flex justify-content-between"><h6 class="modal-title fw-bold mb-0" id="viewerTitle">Preview</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-0 bg-dark d-flex align-items-center justify-content-center" style="min-height:550px;"><div id="viewerContent" class="w-100 text-center"></div></div></div></div></div>
<div class="modal fade" id="uploadSuccessModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg rounded-5 text-center p-5"><i class="fas fa-check-circle text-success mb-3" style="font-size: 60px;"></i><h4 class="fw-bold">Request Sent!</h4><p class="text-muted px-4">Successfully sent request to Admin. Wait for approval.</p><button onclick="window.location.reload()" class="btn btn-royal rounded-pill px-5">OK</button></div></div></div>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const uModal = new bootstrap.Modal(document.getElementById('uploadModal')), vModal = new bootstrap.Modal(document.getElementById('viewerModal')), sModal = new bootstrap.Modal(document.getElementById('uploadSuccessModal'));
    window.openUploadModal = (name, id, fieldName) => { document.getElementById('modalTitle').innerText = 'Upload ' + name; document.getElementById('modalFieldName').value = fieldName; uModal.show(); };
    window.viewDocument = (path, name) => { document.getElementById('viewerTitle').innerText = name; const ext = path.split('.').pop().toLowerCase(); const content = (ext === 'pdf') ? `<iframe src="${path}" width="100%" height="600px" style="border:none;"></iframe>` : `<img src="${path}" class="img-fluid p-3">`; document.getElementById('viewerContent').innerHTML = content; vModal.show(); };
    <?php if ($showSuccessModal): ?> sModal.show(); <?php endif; ?>
});
</script>
<style>.border-dashed { border-style: dashed !important; border-color: #d1d9e6 !important; } .btn-link:hover { opacity:0.8; }</style>