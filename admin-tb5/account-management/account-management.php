<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\account-management.php
session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();

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
                        <button class="btn btn-light btn-sm" onclick="exportAccounts()">
                            <i class="bi bi-download me-1"></i> Export
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
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-people-fill text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Total Users</h6>
                                <h3 class="mb-0 fw-bold" id="statTotal">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                                <small class="text-muted">All accounts</small>
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
                                <i class="bi bi-shield-check text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Admins</h6>
                                <h3 class="mb-0 fw-bold" id="statAdmins">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                                <small class="text-muted">All admin accounts</small>
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
                                <i class="bi bi-person-check text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Students</h6>
                                <h3 class="mb-0 fw-bold" id="statStudents">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                                <small class="text-muted">All student accounts</small>
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
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Active</h6>
                                <h3 class="mb-0 fw-bold" id="statActive">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                                <small class="text-muted">Active accounts</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Search Accounts</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchAccounts" placeholder="Search by name, email, or ID...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Account Type</label>
                        <select class="form-select" id="roleFilter">
                            <option value="">All Accounts</option>
                            <option value="admin">Admins Only</option>
                            <option value="student">Students Only</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" id="resetFilters">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                    </div>
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
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-table me-2"></i>Account List
                    </h5>
                    <button class="btn btn-success btn-sm" onclick="showAssignCourseBatchModal()">
                        <i class="bi bi-clipboard-check me-1"></i>Assign Course and Batch
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table class="table table-hover align-middle" id="accountsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;">
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="card border-0 shadow-sm mt-3" style="display: none;">
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="fw-semibold">
                            <i class="bi bi-check-square me-2"></i>
                            <span id="selectedCount">0</span> account(s) selected
                        </span>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-sm btn-outline-secondary" onclick="$('.account-checkbox').prop('checked', false); updateBulkActions();">
                            <i class="bi bi-x-circle me-1"></i>Clear Selection
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== ADD ACCOUNT MODAL ==================== -->
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-shield-plus me-2"></i>Add New Admin Account
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addAccountForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addFirstName" placeholder="Enter first name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addLastName" placeholder="Enter last name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="addUsername" placeholder="Enter username" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="addEmail" placeholder="Enter email address" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="addPassword" placeholder="Enter password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleAddPassword()">
                                    <i class="bi bi-eye" id="addPasswordEyeIcon"></i>
                                </button>
                            </div>
                            <div class="form-text">Minimum 8 characters</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="addRole" required>
                                <option value="">Select Role</option>
                                <option value="SuperAdmin">Super Admin</option>
                                <option value="Admin">Admin</option>
                                <option value="Staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="addStatus">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Suspended">Suspended</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary px-4" id="submitAddAccountBtn" onclick="submitAddAccount()">
                    <i class="bi bi-shield-plus me-1"></i>Create Admin Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== APPROVE ACCOUNT MODAL ==================== -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-success bg-opacity-10">
                <h5 class="modal-title text-success fw-bold">
                    <i class="bi bi-check-circle-fill me-2"></i>Approve Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-person-check-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Approve Account?</h5>
                <p class="text-muted mb-3">
                    You are about to approve the account for<br>
                    <strong id="approveAccountName" class="text-dark"></strong>
                </p>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    This will grant them access to the system.
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success px-4" id="confirmApproveBtn">
                    <i class="bi bi-check-circle me-1"></i>Approve Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== REJECT ACCOUNT MODAL ==================== -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-danger bg-opacity-10">
                <h5 class="modal-title text-danger fw-bold">
                    <i class="bi bi-x-circle-fill me-2"></i>Reject Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="bi bi-person-x-fill text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-center mb-2">Reject Account?</h5>
                <p class="text-muted text-center mb-3">
                    You are about to reject the account for<br>
                    <strong id="rejectAccountName" class="text-dark"></strong>
                </p>
                <div class="mb-3">
                    <label for="rejectionReason" class="form-label fw-semibold">
                        <i class="bi bi-pencil-square me-1"></i>Rejection Reason <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="rejectionReason" rows="4" 
                              placeholder="Please provide a reason for rejection..." 
                              required></textarea>
                    <div class="form-text">This message will be sent to the user.</div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" id="confirmRejectBtn">
                    <i class="bi bi-x-circle me-1"></i>Reject Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== VIEW ACCOUNT MODAL ==================== -->
