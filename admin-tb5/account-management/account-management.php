<?php
session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();

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
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-people-fill text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Total Users</div>
                                <h3 class="mb-0 fw-bold" id="statTotal">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-shield-check text-danger fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Admins</div>
                                <h3 class="mb-0 fw-bold" id="statAdmins">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-person-check text-info fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Students</div>
                                <h3 class="mb-0 fw-bold" id="statStudents">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Active</div>
                                <h3 class="mb-0 fw-bold" id="statActive">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small mb-1">
                            <i class="bi bi-search me-1"></i>Search
                        </label>
                        <input type="text" class="form-control" id="searchAccounts"
                            placeholder="Search by name, email, or ID...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small mb-1">Account Type</label>
                        <select class="form-select" id="roleFilter">
                            <option value="">All Accounts</option>
                            <option value="admin">Admins Only</option>
                            <option value="student">Students Only</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small mb-1">Status</label>
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
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" onclick="showAddAccountModal()">
                            <i class="bi bi-plus-circle me-1"></i>Add Account
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accounts Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-table me-2 text-primary"></i>Account List
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Show</label>
                        <select class="form-select form-select-sm" id="entriesLength"
                            style="width:75px;" onchange="accountsTable.page.len(this.value).draw()">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="small text-muted mb-0">entries</label>
                        <button class="btn btn-success btn-sm ms-2" onclick="showAssignCourseBatchModal()">
                            <i class="bi bi-clipboard-check me-1"></i>Assign Course & Batch
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="accountsTable" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-center" style="width:40px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>School / Course / Batch</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th class="text-center" style="width:200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small" id="dtInfo">Showing 0 to 0 of 0 accounts</div>
                    <nav><ul class="pagination pagination-sm mb-0" id="dtPagination"></ul></nav>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActionsBar" class="card border-0 shadow-sm mt-3" style="display:none;">
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="fw-semibold">
                            <i class="bi bi-check-square me-2"></i>
                            <span id="selectedCount">0</span> account(s) selected
                        </span>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-sm btn-outline-secondary"
                            onclick="$('.account-checkbox').prop('checked', false); updateBulkActions();">
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
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
            <div class="modal-footer border-0 justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
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
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-check-circle-fill me-2"></i>Approve Account
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-person-check-fill text-success" style="font-size:3rem;"></i>
                <p class="mt-3 fw-semibold mb-1">Are you sure?</p>
                <p class="text-muted small mb-0">
                    Approving <strong class="text-success" id="approveAccountName"></strong>
                    will grant them access to the system.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success px-4" id="confirmApproveBtn">
                    <i class="bi bi-check-circle me-1"></i>Approve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== REJECT ACCOUNT MODAL ==================== -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-x-circle-fill me-2"></i>Reject Account
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <i class="bi bi-person-x-fill text-danger" style="font-size:3rem;"></i>
                <p class="mt-3 fw-semibold mb-1">Reject this account?</p>
                <p class="text-muted small mb-3">
                    You are about to reject <strong class="text-danger" id="rejectAccountName"></strong>
                </p>
                <div class="text-start">
                    <label for="rejectionReason" class="form-label fw-semibold small">
                        <i class="bi bi-pencil-square me-1"></i>Rejection Reason <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="rejectionReason" rows="3"
                        placeholder="Please provide a reason for rejection..." required></textarea>
                    <div class="form-text">This message will be sent to the user.</div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" id="confirmRejectBtn">
                    <i class="bi bi-x-circle me-1"></i>Reject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== VIEW ACCOUNT MODAL ==================== -->
<div class="modal fade" id="viewAccountModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-person-circle me-2"></i>Account Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="accountDetailsBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 text-muted small">Loading...</p>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-end">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== CHANGE PASSWORD MODAL ==================== -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-key me-2"></i>Change Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Changing password for: <strong id="changePasswordUser"></strong>
                </div>
                <form id="changePasswordForm">
                    <input type="hidden" id="changePasswordId">
                    <input type="hidden" id="changePasswordType">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="newPassword"
                            placeholder="Enter new password" required>
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirmNewPassword"
                            placeholder="Confirm new password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary px-4" onclick="submitChangePassword()">
                    <i class="bi bi-check-circle me-1"></i>Change Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== DELETE ACCOUNT MODAL ==================== -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-trash me-2"></i>Delete Account
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle text-danger" style="font-size:3rem;"></i>
                <p class="mt-3 fw-semibold mb-1">Are you sure?</p>
                <p class="text-muted small mb-0">
                    Deleting <strong class="text-danger" id="deleteAccountName"></strong>
                    cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== UNASSIGN COURSE MODAL ==================== -->
