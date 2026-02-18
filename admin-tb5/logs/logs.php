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
                            <i class="bi bi-clock-history me-2"></i>Activity Logs
                        </h2>
                        <p class="text-white-50 mb-0">
                            Track and monitor all admin activities and system changes
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                            <button class="btn btn-light btn-sm" onclick="exportLogs()">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <button class="btn btn-light btn-sm" onclick="clearOldLogs()">
                                <i class="bi bi-trash me-1"></i> Clear Old
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
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-list-ul text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Total Logs</h6>
                                <h3 class="mb-0 fw-bold">1,247</h3>
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
                                <i class="bi bi-calendar-day text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Today</h6>
                                <h3 class="mb-0 fw-bold">42</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Critical Actions</h6>
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
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-shield-check text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Active Admins</h6>
                                <h3 class="mb-0 fw-bold">6</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Search Bar -->
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search logs...">
                        </div>
                    </div>
                    
                    <!-- Admin Filter -->
                    <div class="col-md-2">
                        <select class="form-select" id="adminFilter">
                            <option value="">All Admins</option>
                            <option value="admin1">Admin Rodriguez</option>
                            <option value="admin2">Maria Santos</option>
                            <option value="admin3">Carlos Martinez</option>
                        </select>
                    </div>
                    
                    <!-- Action Type Filter -->
                    <div class="col-md-2">
                        <select class="form-select" id="actionTypeFilter">
                            <option value="">All Actions</option>
                            <option value="create">Create</option>
                            <option value="update">Update</option>
                            <option value="delete">Delete</option>
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                        </select>
                    </div>
                    
                    <!-- Date Filter -->
                    <div class="col-md-2">
                        <select class="form-select" id="dateFilter">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>
                    
                    <!-- Reset Button -->
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" id="resetFilters">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Logs Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Activity Timeline</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshLogs()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
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
                    <table class="table table-hover align-middle mb-0" id="logsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">Timestamp</th>
                                <th>Admin</th>
                                <th>Action Type</th>
                                <th>Description</th>
                                <th>Target</th>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Log Entry 1 - Document Approval -->
                            <tr data-admin="admin1" data-action="approve" data-date="today">
                                <td class="px-4">
                                    <div class="fw-semibold">Feb 16, 2026</div>
                                    <small class="text-muted">10:30:45 AM</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-shield-fill-check text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Admin Rodriguez</div>
                                            <small class="text-muted">admin@tb5.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Approve
                                    </span>
                                </td>
                                <td>Approved document submission</td>
                                <td>
                                    <span class="text-primary">Birth Certificate</span><br>
                                    <small class="text-muted">Juan Dela Cruz</small>
                                </td>
                                <td><code>192.168.1.101</code></td>
                                <td><span class="badge bg-success">Success</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewLogDetails(1)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Log Entry 2 - Batch Creation -->
                            <tr data-admin="admin1" data-action="create" data-date="today">
                                <td class="px-4">
                                    <div class="fw-semibold">Feb 16, 2026</div>
                                    <small class="text-muted">09:15:22 AM</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-shield-fill-check text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Admin Rodriguez</div>
                                            <small class="text-muted">admin@tb5.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Create
                                    </span>
                                </td>
                                <td>Created new batch</td>
                                <td>
                                    <span class="text-primary">Batch 2026-01</span><br>
                                    <small class="text-muted">CSS Course - TB5</small>
                                </td>
                                <td><code>192.168.1.101</code></td>
                                <td><span class="badge bg-success">Success</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewLogDetails(2)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Log Entry 3 - Document Rejection -->
                            <tr data-admin="admin2" data-action="reject" data-date="today">
                                <td class="px-4">
                                    <div class="fw-semibold">Feb 16, 2026</div>
                                    <small class="text-muted">08:45:10 AM</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-shield-fill-check text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Maria Santos</div>
                                            <small class="text-muted">maria@tb5.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Reject
                                    </span>
                                </td>
                                <td>Rejected document submission</td>
                                <td>
                                    <span class="text-primary">Medical Certificate</span><br>
                                    <small class="text-muted">Carlos Martinez</small>
                                </td>
                                <td><code>192.168.1.105</code></td>
                                <td><span class="badge bg-success">Success</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewLogDetails(3)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Log Entry 4 - Account Update -->
                            <tr data-admin="admin1" data-action="update" data-date="today">
                                <td class="px-4">
                                    <div class="fw-semibold">Feb 16, 2026</div>
                                    <small class="text-muted">07:30:55 AM</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-shield-fill-check text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Admin Rodriguez</div>
                                            <small class="text-muted">admin@tb5.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning">
                                        <i class="bi bi-pencil me-1"></i>Update
                                    </span>
                                </td>
                                <td>Updated user account information</td>
                                <td>
                                    <span class="text-primary">User Account</span><br>
                                    <small class="text-muted">Pedro Reyes (#U003)</small>
                                </td>
                                <td><code>192.168.1.101</code></td>
                                <td><span class="badge bg-success">Success</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewLogDetails(4)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Log Entry 5 - Course Update -->
                            <tr data-admin="admin2" data-action="update" data-date="yesterday">
                                <td class="px-4">
                                    <div class="fw-semibold">Feb 15, 2026</div>
                                    <small class="text-muted">04:20:18 PM</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-shield-fill-check text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Maria Santos</div>
                                            <small class="text-muted">maria@tb5.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning">
                                        <i class="bi bi-pencil me-1"></i>Update
                                    </span>
                                </td>
                                <td>Modified course details</td>
                                <td>
                                    <span class="text-primary">CSS Course</span><br>
                                    <small class="text-muted">Duration changed</small>
                                </td>
                                <td><code>192.168.1.105</code></td>
                                <td><span class="badge bg-success">Success</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewLogDetails(5)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Log Entry 6 - Login Activity -->
                            <tr data-admin="admin3" data-action="login" data-date="yesterday">
                                <td class="px-4">
                                    <div class="fw-semibold">Feb 15, 2026</div>
                                    <small class="text-muted">02:10:30 PM</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-shield-fill-check text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Carlos Martinez</div>
                                            <small class="text-muted">carlos@tb5.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                                    </span>
                                </td>
                                <td>Admin logged into system</td>
                                <td>
                                    <span class="text-primary">System Login</span><br>
                                    <small class="text-muted">Web Dashboard</small>
                                </td>
                                <td><code>192.168.1.120</code></td>
                                <td><span class="badge bg-success">Success</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewLogDetails(6)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Log Entry 7 - Account Deletion -->
                            <tr data-admin="admin1" data-action="delete" data-date="yesterday">
                                <td class="px-4">
                                    <div class="fw-semibold">Feb 15, 2026</div>
                                    <small class="text-muted">11:45:00 AM</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-shield-fill-check text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Admin Rodriguez</div>
                                            <small class="text-muted">admin@tb5.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </span>
                                </td>
                                <td>Deleted user account</td>
                                <td>
                                    <span class="text-primary">User Account</span><br>
                                    <small class="text-muted">Test User (#U999)</small>
                                </td>
                                <td><code>192.168.1.101</code></td>
                                <td><span class="badge bg-warning">Critical</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewLogDetails(7)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Log Entry 8 - TOR Grade Entry -->
                            <tr data-admin="admin2" data-action="create" data-date="yesterday">
                                <td class="px-4">
                                    <div class="fw-semibold">Feb 15, 2026</div>
                                    <small class="text-muted">09:30:12 AM</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-shield-fill-check text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Maria Santos</div>
                                            <small class="text-muted">maria@tb5.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Create
                                    </span>
                                </td>
                                <td>Added TOR grades</td>
                                <td>
                                    <span class="text-primary">Grade Entry</span><br>
                                    <small class="text-muted">Ana Garcia - CSS</small>
                                </td>
                                <td><code>192.168.1.105</code></td>
                                <td><span class="badge bg-success">Success</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" onclick="viewLogDetails(8)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing <span id="showingStart">1</span> to <span id="showingEnd">8</span> of <span id="totalEntries">1247</span> entries
                    </div>
                    <nav>
                        <ul class="pagination mb-0" id="pagination">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">...</a></li>
                            <li class="page-item"><a class="page-link" href="#">156</a></li>
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

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activity Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Log ID</label>
                        <p class="fw-semibold">#LOG-2026-001</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Timestamp</label>
                        <p class="fw-semibold">Feb 16, 2026 10:30:45 AM</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Admin</label>
                        <p class="fw-semibold">Admin Rodriguez (admin@tb5.com)</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">IP Address</label>
                        <p><code>192.168.1.101</code></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Action Type</label>
                        <p><span class="badge bg-success">Approve</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Status</label>
                        <p><span class="badge bg-success">Success</span></p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Description</label>
                        <p class="fw-semibold">Approved document submission</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Target</label>
                        <p>Birth Certificate - Juan Dela Cruz</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Additional Details</label>
                        <div class="bg-light p-3 rounded">
                            <pre class="mb-0"><code>{
  "document_id": "DOC-2026-042",
  "student_id": "2024-001",
  "document_type": "birth_certificate",
  "approval_status": "approved",
  "approval_comments": "Document verified and accepted"
}</code></pre>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">User Agent</label>
                        <p class="small">Mozilla/5.0 (Windows NT 10.0; Win64; x64)</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Session ID</label>
                        <p class="small"><code>sess_abc123xyz789</code></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#tableBody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
    updatePaginationInfo();
});