<div class="modal fade" id="viewAccountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-person-circle me-2"></i>Account Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="accountDetailsBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== CHANGE PASSWORD MODAL ==================== -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-key me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Changing password for: <strong id="changePasswordUser"></strong>
                </div>
                <form id="changePasswordForm">
                    <input type="hidden" id="changePasswordId">
                    <input type="hidden" id="changePasswordType">
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
                <button type="button" class="btn btn-primary" onclick="submitChangePassword()">
                    <i class="bi bi-check-circle me-1"></i>Change Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== DELETE ACCOUNT MODAL ==================== -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-danger bg-opacity-10">
                <h5 class="modal-title text-danger fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-trash-fill text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Delete Account?</h5>
                <p class="text-muted mb-3">
                    You are about to permanently delete the account for<br>
                    <strong id="deleteAccountName" class="text-dark"></strong>
                </p>
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone!
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== ASSIGN COURSE AND BATCH MODAL ==================== -->
<div class="modal fade" id="assignCourseBatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-success bg-opacity-10">
                <h5 class="modal-title text-success fw-bold">
                    <i class="bi bi-clipboard-check me-2"></i>Assign Course and Batch
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    Select students from the table and assign them to a course and batch.
                </div>
                <form id="assignCourseBatchForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="assignSchool" class="form-label fw-semibold">School <span class="text-danger">*</span></label>
                            <select class="form-select" id="assignSchool" required>
                                <option value="">Select School</option>
                                <option value="TB5">TB5</option>
                                <option value="BBI">BBI</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="assignCourse" class="form-label fw-semibold">Course <span class="text-danger">*</span></label>
                            <select class="form-select" id="assignCourse" required disabled>
                                <option value="">Select School First</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="assignBatch" class="form-label fw-semibold">Batch <span class="text-danger">*</span></label>
                            <select class="form-select" id="assignBatch" required disabled>
                                <option value="">Select Course First</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-4">
                            <label class="form-label fw-semibold">Selected Students</label>
                            <div id="selectedStudentsList" class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                <p class="text-muted mb-0 text-center">No students selected</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success px-4" onclick="submitAssignCourseBatch()">
                    <i class="bi bi-check-circle me-1"></i>Assign to Students
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let accountsTable;
let currentApproveId = null;
let currentApproveFullName = null;
let currentRejectId = null;
let currentRejectFullName = null;
let currentDeleteId = null;
let currentDeleteType = null;
let currentDeleteFullName = null;

