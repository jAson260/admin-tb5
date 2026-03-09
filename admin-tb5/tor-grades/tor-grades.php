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
                            <i class="bi bi-file-earmark-text me-2"></i>Transcript of Records & Grades
                        </h2>
                        <p class="text-white-50 mb-0">Generate and manage student TOR and Certificates</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#generateTORModal">
                            <i class="bi bi-plus-circle me-2"></i>Generate TOR
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
                                <i class="bi bi-file-earmark-check text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Total TORs</div>
                                <h3 class="mb-0 fw-bold" id="totalTORs">0</h3>
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
                                <i class="bi bi-award text-success fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Competent</div>
                                <h3 class="mb-0 fw-bold" id="competentCount">0</h3>
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
                                <i class="bi bi-calendar-check text-info fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">This Month</div>
                                <h3 class="mb-0 fw-bold" id="thisMonthCount">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-download text-warning fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Downloads</div>
                                <h3 class="mb-0 fw-bold" id="downloadCount">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small mb-1">
                            <i class="bi bi-search me-1"></i>Search TOR
                        </label>
                        <input type="text" class="form-control" id="searchTOR"
                            placeholder="Student name, course, or ISO number...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small mb-1">Filter by Remarks</label>
                        <select class="form-select" id="filterRemarks">
                            <option value="">All Remarks</option>
                            <option value="Competent">Competent</option>
                            <option value="Not Yet Competent">Not Yet Competent</option>
                            <option value="Incomplete">Incomplete</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" onclick="resetTORFilters()">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOR Records Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-table me-2 text-primary"></i>TOR Records
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Show</label>
                        <select class="form-select form-select-sm" id="entriesLength"
                            style="width:75px;" onchange="torTable.page.len(this.value).draw()">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="small text-muted mb-0">entries</label>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="torTable" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">ULI</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Date of Graduation</th>
                                <th>SO Number</th>
                                <th>Status</th>
                                <th class="text-center" style="width:100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small" id="dtInfo">Showing 0 to 0 of 0 records</div>
                    <nav><ul class="pagination pagination-sm mb-0" id="dtPagination"></ul></nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate TOR Modal -->
<div class="modal fade" id="generateTORModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="bi bi-file-earmark-plus me-2"></i>Generate TOR / Transcript of Records
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="generateTORForm">

                    <!-- STEP 1: Generation Mode -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">
                                <span class="badge bg-primary rounded-circle me-2">1</span>
                                Select Generation Mode
                            </h6>
                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="genMode" id="modeByBatch" value="batch" checked>
                                    <label class="btn btn-outline-primary w-100 py-3" for="modeByBatch">
                                        <i class="bi bi-collection-fill d-block fs-4 mb-1"></i>
                                        <span class="fw-semibold">By Batch</span>
                                        <small class="d-block text-muted mt-1">All students in a batch</small>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="genMode" id="modeByCourse" value="course">
                                    <label class="btn btn-outline-primary w-100 py-3" for="modeByCourse">
                                        <i class="bi bi-book-fill d-block fs-4 mb-1"></i>
                                        <span class="fw-semibold">By Course</span>
                                        <small class="d-block text-muted mt-1">All students in a course</small>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="genMode" id="modeByStudent" value="student">
                                    <label class="btn btn-outline-primary w-100 py-3" for="modeByStudent">
                                        <i class="bi bi-person-fill d-block fs-4 mb-1"></i>
                                        <span class="fw-semibold">By Student</span>
                                        <small class="d-block text-muted mt-1">Select one or more students</small>
                                    </label>
                                </div>
                            </div>

                            <!-- By Batch -->
                            <div id="panelByBatch">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Select Batch <span class="text-danger">*</span></label>
                                        <select class="form-select" id="batchSelect">
                                            <option value="">Loading batches...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="alert alert-info mb-0 py-2 px-3 small w-100">
                                            <i class="bi bi-info-circle me-1"></i>
                                            All enrolled students in the selected batch will be listed below.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- By Course -->
                            <div id="panelByCourse" style="display:none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Select Course <span class="text-danger">*</span></label>
                                        <select class="form-select" id="courseSelectMode">
                                            <option value="">Loading courses...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="alert alert-info mb-0 py-2 px-3 small w-100">
                                            <i class="bi bi-info-circle me-1"></i>
                                            All enrolled students in the selected course will be listed below.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- By Student -->
                            <div id="panelByStudent" style="display:none;">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold small">Search Student</label>
                                        <input type="text" class="form-control" id="studentSearchInput"
                                            placeholder="Type name or ULI to search...">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-primary w-100" onclick="searchStudents()">
                                            <i class="bi bi-search me-1"></i>Search
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Student List -->
                            <div id="studentListWrapper" class="mt-3" style="display:none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold small">
                                        <i class="bi bi-people me-1"></i>
                                        Students to Generate TOR
                                        <span class="badge bg-primary ms-1" id="studentListCount">0</span>
                                    </span>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleSelectAll(true)">
                                            <i class="bi bi-check-all me-1"></i>Select All
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleSelectAll(false)">
                                            <i class="bi bi-x me-1"></i>Deselect All
                                        </button>
                                    </div>
                                </div>
                                <div class="border rounded bg-white" style="max-height:260px;overflow-y:auto;">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th style="width:40px;" class="ps-3">
                                                    <input type="checkbox" class="form-check-input" id="selectAllStudents"
                                                        onchange="toggleSelectAll(this.checked)">
                                                </th>
                                                <th>Student Name</th>
                                                <th>ULI</th>
                                                <th>Batch</th>
                                                <th>Course</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentListBody"></tbody>
                                    </table>
                                </div>
                                <div id="selectedStudentsSummary" class="mt-2 small text-muted">
                                    <i class="bi bi-check-circle text-success me-1"></i>
                                    <span id="selectedCount">0</span> student(s) selected for TOR generation
                                </div>
                            </div>

                            <div id="studentListLoading" class="text-center py-3" style="display:none;">
                                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                Loading students...
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Subjects & Grades -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">
                                <span class="badge bg-primary rounded-circle me-2">2</span>
                                Subjects & Grades
                                <small class="text-muted fw-normal ms-2">(auto-loaded from course subjects)</small>
                            </h6>
                            <div id="subjectsLoadingMsg" class="text-center py-3 text-muted">
                                <i class="bi bi-info-circle me-1"></i>Select a batch or course above to load subjects
                            </div>
                            <div id="subjectsTableWrapper" style="display:none;">
                                <div class="alert alert-warning py-2 small mb-3" id="multiStudentGradeNote" style="display:none;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <strong>Multiple students selected.</strong>
                                    These grades will be applied to all selected students.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width:120px;">Code</th>
                                                <th>Subject / Competency</th>
                                                <th style="width:90px;">Hours</th>
                                                <th style="width:110px;">Theoretical (30%)</th>
                                                <th style="width:110px;">Practical (70%)</th>
                                                <th style="width:90px;">Final</th>
                                                <th style="width:130px;">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody id="subjectsGradeBody"></tbody>
                                        <tfoot>
                                            <tr class="table-light fw-bold">
                                                <td colspan="2" class="text-end">OVERALL TOTALS</td>
                                                <td id="totalHours">0 hrs</td>
                                                <td id="overallTheoretical">—</td>
                                                <td id="overallPractical">—</td>
                                                <td id="overallFinal">—</td>
                                                <td id="overallRemarks">—</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: TOR Details -->
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">
                                <span class="badge bg-primary rounded-circle me-2">3</span>
                                TOR Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">Date of Graduation <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="graduationDate" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small">SO Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="isoNumber" placeholder="e.g., 00000000000" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small">Overall Theoretical %</label>
                                    <input type="number" class="form-control bg-white" id="theoreticalGrade" min="0" max="100" step="0.01" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small">Overall Practical %</label>
                                    <input type="number" class="form-control bg-white" id="practicalGrade" min="0" max="100" step="0.01" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small">Average Grade</label>
                                    <input type="number" class="form-control bg-white fw-bold" id="averageGrade" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small">
                                        Final Grade
                                        <small class="text-primary fw-normal" style="font-size:0.7rem;">(type to randomize)</small>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" class="form-control fw-bold" id="finalGrade"
                                            min="85" max="100" step="1" placeholder="e.g. 95">
                                        <button class="btn btn-outline-primary" type="button"
                                            id="btnRandomize" title="Randomize subject grades"
                                            onclick="randomizeGrades($('#finalGrade').val())">
                                            <i class="bi bi-shuffle"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted" style="font-size:0.7rem;">
                                        <i class="bi bi-info-circle me-1"></i>Auto-fills subjects 91–98
                                    </small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold small">Remarks</label>
                                    <input type="text" class="form-control fw-bold" id="remarks">
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <div class="me-auto small text-muted" id="genSummaryFooter"></div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="submitGeneratePDF" disabled>
                    <i class="bi bi-file-earmark-pdf me-1"></i>Generate PDF
                </button>
                <button type="button" class="btn btn-primary" id="submitGenerateTOR" disabled>
                    <i class="bi bi-file-earmark-arrow-down me-1"></i>Generate Excel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Validation Modal -->
