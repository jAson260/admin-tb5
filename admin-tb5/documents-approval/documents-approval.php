<?php

session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');
checkAdmin();

if (isset($_GET['action']) && $_GET['action'] === 'get_courses') {
    header('Content-Type: application/json');
    $school = $_GET['school'] ?? '';
    if (empty($school)) {
        echo json_encode(['success' => false, 'message' => 'School parameter required']);
        exit;
    }
    try {
        $stmt = $pdo->prepare("
            SELECT Id, CourseCode, CourseName, School
            FROM courses
            WHERE School = ? AND IsActive = 1
            ORDER BY CourseName ASC
        ");
        $stmt->execute([strtoupper($school)]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'courses' => $courses]);
    } catch (PDOException $e) {
        error_log('Get Courses Filter Error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

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
                            <i class="bi bi-file-earmark-check me-2"></i>Documents Approval
                        </h2>
                        <p class="text-white-50 mb-0">
                            Review and approve submitted documents from students
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-light btn-sm">
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
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-clock-history text-warning fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Pending</div>
                                <h3 class="mb-0 fw-bold" id="statPending">—</h3>
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
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Approved</div>
                                <h3 class="mb-0 fw-bold" id="statApproved">—</h3>
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
                                <i class="bi bi-x-circle text-danger fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Rejected</div>
                                <h3 class="mb-0 fw-bold" id="statRejected">—</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-people text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Students</div>
                                <h3 class="mb-0 fw-bold" id="statTotal">—</h3>
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
                        <input type="text" class="form-control" id="searchInput"
                            placeholder="Search by student name...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small mb-1">School</label>
                        <select class="form-select" id="schoolFilter">
                            <option value="">All Schools</option>
                            <option value="TB5">TB5</option>
                            <option value="BBI">BBI</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small mb-1">Course</label>
                        <select class="form-select" id="courseFilter" disabled>
                            <option value="">Select School First</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small mb-1">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Has Pending</option>
                            <option value="approved">All Approved</option>
                            <option value="rejected">Has Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" id="resetFilters">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Table (grouped by student) -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-table me-2 text-primary"></i>Student Documents
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Show</label>
                        <select class="form-select form-select-sm" id="entriesLength"
                            style="width:75px;" onchange="documentsTable.page.len(this.value).draw()">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="small text-muted mb-0">entries</label>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="documentsTable" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width:32px;"></th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>School</th>
                                <th>Documents</th>
                                <th>Overall Status</th>
                                <th class="text-center" style="width:120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small" id="dtInfo">Showing 0 to 0 of 0 students</div>
                    <nav><ul class="pagination pagination-sm mb-0" id="dtPagination"></ul></nav>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ==================== DOCUMENT DETAIL MODAL (per student) ==================== -->
<div class="modal fade" id="studentDocsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-folder2-open me-2"></i>
                    <span id="studentDocsModalTitle">Student Documents</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="list-group list-group-flush" id="studentDocsList"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== VIEW DOCUMENT MODAL ==================== -->
<div class="modal fade" id="viewDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    <span id="viewDocumentTitle">View Document</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="viewDocumentBody"></div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary px-4"
                    data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== APPROVE MODAL ==================== -->
<div class="modal fade" id="approveDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-check-circle-fill me-2"></i>Approve Document
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-file-earmark-check-fill text-success" style="font-size:3rem;"></i>
                <p class="mt-3 fw-semibold mb-1">Are you sure?</p>
                <p class="text-muted small mb-0">
                    Approving <strong class="text-success" id="approveDocumentType"></strong>
                    for <strong id="approveStudentName"></strong>
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success px-4" id="confirmApproveBtn">
                    <i class="bi bi-check-circle me-1"></i>Approve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== REJECT MODAL ==================== -->
<div class="modal fade" id="rejectDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    <span id="rejectModalTitle">Reject Document</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <div class="text-center mb-3">
                    <i class="bi bi-file-earmark-x-fill text-danger" style="font-size:3rem;"></i>
                </div>
                <label for="rejectionReason" class="form-label fw-semibold small">
                    <i class="bi bi-pencil-square me-1"></i>Rejection Reason
                    <span class="text-danger">*</span>
                </label>
                <textarea class="form-control" id="rejectionReason" rows="3"
                    placeholder="Please provide a reason for rejection..." required></textarea>
                <div class="form-text">This message will be visible to the student.</div>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" id="confirmRejectBtn">
                    <i class="bi bi-x-circle me-1"></i>Reject
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

/* Expand toggle arrow */
.expand-btn { cursor: pointer; transition: transform .2s; }
.expand-btn.open { transform: rotate(90deg); }

/* Nested documents inside row */
.doc-sub-row td {
    background: #f8f9fa !important;
    border-top: none !important;
}
</style>

<script>
let documentsTable;
let currentApproveId      = null;
let currentApproveType    = null;
let currentApproveStudent = null;
let currentRejectId       = null;
let currentRejectType     = null;

// Raw grouped data keyed by studentId
let groupedData = {};

$(document).ready(function () {

    documentsTable = $('#documentsTable').DataTable({
        ajax: {
            url: 'get-documents.php',
            dataSrc: function (json) {
                if (!json || !json.success) return [];

                updateStatistics(json.statistics);

                // ── Group documents by studentId ──────────────────────────────
                groupedData = {};
                (json.documents || []).forEach(doc => {
                    const sid = doc.studentId;
                    if (!groupedData[sid]) {
                        groupedData[sid] = {
                            studentId:   sid,
                            studentName: doc.studentName,
                            course:      doc.course      || '—',
                            school:      doc.school      || '—',
                            documents:   []
                        };
                    }
                    groupedData[sid].documents.push(doc);
                });

                return Object.values(groupedData);
            },
            error: function (xhr) {
                console.error('AJAX Error:', xhr.responseText);
                showToast('Failed to load documents.', 'danger');
            }
        },
        columns: [
            // Expand toggle
            {
                data: null, orderable: false, className: 'ps-3',
                render: function (data, type, row) {
                    return `<i class="bi bi-chevron-right expand-btn text-muted"
                        data-sid="${row.studentId}"></i>`;
                }
            },
            // Student
            {
                data: null,
                render: function (data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                <i class="bi bi-person-fill text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-semibold small">${escapeHtml(row.studentName)}</div>
                                <small class="text-muted">ID: ${row.studentId}</small>
                            </div>
                        </div>`;
                }
            },
            // Course
            {
                data: 'course',
                render: function (data) {
                    return data && data !== '—'
                        ? `<span class="badge bg-primary bg-opacity-75">${escapeHtml(data)}</span>`
                        : '<span class="text-muted small">—</span>';
                }
            },
            // School
            {
                data: 'school',
                render: function (data) {
                    return data && data !== '—'
                        ? `<span class="badge bg-secondary bg-opacity-75">${escapeHtml(data).toUpperCase()}</span>`
                        : '<span class="text-muted small">—</span>';
                }
            },
            // Documents summary (badges)
            {
                data: 'documents', orderable: false,
                render: function (docs) {
                    if (!docs || !docs.length)
                        return '<span class="text-muted small">No documents</span>';

                    return docs.map(d => {
                        const colors = { pending: 'warning', approved: 'success', rejected: 'danger' };
                        const color  = colors[d.status] || 'secondary';
                        const textClass = d.status === 'pending' ? 'text-dark' : '';
                        return `<span class="badge bg-${color} ${textClass} me-1 mb-1"
                            title="${escapeHtml(d.submissionDate)}">
                            <i class="bi bi-file-earmark-text me-1"></i>${escapeHtml(d.documentType)}
                        </span>`;
                    }).join('');
                }
            },
            // Overall status
            {
                data: 'documents', orderable: false,
                render: function (docs) {
                    if (!docs || !docs.length)
                        return '<span class="badge bg-secondary">No Docs</span>';

                    const statuses = docs.map(d => d.status);
                    if (statuses.every(s => s === 'approved'))
                        return '<span class="badge bg-success"><i class="bi bi-check-all me-1"></i>All Approved</span>';
                    if (statuses.some(s => s === 'rejected'))
                        return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Has Rejected</span>';
                    if (statuses.some(s => s === 'pending'))
                        return '<span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Has Pending</span>';
                    return '<span class="badge bg-secondary">Mixed</span>';
                }
            },
            // Actions
            {
                data: null, orderable: false, className: 'text-center',
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-outline-primary btn-sm"
                            title="View all documents"
                            onclick="openStudentDocs(${row.studentId})">
                            <i class="bi bi-folder2-open me-1"></i>View Docs
                        </button>`;
                }
            }
        ],
        order: [[1, 'asc']],
        pageLength: 10,
        dom: 'rt',
        responsive: true,
        drawCallback: function () {
            const api   = this.api();
            const info  = api.page.info();
            const total = info.recordsDisplay;

            $('#dtInfo').text(
                total === 0
                    ? 'Showing 0 to 0 of 0 students'
                    : `Showing ${info.start + 1} to ${info.end} of ${total} students`
            );
            buildPagination(api);

            // Expand/collapse rows
            $('#documentsTable tbody').off('click', '.expand-btn')
                .on('click', '.expand-btn', function () {
                    const $icon = $(this);
                    const sid   = $icon.data('sid');
                    const tr    = $icon.closest('tr');
                    const subId = `sub-row-${sid}`;

                    if ($(`#${subId}`).length) {
                        $(`#${subId}`).remove();
                        $icon.removeClass('open');
                        return;
                    }

                    $icon.addClass('open');
                    const student = groupedData[sid];
                    if (!student) return;

                    const colSpan = 7;
                    const docsHtml = student.documents.map(d => {
                        const colors   = { pending: 'warning', approved: 'success', rejected: 'danger' };
                        const color    = colors[d.status] || 'secondary';
                        const textCls  = d.status === 'pending' ? 'text-dark' : '';
                        return `
                            <div class="d-flex align-items-center justify-content-between
                                border-bottom py-2 px-3">
                                <div>
                                    <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                    <span class="fw-semibold small">${escapeHtml(d.documentType)}</span>
                                    <span class="text-muted small ms-2">${escapeHtml(d.submissionDate)}</span>
                                    ${d.fileSize ? `<span class="text-muted small ms-1">(${escapeHtml(d.fileSize)})</span>` : ''}
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-${color} ${textCls}">
                                        ${d.status.charAt(0).toUpperCase() + d.status.slice(1)}
                                    </span>
                                    ${getInlineActionButtons(d)}
                                </div>
                            </div>`;
                    }).join('');

                    const subRow = `
                        <tr id="${subId}" class="doc-sub-row">
                            <td colspan="${colSpan}" class="p-0">
                                <div class="border-start border-primary border-3 ms-3">
                                    ${docsHtml}
                                </div>
                            </td>
                        </tr>`;

                    tr.after(subRow);
                });
        }
    });

    // ── SEARCH ────────────────────────────────────────────────────────────────
    $('#searchInput').on('keyup', function () {
        documentsTable.search(this.value).draw();
    });

    // ── STATUS FILTER ─────────────────────────────────────────────────────────
    $('#statusFilter').on('change', applyCustomFilters);

    // ── SCHOOL FILTER ─────────────────────────────────────────────────────────
    $('#schoolFilter').on('change', function () {
        const school      = $(this).val();
        const $courseDrop = $('#courseFilter');
        $courseDrop.html('<option value="">Loading...</option>').prop('disabled', true);

        if (!school) {
            $courseDrop.html('<option value="">Select School First</option>');
            applyCustomFilters();
            return;
        }

        fetch('documents-approval.php?action=get_courses&school=' + encodeURIComponent(school))
            .then(r => r.json())
            .then(data => {
                $courseDrop.html('<option value="">All Courses</option>');
                if (data.success && data.courses.length > 0) {
                    $courseDrop.prop('disabled', false);
                    data.courses.forEach(c => {
                        $courseDrop.append(
                            `<option value="${escapeHtml(c.CourseCode)}">
                                ${escapeHtml(c.CourseCode)} - ${escapeHtml(c.CourseName)}
                            </option>`
                        );
                    });
                } else {
                    $courseDrop.html('<option value="">No courses available</option>');
                }
                applyCustomFilters();
            })
            .catch(() => {
                $courseDrop.html('<option value="">Error loading courses</option>');
                showToast('Failed to load courses.', 'danger');
            });
    });

    $('#courseFilter').on('change', applyCustomFilters);

    // ── RESET ─────────────────────────────────────────────────────────────────
    $('#resetFilters').on('click', function () {
        $('#searchInput').val('');
        $('#schoolFilter').val('');
        $('#statusFilter').val('');
        $('#courseFilter').html('<option value="">Select School First</option>').prop('disabled', true);
        $.fn.dataTable.ext.search = [];
        documentsTable.search('').draw();
    });

    // ── CONFIRM APPROVE ───────────────────────────────────────────────────────
    $('#confirmApproveBtn').on('click', function () {
        if (!currentApproveId) return;
        const $btn     = $(this);
        const origHtml = $btn.html();
        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-1"></span>Approving...');

        fetch('approve-document.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: currentApproveId, type: currentApproveType })
        })
        .then(r => r.json())
        .then(data => {
            $btn.prop('disabled', false).html(origHtml);
            bootstrap.Modal.getInstance(
                document.getElementById('approveDocumentModal')).hide();
            if (data.success) {
                documentsTable.ajax.reload();
                showToast('Document approved successfully.', 'success');
            } else {
                showToast(data.message || 'Approval failed.', 'danger');
            }
            currentApproveId = null; currentApproveType = null;
        })
        .catch(() => {
            $btn.prop('disabled', false).html(origHtml);
            showToast('An error occurred.', 'danger');
        });
    });

    // ── CONFIRM REJECT ────────────────────────────────────────────────────────
    $('#confirmRejectBtn').on('click', function () {
        if (!currentRejectId) return;
        const reason = $('#rejectionReason').val().trim();
        if (!reason) { showToast('Please provide a rejection reason.', 'danger'); return; }

        const $btn     = $(this);
        const origHtml = $btn.html();
        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-1"></span>Rejecting...');

        fetch('reject-document.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: currentRejectId, type: currentRejectType, reason })
        })
        .then(r => r.json())
        .then(data => {
            $btn.prop('disabled', false).html(origHtml);
            bootstrap.Modal.getInstance(
                document.getElementById('rejectDocumentModal')).hide();
            if (data.success) {
                documentsTable.ajax.reload();
                showToast('Document rejected.', 'warning');
            } else {
                showToast(data.message || 'Rejection failed.', 'danger');
            }
            currentRejectId = null; currentRejectType = null;
        })
        .catch(() => {
            $btn.prop('disabled', false).html(origHtml);
            showToast('An error occurred.', 'danger');
        });
    });

    $('#rejectDocumentModal').on('hidden.bs.modal', function () {
        $('#rejectionReason').val('');
    });
});

// ─── OPEN STUDENT DOCS MODAL ──────────────────────────────────────────────────
function openStudentDocs(sid) {
    const student = groupedData[sid];
    if (!student) return;

    $('#studentDocsModalTitle').text(`${escapeHtml(student.studentName)} — Documents`);

    const colors = { pending: 'warning', approved: 'success', rejected: 'danger' };
    const html   = student.documents.map(d => {
        const color   = colors[d.status] || 'secondary';
        const textCls = d.status === 'pending' ? 'text-dark' : '';
        return `
            <div class="list-group-item px-4 py-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <div class="fw-semibold">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                            ${escapeHtml(d.documentType)}
                        </div>
                        <small class="text-muted">
                            Submitted: ${escapeHtml(d.submissionDate)}
                            ${d.fileSize ? ` · ${escapeHtml(d.fileSize)}` : ''}
                        </small>
                        ${d.remarks
                            ? `<div class="text-danger small mt-1">
                                <i class="bi bi-info-circle me-1"></i>${escapeHtml(d.remarks)}
                               </div>`
                            : ''}
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-${color} ${textCls}">
                            ${d.status.charAt(0).toUpperCase() + d.status.slice(1)}
                        </span>
                        ${getInlineActionButtons(d)}
                    </div>
                </div>
            </div>`;
    }).join('');

    $('#studentDocsList').html(html || '<div class="p-4 text-muted text-center">No documents found.</div>');
    new bootstrap.Modal(document.getElementById('studentDocsModal')).show();
}

// ─── INLINE ACTION BUTTONS (used in both expand row & modal) ─────────────────
function getInlineActionButtons(doc) {
    const viewBtn = `
        <button class="btn btn-outline-info btn-sm" title="View"
            onclick="viewDocument('${escapeJs(doc.documentPath)}', '${escapeJs(doc.documentType)}')">
            <i class="bi bi-eye"></i>
        </button>`;

    if (doc.status === 'pending') {
        return `<div class="btn-group btn-group-sm">
            ${viewBtn}
            <button class="btn btn-outline-success btn-sm" title="Approve"
                onclick="triggerApprove(${doc.id}, '${escapeJs(doc.documentType)}', '${escapeJs(doc.studentName)}')">
                <i class="bi bi-check-lg"></i></button>
            <button class="btn btn-outline-danger btn-sm" title="Reject"
                onclick="triggerReject(${doc.id}, '${escapeJs(doc.documentType)}')">
                <i class="bi bi-x-lg"></i></button>
        </div>`;
    } else if (doc.status === 'approved') {
        return `<div class="btn-group btn-group-sm">
            ${viewBtn}
            <button class="btn btn-outline-secondary btn-sm" title="Download"
                onclick="window.open('/uploads/documents/${escapeJs(doc.documentPath)}','_blank')">
                <i class="bi bi-download"></i></button>
        </div>`;
    } else {
        return `<div class="btn-group btn-group-sm">
            ${viewBtn}
            <button class="btn btn-outline-warning btn-sm" title="Rejection reason"
                onclick="showToast('Reason: ${escapeJs(doc.remarks || 'No reason provided')}','warning')">
                <i class="bi bi-info-circle"></i></button>
        </div>`;
    }
}

// ─── TRIGGER APPROVE / REJECT ─────────────────────────────────────────────────
function triggerApprove(id, type, studentName) {
    currentApproveId      = id;
    currentApproveType    = type;
    currentApproveStudent = studentName;
    $('#approveDocumentType').text(type);
    $('#approveStudentName').text(studentName || 'this student');
    new bootstrap.Modal(document.getElementById('approveDocumentModal')).show();
}

function triggerReject(id, type) {
    currentRejectId   = id;
    currentRejectType = type;
    $('#rejectModalTitle').text(`Reject ${type}`);
    $('#rejectionReason').val('');
    new bootstrap.Modal(document.getElementById('rejectDocumentModal')).show();
}

// ─── VIEW DOCUMENT ────────────────────────────────────────────────────────────
function viewDocument(path, type) {
    $('#viewDocumentTitle').text(`View ${type}`);
    const ext      = path.split('.').pop().toLowerCase();
    const filePath = `/uploads/documents/${path}`;
    let   html;

    if (ext === 'pdf') {
        html = `<iframe src="${filePath}" style="width:100%;height:500px;border:none;"></iframe>`;
    } else if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
        html = `<img src="${filePath}" class="img-fluid d-block mx-auto p-3" alt="${escapeHtml(type)}">`;
    } else {
        html = `
            <div class="text-center p-5">
                <i class="bi bi-file-earmark text-primary" style="font-size:4rem;"></i>
                <p class="mt-3 text-muted">Preview not supported for this file type.</p>
                <a href="${filePath}" download class="btn btn-primary">
                    <i class="bi bi-download me-2"></i>Download File
                </a>
            </div>`;
    }

    $('#viewDocumentBody').html(html);
    new bootstrap.Modal(document.getElementById('viewDocumentModal')).show();
}

