<?php

session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();
require_once('../../db-connect.php');

include('../header/header.php');
include('../sidebar/sidebar.php');

try {
    $stats = $pdo->query("
        SELECT
            COUNT(*)                                          AS total,
            SUM(CASE WHEN IsActive = 1 THEN 1 ELSE 0 END)   AS active,
            SUM(CASE WHEN School = 'TB5' THEN 1 ELSE 0 END) AS tb5,
            SUM(CASE WHEN School = 'BBI' THEN 1 ELSE 0 END) AS bbi
        FROM subjects
    ")->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $stats = ['total' => 0, 'active' => 0, 'tb5' => 0, 'bbi' => 0];
}
?>

<div class="content-wrapper">
    <div class="main-content">

        <!-- Page Header -->
        <div class="card border-0 shadow-sm mb-4"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fForcourw-bold text-white mb-1">
                            <i class="bi bi-journal-bookmark-fill me-2"></i>Subject Management
                        </h2>
                        <p class="text-white-50 mb-0 small">
                            Manage TESDA subjects for TB5 and Big Blossom Institute
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-light fw-semibold" onclick="showCreateModal()">
                            <i class="bi bi-plus-circle me-2"></i>Add New Subject
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-journal-bookmark text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Total Subjects</div>
                                <h3 class="mb-0 fw-bold" id="statTotal"><?= $stats['total'] ?? 0 ?></h3>
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
                                <div class="text-muted small">Active</div>
                                <h3 class="mb-0 fw-bold" id="statActive"><?= $stats['active'] ?? 0 ?></h3>
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
                                <div class="text-muted small">TB5 Subjects</div>
                                <h3 class="mb-0 fw-bold" id="statTB5"><?= $stats['tb5'] ?? 0 ?></h3>
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
                                <div class="text-muted small">BBI Subjects</div>
                                <h3 class="mb-0 fw-bold" style="color:#e0314e;" id="statBBI"><?= $stats['bbi'] ?? 0 ?></h3>
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
                        <input type="text" class="form-control" id="searchSubject"
                            placeholder="Code, name..." onkeyup="loadSubjects()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small mb-1">School</label>
                        <select class="form-select" id="filterSchool"
                            onchange="loadFilterCourses(); loadSubjects();">
                            <option value="">All Schools</option>
                            <option value="TB5">TB5</option>
                            <option value="BBI">BBI</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small mb-1">Course</label>
                        <select class="form-select" id="filterCourse" onchange="loadSubjects()">
                            <option value="">All Courses</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small mb-1">Competency</label>
                        <select class="form-select" id="filterCompetency" onchange="loadSubjects()">
                            <option value="">All</option>
                            <option value="Basic">Basic</option>
                            <option value="Common">Common</option>
                            <option value="Core">Core</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-table me-2 text-primary"></i>Subjects List
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted mb-0">Show</label>
                        <select class="form-select form-select-sm" id="entriesPerPage"
                            style="width:75px;" onchange="changeEntries()">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="small text-muted mb-0">entries</label>
                        <span class="badge bg-primary rounded-pill ms-2" id="subjectCount">0</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width:45px;">#</th>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Course</th>
                                <th>School</th>
                                <th>Competency</th>
                                <th>Hours</th>
                                <th class="text-center" style="width:120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subjectsTableBody">
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-2 text-muted small mb-0">Loading subjects...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Pagination Footer -->
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small" id="paginationInfo">
                        Showing 0 to 0 of 0 entries
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="paginationLinks">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ADD / EDIT MODAL -->
<div class="modal fade" id="subjectModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white border-0"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-journal-plus me-2"></i>
                    <span id="modalTitle">Add New Subject</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="subjectForm" autocomplete="off" novalidate>
                    <input type="hidden" id="subjectId">

                    <!-- School & Course -->
                    <div class="p-3 rounded-3 mb-4" style="background:#f0f4ff; border:1px solid #c7d2fe;">
                        <h6 class="fw-bold text-primary mb-3">
                            <span class="badge bg-primary me-2">1</span>School &amp; Course
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">
                                    School <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="modalSchool" required
                                    onchange="loadCoursesBySchool()">
                                    <option value="">-- Choose --</option>
                                    <option value="TB5">TB5</option>
                                    <option value="BBI">BBI</option>
                                </select>
                                <div class="invalid-feedback">Please select a school.</div>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label fw-semibold small">
                                    Course <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="subjectCourse" required disabled>
                                    <option value="">Select School First</option>
                                </select>
                                <div class="invalid-feedback">Please select a course.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Competency & Count (only for Add mode) -->
                    <div class="p-3 rounded-3 mb-4 d-none" id="competencyCountSection"
                        style="background:#fff7ed; border:1px solid #fed7aa;">
                        <h6 class="fw-bold text-warning mb-3">
                            <span class="badge bg-warning text-dark me-2">2</span>
                            Competency &amp; Subject Count
                        </h6>

                        <!-- BASIC -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="badge bg-info fs-6 px-3 py-2">
                                    <i class="bi bi-1-circle me-1"></i>Basic Competencies
                                </span>
                                <div class="input-group" style="width:140px;">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="changeCompCount('basic', -1)">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="form-control form-control-sm text-center fw-bold"
                                        id="countBasic" value="0" min="0" max="20"
                                        oninput="renderCompRows('basic')">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="changeCompCount('basic', 1)">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <small class="text-muted">subjects</small>
                            </div>
                            <div id="basicRowsWrapper" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle mb-0">
                                        <thead class="table-info">
                                            <tr>
                                                <th style="width:40px;" class="text-center">#</th>
                                                <th style="width:180px;">Subject Code</th>
                                                <th>Subject Name</th>
                                                <th style="width:130px;">Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody id="basicRowsBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- COMMON -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                    <i class="bi bi-2-circle me-1"></i>Common Competencies
                                </span>
                                <div class="input-group" style="width:140px;">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="changeCompCount('common', -1)">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="form-control form-control-sm text-center fw-bold"
                                        id="countCommon" value="0" min="0" max="20"
                                        oninput="renderCompRows('common')">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="changeCompCount('common', 1)">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <small class="text-muted">subjects</small>
                            </div>
                            <div id="commonRowsWrapper" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle mb-0">
                                        <thead class="table-warning">
                                            <tr>
                                                <th style="width:40px;" class="text-center">#</th>
                                                <th style="width:180px;">Subject Code</th>
                                                <th>Subject Name</th>
                                                <th style="width:130px;">Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody id="commonRowsBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- CORE -->
                        <div class="mb-2">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="badge bg-danger fs-6 px-3 py-2">
                                    <i class="bi bi-3-circle me-1"></i>Core Competencies
                                </span>
                                <div class="input-group" style="width:140px;">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="changeCompCount('core', -1)">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="form-control form-control-sm text-center fw-bold"
                                        id="countCore" value="0" min="0" max="20"
                                        oninput="renderCompRows('core')">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="changeCompCount('core', 1)">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <small class="text-muted">subjects</small>
                            </div>
                            <div id="coreRowsWrapper" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm align-middle mb-0">
                                        <thead class="table-danger">
                                            <tr>
                                                <th style="width:40px;" class="text-center">#</th>
                                                <th style="width:180px;">Subject Code</th>
                                                <th>Subject Name</th>
                                                <th style="width:130px;">Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody id="coreRowsBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mb-0 py-2 small mt-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Set the count per competency above. Rows appear automatically.
                        </div>
                    </div>

                    <!-- Remove old multiSubjectSection entirely -->

                    <!-- Single Subject Info (Edit mode) -->
                    <div id="singleSubjectSection" class="d-none">
                        <div class="p-3 rounded-3 mb-4" style="background:#f0fff4; border:1px solid #bbf7d0;">
                            <h6 class="fw-bold text-success mb-3">
                                <span class="badge bg-success me-2">2</span>Subject Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">
                                        Subject Code <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control font-monospace fw-bold"
                                        id="subjectCode" placeholder="e.g. CSS-BC1" maxlength="50"
                                        oninput="this.value=this.value.toUpperCase()">
                                    <div class="invalid-feedback">Required.</div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold small">
                                        Subject Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="subjectName"
                                        placeholder="e.g. Participate in Workplace Communication"
                                        maxlength="255">
                                    <div class="invalid-feedback">Required.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">
                                        Competency <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="subjectCompetency">
                                        <option value="">Select...</option>
                                        <option value="Basic">Basic</option>
                                        <option value="Common">Common</option>
                                        <option value="Core">Core</option>
                                    </select>
                                    <div class="invalid-feedback">Required.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">
                                        Hours <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="subjectHours"
                                            placeholder="e.g. 20" min="1" max="9999">
                                        <span class="input-group-text">hrs</span>
                                    </div>
                                    <div class="invalid-feedback">Required.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Multi-Row Section (Add mode) -->
                    <div id="multiSubjectSection" class="d-none">
                        <div class="p-3 rounded-3 mb-2" style="background:#f0fff4; border:1px solid #bbf7d0;">
                            <h6 class="fw-bold text-success mb-3">
                                <span class="badge bg-success me-2">3</span>
                                Subject Details
                                <span class="badge bg-success ms-2" id="competencyBadgeLabel"></span>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-success">
                                        <tr>
                                            <th style="width:45px;" class="text-center">#</th>
                                            <th style="width:200px;">Subject Code</th>
                                            <th>Subject Name</th>
                                            <th style="width:150px;">Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody id="multiSubjectBody">
                                        <!-- Rows injected dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary px-4" id="saveBtn"
                    onclick="saveSubject()">
                    <i class="bi bi-check-circle me-1"></i>Save Subject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white border-0"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-eye me-2"></i>Subject Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="viewModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
                <button type="button" class="btn btn-success" id="viewEditBtn">
                    <i class="bi bi-pencil me-1"></i>Edit This Subject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-trash me-2"></i>Delete Subject
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle text-danger" style="font-size:3rem;"></i>
                <p class="mt-3 fw-semibold mb-1">Are you sure?</p>
                <p class="text-muted small mb-0">
                    Deleting <strong class="text-danger" id="deleteSubjectLabel"></strong>
                    cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100">
    <div id="toastMsg" class="toast align-items-center text-white border-0" role="alert">
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
</style>



<script>
let deleteTargetId   = null;
let viewingSubjectId = null;
let isEditMode       = false;
let allSubjectsData  = [];
let currentPage      = 1;

$(document).ready(function () {
    loadSubjects();
    loadFilterCourses();

    $('#subjectStatus').on('change', function () {
        $('#statusLabel')
            .text(this.checked ? 'Active' : 'Inactive')
            .removeClass('text-success text-danger')
            .addClass(this.checked ? 'text-success' : 'text-danger');
    });

    $('#confirmDeleteBtn').on('click', function () {
        if (!deleteTargetId) return;
        const $btn = $(this);
        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-1"></span>Deleting...');

        $.ajax({
            url: 'delete-subject.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: deleteTargetId }),
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false)
                    .html('<i class="bi bi-trash me-1"></i>Delete');
                bootstrap.Modal.getInstance(
                    document.getElementById('deleteModal')).hide();
                deleteTargetId = null;
                showToast(res.success ? 'Subject deleted.' : 'Error: ' + res.message,
                    res.success ? 'success' : 'danger');
                if (res.success) loadSubjects();
            },
            error: function () {
                $btn.prop('disabled', false)
                    .html('<i class="bi bi-trash me-1"></i>Delete');
                showToast('Failed to delete. Try again.', 'danger');
            }
        });
    });

    $('#viewEditBtn').on('click', function () {
        bootstrap.Modal.getInstance(document.getElementById('viewModal')).hide();
        setTimeout(() => editSubject(viewingSubjectId), 400);
    });
});