<div class="modal fade" id="validationModal" tabindex="-1" style="z-index:99999;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white border-0">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Validation Error
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <i class="bi bi-x-circle text-danger fs-1 mb-3 d-block"></i>
                <p class="mb-0 fw-semibold" id="validationMessage"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">
                    <i class="bi bi-check me-1"></i>OK, Got It
                </button>
            </div>
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
.nav-tabs .nav-link { color: #333; border: none; transition: all 0.3s ease; }
.nav-tabs .nav-link:hover { color: #4169E1; background-color: rgba(65,105,225,0.1); }
.nav-tabs .nav-link.active { background-color: #4169E1 !important; color: white !important; border: none; border-bottom: 3px solid #2948b8; }
.btn-check:checked + .btn-outline-primary { background-color: #0d6efd; color: white; }
.btn-check:checked + .btn-outline-primary small { color: rgba(255,255,255,0.75) !important; }
.sticky-top { top: 0; z-index: 1; }
</style>

<script>
let torTable;
let activeCourseId = null;

$(document).ready(function () {

    // ── DATATABLE ─────────────────────────────────────────────────────────────
    torTable = $('#torTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get-tor-records.php',
            type: 'POST',
            data: function (d) {
                d.searchTOR     = $('#searchTOR').val();
                d.filterRemarks = $('#filterRemarks').val();
            },
            error: function(xhr, error, code) {
                console.error('DataTable Error:', error);
                console.error('Response:', xhr.responseText);
            }
        },
        columns: [
            {
                data: 'student_id',
                className: 'ps-3',
                render: function(data) {
                    return `<span class="badge bg-dark font-monospace px-2 py-1" style="font-size:.8rem;">${data}</span>`;
                }
            },
            {
                data: 'student_name',
                render: function(data) {
                    return `<div class="fw-semibold small">${data}</div>`;
                }
            },
            {
                data: 'course_name',
                render: function(data) {
                    return `<span class="badge bg-primary rounded-pill">${data}</span>`;
                }
            },
            {
                data: 'graduation_date',
                render: function(data) {
                    return `<span class="small">${data}</span>`;
                }
            },
            {
                data: 'iso_number',
                render: function(data) {
                    return `<span class="small font-monospace">${data}</span>`;
                }
            },
            {
                data: 'status',
                render: function(data) {
                    const badges = {
                        'Competent':         'bg-success',
                        'Not Yet Competent': 'bg-danger',
                        'Incomplete':        'bg-warning text-dark',
                        'Pending':           'bg-secondary'
                    };
                    return `<span class="badge ${badges[data] || 'bg-secondary'}">${data}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function(data) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-success" onclick="downloadTOR(${data.id})" title="Download CSV">
                                <i class="bi bi-download"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteTOR(${data.id})" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>`;
                }
            }
        ],
        order: [[3, 'desc']],
        pageLength: 25,
        responsive: true,
        dom: 'rt',
        drawCallback: function() {
            const api   = this.api();
            const info  = api.page.info();
            const total = info.recordsDisplay;
            $('#dtInfo').text(
                total === 0
                    ? 'Showing 0 to 0 of 0 records'
                    : `Showing ${info.start + 1} to ${info.end} of ${total} records`
            );
            buildPagination(api);
        }
    });

    // ── TABLE FILTERS ─────────────────────────────────────────────────────────
    let searchTimeout;
    $('#searchTOR').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() { torTable.ajax.reload(); }, 500);
    });
    $('#filterRemarks').on('change', function() { torTable.ajax.reload(); });

    // ── INIT ──────────────────────────────────────────────────────────────────
    loadStatistics();
    loadAllBatches();
    loadAllCourses();

    // ── MODE SWITCHER ─────────────────────────────────────────────────────────
    $('input[name="genMode"]').on('change', function () {
        const mode = $(this).val();
        $('#panelByBatch').toggle(mode === 'batch');
        $('#panelByCourse').toggle(mode === 'course');
        $('#panelByStudent').toggle(mode === 'student');
        resetStudentList();
        resetSubjectsTable();
        activeCourseId = null;
        if (mode === 'student') {
            loadStudentsForMode('student', '', '');
        }
    });

    // ── BY BATCH CHANGE ───────────────────────────────────────────────────────
    $('#batchSelect').on('change', function () {
        const batchId = $(this).val();
        resetStudentList();
        resetSubjectsTable();
        if (!batchId) return;
        const opt      = $(this).find(':selected');
        activeCourseId = opt.data('course-id') || null;
        loadStudentsForMode('batch', batchId, '');
        if (activeCourseId) {
            loadSubjects(activeCourseId);
        }
    });

    // ── BY COURSE CHANGE ──────────────────────────────────────────────────────
    $('#courseSelectMode').on('change', function () {
        const courseId = $(this).val();
        resetStudentList();
        resetSubjectsTable();
        if (!courseId) return;
        activeCourseId = courseId;
        loadStudentsForMode('course', '', courseId);
        loadSubjects(courseId);
    });

    // ── STUDENT CHECKBOXES ────────────────────────────────────────────────────
    $(document).on('change', '.student-checkbox', function () {
        updateSelectedCount();
        const total   = $('.student-checkbox').length;
        const checked = $('.student-checkbox:checked').length;
        $('#selectAllStudents').prop('checked', total === checked && total > 0);

        // ── LOAD SUBJECTS WHEN STUDENT IS CHECKED (By Student mode) ──────────
        const mode = $('input[name="genMode"]:checked').val();
        if (mode === 'student') {
            const checkedBoxes = $('.student-checkbox:checked');
            if (checkedBoxes.length > 0) {
                // Get course ID from the first checked student
                const courseId = checkedBoxes.first().data('course-id');
                if (courseId && courseId !== activeCourseId) {
                    activeCourseId = courseId;
                    loadSubjects(courseId);
                } else if (!courseId) {
                    resetSubjectsTable();
                    $('#subjectsLoadingMsg')
                        .html('<i class="bi bi-exclamation-circle text-warning me-1"></i>No course linked to this student.')
                        .show();
                }
            } else {
                // No students selected — reset subjects
                activeCourseId = null;
                resetSubjectsTable();
            }
        }
    });

    // ── STUDENT SEARCH ────────────────────────────────────────────────────────
    let studentSearchTimeout;
    $('#studentSearchInput').on('keyup', function () {
        clearTimeout(studentSearchTimeout);
        const q = $(this).val().trim();
        studentSearchTimeout = setTimeout(function () {
            loadStudentsForMode('student', '', '', q);
        }, 400);
    });

    // ── FINAL GRADE INPUT → RANDOMIZE SUBJECT GRADES ─────────────────────────
    $('#finalGrade').on('input', function () {
        const val = parseFloat($(this).val());
        if (!isNaN(val) && val >= 85 && val <= 100) {
            randomizeGrades(val);
        }
    });

    // ── SUBMIT EXCEL ──────────────────────────────────────────────────────────
    $('#submitGenerateTOR').on('click', function () {
        submitTOR('generate-tor-csv.php', 'TOR_Export.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $(this));
    });

    // ── SUBMIT PDF ────────────────────────────────────────────────────────────
    $('#submitGeneratePDF').on('click', function () {
        submitTOR('generate-tor-pdf.php', 'TOR_Export.pdf', 'application/pdf', $(this));
    });

    // ── RESET MODAL ON CLOSE ──────────────────────────────────────────────────
    $('#generateTORModal').on('hidden.bs.modal', function () {
        $('#generateTORForm')[0].reset();
        $('input[name="genMode"][value="batch"]').prop('checked', true);
        $('#panelByBatch').show();
        $('#panelByCourse, #panelByStudent').hide();
        resetStudentList();
        resetSubjectsTable();
        activeCourseId = null;
        $('#submitGenerateTOR').prop('disabled', true);
        $('#submitGeneratePDF').prop('disabled', true);
        $('#genSummaryFooter').text('');
    });

});

