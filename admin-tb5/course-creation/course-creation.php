<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\course-creation\course-creation.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');
checkAdmin();

// Include header
include('../header/header.php');
include('../sidebar/sidebar.php');

// Get statistics
try {
    $statsQuery = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN IsActive = 1 THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN School = 'TB5' THEN 1 ELSE 0 END) as tb5,
            SUM(CASE WHEN School = 'BBI' THEN 1 ELSE 0 END) as bbi
        FROM courses
    ");
    $stats = $statsQuery->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Stats error: " . $e->getMessage());
    $stats = ['total' => 0, 'active' => 0, 'tb5' => 0, 'bbi' => 0];
}
?>

<div class="content-wrapper">
    <div class="main-content">
        <!-- Page Title Card -->
        <div class="card border-0 shadow-sm mb-4" style="background: #4169E1;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold text-white mb-2">
                            <i class="bi bi-book me-2"></i>Course Management
                        </h2>
                        <p class="text-white-50 mb-0">
                            Create and manage courses for TB5 and Big Blossom Institute
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-light" onclick="showCreateCourseModal()">
                            <i class="bi bi-plus-circle me-2"></i>Add New Course
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
                                <i class="bi bi-book text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Total Courses</div>
                                <h3 class="mb-0 fw-bold" id="statTotal"><?php echo $stats['total']; ?></h3>
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
                                <div class="text-muted small">Active Courses</div>
                                <h3 class="mb-0 fw-bold" id="statActive"><?php echo $stats['active']; ?></h3>
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
                                <div class="text-muted small">TB5 Courses</div>
                                <h3 class="mb-0 fw-bold" id="statTB5"><?php echo $stats['tb5']; ?></h3>
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
                                <div class="text-muted small">BBI Courses</div>
                                <h3 class="mb-0 fw-bold" style="color:#e0314e;" id="statBBI"><?php echo $stats['bbi']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small">Search Course</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Course name or code..." id="searchCourse" onkeyup="loadCourses()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Filter by Status</label>
                        <select class="form-select" id="filterStatus" onchange="loadCourses()">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">&nbsp;</label>
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses by School Tabs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <ul class="nav nav-tabs card-header-tabs mb-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-courses-tab"
                                data-bs-toggle="tab" data-bs-target="#all-courses"
                                type="button" role="tab" onclick="setTabFilter('')">
                                <i class="bi bi-list-ul me-1"></i>All Courses
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center gap-2" id="tb5-courses-tab"
                                data-bs-toggle="tab" data-bs-target="#tb5-courses"
                                type="button" role="tab" onclick="setTabFilter('TB5')">
                                <img src="../assets/img/tb5-logo.png" alt="TB5"
                                    style="width:22px;height:22px;object-fit:cover;border-radius:50%;">
                                TB5 Courses
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center gap-2" id="bbi-courses-tab"
                                data-bs-toggle="tab" data-bs-target="#bbi-courses"
                                type="button" role="tab" onclick="setTabFilter('BBI')">
                                <img src="../assets/img/bbi-logo.png" alt="BBI"
                                    style="width:22px;height:22px;object-fit:cover;border-radius:50%;">
                                BBI Courses
                            </button>
                        </li>
                    </ul>
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
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="tab-content">
                    <!-- All Courses Tab -->
                    <div class="tab-pane fade show active" id="all-courses" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3" style="width:45px;">#</th>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>School</th>
                                        <th>Category</th>
                                        <th>Total Hours</th>
                                        <th>Tuition</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width:120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="coursesTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status"></div>
                                            <p class="mt-2 text-muted small mb-0">Loading courses...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TB5 Courses Tab -->
                    <div class="tab-pane fade" id="tb5-courses" role="tabpanel">
                        <div class="alert alert-info d-flex align-items-center gap-2 border-0 rounded-0 mb-0">
                            <img src="../assets/img/tb5-logo.png" alt="TB5"
                                style="width:32px;height:32px;object-fit:cover;border-radius:50%;">
                            Showing courses for <strong>The Big Five Training and Assessment Center</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3" style="width:45px;">#</th>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Category</th>
                                        <th>Total Hours</th>
                                        <th>Tuition</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width:120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tb5TableBody">
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <p class="text-muted small mb-0">Loading...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- BBI Courses Tab -->
                    <div class="tab-pane fade" id="bbi-courses" role="tabpanel">
                        <div class="alert d-flex align-items-center gap-2 border-0 rounded-0 mb-0"
                            style="background-color:#fde8ec; color:#7a0a1e;">
                            <img src="../assets/img/bbi-logo.png" alt="BBI"
                                style="width:32px;height:32px;object-fit:cover;border-radius:50%;">
                            Showing courses for <strong>Big Blossom Institute Inc.</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3" style="width:45px;">#</th>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Category</th>
                                        <th>Total Hours</th>
                                        <th>Tuition</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width:120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="bbiTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <p class="text-muted small mb-0">Loading...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination Footer -->
            <div class="card-footer bg-white border-top py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small" id="paginationInfo">
                        Showing 0 to 0 of 0 entries
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Course Modal -->
<div class="modal fade" id="courseModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: #4169E1;">
                <h5 class="modal-title text-white">
                    <i class="bi bi-plus-circle me-2"></i><span id="modalTitle">Add New Course</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="courseForm">
                    <input type="hidden" id="courseId">
                    
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Basic Information -->
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-info-circle me-2"></i>Basic Information
                            </h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="courseCode" placeholder="e.g., CSS-NC2" required>
                                <div class="form-text">Unique identifier for the course</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="courseName" placeholder="e.g., Caregiving NC II" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" id="courseDescription" rows="4" placeholder="Enter course description..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Prerequisites</label>
                                <textarea class="form-control" id="prerequisites" rows="3" placeholder="e.g., High School Graduate or ALS Passer"></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-gear me-2"></i>Course Details
                            </h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">School <span class="text-danger">*</span></label>
                                    <select class="form-select" id="courseSchool" required>
                                        <option value="">Choose School...</option>
                                        <option value="TB5">TB5</option>
                                        <option value="BBI">Big Blossom Institute</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category" required>
                                        <option value="NC I">NC I</option>
                                        <option value="NC II" selected>NC II</option>
                                        <option value="NC III">NC III</option>
                                        <option value="NC IV">NC IV</option>
                                        <option value="Certificate">Certificate</option>
                                        <option value="Diploma">Diploma</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Total Hours <span class="text-danger">*</span></label>
        <input type="number" class="form-control" id="courseDuration" placeholder="e.g., 216" required oninput="calculateTotalDays()">
        <div class="form-text">Enter total training hours</div>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Total Days</label>
        <input type="number" class="form-control" id="durationHours" placeholder="Auto-calculated" readonly>
        <div class="form-text">Calculated at 8 hours/day</div>
    </div>
