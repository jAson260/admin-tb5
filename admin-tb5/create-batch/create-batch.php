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
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-collection text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Total Batches</h6>
                                <h3 class="mb-0 fw-bold"><span id="totalBatchesCount">0</span></h3>
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
                                <i class="bi bi-play-circle text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Active Batches</h6>
                                <h3 class="mb-0 fw-bold"><span id="activeBatchesCount">0</span></h3>
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
                                <i class="bi bi-people text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Total Students</h6>
                                <h3 class="mb-0 fw-bold"><span id="totalStudentsCount">0</span></h3>
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
                                <i class="bi bi-check-circle text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small">Completed</h6>
                                <h3 class="mb-0 fw-bold"><span id="completedBatchesCount">0</span></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Search Batch</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Batch name or ID..." id="searchBatch">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Filter by School</label>
                        <select class="form-select" id="filterSchool">
                            <option value="">All Schools</option>
                            <option value="tb5">TB5</option>
                            <option value="bbi">BBI</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Filter by Status</label>
                        <select class="form-select" id="filterStatus">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
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

        <!-- Batches Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">All Batches</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Batch ID</th>
                                <th>Batch Name</th>
                                <th>School</th>
                                <th>Course</th>
                                <th>Students</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-secondary">BATCH-2024-001</span></td>
                                <td><strong>CSS Batch January 2024</strong></td>
                                <td><span class="badge bg-info">TB5</span></td>
                                <td>CSS - Caregiving</td>
                                <td>
                                    <span class="badge bg-primary">25 Students</span>
                                </td>
                                <td>Jan 15, 2024</td>
                                <td>Mar 15, 2024</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="viewBatch(1)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success" onclick="editBatch(1)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteBatch(1)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-secondary">BATCH-2024-002</span></td>
                                <td><strong>BPP February Batch</strong></td>
                                <td><span class="badge bg-info">TB5</span></td>
                                <td>BPP - Bread & Pastry</td>
                                <td>
                                    <span class="badge bg-primary">18 Students</span>
                                </td>
                                <td>Feb 01, 2024</td>
                                <td>Apr 01, 2024</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="viewBatch(2)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success" onclick="editBatch(2)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteBatch(2)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-secondary">BATCH-2023-045</span></td>
                                <td><strong>COK December 2023</strong></td>
                                <td><span class="badge bg-warning">BBI</span></td>
                                <td>COK - Commercial Cooking</td>
                                <td>
                                    <span class="badge bg-primary">30 Students</span>
                                </td>
                                <td>Dec 01, 2023</td>
                                <td>Feb 01, 2024</td>
                                <td><span class="badge bg-secondary">Completed</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="viewBatch(3)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success" onclick="editBatch(3)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteBatch(3)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Batch Modal -->
<div class="modal fade" id="createBatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-plus-circle me-2"></i>Create New Batch
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createBatchForm">
                    <input type="hidden" id="editingBatchRowId" value="">
                    <!-- Batch ID Section -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-hash me-2"></i>Batch ID Configuration
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Batch ID</label>
                                    <input type="text" class="form-control" id="batchId" placeholder="e.g., BATCH-2024-001">
                                    <small class="text-muted">Leave empty for auto-generated ID</small>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary w-100" onclick="generateBatchId()">
                                        <i class="bi bi-shuffle me-1"></i>Generate ID
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Batch Information -->
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-info-circle me-2"></i>Batch Information
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Batch Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="batchName" placeholder="e.g., CSS Batch January 2024" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select School <span class="text-danger">*</span></label>
                            <select class="form-select" id="batchSchool" onchange="loadCourses()" required>
                                <option value="">Choose School...</option>
                                <option value="tb5">The Big Five Training and Assessment Center (TB5)</option>
                                <option value="bbi">Big Blossom Institute Inc. (BBI)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Course <span class="text-danger">*</span></label>
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
                            <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="startDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="endDate" required>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-text-paragraph me-2"></i>Additional Details
                    </h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description (Optional)</label>
                        <textarea class="form-control" id="batchDescription" rows="3" placeholder="Add notes or description about this batch..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveBatchBtn" onclick="saveBatch()">
                    <i class="bi bi-check-circle me-1"></i>Create Batch
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Batch Modal -->
<div class="modal fade" id="viewBatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batch Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewBatchContent">
                <!-- Filled dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Course options for each school
