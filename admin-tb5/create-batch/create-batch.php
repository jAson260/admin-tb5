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
                            <i class="bi bi-collection me-2"></i>Batch Management
                        </h2>
                        <p class="text-white-50 mb-0">
                            Create and manage training batches for different schools and courses
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-light" onclick="showCreateBatchModal()">
                            <i class="bi bi-plus-circle me-2"></i>Create New Batch
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
                                <i class="bi bi-collection text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Total Batches</div>
                                <h3 class="mb-0 fw-bold"><span id="totalBatchesCount">0</span></h3>
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
                                <i class="bi bi-play-circle text-success fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Active Batches</div>
                                <h3 class="mb-0 fw-bold"><span id="activeBatchesCount">0</span></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <img src="../assets/img/tb5-logo.png" alt="TB5"
                                style="width:48px;height:48px;object-fit:cover;border-radius:50%;
                                       box-shadow:0 2px 8px rgba(0,0,0,.15);">
                            <div>
                                <div class="text-muted small">Total Students</div>
                                <h3 class="mb-0 fw-bold"><span id="totalStudentsCount">0</span></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <img src="../assets/img/bbi-logo.png" alt="BBI"
                                style="width:48px;height:48px;object-fit:cover;border-radius:50%;
                                       box-shadow:0 2px 8px rgba(0,0,0,.15);">
                            <div>
                                <div class="text-muted small">Completed</div>
                                <h3 class="mb-0 fw-bold" style="color:#e0314e;"><span id="completedBatchesCount">0</span></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small mb-1">
                            <i class="bi bi-search me-1"></i>Search
                        </label>
                        <input type="text" class="form-control" id="searchBatch"
                            placeholder="Batch name or ID..." onkeyup="applyFilters()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small mb-1">School</label>
                        <select class="form-select" id="filterSchool" onchange="applyFilters()">
                            <option value="">All Schools</option>
                            <option value="tb5">TB5</option>
                            <option value="bbi">BBI</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small mb-1">Status</label>
                        <select class="form-select" id="filterStatus" onchange="applyFilters()">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Batches Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-table me-2 text-primary"></i>All Batches
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Show</label>
                        <select class="form-select form-select-sm" id="entriesLength"
                            style="width:75px;" onchange="batchesTable.page.len(this.value).draw()">
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
                    <table class="table table-hover align-middle mb-0" id="batchesTable" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width:45px;">#</th>
                                <th>Batch ID</th>
                                <th>Batch Name</th>
                                <th>School</th>
                                <th>Course</th>
                                <th>Students</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th class="text-center" style="width:120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small" id="dtInfo">Showing 0 to 0 of 0 entries</div>
                    <nav><ul class="pagination pagination-sm mb-0" id="dtPagination"></ul></nav>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ==================== CREATE / EDIT BATCH MODAL ==================== -->
<div class="modal fade" id="createBatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold" id="createBatchModalTitle">
                    <i class="bi bi-plus-circle me-2"></i>Create New Batch
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="createBatchForm">
                    <input type="hidden" id="editingBatchRowId" value="">

                    <!-- Batch ID -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-hash me-2"></i>Batch ID Configuration
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Batch ID</label>
                                    <input type="text" class="form-control" id="batchId"
                                        placeholder="e.g., BATCH-2024-001">
                                    <small class="text-muted">Leave empty for auto-generated ID</small>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary w-100"
                                        onclick="generateBatchId()">
                                        <i class="bi bi-shuffle me-1"></i>Generate ID
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Batch Info -->
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-info-circle me-2"></i>Batch Information
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Batch Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="batchName"
                                placeholder="e.g., CSS Batch January 2024" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Select School <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="batchSchool" onchange="loadCourses()" required>
                                <option value="">Choose School...</option>
                                <option value="tb5">The Big Five Training and Assessment Center (TB5)</option>
                                <option value="bbi">Big Blossom Institute Inc. (BBI)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Select Course <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="batchCourse" disabled required>
                                <option value="">Choose Course...</option>
                            </select>
                        </div>
                    </div>

                    <!-- Schedule -->
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-calendar-event me-2"></i>Batch Schedule
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Start Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="startDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                End Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="endDate" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-text-paragraph me-2"></i>Additional Details
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description (Optional)</label>
                        <textarea class="form-control" id="batchDescription" rows="3"
                            placeholder="Add notes or description about this batch..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary px-4" id="saveBatchBtn" onclick="saveBatch()">
                    <i class="bi bi-check-circle me-1"></i>Create Batch
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== VIEW BATCH MODAL ==================== -->
<div class="modal fade" id="viewBatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-eye me-2"></i>Batch Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="viewBatchContent">
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