<div class="modal fade" id="unassignModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-slash-circle me-2"></i>Unassign Course
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size:3rem;"></i>
                <p class="mt-3 fw-semibold mb-1">Are you sure?</p>
                <p class="text-muted small mb-0">
                    Unassigning <strong class="text-warning" id="unassignStudentName"></strong>
                    will remove them from their current course and batch.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-warning px-4" id="confirmUnassignBtn">
                    <i class="bi bi-slash-circle me-1"></i>Unassign
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== ASSIGN COURSE AND BATCH MODAL ==================== -->
<div class="modal fade" id="assignCourseBatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-clipboard-check me-2"></i>Assign Course and Batch
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    Select students from the table and assign them to a course and batch.
                </div>
                <form id="assignCourseBatchForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">School <span class="text-danger">*</span></label>
                            <select class="form-select" id="assignSchool" required>
                                <option value="">Select School</option>
                                <option value="TB5">TB5</option>
                                <option value="BBI">BBI</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Course <span class="text-danger">*</span></label>
                            <select class="form-select" id="assignCourse" required disabled>
                                <option value="">Select School First</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Batch <span class="text-danger">*</span></label>
                            <select class="form-select" id="assignBatch" required disabled>
                                <option value="">Select Course First</option>
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <label class="form-label fw-semibold">Selected Students</label>
                            <div id="selectedStudentsList" class="border rounded p-3 bg-light"
                                style="max-height:200px;overflow-y:auto;">
                                <p class="text-muted mb-0 text-center small">No students selected</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success px-4" onclick="submitAssignCourseBatch()">
                    <i class="bi bi-check-circle me-1"></i>Assign to Students
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== TOAST ==================== -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100;">
    <div id="toastMsg" class="toast align-items-center text-white border-0 bg-success" role="alert">
        <div class="d-flex">
            <div class="toast-body fw-semibold" id="toastText"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<style>
.table > :not(caption) > * > th {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6c757d;
}
.dataTables_length,
.dataTables_filter,
.dataTables_info,
.dataTables_paginate { display: none !important; }
</style>

<script>
let accountsTable;
let currentApproveId       = null;
let currentApproveFullName = null;
let currentRejectId        = null;
let currentRejectFullName  = null;
let currentDeleteId        = null;
let currentDeleteType      = null;
let currentDeleteFullName  = null;
let currentUnassignId      = null;
let currentUnassignName    = null;

