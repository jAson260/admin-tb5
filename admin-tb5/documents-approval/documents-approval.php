<?php
// Include header
include('../header/header.php');
include('../sidebar/sidebar.php');
?>  


<div class="content-wrapper">
    <div class="main-content">
        <!-- Page Title -->
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold text-white mb-2">
                    <i class="bi bi-file-earmark-check me-2"></i>Documents Approval
                </h2>
                <p class="text-white-50 mb-0">
                    Review and approve submitted documents from students
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                    <button class="btn btn-light btn-sm">
                        <i class="bi bi-download me-1"></i> Export
                    </button>
                    <button class="btn btn-warning btn-sm">
                        <i class="bi bi-clock-history me-1"></i>
                        <span class="badge bg-danger">24</span> Pending
                    </button>
                </div>
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
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-clock-history text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Pending</h6>
                                <h3 class="mb-0 fw-bold">24</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Approved</h6>
                                <h3 class="mb-0 fw-bold">156</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-x-circle text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Rejected</h6>
                                <h3 class="mb-0 fw-bold">8</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-file-earmark text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Total</h6>
                                <h3 class="mb-0 fw-bold">188</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Search Bar -->
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search by name, document type...">
                        </div>
                    </div>
                    
                    <!-- Status Filter -->
<div class="col-md-2">
    <select class="form-select" id="schoolFilter">
        <option value="" selected disabled>School</option>
        <option value="tb5">TB5</option>
        <option value="bbi">BBI</option>
    </select>
</div>
                    
                    <!-- Course Type Filter -->
<div class="col-md-3">
    <select class="form-select" id="documentTypeFilter" disabled>
        <option value="" selected>Course</option>
    </select>
</div>
                    
                    <!-- Batch Filter -->
                  <div class="col-md-2">
                 <select class="form-select" id="batchFilter">
                 <option value="" selected disabled>Batch</option>
                 <option value="batch-2024-01">Batch 2024-01</option>
                 <option value="batch-2024-02">Batch 2024-02</option>
                 <option value="batch-2025-01">Batch 2025-01</option>
                 <option value="batch-2026-01">Batch 2026-01</option>
              </select>
</div>
                    
                    <!-- Reset Button -->
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100" id="resetFilters">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Submitted Documents</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download me-1"></i> Export
                    </button>
                    <select class="form-select form-select-sm" id="entriesPerPage" style="width: auto;">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="documentsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Student Name</th>
                                <th>Document Type</th>
                                <th>Submission Date</th>
                                <th>File Size</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Row 1 -->
                            <tr>
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Juan Dela Cruz</div>
                                            <small class="text-muted">ID: 2024-001</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-file-earmark-text text-primary me-1"></i>
                                    Birth Certificate
                                </td>
                                <td>Feb 10, 2026</td>
                                <td>2.4 MB</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View" onclick="viewDocument(1)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success" title="Approve" onclick="approveDocument(1)">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Reject" onclick="rejectDocument(1)">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Row 2 -->
                            <tr>
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Maria Santos</div>
                                            <small class="text-muted">ID: 2024-002</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-file-earmark-text text-info me-1"></i>
                                    ID Card
                                </td>
                                <td>Feb 11, 2026</td>
                                <td>1.8 MB</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View" onclick="viewDocument(2)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success" title="Approve" onclick="approveDocument(2)">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Reject" onclick="rejectDocument(2)">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Row 3 -->
                            <tr>
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Pedro Reyes</div>
                                            <small class="text-muted">ID: 2024-003</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-file-earmark-text text-success me-1"></i>
                                    Transcript
                                </td>
                                <td>Feb 12, 2026</td>
                                <td>3.2 MB</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View" onclick="viewDocument(3)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success" title="Approve" onclick="approveDocument(3)">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Reject" onclick="rejectDocument(3)">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Row 4 -->
                            <tr>
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Ana Garcia</div>
                                            <small class="text-muted">ID: 2024-004</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-file-earmark-text text-warning me-1"></i>
                                    Diploma
                                </td>
                                <td>Feb 09, 2026</td>
                                <td>2.1 MB</td>
                                <td><span class="badge bg-success">Approved</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View" onclick="viewDocument(4)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" title="Download" onclick="downloadDocument(4)">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Row 5 -->
                            <tr>
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Carlos Martinez</div>
                                            <small class="text-muted">ID: 2024-005</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-file-earmark-text text-danger me-1"></i>
                                    Medical Certificate
                                </td>
                                <td>Feb 08, 2026</td>
                                <td>1.5 MB</td>
                                <td><span class="badge bg-danger">Rejected</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View" onclick="viewDocument(5)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-info" title="View Reason" onclick="viewReason(5)">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Row 6 -->
                            <tr>
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Isabella Rodriguez</div>
                                            <small class="text-muted">ID: 2024-006</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-file-earmark-text text-primary me-1"></i>
                                    Birth Certificate
                                </td>
                                <td>Feb 13, 2026</td>
                                <td>2.7 MB</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View" onclick="viewDocument(6)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success" title="Approve" onclick="approveDocument(6)">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Reject" onclick="rejectDocument(6)">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Row 7 -->
                            <tr>
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Miguel Torres</div>
                                            <small class="text-muted">ID: 2024-007</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-file-earmark-text text-success me-1"></i>
                                    Transcript
                                </td>
                                <td>Feb 07, 2026</td>
                                <td>3.8 MB</td>
                                <td><span class="badge bg-success">Approved</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View" onclick="viewDocument(7)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" title="Download" onclick="downloadDocument(7)">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Row 8 -->
                            <tr>
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Sofia Fernandez</div>
                                            <small class="text-muted">ID: 2024-008</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="bi bi-file-earmark-text text-info me-1"></i>
                                    ID Card
                                </td>
                                <td>Feb 12, 2026</td>
                                <td>1.9 MB</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="View" onclick="viewDocument(8)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success" title="Approve" onclick="approveDocument(8)">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Reject" onclick="rejectDocument(8)">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing <span id="showingStart">1</span> to <span id="showingEnd">8</span> of <span id="totalEntries">24</span> entries
                    </div>
                    <nav>
                        <ul class="pagination mb-0" id="pagination">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Document View Modal -->
