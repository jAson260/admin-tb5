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
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Search Batch</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Batch name or ID..." id="searchBatch" onkeyup="applyFilters()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Filter by School</label>
                        <select class="form-select" id="filterSchool" onchange="applyFilters()">
                            <option value="">All Schools</option>
                            <option value="tb5">TB5</option>
                            <option value="bbi">BBI</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Filter by Status</label>
                        <select class="form-select" id="filterStatus" onchange="applyFilters()">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">&nbsp;</label>
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
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
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table class="table table-hover align-middle" id="batchesTable" style="width:100%">
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
                            <!-- DataTables will populate this -->
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
let batchesTable;

$(document).ready(function() {
    // Initialize DataTable
    batchesTable = $('#batchesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get-batches.php',
            type: 'POST',
            data: function(d) {
                // Add custom filter parameters
                d.schoolFilter = $('#filterSchool').val();
                d.statusFilter = $('#filterStatus').val();
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable Error:', error, thrown);
            }
        },
        columns: [
            { 
                data: 'BatchCode',
                render: function(data) {
                    return `<span class="badge bg-secondary">${data}</span>`;
                }
            },
            { 
                data: 'BatchName',
                render: function(data) {
                    return `<strong>${data}</strong>`;
                }
            },
            { 
                data: 'School',
                render: function(data) {
                    const badgeClass = data === 'TB5' ? 'bg-info' : 'bg-warning';
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    return `${row.CourseCode || ''} - ${row.CourseName || ''}`;
                }
            },
            { 
                data: 'CurrentStudents',
                render: function(data) {
                    return `<span class="badge bg-primary">${data || 0} Students</span>`;
                }
            },
            { 
                data: 'StartDate',
                render: function(data) {
                    return formatDate(data);
                }
            },
            { 
                data: 'EndDate',
                render: function(data) {
                    return formatDate(data);
                }
            },
            { 
                data: 'Status',
                render: function(data) {
                    const badgeClass = data === 'Active' ? 'bg-success' : 'bg-secondary';
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="viewBatch(${row.Id})" title="View">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-success" onclick="editBatch(${row.Id})" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteBatch(${row.Id})" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'desc']],
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6">>rtip',
        language: {
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ batches",
            infoEmpty: "No batches found",
            infoFiltered: "(filtered from _MAX_ total batches)",
            zeroRecords: "No matching batches found",
            emptyTable: "No batches available",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        },
        drawCallback: function(settings) {
            updateStats();
        }
    });

    // Custom search - use DataTable's built-in search
    let searchTimeout;
    $('#searchBatch').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            batchesTable.search($('#searchBatch').val()).draw();
        }, 500);
    });

    // Filter change events
    $('#filterSchool, #filterStatus').on('change', function() {
        batchesTable.ajax.reload();
    });
    
    // Initial stats load
    updateStats();
});

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
    
    courseDropdown.innerHTML = '<option value="">Loading courses...</option>';
    courseDropdown.disabled = true;
    
    if (!school) {
        courseDropdown.innerHTML = '<option value="">Choose Course...</option>';
        return;
    }
    
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
                    courseDropdown.appendChild(option);
                });
            } else {
                courseDropdown.innerHTML = '<option value="">No courses available</option>';
            }
        })
        .catch(error => {
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
function saveBatch() {
    const form = document.getElementById('createBatchForm');
    const batchName = document.getElementById('batchName').value.trim();
    const batchSchool = document.getElementById('batchSchool').value;
    const batchCourse = document.getElementById('batchCourse').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    
    let hasError = false;
    
    if (!batchName) {
        document.getElementById('batchName').classList.add('is-invalid');
        hasError = true;
    }
    if (!batchSchool) {
        document.getElementById('batchSchool').classList.add('is-invalid');
        hasError = true;
    }
    if (!batchCourse) {
        document.getElementById('batchCourse').classList.add('is-invalid');
        hasError = true;
    }
    if (!startDate) {
        document.getElementById('startDate').classList.add('is-invalid');
        hasError = true;
    }
    if (!endDate) {
        document.getElementById('endDate').classList.add('is-invalid');
        hasError = true;
    }
    
    if (hasError) {
        alert('Please fill in all required fields!');
        return;
    }

    const editingId = document.getElementById('editingBatchRowId').value;
    const batchId = document.getElementById('batchId').value || generateBatchId();
    const courseSelect = document.getElementById('batchCourse');
    const selectedCourseOption = courseSelect.options[courseSelect.selectedIndex];
    const courseText = selectedCourseOption.textContent;
    const courseCode = courseText.split(' - ')[0].trim();
    const courseName = courseText.split(' - ')[1]?.trim() || courseText;
    
    const batchData = {
        batchId: batchId,
        batchName: batchName,
        school: batchSchool.toUpperCase(),
        courseId: batchCourse,
        courseCode: courseCode,
        courseName: courseName,
        startDate: startDate,
        endDate: endDate,
        description: document.getElementById('batchDescription').value,
        editingId: editingId
    };

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
            bootstrap.Modal.getInstance(document.getElementById('createBatchModal')).hide();
            resetCreateForm();
            batchesTable.ajax.reload();
            updateStats();
        } else {
            alert('Error: ' + (result.message || 'Failed to save batch'));
        }
    })
    .catch(error => {
        alert('Error saving batch. Please try again.');
    });
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
}