$(document).ready(function () {

    accountsTable = $('#accountsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get-accounts.php',
            type: 'POST',
            data: function (d) {
                d.roleFilter    = $('#roleFilter').val();
                d.statusFilter  = $('#statusFilter').val();
                d.searchKeyword = $('#searchAccounts').val();
            },
            error: function (xhr, error, thrown) {
                console.error('DataTable Error:', error, thrown, xhr.responseText);
            }
        },
        columns: [
            {
                data: null, orderable: false, searchable: false,
                className: 'text-center ps-3',
                render: function (data, type, row) {
                    return `<input type="checkbox" class="form-check-input account-checkbox"
                        value="${row.Id}" data-type="${row.AccountType}">`;
                }
            },
            {
                data: 'Id',
                render: function (data, type, row) {
                    const prefix = row.AccountType === 'admin' ? 'A' : 'S';
                    return `<span class="badge bg-dark font-monospace px-2 py-1"
                        style="font-size:.8rem;">#${prefix}${data}</span>`;
                }
            },
            {
                data: 'FullName',
                render: function (data, type, row) {
                    const isAdmin   = row.AccountType === 'admin';
                    const iconClass = isAdmin ? 'bi-shield-fill-check' : 'bi-person-fill';
                    const iconColor = isAdmin ? 'danger' : 'primary';
                    const subtitle  = isAdmin ? row.Role : 'Student';
                    return `
                        <div class="d-flex align-items-center">
                            <div class="bg-${iconColor} bg-opacity-10 rounded-circle p-2 me-2">
                                <i class="bi ${iconClass} text-${iconColor}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold small">${escapeHtml(data)}</div>
                                <small class="text-muted">${escapeHtml(subtitle)}</small>
                            </div>
                        </div>`;
                }
            },
            {
                data: 'Email',
                render: function (data) {
                    return `<span class="small">${escapeHtml(data)}</span>`;
                }
            },
            {
                data: 'AccountType',
                render: function (data) {
                    const isAdmin = data === 'admin';
                    return `<span class="badge rounded-pill ${isAdmin ? 'bg-danger' : 'bg-info'}">
                        ${isAdmin ? 'Admin' : 'Student'}</span>`;
                }
            },
            // ── NEW: School / Course / Batch column ──────────────────────────
            {
                data: null, orderable: false,
                render: function (data, type, row) {
                    if (row.AccountType === 'admin') {
                        return '<span class="text-muted small">—</span>';
                    }

                    const hasAssignment = row.School || row.CourseCode || row.BatchCode;
                    if (!hasAssignment) {
                        return `<span class="badge bg-secondary bg-opacity-75 small">
                            <i class="bi bi-slash-circle me-1"></i>Unassigned</span>`;
                    }

                    const schoolBadge = row.School === 'TB5'
                        ? `<img src="../assets/img/tb5-logo.png"
                                style="width:16px;height:16px;object-fit:cover;border-radius:50%;
                                       vertical-align:middle;"> TB5`
                        : `<img src="../assets/img/bbi-logo.png"
                                style="width:16px;height:16px;object-fit:cover;border-radius:50%;
                                       vertical-align:middle;"> BBI`;

                    return `
                        <div class="d-flex flex-column gap-1">
                            <span class="small fw-semibold">${schoolBadge}</span>
                            <span class="badge bg-primary bg-opacity-75 text-truncate"
                                style="max-width:160px;" title="${escapeHtml(row.CourseName || '')}">
                                <i class="bi bi-book me-1"></i>${escapeHtml(row.CourseCode || 'N/A')}
                            </span>
                            <span class="badge bg-info text-dark text-truncate"
                                style="max-width:160px;" title="${escapeHtml(row.BatchName || '')}">
                                <i class="bi bi-collection me-1"></i>${escapeHtml(row.BatchCode || 'N/A')}
                            </span>
                        </div>`;
                }
            },
            {
                data: 'Status',
                render: function (data) { return getStatusBadge(data); }
            },
            {
                data: 'LastLogin',
                render: function (data) {
                    return data
                        ? `<span class="small">${formatDateTime(data)}</span>`
                        : '<span class="text-muted small">Never</span>';
                }
            },
            {
                data: null, orderable: false, className: 'text-center',
                render: function (data, type, row) {
                    const isPending   = row.Status === 'Pending';
                    const isRejected  = row.Status === 'Rejected';
                    const isStudent   = row.AccountType === 'student';
                    const isAssigned  = isStudent && (row.School || row.CourseCode || row.BatchCode);
                    const fullName    = escapeJs(row.FullName);

                    let buttons = '<div class="btn-group btn-group-sm">';

                    if (isStudent && (isPending || isRejected)) {
                        buttons += `<button class="btn btn-outline-success" title="Approve"
                            onclick="showApproveModal(${row.Id}, '${fullName}')">
                            <i class="bi bi-check-circle"></i></button>`;
                    }
                    if (isStudent && isPending) {
                        buttons += `<button class="btn btn-outline-danger" title="Reject"
                            onclick="showRejectModal(${row.Id}, '${fullName}')">
                            <i class="bi bi-x-circle"></i></button>`;
                    }

                    buttons += `
                        <button class="btn btn-outline-info" title="View"
                            onclick="viewAccount(${row.Id}, '${row.AccountType}')">
                            <i class="bi bi-eye"></i></button>
                        <button class="btn btn-outline-warning" title="Change Password"
                            onclick="changePassword(${row.Id}, '${row.AccountType}', '${fullName}')">
                            <i class="bi bi-key"></i></button>`;

                    if (isAssigned) {
                        buttons += `
                        <button class="btn btn-outline-secondary" title="Unassign Course"
                            onclick="showUnassignModal(${row.Id}, '${fullName}')">
                            <i class="bi bi-slash-circle"></i></button>`;
                    }

                    buttons += `
                        <button class="btn btn-outline-danger" title="Delete"
                            onclick="deleteAccount(${row.Id}, '${row.AccountType}', '${fullName}')">
                            <i class="bi bi-trash"></i></button>
                    </div>`;
                    return buttons;
                }
            }
        ],
        pageLength: 10,
        order: [[1, 'desc']],
        responsive: true,
        dom: 'rt',
        drawCallback: function () {
            updateStatistics();
            const api   = this.api();
            const info  = api.page.info();
            const total = info.recordsDisplay;
            $('#dtInfo').text(
                total === 0
                    ? 'Showing 0 to 0 of 0 accounts'
                    : `Showing ${info.start + 1} to ${info.end} of ${total} accounts`
            );
            buildPagination(api);
        }
    });

    // ── SEARCH ────────────────────────────────────────────────────────────────
    let searchTimeout;
    $('#searchAccounts').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () { accountsTable.ajax.reload(); }, 500);
    });

    $('#roleFilter, #statusFilter').on('change', function () {
        accountsTable.ajax.reload();
    });

    $('#resetFilters').on('click', function () {
        $('#searchAccounts').val('');
        $('#roleFilter').val('');
        $('#statusFilter').val('');
        accountsTable.ajax.reload();
    });

    // ── SELECT ALL ────────────────────────────────────────────────────────────
    $('#selectAll').on('change', function () {
        $('.account-checkbox').prop('checked', this.checked);
        updateBulkActions();
    });

    $(document).on('change', '.account-checkbox', function () {
        updateBulkActions();
        const total   = $('.account-checkbox').length;
        const checked = $('.account-checkbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
    });

    // ── CONFIRM APPROVE ───────────────────────────────────────────────────────
    $('#confirmApproveBtn').on('click', function () {
        if (currentApproveId) approveAccount(currentApproveId, currentApproveFullName);
    });

    // ── CONFIRM REJECT ────────────────────────────────────────────────────────
    $('#confirmRejectBtn').on('click', function () {
        const reason = $('#rejectionReason').val().trim();
        if (!reason) { showToast('Please provide a rejection reason.', 'danger'); return; }
        if (currentRejectId) rejectAccount(currentRejectId, currentRejectFullName, reason);
    });

    // ── CONFIRM DELETE ────────────────────────────────────────────────────────
    $('#confirmDeleteBtn').on('click', function () {
        if (currentDeleteId && currentDeleteType)
            performDeleteAccount(currentDeleteId, currentDeleteType, currentDeleteFullName);
    });

    // ── CONFIRM UNASSIGN ──────────────────────────────────────────────────────
    $('#confirmUnassignBtn').on('click', function () {
        if (currentUnassignId) performUnassign(currentUnassignId, currentUnassignName);
    });

    $('#rejectModal').on('hidden.bs.modal', function () { $('#rejectionReason').val(''); });

    // ── ASSIGN SCHOOL → COURSES ───────────────────────────────────────────────
    $('#assignSchool').on('change', function () {
        const school         = $(this).val();
        const courseDropdown = $('#assignCourse');
        const batchDropdown  = $('#assignBatch');
        batchDropdown.prop('disabled', true).html('<option value="">Select Course First</option>');
        if (!school) {
            courseDropdown.prop('disabled', true).html('<option value="">Select School First</option>');
            return;
        }
        courseDropdown.prop('disabled', true).html('<option value="">Loading courses...</option>');
        $.ajax({
            url: '../create-batch/get-courses-by-school.php',
            method: 'GET', data: { school }, dataType: 'json',
            success: function (data) {
                if (data.success && data.courses && data.courses.length > 0) {
                    let opts = '<option value="">Select Course</option>';
                    data.courses.forEach(c => {
                        opts += `<option value="${c.Id}">${escapeHtml(c.CourseCode)} - ${escapeHtml(c.CourseName)}</option>`;
                    });
                    courseDropdown.prop('disabled', false).html(opts);
                } else {
                    courseDropdown.prop('disabled', true)
                        .html(`<option value="">${data.message || 'No courses available'}</option>`);
                }
            },
            error: function () {
                courseDropdown.prop('disabled', true)
                    .html('<option value="">Error loading courses</option>');
            }
        });
    });

    // ── ASSIGN COURSE → BATCHES ───────────────────────────────────────────────
    $('#assignCourse').on('change', function () {
        const courseId      = $(this).val();
        const batchDropdown = $('#assignBatch');
        if (!courseId) {
            batchDropdown.prop('disabled', true).html('<option value="">Select Course First</option>');
            return;
        }
        batchDropdown.prop('disabled', true).html('<option value="">Loading batches...</option>');
        $.ajax({
            url: 'get-batches-by-course.php',
            method: 'GET', data: { courseId }, dataType: 'json',
            success: function (data) {
                if (data.success && data.batches && data.batches.length > 0) {
                    let opts = '<option value="">Select Batch</option>';
                    data.batches.forEach(b => {
                        const slots  = b.MaxStudents
                            ? ` (${b.CurrentStudents || 0}/${b.MaxStudents} slots)` : '';
                        const status = b.Status !== 'Active' ? ` [${b.Status}]` : '';
                        opts += `<option value="${b.Id}">
                            ${escapeHtml(b.BatchCode)} - ${escapeHtml(b.BatchName)}${slots}${status}
                        </option>`;
                    });
                    batchDropdown.prop('disabled', false).html(opts);
                } else {
                    batchDropdown.prop('disabled', true)
                        .html(`<option value="">${data.message || 'No batches available'}</option>`);
                }
            },
            error: function () {
                batchDropdown.prop('disabled', true)
                    .html('<option value="">Error loading batches</option>');
            }
        });
    });

    updateStatistics();
});

