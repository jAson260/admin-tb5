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
                <div class="row g-3 align-items-center">
                    <!-- Role Filter -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Account Type</label>
                        <select class="form-select" id="roleFilter">
                            <option value="">All Accounts</option>
                            <option value="admin">Admins Only</option>
                            <option value="student">Students Only</option>
                        </select>
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="col-md-3">
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
                    
                    <!-- Reset Button -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">&nbsp;</label>
                        <button class="btn btn-outline-secondary w-100" id="resetFilters">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Filters
                        </button>
                    </div>

                    <!-- Add Account Button -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">&nbsp;</label>
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
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-table me-2"></i>Account List
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table class="table table-hover align-middle" id="accountsTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Account Modal -->
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

<!-- Reject Account Modal -->
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
                              placeholder="Please provide a reason for rejection (e.g., Incomplete documents, Invalid credentials, etc.)" 
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

<!-- View Account Details Modal -->
<div class="modal fade" id="viewAccountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-person-circle me-2"></i>Account Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="accountDetailsBody">
                <!-- Account details will be loaded here -->
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

<script>
let accountsTable;
let currentApproveId = null;
let currentApproveFullName = null;
let currentRejectId = null;
let currentRejectFullName = null;

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
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable Error:', error, thrown, xhr.responseText);
                alert('Error loading data. Please check console for details.');
            }
        },
        columns: [
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
                render: function(data) {
                    return escapeHtml(data);
                }
            },
            { 
                data: 'AccountType',
                render: function(data) {
                    const isAdmin = data === 'admin';
                    const badgeClass = isAdmin ? 'bg-danger' : 'bg-info';
                    const label = isAdmin ? 'Admin' : 'Student';
                    return `<span class="badge ${badgeClass}">${label}</span>`;
                }
            },
            { 
                data: 'Status',
                render: function(data) {
                    return getStatusBadge(data);
                }
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
                    
                    let buttons = '<div class="btn-group btn-group-sm" role="group">';
                    
                    // Show Approve button for Pending or Rejected student accounts
                    if (isStudent && (isPending || isRejected)) {
                        buttons += `
                            <button class="btn btn-outline-success" title="Approve Account" 
                                onclick="showApproveModal(${row.Id}, '${escapeHtml(row.FullName)}')">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        `;
                    }
                    
                    // Show Reject button for Pending student accounts
                    if (isStudent && isPending) {
                        buttons += `
                            <button class="btn btn-outline-danger" title="Reject Account" 
                                onclick="showRejectModal(${row.Id}, '${escapeHtml(row.FullName)}')">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        `;
                    }
                    
                    // View Details button (always visible)
                    buttons += `
                        <button class="btn btn-outline-info" title="View Details" 
                            onclick="viewAccount(${row.Id}, '${row.AccountType}')">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-warning" title="Change Password" 
                            onclick="changePassword(${row.Id}, '${row.AccountType}', '${escapeHtml(row.FullName)}')">
                            <i class="bi bi-key"></i>
                        </button>
                        <button class="btn btn-outline-danger" title="Delete Account" 
                            onclick="deleteAccount(${row.Id}, '${row.AccountType}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>`;
                    
                    return buttons;
                }
            }
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'desc']],
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search accounts...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ accounts",
            infoEmpty: "No accounts found",
            infoFiltered: "(filtered from _MAX_ total accounts)",
            zeroRecords: "No matching accounts found",
            emptyTable: "No accounts available",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        drawCallback: function(settings) {
            updateStatistics();
        }
    });

    // Filter change events
    $('#roleFilter, #statusFilter').on('change', function() {
        accountsTable.ajax.reload();
    });

    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#roleFilter').val('');
        $('#statusFilter').val('');
        accountsTable.search('').columns().search('').draw();
    });
    
    // Approve button click handler
    $('#confirmApproveBtn').on('click', function() {
        if (currentApproveId) {
            approveAccount(currentApproveId, currentApproveFullName);
        }
    });
    
    // Reject button click handler
    $('#confirmRejectBtn').on('click', function() {
        const reason = $('#rejectionReason').val().trim();
        if (!reason) {
            alert('Please provide a rejection reason!');
            return;
        }
        if (currentRejectId) {
            rejectAccount(currentRejectId, currentRejectFullName, reason);
        }
    });
    
    // Clear rejection reason when modal closes
    $('#rejectModal').on('hidden.bs.modal', function() {
        $('#rejectionReason').val('');
    });
    
    // Initial statistics load
    updateStatistics();
});

// Update statistics
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
        error: function(xhr, status, error) {
            console.error('Statistics Error:', error);
        }
    });
}

