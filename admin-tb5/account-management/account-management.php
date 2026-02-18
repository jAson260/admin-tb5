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
                            <i class="bi bi-people-fill me-2"></i>Account Management
                        </h2>
                        <p class="text-white-50 mb-0">
                            Manage user and admin accounts, permissions, and access control
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                            <button class="btn btn-light btn-sm">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                            <button class="btn btn-light btn-sm" onclick="showAddAccountModal()">
                                <i class="bi bi-plus-circle me-1"></i> Add Account
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
                                <i class="bi bi-people-fill text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Total Users</h6>
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
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-shield-check text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Admins</h6>
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
                                <i class="bi bi-check-circle text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Active</h6>
                                <h3 class="mb-0 fw-bold">328</h3>
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
                                <i class="bi bi-pause-circle text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Disabled</h6>
                                <h3 class="mb-0 fw-bold">22</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filters and Actions Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <!-- Search Bar -->
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Search by name, email, ID...">
                        </div>
                    </div>
                    
                    <!-- Role Filter -->
                    <div class="col-md-2">
                        <select class="form-select" id="roleFilter">
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="disabled">Disabled</option>
                        </select>
                    </div>
                    
                    <!-- Reset Button -->
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" id="resetFilters">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </button>
                    </div>

                    <!-- Add Account Button -->
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" onclick="showAddAccountModal()">
                            <i class="bi bi-plus-circle me-1"></i> Add Account
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accounts Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Account List</h5>
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
                    <table class="table table-hover align-middle mb-0" id="accountsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Admin Accounts -->
                            <tr data-role="admin" data-status="active">
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td><span class="text-muted">#A001</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-shield-fill-check text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Admin Rodriguez</div>
                                            <small class="text-muted">Super Admin</small>
                                        </div>
                                    </div>
                                </td>
                                <td>admin.rodriguez@tb5.com</td>
                                <td><span class="badge bg-danger">Admin</span></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>Feb 13, 2026 09:30 AM</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" title="View Details" onclick="viewAccount(1)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Change Password" onclick="changePassword(1)">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" title="Disable Account" onclick="toggleStatus(1, 'disable')">
                                            <i class="bi bi-pause-circle"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Delete Account" onclick="deleteAccount(1)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-role="admin" data-status="active">
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td><span class="text-muted">#A002</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-shield-fill-check text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Maria Santos</div>
                                            <small class="text-muted">Admin</small>
                                        </div>
                                    </div>
                                </td>
                                <td>maria.santos@tb5.com</td>
                                <td><span class="badge bg-danger">Admin</span></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>Feb 13, 2026 08:15 AM</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" title="View Details" onclick="viewAccount(2)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Change Password" onclick="changePassword(2)">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" title="Disable Account" onclick="toggleStatus(2, 'disable')">
                                            <i class="bi bi-pause-circle"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Delete Account" onclick="deleteAccount(2)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- User Accounts -->
                            <tr data-role="user" data-status="active">
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td><span class="text-muted">#U001</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Juan Dela Cruz</div>
                                            <small class="text-muted">Student</small>
                                        </div>
                                    </div>
                                </td>
                                <td>juan.delacruz@student.com</td>
                                <td><span class="badge bg-primary">User</span></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>Feb 12, 2026 02:45 PM</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" title="View Details" onclick="viewAccount(3)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Change Password" onclick="changePassword(3)">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" title="Disable Account" onclick="toggleStatus(3, 'disable')">
                                            <i class="bi bi-pause-circle"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Delete Account" onclick="deleteAccount(3)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-role="user" data-status="active">
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td><span class="text-muted">#U002</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Ana Garcia</div>
                                            <small class="text-muted">Student</small>
                                        </div>
                                    </div>
                                </td>
                                <td>ana.garcia@student.com</td>
                                <td><span class="badge bg-primary">User</span></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>Feb 12, 2026 11:20 AM</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" title="View Details" onclick="viewAccount(4)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Change Password" onclick="changePassword(4)">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" title="Disable Account" onclick="toggleStatus(4, 'disable')">
                                            <i class="bi bi-pause-circle"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Delete Account" onclick="deleteAccount(4)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-role="user" data-status="disabled">
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td><span class="text-muted">#U003</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-secondary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Pedro Reyes</div>
                                            <small class="text-muted">Student</small>
                                        </div>
                                    </div>
                                </td>
                                <td>pedro.reyes@student.com</td>
                                <td><span class="badge bg-primary">User</span></td>
                                <td><span class="badge bg-warning">Disabled</span></td>
                                <td>Feb 10, 2026 03:15 PM</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" title="View Details" onclick="viewAccount(5)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Change Password" onclick="changePassword(5)">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        <button class="btn btn-outline-success" title="Enable Account" onclick="toggleStatus(5, 'enable')">
                                            <i class="bi bi-play-circle"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Delete Account" onclick="deleteAccount(5)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-role="user" data-status="active">
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input row-checkbox">
                                </td>
                                <td><span class="text-muted">#U004</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Isabella Rodriguez</div>
                                            <small class="text-muted">Student</small>
                                        </div>
                                    </div>
                                </td>
                                <td>isabella.rodriguez@student.com</td>
                                <td><span class="badge bg-primary">User</span></td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>Feb 13, 2026 07:00 AM</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" title="View Details" onclick="viewAccount(6)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" title="Change Password" onclick="changePassword(6)">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" title="Disable Account" onclick="toggleStatus(6, 'disable')">
                                            <i class="bi bi-pause-circle"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Delete Account" onclick="deleteAccount(6)">
                                            <i class="bi bi-trash"></i>
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
                        Showing <span id="showingStart">1</span> to <span id="showingEnd">6</span> of <span id="totalEntries">350</span> entries
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

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addAccountForm">
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" required>
                            <option value="">Select Role</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAddAccount()">Add Account</button>
            </div>
        </div>
    </div>