<!-- ==================== DELETE BATCH MODAL ==================== -->
<div class="modal fade" id="deleteBatchModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-trash me-2"></i>Delete Batch
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle text-danger" style="font-size:3rem;"></i>
                <p class="mt-3 fw-semibold mb-1">Are you sure?</p>
                <p class="text-muted small mb-0">
                    Deleting <strong class="text-danger" id="deleteBatchName"></strong>
                    cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBatchBtn">
                    <i class="bi bi-trash me-1"></i>Delete
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
.font-monospace { font-family: 'Courier New', monospace; }
.is-invalid { border-color: #dc3545 !important; }
.dataTables_length,
.dataTables_filter,
.dataTables_info,
.dataTables_paginate { display: none !important; }
</style>

<script>
let batchesTable;
let currentDeleteBatchId = null;

$(document).ready(function () {
    batchesTable = $('#batchesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get-batches.php',
            type: 'POST',
            data: function (d) {
                d.schoolFilter = $('#filterSchool').val();
                d.statusFilter = $('#filterStatus').val();
            },
            error: function (xhr, error, thrown) {
                console.error('DataTable Error:', error, thrown);
            }
        },
        columns: [
            {
                data: null, orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'BatchCode',
                render: function (data) {
                    return `<span class="badge bg-dark font-monospace px-2 py-1"
                        style="font-size:.8rem;">${escapeHtml(data)}</span>`;
                }
            },
            {
                data: 'BatchName',
                render: function (data) {
                    return `<div class="fw-semibold small">${escapeHtml(data)}</div>`;
                }
            },
            {
                data: 'School',
                render: function (data) {
                    return data === 'TB5'
                        ? `<div class="d-flex align-items-center gap-1">
                                <img src="../assets/img/tb5-logo.png" alt="TB5"
                                    style="width:24px;height:24px;object-fit:cover;border-radius:50%;">
                                <span class="fw-semibold small">TB5</span>
                           </div>`
                        : `<div class="d-flex align-items-center gap-1">
                                <img src="../assets/img/bbi-logo.png" alt="BBI"
                                    style="width:24px;height:24px;object-fit:cover;border-radius:50%;">
                                <span class="fw-semibold small" style="color:#e0314e;">BBI</span>
                           </div>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return `<div class="small fw-semibold">${escapeHtml(row.CourseName || 'N/A')}</div>
                            <small class="text-muted font-monospace">${escapeHtml(row.CourseCode || '')}</small>`;
                }
            },
            {
                data: 'CurrentStudents',
                render: function (data) {
                    return `<span class="badge bg-primary rounded-pill">${data || 0} Students</span>`;
                }
            },
            {
                data: 'StartDate',
                render: function (data) { return formatDate(data); }
            },
            {
                data: 'EndDate',
                render: function (data) { return formatDate(data); }
            },
            {
                data: 'Status',
                render: function (data) {
                    const cls = data === 'Active'     ? 'bg-success'
                              : data === 'Completed'  ? 'bg-secondary'
                              : 'bg-warning text-dark';
                    return `<span class="badge ${cls}">${escapeHtml(data)}</span>`;
                }
            },
            {
                data: null, orderable: false, className: 'text-center',
                render: function (data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info" title="View"
                                onclick="viewBatch(${row.Id})">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-success" title="Edit"
                                onclick="editBatch(${row.Id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger" title="Delete"
                                onclick="deleteBatch(${row.Id}, '${escapeJs(row.BatchCode)} - ${escapeJs(row.BatchName)}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>`;
                }
            }
        ],
        pageLength: 10,
        order: [[0, 'asc']],
        responsive: true,
        dom: 'rt',
        drawCallback: function () {
            updateStats();
            const api   = this.api();
            const info  = api.page.info();
            const total = info.recordsDisplay;
            $('#dtInfo').text(
                total === 0
                    ? 'Showing 0 to 0 of 0 entries'
                    : `Showing ${info.start + 1} to ${info.end} of ${total} entries`
            );
            buildPagination(api);
        }
    });

    // ── SEARCH ────────────────────────────────────────────────────────────────
    let searchTimeout;
    $('#searchBatch').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            batchesTable.search($('#searchBatch').val()).draw();
        }, 500);
    });

    $('#filterSchool, #filterStatus').on('change', function () {
        batchesTable.ajax.reload();
    });

    // ── CONFIRM DELETE ────────────────────────────────────────────────────────
    $('#confirmDeleteBatchBtn').on('click', function () {
        if (currentDeleteBatchId) performDeleteBatch(currentDeleteBatchId);
    });

    updateStats();
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
        onclick="batchesTable.page('previous').draw('page'); return false;">
        <i class="bi bi-chevron-left"></i></a>`;
    ul.appendChild(prev);

    const range = 2;
    for (let p = 0; p < pages; p++) {
        if (p === 0 || p === pages - 1 ||
            (p >= cur - range && p <= cur + range)) {
            const li = document.createElement('li');
            li.className = `page-item ${p === cur ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#"
                onclick="batchesTable.page(${p}).draw('page'); return false;">${p + 1}</a>`;
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
        onclick="batchesTable.page('next').draw('page'); return false;">
        <i class="bi bi-chevron-right"></i></a>`;
    ul.appendChild(next);
}