$(document).ready(function() {
    // Initialize DataTable
    accountsTable = $('#accountsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get-accounts.php',
            type: 'POST',
            data: function(d) {
                d.roleFilter = $('#roleFilter').val();
                d.statusFilter = $('#statusFilter').val();
                d.search = $('#searchAccounts').val();
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable Error:', error, thrown, xhr.responseText);
            }
        },
        columns: [
            { 
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `<input type="checkbox" class="form-check-input account-checkbox" value="${row.Id}" data-type="${row.AccountType}">`;
                }
            },
            { 
                data: 'Id',
                render: function(data, type, row) {
                    const prefix = row.AccountType === 'admin' ? 'A' : 'S';
                    return `<span class="text-muted fw-semibold">#${prefix}${data}</span>`;
                }
            },
            { 
                data: 'FullName',
                render: function(data, type, row) {
                    const isAdmin = row.AccountType === 'admin';
                    const iconClass = isAdmin ? 'bi-shield-fill-check' : 'bi-person-fill';
                    const iconColor = isAdmin ? 'danger' : 'primary';
                    const subtitle = isAdmin ? row.Role : 'Student';
                    return `
                        <div class="d-flex align-items-center">
                            <div class="bg-${iconColor} bg-opacity-10 rounded-circle p-2 me-2">
                                <i class="bi ${iconClass} text-${iconColor}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">${escapeHtml(data)}</div>
                                <small class="text-muted">${escapeHtml(subtitle)}</small>
                            </div>
                        </div>
                    `;
                }
            },
            { 
                data: 'Email',
                render: function(data) { return escapeHtml(data); }
            },
            { 
                data: 'AccountType',
                render: function(data) {
                    const isAdmin = data === 'admin';
                    return `<span class="badge ${isAdmin ? 'bg-danger' : 'bg-info'}">${isAdmin ? 'Admin' : 'Student'}</span>`;
                }
            },
            { 
                data: 'Status',
                render: function(data) { return getStatusBadge(data); }
            },
            { 
                data: 'LastLogin',
                render: function(data) {
                    return data ? formatDateTime(data) : '<span class="text-muted">Never</span>';
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    const status = row.Status;
                    const isStudent = row.AccountType === 'student';
                    const isPending = status === 'Pending';
                    const isRejected = status === 'Rejected';
                    const fullName = escapeHtml(row.FullName);
                    
                    let buttons = '<div class="btn-group btn-group-sm" role="group">';
                    
                    if (isStudent && (isPending || isRejected)) {
                        buttons += `<button class="btn btn-outline-success" title="Approve Account" onclick="showApproveModal(${row.Id}, '${fullName}')"><i class="bi bi-check-circle"></i></button>`;
                    }
                    if (isStudent && isPending) {
                        buttons += `<button class="btn btn-outline-danger" title="Reject Account" onclick="showRejectModal(${row.Id}, '${fullName}')"><i class="bi bi-x-circle"></i></button>`;
                    }
                    
                    buttons += `
                        <button class="btn btn-outline-info" title="View Details" onclick="viewAccount(${row.Id}, '${row.AccountType}')"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-outline-warning" title="Change Password" onclick="changePassword(${row.Id}, '${row.AccountType}', '${fullName}')"><i class="bi bi-key"></i></button>
                        <button class="btn btn-outline-danger" title="Delete Account" onclick="deleteAccount(${row.Id}, '${row.AccountType}', '${fullName}')"><i class="bi bi-trash"></i></button>
                    </div>`;
                    
                    return buttons;
                }
            }
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[1, 'desc']],
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6">>rtip',
        language: {
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ accounts",
            infoEmpty: "No accounts found",
            infoFiltered: "(filtered from _MAX_ total accounts)",
            zeroRecords: "No matching accounts found",
            emptyTable: "No accounts available",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        drawCallback: function() { updateStatistics(); }
    });

    // Custom search with debounce
    let searchTimeout;
    $('#searchAccounts').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() { accountsTable.ajax.reload(); }, 500);
    });

    // Filter changes
    $('#roleFilter, #statusFilter').on('change', function() { accountsTable.ajax.reload(); });

    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#searchAccounts').val('');
        $('#roleFilter').val('');
        $('#statusFilter').val('');
        accountsTable.ajax.reload();
    });

    // Select all checkboxes
    $('#selectAll').on('change', function() {
        $('.account-checkbox').prop('checked', this.checked);
        updateBulkActions();
    });

    // Individual checkbox change
    $(document).on('change', '.account-checkbox', function() {
        updateBulkActions();
        const total = $('.account-checkbox').length;
        const checked = $('.account-checkbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
    });

    // Approve confirm button
    $('#confirmApproveBtn').on('click', function() {
        if (currentApproveId) approveAccount(currentApproveId, currentApproveFullName);
    });

    // Reject confirm button
    $('#confirmRejectBtn').on('click', function() {
        const reason = $('#rejectionReason').val().trim();
        if (!reason) { alert('Please provide a rejection reason!'); return; }
        if (currentRejectId) rejectAccount(currentRejectId, currentRejectFullName, reason);
    });

    // Delete confirm button
    $('#confirmDeleteBtn').on('click', function() {
        if (currentDeleteId && currentDeleteType) {
            performDeleteAccount(currentDeleteId, currentDeleteType, currentDeleteFullName);
        }
    });

    // Clear rejection reason on modal close
    $('#rejectModal').on('hidden.bs.modal', function() { $('#rejectionReason').val(''); });

    // Load courses when school is selected
    $('#assignSchool').on('change', function() {
        const school = $(this).val();
        const courseDropdown = $('#assignCourse');
        const batchDropdown = $('#assignBatch');

        batchDropdown.prop('disabled', true).html('<option value="">Select Course First</option>');

        if (!school) {
            courseDropdown.prop('disabled', true).html('<option value="">Select School First</option>');
            return;
        }

        courseDropdown.prop('disabled', true).html('<option value="">Loading courses...</option>');

        fetch('../create-batch/get-courses-by-school.php?school=' + encodeURIComponent(school))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses && data.courses.length > 0) {
                    let options = '<option value="">Select Course</option>';
                    data.courses.forEach(course => {
                        options += `<option value="${course.Id}">${escapeHtml(course.CourseCode)} - ${escapeHtml(course.CourseName)}</option>`;
                    });
                    courseDropdown.prop('disabled', false).html(options);
                } else {
                    courseDropdown.html('<option value="">No courses available</option>');
                }
            })
            .catch(error => {
                console.error('Error loading courses:', error);
                courseDropdown.html('<option value="">Error loading courses</option>');
            });
    });

    // Load batches when course is selected
    $('#assignCourse').on('change', function() {
        const courseId = $(this).val();
        const batchDropdown = $('#assignBatch');

        if (!courseId) {
            batchDropdown.prop('disabled', true).html('<option value="">Select Course First</option>');
            return;
        }

        batchDropdown.prop('disabled', true).html('<option value="">Loading batches...</option>');

        $.ajax({
            url: 'get-batches-by-course.php',
            method: 'GET',
            data: { courseId: courseId },
            dataType: 'json',
            success: function(data) {
                if (data.success && data.batches && data.batches.length > 0) {
                    let options = '<option value="">Select Batch</option>';
                    data.batches.forEach(batch => {
                        const availability = batch.MaxStudents ? ` (${batch.CurrentStudents || 0}/${batch.MaxStudents})` : '';
                        options += `<option value="${batch.Id}">${escapeHtml(batch.BatchCode)} - ${escapeHtml(batch.BatchName)}${availability}</option>`;
                    });
                    batchDropdown.prop('disabled', false).html(options);
                } else {
                    batchDropdown.html(`<option value="">${data.message || 'No active batches available'}</option>`);
                }
            },
            error: function(xhr, status, error) {
                console.error('Batch loading error:', error);
                batchDropdown.html('<option value="">Error loading batches</option>');
            }
        });
    });

    // Initial statistics load
    updateStatistics();
});