const courseOptions = {
    tb5: [
        { value: 'css', text: 'CSS - Caregiving' },
        { value: 'bpp', text: 'BPP - Bread & Pastry Production' },
        { value: 'hsk', text: 'HSK - Housekeeping' },
        { value: 'epas', text: 'EPAS - Events Planning and Services' },
        { value: 'tmi', text: 'TMI - Technical Maintenance Installation' }
    ],
    bbi: [
        { value: 'cok', text: 'COK - Commercial Cooking' },
        { value: 'hsk', text: 'HSK - Housekeeping' },
        { value: 'eim', text: 'EIM - Electrical Installation & Maintenance' },
        { value: 'fbs', text: 'FBS - Food & Beverage Services' },
        { value: 'evm', text: 'EVM - Events Management' }
    ]
};

// Show create batch modal
function showCreateBatchModal() {
    resetCreateForm();
    document.getElementById('saveBatchBtn').innerHTML = '<i class="bi bi-check-circle me-1"></i>Create Batch';
    const modal = new bootstrap.Modal(document.getElementById('createBatchModal'));
    modal.show();
}

// Load courses based on school selection
function loadCourses() {
    const school = document.getElementById('batchSchool').value;
    const courseDropdown = document.getElementById('batchCourse');
    
    // Reset dropdown
    courseDropdown.innerHTML = '<option value="">Loading courses...</option>';
    courseDropdown.disabled = true;
    
    if (!school) {
        courseDropdown.innerHTML = '<option value="">Choose Course...</option>';
        return;
    }
    
    // Fetch courses from database
    fetch(`get-courses-by-school.php?school=${school}`)
        .then(response => response.json())
        .then(data => {
            courseDropdown.innerHTML = '<option value="">Choose Course...</option>';
            
            if (data.success && data.courses.length > 0) {
                courseDropdown.disabled = false;
                data.courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.Id;
                    option.textContent = `${course.CourseCode} - ${course.CourseName}`;
                    option.dataset.courseName = course.CourseName;
                    option.dataset.duration = course.Duration || '';
                    option.dataset.maxStudents = course.MaxStudents || '30';
                    courseDropdown.appendChild(option);
                });
            } else {
                courseDropdown.innerHTML = '<option value="">No courses available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading courses:', error);
            courseDropdown.innerHTML = '<option value="">Error loading courses</option>';
        });
}

// Generate random batch ID
function generateBatchId() {
    const year = new Date().getFullYear();
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    const batchId = `BATCH-${year}-${random}`;
    document.getElementById('batchId').value = batchId;
    return batchId;
}

// Save batch
let nextRowId = 1;

function initializeRows() {
    const tbody = document.querySelector('.table tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    rows.forEach((tr, idx) => {
        const rowId = idx + 1;
        tr.dataset.rowId = rowId;
        const cells = tr.querySelectorAll('td');
        tr.dataset.batchId = cells[0].textContent.trim();
        tr.dataset.batchName = cells[1].textContent.trim();
        tr.dataset.school = cells[2].textContent.trim();
        tr.dataset.course = cells[3].textContent.trim();
        const studentsText = cells[4].textContent.trim();
        const studentsNum = parseInt(studentsText, 10) || 0;
        tr.dataset.students = studentsNum;
        tr.dataset.startDate = cells[5].textContent.trim();
        tr.dataset.endDate = cells[6].textContent.trim();
        tr.dataset.status = cells[7].textContent.trim();
        nextRowId = Math.max(nextRowId, rowId + 1);
    });
    updateStats();
}