// ─── RANDOMIZE GRADES ─────────────────────────────────────────────────────────
function randomizeGrades(targetFinal) {
    const rows = $('#subjectsGradeBody tr[data-index]');
    if (!rows.length) {
        alert('Please select a batch or course first to load subjects.');
        return;
    }

    const target = parseFloat(targetFinal);
    if (isNaN(target) || target < 85 || target > 100) return;

    rows.each(function () {
        const i = $(this).data('index');

        // Random whole number theoretical between 91–98
        const th = Math.floor(Math.random() * (98 - 91 + 1)) + 91;

        // Derive practical: (target - th*0.3) / 0.7 + small jitter ±1
        const jitter = Math.floor(Math.random() * 3) - 1; // –1, 0, or +1
        let pr = Math.round(((target - (th * 0.3)) / 0.7) + jitter);

        // Clamp between 91–98
        pr = Math.min(98, Math.max(91, pr));

        // Fill inputs
        $(`.grade-theoretical[data-row="${i}"]`).val(th);
        $(`.grade-practical[data-row="${i}"]`).val(pr);

        // Per-row final as whole number & remarks
        const fin = Math.round((th * 0.3) + (pr * 0.7));
        const rem = fin >= 85 ? 'Competent' : 'Not Yet Competent';
        $(`#final_${i}`).text(fin);
        $(`#remarks_${i}`).html(
            `<span class="badge ${fin >= 85 ? 'bg-success' : 'bg-danger'} small">${rem}</span>`
        );
    });

    // Recalc overall after all rows filled
    recalcOverall();
}