// ─── FILTERS ──────────────────────────────────────────────────────────────────
function applyFilters() {
    batchesTable.search($('#searchBatch').val()).draw();
    batchesTable.ajax.reload();
}

function resetFilters() {
    $('#searchBatch').val('');
    $('#filterSchool').val('');
    $('#filterStatus').val('');
    batchesTable.search('').draw();
    batchesTable.ajax.reload();
}

// ─── SHOW CREATE MODAL ────────────────────────────────────────────────────────
function showCreateBatchModal() {
    resetCreateForm();
    $('#createBatchModalTitle').html('<i class="bi bi-plus-circle me-2"></i>Create New Batch');
    $('#saveBatchBtn').html('<i class="bi bi-check-circle me-1"></i>Create Batch');
    new bootstrap.Modal(document.getElementById('createBatchModal')).show();
}

// ─── LOAD COURSES ─────────────────────────────────────────────────────────────
function loadCourses() {
    const school         = document.getElementById('batchSchool').value;
    const courseDropdown = document.getElementById('batchCourse');
    courseDropdown.innerHTML = '<option value="">Loading courses...</option>';
    courseDropdown.disabled  = true;
    if (!school) { courseDropdown.innerHTML = '<option value="">Choose Course...</option>'; return; }
    fetch(`get-courses-by-school.php?school=${school}`)
        .then(r => r.json())
        .then(data => {
            courseDropdown.innerHTML = '<option value="">Choose Course...</option>';
            if (data.success && data.courses.length > 0) {
                courseDropdown.disabled = false;
                data.courses.forEach(c => {
                    const o = document.createElement('option');
                    o.value       = c.Id;
                    o.textContent = `${c.CourseCode} - ${c.CourseName}`;
                    courseDropdown.appendChild(o);
                });
            } else {
                courseDropdown.innerHTML = '<option value="">No courses available</option>';
            }
        })
        .catch(() => { courseDropdown.innerHTML = '<option value="">Error loading courses</option>'; });
}

// ─── GENERATE BATCH ID ────────────────────────────────────────────────────────
function generateBatchId() {
    const year   = new Date().getFullYear();
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    document.getElementById('batchId').value = `BATCH-${year}-${random}`;
}