// ==================== ADD ACCOUNT FUNCTIONS ====================
function showAddAccountModal() {
    $('#addAccountForm')[0].reset();
    const modal = new bootstrap.Modal(document.getElementById('addAccountModal'));
    modal.show();
}

function toggleAddPassword() {
    const input = $('#addPassword');
    const icon = $('#addPasswordEyeIcon');
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
        input.attr('type', 'password');
        icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
}

function submitAddAccount() {
    const firstName = $('#addFirstName').val().trim();
    const lastName  = $('#addLastName').val().trim();
    const username  = $('#addUsername').val().trim();
    const email     = $('#addEmail').val().trim();
    const password  = $('#addPassword').val();
    const role      = $('#addRole').val();
    const status    = $('#addStatus').val();

    if (!firstName || !lastName || !username || !email || !password || !role) {
        alert('Please fill in all required fields!');
        return;
    }

    if (password.length < 8) {
        alert('Password must be at least 8 characters long!');
        return;
    }

    const submitBtn = $('#submitAddAccountBtn');
    const originalHtml = submitBtn.html();
    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Creating...');

    $.ajax({
        url: 'add-account.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ firstName, lastName, username, email, password, role, status }),
        dataType: 'json',
        success: function(response) {
            submitBtn.prop('disabled', false).html(originalHtml);
            if (response.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addAccountModal'));
                modal.hide();
                $('#addAccountForm')[0].reset();
                accountsTable.ajax.reload();
                updateStatistics();
                showToast('success', 'Account Created', `${firstName} ${lastName}'s admin account has been created successfully.`);
            } else {
                showToast('error', 'Creation Failed', response.message || 'Failed to create account.');
            }
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).html(originalHtml);
            console.error('Error:', xhr.responseText);
            showToast('error', 'Creation Failed', 'An error occurred. Check console for details.');
        }
    });
}

