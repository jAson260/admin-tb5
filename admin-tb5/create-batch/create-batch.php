<?php
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
                                <h3 class="mb-0 fw-bold" id="totalBatches">0</h3>
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
                                <h3 class="mb-0 fw-bold" id="activeBatches">0</h3>
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
                                <h3 class="mb-0 fw-bold" id="totalStudents">0</h3>
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
                                <h3 class="mb-0 fw-bold" id="completedBatches">0</h3>
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
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
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
                    <table class="table table-hover align-middle" id="batchesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Batch Code</th>
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
                            <!-- Data loaded via DataTables -->
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
                    <!-- Batch Code Section -->
                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-hash me-2"></i>Batch Code Configuration
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Batch Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="batchCode" placeholder="e.g., BATCH-2024-001" required>
                                    <small class="text-muted">Unique identifier for this batch</small>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-primary w-100" onclick="generateBatchCode()">
                                        <i class="bi bi-shuffle me-1"></i>Generate
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
                            <input type="text" class="form-control" id="batchName" placeholder="e.g., Housekeeping January 2024" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select School <span class="text-danger">*</span></label>
                            <select class="form-select" id="batchSchool" onchange="loadCourses()" required>
                                <option value="">Choose School...</option>
                                <option value="TB5">The Big Five Training and Assessment Center (TB5)</option>
                                <option value="BBI">Big Blossom Institute Inc. (BBI)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Select Course <span class="text-danger">*</span></label>
                            <select class="form-select" id="batchCourse" disabled required>
                                <option value="">Choose Course...</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Maximum Students</label>
                            <input type="number" class="form-control" id="maxStudents" value="30" min="10" max="100">
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
                <button type="button" class="btn btn-primary" onclick="saveBatch()">
                    <i class="bi bi-check-circle me-1"></i>Create Batch
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Batch Details Modal -->
<div class="modal fade" id="viewBatchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Batch Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="batchDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let batchesTable;

$(document).ready(function() {
    // Load statistics
    loadStatistics();
    
    // Initialize DataTable
    batchesTable = $('#batchesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get-batches.php',
            type: 'POST',
            data: function(d) {
                d.school = $('#filterSchool').val();
                d.status = $('#filterStatus').val();
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
                data: 'CourseName',
                render: function(data, type, row) {
                    return `${data} (${row.CourseCode})`;
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    const percentage = row.MaxStudents > 0 ? (row.CurrentStudents / row.MaxStudents * 100).toFixed(0) : 0;
                    return `<span class="badge bg-primary">${row.CurrentStudents} / ${row.MaxStudents}</span>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar" style="width: ${percentage}%"></div>
                            </div>`;
                }
            },
            { data: 'StartDate' },
            { data: 'EndDate' },
            { 
                data: 'Status',
                render: function(data) {
                    const badges = {
                        'Active': 'bg-success',
                        'Pending': 'bg-warning',
                        'Completed': 'bg-secondary',
                        'Cancelled': 'bg-danger'
                    };
                    return `<span class="badge ${badges[data] || 'bg-secondary'}">${data}</span>`;
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="viewBatch(${row.Id})" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteBatch(${row.Id}, '${escapeHtml(row.BatchName)}')" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        pageLength: 10,
        order: [[0, 'desc']],
        language: {
            emptyTable: "No batches found",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
        }
    });
    
    // Search functionality
    $('#searchBatch').on('keyup', function() {
        batchesTable.search(this.value).draw();
    });
});

// Load statistics
function loadStatistics() {
    $.ajax({
        url: 'get-batch-statistics.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                $('#totalBatches').text(data.statistics.total);
                $('#activeBatches').text(data.statistics.active);
                $('#totalStudents').text(data.statistics.students);
                $('#completedBatches').text(data.statistics.completed);
            }
        }
    });
}

// Show create batch modal
function showCreateBatchModal() {
    $('#createBatchForm')[0].reset();
    $('#batchCourse').prop('disabled', true);
    generateBatchCode(); // Auto-generate batch code
    const modal = new bootstrap.Modal(document.getElementById('createBatchModal'));
    modal.show();
}

// Generate batch code
function generateBatchCode() {
    const year = new Date().getFullYear();
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    const batchCode = `BATCH-${year}-${random}`;
    document.getElementById('batchCode').value = batchCode;
}

