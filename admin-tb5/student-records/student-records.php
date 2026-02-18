<?php
// Include header
include('../header/header.php');
include('../sidebar/sidebar.php');
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
                            View and manage student information, documents, and records
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-light">
                            <i class="bi bi-download me-2"></i>Export Records
                        </button>
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
                                <h6 class="text-muted mb-0 small">Total Students</h6>
                                <h3 class="mb-0 fw-bold">342</h3>
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
                                <h3 class="mb-0 fw-bold">256</h3>
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
                                <h3 class="mb-0 fw-bold">86</h3>
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
                                <h3 class="mb-0 fw-bold">189</h3>
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
                            <input type="text" class="form-control" placeholder="Name, ID, or email..." id="searchStudent">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">School</label>
                        <select class="form-select" id="filterSchool">
                            <option value="">All Schools</option>
                            <option value="tb5">TB5</option>
                            <option value="bbi">BBI</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Course</label>
                        <select class="form-select" id="filterCourse">
                            <option value="">All Courses</option>
                            <option value="css">CSS</option>
                            <option value="bpp">BPP</option>
                            <option value="cok">COK</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Document Status</label>
                        <select class="form-select" id="filterDocStatus">
                            <option value="">All Status</option>
                            <option value="complete">Complete</option>
                            <option value="pending">Pending</option>
                            <option value="incomplete">Incomplete</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">&nbsp;</label>
                        <button class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="bi bi-funnel me-1"></i>Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">Student Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Student Info</th>
                                <th>School/Course</th>
                                <th>Batch</th>
                                <th>Documents Status</th>
                                <th>TOR Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Student 1 - Complete Documents -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-person-fill text-primary" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <strong>Juan Dela Cruz</strong>
                                            <br>
                                            <small class="text-muted">ID: 2024-001</small>
                                            <br>
                                            <small class="text-muted">juan.delacruz@email.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info mb-1">TB5</span>
                                    <br>
                                    <small>CSS - Caregiving</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Batch 2024-01</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-success" title="PSA Birth Certificate - Approved">
                                            <i class="bi bi-file-earmark-check-fill"></i> PSA
                                        </span>
                                        <span class="badge bg-success" title="Form 137 - Approved">
                                            <i class="bi bi-file-earmark-check-fill"></i> F137
                                        </span>
                                        <span class="badge bg-success" title="Marriage Certificate - Approved">
                                            <i class="bi bi-file-earmark-check-fill"></i> Marriage
                                        </span>
                                    </div>
                                    <small class="text-success fw-semibold mt-1 d-block">
                                        <i class="bi bi-check-circle-fill"></i> Complete
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="bi bi-clipboard-check-fill"></i> TOR Added
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewStudentDetails(1)">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </button>
                                </td>
                            </tr>

                            <!-- Student 2 - Pending Documents -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-person-fill text-primary" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <strong>Maria Santos</strong>
                                            <br>
                                            <small class="text-muted">ID: 2024-002</small>
                                            <br>
                                            <small class="text-muted">maria.santos@email.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info mb-1">TB5</span>
                                    <br>
                                    <small>BPP - Bread & Pastry</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Batch 2024-01</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-success" title="PSA Birth Certificate - Approved">
                                            <i class="bi bi-file-earmark-check-fill"></i> PSA
                                        </span>
                                        <span class="badge bg-warning" title="Form 137 - Pending Approval">
                                            <i class="bi bi-clock-fill"></i> F137
                                        </span>
                                        <span class="badge bg-secondary" title="Marriage Certificate - Not Uploaded">
                                            <i class="bi bi-x-circle-fill"></i> Marriage
                                        </span>
                                    </div>
                                    <small class="text-warning fw-semibold mt-1 d-block">
                                        <i class="bi bi-clock-fill"></i> Pending
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle"></i> No TOR
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewStudentDetails(2)">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </button>
                                </td>
                            </tr>

                            <!-- Student 3 - ALS/Indigent Student -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-person-fill text-primary" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <strong>Pedro Reyes</strong>
                                            <br>
                                            <small class="text-muted">ID: 2024-003</small>
                                            <br>
                                            <small class="text-muted">pedro.reyes@email.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning mb-1">BBI</span>
                                    <br>
                                    <small>COK - Commercial Cooking</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Batch 2024-02</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-success" title="PSA Birth Certificate - Approved">
                                            <i class="bi bi-file-earmark-check-fill"></i> PSA
                                        </span>
                                        <span class="badge bg-success" title="ALS Certificate - Approved">
                                            <i class="bi bi-file-earmark-check-fill"></i> ALS
                                        </span>
                                        <span class="badge bg-success" title="Barangay Indigency - Approved">
                                            <i class="bi bi-file-earmark-check-fill"></i> Indigency
                                        </span>
                                        <span class="badge bg-success" title="Certificate of Residency - Approved">
                                            <i class="bi bi-file-earmark-check-fill"></i> Residency
                                        </span>
                                    </div>
                                    <small class="text-success fw-semibold mt-1 d-block">
                                        <i class="bi bi-check-circle-fill"></i> Complete (ALS)
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="bi bi-clipboard-check-fill"></i> TOR Added
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewStudentDetails(3)">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </button>
                                </td>
                            </tr>

                            <!-- Student 4 - Incomplete Documents -->
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="bi bi-person-fill text-primary" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <strong>Ana Lopez</strong>
                                            <br>
                                            <small class="text-muted">ID: 2024-004</small>
                                            <br>
                                            <small class="text-muted">ana.lopez@email.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info mb-1">TB5</span>
                                    <br>
                                    <small>HSK - Housekeeping</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Batch 2024-03</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-warning" title="PSA Birth Certificate - Pending">
                                            <i class="bi bi-clock-fill"></i> PSA
                                        </span>
                                        <span class="badge bg-secondary" title="Form 137 - Not Uploaded">
                                            <i class="bi bi-x-circle-fill"></i> F137
                                        </span>
                                        <span class="badge bg-secondary" title="Marriage Certificate - Not Uploaded">
                                            <i class="bi bi-x-circle-fill"></i> Marriage
                                        </span>
                                    </div>
                                    <small class="text-danger fw-semibold mt-1 d-block">
                                        <i class="bi bi-exclamation-circle-fill"></i> Incomplete
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle"></i> No TOR
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewStudentDetails(4)">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </button>
                                </td>
                            </tr>
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
                                    <i class="bi bi-person-circle me-2"></i>Juan Dela Cruz
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Student ID</small>
                                        <strong>2024-001</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Email</small>
                                        <strong>juan.delacruz@email.com</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">School</small>
                                        <span class="badge bg-info">TB5</span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Course</small>
                                        <strong>CSS - Caregiving</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Batch</small>
                                        <span class="badge bg-secondary">Batch 2024-01</span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Contact</small>
                                        <strong>0912-345-6789</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="bg-white rounded-3 p-3 shadow-sm">
                                    <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
                                    <div class="mt-2">
                                        <span class="badge bg-success">Active</span>
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

                <div class="row g-3 mb-4">
                    <!-- PSA Birth Certificate -->
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            <i class="bi bi-file-earmark-person text-success me-2"></i>PSA Birth Certificate
                                        </h6>
                                        <small class="text-muted">Uploaded: Jan 15, 2024</small>
                                    </div>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill"></i> Approved
                                    </span>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('psa', 1)">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="downloadDocument('psa', 1)">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form 137 -->
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            <i class="bi bi-file-earmark-text text-success me-2"></i>Form 137 (High School)
                                        </h6>
                                        <small class="text-muted">Uploaded: Jan 16, 2024</small>
                                    </div>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill"></i> Approved
                                    </span>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('f137', 1)">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="downloadDocument('f137', 1)">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Marriage Certificate -->
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            <i class="bi bi-file-earmark-heart text-success me-2"></i>Marriage Certificate
                                        </h6>
                                        <small class="text-muted">Uploaded: Jan 16, 2024</small>
                                    </div>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill"></i> Approved
                                    </span>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('marriage', 1)">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="downloadDocument('marriage', 1)">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOR (If Added by Admin) -->
                    <div class="col-md-6">
                        <div class="card border-primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            <i class="bi bi-clipboard-data text-primary me-2"></i>Transcript of Records (TOR)
                                        </h6>
                                        <small class="text-muted">Added by Admin: Feb 01, 2024</small>
                                    </div>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-clipboard-check-fill"></i> Graded
                                    </span>
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('tor', 1)">
                                        <i class="bi bi-eye"></i> View TOR
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" onclick="downloadDocument('tor', 1)">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="editTOR(1)">
                                        <i class="bi bi-pencil"></i> Edit Grades
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Status Legend -->
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Document Status Legend</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <span class="badge bg-success me-2">
                                    <i class="bi bi-file-earmark-check-fill"></i>
                                </span>
                                <small>Approved by Admin</small>
                            </div>
                            <div class="col-md-4">
                                <span class="badge bg-warning me-2">
                                    <i class="bi bi-clock-fill"></i>
                                </span>
                                <small>Pending Approval</small>
                            </div>
                            <div class="col-md-4">
                                <span class="badge bg-secondary me-2">
                                    <i class="bi bi-x-circle-fill"></i>
                                </span>
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
                <button type="button" class="btn btn-primary" onclick="printStudentRecord()">
                    <i class="bi bi-printer me-1"></i>Print Record
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// View student details
function viewStudentDetails(studentId) {
    const modal = new bootstrap.Modal(document.getElementById('studentDetailsModal'));
    modal.show();
    console.log('Viewing student #' + studentId);
}

// View document
function viewDocument(docType, studentId) {
    alert(`Viewing ${docType} for student #${studentId}`);
    // Implement document viewer
}

// Download document
function downloadDocument(docType, studentId) {
    alert(`Downloading ${docType} for student #${studentId}`);
    // Implement download logic
}

// Edit TOR
function editTOR(studentId) {
    alert('Redirecting to TOR grading page for student #' + studentId);
    // window.location.href = '../tor-grades/tor-grades.php?student=' + studentId;
}

// Print student record
function printStudentRecord() {
    alert('Printing student record...');
    // Implement print logic
}

// Apply filters
function applyFilters() {
    const search = document.getElementById('searchStudent').value;
    const school = document.getElementById('filterSchool').value;
    const course = document.getElementById('filterCourse').value;
    const docStatus = document.getElementById('filterDocStatus').value;
    
    console.log('Applying filters:', { search, school, course, docStatus });
    alert('Filters applied! (Demo)');
}
</script>

<?php
// Include footer
include('../footer/footer.php');
?>