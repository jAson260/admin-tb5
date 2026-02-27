<?php
session_start();
require_once('../../includes/rbac-guard.php');

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
                                <th>Course</th>
                                <th>School</th>
                                <th>Submission Date</th>
                                <th>File Size</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
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

<!-- Approve Document Modal -->
<div class="modal fade" id="approveDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-success bg-opacity-10">
                <h5 class="modal-title text-success fw-bold">
                    <i class="bi bi-check-circle-fill me-2"></i>Approve Document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold mb-2">Approve Document?</h5>
                <p class="text-muted mb-3">
                    You are about to approve <strong id="approveDocumentType" class="text-dark"></strong><br>
                    for <strong id="approveStudentName" class="text-dark"></strong>
                </p>
                <div class="alert alert-success mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    This document will be marked as <strong>verified and approved</strong>.
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success px-4" id="confirmApproveBtn">
                    <i class="bi bi-check-circle me-1"></i>Approve Document
                </button>
            </div>
        </div>
    </div>
</div>






<script>
let documentsTable;
let currentApproveId = null;
let currentApproveType = null;
let currentApproveStudent = null;
let currentRejectId = null;
let currentRejectType = null;

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

// Initialize DataTable on page load
$(document).ready(function() {
    console.log('Page loaded, jQuery version:', $.fn.jquery);
    console.log('DataTables available:', typeof $.fn.DataTable);
    
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables library not loaded!');
        alert('DataTables library failed to load. Check console.');
        return;
    }
    
    console.log('Initializing DataTables...');
    
    documentsTable = $('#documentsTable').DataTable({
        ajax: {
            url: 'get-documents.php',
            dataSrc: function(json) {
                console.log('AJAX Response:', json);
                
                if (!json) {
                    console.error('No response from server');
                    return [];
                }
                
                if (json.success) {
                    console.log('Documents count:', json.documents.length);
                    updateStatistics(json.statistics);
                    return json.documents;
                } else {
                    console.error('Error from server:', json.message);
                    alert('Error loading documents: ' + json.message);
                    return [];
                }
            },
            error: function(xhr, error, thrown) {
                console.error('AJAX Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    error: error,
                    thrown: thrown,
                    responseText: xhr.responseText
                });
                alert('Failed to load documents. Check console for details.');
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `<input type="checkbox" class="form-check-input row-checkbox" data-id="${row.id}">`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                <i class="bi bi-person-fill text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">${row.studentName}</div>
                                <small class="text-muted">ID: ${row.studentId}</small>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    const iconColor = getDocumentTypeColor(row.documentType);
                    return `<i class="bi bi-file-earmark-text ${iconColor} me-1"></i>${row.documentType}`;
                }
            },
            { 
                data: 'course',
                defaultContent: '-'
            },
            { 
                data: 'school',
                defaultContent: '-',
                render: function(data) {
                    return data ? data.toUpperCase() : '-';
                }
            },
            { data: 'submissionDate' },
            { data: 'fileSize' },
            {
                data: 'status',
                render: function(data) {
                    return getStatusBadge(data);
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return getActionButtons(row);
                }
            }
        ],
        order: [[5, 'desc']], // Sort by submission date
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search documents...",
            lengthMenu: "_MENU_ entries per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No documents available",
            infoFiltered: "(filtered from _MAX_ total entries)",
            zeroRecords: "No matching documents found",
            emptyTable: "No documents available in table"
        },
        drawCallback: function() {
            // Re-bind select all checkbox
            $('#selectAll').off('change').on('change', function() {
                $('.row-checkbox').prop('checked', this.checked);
            });
        }
    });
    
    console.log('DataTable initialized');
    
    // ✅ ADD THESE EVENT LISTENERS
    // Approve button click handler
    $('#confirmApproveBtn').on('click', function() {
        if (currentApproveId) {
            confirmApprove();
        }
    });
    
    // Reject button click handler - update the selector to match modal button
    $('#rejectDocumentModal .btn-danger').on('click', function() {
        confirmReject();
    });
    
    // Clear rejection reason when modal closes
    $('#rejectDocumentModal').on('hidden.bs.modal', function() {
        $('#rejectionReason').val('');
        currentRejectId = null;
        currentRejectType = null;
    });
    
    // Clear approve modal data when it closes
    $('#approveDocumentModal').on('hidden.bs.modal', function() {
        currentApproveId = null;
        currentApproveType = null;
        currentApproveStudent = null;
    });
});