// ─── SAVE BATCH ───────────────────────────────────────────────────────────────
function saveBatch() {
    document.querySelectorAll('.is-invalid')
        .forEach(el => el.classList.remove('is-invalid'));

    let hasError = false;
    ['batchName', 'batchSchool', 'batchCourse', 'startDate', 'endDate'].forEach(id => {
        if (!document.getElementById(id).value.trim()) {
            document.getElementById(id).classList.add('is-invalid');
            hasError = true;
        }
    });
    if (hasError) { showToast('Please fill in all required fields.', 'danger'); return; }

    const $btn     = $('#saveBatchBtn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

    const editingId    = document.getElementById('editingBatchRowId').value;
    const courseSelect = document.getElementById('batchCourse');
    const courseText   = courseSelect.options[courseSelect.selectedIndex].textContent;

    const batchData = {
        batchId:     document.getElementById('batchId').value || null,
        editingId,
        batchName:   document.getElementById('batchName').value.trim(),
        school:      document.getElementById('batchSchool').value.toUpperCase(),
        courseId:    courseSelect.value,
        courseCode:  courseText.split(' - ')[0].trim(),
        courseName:  courseText.split(' - ')[1]?.trim() || courseText,
        startDate:   document.getElementById('startDate').value,
        endDate:     document.getElementById('endDate').value,
        description: document.getElementById('batchDescription').value
    };

    fetch('save-batch.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(batchData)
    })
    .then(r => r.json())
    .then(result => {
        $btn.prop('disabled', false).html(origHtml);
        if (result.success) {
            bootstrap.Modal.getInstance(
                document.getElementById('createBatchModal')).hide();
            resetCreateForm();
            batchesTable.ajax.reload();
            updateStats();
            showToast(
                editingId ? 'Batch updated successfully.' : 'Batch created successfully.',
                'success'
            );
        } else {
            showToast('Error: ' + (result.message || 'Failed to save batch.'), 'danger');
        }
    })
    .catch(() => {
        $btn.prop('disabled', false).html(origHtml);
        showToast('Network error. Please try again.', 'danger');
    });
}

