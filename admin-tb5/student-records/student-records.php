<?php
// Include header, sidebar, and database connection
include('../header/header.php');
include('../sidebar/sidebar.php');
require_once('../../db-connect.php'); // Your PDO connection file

// --- 1. HANDLE FILTERS ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$docStatus = isset($_GET['docStatus']) ? trim($_GET['docStatus']) : '';
$sortBy = isset($_GET['sort']) ? trim($_GET['sort']) : 'name';

// --- 2. FETCH DOCUMENTS (Using PDO) ---
// Fetch all documents and map them by StudentInfoId
$sql_docs = "SELECT * FROM `documents`";
$stmt_docs = $pdo->query($sql_docs);
$student_docs = [];

if ($stmt_docs) {
    while ($row = $stmt_docs->fetch(PDO::FETCH_ASSOC)) {
        // Map the single row of documents to the student's ID
        $student_docs[$row['StudentInfoId']] = $row;
    }
}

// --- 3. FETCH ONLY APPROVED STUDENTS (Using PDO Prepared Statements) ---
$sql_students = "SELECT * FROM `studentinfos` WHERE Status = 'Approved'";
$params = []; 

if (!empty($search)) {
    $sql_students .= " AND (FirstName LIKE :search1 OR LastName LIKE :search2 OR ULI LIKE :search3 OR Email LIKE :search4)";
    $params[':search1'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
    $params[':search4'] = "%$search%";
}

$sql_students .= " ORDER BY Id DESC";

$stmt_students = $pdo->prepare($sql_students);
$stmt_students->execute($params);

$students = [];
$total_students = 0;
$complete_docs_count = 0;
$pending_docs_count = 0;
$with_tor_count = 0;

while ($student = $stmt_students->fetch(PDO::FETCH_ASSOC)) {
    $sid = $student['Id']; 
    $docs = isset($student_docs[$sid]) ? $student_docs[$sid] : null;
    
    // Evaluate document status based on your schema columns
    $overall_doc_status = 'incomplete'; 
    $has_pending = false;
    $has_approved = false;
    $has_tor = !empty($docs['TORPath'] ?? null);

    if ($docs) {
        $statuses = [
            $docs['PSAStatus'] ?? null,
            $docs['Form137Status'] ?? null,
            $docs['MarriageCertificateStatus'] ?? null,
            $docs['ALSCertificateStatus'] ?? null,
            $docs['BarangayIndigencyStatus'] ?? null,
            $docs['CertificateOfResidencyStatus'] ?? null
        ];

        foreach ($statuses as $status) {
            if ($status === 'pending') {
                $has_pending = true;
            } elseif ($status === 'approved') {
                $has_approved = true;
            }
        }

        if ($has_pending) {
            $overall_doc_status = 'pending';
        } elseif ($has_approved && !$has_pending) {
            $overall_doc_status = 'complete';
        }
    }

    // Apply Document Status Filter manually
    if (!empty($docStatus) && $overall_doc_status !== $docStatus) {
        continue;
    }

    $student['overall_doc_status'] = $overall_doc_status;
    $student['has_tor'] = $has_tor;
    $student['full_name'] = ($student['FirstName'] ?? '') . ' ' . ($student['LastName'] ?? '');
    
    // Package documents nicely for the frontend JavaScript
    $student['docs_pkg'] = [
        'psa' => [
            'path' => $docs['PSAPath'] ?? null, 
            'status' => $docs['PSAStatus'] ?? 'missing',
            'filename' => isset($docs['PSAPath']) ? basename($docs['PSAPath']) : null
        ],
        'f137' => [
            'path' => $docs['Form137Path'] ?? null, 
            'status' => $docs['Form137Status'] ?? 'missing',
            'filename' => isset($docs['Form137Path']) ? basename($docs['Form137Path']) : null
        ],
        'marriage' => [
            'path' => $docs['MarriageCertificatePath'] ?? null, 
            'status' => $docs['MarriageCertificateStatus'] ?? 'missing',
            'filename' => isset($docs['MarriageCertificatePath']) ? basename($docs['MarriageCertificatePath']) : null
        ],
        'tor' => [
            'path' => $docs['TORPath'] ?? null, 
            'status' => !empty($docs['TORPath'] ?? null) ? 'approved' : 'missing',
            'filename' => isset($docs['TORPath']) ? basename($docs['TORPath']) : null
        ]
    ];
    
    $students[] = $student;

    // Increment Stats
    $total_students++;
    if ($overall_doc_status === 'complete') $complete_docs_count++;
    if ($overall_doc_status === 'pending') $pending_docs_count++;
    if ($has_tor) $with_tor_count++;
}

// Apply sorting in PHP
if ($sortBy === 'name') {
    usort($students, function($a, $b) {
        return strcmp($a['full_name'], $b['full_name']);
    });
} elseif ($sortBy === 'status') {
    $statusPriority = [
        'complete' => 1,
        'pending' => 2,
        'incomplete' => 3
    ];
    
    usort($students, function($a, $b) use ($statusPriority) {
        $priorityA = $statusPriority[$a['overall_doc_status']] ?? 4;
        $priorityB = $statusPriority[$b['overall_doc_status']] ?? 4;
        
        if ($priorityA === $priorityB) {
            return strcmp($a['full_name'], $b['full_name']);
        }
        
        return $priorityA - $priorityB;
    });
}
?>

<div class="content-wrapper">
    <div class="main-content">
        <!-- Page Title Card -->
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold text-white mb-2">
                            <i class="bi bi-person-lines-fill me-2"></i>Student Records
                        </h2>
                        <p class="text-white-50 mb-0">
                            View and manage approved student information, documents, and records
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Approved Students</h6>
                                <h3 class="mb-0 fw-bold"><?php echo $total_students; ?></h3>
                                <small class="text-muted">Active records</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Complete Documents</h6>
                                <h3 class="mb-0 fw-bold"><?php echo $complete_docs_count; ?></h3>
                                <small class="text-muted">All documents approved</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-clock text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Pending Documents</h6>
                                <h3 class="mb-0 fw-bold"><?php echo $pending_docs_count; ?></h3>
                                <small class="text-muted">Awaiting approval</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-clipboard-check text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">With TOR</h6>
                                <h3 class="mb-0 fw-bold"><?php echo $with_tor_count; ?></h3>
                                <small class="text-muted">Transcript uploaded</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Search Student</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Name, ULI, or email..." id="searchStudent" value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Document Status</label>
                        <select class="form-select" id="filterDocStatus">
                            <option value="">All Status</option>
                            <option value="complete" <?php echo $docStatus == 'complete' ? 'selected' : ''; ?>>Complete</option>
                            <option value="pending" <?php echo $docStatus == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="incomplete" <?php echo $docStatus == 'incomplete' ? 'selected' : ''; ?>>Incomplete</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Sort By</label>
                        <select class="form-select" id="sortBy">
                            <option value="name" <?php echo $sortBy == 'name' ? 'selected' : ''; ?>>Name</option>
                            <option value="status" <?php echo $sortBy == 'status' ? 'selected' : ''; ?>>Document Status</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">&nbsp;</label>
                        <button class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="bi bi-funnel me-1"></i>Apply
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">&nbsp;</label>
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-people-fill me-2"></i>Approved Student Records
                </h5>
                <span class="badge bg-primary"><?php echo count($students); ?> Approved Students</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="studentsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Student Info</th>
                                <th>Role</th>
                                <th>Documents Status</th>
                                <th>TOR Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($students) > 0): ?>
                                <?php foreach ($students as $student): ?>
                                    <tr data-student-id="<?php echo $student['Id']; ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="position-relative me-3">
                                                    <?php 
                                                    $profilePath = '../uploads/' . ($student['ProfilePicture'] ?? '');
                                                    if(!empty($student['ProfilePicture']) && file_exists($profilePath)): 
                                                    ?>
                                                        <img src="<?= htmlspecialchars($profilePath); ?>" 
                                                             alt="<?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']); ?>"
                                                             class="rounded-circle border" 
                                                             style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #667eea;">
                                                    <?php else: ?>
                                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px;">
                                                            <i class="bi bi-person-fill text-primary" style="font-size: 1.2rem;"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <strong class="student-name"><?php echo htmlspecialchars(($student['FirstName'] ?? '') . ' ' . ($student['LastName'] ?? '')); ?></strong>
                                                    <br>
                                                    <small class="text-muted">ULI: <?php echo htmlspecialchars($student['ULI'] ?? 'N/A'); ?></small>
                                                    <br>
                                                    <small class="text-muted student-email"><?php echo htmlspecialchars($student['Email'] ?? 'No email'); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info mb-1 student-role"><?php echo htmlspecialchars($student['Role'] ?? 'Student'); ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1 student-docs">
                                                <?php 
                                                $req_docs = ['psa' => 'PSA', 'f137' => 'F137', 'marriage' => 'Marriage'];
                                                foreach($req_docs as $key => $label) {
                                                    $doc = $student['docs_pkg'][$key] ?? null;
                                                    
                                                    if($doc && $doc['path']) {
                                                        if(($doc['status'] ?? '') == 'approved') {
                                                            echo "<span class='badge bg-success' title='{$label} - Approved'><i class='bi bi-file-earmark-check-fill'></i> {$label}</span>";
                                                        } elseif(($doc['status'] ?? '') == 'rejected') {
                                                            echo "<span class='badge bg-danger' title='{$label} - Rejected'><i class='bi bi-file-earmark-x-fill'></i> {$label}</span>";
                                                        } else {
                                                            echo "<span class='badge bg-warning' title='{$label} - Pending'><i class='bi bi-clock-fill'></i> {$label}</span>";
                                                        }
                                                    } else {
                                                        echo "<span class='badge bg-secondary' title='{$label} - Not Uploaded'><i class='bi bi-x-circle-fill'></i> {$label}</span>";
                                                    }
                                                }
                                                ?>
                                            </div>
                                            
                                            <?php if($student['overall_doc_status'] == 'complete'): ?>
                                                <small class="text-success fw-semibold mt-1 d-block doc-status-complete"><i class="bi bi-check-circle-fill"></i> Complete</small>
                                            <?php elseif($student['overall_doc_status'] == 'pending'): ?>
                                                <small class="text-warning fw-semibold mt-1 d-block doc-status-pending"><i class="bi bi-clock-fill"></i> Pending</small>
                                            <?php else: ?>
                                                <small class="text-danger fw-semibold mt-1 d-block doc-status-incomplete"><i class="bi bi-exclamation-circle-fill"></i> Incomplete</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($student['has_tor']): ?>
                                                <span class="badge bg-success tor-status"><i class="bi bi-clipboard-check-fill"></i> TOR Added</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary tor-status"><i class="bi bi-x-circle"></i> No TOR</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" 
                                                    data-info="<?php echo htmlspecialchars(json_encode($student)); ?>" 
                                                    onclick="viewStudentDetails(this)">
                                                <i class="bi bi-eye me-1"></i>View Details
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                        <h5 class="text-muted">No approved students found</h5>
                                        <p class="text-muted small">Students need to be approved first before appearing in records</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-person-badge me-2"></i>Student Details & Documents
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Student Information -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-person-circle me-2"></i><span id="modalFullName"></span>
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">ULI</small>
                                        <strong id="modalStudentId"></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Email</small>
                                        <strong id="modalEmail"></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Contact</small>
                                        <strong id="modalContact"></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Role</small>
                                        <span class="badge bg-info" id="modalRole"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-white rounded-3 p-3 shadow-sm">
                                    <!-- Profile Picture in Modal -->
                                    <div id="modalProfilePictureContainer">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge" id="modalStatus">Approved</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-file-earmark-text me-2"></i>Uploaded Documents
                </h6>

                <div class="row g-3 mb-4" id="modalDocsContainer">
                    <!-- Documents will be populated here -->
                </div>

                <!-- Document Status Legend -->
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Document Status Legend</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <span class="badge bg-success me-2"><i class="bi bi-file-earmark-check-fill"></i></span>
                                <small>Approved by Admin</small>
                            </div>
                            <div class="col-md-4">
                                <span class="badge bg-warning me-2"><i class="bi bi-clock-fill"></i></span>
                                <small>Pending Approval</small>
                            </div>
                            <div class="col-md-4">
                                <span class="badge bg-secondary me-2"><i class="bi bi-x-circle-fill"></i></span>
                                <small>Not Uploaded</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Document View Modal -->