// ==================== EXISTING FUNCTIONS ====================
function updateStatistics() {
    $.ajax({
        url: 'get-statistics.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                $('#statTotal').text(data.statistics.total);
                $('#statAdmins').text(data.statistics.admins);
                $('#statStudents').text(data.statistics.students);
                $('#statActive').text(data.statistics.active);
            }
        },
        error: function(xhr, status, error) { console.error('Statistics Error:', error); }
    });
}

function showApproveModal(id, fullName) {
    currentApproveId = id;
    currentApproveFullName = fullName;
    $('#approveAccountName').text(fullName);
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function showRejectModal(id, fullName) {
    currentRejectId = id;
    currentRejectFullName = fullName;
    $('#rejectAccountName').text(fullName);
    $('#rejectionReason').val('');
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function approveAccount(id, fullName) {
    $.ajax({
        url: 'approve-account.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id: id }),
        dataType: 'json',
        success: function(data) {
            bootstrap.Modal.getInstance(document.getElementById('approveModal')).hide();
            if (data.success) {
                accountsTable.ajax.reload();
                updateStatistics();
            } else {
                alert('Failed to approve account: ' + data.message);
            }
        },
        error: function(xhr) { console.error('Error:', xhr.responseText); }
    });
}

function rejectAccount(id, fullName, reason) {
    $.ajax({
        url: 'reject-account.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id: id, reason: reason }),
        dataType: 'json',
        success: function(data) {
            bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
            if (data.success) {
                accountsTable.ajax.reload();
                updateStatistics();
            } else {
                alert('Failed to reject account: ' + data.message);
            }
        },
        error: function(xhr) { console.error('Error:', xhr.responseText); }
    });
}

function viewAccount(id, type) {
    $.ajax({
        url: 'get-account-details.php',
        method: 'GET',
        data: { id: id, type: type },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                const account = data.account;
                const isAdmin = type === 'admin';
                let detailsHtml = '';

                if (isAdmin) {
                    detailsHtml = `
                        <div class="row g-3">
                            <div class="col-md-6"><label class="text-muted small">Account ID</label><p class="fw-semibold">#A${account.Id}</p></div>
                            <div class="col-md-6"><label class="text-muted small">Account Type</label><p><span class="badge bg-danger">Admin</span></p></div>
                            <div class="col-md-6"><label class="text-muted small">Full Name</label><p class="fw-semibold">${escapeHtml(account.FullName)}</p></div>
                            <div class="col-md-6"><label class="text-muted small">Email</label><p class="fw-semibold">${escapeHtml(account.Email)}</p></div>
                            <div class="col-md-6"><label class="text-muted small">Role</label><p><span class="badge bg-primary">${escapeHtml(account.Role)}</span></p></div>
                            <div class="col-md-6"><label class="text-muted small">Status</label><p>${getStatusBadge(account.Status)}</p></div>
                            <div class="col-md-6"><label class="text-muted small">Last Login</label><p class="fw-semibold">${account.LastLogin ? formatDateTime(account.LastLogin) : 'Never'}</p></div>
                            <div class="col-md-6"><label class="text-muted small">Account Created</label><p class="fw-semibold">${formatDateTime(account.CreatedAt)}</p></div>
                        </div>`;
                } else {
                    const fullAddress = [account.Street, account.BarangayName, account.CityName, account.ProvinceName, account.RegionName].filter(Boolean).join(', ');
                    detailsHtml = `
                        <div class="row g-3">
                            <div class="col-12"><h6 class="fw-bold text-primary border-bottom pb-2"><i class="bi bi-person me-2"></i>Basic Information</h6></div>
                            <div class="col-md-4"><label class="text-muted small">Student ID</label><p class="fw-semibold">#S${account.Id}</p></div>
                            <div class="col-md-4"><label class="text-muted small">ULI Number</label><p class="fw-semibold">${escapeHtml(account.ULI || 'N/A')}</p></div>
                            <div class="col-md-4"><label class="text-muted small">Status</label><p>${getStatusBadge(account.Status)}</p></div>
                            <div class="col-md-6"><label class="text-muted small">Full Name</label><p class="fw-semibold">${escapeHtml(account.FirstName)} ${escapeHtml(account.MiddleName || '')} ${escapeHtml(account.LastName)}</p></div>
                            <div class="col-md-6"><label class="text-muted small">Email</label><p class="fw-semibold">${escapeHtml(account.Email)}</p></div>
                            <div class="col-12 mt-3"><h6 class="fw-bold text-primary border-bottom pb-2"><i class="bi bi-card-text me-2"></i>Personal Details</h6></div>
                            <div class="col-md-3"><label class="text-muted small">Birth Date</label><p class="fw-semibold">${formatDate(account.BirthDate)}</p></div>
                            <div class="col-md-3"><label class="text-muted small">Age</label><p class="fw-semibold">${account.Age} years old</p></div>
                            <div class="col-md-3"><label class="text-muted small">Sex</label><p class="fw-semibold">${escapeHtml(account.Sex)}</p></div>
                            <div class="col-md-3"><label class="text-muted small">Civil Status</label><p class="fw-semibold">${escapeHtml(account.CivilStatus)}</p></div>
                            <div class="col-md-6"><label class="text-muted small">Contact Number</label><p class="fw-semibold">${escapeHtml(account.ContactNo)}</p></div>
                            <div class="col-md-12"><label class="text-muted small">Complete Address</label><p class="fw-semibold">${escapeHtml(fullAddress)}</p></div>
                            <div class="col-12 mt-3"><h6 class="fw-bold text-primary border-bottom pb-2"><i class="bi bi-gear me-2"></i>Account Information</h6></div>
                            <div class="col-md-4"><label class="text-muted small">Last Login</label><p class="fw-semibold">${account.LastLogin ? formatDateTime(account.LastLogin) : 'Never'}</p></div>
                            <div class="col-md-4"><label class="text-muted small">Registration Date</label><p class="fw-semibold">${formatDateTime(account.CreatedAt)}</p></div>
                        </div>`;
                }

                $('#accountDetailsBody').html(detailsHtml);
                new bootstrap.Modal(document.getElementById('viewAccountModal')).show();
            } else {
                alert('Failed to load account details: ' + data.message);
            }
        },
        error: function(xhr) { console.error('AJAX Error:', xhr.responseText); }
    });
}