// ─── LOAD FILTER COURSES ──────────────────────────────────────────────────────
function loadFilterCourses() {
    const school = $('#filterSchool').val();
    $('#filterCourse').html('<option value="">All Courses</option>');

    $.ajax({
        url: 'get-courses-by-school.php',
        method: 'GET',
        data: { school },
        dataType: 'json',
        success: function (res) {
            if (!res.success || !res.courses.length) return;
            res.courses.forEach(c => {
                $('#filterCourse').append(
                    `<option value="${c.Id}">${escHtml(c.CourseCode)} — ${escHtml(c.CourseName)}</option>`
                );
            });
        }
    });
}

// ─── LOAD SUBJECTS ────────────────────────────────────────────────────────────
function loadSubjects() {
    currentPage = 1;
    $('#subjectsTableBody').html(`
        <tr><td colspan="8" class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2 text-muted small mb-0">Loading...</p>
        </td></tr>`);

    $.ajax({
        url: 'get-subjects.php',
        method: 'POST',
        data: {
            search:     $('#searchSubject').val().trim(),
            school:     $('#filterSchool').val(),
            courseId:   $('#filterCourse').val(),
            competency: $('#filterCompetency').val()
        },
        dataType: 'json',
        success: function (res) {
            if (!res.success) {
                $('#subjectsTableBody').html(`
                    <tr><td colspan="8" class="text-center py-4 text-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>${escHtml(res.message)}
                    </td></tr>`);
                return;
            }
            allSubjectsData = res.subjects;
            $('#statTotal').text(res.stats.total   || 0);
            $('#statActive').text(res.stats.active || 0);
            $('#statTB5').text(res.stats.tb5       || 0);
            $('#statBBI').text(res.stats.bbi       || 0);
            $('#subjectCount').text(res.subjects.length + ' subject(s)');
            renderPage();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            $('#subjectsTableBody').html(`
                <tr><td colspan="8" class="text-center py-4 text-danger">
                    <i class="bi bi-wifi-off me-1"></i>Failed to load subjects.
                </td></tr>`);
        }
    });
}