</div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tuition Fee (₱)</label>
                                    <input type="number" step="0.01" class="form-control" id="tuition" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Max Students</label>
                                    <input type="number" class="form-control" id="maxStudents" placeholder="e.g., 45">
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <h6 class="fw-bold mb-3 mt-4">
                                <i class="bi bi-toggle-on me-2"></i>Status
                            </h6>
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="courseStatus" checked>
                                        <label class="form-check-label fw-semibold" for="courseStatus">
                                            Course is Active and Available for Enrollment
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="saveCourse()">
                    <i class="bi bi-check-circle me-1"></i>Save Course
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Course Details Modal -->
<div class="modal fade" id="viewCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Course Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="courseDetailsBody">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ── DELETE CONFIRMATION MODAL ─────────────────────────────────────────── -->
<div class="modal fade" id="deleteCourseModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-trash me-2"></i>Delete Course
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle text-danger" style="font-size:3rem;"></i>
                <p class="mt-3 fw-semibold mb-1">Are you sure?</p>
                <p class="text-muted small mb-0">
                    Deleting <strong class="text-danger" id="deleteCourseLabel"></strong>
                    cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4"
                    data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteCourseBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── TOAST ──────────────────────────────────────────────────────────────── -->
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
.nav-tabs .nav-link {
    color: #333;
    border: none;
    transition: all 0.3s ease;
}
.nav-tabs .nav-link:hover {
    color: #4169E1;
    background-color: rgba(65, 105, 225, 0.1);
}
.nav-tabs .nav-link.active {
    background-color: #4169E1 !important;
    color: white !important;
    border: none;
    border-bottom: 3px solid #2948b8;
}
.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}
</style>

<script>
let currentTabFilter     = '';
let allCoursesData       = [];
let currentPage          = 1;
let deleteCourseTargetId = null;