// Load courses based on school
function loadCourses() {
    const school = document.getElementById('batchSchool').value;
    const courseDropdown = document.getElementById('batchCourse');
    
    if (!school) {
        courseDropdown.disabled = true;
        courseDropdown.innerHTML = '<option value="">Choose Course...</option>';
        return;
    }
    
    $.ajax({
        url: 'get-courses-by-school.php',
        method: 'GET',
        data: { school: school },
        dataType: 'json',
        success: function(data) {
            courseDropdown.innerHTML = '<option value="">Choose Course...</option>';
            
            if (data.success && data.courses.length > 0) {
                courseDropdown.disabled = false;
                data.courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.Id;
                    option.textContent = `${course.CourseName} (${course.Category}) - ${course.Duration}`;
                    option.dataset.maxStudents = course.MaxStudents;
                    courseDropdown.appendChild(option);
                });
            } else {
                courseDropdown.disabled = true;
            }
        }
    });
}

// Update max students when course is selected
$('#batchCourse').on('change', function() {
    const selectedOption = $(this).find(':selected');
    const maxStudents = selectedOption.data('maxStudents');
    if (maxStudents) {
        $('#maxStudents').val(maxStudents);
    }
});

// Save batch
function saveBatch() {
    const form = document.getElementById('createBatchForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const batchData = {
        batch_code: $('#batchCode').val(),
        batch_name: $('#batchName').val(),
        school: $('#batchSchool').val(),
        course_id: $('#batchCourse').val(),
        start_date: $('#startDate').val(),
        end_date: $('#endDate').val(),
        description: $('#batchDescription').val(),
        max_students: $('#maxStudents').val()
    };
    
    $.ajax({
        url: 'save-batch.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(batchData),
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                
                bootstrap.Modal.getInstance(document.getElementById('createBatchModal')).hide();
                batchesTable.ajax.reload();
                loadStatistics();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to create batch'
            });
        }
    });
}

// View batch details
function viewBatch(batchId) {
    const modal = new bootstrap.Modal(document.getElementById('viewBatchModal'));
    modal.show();
    
    $.ajax({
        url: 'get-batch-details.php',
        method: 'GET',
        data: { batch_id: batchId },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                const batch = data.batch;
                const statusBadge = {
                    'Active': 'bg-success',
                    'Pending': 'bg-warning',
                    'Completed': 'bg-secondary',
                    'Cancelled': 'bg-danger'
                }[batch.Status] || 'bg-secondary';
                
                const html = `
                    <div class="row g-3">
                        <div class="col-12">
                            <h5 class="fw-bold">${batch.BatchName}</h5>
                            <span class="badge ${statusBadge} mb-3">${batch.Status}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Batch Code</label>
                            <p class="fw-bold">${batch.BatchCode}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">School</label>
                            <p class="fw-bold">${batch.School}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="text-muted small">Course</label>
                            <p class="fw-bold">${batch.CourseName} (${batch.CourseCode})</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Start Date</label>
                            <p class="fw-bold">${batch.StartDate}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">End Date</label>
                            <p class="fw-bold">${batch.EndDate}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Students Enrolled</label>
                            <p class="fw-bold">${batch.CurrentStudents} / ${batch.MaxStudents}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Duration</label>
                            <p class="fw-bold">${batch.Duration} (${batch.DurationHours} hours)</p>
                        </div>
                        ${batch.Description ? `
                        <div class="col-12">
                            <label class="text-muted small">Description</label>
                            <p>${batch.Description}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
                
                $('#batchDetailsContent').html(html);
            }
        }
    });
}

// Delete batch
function deleteBatch(batchId, batchName) {
    Swal.fire({
        title: 'Delete Batch?',
        text: `Are you sure you want to delete "${batchName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'delete-batch.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ batch_id: batchId }),
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        Swal.fire('Deleted!', data.message, 'success');
                        batchesTable.ajax.reload();
                        loadStatistics();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                }
            });
        }
    });
}

// Apply filters
function applyFilters() {
    batchesTable.ajax.reload();
}

// Helper function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
</script>

<?php
// Include footer
include('../footer/footer.php');
?>