<div class="modal fade" id="viewDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">View Document</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be dynamically loaded -->
            </div>
        </div>
    </div>
</div>



<script>
// Base path for documents
const DOCUMENTS_BASE_PATH = '/uploads/documents/';
const PROFILE_BASE_PATH = '../uploads/';

// Store current student data
let currentStudentData = null;

// View student details
function viewStudentDetails(buttonElement) {
    const student = JSON.parse(buttonElement.getAttribute('data-info'));
    currentStudentData = student;
    
    // Populate Modal Details
    document.getElementById('modalFullName').textContent = (student.FirstName || '') + ' ' + (student.LastName || '');
    document.getElementById('modalStudentId').textContent = student.ULI || 'N/A';
    document.getElementById('modalEmail').textContent = student.Email || 'N/A';
    document.getElementById('modalContact').textContent = student.ContactNo || 'N/A';
    document.getElementById('modalRole').textContent = student.Role || 'Student';
    
    const statusBadge = document.getElementById('modalStatus');
    statusBadge.textContent = student.Status || 'Approved';
    statusBadge.className = 'badge bg-success';

    // Profile Picture Handling
    const profileContainer = document.getElementById('modalProfilePictureContainer');
    const fullName = (student.FirstName || '') + ' ' + (student.LastName || '');
    
    // Check if profile picture exists
    if (student.ProfilePicture) {
        // Try to load the image, show fallback if it fails
        profileContainer.innerHTML = `
            <img src="${PROFILE_BASE_PATH}${student.ProfilePicture}" 
                 alt="${fullName}"
                 class="rounded-circle border border-3" 
                 style="width: 100px; height: 100px; object-fit: cover; border-color: #667eea !important;"
                 onerror="this.onerror=null; this.parentNode.innerHTML='<div class=\'bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto\' style=\'width: 100px; height: 100px;\'><i class=\'bi bi-person-fill text-primary\' style=\'font-size: 3rem;\'></i></div><small class=\'text-muted mt-2 d-block\'>No profile picture</small>';">
        `;
    } else {
        // No profile picture - show icon
        profileContainer.innerHTML = `
            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                 style="width: 100px; height: 100px;">
                <i class="bi bi-person-fill text-primary" style="font-size: 3rem;"></i>
            </div>
            <small class="text-muted mt-2 d-block">No profile picture</small>
        `;
    }

    // Populate Documents
    const docsContainer = document.getElementById('modalDocsContainer');
    docsContainer.innerHTML = ''; 

    const expectedDocs =[
        { key: 'psa', title: 'PSA Birth Certificate', icon: 'bi-file-earmark-person' },
        { key: 'f137', title: 'Form 137 (High School)', icon: 'bi-file-earmark-text' },
        { key: 'marriage', title: 'Marriage Certificate', icon: 'bi-file-earmark-heart' },
        { key: 'tor', title: 'Transcript of Records (TOR)', icon: 'bi-clipboard-data' }
    ];

    expectedDocs.forEach(docTemp => {
        let docData = student.docs_pkg ? student.docs_pkg[docTemp.key] : null;
        
        let isUploaded = docData && docData.path ? true : false;
        let isApproved = docData && docData.status === 'approved';
        let cardBorder = isApproved ? 'border-success' : (isUploaded ? 'border-warning' : 'border-secondary');
        let badgeColor = isApproved ? 'bg-success' : (isUploaded ? 'bg-warning' : 'bg-secondary');
        let badgeIcon = isApproved ? 'bi-check-circle-fill' : (isUploaded ? 'bi-clock-fill' : 'bi-x-circle-fill');
        let badgeText = isApproved ? 'Approved' : (isUploaded ? 'Pending' : 'Missing');

        let html = `
        <div class="col-md-6">
            <div class="card ${cardBorder} h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-1">
                                <i class="bi ${docTemp.icon} text-${isApproved ? 'success' : 'secondary'} me-2"></i>${docTemp.title}
                            </h6>
                            <small class="text-muted">${isUploaded ? 'Document Uploaded' : 'Not uploaded yet'}</small>
                        </div>
                        <span class="badge ${badgeColor}">
                            <i class="bi ${badgeIcon}"></i> ${badgeText}
                        </span>
                    </div>`;
                    
        if (isUploaded) {
            // Get just the filename from the path
            let filename = docData.path.split('/').pop();
            
            html += `<div class="d-flex gap-2 mt-3">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('${filename}', '${docTemp.title}')">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="downloadDocument('${filename}')">
                            <i class="bi bi-download"></i> Download
                        </button>`;
                        
            if (docTemp.key === 'tor') {
                html += `<button class="btn btn-sm btn-outline-warning" onclick="editTOR('${student.Id}')">
                            <i class="bi bi-pencil"></i> Edit Grades
                         </button>`;
            }
            html += `</div>`;
        }
        
        html += `</div></div></div>`;
        docsContainer.innerHTML += html;
    });

    new bootstrap.Modal(document.getElementById('studentDetailsModal')).show();
}

