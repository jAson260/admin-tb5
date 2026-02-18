<?php
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
                                <h3 class="mb-0 fw-bold">18</h3>
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
                                <h3 class="mb-0 fw-bold">15</h3>
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
                                <h3 class="mb-0 fw-bold">10</h3>
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
                                <h3 class="mb-0 fw-bold">8</h3>
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
                            <input type="text" class="form-control" placeholder="Course name or code..." id="searchCourse">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Filter by School</label>
                        <select class="form-select" id="filterSchool">
                            <option value="">All Schools</option>
                            <option value="tb5">TB5</option>
                            <option value="bbi">Big Blossom Institute</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Status</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">&nbsp;</label>
                        <button class="btn btn-primary w-100" onclick="applyFilters()">
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
                        <button class="nav-link active" id="all-courses-tab" data-bs-toggle="tab" data-bs-target="#all-courses" type="button" role="tab">
                            <i class="bi bi-list-ul me-1"></i> All Courses
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tb5-courses-tab" data-bs-toggle="tab" data-bs-target="#tb5-courses" type="button" role="tab">
                            <i class="bi bi-bank me-1"></i> TB5 Courses
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bbi-courses-tab" data-bs-toggle="tab" data-bs-target="#bbi-courses" type="button" role="tab">
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
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>School</th>
                                        <th>Duration</th>
                                        <th>Students</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-secondary">CSS-NC2</span></td>
                                        <td><strong>Caregiving NC II</strong></td>
                                        <td><span class="badge bg-info">TB5</span></td>
                                        <td>3 Months</td>
                                        <td><span class="badge bg-primary">45 Students</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked onchange="toggleCourseStatus(1, this)">
                                                <label class="form-check-label small text-success fw-semibold">Active</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="viewCourse(1)" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" onclick="editCourse(1)" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="deleteCourse(1)" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-secondary">BPP-NC2</span></td>
                                        <td><strong>Bread & Pastry Production NC II</strong></td>
                                        <td><span class="badge bg-info">TB5</span></td>
                                        <td>2 Months</td>
                                        <td><span class="badge bg-primary">32 Students</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked onchange="toggleCourseStatus(2, this)">
                                                <label class="form-check-label small text-success fw-semibold">Active</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="viewCourse(2)" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" onclick="editCourse(2)" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="deleteCourse(2)" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-secondary">COK-NC2</span></td>
                                        <td><strong>Commercial Cooking NC II</strong></td>
                                        <td><span class="badge bg-warning">BBI</span></td>
                                        <td>3 Months</td>
                                        <td><span class="badge bg-primary">38 Students</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked onchange="toggleCourseStatus(3, this)">
                                                <label class="form-check-label small text-success fw-semibold">Active</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="viewCourse(3)" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" onclick="editCourse(3)" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="deleteCourse(3)" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-secondary">HSK-NC2</span></td>
                                        <td><strong>Housekeeping NC II</strong></td>
                                        <td><span class="badge bg-info">TB5</span></td>
                                        <td>2 Months</td>
                                        <td><span class="badge bg-primary">28 Students</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" onchange="toggleCourseStatus(4, this)">
                                                <label class="form-check-label small text-danger fw-semibold">Inactive</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="viewCourse(4)" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" onclick="editCourse(4)" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="deleteCourse(4)" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
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
                                        <th>Duration</th>
                                        <th>Students</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-secondary">CSS-NC2</span></td>
                                        <td><strong>Caregiving NC II</strong></td>
                                        <td>3 Months</td>
                                        <td><span class="badge bg-primary">45 Students</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                                <label class="form-check-label small text-success fw-semibold">Active</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary"><i class="bi bi-eye"></i></button>
                                                <button class="btn btn-outline-success"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- More TB5 courses would be listed here -->
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
                                        <th>Duration</th>
                                        <th>Students</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-secondary">COK-NC2</span></td>
                                        <td><strong>Commercial Cooking NC II</strong></td>
                                        <td>3 Months</td>
                                        <td><span class="badge bg-primary">38 Students</span></td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                                <label class="form-check-label small text-success fw-semibold">Active</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary"><i class="bi bi-eye"></i></button>
                                                <button class="btn btn-outline-success"><i class="bi bi-pencil"></i></button>
                                                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- More BBI courses would be listed here -->
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
    <div class="modal-dialog modal-lg">
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
                    
                    <!-- Basic Information -->
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-info-circle me-2"></i>Basic Information
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Course Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="courseCode" placeholder="e.g., CSS-NC2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Course Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="courseName" placeholder="e.g., Caregiving NC II" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select School <span class="text-danger">*</span></label>
                            <select class="form-select" id="courseSchool" required>
                                <option value="">Choose School...</option>
                                <option value="tb5">The Big Five Training and Assessment Center (TB5)</option>
                                <option value="bbi">Big Blossom Institute Inc. (BBI)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Duration <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="courseDuration" placeholder="e.g., 3 Months" required>
                        </div>
                    </div>

                    <!-- Course Details -->
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-text-paragraph me-2"></i>Course Details
                    </h6>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="courseDescription" rows="4" placeholder="Enter course description..."></textarea>
                    </div>

                    <!-- Status -->
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-toggle-on me-2"></i>Availability Status
                    </h6>
                    <div class="card bg-light border-0 mb-3">
                        <div class="card-body">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="courseStatus" checked>
                                <label class="form-check-label fw-semibold" for="courseStatus">
                                    Course is Active and Available for Enrollment
                                </label>
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
    
    if (checkbox.checked) {
        label.textContent = 'Active';
        label.classList.remove('text-danger');
        label.classList.add('text-success');
        console.log(`Course ${courseId} activated`);
    } else {
        label.textContent = 'Inactive';
        label.classList.remove('text-success');
        label.classList.add('text-danger');
        console.log(`Course ${courseId} deactivated`);
    }
}