// Admin filter
document.getElementById('adminFilter').addEventListener('change', function() {
    filterLogs();
});

// Action type filter
document.getElementById('actionTypeFilter').addEventListener('change', function() {
    filterLogs();
});

// Date filter
document.getElementById('dateFilter').addEventListener('change', function() {
    filterLogs();
});

// Filter logs based on admin, action type, and date
function filterLogs() {
    const admin = document.getElementById('adminFilter').value;
    const actionType = document.getElementById('actionTypeFilter').value;
    const date = document.getElementById('dateFilter').value;
    const tableRows = document.querySelectorAll('#tableBody tr');
    
    tableRows.forEach(row => {
        const rowAdmin = row.getAttribute('data-admin');
        const rowAction = row.getAttribute('data-action');
        const rowDate = row.getAttribute('data-date');
        
        const adminMatch = !admin || rowAdmin === admin;
        const actionMatch = !actionType || rowAction === actionType;
        const dateMatch = !date || rowDate === date;
        
        row.style.display = (adminMatch && actionMatch && dateMatch) ? '' : 'none';
    });
    updatePaginationInfo();
}

// Reset filters
document.getElementById('resetFilters').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('adminFilter').value = '';
    document.getElementById('actionTypeFilter').value = '';
    document.getElementById('dateFilter').value = '';
    
    const tableRows = document.querySelectorAll('#tableBody tr');
    tableRows.forEach(row => row.style.display = '');
    updatePaginationInfo();
});

// View log details
function viewLogDetails(logId) {
    const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
    modal.show();
    // Load log details based on ID
}

// Export logs
function exportLogs() {
    alert('Exporting logs to CSV...');
    // Add your export logic here
}

// Clear old logs
function clearOldLogs() {
    if (confirm('Are you sure you want to clear logs older than 90 days?')) {
        alert('Old logs cleared successfully!');
        // Add your clear logs logic here
    }
}

// Refresh logs
function refreshLogs() {
    alert('Refreshing logs...');
    location.reload();
}

// Update pagination info
function updatePaginationInfo() {
    const visibleRows = document.querySelectorAll('#tableBody tr:not([style*="display: none"])').length;
    document.getElementById('showingEnd').textContent = visibleRows;
}
</script>

<?php
    // Include footer
    include('../footer/footer.php');
?>  