// ─── LOAD ALL BATCHES ─────────────────────────────────────────────────────────
function loadAllBatches() {
    $('#batchSelect').html('<option value="">Loading batches...</option>');
    $.ajax({
        url: 'get-all-batches.php',
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            const $sel = $('#batchSelect');
            if (res.success && res.batches && res.batches.length) {
                $sel.html('<option value="">Select Batch...</option>');
                res.batches.forEach(b => {
                    $sel.append(`<option value="${b.Id}"
                        data-course-id="${b.CourseId}"
                        data-school="${b.School}">
                        ${b.BatchCode} — ${b.BatchName} (${b.School} · ${b.StudentCount} student/s)
                    </option>`);
                });
            } else {
                $sel.html('<option value="">No batches found</option>');
            }
        },
        error: function (xhr) {
            $('#batchSelect').html('<option value="">Failed to load batches</option>');
            console.error('Batches error:', xhr.responseText);
        }
    });
}

// ─── LOAD ALL COURSES ─────────────────────────────────────────────────────────
function loadAllCourses() {
    $('#courseSelectMode').html('<option value="">Loading courses...</option>');
    $.ajax({
        url: 'get-all-courses.php',
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            const $sel = $('#courseSelectMode');
            if (res.success && res.courses && res.courses.length) {
                $sel.html('<option value="">Select Course...</option>');
                res.courses.forEach(c => {
                    $sel.append(`<option value="${c.Id}">
                        ${c.CourseCode} — ${c.CourseName} (${c.StudentCount} student/s)
                    </option>`);
                });
            } else {
                $sel.html('<option value="">No courses found</option>');
            }
        },
        error: function (xhr) {
            $('#courseSelectMode').html('<option value="">Failed to load courses</option>');
            console.error('Courses error:', xhr.responseText);
        }
    });
}

// ─── LOAD STUDENTS FOR MODE ───────────────────────────────────────────────────
function loadStudentsForMode(mode, batchId, courseId, search) {
    $('#studentListLoading').show();
    $('#studentListWrapper').hide();

    $.ajax({
        url: 'get-students-by-filter.php',
        method: 'GET',
        data: {
            mode:      mode,
            batch_id:  batchId  || '',
            course_id: courseId || '',
            search:    search   || ''
        },
        dataType: 'json',
        success: function (res) {
            $('#studentListLoading').hide();

            if (!res.success || !res.students || !res.students.length) {
                $('#studentListBody').html(`
                    <tr><td colspan="6" class="text-center text-muted py-3">
                        <i class="bi bi-people me-1"></i>No students found
                    </td></tr>`);
                $('#studentListCount').text(0);
                $('#studentListWrapper').show();
                updateSelectedCount();
                return;
            }

            let html = '';
            res.students.forEach(s => {
                const statusBadge = s.Status === 'Approved'
                    ? 'bg-success'
                    : s.Status === 'Pending'
                    ? 'bg-warning text-dark'
                    : 'bg-secondary';
                const isChecked = (mode !== 'student') ? 'checked' : '';

                // ── CourseId must be on the checkbox data attribute ───────────
                const courseId = s.CourseId || activeCourseId || '';

                html += `
                    <tr>
                        <td class="ps-3">
                            <input type="checkbox" class="form-check-input student-checkbox"
                                value="${s.Id}"
                                data-name="${s.FullName}"
                                data-batch-id="${s.BatchId   || ''}"
                                data-course-id="${courseId}"
                                ${isChecked}>
                        </td>
                        <td>
                            <div class="fw-semibold small">${s.FullName}</div>
                            <small class="text-muted">${s.Email}</small>
                        </td>
                        <td><span class="font-monospace small">${s.ULI || '—'}</span></td>
                        <td><span class="small">${s.BatchCode  || '—'}</span></td>
                        <td><span class="small">${s.CourseName || '—'}</span></td>
                        <td><span class="badge ${statusBadge}">${s.Status}</span></td>
                    </tr>`;
            });

            $('#studentListBody').html(html);
            $('#studentListCount').text(res.students.length);
            $('#selectAllStudents').prop('checked', mode !== 'student');
            $('#studentListWrapper').show();
            updateSelectedCount();
        },
        error: function (xhr) {
            $('#studentListLoading').hide();
            $('#studentListBody').html(`
                <tr><td colspan="6" class="text-center text-danger py-3">
                    <i class="bi bi-x-circle me-1"></i>Failed to load students
                </td></tr>`);
            $('#studentListWrapper').show();
            console.error('Students error:', xhr.responseText);
        }
    });
}

// ─── SEARCH STUDENTS ──────────────────────────────────────────────────────────
function searchStudents() {
    const q = $('#studentSearchInput').val().trim();
    loadStudentsForMode('student', '', '', q);
}

// ─── TOGGLE SELECT ALL ────────────────────────────────────────────────────────
function toggleSelectAll(checked) {
    $('.student-checkbox').prop('checked', checked);
    $('#selectAllStudents').prop('checked', checked);
    updateSelectedCount();
}