</div>

<!-- View Account Details Modal -->
<div class="modal fade" id="viewAccountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Account Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Account ID</label>
                        <p class="fw-semibold">#U001</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Full Name</label>
                        <p class="fw-semibold">Juan Dela Cruz</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Email</label>
                        <p class="fw-semibold">juan.delacruz@student.com</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Role</label>
                        <p><span class="badge bg-primary">User</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Status</label>
                        <p><span class="badge bg-success">Active</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Last Login</label>
                        <p class="fw-semibold">Feb 12, 2026 02:45 PM</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Account Created</label>
                        <p class="fw-semibold">Jan 15, 2026</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Last Updated</label>
                        <p class="fw-semibold">Feb 10, 2026</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Changing password for: <strong id="changePasswordUser"></strong>
                </div>
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" required>
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmNewPassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitChangePassword()">Change Password</button>
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

// Role filter
document.getElementById('roleFilter').addEventListener('change', function() {
    filterTable();
});

// Status filter
document.getElementById('statusFilter').addEventListener('change', function() {
    filterTable();
});

// Filter table based on role and status
function filterTable() {
    const role = document.getElementById('roleFilter').value.toLowerCase();
    const status = document.getElementById('statusFilter').value.toLowerCase();
    const tableRows = document.querySelectorAll('#tableBody tr');
    
    tableRows.forEach(row => {
        const rowRole = row.getAttribute('data-role');
        const rowStatus = row.getAttribute('data-status');
        
        const roleMatch = !role || rowRole === role;
        const statusMatch = !status || rowStatus === status;
        
        row.style.display = (roleMatch && statusMatch) ? '' : 'none';
    });
    updatePaginationInfo();
}

// Reset filters
document.getElementById('resetFilters').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('statusFilter').value = '';
    
    const tableRows = document.querySelectorAll('#tableBody tr');
    tableRows.forEach(row => row.style.display = '');
    updatePaginationInfo();
});

// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        if (checkbox.closest('tr').style.display !== 'none') {
            checkbox.checked = this.checked;
        }
    });
});

// Show Add Account Modal
function showAddAccountModal() {
    const modal = new bootstrap.Modal(document.getElementById('addAccountModal'));
    modal.show();
}

// Submit Add Account
function submitAddAccount() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    if (password.length < 8) {
        alert('Password must be at least 8 characters long!');
        return;
    }
    
    // Add your account creation logic here
    alert('Account created successfully!');
    bootstrap.Modal.getInstance(document.getElementById('addAccountModal')).hide();
    document.getElementById('addAccountForm').reset();
}

// View Account Details
function viewAccount(id) {
    const modal = new bootstrap.Modal(document.getElementById('viewAccountModal'));
    modal.show();
    // Load account details based on ID
}

// Change Password
let currentPasswordChangeId = null;

function changePassword(id) {
    currentPasswordChangeId = id;
    document.getElementById('changePasswordUser').textContent = 'User #' + id;
    const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
    modal.show();
}

// Submit Change Password
function submitChangePassword() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmNewPassword = document.getElementById('confirmNewPassword').value;
    
    if (newPassword !== confirmNewPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    if (newPassword.length < 8) {
        alert('Password must be at least 8 characters long!');
        return;
    }
    
    // Add your password change logic here
    alert('Password changed successfully for account #' + currentPasswordChangeId);
    bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
    document.getElementById('changePasswordForm').reset();
}

// Toggle Account Status (Enable/Disable)
function toggleStatus(id, action) {
    const actionText = action === 'enable' ? 'enable' : 'disable';
    if (confirm(`Are you sure you want to ${actionText} this account?`)) {
        alert(`Account #${id} has been ${actionText}d successfully!`);
        // Add your status toggle logic here
        // Reload or update the table row
        location.reload();
    }
}

// Delete Account
function deleteAccount(id) {
    if (confirm('Are you sure you want to delete this account? This action cannot be undone!')) {
        if (confirm('Final confirmation: Delete this account permanently?')) {
            alert('Account #' + id + ' has been deleted successfully!');
            // Add your delete logic here
            location.reload();
        }
    }
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