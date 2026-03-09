<?php
    // filepath: c:\laragon\www\admin-tb5\upload\upload.php
    session_start();

    // Authentication Check
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: ../login/login.php');
        exit;
    }

    require_once('../db-connect.php'); 

    $showSuccessModal = false;
    $message = "";

    // --- BACKEND LOGIC: Processing with Filename Detection ---
    if (isset($_POST['modal_upload_submit'])) {
        $fieldName = $_POST['field_name']; 
        $targetDir = "../uploads/documents/";
        if (!file_exists($targetDir)) { mkdir($targetDir, 0777, true); }
        
        $file = $_FILES["modalFile"];
        $originalName = strtoupper($file['name']);
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
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

        if ($isValidType) {
            if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
                try {
                    $checkStmt = $pdo->prepare("SELECT $fieldName FROM documents WHERE StudentInfoId = ?");
                    $checkStmt->execute([$_SESSION['user_id']]);
                    $oldData = $checkStmt->fetch();
                    if ($oldData && !empty($oldData[$fieldName])) {
                        $oldFile = $targetDir . $oldData[$fieldName];
                        if (file_exists($oldFile)) { unlink($oldFile); }
                    }

                    $fileName = strtolower(str_replace(['Path', '_'], '', $fieldName)) . '_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;

                    if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
                        $statusField  = str_replace('Path', 'Status',  $fieldName);
                        // ── Derive remarks column and reset it on resubmit ────
                        $remarksField = str_replace('Path', 'Remarks', $fieldName);

                        $updateSql = "UPDATE documents 
                                      SET $fieldName    = ?, 
                                          $statusField  = 'pending',
                                          $remarksField = NULL
                                      WHERE StudentInfoId = ?";
                        $stmt = $pdo->prepare($updateSql);
                        if ($stmt->execute([$fileName, $_SESSION['user_id']])) {
                            $_SESSION['upload_success'] = true;
                            header('Location: upload.php');
                            exit;
                        } else { $_SESSION['upload_error'] = "Database update failed."; }
                    } else { $_SESSION['upload_error'] = "Upload failed. Check permissions."; }
                } catch(PDOException $e) { $_SESSION['upload_error'] = "Database Sync Error."; }
            } else { $_SESSION['upload_error'] = "Invalid file type. (PDF/JPG/PNG only)"; }
        }
        
        if (isset($_SESSION['upload_error'])) { header('Location: upload.php'); exit; }
    }

    if (isset($_SESSION['upload_success'])) { $showSuccessModal = true; unset($_SESSION['upload_success']); }
    if (isset($_SESSION['upload_error'])) { $message = $_SESSION['upload_error']; unset($_SESSION['upload_error']); }

    // --- FETCH DATA FROM DATABASE ---
    try {
        $stmt = $pdo->prepare("SELECT * FROM documents WHERE StudentInfoId = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $docData = $stmt->fetch();
        if (!$docData) {
            $insertStmt = $pdo->prepare("INSERT INTO documents (StudentInfoId) VALUES (?)");
            $insertStmt->execute([$_SESSION['user_id']]);
            $stmt->execute([$_SESSION['user_id']]);
            $docData = $stmt->fetch();
        }
    } catch(PDOException $e) { $message = "Connection error."; }

    // --- MAPPER LOGIC FOR DROPDOWN AND LABELS ---
    $groupedRequirements = [
        [
            "display_name" => "Academic Record (TOR / DIPLOMA / Form 137 / ALS)",
            "fields"        => ["TORPath", "DiplomaPath", "Form137Path", "ALSCertificatePath"],
            "field_labels"  => ["TORPath" => "TOR", "DiplomaPath" => "Diploma", "Form137Path" => "Form 137", "ALSCertificatePath" => "ALS Cert"],
            "primary_field" => "TORPath",
            "status_fields" => ["TORStatus", "DiplomaStatus", "Form137Status", "ALSCertificateStatus"],
            // ── Map each field to its remarks column ──────────────────────────
            "remarks_fields" => ["TORPath" => "TORRemarks", "DiplomaPath" => "DiplomaRemarks", "Form137Path" => "Form137Remarks", "ALSCertificatePath" => "ALSCertificateRemarks"],
            "icon"          => "fa-graduation-cap"
        ],
        [
            "display_name"  => "Birth Identity (PSA / BIRTH CERTIFICATE)",
            "fields"        => ["PSAPath"],
            "field_labels"  => ["PSAPath" => "PSA Certificate"],
            "primary_field" => "PSAPath",
            "status_fields" => ["PSAStatus"],
            "remarks_fields" => ["PSAPath" => "PSARemarks"],
            "icon"          => "fa-baby"
        ],
        [
            "display_name"  => "Status Credential (MARRIAGE CERTIFICATE)",
            "fields"        => ["MarriageCertificatePath"],
            "field_labels"  => ["MarriageCertificatePath" => "Marriage Certificate"],
            "primary_field" => "MarriageCertificatePath",
            "status_fields" => ["MarriageCertificateStatus"],
            "remarks_fields" => ["MarriageCertificatePath" => "MarriageCertificateRemarks"],
            "icon"          => "fa-ring"
        ],
        [
            "display_name"  => "Barangay Documents (Indigency / Residency)",
            "fields"        => ["BarangayIndigencyPath", "CertificateOfResidencyPath"],
            "field_labels"  => ["BarangayIndigencyPath" => "Brgy Indigency", "CertificateOfResidencyPath" => "Brgy Residency"],
            "primary_field" => "BarangayIndigencyPath",
            "status_fields" => ["BarangayIndigencyStatus", "CertificateOfResidencyStatus"],
            "remarks_fields" => ["BarangayIndigencyPath" => "BarangayIndigencyRemarks", "CertificateOfResidencyPath" => "CertificateOfResidencyRemarks"],
            "icon"          => "fa-home"
        ]
    ];

    include '../includes/header.php'; 
    include '../includes/sidebar.php'; 
    ?>

    <div class="main-content">
        <div class="container-fluid">
            <!-- Institutional Header -->
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
                    <div class="d-flex align-items-center"><i class="fas fa-exclamation-triangle me-3 fa-lg"></i><div><strong>Detection Error:</strong> <?php echo htmlspecialchars($message); ?></div></div>
                </div>
            <?php endif; ?>

            <!-- UNIFIED GROUPED TABLE -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-royal"><i class="fas fa-list-check me-2"></i>Uploaded Documents</h6>
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
                                    $subDocsList = [];
                                    $rejectedItems = [];
                                    $hasUploaded = false;
                                    
                                    foreach ($rowGroup['fields'] as $key => $field) {
                                        if (!empty($docData[$field]) && $docData[$field] !== 'None') {
                                            $label           = $rowGroup['field_labels'][$field];
                                            // ── Fix: use $key to get matching status field ────────
                                            $subStatusField  = $rowGroup['status_fields'][$key];
                                            $remarksField    = $rowGroup['remarks_fields'][$field] ?? null;
                                            $currentStatus   = strtolower($docData[$subStatusField] ?? 'pending');
                                            // ── Get per-document rejection reason ────────────────
                                            $currentRemarks  = ($remarksField && !empty($docData[$remarksField]))
                                                             ? $docData[$remarksField] : null;

                                            $subDocsList[] = [
                                                "name"    => $label,
                                                "file"    => $docData[$field],
                                                "status"  => $currentStatus,
                                                "remarks" => $currentRemarks
                                            ];

                                            if ($currentStatus === 'rejected') {
                                                $rejectedItems[] = [
                                                    'name'    => $label,
                                                    'remarks' => $currentRemarks ?? 'No reason provided'
                                                ];
                                            }

                                            $hasUploaded = true;
                                        }
                                    }

                                    if (!$hasUploaded) continue;

                                    $groupStatus = "Not Uploaded";
                                    $approvedFound = false;
                                    $rejectedFound = false;
                                    $pendingFound  = false;

                                    foreach ($rowGroup['status_fields'] as $sf) {
                                        $curr = strtolower($docData[$sf] ?? '');
                                        if ($curr === 'rejected') { $rejectedFound = true; break; }
                                        if ($curr === 'approved') $approvedFound = true;
                                        if ($curr === 'pending')  $pendingFound  = true;
                                    }

                                    if ($rejectedFound)      $groupStatus = "Rejected";
                                    elseif ($approvedFound)  $groupStatus = "Approved";
                                    elseif ($pendingFound)   $groupStatus = "Pending";

                                    $badge = ($groupStatus === 'Approved') ? 'bg-success'
                                           : ($groupStatus === 'Rejected'  ? 'bg-danger'
                                           : ($groupStatus === 'Pending'   ? 'bg-warning text-dark'
                                           : 'bg-secondary'));
                                ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-light p-2 rounded text-royal me-3 shadow-sm mt-1">
                                                <i class="fas <?php echo $rowGroup['icon']; ?> fa-fw"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block" style="font-size:13.5px;">
                                                    <?php echo $rowGroup['display_name']; ?>
                                                </span>
                                                <small class="text-muted"><?php echo count($subDocsList); ?> Document(s) available</small>

                                                <?php if ($groupStatus === 'Rejected' && !empty($rejectedItems)): ?>
                                                    <?php foreach ($rejectedItems as $rej): ?>
                                                    <div class="mt-1 d-flex align-items-start gap-1 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded px-2 py-1">
                                                        <i class="fas fa-exclamation-circle text-danger mt-1" style="font-size:10px;"></i>
                                                        <small class="text-danger" style="font-size:10px;">
                                                            <strong><?php echo htmlspecialchars($rej['name']); ?>:</strong>
                                                            <?php echo htmlspecialchars($rej['remarks']); ?>
                                                        </small>
                                                    </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2 shadow-sm text-uppercase"
                                            style="font-size:9px;letter-spacing:0.5px;">
                                            <?php echo $groupStatus; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-royal p-0"
                                                type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 py-2">
                                                <li class="dropdown-header small text-uppercase fw-bold pb-1">
                                                    Check Documents
                                                </li>
                                                <?php foreach ($subDocsList as $sub): ?>
                                                <li>
                                                    <button class="dropdown-item py-2"
                                                        onclick="viewDocument('../uploads/documents/<?php echo $sub['file']; ?>',
                                                                 '<?php echo addslashes($sub['name']); ?>')">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>
                                                                <i class="far fa-file-alt me-2 opacity-50"></i>
                                                                <?php echo $sub['name']; ?>
                                                            </span>
                                                            <?php if ($sub['status'] === 'rejected'): ?>
                                                                <i class="fas fa-exclamation-circle text-danger ms-2"
                                                                   title="<?php echo htmlspecialchars($sub['remarks'] ?? 'Rejected'); ?>">
                                                                </i>
                                                            <?php elseif ($sub['status'] === 'approved'): ?>
                                                                <i class="fas fa-check-circle text-success ms-2"></i>
                                                            <?php else: ?>
                                                                <i class="fas fa-clock text-warning ms-2"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if ($sub['status'] === 'rejected' && $sub['remarks']): ?>
                                                            <div class="text-danger mt-1" style="font-size:10px;white-space:normal;">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                <?php echo htmlspecialchars($sub['remarks']); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </button>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($groupStatus === 'Rejected'): ?>
                                            <button class="btn btn-sm btn-outline-danger border-0 rounded-circle shadow-sm resubmit-btn"
                                                data-name="<?php echo htmlspecialchars($rowGroup['display_name']); ?>"
                                                data-fields="<?php echo htmlspecialchars(json_encode($rowGroup['fields'])); ?>"
                                                data-labels="<?php echo htmlspecialchars(json_encode($rowGroup['field_labels'])); ?>"
                                                data-docs="<?php echo htmlspecialchars(json_encode($subDocsList)); ?>"
                                                title="Resubmit">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        <?php elseif ($groupStatus === 'Approved'): ?>
                                            <span class="text-success">
                                                <i class="fas fa-check-circle fa-lg"></i>
                                            </span>
                                        <?php elseif ($groupStatus === 'Pending'): ?>
                                            <i class="fas fa-clock text-warning fa-lg"></i>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-primary border-0 rounded-circle shadow-sm"
                                                onclick="openUploadModal('<?php echo addslashes($rowGroup['display_name']); ?>',
                                                         0, '<?php echo $rowGroup['primary_field']; ?>')"
                                                title="Upload">
                                                <i class="fas fa-sync-alt"></i>
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

    <!-- (MODAL SECTIONS UNCHANGED) -->

    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4 border-0 shadow-lg"><div class="modal-header border-0 p-4 pb-0"><h5 class="modal-title fw-bold" id="modalTitle">Resubmit</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form action="upload.php" method="POST" enctype="multipart/form-data"><div class="modal-body px-4"><input type="hidden" name="field_name" id="modalFieldName"><div id="documentSelectorContainer" style="display:none;" class="mb-3"><label class="form-label fw-bold text-dark small">Select Rejected Document</label><select class="form-select rounded-3 border-2" id="rejectedDocSelect" onchange="updateFieldName()"><option value="">-- Choose Document --</option></select></div><div class="p-4 border-2 border-dashed rounded-3 bg-light text-center"><i class="fas fa-cloud-upload-alt text-royal fa-3x mb-3"></i><p class="mb-2 fw-bold">Choose file to upload</p><p class="text-muted small mb-3">PDF, JPG, JPEG, or PNG (Max 5MB)</p><input type="file" name="modalFile" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required></div><div class="alert alert-info mt-3 mb-0 small"><i class="fas fa-info-circle me-1"></i>Your document will replace the existing one and will be sent to admin for approval.</div></div><div class="modal-footer border-0 p-4"><button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button><button type="submit" name="modal_upload_submit" class="btn btn-royal rounded-pill px-5 shadow">Submit to Admin</button></div></form></div></div></div>
    <div class="modal fade" id="viewerModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content rounded-4 border-0 overflow-hidden shadow-lg"><div class="modal-header p-3 border-bottom d-flex justify-content-between"><h6 class="modal-title fw-bold mb-0" id="viewerTitle">Preview</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-0 bg-dark d-flex align-items-center justify-content-center" style="min-height:550px;"><div id="viewerContent" class="w-100 text-center"></div></div></div></div></div>
    <div class="modal fade" id="uploadSuccessModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg rounded-5 text-center p-5"><i class="fas fa-check-circle text-success mb-3" style="font-size: 60px;"></i><h4 class="fw-bold">Request Sent!</h4><p class="text-muted px-4">Successfully sent request to Admin. Wait for approval.</p><button onclick="closeSuccessModal()" class="btn btn-royal rounded-pill px-5">OK</button></div></div></div>

    <?php include '../includes/footer.php'; ?>

    <script>
    let successModal;
    let uModal;
    let vModal;
    
    document.addEventListener("DOMContentLoaded", function() {
        console.log("DOMContentLoaded fired");
        uModal = new bootstrap.Modal(document.getElementById('uploadModal'));
        vModal = new bootstrap.Modal(document.getElementById('viewerModal'));
        successModal = new bootstrap.Modal(document.getElementById('uploadSuccessModal'));
        console.log("Modals initialized:", { uModal, vModal, successModal });
        
        // Attach event listeners to resubmit buttons
        document.querySelectorAll('.resubmit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                const fields = JSON.parse(this.getAttribute('data-fields'));
                const labels = JSON.parse(this.getAttribute('data-labels'));
                const docs = JSON.parse(this.getAttribute('data-docs'));
                console.log("Resubmit button clicked:", { name, fields, labels, docs });
                openResubmitModal(name, fields, labels, docs);
            });
        });
        
        window.openUploadModal = (name, id, fieldName) => { 
            console.log("openUploadModal called:", name, fieldName);
            document.getElementById('modalTitle').innerText = 'Upload ' + name; 
            document.getElementById('modalFieldName').value = fieldName;
            document.getElementById('documentSelectorContainer').style.display = 'none';
            uModal.show(); 
        };
        
        window.openResubmitModal = (name, fields, fieldLabels, subDocsList) => {
            console.log("openResubmitModal called:", name, fields, fieldLabels, subDocsList);
            try {
                document.getElementById('modalTitle').innerText = 'Resubmit - ' + name;
                document.getElementById('documentSelectorContainer').style.display = 'block';
                
                const select = document.getElementById('rejectedDocSelect');
                if (!select) {
                    console.error("rejectedDocSelect element not found");
                    return;
                }
                select.innerHTML = '<option value="">-- Choose Document --</option>';
                
                // Build dropdown of rejected documents
                for (let i = 0; i < fields.length; i++) {
                    const fieldName = fields[i];
                    const label = fieldLabels[fieldName];
                    const isRejected = subDocsList.some(doc => doc.name === label && doc.status === 'rejected');
                    console.log(`Checking field ${fieldName} (${label}): rejected=${isRejected}`);
                    
                    if (isRejected) {
                        const option = document.createElement('option');
                        option.value = fieldName;
                        option.textContent = label + ' (Rejected)';
                        select.appendChild(option);
                    }
                }
                
                document.getElementById('modalFieldName').value = '';
                console.log("About to show modal");
                uModal.show();
                console.log("Modal showed successfully");
            } catch (e) {
                console.error("Error in openResubmitModal:", e);
            }
        };
        
        window.updateFieldName = () => {
            const fieldName = document.getElementById('rejectedDocSelect').value;
            document.getElementById('modalFieldName').value = fieldName;
            console.log("Field name updated to:", fieldName);
        };
        
        window.viewDocument = (path, name) => { 
            document.getElementById('viewerTitle').innerText = name; 
            const ext = path.split('.').pop().toLowerCase(); 
            const content = (ext === 'pdf') ? `<iframe src="${path}" width="100%" height="600px" style="border:none;"></iframe>` : `<img src="${path}" class="img-fluid p-3">`; 
            document.getElementById('viewerContent').innerHTML = content; 
            vModal.show(); 
        };
        
        window.closeSuccessModal = () => successModal.hide();
        <?php if ($showSuccessModal): ?> successModal.show(); <?php endif; ?>
    });
    </script>
    <style>.border-dashed { border-style: dashed !important; border-color: #d1d9e6 !important; } .btn-link:hover { opacity:0.8; }</style>