$(document).ready(function () {
    loadCourses();

    $('#confirmDeleteCourseBtn').on('click', function () {
        if (!deleteCourseTargetId) return;
        const $btn = $(this);
        $btn.prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-1"></span>Deleting...');

        $.ajax({
            url: 'delete-course.php',
            method: 'POST',
            data: JSON.stringify({ id: deleteCourseTargetId }),
            contentType: 'application/json',
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false)
                    .html('<i class="bi bi-trash me-1"></i>Delete');
                bootstrap.Modal.getInstance(
                    document.getElementById('deleteCourseModal')).hide();
                deleteCourseTargetId = null;
                showToast(
                    res.success ? 'Course deleted successfully.' : 'Error: ' + res.message,
                    res.success ? 'success' : 'danger'
                );
                if (res.success) loadCourses();
            },
            error: function () {
                $btn.prop('disabled', false)
                    .html('<i class="bi bi-trash me-1"></i>Delete');
                showToast('Failed to delete course. Try again.', 'danger');
            }
        });
    });
});

// ─── SET TAB FILTER ───────────────────────────────────────────────────────────
function setTabFilter(school) {
    currentTabFilter = school;
    currentPage      = 1;
    loadCourses();
}

// ─── CHANGE ENTRIES ───────────────────────────────────────────────────────────
function changeEntries() {
    currentPage = 1;
    renderPage();
}

// ─── RESET FILTERS ────────────────────────────────────────────────────────────
function resetFilters() {
    $('#searchCourse').val('');
    $('#filterStatus').val('');
    currentTabFilter = '';
    currentPage      = 1;
    $('#all-courses-tab').tab('show');
    loadCourses();
}

// ─── LOAD COURSES ─────────────────────────────────────────────────────────────
function loadCourses() {
    currentPage = 1;

    $('#coursesTableBody, #tb5TableBody, #bbiTableBody').html(`
        <tr><td colspan="9" class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2 text-muted small mb-0">Loading...</p>
        </td></tr>`);

    $.ajax({
        url: 'get-courses.php',
        method: 'POST',
        data: {
            search: $('#searchCourse').val(),
            school: currentTabFilter,
            status: $('#filterStatus').val()
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                allCoursesData = response.courses;
                $('#statTotal').text(response.stats.total  || 0);
                $('#statActive').text(response.stats.active || 0);
                $('#statTB5').text(response.stats.tb5      || 0);
                $('#statBBI').text(response.stats.bbi      || 0);
                renderPage();
            } else {
                const err = `<tr><td colspan="9" class="text-center py-4 text-danger">
                    <i class="bi bi-exclamation-circle me-1"></i>${escapeHtml(response.message)}
                </td></tr>`;
                $('#coursesTableBody, #tb5TableBody, #bbiTableBody').html(err);
            }
        },
        error: function (xhr) {
            console.error('Error:', xhr.responseText);
            const err = `<tr><td colspan="9" class="text-center py-4 text-danger">
                <i class="bi bi-wifi-off me-1"></i>Failed to load courses.
            </td></tr>`;
            $('#coursesTableBody, #tb5TableBody, #bbiTableBody').html(err);
        }
    });
}