// Show approve modal
function showApproveModal(id, fullName) {
    currentApproveId = id;
    currentApproveFullName = fullName;
    $('#approveAccountName').text(fullName);
    const modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

// Show reject modal
function showRejectModal(id, fullName) {
    currentRejectId = id;
    currentRejectFullName = fullName;
    $('#rejectAccountName').text(fullName);
    $('#rejectionReason').val('');
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

// Approve account
function approveAccount(id, fullName) {
    $.ajax({
        url: 'approve-account.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id: id }),
        dataType: 'json',
        success: function(data) {
            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('approveModal'));
            modal.hide();
            
            if (data.success) {
                accountsTable.ajax.reload();
                updateStatistics();
            } else {
                alert('Failed to approve account: ' + data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText, error);
            alert('Failed to approve account. Check console for details.');
        }
    });
}

// Reject account
function rejectAccount(id, fullName, reason) {
    $.ajax({
        url: 'reject-account.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id: id, reason: reason }),
        dataType: 'json',
        success: function(data) {
            // Hide modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
            modal.hide();
            
            if (data.success) {
                accountsTable.ajax.reload();
                updateStatistics();
            } else {
                alert('Failed to reject account: ' + data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText, error);
            alert('Failed to reject account. Check console for details.');
        }
    });
}

// View account details
function viewAccount(id, type) {
    console.log('ViewAccount called - ID:', id, 'Type:', type);
    
    $.ajax({
        url: 'get-account-details.php',
        method: 'GET',
        data: { id: id, type: type },
        dataType: 'json',
        success: function(data) {
            console.log('Server response:', data);
            
            if (data.success) {
                const account = data.account;
                const isAdmin = type === 'admin';
                
                $('#accountDetailsBody').html(`
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Account ID</label>
                            <p class="fw-semibold">#${type.charAt(0).toUpperCase()}${account.Id}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Account Type</label>
                            <p><span class="badge ${isAdmin ? 'bg-danger' : 'bg-info'}">${isAdmin ? 'Admin' : 'Student'}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Full Name</label>
                            <p class="fw-semibold">${escapeHtml(account.FullName)}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Email</label>
                            <p class="fw-semibold">${escapeHtml(account.Email)}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">${isAdmin ? 'Role' : 'Status'}</label>
                            <p class="fw-semibold">${isAdmin ? account.Role : account.Status}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Last Login</label>
                            <p class="fw-semibold">${account.LastLogin ? formatDateTime(account.LastLogin) : 'Never'}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Account Created</label>
                            <p class="fw-semibold">${formatDateTime(account.CreatedAt)}</p>
                        </div>
                        ${!isAdmin && account.ULI ? `
                        <div class="col-md-6">
                            <label class="text-muted small">ULI Number</label>
                            <p class="fw-semibold">${account.ULI}</p>
                        </div>
                        ` : ''}
                    </div>
                `);
                
                const modal = new bootstrap.Modal(document.getElementById('viewAccountModal'));
                modal.show();
            } else {
                console.error('Server error:', data.message);
                alert('Failed to load account details: ' + data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            alert('Failed to load account details. Error: ' + xhr.status + ' - Check browser console.');
        }
    });
}

// Change password
function changePassword(id, type, fullName) {
    $('#changePasswordId').val(id);
    $('#changePasswordType').val(type);
    $('#changePasswordUser').text(fullName);
    $('#changePasswordForm')[0].reset();
    
    const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
    modal.show();
}

// Submit change password
function submitChangePassword() {
    const id = $('#changePasswordId').val();
    const type = $('#changePasswordType').val();
    const newPassword = $('#newPassword').val();
    const confirmPassword = $('#confirmNewPassword').val();
    
    if (newPassword !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    if (newPassword.length < 8) {
        alert('Password must be at least 8 characters long!');
        return;
    }
    
    $.ajax({
        url: 'change-password.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id: id, type: type, password: newPassword }),
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                alert('Password changed successfully!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                modal.hide();
                $('#changePasswordForm')[0].reset();
            } else {
                alert('Failed to change password: ' + data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText, error);
            alert('Failed to change password. Check console for details.');
        }
    });
}

// Delete account
function deleteAccount(id, type) {
    if (confirm('Are you sure you want to delete this account? This action cannot be undone!')) {
        $.ajax({
            url: 'delete-account.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: id, type: type }),
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    alert('Account deleted successfully!');
                    accountsTable.ajax.reload();
                    updateStatistics();
                } else {
                    alert('Failed to delete account: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText, error);
                alert('Failed to delete account. Check console for details.');
            }
        });
    }
}

// Export accounts
function exportAccounts() {
    window.location.href = 'export-accounts.php';
}

// Helper functions
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
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php
    // Include footer
    include('../footer/footer.php');