function viewBatch(batchId) {
    $.ajax({
        url: 'get-batch-details.php',
        method: 'GET',
        data: { id: batchId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const batch = response.batch;
                const content = `
                    <p><strong>Batch ID:</strong> ${batch.BatchCode}</p>
                    <p><strong>Batch Name:</strong> ${batch.BatchName}</p>
                    <p><strong>School:</strong> ${batch.School}</p>
                    <p><strong>Course:</strong> ${batch.CourseCode} - ${batch.CourseName}</p>
                    <p><strong>Start Date:</strong> ${formatDate(batch.StartDate)}</p>
                    <p><strong>End Date:</strong> ${formatDate(batch.EndDate)}</p>
                    <p><strong>Status:</strong> ${batch.Status}</p>
                    <p><strong>Students:</strong> ${batch.CurrentStudents}/${batch.MaxStudents}</p>
                `;
                document.getElementById('viewBatchContent').innerHTML = content;
                const modal = new bootstrap.Modal(document.getElementById('viewBatchModal'));
                modal.show();
            }
        }
    });
}

function editBatch(batchId) {
    $.ajax({
        url: 'get-batch-details.php',
        method: 'GET',
        data: { id: batchId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const batch = response.batch;
                document.getElementById('editingBatchRowId').value = batchId;
                document.getElementById('batchId').value = batch.BatchCode;
                document.getElementById('batchName').value = batch.BatchName;
                document.getElementById('batchSchool').value = batch.School.toLowerCase();
                loadCourses();
                setTimeout(() => {
                    document.getElementById('batchCourse').value = batch.CourseId;
                }, 300);
                document.getElementById('startDate').value = batch.StartDate;
                document.getElementById('endDate').value = batch.EndDate;
                document.getElementById('batchDescription').value = batch.Description || '';
                document.getElementById('saveBatchBtn').innerHTML = '<i class="bi bi-check-circle me-1"></i>Update Batch';
                const modal = new bootstrap.Modal(document.getElementById('createBatchModal'));
                modal.show();
            }
        }
    });
}

function deleteBatch(batchId) {
    if (!confirm('Are you sure you want to delete this batch?')) return;
    
    $.ajax({
        url: 'delete-batch.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ batch_id: batchId }),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                batchesTable.ajax.reload();
                updateStats();
            } else {
                alert('Error: ' + response.message);
            }
        }
    });
}

function resetFilters() {
    document.getElementById('searchBatch').value = '';
    document.getElementById('filterSchool').value = '';
    document.getElementById('filterStatus').value = '';
    batchesTable.search('').draw();
    batchesTable.ajax.reload();
}

function resetCreateForm() {
    const form = document.getElementById('createBatchForm');
    form.reset();
    document.getElementById('batchCourse').disabled = true;
    document.getElementById('editingBatchRowId').value = '';
}

function updateStats() {
    $.ajax({
        url: 'get-batch-statistics.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success && data.statistics) {
                $('#totalBatchesCount').text(data.statistics.total || 0);
                $('#activeBatchesCount').text(data.statistics.active || 0);
                $('#totalStudentsCount').text(data.statistics.students || 0);
                $('#completedBatchesCount').text(data.statistics.completed || 0);
            }
        }
    });
}
</script>

<style>
.is-invalid {
    border-color: #dc3545 !important;
}
</style>

<?php
// Include footer
include('../footer/footer.php');
?>