// ─── PAGINATION ───────────────────────────────────────────────────────────────
function buildPagination(api) {
    const info  = api.page.info();
    const pages = info.pages;
    const cur   = info.page;
    const ul    = document.getElementById('dtPagination');
    ul.innerHTML = '';
    if (pages <= 1) return;

    const prev = document.createElement('li');
    prev.className = `page-item ${cur === 0 ? 'disabled' : ''}`;
    prev.innerHTML = `<a class="page-link" href="#"
        onclick="accountsTable.page('previous').draw('page'); return false;">
        <i class="bi bi-chevron-left"></i></a>`;
    ul.appendChild(prev);

    const range = 2;
    for (let p = 0; p < pages; p++) {
        if (p === 0 || p === pages - 1 ||
            (p >= cur - range && p <= cur + range)) {
            const li = document.createElement('li');
            li.className = `page-item ${p === cur ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#"
                onclick="accountsTable.page(${p}).draw('page'); return false;">${p + 1}</a>`;
            ul.appendChild(li);
        } else if (p === cur - range - 1 || p === cur + range + 1) {
            const li = document.createElement('li');
            li.className = 'page-item disabled';
            li.innerHTML = `<span class="page-link">…</span>`;
            ul.appendChild(li);
        }
    }

    const next = document.createElement('li');
    next.className = `page-item ${cur === pages - 1 ? 'disabled' : ''}`;
    next.innerHTML = `<a class="page-link" href="#"
        onclick="accountsTable.page('next').draw('page'); return false;">
        <i class="bi bi-chevron-right"></i></a>`;
    ul.appendChild(next);
}