// ─── VIEW BATCH ───────────────────────────────────────────────────────────────
function viewBatch(batchId) {
    $('#viewBatchContent').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2 text-muted small">Loading...</p>
        </div>`);

    new bootstrap.Modal(document.getElementById('viewBatchModal')).show();

    $.ajax({
        url: 'get-batch-details.php', method: 'GET',
        data: { id: batchId }, dataType: 'json',
        success: function (response) {
            if (!response.success) {
                $('#viewBatchContent').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        ${escapeHtml(response.message)}
                    </div>`);
                return;
            }

            const b = response.batch;
            const schoolBadge = b.School === 'TB5'
                ? `<div class="d-flex align-items-center gap-1">
                        <img src="../assets/img/tb5-logo.png"
                            style="width:24px;height:24px;object-fit:cover;border-radius:50%;">
                        <span class="fw-semibold small">TB5</span>
                   </div>`
                : `<div class="d-flex align-items-center gap-1">
                        <img src="../assets/img/bbi-logo.png"
                            style="width:24px;height:24px;object-fit:cover;border-radius:50%;">
                        <span class="fw-semibold small" style="color:#e0314e;">BBI</span>
                   </div>`;

            const statusCls = b.Status === 'Active'    ? 'bg-success'
                            : b.Status === 'Completed' ? 'bg-secondary'
                            : 'bg-warning text-dark';

            $('#viewBatchContent').html(`
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Batch ID</div>
                            <div class="fw-bold font-monospace">${escapeHtml(b.BatchCode)}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">School</div>
                            <div class="fw-bold d-flex align-items-center gap-2">${schoolBadge}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3 border">
                            <div class="text-muted small mb-1">Batch Name</div>
                            <div class="fw-bold">${escapeHtml(b.BatchName)}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Course</div>
                            <div class="fw-bold small">${escapeHtml(b.CourseCode)} — ${escapeHtml(b.CourseName)}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Students</div>
                            <div class="fw-bold">${b.CurrentStudents || 0} / ${b.MaxStudents || '∞'}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Status</div>
                            <span class="badge ${statusCls} px-3 py-2">${escapeHtml(b.Status)}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Start Date</div>
                            <div class="fw-bold">${formatDate(b.StartDate)}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">End Date</div>
                            <div class="fw-bold">${formatDate(b.EndDate)}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3 border">
                            <div class="text-muted small mb-1">Description</div>
                            <div>${escapeHtml(b.Description || 'No description provided.')}</div>
                        </div>
                    </div>
                </div>`);
        },
        error: function () {
            $('#viewBatchContent').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-wifi-off me-1"></i>Failed to load batch details.
                </div>`);
        }
    });
}

// ─── EDIT BATCH ───────────────────────────────────────────────────────────────
function editBatch(batchId) {
    $.ajax({
        url: 'get-batch-details.php', method: 'GET',
        data: { id: batchId }, dataType: 'json',
        success: function (response) {
            if (!response.success) { showToast('Error: ' + response.message, 'danger'); return; }
            const b = response.batch;
            document.getElementById('editingBatchRowId').value = batchId;
            document.getElementById('batchId').value           = b.BatchCode;
            document.getElementById('batchName').value         = b.BatchName;
            document.getElementById('batchSchool').value       = b.School.toLowerCase();
            document.getElementById('startDate').value         = b.StartDate;
            document.getElementById('endDate').value           = b.EndDate;
            document.getElementById('batchDescription').value  = b.Description || '';
            $('#createBatchModalTitle').html('<i class="bi bi-pencil me-2"></i>Edit Batch');
            $('#saveBatchBtn').html('<i class="bi bi-check-circle me-1"></i>Update Batch');
            loadCourses();
            setTimeout(() => { document.getElementById('batchCourse').value = b.CourseId; }, 300);
            new bootstrap.Modal(document.getElementById('createBatchModal')).show();
        },
        error: function () { showToast('Failed to load batch details.', 'danger'); }
    });
}

// ─── DELETE BATCH ─────────────────────────────────────────────────────────────
function deleteBatch(batchId, batchName) {
    currentDeleteBatchId = batchId;
    $('#deleteBatchName').text(batchName);
    new bootstrap.Modal(document.getElementById('deleteBatchModal')).show();
}

function performDeleteBatch(batchId) {
    const $btn = $('#confirmDeleteBatchBtn');
    const orig = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Deleting...');

    $.ajax({
        url: 'delete-batch.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ batch_id: batchId }),
        dataType: 'json',
        success: function (response) {
            $btn.prop('disabled', false).html(orig);
            bootstrap.Modal.getInstance(
                document.getElementById('deleteBatchModal')).hide();
            currentDeleteBatchId = null;
            if (response.success) {
                batchesTable.ajax.reload();
                updateStats();
                showToast('Batch deleted successfully.', 'success');
            } else {
                showToast('Error: ' + (response.message || 'Failed to delete.'), 'danger');
            }
        },
        error: function () {
            $btn.prop('disabled', false).html(orig);
            bootstrap.Modal.getInstance(
                document.getElementById('deleteBatchModal')).hide();
            showToast('An error occurred. Please try again.', 'danger');
        }
    });
}

// ─── RESET FORM ───────────────────────────────────────────────────────────────
function resetCreateForm() {
    document.getElementById('createBatchForm').reset();
    document.getElementById('batchCourse').disabled    = true;
    document.getElementById('editingBatchRowId').value = '';
    document.querySelectorAll('.is-invalid')
        .forEach(el => el.classList.remove('is-invalid'));
}

// ─── STATS ────────────────────────────────────────────────────────────────────
function updateStats() {
    $.ajax({
        url: 'get-batch-statistics.php', method: 'GET', dataType: 'json',
        success: function (data) {
            if (data.success && data.statistics) {
                $('#totalBatchesCount').text(data.statistics.total       || 0);
                $('#activeBatchesCount').text(data.statistics.active     || 0);
                $('#totalStudentsCount').text(data.statistics.students   || 0);
                $('#completedBatchesCount').text(data.statistics.completed || 0);
            }
        }
    });
}

// ─── TOAST ────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const el = document.getElementById('toastMsg');
    el.className = `toast align-items-center text-white border-0 bg-${type}`;
    document.getElementById('toastText').textContent = msg;
    bootstrap.Toast.getOrCreateInstance(el, { delay: 3500 }).show();
}

// ─── HELPERS ──────────────────────────────────────────────────────────────────
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const d      = new Date(dateString);
    const months = ['Jan','Feb','Mar','Apr','May','Jun',
                    'Jul','Aug','Sep','Oct','Nov','Dec'];
    return `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
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