<div class="modal fade" id="viewDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center p-5">
                    <i class="bi bi-file-earmark-text text-primary" style="font-size: 4rem;"></i>
                    <p class="mt-3">Document preview would appear here</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Document Modal -->
<div class="modal fade" id="rejectDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="rejectionReason" class="form-label">Reason for Rejection</label>
                    <textarea class="form-control" id="rejectionReason" rows="4" placeholder="Enter reason for rejection..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Reject Document</button>
            </div>
        </div>
    </div>
</div>

<script>
// Course options for each school
const courseOptions = {
    tb5: [
        { value: 'css', text: 'CSS - Computer Systems Servicing' },
        { value: 'bpp', text: 'BPP - Bread and Pastry Production' },
        { value: 'hsk', text: 'HSK - Housekeeping' },
        { value: 'epas', text: 'EPAS - Electronic Products Assembly and Servicing' },
        { value: 'tmi', text: 'TMI - Trainers Methodology Level I' },
        { value: 'bcl', text: 'BCL - Basic Computer Literacy' }
    ],
    bbi: [
        { value: 'cok', text: 'COK - Cookery' },
        { value: 'hsk', text: 'HSK - Housekeeping' },
        { value: 'eim', text: 'EIM - Electrical Installation and Maintenance' },
        { value: 'fbs', text: 'FBS - Food and Beverage Services' },
        { value: 'evm', text: 'EVM - Events Management Services' }
    ]
};

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#tableBody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// School filter - dynamically populate course dropdown
document.getElementById('schoolFilter').addEventListener('change', function() {
    const school = this.value;
    const courseDropdown = document.getElementById('documentTypeFilter');
    
    // Clear existing options
    courseDropdown.innerHTML = '<option value="" selected>Course</option>';
    
    if (school && courseOptions[school]) {
        // Enable dropdown and populate with courses
        courseDropdown.disabled = false;
        courseOptions[school].forEach(course => {
            const option = document.createElement('option');
            option.value = course.value;
            option.textContent = course.text;
            courseDropdown.appendChild(option);
        });
    } else {
        // Disable dropdown if no school selected
        courseDropdown.disabled = true;
    }
});

// Document type (Course) filter
document.getElementById('documentTypeFilter').addEventListener('change', function() {
    const docType = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#tableBody tr');
    
    tableRows.forEach(row => {
        if (docType === '') {
            row.style.display = '';
        } else {
            const text = row.children[2].textContent.toLowerCase();
            row.style.display = text.includes(docType) ? '' : 'none';
        }
    });
});

// Reset filters
document.getElementById('resetFilters').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('schoolFilter').value = '';
    document.getElementById('documentTypeFilter').innerHTML = '<option value="" selected>Course</option>';
    document.getElementById('documentTypeFilter').disabled = true;
    document.getElementById('batchFilter').value = '';
    
    const tableRows = document.querySelectorAll('#tableBody tr');
    tableRows.forEach(row => row.style.display = '');
});

// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
});

// Document action functions
function viewDocument(id) {
    const modal = new bootstrap.Modal(document.getElementById('viewDocumentModal'));
    modal.show();
}

function approveDocument(id) {
    if (confirm('Are you sure you want to approve this document?')) {
        alert('Document #' + id + ' has been approved successfully!');
        // Add your approval logic here
    }
}

let currentRejectId = null;

function rejectDocument(id) {
    currentRejectId = id;
    const modal = new bootstrap.Modal(document.getElementById('rejectDocumentModal'));
    modal.show();
}

function confirmReject() {
    const reason = document.getElementById('rejectionReason').value;
    if (reason.trim() === '') {
        alert('Please provide a reason for rejection');
        return;
    }
    alert('Document #' + currentRejectId + ' has been rejected.\nReason: ' + reason);
    bootstrap.Modal.getInstance(document.getElementById('rejectDocumentModal')).hide();
    document.getElementById('rejectionReason').value = '';
    // Add your rejection logic here
}

function downloadDocument(id) {
    alert('Downloading document #' + id);
    // Add your download logic here
}

function viewReason(id) {
    alert('Document was rejected due to: Incomplete information or poor image quality');
    // Add your view reason logic here
}
</script>













<?php
    // Include footer
    include('../footer/footer.php');
    ?>