// Update statistics cards
function updateStatistics(stats) {
    $('.col-md-3:nth-child(1) h3').text(stats.pending);
    $('.col-md-3:nth-child(2) h3').text(stats.approved);
    $('.col-md-3:nth-child(3) h3').text(stats.rejected);
    $('.col-md-3:nth-child(4) h3').text(stats.total);
    
    // Update pending badge in header
    $('.btn-warning .badge').text(stats.pending);
}

// Get status badge HTML
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'approved': '<span class="badge bg-success">Approved</span>',
        'rejected': '<span class="badge bg-danger">Rejected</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

// Get document type icon color
function getDocumentTypeColor(type) {
    const colors = {
        'PSA Birth Certificate': 'text-primary',
        'Transcript of Records': 'text-success',
        'Diploma': 'text-warning',
        'Marriage Certificate': 'text-info'
    };
    return colors[type] || 'text-secondary';
}

// Get action buttons based on status
function getActionButtons(doc) {
    const baseButtons = `
        <button class="btn btn-sm btn-outline-primary" title="View" onclick="viewDocument('${doc.documentPath}', '${doc.documentType}')">
            <i class="bi bi-eye"></i>
        </button>
    `;
    
    if (doc.status === 'pending') {
        return `
            <div class="btn-group btn-group-sm">
                ${baseButtons}
                <button class="btn btn-sm btn-outline-success" title="Approve" onclick="approveDocument(${doc.id}, '${doc.documentType}', '${doc.studentName}')">
                    <i class="bi bi-check-lg"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" title="Reject" onclick="rejectDocument(${doc.id}, '${doc.documentType}')">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        `;
    } else if (doc.status === 'approved') {
        return `
            <div class="btn-group btn-group-sm">
                ${baseButtons}
                <button class="btn btn-sm btn-outline-secondary" title="Download" onclick="downloadDocument('${doc.documentPath}')">
                    <i class="bi bi-download"></i>
                </button>
            </div>
        `;
    } else if (doc.status === 'rejected') {
        return `
            <div class="btn-group btn-group-sm">
                ${baseButtons}
                <button class="btn btn-sm btn-outline-info" title="View Reason" onclick="viewReason(${doc.id}, '${doc.remarks || 'No reason provided'}')">
                    <i class="bi bi-info-circle"></i>
                </button>
            </div>
        `;
    }
}

// School filter - dynamically populate course dropdown
$('#schoolFilter').on('change', function() {
    const school = this.value;
    const courseDropdown = $('#documentTypeFilter');
    
    // Clear existing options
    courseDropdown.html('<option value="">All Courses</option>');
    
    if (school && courseOptions[school]) {
        // Enable dropdown and populate with courses
        courseDropdown.prop('disabled', false);
        courseOptions[school].forEach(course => {
            courseDropdown.append(`<option value="${course.value}">${course.text}</option>`);
        });
    } else {
        // Disable dropdown if no school selected
        courseDropdown.prop('disabled', true);
    }
    
    // Apply filter
    applyCustomFilters();
});

// Course filter
$('#documentTypeFilter').on('change', applyCustomFilters);

// Batch filter
$('#batchFilter').on('change', applyCustomFilters);

// Apply custom filters
function applyCustomFilters() {
    const school = $('#schoolFilter').val();
    const course = $('#documentTypeFilter').val();
    const batch = $('#batchFilter').val();
    
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            const rowData = documentsTable.row(dataIndex).data();
            
            let matchSchool = !school || (rowData.school && rowData.school.toLowerCase() === school.toLowerCase());
            let matchCourse = !course || (rowData.course && rowData.course.toLowerCase().includes(course.toLowerCase()));
            let matchBatch = !batch || (rowData.batch && rowData.batch.toLowerCase() === batch.toLowerCase());
            
            return matchSchool && matchCourse && matchBatch;
        }
    );
    
    documentsTable.draw();
    $.fn.dataTable.ext.search.pop();
}

// Use DataTables search instead
$('#searchInput').on('keyup', function() {
    documentsTable.search(this.value).draw();
});

// Reset filters
$('#resetFilters').on('click', function() {
    $('#searchInput').val('');
    $('#schoolFilter').val('');
    $('#documentTypeFilter').html('<option value="">Course</option>').prop('disabled', true);
    $('#batchFilter').val('');
    
    documentsTable.search('').draw();
    applyCustomFilters();
});

