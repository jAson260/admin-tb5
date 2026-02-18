<?php 
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
    
    $targetDir = "../uploads/";
    if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }
    
    $fileName = basename($_FILES["modalFile"]["name"]);
    $targetPath = $targetDir . time() . "_" . $fileName;

    if (move_uploaded_file($_FILES["modalFile"]["tmp_name"], $targetPath)) {
        $showSuccessModal = true;
    } else {
        $message = "Failed to upload file. Please check server permissions.";
    }
}

// --- DATA SOURCE: Mock Array ---
$uploadedDocs = [
    ["id" => 1, "doc_name" => "PSA Birth Certificate", "file" => "birth_cert.pdf", "file_path" => "../uploads/sample_image.jpg", "date" => "2024-01-15", "status" => "Approved"],
    ["id" => 2, "doc_name" => "Transcript of Records (TOR)", "file" => "tor_official.pdf", "file_path" => "../uploads/sample_document.pdf", "date" => "2024-01-15", "status" => "Pending"],
    ["id" => 3, "doc_name" => "Diploma", "file" => "diploma_copy.jpg", "file_path" => "../uploads/sample_image.jpg", "date" => "2024-01-15", "status" => "Rejected"],
    ["id" => 4, "doc_name" => "Marriage Certificate", "file" => "None", "file_path" => "", "date" => "-", "status" => "Not Uploaded"],
];
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
            <div class="alert alert-danger border-0 shadow-sm small" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $message; ?>
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
                                <th>Date Modified</th> <!-- ADDED COLUMN -->
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
                                            <span class="fw-bold text-dark d-block"><?php echo $doc['doc_name']; ?></span>
                                            <small class="text-muted italic"><?php echo $doc['file']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <!-- DATE MODIFIED DATA -->
                                <td class="text-muted small">
                                    <?php echo ($doc['date'] != "-") ? date("M d, Y", strtotime($doc['date'])) : "-"; ?>
                                </td>
                                <td>
                                    <?php 
                                        $badge = ($doc['status'] == 'Approved') ? 'bg-success' : (($doc['status'] == 'Rejected') ? 'bg-danger' : (($doc['status'] == 'Not Uploaded') ? 'bg-secondary' : 'bg-warning text-dark'));
                                    ?>
                                    <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2 shadow-sm" style="font-size: 10px;"><?php echo $doc['status']; ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if($doc['status'] != 'Not Uploaded'): ?>
                                        <button class="btn btn-sm btn-link text-royal p-0" onclick="viewDocument('<?php echo $doc['file_path']; ?>', '<?php echo htmlspecialchars($doc['doc_name'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-eye fa-lg"></i>
                                        </button>
                                    <?php else: ?>
                                        <i class="fas fa-eye-slash text-muted"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary border-0 rounded-circle p-2" onclick="openUploadModal('<?php echo htmlspecialchars($doc['doc_name'], ENT_QUOTES); ?>', <?php echo $doc['id']; ?>)">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
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

<!-- MODALS REMAIN THE SAME -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 p-4"><h5 class="modal-title fw-bold" id="modalTitle">Resubmit</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body px-4">
                    <input type="hidden" name="doc_id" id="modalDocID">
                    <input type="hidden" name="doc_name_label" id="modalDocNameLabel">
                    <div class="p-4 border-2 border-dashed rounded-3 bg-light text-center">
                        <i class="fas fa-cloud-upload-alt text-royal fa-3x mb-3"></i>
                        <input type="file" name="modalFile" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4"><button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button><button type="submit" name="modal_upload_submit" class="btn btn-royal rounded-pill px-5">Submit</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden border-0"><div class="modal-header p-3 border-bottom"><h6 class="fw-bold mb-0" id="viewerTitle"></h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-0 bg-dark d-flex justify-content-center" style="min-height: 500px;"><div id="viewerContent" class="w-100 text-center"></div></div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-5">
                <i class="fas fa-check-circle text-success mb-4" style="font-size: 50px;"></i>
                <h4 class="fw-bold">Success!</h4>
                <p class="text-muted small">Succesfully request sent to Admin. Wait for the admin to approve the document/file.</p>
                <button type="button" class="btn btn-royal rounded-pill px-5" data-bs-dismiss="modal">OK</button>
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

        window.openUploadModal = function(name, id) {
            document.getElementById('modalTitle').innerText = 'Resubmit ' + name;
            document.getElementById('modalDocNameLabel').value = name;
            document.getElementById('modalDocID').value = id;
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