// ─── ADD ACCOUNT ──────────────────────────────────────────────────────────────
function showAddAccountModal() {
    $('#addAccountForm')[0].reset();
    new bootstrap.Modal(document.getElementById('addAccountModal')).show();
}

function toggleAddPassword() {
    const input = $('#addPassword');
    const icon  = $('#addPasswordEyeIcon');
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
        showToast('Please fill in all required fields.', 'danger'); return;
    }
    if (password.length < 8) {
        showToast('Password must be at least 8 characters long.', 'danger'); return;
    }

    const $btn     = $('#submitAddAccountBtn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Creating...');

    $.ajax({
        url: 'add-account.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ firstName, lastName, username, email, password, role, status }),
        dataType: 'json',
        success: function (response) {
            $btn.prop('disabled', false).html(origHtml);
            if (response.success) {
                bootstrap.Modal.getInstance(document.getElementById('addAccountModal')).hide();
                $('#addAccountForm')[0].reset();
                accountsTable.ajax.reload();
                updateStatistics();
                showToast(`${firstName} ${lastName}'s admin account created successfully.`, 'success');
            } else {
                showToast(response.message || 'Failed to create account.', 'danger');
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html(origHtml);
            console.error('Error:', xhr.responseText);
            showToast('An error occurred. Check console for details.', 'danger');
        }
    });
}

// ─── STATISTICS ───────────────────────────────────────────────────────────────
function updateStatistics() {
    $.ajax({
        url: 'get-statistics.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data.success) {
                $('#statTotal').text(data.statistics.total);
                $('#statAdmins').text(data.statistics.admins);
                $('#statStudents').text(data.statistics.students);
                $('#statActive').text(data.statistics.active);
            }
        },
        error: function (xhr) { console.error('Statistics Error:', xhr.responseText); }
    });
}