function saveBatch() {
    const form = document.getElementById('createBatchForm');
    if (!form.checkValidity()) { 
        form.reportValidity(); 
        return; 
    }

    const editingId = document.getElementById('editingBatchRowId').value;
    const batchId = document.getElementById('batchId').value || generateBatchId();
    
    // Get course info
    const courseSelect = document.getElementById('batchCourse');
    const selectedCourseOption = courseSelect.options[courseSelect.selectedIndex];
    const courseId = courseSelect.value;
    const courseName = selectedCourseOption.dataset.courseName || selectedCourseOption.textContent;
    
    const batchData = {
        batchId: batchId,
        batchName: document.getElementById('batchName').value,
        school: document.getElementById('batchSchool').value.toUpperCase(),
        courseId: courseId,
        courseName: courseName,
        startDate: document.getElementById('startDate').value,
        endDate: document.getElementById('endDate').value,
        description: document.getElementById('batchDescription').value
    };

    // Send to backend to save in database
    fetch('save-batch.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(batchData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const tbody = document.querySelector('.table tbody');

            if (editingId) {
                // Update existing row
                const tr = tbody.querySelector(`tr[data-row-id="${editingId}"]`);
                if (tr) {
                    const cells = tr.querySelectorAll('td');
                    cells[0].innerHTML = `<span class="badge bg-secondary">${batchData.batchId}</span>`;
                    cells[1].innerHTML = `<strong>${batchData.batchName}</strong>`;
                    cells[2].innerHTML = `<span class="badge bg-info">${batchData.school}</span>`;
                    cells[3].textContent = batchData.courseName;
                    cells[4].innerHTML = `<span class="badge bg-primary">0 Students</span>`;
                    cells[5].textContent = batchData.startDate;
                    cells[6].textContent = batchData.endDate;
                    cells[7].innerHTML = `<span class="badge bg-success">Active</span>`;

                    tr.dataset.batchId = batchData.batchId;
                    tr.dataset.batchName = batchData.batchName;
                    tr.dataset.school = batchData.school;
                    tr.dataset.courseId = batchData.courseId;
                    tr.dataset.course = batchData.courseName;
                    tr.dataset.students = 0;
                    tr.dataset.startDate = batchData.startDate;
                    tr.dataset.endDate = batchData.endDate;
                    tr.dataset.status = 'Active';
                }
            } else {
                // Create new row
                const rowId = result.batchId || nextRowId++;
                const tr = document.createElement('tr');
                tr.dataset.rowId = rowId;
                tr.dataset.batchId = batchData.batchId;
                tr.dataset.batchName = batchData.batchName;
                tr.dataset.school = batchData.school;
                tr.dataset.courseId = batchData.courseId;
                tr.dataset.course = batchData.courseName;
                tr.dataset.startDate = batchData.startDate;
                tr.dataset.endDate = batchData.endDate;
                tr.dataset.status = 'Active';
                tr.dataset.students = 0;

                tr.innerHTML = `
                    <td><span class="badge bg-secondary">${batchData.batchId}</span></td>
                    <td><strong>${batchData.batchName}</strong></td>
                    <td><span class="badge bg-info">${batchData.school}</span></td>
                    <td>${batchData.courseName}</td>
                    <td><span class="badge bg-primary">0 Students</span></td>
                    <td>${batchData.startDate}</td>
                    <td>${batchData.endDate}</td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="viewBatch(${rowId})">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-success" onclick="editBatch(${rowId})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteBatch(${rowId})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                `;

                tbody.prepend(tr);
            }

            bootstrap.Modal.getInstance(document.getElementById('createBatchModal')).hide();
            resetCreateForm();
            updateStats();
            
            // Show success message
            alert(result.message || 'Batch saved successfully!');
        } else {
            alert('Error: ' + (result.message || 'Failed to save batch'));
        }
    })
    .catch(error => {
        console.error('Error saving batch:', error);
        alert('Error saving batch. Please try again.');
    });
}