// View course details
function viewCourse(courseId) {
    alert('Viewing course #' + courseId);
    // Implement view logic
}

// Edit course
function editCourse(courseId) {
    document.getElementById('modalTitle').textContent = 'Edit Course';
    
    // Populate form with existing data (demo data)
    document.getElementById('courseId').value = courseId;
    document.getElementById('courseCode').value = 'CSS-NC2';
    document.getElementById('courseName').value = 'Caregiving NC II';
    document.getElementById('courseSchool').value = 'tb5';
    document.getElementById('courseDuration').value = '3 Months';
    document.getElementById('courseDescription').value = 'This is a sample course description.';
    document.getElementById('courseStatus').checked = true;
    
    const modal = new bootstrap.Modal(document.getElementById('courseModal'));
    modal.show();
}

// Delete course
function deleteCourse(courseId) {
    if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
        alert('Course #' + courseId + ' deleted successfully!');
        // Implement delete logic
    }
}

// Save course (create or update)
function saveCourse() {
    const form = document.getElementById('courseForm');
    
    if (form.checkValidity()) {
        const courseData = {
            id: document.getElementById('courseId').value,
            code: document.getElementById('courseCode').value,
            name: document.getElementById('courseName').value,
            school: document.getElementById('courseSchool').value,
            duration: document.getElementById('courseDuration').value,
            description: document.getElementById('courseDescription').value,
            status: document.getElementById('courseStatus').checked ? 'active' : 'inactive'
        };
        
        console.log('Course Data:', courseData);
        
        if (courseData.id) {
            alert('Course updated successfully!');
        } else {
            alert('Course created successfully!');
        }
        
        // Hide modal
        bootstrap.Modal.getInstance(document.getElementById('courseModal')).hide();
        
        // Reset form
        form.reset();
    } else {
        form.reportValidity();
    }
}

// Apply filters
function applyFilters() {
    const search = document.getElementById('searchCourse').value;
    const school = document.getElementById('filterSchool').value;
    const status = document.getElementById('filterStatus').value;
    
    console.log('Applying filters:', { search, school, status });
    alert('Filters applied! (Demo)');
}
</script>

<?php
// Include footer
include('../footer/footer.php');
?>