// ─── APPROVE ──────────────────────────────────────────────────────────────────
function showApproveModal(id, fullName) {
    currentApproveId       = id;
    currentApproveFullName = fullName;
    $('#approveAccountName').text(fullName);
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function approveAccount(id, fullName) {
    const $btn     = $('#confirmApproveBtn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Approving...');

    $.ajax({
        url: 'approve-account.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id }),
        dataType: 'json',
        success: function (data) {
            $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Approve');
            bootstrap.Modal.getInstance(document.getElementById('approveModal')).hide();
            if (data.success) {
                accountsTable.ajax.reload();
                updateStatistics();
                showToast(`${fullName}'s account has been approved.`, 'success');
            } else {
                showToast('Failed to approve: ' + data.message, 'danger');
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Approve');
            console.error('Error:', xhr.responseText);
            showToast('An error occurred. Please try again.', 'danger');
        }
    });
}

// ─── REJECT ───────────────────────────────────────────────────────────────────
function showRejectModal(id, fullName) {
    currentRejectId       = id;
    currentRejectFullName = fullName;
    $('#rejectAccountName').text(fullName);
    $('#rejectionReason').val('');
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function rejectAccount(id, fullName, reason) {
    const $btn     = $('#confirmRejectBtn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Rejecting...');

    $.ajax({
        url: 'reject-account.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id, reason }),
        dataType: 'json',
        success: function (data) {
            $btn.prop('disabled', false).html('<i class="bi bi-x-circle me-1"></i>Reject');
            bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
            if (data.success) {
                accountsTable.ajax.reload();
                updateStatistics();
                showToast(`${fullName}'s account has been rejected.`, 'warning');
            } else {
                showToast('Failed to reject: ' + data.message, 'danger');
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html('<i class="bi bi-x-circle me-1"></i>Reject');
            console.error('Error:', xhr.responseText);
            showToast('An error occurred. Please try again.', 'danger');
        }
    });
}

// ─── VIEW ACCOUNT ─────────────────────────────────────────────────────────────
function viewAccount(id, type) {
    $('#accountDetailsBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2 text-muted small">Loading...</p>
        </div>`);

    new bootstrap.Modal(document.getElementById('viewAccountModal')).show();

    $.ajax({
        url: 'get-account-details.php',
        method: 'GET',
        data: { id, type },
        dataType: 'json',
        success: function (data) {
            if (!data.success) {
                $('#accountDetailsBody').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>${escapeHtml(data.message)}
                    </div>`);
                return;
            }

            const a       = data.account;
            const isAdmin = type === 'admin';
            let html      = '';

            if (isAdmin) {
                html = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Account ID</div>
                                <div class="fw-bold font-monospace">#A${a.Id}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Account Type</div>
                                <span class="badge bg-danger px-3 py-2">Admin</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Full Name</div>
                                <div class="fw-bold">${escapeHtml(a.FullName)}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Email</div>
                                <div class="fw-bold">${escapeHtml(a.Email)}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Role</div>
                                <span class="badge bg-primary px-3 py-2">${escapeHtml(a.Role)}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Status</div>
                                ${getStatusBadge(a.Status)}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Last Login</div>
                                <div class="fw-bold small">${a.LastLogin ? formatDateTime(a.LastLogin) : 'Never'}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded-3 border">
                                <div class="text-muted small mb-1">Account Created</div>
                                <div class="fw-bold small">${formatDateTime(a.CreatedAt)}</div>
                            </div>
                        </div>
                    </div>`;
            } else {
                const fullAddress = [a.Street, a.BarangayName, a.CityName, a.ProvinceName, a.RegionName]
                    .filter(Boolean).join(', ');

                // ── Enrollment info for student ───────────────────────────────
                const hasEnrollment = a.School || a.CourseCode || a.BatchCode;
                const enrollmentHtml = hasEnrollment ? `
                    <div class="col-12 mt-2">
                        <h6 class="fw-bold text-success border-bottom pb-2">
                            <i class="bi bi-clipboard-check me-2"></i>Enrollment Information
                        </h6>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">School</div>
                            <div class="fw-bold">${escapeHtml(a.School || 'N/A')}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Course</div>
                            <div class="fw-bold small">${escapeHtml(a.CourseCode || 'N/A')}</div>
                            <small class="text-muted">${escapeHtml(a.CourseName || '')}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Batch</div>
                            <div class="fw-bold small">${escapeHtml(a.BatchCode || 'N/A')}</div>
                            <small class="text-muted">${escapeHtml(a.BatchName || '')}</small>
                        </div>
                    </div>` : `
                    <div class="col-12 mt-2">
                        <div class="alert alert-warning border-0 mb-0">
                            <i class="bi bi-slash-circle me-2"></i>
                            This student has not been assigned to any course or batch yet.
                        </div>
                    </div>`;

                html = `
                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="fw-bold text-primary border-bottom pb-2">
                                <i class="bi bi-person me-2"></i>Basic Information
                            </h6>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Student ID</div>
                                <div class="fw-bold font-monospace">#S${a.Id}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">ULI Number</div>
                                <div class="fw-bold">${escapeHtml(a.ULI || 'N/A')}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Status</div>
                                ${getStatusBadge(a.Status)}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Full Name</div>
                                <div class="fw-bold">
                                    ${escapeHtml(a.FirstName)} ${escapeHtml(a.MiddleName || '')} ${escapeHtml(a.LastName)}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Email</div>
                                <div class="fw-bold">${escapeHtml(a.Email)}</div>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-2">
                                <i class="bi bi-card-text me-2"></i>Personal Details
                            </h6>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Birth Date</div>
                                <div class="fw-bold small">${formatDate(a.BirthDate)}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Age</div>
                                <div class="fw-bold">${a.Age} yrs</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Sex</div>
                                <div class="fw-bold">${escapeHtml(a.Sex)}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Civil Status</div>
                                <div class="fw-bold">${escapeHtml(a.CivilStatus)}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Contact Number</div>
                                <div class="fw-bold">${escapeHtml(a.ContactNo)}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Complete Address</div>
                                <div class="fw-bold small">${escapeHtml(fullAddress || 'N/A')}</div>
                            </div>
                        </div>
                        ${enrollmentHtml}
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-primary border-bottom pb-2">
                                <i class="bi bi-gear me-2"></i>Account Information
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Last Login</div>
                                <div class="fw-bold small">${a.LastLogin ? formatDateTime(a.LastLogin) : 'Never'}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100">
                                <div class="text-muted small mb-1">Registration Date</div>
                                <div class="fw-bold small">${formatDateTime(a.CreatedAt)}</div>
                            </div>
                        </div>
                    </div>`;
            }

            $('#accountDetailsBody').html(html);
        },
        error: function () {
            $('#accountDetailsBody').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-wifi-off me-1"></i>Failed to load account details.
                </div>`);
        }
    });
}

