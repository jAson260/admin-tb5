<?php

session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();
require_once('../../db-connect.php');

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
        FROM subjects
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
                            <i class="bi bi-journal-bookmark-fill me-2"></i>Subject Management
                        </h2>
                        <p class="text-white-50 mb-0">
                            Create and manage TESDA subjects/competencies for TB5 and Big Blossom Institute
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-light" onclick="showCreateSubjectModal()">
                            <i class="bi bi-plus-circle me-2"></i>Add New Subject
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
                                <i class="bi bi-journal-bookmark-fill text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Total Subjects</h6>
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
                                <h6 class="text-muted mb-0 small">Active Subjects</h6>
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
                                <h6 class="text-muted mb-0 small">TB5 Subjects</h6>
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
                                <h6 class="text-muted mb-0 small">BBI Subjects</h6>
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
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small">Search Subject</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Subject name, code, or course..." id="searchSubject" onkeyup="loadSubjects()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Filter by Status</label>
                        <select class="form-select" id="filterStatus" onchange="loadSubjects()">
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

        <!-- Subjects by School Tabs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-subjects-tab" data-bs-toggle="tab" data-bs-target="#all-subjects" type="button" role="tab" onclick="setTabFilter('')">
                            <i class="bi bi-list-ul me-1"></i> All Subjects
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tb5-subjects-tab" data-bs-toggle="tab" data-bs-target="#tb5-subjects" type="button" role="tab" onclick="setTabFilter('TB5')">
                            <i class="bi bi-bank me-1"></i> TB5 Subjects
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bbi-subjects-tab" data-bs-toggle="tab" data-bs-target="#bbi-subjects" type="button" role="tab" onclick="setTabFilter('BBI')">
                            <i class="bi bi-building me-1"></i> BBI Subjects
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- All Subjects Tab -->
                    <div class="tab-pane fade show active" id="all-subjects" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="subjectsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Subject Name</th>
                                        <th>Course</th>
                                        <th>School</th>
                                        <th>Type</th>
                                        <th>Hours</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="subjectsTableBody">
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

                    <!-- TB5 Subjects Tab -->
                    <div class="tab-pane fade" id="tb5-subjects" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Showing subjects for <strong>The Big Five Training and Assessment Center</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Subject Name</th>
                                        <th>Course</th>
                                        <th>Type</th>
                                        <th>Hours</th>
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

                    <!-- BBI Subjects Tab -->
                    <div class="tab-pane fade" id="bbi-subjects" role="tabpanel">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle me-2"></i>
                            Showing subjects for <strong>Big Blossom Institute Inc.</strong>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Subject Name</th>
                                        <th>Course</th>
                                        <th>Type</th>
                                        <th>Hours</th>
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

<!-- Create/Edit Subject Modal -->
<div class="modal fade" id="subjectModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-plus-circle me-2"></i><span id="modalTitle">Add New Subject</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="subjectForm">
                    <input type="hidden" id="subjectId">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-info-circle me-2"></i>Basic Information
                            </h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Subject Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subjectCode" placeholder="e.g., CSS-NC2-001" required>
                                <div class="form-text">Unique identifier for the subject</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Subject Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subjectName" placeholder="e.g., Basic Life Support" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" id="subjectDescription" rows="4" placeholder="Enter subject description..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Learning Outcomes</label>
                                <textarea class="form-control" id="learningOutcomes" rows="3" placeholder="e.g., Demonstrate basic life support techniques..."></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-gear me-2"></i>Subject Details
                            </h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">School <span class="text-danger">*</span></label>
                                    <select class="form-select" id="subjectSchool" required onchange="loadCoursesBySchool()">
                                        <option value="">Choose School...</option>
                                        <option value="TB5">TB5</option>
                                        <option value="BBI">Big Blossom Institute</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Course <span class="text-danger">*</span></label>
                                    <select class="form-select" id="subjectCourse" required disabled>
                                        <option value="">Select School First</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Subject Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="subjectType" required>
                                        <option value="">Select Type</option>
                                        <option value="Theory">Theory</option>
                                        <option value="Practical">Practical</option>
                                        <option value="Mixed">Mixed</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Competency <span class="text-danger">*</span></label>
                                    <select class="form-select" id="subjectCompetency" required>
                                        <option value="">Select Competency</option>
                                        <option value="Basic">Basic Competency</option>
                                        <option value="Common">Common Competency</option>
                                        <option value="Core">Core Competency</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Total Hours <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="subjectHours" placeholder="e.g., 40" required min="1" oninput="calculateSubjectDays()">
                                    <div class="form-text">Enter total subject hours</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Total Days</label>
                                    <input type="number" class="form-control" id="subjectDays" placeholder="Auto-calculated" readonly>
                                    <div class="form-text">Calculated at 8 hours/day</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Passing Grade (%)</label>
                                    <input type="number" class="form-control" id="passingGrade" placeholder="e.g., 75" min="0" max="100">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Sequence/Order</label>
                                    <input type="number" class="form-control" id="subjectOrder" placeholder="e.g., 1" min="1">
                                    <div class="form-text">Display order within course</div>
                                </div>
                            </div>

                            <!-- Status -->
                            <h6 class="fw-bold mb-3 mt-4">
                                <i class="bi bi-toggle-on me-2"></i>Status
                            </h6>
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="subjectStatus" checked>
                                        <label class="form-check-label fw-semibold" for="subjectStatus">
                                            Subject is Active and Available
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
                <button type="button" class="btn btn-primary" onclick="saveSubject()">
                    <i class="bi bi-check-circle me-1"></i>Save Subject
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Subject Details Modal -->
<div class="modal fade" id="viewSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-eye me-2"></i>Subject Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="subjectDetailsBody"></div>
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