// View document
function viewDocument(path, type) {
    const modal = new bootstrap.Modal(document.getElementById('viewDocumentModal'));
    const modalBody = $('#viewDocumentModal .modal-body');
    const modalTitle = $('#viewDocumentModal .modal-title');
    
    modalTitle.text(`View ${type}`);
    
    const fileExtension = path.split('.').pop().toLowerCase();
    const filePath = `/uploads/documents/${path}`;
    
    if (fileExtension === 'pdf') {
        modalBody.html(`
            <iframe src="${filePath}" style="width: 100%; height: 500px; border: none;"></iframe>
        `);
    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
        modalBody.html(`
            <img src="${filePath}" class="img-fluid" alt="${type}">
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

// Approve document
function approveDocument(id, type, studentName) {
    currentApproveId = id;
    currentApproveType = type;
    currentApproveStudent = studentName;
    
    $('#approveDocumentType').text(type);
    $('#approveStudentName').text(studentName || 'this student');
    
    const modal = new bootstrap.Modal(document.getElementById('approveDocumentModal'));
    modal.show();
}

// Confirm approve with loading state
function confirmApprove() {
    if (!currentApproveId) {
        console.error('No approve ID set');
        return;
    }
    
    const confirmBtn = $('#confirmApproveBtn');
    const originalHtml = confirmBtn.html();
    confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Approving...');
    
    fetch('approve-document.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: currentApproveId, type: currentApproveType })
    })
    .then(response => response.json())
    .then(data => {
        // Hide modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('approveDocumentModal'));
        modal.hide();
        
        // Reset button
        confirmBtn.prop('disabled', false).html(originalHtml);
        
        if (data.success) {
            showToast('success', 'Document Approved', `${currentApproveType} has been approved successfully!`);
            documentsTable.ajax.reload();
            
            // Reset variables
            currentApproveId = null;
            currentApproveType = null;
            currentApproveStudent = null;
        } else {
            showToast('error', 'Approval Failed', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Hide modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('approveDocumentModal'));
        if (modal) modal.hide();
        
        // Reset button
        confirmBtn.prop('disabled', false).html(originalHtml);
        
        showToast('error', 'Network Error', 'Failed to approve document. Check console.');
    });
}

// Reject document
function rejectDocument(id, type) {
    currentRejectId = id;
    currentRejectType = type;
    $('#rejectDocumentModal .modal-title').text(`Reject ${type}`);
    const modal = new bootstrap.Modal(document.getElementById('rejectDocumentModal'));
    modal.show();
}

function confirmReject() {
    if (!currentRejectId) {
        console.error('No reject ID set');
        return;
    }
    
    const reason = $('#rejectionReason').val();
    if (reason.trim() === '') {
        alert('Please provide a reason for rejection');
        return;
    }
    
    const confirmBtn = $('#rejectDocumentModal .btn-danger');
    const originalHtml = confirmBtn.html();
    confirmBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Rejecting...');
    
    fetch('reject-document.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: currentRejectId, type: currentRejectType, reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        // Hide modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('rejectDocumentModal'));
        modal.hide();
        
        // Reset button
        confirmBtn.prop('disabled', false).html(originalHtml);
        
        if (data.success) {
            showToast('success', 'Document Rejected', `${currentRejectType} has been rejected.`);
            $('#rejectionReason').val('');
            documentsTable.ajax.reload();
            
            // Reset variables
            currentRejectId = null;
            currentRejectType = null;
        } else {
            showToast('error', 'Rejection Failed', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Hide modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('rejectDocumentModal'));
        if (modal) modal.hide();
        
        // Reset button
        confirmBtn.prop('disabled', false).html(originalHtml);
        
        showToast('error', 'Network Error', 'Failed to reject document.');
    });
}

// Toast notification function
function showToast(type, title, message) {
    const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
    const icon = type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill';
    
    const toast = `
        <div class="toast align-items-center text-white ${bgColor} border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${icon} me-2"></i>
                    <strong>${title}:</strong> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    $('body').append(toast);
    const toastElement = $('.toast').last()[0];
    const bsToast = new bootstrap.Toast(toastElement, { delay: 3000 });
    bsToast.show();
    
    // Remove from DOM after hidden
    $(toastElement).on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

// ...existing helper functions...
</script>

<?php include '../footer/footer.php'; ?>