// ─── CHANGE ENTRIES ───────────────────────────────────────────────────────────
function changeEntries() {
    currentPage = 1;
    renderPage();
}

// ─── RENDER PAGE ──────────────────────────────────────────────────────────────
function renderPage() {
    const perPage  = parseInt($('#entriesPerPage').val()) || 10;
    const total    = allSubjectsData.length;
    const pages    = Math.ceil(total / perPage) || 1;

    if (currentPage < 1)     currentPage = 1;
    if (currentPage > pages) currentPage = pages;

    const start    = (currentPage - 1) * perPage;
    const end      = Math.min(start + perPage, total);
    const pageData = allSubjectsData.slice(start, end);

    renderTable(pageData, start);

    $('#paginationInfo').text(
        total === 0
            ? 'Showing 0 to 0 of 0 entries'
            : `Showing ${start + 1} to ${end} of ${total} entries`
    );

    renderPagination(pages);
}

// ─── RENDER PAGINATION ────────────────────────────────────────────────────────
function renderPagination(pages) {
    const ul = document.getElementById('paginationLinks');
    ul.innerHTML = '';
    if (pages <= 1) return;

    const prev = document.createElement('li');
    prev.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prev.innerHTML = `<a class="page-link" href="#"
        onclick="goToPage(${currentPage - 1}); return false;">
        <i class="bi bi-chevron-left"></i></a>`;
    ul.appendChild(prev);

    const range = 2;
    for (let p = 1; p <= pages; p++) {
        if (p === 1 || p === pages ||
            (p >= currentPage - range && p <= currentPage + range)) {
            const li = document.createElement('li');
            li.className = `page-item ${p === currentPage ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#"
                onclick="goToPage(${p}); return false;">${p}</a>`;
            ul.appendChild(li);
        } else if (p === currentPage - range - 1 || p === currentPage + range + 1) {
            const li = document.createElement('li');
            li.className = 'page-item disabled';
            li.innerHTML = `<span class="page-link">…</span>`;
            ul.appendChild(li);
        }
    }

    const next = document.createElement('li');
    next.className = `page-item ${currentPage === pages ? 'disabled' : ''}`;
    next.innerHTML = `<a class="page-link" href="#"
        onclick="goToPage(${currentPage + 1}); return false;">
        <i class="bi bi-chevron-right"></i></a>`;
    ul.appendChild(next);
}