function changePassword(id, type, fullName) {
    $('#changePasswordId').val(id);
    $('#changePasswordType').val(type);
    $('#changePasswordUser').text(fullName);
    $('#changePasswordForm')[0].reset();
    new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
}

function submitChangePassword() {
    const id = $('#changePasswordId').val();
    const type = $('#changePasswordType').val();
    const newPassword = $('#newPassword').val();
    const confirmPassword = $('#confirmNewPassword').val();

    if (newPassword !== confirmPassword) { alert('Passwords do not match!'); return; }
    if (newPassword.length < 8) { alert('Password must be at least 8 characters long!'); return; }

    $.ajax({
        url: 'change-password.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id, type, password: newPassword }),
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
                $('#changePasswordForm')[0].reset();
                showToast('success', 'Password Changed', 'Password has been changed successfully.');
            } else {
                alert('Failed to change password: ' + data.message);
            }
        },
        error: function(xhr) { console.error('Error:', xhr.responseText); }
    });
}

function deleteAccount(id, type, fullName) {
    currentDeleteId = id;
    currentDeleteType = type;
    currentDeleteFullName = fullName;
    $('#deleteAccountName').text(fullName);
    new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
}

function performDeleteAccount(id, type, fullName) {
    const confirmBtn = $('#confirmDeleteBtn');
    const originalHtml = confirmBtn.html();
    confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Deleting...');

    $.ajax({
        url: 'delete-account.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id, type }),
        dataType: 'json',
        success: function(data) {
            bootstrap.Modal.getInstance(document.getElementById('deleteAccountModal')).hide();
            confirmBtn.prop('disabled', false).html(originalHtml);
            if (data.success) {
                showToast('success', 'Account Deleted', `${fullName}'s account has been deleted successfully.`);
                accountsTable.ajax.reload();
                updateStatistics();
                currentDeleteId = null; currentDeleteType = null; currentDeleteFullName = null;
            } else {
                showToast('error', 'Delete Failed', data.message);
            }
        },
        error: function(xhr) {
            bootstrap.Modal.getInstance(document.getElementById('deleteAccountModal')).hide();
            confirmBtn.prop('disabled', false).html(originalHtml);
            showToast('error', 'Delete Failed', 'An error occurred. Check console for details.');
        }
    });
}

