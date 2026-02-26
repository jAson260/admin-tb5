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
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-book text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Total Courses</h6>
                                <h3 class="mb-0 fw-bold" id="statTotal"><?php echo $stats['total']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-check-circle text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Active Courses</h6>
                                <h3 class="mb-0 fw-bold" id="statActive"><?php echo $stats['active']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-bank text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">TB5 Courses</h6>
                                <h3 class="mb-0 fw-bold" id="statTB5"><?php echo $stats['tb5']; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-building text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">BBI Courses</h6>
                                <h3 class="mb-0 fw-bold" id="statBBI"><?php echo $stats['bbi']; ?></h3>
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
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Search Course</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Course name or code..." id="searchCourse">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Status</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">&nbsp;</label>
                        <button class="btn btn-primary w-100" onclick="loadCourses()">
                            <i class="bi bi-funnel me-1"></i>Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses by School Tabs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-courses-tab" data-bs-toggle="tab" data-bs-target="#all-courses" type="button" role="tab" onclick="setTabFilter('')">
                            <i class="bi bi-list-ul me-1"></i> All Courses
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tb5-courses-tab" data-bs-toggle="tab" data-bs-target="#tb5-courses" type="button" role="tab" onclick="setTabFilter('TB5')">
                            <i class="bi bi-bank me-1"></i> TB5 Courses
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bbi-courses-tab" data-bs-toggle="tab" data-bs-target="#bbi-courses" type="button" role="tab" onclick="setTabFilter('BBI')">
                            <i class="bi bi-building me-1"></i> BBI Courses
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- All Courses Tab -->
                    <div class="tab-pane fade show active" id="all-courses" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="coursesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>School</th>
                                        <th>Category</th>
                                        <th>Duration</th>
                                        <th>Tuition</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="coursesTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TB5 Courses Tab -->
                    <div class="tab-pane fade" id="tb5-courses" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Showing courses for <strong>The Big Five Training and Assessment Center</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Category</th>
                                        <th>Duration</th>
                                        <th>Tuition</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tb5TableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- BBI Courses Tab -->
                    <div class="tab-pane fade" id="bbi-courses" role="tabpanel">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            Showing courses for <strong>Big Blossom Institute Inc.</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Category</th>
                                        <th>Duration</th>
                                        <th>Tuition</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="bbiTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Course Modal -->
<div class="modal fade" id="courseModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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
                                    <label class="form-label fw-semibold">Duration <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="courseDuration" placeholder="e.g., 3 Months" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Total Hours</label>
                                    <input type="number" class="form-control" id="durationHours" placeholder="e.g., 216">
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

<style>
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
let currentTabFilter = '';

// Load courses on page load
$(document).ready(function() {
    loadCourses();
});

// Set tab filter
function setTabFilter(school) {
    currentTabFilter = school;
    loadCourses();
}

// Load courses from database
function loadCourses() {
    const search = $('#searchCourse').val();
    const school = currentTabFilter; // Removed filterSchool dropdown reference
    const status = $('#filterStatus').val();
    
    $.ajax({
        url: 'get-courses.php',
        method: 'POST',
        data: { search, school, status },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderCoursesTable(response.courses, 'coursesTableBody', true);
                renderCoursesTable(response.courses.filter(c => c.School === 'TB5'), 'tb5TableBody', false);
                renderCoursesTable(response.courses.filter(c => c.School === 'BBI'), 'bbiTableBody', false);
                
                // Update stats
                $('#statTotal').text(response.stats.total);
                $('#statActive').text(response.stats.active);
                $('#statTB5').text(response.stats.tb5);
                $('#statBBI').text(response.stats.bbi);
            } else {
                alert('Error loading courses: ' + response.message);
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            alert('Failed to load courses');
        }
    });
}