// ─── CHANGE PASSWORD ──────────────────────────────────────────────────────────
function changePassword(id, type, fullName) {
    $('#changePasswordId').val(id);
    $('#changePasswordType').val(type);
    $('#changePasswordUser').text(fullName);
    $('#changePasswordForm')[0].reset();
    new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
}

function submitChangePassword() {
    const id              = $('#changePasswordId').val();
    const type            = $('#changePasswordType').val();
    const newPassword     = $('#newPassword').val();
    const confirmPassword = $('#confirmNewPassword').val();

    if (newPassword !== confirmPassword) {
        showToast('Passwords do not match.', 'danger'); return;
    }
    if (newPassword.length < 8) {
        showToast('Password must be at least 8 characters.', 'danger'); return;
    }

    const $btn     = $('#changePasswordModal .btn-primary');
    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

    $.ajax({
        url: 'change-password.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id, type, password: newPassword }),
        dataType: 'json',
        success: function (data) {
            $btn.prop('disabled', false).html(origHtml);
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
                $('#changePasswordForm')[0].reset();
                showToast('Password changed successfully.', 'success');
            } else {
                showToast('Failed: ' + data.message, 'danger');
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html(origHtml);
            console.error('Error:', xhr.responseText);
            showToast('An error occurred. Please try again.', 'danger');
        }
    });
}

// ─── DELETE ACCOUNT ───────────────────────────────────────────────────────────
function deleteAccount(id, type, fullName) {
    currentDeleteId       = id;
    currentDeleteType     = type;
    currentDeleteFullName = fullName;
    $('#deleteAccountName').text(fullName);
    new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
}