// ─── GO TO PAGE ───────────────────────────────────────────────────────────────
function goToPage(page) {
    currentPage = page;
    renderPage();
    document.getElementById('subjectsTableBody')
        .closest('.card')
        .scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ─── RENDER TABLE ─────────────────────────────────────────────────────────────
function renderTable(subjects, offset = 0) {
    const tbody = document.getElementById('subjectsTableBody');

    if (!subjects || subjects.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x" style="font-size:3rem;"></i>
                    <p class="mt-2 mb-0 fw-semibold">No subjects found</p>
                    <small>Try adjusting your filters or add a new subject.</small>
                </td>
            </tr>`;
        return;
    }

    const compClass = {
        'Basic':  'bg-info text-white',
        'Common': 'bg-warning text-dark',
        'Core':   'bg-danger text-white'
    };

    let html = '';
    subjects.forEach((s, i) => {
        const schoolBadge = s.School === 'TB5'
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

        html += `
        <tr>
            <td class="ps-3 text-muted small">${offset + i + 1}</td>
            <td>
                <span class="badge bg-dark font-monospace px-2 py-1" style="font-size:.8rem;">
                    ${escHtml(s.SubjectCode)}
                </span>
            </td>
            <td>
                <div class="fw-semibold small">${escHtml(s.SubjectName)}</div>
            </td>
            <td>
                <div class="small fw-semibold">${escHtml(s.CourseName || 'N/A')}</div>
                <small class="text-muted font-monospace">${escHtml(s.CourseCode || '')}</small>
            </td>
            <td>${schoolBadge}</td>
            <td>
                <span class="badge rounded-pill ${compClass[s.Competency] || 'bg-secondary'}">
                    ${escHtml(s.Competency || 'N/A')}
                </span>
            </td>
            <td class="small">
                <span class="fw-semibold">${s.Hours || 'N/A'} hrs</span>
                ${s.Days ? `<div class="text-muted" style="font-size:.7rem;">${s.Days} days</div>` : ''}
            </td>
            <td class="text-center">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-info" title="View"
                        onclick="viewSubject(${s.Id})">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" title="Edit"
                        onclick="editSubject(${s.Id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger" title="Delete"
                        onclick="openDeleteModal(${s.Id}, '${escJs(s.SubjectCode)} — ${escJs(s.SubjectName)}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    });

    tbody.innerHTML = html;
}

// ─── VIEW SUBJECT ─────────────────────────────────────────────────────────────
function viewSubject(id) {
    viewingSubjectId = id;

    $('#viewModalBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2 text-muted small">Loading...</p>
        </div>`);

    new bootstrap.Modal(document.getElementById('viewModal')).show();

    $.ajax({
        url: 'get-subject-details.php',
        method: 'GET',
        data: { id: id },
        dataType: 'json',
        success: function (res) {
            if (!res.success) {
                $('#viewModalBody').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>${escHtml(res.message)}
                    </div>`);
                return;
            }

            const s = res.subject;
            const compClass = {
                'Basic':  'bg-info text-white',
                'Common': 'bg-warning text-dark',
                'Core':   'bg-danger text-white'
            };
            const schoolImg = s.School === 'TB5'
                ? `<img src="../assets/img/tb5-logo.png"
                        style="width:28px;height:28px;border-radius:50%;object-fit:cover;"> TB5`
                : `<img src="../assets/img/bbi-logo.png"
                        style="width:28px;height:28px;border-radius:50%;object-fit:cover;"> BBI`;

            $('#viewModalBody').html(`
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Subject Code</div>
                            <div class="fw-bold font-monospace fs-5">${escHtml(s.SubjectCode)}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">School</div>
                            <div class="fw-bold d-flex align-items-center gap-2">${schoolImg}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3 border">
                            <div class="text-muted small mb-1">Subject Name</div>
                            <div class="fw-bold">${escHtml(s.SubjectName)}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Course</div>
                            <div class="fw-bold">${escHtml(s.CourseName || 'N/A')}</div>
                            <small class="text-muted font-monospace">${escHtml(s.CourseCode || '')}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Competency</div>
                            <span class="badge rounded-pill ${compClass[s.Competency] || 'bg-secondary'} px-3 py-2">
                                ${escHtml(s.Competency || 'N/A')}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Hours</div>
                            <div class="fw-bold fs-5">
                                ${escHtml(String(s.Hours || 'N/A'))}
                                <small class="text-muted fs-6">hrs</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Status</div>
                            <span class="badge ${s.IsActive == 1 ? 'bg-success' : 'bg-secondary'} px-3 py-2">
                                <i class="bi bi-${s.IsActive == 1 ? 'check-circle' : 'x-circle'} me-1"></i>
                                ${s.IsActive == 1 ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>
                </div>`);
        },
        error: function () {
            $('#viewModalBody').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-wifi-off me-1"></i>Failed to load subject details.
                </div>`);
        }
    });
}

// ─── OPEN DELETE MODAL ────────────────────────────────────────────────────────
function openDeleteModal(id, label) {
    deleteTargetId = id;
    $('#deleteSubjectLabel').text(label);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// ─── SHOW CREATE MODAL ────────────────────────────────────────────────────────
function showCreateModal() {
    isEditMode = false;
    $('#modalTitle').text('Add New Subject');
    document.getElementById('subjectForm').reset();
    document.getElementById('subjectForm').classList.remove('was-validated');
    $('#subjectId').val('');

    ['basic', 'common', 'core'].forEach(sec => {
        document.getElementById('count' + capitalize(sec)).value = 0;
        document.getElementById(sec + 'RowsBody').innerHTML = '';
        document.getElementById(sec + 'RowsWrapper').classList.add('d-none');
    });

    $('#modalSchool').val('');
    $('#subjectCourse').prop('disabled', true)
        .html('<option value="">Select School First</option>');

    $('#competencyCountSection').removeClass('d-none');
    $('#singleSubjectSection').addClass('d-none');

    new bootstrap.Modal(document.getElementById('subjectModal')).show();
}

// ─── LOAD COURSES BY SCHOOL ───────────────────────────────────────────────────
function loadCoursesBySchool(preSelectCourseId = null) {
    const school = $('#modalSchool').val();

    if (!school) {
        $('#subjectCourse').prop('disabled', true)
            .html('<option value="">Select School First</option>');
        return;
    }

    $('#subjectCourse').prop('disabled', true)
        .html('<option value="">Loading...</option>');

    $.ajax({
        url: 'get-courses-by-school.php',
        method: 'GET',
        data: { school },
        dataType: 'json',
        success: function (res) {
            if (!res.success || res.courses.length === 0) {
                $('#subjectCourse').prop('disabled', true)
                    .html('<option value="">No courses found for ' + school + '</option>');
                return;
            }

            let options = '<option value="">-- Select Course --</option>';
            res.courses.forEach(c => {
                const selected = preSelectCourseId && c.Id == preSelectCourseId ? 'selected' : '';
                options += `<option value="${c.Id}" ${selected}>
                    ${escHtml(c.CourseCode)} — ${escHtml(c.CourseName)}
                </option>`;
            });

            $('#subjectCourse').prop('disabled', false).html(options);
        },
        error: function () {
            $('#subjectCourse').prop('disabled', true)
                .html('<option value="">Failed to load courses</option>');
            showToast('Could not load courses. Try again.', 'danger');
        }
    });
}

// ─── EDIT SUBJECT ─────────────────────────────────────────────────────────────
function editSubject(id) {
    isEditMode = true;
    $.ajax({
        url: 'get-subject-details.php',
        method: 'GET',
        data: { id },
        dataType: 'json',
        success: function (res) {
            if (!res.success) { showToast('Error: ' + res.message, 'danger'); return; }
            const s = res.subject;

            $('#modalTitle').text('Edit Subject');
            document.getElementById('subjectForm').classList.remove('was-validated');

            $('#competencyCountSection').addClass('d-none');
            $('#multiSubjectSection').addClass('d-none');
            $('#singleSubjectSection').removeClass('d-none');

            $('#subjectId').val(s.Id);
            $('#modalSchool').val(s.School);
            $('#subjectCode').val(s.SubjectCode);
            $('#subjectName').val(s.SubjectName);
            $('#subjectType').val(s.SubjectType);
            $('#subjectCompetency').val(s.Competency);
            $('#subjectHours').val(s.Hours || '');

            loadCoursesBySchool(s.CourseId);
            new bootstrap.Modal(document.getElementById('subjectModal')).show();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            showToast('Failed to load subject data.', 'danger');
        }
    });
}

// ─── CHANGE COMP COUNT ────────────────────────────────────────────────────────
function changeCompCount(section, delta) {
    const input = document.getElementById('count' + capitalize(section));
    let val = parseInt(input.value) || 0;
    val = Math.min(20, Math.max(0, val + delta));
    input.value = val;
    renderCompRows(section);
}

// ─── RENDER COMP ROWS ─────────────────────────────────────────────────────────
function renderCompRows(section) {
    const input   = document.getElementById('count' + capitalize(section));
    const count   = Math.min(20, Math.max(0, parseInt(input.value) || 0));
    const wrapper = document.getElementById(section + 'RowsWrapper');
    const tbody   = document.getElementById(section + 'RowsBody');

    input.value = count;

    if (count === 0) {
        wrapper.classList.add('d-none');
        tbody.innerHTML = '';
        return;
    }

    wrapper.classList.remove('d-none');

    const existing = [];
    tbody.querySelectorAll('tr').forEach(tr => {
        existing.push({
            code:  tr.querySelector('.ms-code')?.value  || '',
            name:  tr.querySelector('.ms-name')?.value  || '',
            hours: tr.querySelector('.ms-hours')?.value || ''
        });
    });

    tbody.innerHTML = '';
    for (let i = 0; i < count; i++) {
        const prev = existing[i] || {};
        const tr   = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center text-muted fw-semibold small">${i + 1}</td>
            <td>
                <input type="text"
                    class="form-control form-control-sm font-monospace ms-code"
                    placeholder="e.g. ${section.toUpperCase().substring(0,3)}-${i + 1}"
                    maxlength="50"
                    value="${escAttr(prev.code)}"
                    oninput="this.value=this.value.toUpperCase()">
            </td>
            <td>
                <input type="text"
                    class="form-control form-control-sm ms-name"
                    placeholder="Subject name..."
                    maxlength="255"
                    value="${escAttr(prev.name)}">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control ms-hours"
                        placeholder="hrs" min="1" max="9999"
                        value="${escAttr(prev.hours)}">
                    <span class="input-group-text">hrs</span>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    }
}

// ─── SAVE SUBJECT ─────────────────────────────────────────────────────────────
function saveSubject() {
    const courseId = $('#subjectCourse').val();
    const school   = $('#modalSchool').val();

    if (!school)   { showToast('Please select a school.', 'danger'); return; }
    if (!courseId) { showToast('Please select a course.', 'danger'); return; }

    const $btn     = $('#saveBtn');
    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

    if (isEditMode) {
        const id          = parseInt($('#subjectId').val()) || 0;
        const subjectCode = $('#subjectCode').val().trim().toUpperCase();
        const subjectName = $('#subjectName').val().trim();
        const competency  = $('#subjectCompetency').val();
        const hours       = parseInt($('#subjectHours').val()) || 0;

        if (!subjectCode || !subjectName || !competency || hours <= 0) {
            $btn.prop('disabled', false).html(origHtml);
            showToast('Please fill in all required fields.', 'danger');
            return;
        }

        $.ajax({
            url: 'save-subject.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id, school, courseId, subjectCode, subjectName, competency, hours, isActive: 1 }),
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res.success) {
                    bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();
                    showToast('Subject updated successfully.', 'success');
                    loadSubjects();
                } else {
                    showToast('⚠ ' + res.message, 'danger');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(origHtml);
                showToast('Server error. Please try again.', 'danger');
            }
        });

    } else {
        const competencyMap = { basic: 'Basic', common: 'Common', core: 'Core' };
        const allSubjects   = [];
        let hasError        = false;

        ['basic', 'common', 'core'].forEach(sec => {
            const rows = document.getElementById(sec + 'RowsBody').querySelectorAll('tr');
            rows.forEach((tr, i) => {
                const code  = tr.querySelector('.ms-code').value.trim().toUpperCase();
                const name  = tr.querySelector('.ms-name').value.trim();
                const hours = parseInt(tr.querySelector('.ms-hours').value) || 0;

                if (!code || !name || hours <= 0) {
                    showToast(`${capitalize(sec)} row ${i + 1}: Fill in all fields.`, 'danger');
                    hasError = true;
                    return;
                }
                allSubjects.push({ code, name, hours, competency: competencyMap[sec] });
            });
        });

        if (hasError) { $btn.prop('disabled', false).html(origHtml); return; }

        if (allSubjects.length === 0) {
            $btn.prop('disabled', false).html(origHtml);
            showToast('Please add at least one subject row.', 'danger');
            return;
        }

        $.ajax({
            url: 'save-subjects-multi.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ school, courseId, subjects: allSubjects }),
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res.success) {
                    bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();
                    showToast(`${allSubjects.length} subject(s) added successfully.`, 'success');
                    loadSubjects();
                } else {
                    showToast('⚠ ' + res.message, 'danger');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html(origHtml);
                showToast('Server error. Please try again.', 'danger');
            }
        });
    }
}

// ─── RESET FILTERS ────────────────────────────────────────────────────────────
function resetFilters() {
    $('#searchSubject').val('');
    $('#filterSchool').val('');
    $('#filterCompetency').val('');
    $('#filterStatus').val('');
    $('#filterCourse').html('<option value="">All Courses</option>');
    loadFilterCourses();
    loadSubjects();
}

// ─── TOAST ────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const el = document.getElementById('toastMsg');
    el.className = `toast align-items-center text-white border-0 bg-${type}`;
    document.getElementById('toastText').textContent = msg;
    bootstrap.Toast.getOrCreateInstance(el, { delay: 3500 }).show();
}

// ─── HELPERS ──────────────────────────────────────────────────────────────────
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function escHtml(text) {
    if (!text) return '';
    const d = document.createElement('div');
    d.textContent = String(text);
    return d.innerHTML;
}

function escJs(text) {
    if (!text) return '';
    return String(text).replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function escAttr(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function fmtDate(str) {
    if (!str) return 'N/A';
    return new Date(str).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}
</script>

<?php include('../footer/footer.php'); ?>