// ─── UPDATE SELECTED COUNT - enable both buttons ────────────────────────────
function updateSelectedCount() {
    const count = $('.student-checkbox:checked').length;
    $('#selectedCount').text(count);
    $('#submitGenerateTOR').prop('disabled', count === 0);
    $('#submitGeneratePDF').prop('disabled', count === 0);  // ← add this
    $('#multiStudentGradeNote').toggle(count > 1);
    $('#genSummaryFooter').text(
        count > 0 ? `${count} student(s) will have TOR generated` : ''
    );
}

// ─── GET SELECTED IDs ─────────────────────────────────────────────────────────
function getSelectedStudentIds() {
    return $('.student-checkbox:checked').map(function () {
        return $(this).val();
    }).get();
}

// ─── LOAD SUBJECTS ────────────────────────────────────────────────────────────
function loadSubjects(courseId) {
    $('#subjectsLoadingMsg')
        .html('<div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading subjects...')
        .show();
    $('#subjectsTableWrapper').hide();

    $.ajax({
        url: 'get-course-subjects.php',
        method: 'GET',
        data: { course_id: courseId },
        dataType: 'json',
        success: function (res) {
            if (!res.success || !res.subjects || !res.subjects.length) {
                $('#subjectsLoadingMsg')
                    .html('<i class="bi bi-exclamation-circle text-warning me-1"></i>No subjects found for this course.')
                    .show();
                return;
            }
            renderSubjectsTable(res.subjects);
        },
        error: function (xhr) {
            $('#subjectsLoadingMsg')
                .html('<i class="bi bi-x-circle text-danger me-1"></i>Failed to load subjects.')
                .show();
            console.error('Subjects error:', xhr.responseText);
        }
    });
}

// ─── RENDER SUBJECTS TABLE ────────────────────────────────────────────────────
function renderSubjectsTable(subjects) {
    const order = { 'Basic': 1, 'Common': 2, 'Core': 3 };
    subjects.sort((a, b) => (order[a.Competency] || 9) - (order[b.Competency] || 9));

    let html = '', lastComp = '', totalHours = 0;

    subjects.forEach((s, i) => {
        if (s.Competency !== lastComp) {
            html += `
                <tr class="table-secondary">
                    <td colspan="7" class="fw-bold text-uppercase small ps-2">
                        <i class="bi bi-bookmark-fill me-1"></i>${s.Competency} Competencies
                    </td>
                </tr>`;
            lastComp = s.Competency;
        }

        totalHours += parseInt(s.Hours) || 0;

        html += `
            <tr data-index="${i}">
                <td class="font-monospace small">${s.SubjectCode}</td>
                <td class="small">${s.SubjectName}</td>
                <td class="text-center small">${s.Hours} hrs</td>
                <td>
                    <input type="number" class="form-control form-control-sm grade-theoretical"
                        data-row="${i}" data-code="${s.SubjectCode}"
                        min="0" max="100" step="1" placeholder="0–100"
                        maxlength="3"
                        oninput="if(this.value>100)this.value=100;if(this.value<0)this.value=0;this.value=this.value.slice(0,3);">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm grade-practical"
                        data-row="${i}" min="0" max="100" step="1" placeholder="0–100"
                        maxlength="3"
                        oninput="if(this.value>100)this.value=100;if(this.value<0)this.value=0;this.value=this.value.slice(0,3);">
                </td>
                <td class="text-center fw-bold small" id="final_${i}">—</td>
                <td class="text-center small" id="remarks_${i}">—</td>
            </tr>`;
    });

    $('#subjectsGradeBody').html(html);
    $('#totalHours').text(totalHours + ' hrs');
    $('#subjectsLoadingMsg').hide();
    $('#subjectsTableWrapper').show();

    $(document).off('input.grades').on('input.grades', '.grade-theoretical, .grade-practical', function () {
        const row = $(this).data('row');
        const th  = parseFloat($(`.grade-theoretical[data-row="${row}"]`).val()) || 0;
        const pr  = parseFloat($(`.grade-practical[data-row="${row}"]`).val())   || 0;

        if (th > 0 || pr > 0) {
            const fin = Math.round((th * 0.3) + (pr * 0.7));
            const rem = fin >= 85 ? 'Competent' : 'Not Yet Competent';
            $(`#final_${row}`).text(fin);
            $(`#remarks_${row}`).html(
                `<span class="badge ${fin >= 85 ? 'bg-success' : 'bg-danger'} small">${rem}</span>`
            );
        } else {
            $(`#final_${row}`).text('—');
            $(`#remarks_${row}`).text('—');
        }
        recalcOverall();
    });
}

// ─── COLLECT SUBJECT GRADES ───────────────────────────────────────────────────
function collectSubjectGrades() {
    const grades = [];
    $('#subjectsGradeBody tr[data-index]').each(function () {
        const i  = $(this).data('index');
        const th = $(`.grade-theoretical[data-row="${i}"]`);
        grades.push({
            code:        th.data('code'),
            name:        $(this).find('td:eq(1)').text().trim(),
            hours:       $(this).find('td:eq(2)').text().replace(' hrs', '').trim(),
            theoretical: parseFloat(th.val()) || 0,
            practical:   parseFloat($(`.grade-practical[data-row="${i}"]`).val()) || 0,
            final:       parseFloat($(`#final_${i}`).text()) || 0,
            remarks:     $(`#remarks_${i} .badge`).text() || $(`#remarks_${i}`).text() || '—'
        });
    });
    return grades;
}