$(document).ready(function() {
    loadSubjects();
});

function setTabFilter(school) {
    currentTabFilter = school;
    loadSubjects();
}

function resetFilters() {
    $('#searchSubject').val('');
    $('#filterStatus').val('');
    currentTabFilter = '';
    $('#all-subjects-tab').tab('show');
    loadSubjects();
}

function loadSubjects() {
    const search = $('#searchSubject').val();
    const school = currentTabFilter;
    const status = $('#filterStatus').val();

    $.ajax({
        url: 'get-subjects.php',
        method: 'POST',
        data: { search, school, status },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderSubjectsTable(response.subjects, 'subjectsTableBody', true);
                renderSubjectsTable(response.subjects.filter(s => s.School === 'TB5'), 'tb5TableBody', false);
                renderSubjectsTable(response.subjects.filter(s => s.School === 'BBI'), 'bbiTableBody', false);

                $('#statTotal').text(response.stats.total);
                $('#statActive').text(response.stats.active);
                $('#statTB5').text(response.stats.tb5);
                $('#statBBI').text(response.stats.bbi);
            } else {
                console.error('Error loading subjects:', response.message);
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
        }
    });
}

function renderSubjectsTable(subjects, tableBodyId, showSchool) {
    const tbody = document.getElementById(tableBodyId);
    const colSpan = showSchool ? 8 : 7;

    if (!subjects || subjects.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center text-muted py-4">
            <i class="bi bi-journal-x" style="font-size:2rem;"></i><br>No subjects found
        </td></tr>`;
        return;
    }

    let html = '';
    subjects.forEach(subject => {
        const isActive = subject.IsActive == 1;
        const schoolBadge = subject.School === 'TB5'
            ? '<span class="badge bg-info"><i class="bi bi-bank me-1"></i>TB5</span>'
            : '<span class="badge bg-warning"><i class="bi bi-building me-1"></i>BBI</span>';

        const typeBadge = {
            'Theory':    'bg-primary',
            'Practical': 'bg-success',
            'Mixed':     'bg-secondary'
        }[subject.SubjectType] || 'bg-secondary';

        const competencyBadge = {
            'Basic':  'bg-info',
            'Common': 'bg-warning',
            'Core':   'bg-danger'
        }[subject.Competency] || 'bg-secondary';

        html += `
            <tr>
                <td><span class="badge bg-secondary">${escapeHtml(subject.SubjectCode)}</span></td>
                <td>
                    <div class="fw-semibold">${escapeHtml(subject.SubjectName)}</div>
                    <small class="text-muted"><span class="badge ${competencyBadge} bg-opacity-75">${escapeHtml(subject.Competency || '')}</span></small>
                </td>
                <td><small class="text-muted">${escapeHtml(subject.CourseName || 'N/A')}</small></td>
                ${showSchool ? `<td>${schoolBadge}</td>` : ''}
                <td><span class="badge ${typeBadge}">${escapeHtml(subject.SubjectType || 'N/A')}</span></td>
                <td>${subject.Hours || 'N/A'} hrs</td>
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" ${isActive ? 'checked' : ''}
                            onchange="toggleSubjectStatus(${subject.Id}, this)">
                        <label class="form-check-label small fw-semibold ${isActive ? 'text-success' : 'text-danger'}">
                            ${isActive ? 'Active' : 'Inactive'}
                        </label>
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="viewSubject(${subject.Id})" title="View">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-outline-success" onclick="editSubject(${subject.Id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteSubject(${subject.Id})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}

function calculateSubjectDays() {
    const hours = parseFloat($('#subjectHours').val()) || 0;
    $('#subjectDays').val(hours > 0 ? Math.ceil(hours / 8) : '');
}

function loadCoursesBySchool() {
    const school = $('#subjectSchool').val();
    const courseDropdown = $('#subjectCourse');

    if (!school) {
        courseDropdown.prop('disabled', true).html('<option value="">Select School First</option>');
        return;
    }

    courseDropdown.prop('disabled', true).html('<option value="">Loading courses...</option>');

    $.ajax({
        url: '../create-batch/get-courses-by-school.php',
        method: 'GET',
        data: { school: school },
        dataType: 'json',
        success: function(data) {
            if (data.success && data.courses && data.courses.length > 0) {
                let options = '<option value="">Select Course</option>';
                data.courses.forEach(course => {
                    options += `<option value="${course.Id}">${escapeHtml(course.CourseCode)} - ${escapeHtml(course.CourseName)}</option>`;
                });
                courseDropdown.prop('disabled', false).html(options);
            } else {
                courseDropdown.prop('disabled', true).html('<option value="">No courses available</option>');
            }
        },
        error: function(xhr) {
            console.error('Error loading courses:', xhr.responseText);
            courseDropdown.prop('disabled', true).html('<option value="">Error loading courses</option>');
        }
    });
}

function showCreateSubjectModal() {
    document.getElementById('modalTitle').textContent = 'Add New Subject';
    document.getElementById('subjectForm').reset();
    document.getElementById('subjectId').value = '';
    document.getElementById('subjectStatus').checked = true;
    $('#subjectCourse').prop('disabled', true).html('<option value="">Select School First</option>');
    new bootstrap.Modal(document.getElementById('subjectModal')).show();
}

function toggleSubjectStatus(subjectId, checkbox) {
    const label = checkbox.nextElementSibling;
    const isActive = checkbox.checked ? 1 : 0;

    $.ajax({
        url: 'toggle-subject-status.php',
        method: 'POST',
        data: JSON.stringify({ id: subjectId, isActive: isActive }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                label.textContent = checkbox.checked ? 'Active' : 'Inactive';
                label.className = `form-check-label small fw-semibold ${checkbox.checked ? 'text-success' : 'text-danger'}`;
                loadSubjects();
            } else {
                alert('Error: ' + response.message);
                checkbox.checked = !checkbox.checked;
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            checkbox.checked = !checkbox.checked;
        }
    });
}

function viewSubject(subjectId) {
    $.ajax({
        url: 'get-subject-details.php',
        method: 'GET',
        data: { id: subjectId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const s = response.subject;
                const schoolBadge = s.School === 'TB5'
                    ? '<span class="badge bg-info">TB5</span>'
                    : '<span class="badge bg-warning">BBI</span>';

                $('#subjectDetailsBody').html(`
                    <div class="row g-3">
                        <div class="col-md-6"><label class="text-muted small">Subject Code</label><p class="fw-semibold">${escapeHtml(s.SubjectCode)}</p></div>
                        <div class="col-md-6"><label class="text-muted small">Subject Name</label><p class="fw-semibold">${escapeHtml(s.SubjectName)}</p></div>
                        <div class="col-md-6"><label class="text-muted small">School</label><p>${schoolBadge}</p></div>
                        <div class="col-md-6"><label class="text-muted small">Course</label><p class="fw-semibold">${escapeHtml(s.CourseName || 'N/A')}</p></div>
                        <div class="col-md-6"><label class="text-muted small">Subject Type</label><p><span class="badge bg-primary">${escapeHtml(s.SubjectType || 'N/A')}</span></p></div>
                        <div class="col-md-6"><label class="text-muted small">Competency</label><p><span class="badge bg-secondary">${escapeHtml(s.Competency || 'N/A')}</span></p></div>
                        <div class="col-md-4"><label class="text-muted small">Total Hours</label><p class="fw-semibold">${s.Hours || 'N/A'} hrs</p></div>
                        <div class="col-md-4"><label class="text-muted small">Total Days</label><p class="fw-semibold">${s.Days || 'N/A'} days</p></div>
                        <div class="col-md-4"><label class="text-muted small">Passing Grade</label><p class="fw-semibold">${s.PassingGrade || 'N/A'}%</p></div>
                        <div class="col-12"><label class="text-muted small">Description</label><p>${escapeHtml(s.Description || 'No description')}</p></div>
                        <div class="col-12"><label class="text-muted small">Learning Outcomes</label><p>${escapeHtml(s.LearningOutcomes || 'None')}</p></div>
                        <div class="col-md-6"><label class="text-muted small">Status</label><p><span class="badge ${s.IsActive == 1 ? 'bg-success' : 'bg-danger'}">${s.IsActive == 1 ? 'Active' : 'Inactive'}</span></p></div>
                        <div class="col-md-6"><label class="text-muted small">Created</label><p class="fw-semibold">${formatDateTime(s.CreatedAt)}</p></div>
                    </div>
                `);
                new bootstrap.Modal(document.getElementById('viewSubjectModal')).show();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) { console.error('Error:', xhr.responseText); }
    });
}

function editSubject(subjectId) {
    $.ajax({
        url: 'get-subject-details.php',
        method: 'GET',
        data: { id: subjectId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const s = response.subject;
                document.getElementById('modalTitle').textContent = 'Edit Subject';
                document.getElementById('subjectId').value   = s.Id;
                document.getElementById('subjectCode').value = s.SubjectCode;
                document.getElementById('subjectName').value = s.SubjectName;
                document.getElementById('subjectDescription').value = s.Description || '';
                document.getElementById('learningOutcomes').value   = s.LearningOutcomes || '';
                document.getElementById('subjectSchool').value      = s.School;
                document.getElementById('subjectType').value        = s.SubjectType;
                document.getElementById('subjectCompetency').value  = s.Competency;
                document.getElementById('subjectHours').value       = s.Hours || '';
                document.getElementById('subjectDays').value        = s.Days || '';
                document.getElementById('passingGrade').value       = s.PassingGrade || '';
                document.getElementById('subjectOrder').value       = s.SubjectOrder || '';
                document.getElementById('subjectStatus').checked    = s.IsActive == 1;

                // Load courses for this school then set selected course
                loadCoursesBySchool();
                setTimeout(() => {
                    $('#subjectCourse').val(s.CourseId);
                }, 600);

                new bootstrap.Modal(document.getElementById('subjectModal')).show();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) { console.error('Error:', xhr.responseText); }
    });
}

function deleteSubject(subjectId) {
    if (confirm('Are you sure you want to delete this subject? This action cannot be undone.')) {
        $.ajax({
            url: 'delete-subject.php',
            method: 'POST',
            data: JSON.stringify({ id: subjectId }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    loadSubjects();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) { console.error('Error:', xhr.responseText); }
        });
    }
}

function saveSubject() {
    const form = document.getElementById('subjectForm');

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const subjectData = {
        id:              document.getElementById('subjectId').value,
        subjectCode:     document.getElementById('subjectCode').value,
        subjectName:     document.getElementById('subjectName').value,
        description:     document.getElementById('subjectDescription').value,
        learningOutcomes:document.getElementById('learningOutcomes').value,
        school:          document.getElementById('subjectSchool').value,
        courseId:        document.getElementById('subjectCourse').value,
        subjectType:     document.getElementById('subjectType').value,
        competency:      document.getElementById('subjectCompetency').value,
        hours:           document.getElementById('subjectHours').value,
        days:            document.getElementById('subjectDays').value || null,
        passingGrade:    document.getElementById('passingGrade').value || null,
        subjectOrder:    document.getElementById('subjectOrder').value || null,
        isActive:        document.getElementById('subjectStatus').checked ? 1 : 0
    };

    const saveBtn = $('button[onclick="saveSubject()"]');
    const originalHtml = saveBtn.html();
    saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

    $.ajax({
        url: 'save-subject.php',
        method: 'POST',
        data: JSON.stringify(subjectData),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            saveBtn.prop('disabled', false).html(originalHtml);
            if (response.success) {
                bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();
                form.reset();
                loadSubjects();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            saveBtn.prop('disabled', false).html(originalHtml);
            console.error('Error:', xhr.responseText);
            alert('Failed to save subject');
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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