// ─── RENDER PAGE ──────────────────────────────────────────────────────────────
function renderPage() {
    const perPage = parseInt($('#entriesPerPage').val()) || 10;
    const total   = allCoursesData.length;
    const pages   = Math.ceil(total / perPage) || 1;

    if (currentPage < 1)     currentPage = 1;
    if (currentPage > pages) currentPage = pages;

    const start    = (currentPage - 1) * perPage;
    const end      = Math.min(start + perPage, total);
    const pageData = allCoursesData.slice(start, end);

    renderCoursesTable(pageData,                              'coursesTableBody', true,  start);
    renderCoursesTable(pageData.filter(c => c.School==='TB5'), 'tb5TableBody',    false, start);
    renderCoursesTable(pageData.filter(c => c.School==='BBI'), 'bbiTableBody',    false, start);

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
        } else if (p === currentPage - range - 1 ||
                   p === currentPage + range + 1) {
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
    document.getElementById('coursesTableBody')
        .closest('.card')
        .scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ─── RENDER COURSES TABLE ─────────────────────────────────────────────────────
function renderCoursesTable(courses, tableBodyId, showSchool, offset = 0) {
    const tbody = document.getElementById(tableBodyId);
    const cols  = showSchool ? 9 : 8;

    if (!courses || courses.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="${cols}" class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x" style="font-size:3rem;"></i>
                    <p class="mt-2 mb-0 fw-semibold">No courses found</p>
                    <small>Try adjusting your filters or add a new course.</small>
                </td>
            </tr>`;
        return;
    }

    let html = '';
    courses.forEach((course, i) => {
        const isActive    = course.IsActive == 1;
        const schoolBadge = course.School === 'TB5'
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
                    ${escapeHtml(course.CourseCode)}
                </span>
            </td>
            <td>
                <div class="fw-semibold small">${escapeHtml(course.CourseName)}</div>
            </td>
            ${showSchool ? `<td>${schoolBadge}</td>` : ''}
            <td>
                <span class="badge bg-primary rounded-pill">
                    ${escapeHtml(course.Category)}
                </span>
            </td>
            <td class="small fw-semibold">${escapeHtml(course.Duration || 'N/A')}</td>
            <td class="small fw-semibold">
                ₱${parseFloat(course.Tuition || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}
            </td>
            <td>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch"
                        ${isActive ? 'checked' : ''}
                        onchange="toggleCourseStatus(${course.Id}, this)">
                    <label class="form-check-label small fw-semibold ${isActive ? 'text-success' : 'text-danger'}">
                        ${isActive ? 'Active' : 'Inactive'}
                    </label>
                </div>
            </td>
            <td class="text-center">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-info" title="View"
                        onclick="viewCourse(${course.Id})">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" title="Edit"
                        onclick="editCourse(${course.Id})">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger" title="Delete"
                        onclick="deleteCourse(${course.Id}, '${escapeJs(course.CourseCode)} — ${escapeJs(course.CourseName)}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    });

    tbody.innerHTML = html;
}

// ─── SHOW CREATE MODAL ────────────────────────────────────────────────────────
function showCreateCourseModal() {
    document.getElementById('modalTitle').textContent = 'Add New Course';
    document.getElementById('courseForm').reset();
    document.getElementById('courseId').value        = '';
    document.getElementById('courseStatus').checked  = true;
    new bootstrap.Modal(document.getElementById('courseModal')).show();
}

// ─── TOGGLE COURSE STATUS ─────────────────────────────────────────────────────
function toggleCourseStatus(courseId, checkbox) {
    const label    = checkbox.nextElementSibling;
    const isActive = checkbox.checked ? 1 : 0;

    $.ajax({
        url: 'toggle-course-status.php',
        method: 'POST',
        data: JSON.stringify({ id: courseId, isActive }),
        contentType: 'application/json',
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                label.textContent = checkbox.checked ? 'Active' : 'Inactive';
                label.classList.toggle('text-success',  checkbox.checked);
                label.classList.toggle('text-danger',  !checkbox.checked);
                loadCourses();
            } else {
                showToast('Error: ' + res.message, 'danger');
                checkbox.checked = !checkbox.checked;
            }
        },
        error: function () {
            showToast('Failed to update status.', 'danger');
            checkbox.checked = !checkbox.checked;
        }
    });
}

// ─── VIEW COURSE ──────────────────────────────────────────────────────────────
function viewCourse(courseId) {
    $('#courseDetailsBody').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2 text-muted small">Loading...</p>
        </div>`);

    new bootstrap.Modal(document.getElementById('viewCourseModal')).show();

    $.ajax({
        url: 'get-course-details.php',
        method: 'GET',
        data: { id: courseId },
        dataType: 'json',
        success: function (res) {
            if (!res.success) {
                $('#courseDetailsBody').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-1"></i>${escapeHtml(res.message)}
                    </div>`);
                return;
            }

            const c = res.course;
            const schoolImg = `<img src="../assets/img/${c.School === 'TB5' ? 'tb5' : 'bbi'}-logo.png"
                style="width:28px;height:28px;object-fit:cover;border-radius:50%;"> ${c.School}`;

            $('#courseDetailsBody').html(`
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Course Code</div>
                            <div class="fw-bold font-monospace fs-5">${escapeHtml(c.CourseCode)}</div>
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
                            <div class="text-muted small mb-1">Course Name</div>
                            <div class="fw-bold">${escapeHtml(c.CourseName)}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Category</div>
                            <span class="badge bg-primary px-3 py-2">${escapeHtml(c.Category)}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Total Hours</div>
                            <div class="fw-bold">${escapeHtml(c.Duration || 'N/A')} hrs</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Total Days</div>
                            <div class="fw-bold">${c.DurationHours || 'N/A'} days</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Tuition Fee</div>
                            <div class="fw-bold">₱${parseFloat(c.Tuition || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Max Students</div>
                            <div class="fw-bold">${c.MaxStudents || 'N/A'}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small mb-1">Status</div>
                            <span class="badge ${c.IsActive == 1 ? 'bg-success' : 'bg-secondary'} px-3 py-2">
                                <i class="bi bi-${c.IsActive == 1 ? 'check-circle' : 'x-circle'} me-1"></i>
                                ${c.IsActive == 1 ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3 border">
                            <div class="text-muted small mb-1">Description</div>
                            <div>${escapeHtml(c.Description || 'No description provided.')}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3 border">
                            <div class="text-muted small mb-1">Prerequisites</div>
                            <div>${escapeHtml(c.Prerequisites || 'None')}</div>
                        </div>
                    </div>
                </div>`);
        },
        error: function () {
            $('#courseDetailsBody').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-wifi-off me-1"></i>Failed to load course details.
                </div>`);
        }
    });
}

// ─── EDIT COURSE ──────────────────────────────────────────────────────────────
function editCourse(courseId) {
    $.ajax({
        url: 'get-course-details.php',
        method: 'GET',
        data: { id: courseId },
        dataType: 'json',
        success: function (res) {
            if (!res.success) { showToast('Error: ' + res.message, 'danger'); return; }
            const c = res.course;

            document.getElementById('modalTitle').textContent         = 'Edit Course';
            document.getElementById('courseId').value                 = c.Id;
            document.getElementById('courseCode').value               = c.CourseCode;
            document.getElementById('courseName').value               = c.CourseName;
            document.getElementById('courseDescription').value        = c.Description    || '';
            document.getElementById('courseSchool').value             = c.School;
            document.getElementById('category').value                 = c.Category;
            document.getElementById('courseDuration').value           = c.Duration       || '';
            document.getElementById('durationHours').value            = c.DurationHours  || '';
            document.getElementById('tuition').value                  = c.Tuition        || '';
            document.getElementById('maxStudents').value              = c.MaxStudents    || '';
            document.getElementById('prerequisites').value            = c.Prerequisites  || '';
            document.getElementById('courseStatus').checked           = c.IsActive == 1;

            new bootstrap.Modal(document.getElementById('courseModal')).show();
        },
        error: function () {
            showToast('Failed to load course details.', 'danger');
        }
    });
}

// ─── DELETE COURSE (open modal) ───────────────────────────────────────────────
function deleteCourse(courseId, label) {
    deleteCourseTargetId = courseId;
    $('#deleteCourseLabel').text(label || 'this course');
    new bootstrap.Modal(document.getElementById('deleteCourseModal')).show();
}

// ─── SAVE COURSE ──────────────────────────────────────────────────────────────
function saveCourse() {
    const form = document.getElementById('courseForm');

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const $btn     = $('#courseModal .modal-footer .btn-primary');
    const origHtml = $btn.html();
    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

    const courseData = {
        id:            document.getElementById('courseId').value,
        courseCode:    document.getElementById('courseCode').value,
        courseName:    document.getElementById('courseName').value,
        description:   document.getElementById('courseDescription').value,
        school:        document.getElementById('courseSchool').value,
        category:      document.getElementById('category').value,
        duration:      document.getElementById('courseDuration').value,
        durationHours: document.getElementById('durationHours').value || null,
        tuition:       document.getElementById('tuition').value       || null,
        maxStudents:   document.getElementById('maxStudents').value   || null,
        prerequisites: document.getElementById('prerequisites').value,
        isActive:      document.getElementById('courseStatus').checked ? 1 : 0
    };

    $.ajax({
        url: 'save-course.php',
        method: 'POST',
        data: JSON.stringify(courseData),
        contentType: 'application/json',
        dataType: 'json',
        success: function (res) {
            $btn.prop('disabled', false).html(origHtml);
            if (res.success) {
                bootstrap.Modal.getInstance(
                    document.getElementById('courseModal')).hide();
                form.reset();
                showToast(
                    courseData.id ? 'Course updated successfully.' : 'Course added successfully.',
                    'success'
                );
                loadCourses();
            } else {
                showToast('⚠ ' + res.message, 'danger');
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false).html(origHtml);
            console.error(xhr.responseText);
            showToast('Server error. Please try again.', 'danger');
        }
    });
}

// ─── CALCULATE TOTAL DAYS ─────────────────────────────────────────────────────
function calculateTotalDays() {
    const hrs  = parseInt(document.getElementById('courseDuration').value) || 0;
    document.getElementById('durationHours').value = hrs > 0 ? Math.ceil(hrs / 8) : '';
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
    return String(text).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function formatDateTime(dateStr) {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}
</script>

<?php include('../footer/footer.php'); ?>