// ─── CUSTOM FILTERS ───────────────────────────────────────────────────────────
function applyCustomFilters() {
    const school = $('#schoolFilter').val().toUpperCase();
    const course = $('#courseFilter').val().toUpperCase();
    const status = $('#statusFilter').val();

    $.fn.dataTable.ext.search = [];
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        const row = documentsTable.row(dataIndex).data();
        if (!row) return true;

        const matchSchool = !school || row.school.toUpperCase() === school;
        const matchCourse = !course || row.course.toUpperCase().includes(course);

        if (!status) return matchSchool && matchCourse;

        const statuses = (row.documents || []).map(d => d.status);
        let   matchStatus = false;
        if (status === 'approved')  matchStatus = statuses.every(s => s === 'approved');
        if (status === 'pending')   matchStatus = statuses.some(s => s === 'pending');
        if (status === 'rejected')  matchStatus = statuses.some(s => s === 'rejected');

        return matchSchool && matchCourse && matchStatus;
    });

    documentsTable.draw();
}

// ─── STATISTICS ───────────────────────────────────────────────────────────────
function updateStatistics(stats) {
    if (!stats) return;
    $('#statPending').text(stats.pending   ?? '—');
    $('#statApproved').text(stats.approved ?? '—');
    $('#statRejected').text(stats.rejected ?? '—');
    $('#statTotal').text(stats.total       ?? '—');
}