function performDeleteAccount(id, type, fullName) {
    const $btn     = $('#confirmDeleteBtn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Deleting...');

    $.ajax({
        url: 'delete-account.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id, type }),
        dataType: 'json',
        success: function (data) {
            $btn.prop('disabled', false).html('<i class="bi bi-trash me-1"></i>Delete');
            bootstrap.Modal.getInstance(document.getElementById('deleteAccountModal')).hide();
            currentDeleteId = null; currentDeleteType = null; currentDeleteFullName = null;
            if (data.success) {
                accountsTable.ajax.reload();
                updateStatistics();
                showToast(`${fullName}'s account has been deleted.`, 'success');
            } else {
                showToast('Delete failed: ' + data.message, 'danger');
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html('<i class="bi bi-trash me-1"></i>Delete');
            bootstrap.Modal.getInstance(document.getElementById('deleteAccountModal')).hide();
            console.error('Error:', xhr.responseText);
            showToast('An error occurred. Please try again.', 'danger');
        }
    });
}

// ─── UNASSIGN COURSE ──────────────────────────────────────────────────────────
function showUnassignModal(id, fullName) {
    currentUnassignId   = id;
    currentUnassignName = fullName;
    $('#unassignStudentName').text(fullName);
    new bootstrap.Modal(document.getElementById('unassignModal')).show();
}

function performUnassign(id, fullName) {
    const $btn     = $('#confirmUnassignBtn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Unassigning...');

    $.ajax({
        url: 'unassign-course.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id }),
        dataType: 'json',
        success: function (data) {
            $btn.prop('disabled', false).html('<i class="bi bi-slash-circle me-1"></i>Unassign');
            bootstrap.Modal.getInstance(document.getElementById('unassignModal')).hide();
            currentUnassignId = null; currentUnassignName = null;
            if (data.success) {
                accountsTable.ajax.reload();
                showToast(`${fullName} has been unassigned from their course and batch.`, 'warning');
            } else {
                showToast('Unassign failed: ' + data.message, 'danger');
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html('<i class="bi bi-slash-circle me-1"></i>Unassign');
            bootstrap.Modal.getInstance(document.getElementById('unassignModal')).hide();
            console.error('Error:', xhr.responseText);
            showToast('An error occurred. Please try again.', 'danger');
        }
    });
}

// ─── ASSIGN COURSE & BATCH ────────────────────────────────────────────────────
function showAssignCourseBatchModal() {
    const selected = $('.account-checkbox:checked');
    if (selected.length === 0) {
        showToast('Please select at least one student.', 'danger'); return;
    }

    let hasNonStudent = false;
    selected.each(function () {
        if ($(this).data('type') === 'admin') { hasNonStudent = true; return false; }
    });
    if (hasNonStudent) {
        showToast('You can only assign courses to student accounts.', 'danger'); return;
    }

    updateSelectedStudentsList();
    $('#assignCourseBatchForm')[0].reset();
    $('#assignCourse').prop('disabled', true).html('<option value="">Select School First</option>');
    $('#assignBatch').prop('disabled', true).html('<option value="">Select Course First</option>');
    new bootstrap.Modal(document.getElementById('assignCourseBatchModal')).show();
}

function updateSelectedStudentsList() {
    const selected      = $('.account-checkbox:checked');
    const listContainer = $('#selectedStudentsList');

    if (selected.length === 0) {
        listContainer.html('<p class="text-muted mb-0 text-center small">No students selected</p>');
        return;
    }

    let html = '<div class="d-flex flex-wrap gap-2">';
    selected.each(function () {
        const name = $(this).closest('tr').find('td:eq(2) .fw-semibold').text();
        html += `<span class="badge bg-primary py-2 px-3">
            <i class="bi bi-person-fill me-1"></i>${escapeHtml(name)}</span>`;
    });
    html += `</div>
        <p class="text-muted small mt-2 mb-0">
            <strong>${selected.length}</strong> student(s) selected
        </p>`;
    listContainer.html(html);
}

function submitAssignCourseBatch() {
    const school   = $('#assignSchool').val();
    const courseId = $('#assignCourse').val();
    const batchId  = $('#assignBatch').val();

    if (!school || !courseId || !batchId) {
        showToast('Please fill in all required fields.', 'danger'); return;
    }

    const selectedStudents = [];
    $('.account-checkbox:checked').each(function () {
        if ($(this).data('type') === 'student') selectedStudents.push($(this).val());
    });

    if (selectedStudents.length === 0) {
        showToast('No students selected.', 'danger'); return;
    }

    const $btn     = $('#assignCourseBatchModal .btn-success');
    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Assigning...');

    $.ajax({
        url: 'assign-course-batch.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ students: selectedStudents, school, courseId, batchId }),
        dataType: 'json',
        success: function (data) {
            $btn.prop('disabled', false).html(origHtml);
            if (data.success) {
                bootstrap.Modal.getInstance(
                    document.getElementById('assignCourseBatchModal')).hide();
                accountsTable.ajax.reload();
                $('.account-checkbox').prop('checked', false);
                $('#selectAll').prop('checked', false);
                updateBulkActions();
                showToast(`${data.assigned} student(s) assigned successfully.`, 'success');
            } else {
                showToast(data.message || 'Assignment failed.', 'danger');
            }
        },
        error: function () {
            $btn.prop('disabled', false).html(origHtml);
            showToast('An error occurred. Please try again.', 'danger');
        }
    });
}

// ─── BULK ACTIONS ─────────────────────────────────────────────────────────────
function updateBulkActions() {
    const count = $('.account-checkbox:checked').length;
    if (count > 0) { $('#bulkActionsBar').fadeIn(); $('#selectedCount').text(count); }
    else           { $('#bulkActionsBar').fadeOut(); }
}

// ─── EXPORT ───────────────────────────────────────────────────────────────────
function exportAccounts() {
    showToast('Export functionality coming soon!', 'info');
}

// ─── TOAST ────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const el = document.getElementById('toastMsg');
    el.className = `toast align-items-center text-white border-0 bg-${type}`;
    document.getElementById('toastText').textContent = msg;
    bootstrap.Toast.getOrCreateInstance(el, { delay: 3500 }).show();
}

// ─── HELPERS ──────────────────────────────────────────────────────────────────
function getStatusBadge(status) {
    const badges = {
        'Active':    '<span class="badge bg-success">Active</span>',
        'Approved':  '<span class="badge bg-success">Approved</span>',
        'Pending':   '<span class="badge bg-warning text-dark">Pending</span>',
        'Suspended': '<span class="badge bg-danger">Suspended</span>',
        'Rejected':  '<span class="badge bg-danger">Rejected</span>',
        'Inactive':  '<span class="badge bg-secondary">Inactive</span>'
    };
    return badges[status] || `<span class="badge bg-secondary">${escapeHtml(status)}</span>`;
}

function formatDateTime(dateStr) {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const d = document.createElement('div');
    d.textContent = String(text);
    return d.innerHTML;
}

function escapeJs(text) {
    if (!text) return '';
    return String(text).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
}
</script>

<?php include('../footer/footer.php'); ?>