// View document function
function viewDocument(filename, type) {
    const modal = new bootstrap.Modal(document.getElementById('viewDocumentModal'));
    const modalBody = $('#viewDocumentModal .modal-body');
    const modalTitle = $('#viewDocumentModal .modal-title');
    
    modalTitle.text(`View ${type}`);
    
    const filePath = `/uploads/documents/${filename}`;
    const fileExtension = filename.split('.').pop().toLowerCase();
    
    // Show loading indicator
    modalBody.html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    
    if (fileExtension === 'pdf') {
        modalBody.html(`
            <iframe src="${filePath}" style="width: 100%; height: 500px; border: none;"></iframe>
        `);
    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
        modalBody.html(`
            <img src="${filePath}" class="img-fluid" alt="${type}" style="max-height: 500px; margin: 0 auto; display: block;" 
                 onerror="this.onerror=null; this.parentNode.innerHTML='<div class=\'text-center p-5\'><i class=\'bi bi-exclamation-triangle text-danger\' style=\'font-size: 3rem;\'></i><p class=\'mt-3 text-danger\'>Failed to load image</p><a href=\'${filePath}\' target=\'_blank\' class=\'btn btn-primary mt-2\'>Open in new tab</a></div>';">
        `);
    } else {
        modalBody.html(`
            <div class="text-center p-5">
                <i class="bi bi-file-earmark-text text-primary" style="font-size: 4rem;"></i>
                <p class="mt-3">File type not supported for preview</p>
                <a href="${filePath}" download class="btn btn-primary">
                    <i class="bi bi-download me-2"></i>Download File
                </a>
            </div>
        `);
    }
    
    modal.show();
}