// View batch details
function viewBatch(batchId) {
    const tr = document.querySelector(`.table tbody tr[data-row-id="${batchId}"]`);
    if (!tr) { alert('Batch not found'); return; }
    const content = `
        <p><strong>Batch ID:</strong> ${tr.dataset.batchId}</p>
        <p><strong>Batch Name:</strong> ${tr.dataset.batchName}</p>
        <p><strong>School:</strong> ${tr.dataset.school}</p>
        <p><strong>Course:</strong> ${tr.dataset.course}</p>
        <p><strong>Start Date:</strong> ${tr.dataset.startDate}</p>
        <p><strong>End Date:</strong> ${tr.dataset.endDate}</p>
        <p><strong>Status:</strong> ${tr.dataset.status}</p>
    `;
    document.getElementById('viewBatchContent').innerHTML = content;
    const modal = new bootstrap.Modal(document.getElementById('viewBatchModal'));
    modal.show();
}

// Edit batch
function editBatch(batchId) {
    const tr = document.querySelector(`.table tbody tr[data-row-id="${batchId}"]`);
    if (!tr) { alert('Batch not found'); return; }
    // populate form
    document.getElementById('editingBatchRowId').value = batchId;
    document.getElementById('batchId').value = tr.dataset.batchId || '';
    document.getElementById('batchName').value = tr.dataset.batchName || '';
    document.getElementById('batchSchool').value = tr.dataset.school.toLowerCase() || '';
    loadCourses();
    setTimeout(() => {
        document.getElementById('batchCourse').value = tr.dataset.course || '';
    }, 50);
    document.getElementById('startDate').value = tr.dataset.startDate || '';
    document.getElementById('endDate').value = tr.dataset.endDate || '';
    document.getElementById('batchDescription').value = tr.dataset.description || '';
    document.getElementById('saveBatchBtn').innerHTML = '<i class="bi bi-check-circle me-1"></i>Update Batch';
    const modal = new bootstrap.Modal(document.getElementById('createBatchModal'));
    modal.show();
}

// Delete batch
function deleteBatch(batchId) {
    if (!confirm('Are you sure you want to delete this batch?')) return;
    const tr = document.querySelector(`.table tbody tr[data-row-id="${batchId}"]`);
    if (tr) tr.remove();
    updateStats();
}

// Apply filters
function applyFilters() {
    const search = document.getElementById('searchBatch').value.toLowerCase();
    const school = document.getElementById('filterSchool').value.toLowerCase();
    const status = document.getElementById('filterStatus').value.toLowerCase();
    const rows = document.querySelectorAll('.table tbody tr');
    rows.forEach(tr => {
        const name = (tr.dataset.batchName || '').toLowerCase();
        const rowSchool = (tr.dataset.school || '').toLowerCase();
        const rowStatus = (tr.dataset.status || '').toLowerCase();
        const matchesSearch = !search || name.includes(search) || (tr.dataset.batchId || '').toLowerCase().includes(search);
        const matchesSchool = !school || rowSchool.includes(school);
        const matchesStatus = !status || rowStatus.includes(status);
        tr.style.display = (matchesSearch && matchesSchool && matchesStatus) ? '' : 'none';
    });
    // Optionally update visible stats when filters are applied
    updateStats();
}

function resetCreateForm() {
    const form = document.getElementById('createBatchForm');
    form.reset();
    document.getElementById('batchCourse').disabled = true;
    document.getElementById('editingBatchRowId').value = '';
}

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    initializeRows();
});

// Update the stats cards from table data
function updateStats() {
    const rows = Array.from(document.querySelectorAll('.table tbody tr'));
    // count only visible rows
    const visibleRows = rows.filter(r => r.style.display !== 'none');
    const total = visibleRows.length;
    let active = 0, completed = 0, students = 0;
    visibleRows.forEach(r => {
        const status = (r.dataset.status || '').toLowerCase();
        if (status.includes('active')) active++;
        if (status.includes('completed')) completed++;
        const s = parseInt(r.dataset.students, 10) || 0;
        students += s;
    });

    document.getElementById('totalBatchesCount').textContent = total;
    document.getElementById('activeBatchesCount').textContent = active;
    document.getElementById('completedBatchesCount').textContent = completed;
    document.getElementById('totalStudentsCount').textContent = students;
}
</script>
</script>

<?php
// Include footer
include('../footer/footer.php');
?>