// ─── GENERATE RANDOMIZED GRADES PER STUDENT ───────────────────────────────────
function generateRandomizedGradesPerStudent(targetFinal) {
    const rows = $('#subjectsGradeBody tr[data-index]');
    if (!rows.length) return [];

    const target = parseFloat(targetFinal);
    if (isNaN(target) || target < 85 || target > 100) return [];

    const perStudentGrades = [];

    rows.each(function () {
        const i    = $(this).data('index');
        const code = $(`.grade-theoretical[data-row="${i}"]`).data('code');
        const name = $(this).find('td:eq(1)').text().trim();
        const hrs  = $(this).find('td:eq(2)').text().replace(' hrs', '').trim();

        // Randomize theoretical between 91–98
        const th = Math.floor(Math.random() * (98 - 91 + 1)) + 91;

        // Derive practical with fresh jitter for THIS student
        const jitter = Math.floor(Math.random() * 3) - 1; // -1, 0, or +1
        let pr = Math.round(((target - (th * 0.3)) / 0.7) + jitter);
        pr = Math.min(98, Math.max(91, pr));

        const fin = Math.round((th * 0.3) + (pr * 0.7));
        const rem = fin >= 85 ? 'Competent' : 'Not Yet Competent';

        perStudentGrades.push({
            code:        code,
            name:        name,
            hours:       hrs,
            theoretical: th,
            practical:   pr,
            final:       fin,
            remarks:     rem
        });
    });

    return perStudentGrades;
}

// ─── RECALC OVERALL ───────────────────────────────────────────────────────────
function recalcOverall() {
    const thVals = [], prVals = [];
    $('.grade-theoretical').each(function () { const v = parseFloat($(this).val()); if (!isNaN(v) && v > 0) thVals.push(v); });
    $('.grade-practical').each(function ()   { const v = parseFloat($(this).val()); if (!isNaN(v) && v > 0) prVals.push(v); });

    if (!thVals.length && !prVals.length) { resetOverallGrades(); return; }

    const avgTh   = thVals.length ? Math.round(thVals.reduce((a,b)=>a+b,0) / thVals.length) : 0;
    const avgPr   = prVals.length ? Math.round(prVals.reduce((a,b)=>a+b,0) / prVals.length) : 0;
    const avg     = Math.round((avgTh * 0.3) + (avgPr * 0.7));
    const remarks = avg >= 85 ? 'Competent' : 'Not Yet Competent';

    $('#theoreticalGrade').val(avgTh);
    $('#practicalGrade').val(avgPr);
    $('#averageGrade').val(avg);
    // Only update finalGrade if user hasn't manually typed a value
    if (!$('#finalGrade').is(':focus')) {
        $('#finalGrade').val(avg);
    }
    $('#remarks').val(remarks);
    $('#overallTheoretical').text(avgTh + '%');
    $('#overallPractical').text(avgPr + '%');
    $('#overallFinal').text(avg + '%');
    $('#overallRemarks').html(
        `<span class="badge ${avg >= 85 ? 'bg-success' : 'bg-danger'} small">${remarks}</span>`
    );
}

// ─── RESET OVERALL GRADES ─────────────────────────────────────────────────────
function resetOverallGrades() {
    ['theoreticalGrade','practicalGrade','averageGrade','finalGrade','remarks']
        .forEach(id => $('#' + id).val(''));
    $('#overallTheoretical, #overallPractical, #overallFinal').text('—');
    $('#overallRemarks').text('—');
}

// ─── RESET SUBJECTS TABLE ─────────────────────────────────────────────────────
function resetSubjectsTable() {
    $('#subjectsGradeBody').html('');
    $('#subjectsTableWrapper').hide();
    $('#subjectsLoadingMsg')
        .html('<i class="bi bi-info-circle me-1"></i>Select a batch or course above to load subjects')
        .show();
    $('#totalHours').text('0 hrs');
    resetOverallGrades();
}

// ─── RESET STUDENT LIST ───────────────────────────────────────────────────────
function resetStudentList() {
    $('#studentListBody').html('');
    $('#studentListWrapper').hide();
    $('#studentListCount').text(0);
    $('#selectedCount').text(0);
    $('#submitGenerateTOR').prop('disabled', true);
    $('#submitGeneratePDF').prop('disabled', true);
    $('#genSummaryFooter').text('');
    $('#selectAllStudents').prop('checked', false);
}

// ─── STATISTICS ───────────────────────────────────────────────────────────────
function loadStatistics() {
    $.ajax({
        url: 'get-tor-statistics.php',
        method: 'GET',
        dataType: 'json',
        success: function(stats) {
            $('#totalTORs').text(stats.total      || 0);
            $('#competentCount').text(stats.competent  || 0);
            $('#thisMonthCount').text(stats.this_month || 0);
            $('#downloadCount').text(stats.downloads  || 0);
        },
        error: function() {
            $('#totalTORs, #competentCount, #thisMonthCount, #downloadCount').text(0);
        }
    });
}

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
        onclick="torTable.page('previous').draw('page'); return false;">
        <i class="bi bi-chevron-left"></i></a>`;
    ul.appendChild(prev);

    const range = 2;
    for (let p = 0; p < pages; p++) {
        if (p === 0 || p === pages - 1 || (p >= cur - range && p <= cur + range)) {
            const li = document.createElement('li');
            li.className = `page-item ${p === cur ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#"
                onclick="torTable.page(${p}).draw('page'); return false;">${p + 1}</a>`;
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
        onclick="torTable.page('next').draw('page'); return false;">
        <i class="bi bi-chevron-right"></i></a>`;
    ul.appendChild(next);
}

// ─── MISC ─────────────────────────────────────────────────────────────────────
function resetTORFilters() {
    $('#searchTOR').val('');
    $('#filterRemarks').val('');
    torTable.ajax.reload();
}

function downloadTOR(id) {
    window.location.href = `download-tor-csv.php?id=${id}`;
}