// Download document
function downloadDocument(filename) {
    if(filename) {
        const filePath = `/uploads/documents/${filename}`;
        let a = document.createElement('a');
        a.href = filePath;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
}

// Edit TOR
function editTOR(studentId) {
    window.location.href = '../tor-grades/tor-grades.php?student=' + studentId;
}

// Apply filters
function applyFilters() {
    const search = document.getElementById('searchStudent').value;
    const docStatus = document.getElementById('filterDocStatus').value;
    const sortBy = document.getElementById('sortBy').value;
    
    let url = new URL(window.location.href);
    url.searchParams.set('search', search);
    url.searchParams.set('docStatus', docStatus);
    url.searchParams.set('sort', sortBy);
    
    window.location.href = url.href;
}

// Reset filters to default
function resetFilters() {
    window.location.href = window.location.pathname;
}

// Clear modal content when closed
$('#viewDocumentModal').on('hidden.bs.modal', function () {
    $('#viewDocumentModal .modal-body').html('');
});

</script>

<style>
.card { transition: all 0.3s ease; }
.card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important; }
.badge { font-size: 0.75rem; padding: 0.35em 0.65em; }
.modal.fade .modal-dialog { transition: transform 0.3s ease-out; }
.modal.show .modal-dialog { transform: none; }
#viewDocumentModal .modal-body { padding: 0; min-height: 300px; }
#viewDocumentModal .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
#viewDocumentModal .btn-close-white { filter: brightness(0) invert(1); }
</style>

<?php include('../footer/footer.php'); ?>