// Render courses table
function renderCoursesTable(courses, tableBodyId, showSchool) {
    const tbody = document.getElementById(tableBodyId);
    
    if (courses.length === 0) {
        tbody.innerHTML = '<tr><td colspan="' + (showSchool ? '8' : '7') + '" class="text-center text-muted">No courses found</td></tr>';
        return;
    }
    
    let html = '';
    courses.forEach(course => {
        const isActive = course.IsActive == 1;
        const schoolBadge = course.School === 'TB5' ? 
            '<span class="badge bg-info"><i class="bi bi-bank me-1"></i>TB5</span>' : 
            '<span class="badge bg-warning"><i class="bi bi-flower1 me-1"></i>BBI</span>';
        
        html += `
            <tr>
                <td><span class="badge bg-secondary">${escapeHtml(course.CourseCode)}</span></td>
                <td><strong>${escapeHtml(course.CourseName)}</strong></td>
                ${showSchool ? `<td>${schoolBadge}</td>` : ''}
                <td><span class="badge bg-primary">${escapeHtml(course.Category)}</span></td>
                <td>${escapeHtml(course.Duration || 'N/A')}</td>
                <td>₱${parseFloat(course.Tuition || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" ${isActive ? 'checked' : ''} 
                            onchange="toggleCourseStatus(${course.Id}, this)">
                        <label class="form-check-label small fw-semibold ${isActive ? 'text-success' : 'text-danger'}">
                            ${isActive ? 'Active' : 'Inactive'}
                        </label>
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="viewCourse(${course.Id})" title="View">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-success" onclick="editCourse(${course.Id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteCourse(${course.Id})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Show create course modal
function showCreateCourseModal() {
    document.getElementById('modalTitle').textContent = 'Add New Course';
    document.getElementById('courseForm').reset();
    document.getElementById('courseId').value = '';
    document.getElementById('courseStatus').checked = true;
    
    const modal = new bootstrap.Modal(document.getElementById('courseModal'));
    modal.show();
}

// Toggle course status
function toggleCourseStatus(courseId, checkbox) {
    const label = checkbox.nextElementSibling;
    const isActive = checkbox.checked ? 1 : 0;
    
    $.ajax({
        url: 'toggle-course-status.php',
        method: 'POST',
        data: JSON.stringify({ id: courseId, isActive: isActive }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (checkbox.checked) {
                    label.textContent = 'Active';
                    label.classList.remove('text-danger');
                    label.classList.add('text-success');
                } else {
                    label.textContent = 'Inactive';
                    label.classList.remove('text-success');
                    label.classList.add('text-danger');
                }
                loadCourses(); // Refresh stats
            } else {
                alert('Error: ' + response.message);
                checkbox.checked = !checkbox.checked; // Revert
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            alert('Failed to update status');
            checkbox.checked = !checkbox.checked; // Revert
        }
    });
}

// View course details
function viewCourse(courseId) {
    $.ajax({
        url: 'get-course-details.php',
        method: 'GET',
        data: { id: courseId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const course = response.course;
                const html = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Course Code</label>
                            <p class="fw-semibold">${escapeHtml(course.CourseCode)}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Course Name</label>
                            <p class="fw-semibold">${escapeHtml(course.CourseName)}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">School</label>
                            <p><span class="badge ${course.School === 'TB5' ? 'bg-info' : 'bg-warning'}">${course.School}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Category</label>
                            <p><span class="badge bg-primary">${escapeHtml(course.Category)}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Duration</label>
                            <p class="fw-semibold">${escapeHtml(course.Duration || 'N/A')}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Total Hours</label>
                            <p class="fw-semibold">${course.DurationHours || 'N/A'} hours</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Tuition</label>
                            <p class="fw-semibold">₱${parseFloat(course.Tuition || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Max Students</label>
                            <p class="fw-semibold">${course.MaxStudents || 'N/A'}</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Description</label>
                            <p>${escapeHtml(course.Description || 'No description')}</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Prerequisites</label>
                            <p>${escapeHtml(course.Prerequisites || 'None')}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Status</label>
                            <p><span class="badge ${course.IsActive == 1 ? 'bg-success' : 'bg-danger'}">${course.IsActive == 1 ? 'Active' : 'Inactive'}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Created</label>
                            <p class="fw-semibold">${formatDateTime(course.CreatedAt)}</p>
                        </div>
                    </div>
                `;
                
                $('#courseDetailsBody').html(html);
                const modal = new bootstrap.Modal(document.getElementById('viewCourseModal'));
                modal.show();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            alert('Failed to load course details');
        }
    });
}

// Edit course
function editCourse(courseId) {
    $.ajax({
        url: 'get-course-details.php',
        method: 'GET',
        data: { id: courseId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const course = response.course;
                
                document.getElementById('modalTitle').textContent = 'Edit Course';
                document.getElementById('courseId').value = course.Id;
                document.getElementById('courseCode').value = course.CourseCode;
                document.getElementById('courseName').value = course.CourseName;
                document.getElementById('courseDescription').value = course.Description || '';
                document.getElementById('courseSchool').value = course.School;
                document.getElementById('category').value = course.Category;
                document.getElementById('courseDuration').value = course.Duration || '';
                document.getElementById('durationHours').value = course.DurationHours || '';
                document.getElementById('tuition').value = course.Tuition || '';
                document.getElementById('maxStudents').value = course.MaxStudents || '';
                document.getElementById('prerequisites').value = course.Prerequisites || '';
                document.getElementById('courseStatus').checked = course.IsActive == 1;
                
                const modal = new bootstrap.Modal(document.getElementById('courseModal'));
                modal.show();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            alert('Failed to load course details');
        }
    });
}

// Delete course
function deleteCourse(courseId) {
    if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
        $.ajax({
            url: 'delete-course.php',
            method: 'POST',
            data: JSON.stringify({ id: courseId }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    loadCourses();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                alert('Failed to delete course');
            }
        });
    }
}

// Save course (create or update)
function saveCourse() {
    const form = document.getElementById('courseForm');
    
    if (form.checkValidity()) {
        const courseData = {
            id: document.getElementById('courseId').value,
            courseCode: document.getElementById('courseCode').value,
            courseName: document.getElementById('courseName').value,
            description: document.getElementById('courseDescription').value,
            school: document.getElementById('courseSchool').value,
            category: document.getElementById('category').value,
            duration: document.getElementById('courseDuration').value,
            durationHours: document.getElementById('durationHours').value || null,
            tuition: document.getElementById('tuition').value || null,
            maxStudents: document.getElementById('maxStudents').value || null,
            prerequisites: document.getElementById('prerequisites').value,
            isActive: document.getElementById('courseStatus').checked ? 1 : 0
        };
        
        $.ajax({
            url: 'save-course.php',
            method: 'POST',
            data: JSON.stringify(courseData),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    bootstrap.Modal.getInstance(document.getElementById('courseModal')).hide();
                    form.reset();
                    loadCourses();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                alert('Failed to save course');
            }
        });
    } else {
        form.reportValidity();
    }
}

// Helper functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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
</script>

<?php
// Include footer
include('../footer/footer.php');
?>