function deleteTOR(id) {
    if (confirm('Are you sure you want to delete this TOR record?')) {
        $.post('delete-tor.php', { id: id }, function(response) {
            if (response.success) {
                torTable.ajax.reload();
                loadStatistics();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json').fail(function(xhr) {
            console.error('Delete error:', xhr.responseText);
            alert('Failed to delete TOR');
        });
    }
}

// ─── SHOW VALIDATION MODAL ────────────────────────────────────────────────────
function showValidation(message) {
    $('#validationMessage').text(message);
    const modal = new bootstrap.Modal(document.getElementById('validationModal'), {
        backdrop: 'static'
    });
    modal.show();
}

// ─── SHARED SUBMIT FUNCTION ───────────────────────────────────────────────────
function submitTOR(endpoint, defaultFilename, mimeType, $btn) {
    const graduation  = $('#graduationDate').val();
    const iso         = $('#isoNumber').val().trim();
    const theoretical = $('#theoreticalGrade').val();
    const practical   = $('#practicalGrade').val();
    const average     = $('#averageGrade').val();
    const finalGrade  = $('#finalGrade').val();
    const remarks     = $('#remarks').val();
    const selectedIds = getSelectedStudentIds();

    if (!selectedIds.length) {
        showValidation('Please select at least one student before generating TOR.');
        return;
    }
    if (!graduation) {
        showValidation('Please fill in the Date of Graduation.');
        return;
    }
    if (!iso) {
        showValidation('Please fill in the SO Number.');
        return;
    }
    if (!theoretical || !practical) {
        showValidation('Please enter subject grades in Step 2 before generating TOR.');
        return;
    }

    const targetFinal = parseFloat(finalGrade) || parseFloat(average) || 90;

    // ── Build per-student grades — ALWAYS randomize per student independently ─
    // Even single student gets a fresh set from the form
    const perStudentGrades = selectedIds.map(function(sid, index) {
        if (selectedIds.length === 1) {
            // Single student: use exact grades typed into the form
            return collectSubjectGrades();
        }
        // Multiple students: each gets independently randomized grades
        // buildRandomizedGrades() uses Math.random() fresh each call
        return buildRandomizedGrades(targetFinal);
    });

    // ── Quick sanity check: log first 2 students to confirm they differ ───────
    if (selectedIds.length > 1 && perStudentGrades.length > 1) {
        const s0 = perStudentGrades[0].map(g => g.final).join(',');
        const s1 = perStudentGrades[1].map(g => g.final).join(',');
        if (s0 === s1) {
            console.warn('⚠ perStudentGrades[0] and [1] are identical — randomization may have failed');
        } else {
            console.log('✓ Grades differ between students:', s0, '|', s1);
        }
    }

    const payload = {
        student_ids:           selectedIds,
        course_id:             activeCourseId,
        graduation_date:       graduation,
        so_number:             iso,
        theoretical_grade:     parseFloat(theoretical) || 0,
        practical_grade:       parseFloat(practical)   || 0,
        average_grade:         parseFloat(average)     || 0,
        final_grade:           targetFinal,
        remarks:               remarks,
        per_student_grades:    perStudentGrades,
        randomize_per_student: selectedIds.length > 1
    };

    // ── DEBUG: log what is being sent ─────────────────────────────────────────
    console.log('Sending payload:', JSON.stringify({
        student_count: payload.student_ids.length,
        per_student_grades_count: payload.per_student_grades.length,
        sample_student_0: payload.per_student_grades[0]?.slice(0,2),
        sample_student_1: payload.per_student_grades[1]?.slice(0,2)
    }, null, 2));

    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Generating...');

    const xhr = new XMLHttpRequest();
    xhr.open('POST', endpoint, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.responseType = 'blob';

    xhr.onload = function () {
        $btn.prop('disabled', false).html(origHtml);

        if (xhr.status === 200) {
            const contentType = xhr.getResponseHeader('Content-Type');

            if (contentType && contentType.includes('application/json')) {
                const reader = new FileReader();
                reader.onload = function () {
                    try {
                        const err = JSON.parse(reader.result);
                        showValidation('Server error: ' + (err.error || JSON.stringify(err)));
                    } catch (e) {
                        showValidation('Server error occurred. Please try again.');
                    }
                };
                reader.readAsText(xhr.response);
                return;
            }

            const disposition = xhr.getResponseHeader('Content-Disposition') || '';
            const match       = disposition.match(/filename="?([^"]+)"?/);
            const filename    = match ? match[1] : defaultFilename;

            const blob = new Blob([xhr.response], { type: mimeType });
            const url  = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href     = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);

            $('#generateTORModal').modal('hide');
            torTable.ajax.reload();
            loadStatistics();

        } else {
            const reader = new FileReader();
            reader.onload = function () {
                try {
                    const err = JSON.parse(reader.result);
                    showValidation('Server error: ' + (err.error || JSON.stringify(err)));
                } catch (e) {
                    showValidation('Server error occurred. Please try again.');
                }
            };
            reader.readAsText(xhr.response);
        }
    };

    xhr.onerror = function () {
        $btn.prop('disabled', false).html(origHtml);
        showValidation('Network error. Please check your connection and try again.');
    };

    xhr.send(JSON.stringify(payload));
}

// ─── BUILD RANDOMIZED GRADES (pure JS, no DOM dependency) ────────────────────
// Called once per student — generates completely independent random values
function buildRandomizedGrades(targetFinal) {
    const rows = $('#subjectsGradeBody tr[data-index]');
    if (!rows.length) return [];

    const target = parseFloat(targetFinal);
    if (isNaN(target) || target < 85 || target > 100) return [];

    const grades = [];

    // ── Determine a unique per-student grade band ─────────────────────────────
    // Instead of deriving from targetFinal, pick a random band offset per student
    // so each student gets genuinely different grades
    const bandOffset = Math.floor(Math.random() * 7) - 3; // -3 to +3 per student
    const studentTarget = Math.min(100, Math.max(85, target + bandOffset));

    rows.each(function () {
        const i    = $(this).data('index');
        const code = $(`.grade-theoretical[data-row="${i}"]`).data('code');
        const name = $(this).find('td:eq(1)').text().trim();
        const hrs  = $(this).find('td:eq(2)').text().replace(' hrs', '').trim();

        // ── Fully independent random grades per subject per student ───────────
        // Range varies per subject: some students get higher/lower per subject
        const thMin = Math.max(85, studentTarget - 5);
        const thMax = Math.min(100, studentTarget + 3);
        const th    = Math.floor(Math.random() * (thMax - thMin + 1)) + thMin;

        const prMin = Math.max(85, studentTarget - 4);
        const prMax = Math.min(100, studentTarget + 4);
        const pr    = Math.floor(Math.random() * (prMax - prMin + 1)) + prMin;

        const fin = Math.round((th * 0.3) + (pr * 0.7));
        const rem = fin >= 85 ? 'Competent' : 'Not Yet Competent';

        grades.push({
            code:        code,
            name:        name,
            hours:       hrs,
            theoretical: th,
            practical:   pr,
            final:       fin,
            remarks:     rem
        });
    });

    return grades;
}