function showAssignCourseBatchModal() {
    const selected = $('.account-checkbox:checked');
    if (selected.length === 0) { alert('Please select at least one student.'); return; }

    let hasNonStudent = false;
    selected.each(function() { if ($(this).data('type') === 'admin') { hasNonStudent = true; return false; } });
    if (hasNonStudent) { alert('You can only assign courses to student accounts.'); return; }

    updateSelectedStudentsList();
    $('#assignCourseBatchForm')[0].reset();
    $('#assignCourse').prop('disabled', true).html('<option value="">Select School First</option>');
    $('#assignBatch').prop('disabled', true).html('<option value="">Select Course First</option>');
    new bootstrap.Modal(document.getElementById('assignCourseBatchModal')).show();
}

function updateSelectedStudentsList() {
    const selected = $('.account-checkbox:checked');
    const listContainer = $('#selectedStudentsList');

    if (selected.length === 0) {
        listContainer.html('<p class="text-muted mb-0 text-center">No students selected</p>');
        return;
    }

    let html = '<div class="d-flex flex-wrap gap-2">';
    selected.each(function() {
        const name = $(this).closest('tr').find('td:eq(2) .fw-semibold').text();
        html += `<span class="badge bg-primary py-2 px-3"><i class="bi bi-person-fill me-1"></i>${escapeHtml(name)}</span>`;
    });
    html += `</div><p class="text-muted small mt-2 mb-0"><strong>${selected.length}</strong> student(s) selected</p>`;
    listContainer.html(html);
}

function submitAssignCourseBatch() {
    const school = $('#assignSchool').val();
    const courseId = $('#assignCourse').val();
    const batchId = $('#assignBatch').val();

    if (!school || !courseId || !batchId) { alert('Please fill in all required fields!'); return; }

    const selectedStudents = [];
    $('.account-checkbox:checked').each(function() {
        if ($(this).data('type') === 'student') selectedStudents.push($(this).val());
    });

    if (selectedStudents.length === 0) { alert('No students selected!'); return; }

    const submitBtn = $('button[onclick="submitAssignCourseBatch()"]');
    const originalHtml = submitBtn.html();
    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Assigning...');

    $.ajax({
        url: 'assign-course-batch.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ students: selectedStudents, school, courseId, batchId }),
        dataType: 'json',
        success: function(data) {
            submitBtn.prop('disabled', false).html(originalHtml);
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('assignCourseBatchModal')).hide();
                showToast('success', 'Assignment Successful', `${data.assigned} student(s) assigned successfully.`);
                accountsTable.ajax.reload();
                $('.account-checkbox').prop('checked', false);
                $('#selectAll').prop('checked', false);
                updateBulkActions();
            } else {
                showToast('error', 'Assignment Failed', data.message);
            }
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).html(originalHtml);
            showToast('error', 'Assignment Failed', 'An error occurred.');
        }
    });
}

function updateBulkActions() {
    const count = $('.account-checkbox:checked').length;
    if (count > 0) { $('#bulkActionsBar').fadeIn(); $('#selectedCount').text(count); }
    else { $('#bulkActionsBar').fadeOut(); }
}

function showToast(type, title, message) {
    const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
    const icon = type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill';
    const toast = `
        <div class="toast align-items-center text-white ${bgColor} border-0" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-${icon} me-2"></i><strong>${title}:</strong> ${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;
    $('body').append(toast);
    const el = $('.toast').last()[0];
    const bsToast = new bootstrap.Toast(el, { delay: 3000 });
    bsToast.show();
    $(el).on('hidden.bs.toast', function() { $(this).remove(); });
}

function getStatusBadge(status) {
    const badges = {
        'Active': '<span class="badge bg-success">Active</span>',
        'Approved': '<span class="badge bg-success">Approved</span>',
        'Pending': '<span class="badge bg-warning">Pending</span>',
        'Suspended': '<span class="badge bg-danger">Suspended</span>',
        'Rejected': '<span class="badge bg-danger">Rejected</span>',
        'Inactive': '<span class="badge bg-secondary">Inactive</span>'
    };
    return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
}

function formatDateTime(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function exportAccounts() {
    alert('Export functionality coming soon!');
}
</script>

<?php include('../footer/footer.php'); ?>