// ─── PAGINATION ───────────────────────────────────────────────────────────────
function buildPagination(api) {
    const info  = api.page.info();
    const pages = info.pages;
    const cur   = info.page;
    const ul    = document.getElementById('dtPagination');
    ul.innerHTML = '';
    if (pages <= 1) return;

    const mkItem = (label, page, disabled, active) => {
        const li  = document.createElement('li');
        li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;
        li.innerHTML = disabled
            ? `<span class="page-link">${label}</span>`
            : `<a class="page-link" href="#"
                onclick="documentsTable.page(${page}).draw('page'); return false;">${label}</a>`;
        return li;
    };

    ul.appendChild(mkItem('<i class="bi bi-chevron-left"></i>', 'previous',  cur === 0, false));

    const range = 2;
    for (let p = 0; p < pages; p++) {
        if (p === 0 || p === pages - 1 || (p >= cur - range && p <= cur + range)) {
            ul.appendChild(mkItem(p + 1, p, false, p === cur));
        } else if (p === cur - range - 1 || p === cur + range + 1) {
            ul.appendChild(mkItem('…', null, true, false));
        }
    }

    ul.appendChild(mkItem('<i class="bi bi-chevron-right"></i>', 'next', cur === pages - 1, false));

    // Fix prev/next to use string page
    ul.firstChild.querySelector('a')?.setAttribute('onclick',
        `documentsTable.page('previous').draw('page'); return false;`);
    ul.lastChild.querySelector('a')?.setAttribute('onclick',
        `documentsTable.page('next').draw('page'); return false;`);
}

// ─── TOAST ────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const el = document.getElementById('toastMsg');
    el.className = `toast align-items-center text-white border-0 bg-${type}`;
    document.getElementById('toastText').textContent = msg;
    bootstrap.Toast.getOrCreateInstance(el, { delay: 3500 }).show();
}

// ─── HELPERS ──────────────────────────────────────────────────────────────────
function escapeHtml(text) {
    if (!text) return '';
    const d = document.createElement('div');
    d.textContent = String(text);
    return d.innerHTML;
}

function escapeJs(text) {
    if (!text) return '';
    return String(text)
        .replace(/\\/g, '\\\\')
        .replace(/'/g, "\\'")
        .replace(/"/g, '\\"');
}
</script>

<?php include '../footer/footer.php'; ?>