// ─── SHARED SUBMIT FUNCTION ───────────────────────────────────────────────────
function submitTOR(endpoint, defaultFilename, mimeType, $btn) {
    const graduation  = $('#graduationDate').val();
    const iso         = $('#isoNumber').val().trim();
    const theoretical = $('#theoreticalGrade').val();
    const practical   = $('#practicalGrade').val();
    const average     = $('#averageGrade').val();
    const finalGrade  = $('#finalGrade').val();
    const remarks     = $('#remarks').val();
    const selectedIds = getSelectedStudentIds();

    if (!selectedIds.length) {
        showValidation('Please select at least one student before generating TOR.');
        return;
    }
    if (!graduation) {
        showValidation('Please fill in the Date of Graduation.');
        return;
    }
    if (!iso) {
        showValidation('Please fill in the SO Number.');
        return;
    }
    if (!theoretical || !practical) {
        showValidation('Please enter subject grades in Step 2 before generating TOR.');
        return;
    }

    const targetFinal = parseFloat(finalGrade) || parseFloat(average) || 90;

    // ── Build per-student grades — ALWAYS randomize per student independently ─
    // Even single student gets a fresh set from the form
    const perStudentGrades = selectedIds.map(function(sid, index) {
        if (selectedIds.length === 1) {
            // Single student: use exact grades typed into the form
            return collectSubjectGrades();
        }
        // Multiple students: each gets independently randomized grades
        // buildRandomizedGrades() uses Math.random() fresh each call
        return buildRandomizedGrades(targetFinal);
    });

    // ── Quick sanity check: log first 2 students to confirm they differ ───────
    if (selectedIds.length > 1 && perStudentGrades.length > 1) {
        const s0 = perStudentGrades[0].map(g => g.final).join(',');
        const s1 = perStudentGrades[1].map(g => g.final).join(',');
        if (s0 === s1) {
            console.warn('⚠ perStudentGrades[0] and [1] are identical — randomization may have failed');
        } else {
            console.log('✓ Grades differ between students:', s0, '|', s1);
        }
    }

    const payload = {
        student_ids:           selectedIds,
        course_id:             activeCourseId,
        graduation_date:       graduation,
        so_number:             iso,
        theoretical_grade:     parseFloat(theoretical) || 0,
        practical_grade:       parseFloat(practical)   || 0,
        average_grade:         parseFloat(average)     || 0,
        final_grade:           targetFinal,
        remarks:               remarks,
        per_student_grades:    perStudentGrades,
        randomize_per_student: selectedIds.length > 1
    };

    // ── DEBUG: log what is being sent ─────────────────────────────────────────
    console.log('Sending payload:', JSON.stringify({
        student_count: payload.student_ids.length,
        per_student_grades_count: payload.per_student_grades.length,
        sample_student_0: payload.per_student_grades[0]?.slice(0,2),
        sample_student_1: payload.per_student_grades[1]?.slice(0,2)
    }, null, 2));

    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Generating...');

    const xhr = new XMLHttpRequest();
    xhr.open('POST', endpoint, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.responseType = 'blob';

    xhr.onload = function () {
        $btn.prop('disabled', false).html(origHtml);

        if (xhr.status === 200) {
            const contentType = xhr.getResponseHeader('Content-Type');

            if (contentType && contentType.includes('application/json')) {
                const reader = new FileReader();
                reader.onload = function () {
                    try {
                        const err = JSON.parse(reader.result);
                        showValidation('Server error: ' + (err.error || JSON.stringify(err)));
                    } catch (e) {
                        showValidation('Server error occurred. Please try again.');
                    }
                };
                reader.readAsText(xhr.response);
                return;
            }

            const disposition = xhr.getResponseHeader('Content-Disposition') || '';
            const match       = disposition.match(/filename="?([^"]+)"?/);
            const filename    = match ? match[1] : defaultFilename;

            const blob = new Blob([xhr.response], { type: mimeType });
            const url  = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href     = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);

            $('#generateTORModal').modal('hide');
            torTable.ajax.reload();
            loadStatistics();

        } else {
            const reader = new FileReader();
            reader.onload = function () {
                try {
                    const err = JSON.parse(reader.result);
                    showValidation('Server error: ' + (err.error || JSON.stringify(err)));
                } catch (e) {
                    showValidation('Server error occurred. Please try again.');
                }
            };
            reader.readAsText(xhr.response);
        }
    };

    xhr.onerror = function () {
        $btn.prop('disabled', false).html(origHtml);
        showValidation('Network error. Please check your connection and try again.');
    };

    xhr.send(JSON.stringify(payload));
}

// ─── RESET MODAL ON CLOSE ──────────────────────────────────────────────────
$('#generateTORModal').on('hidden.bs.modal', function () {
    $('#generateTORForm')[0].reset();
    $('input[name="genMode"][value="batch"]').prop('checked', true);
    $('#panelByBatch').show();
    $('#panelByCourse, #panelByStudent').hide();
    resetStudentList();
    resetSubjectsTable();
    activeCourseId = null;
    $('#submitGenerateTOR').prop('disabled', true);
    $('#submitGeneratePDF').prop('disabled', true);
    $('#genSummaryFooter').text('');
});

</script>

<?php include('../footer/footer.php'); ?>