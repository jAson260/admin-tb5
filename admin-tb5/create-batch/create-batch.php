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
                                <h3 class="mb-0 fw-bold">24</h3>
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
                                <h3 class="mb-0 fw-bold">12</h3>
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
                                <h3 class="mb-0 fw-bold">342</h3>
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
                                <h3 class="mb-0 fw-bold">8</h3>
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
                <button type="button" class="btn btn-primary" onclick="saveBatch()">
                    <i class="bi bi-check-circle me-1"></i>Create Batch
                </button>
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
    const modal = new bootstrap.Modal(document.getElementById('createBatchModal'));
    modal.show();
}

// Load courses based on school selection
function loadCourses() {
    const school = document.getElementById('batchSchool').value;
    const courseDropdown = document.getElementById('batchCourse');
    
    courseDropdown.innerHTML = '<option value="">Choose Course...</option>';
    
    if (school && courseOptions[school]) {
        courseDropdown.disabled = false;
        courseOptions[school].forEach(course => {
            const option = document.createElement('option');
            option.value = course.value;
            option.textContent = course.text;
            courseDropdown.appendChild(option);
        });
    } else {
        courseDropdown.disabled = true;
    }
}

// Generate random batch ID
function generateBatchId() {
    const year = new Date().getFullYear();
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    const batchId = `BATCH-${year}-${random}`;
    document.getElementById('batchId').value = batchId;
}

// Save batch
function saveBatch() {
    const form = document.getElementById('createBatchForm');
    
    if (form.checkValidity()) {
        const batchData = {
            batchId: document.getElementById('batchId').value || generateBatchId(),
            batchName: document.getElementById('batchName').value,
            school: document.getElementById('batchSchool').value,
            course: document.getElementById('batchCourse').value,
            startDate: document.getElementById('startDate').value,
            endDate: document.getElementById('endDate').value,
            description: document.getElementById('batchDescription').value
        };
        
        console.log('Batch Data:', batchData);
        alert('Batch created successfully! (This is a demo)');
        
        // Hide modal
        bootstrap.Modal.getInstance(document.getElementById('createBatchModal')).hide();
        
        // Reset form
        form.reset();
        document.getElementById('batchCourse').disabled = true;
    } else {
        form.reportValidity();
    }
}

// View batch details
function viewBatch(batchId) {
    alert('View batch #' + batchId);
}

// Edit batch
function editBatch(batchId) {
    alert('Edit batch #' + batchId);
}

// Delete batch
function deleteBatch(batchId) {
    if (confirm('Are you sure you want to delete this batch?')) {
        alert('Batch #' + batchId + ' deleted');
    }
}

// Apply filters
function applyFilters() {
    const search = document.getElementById('searchBatch').value;
    const school = document.getElementById('filterSchool').value;
    const status = document.getElementById('filterStatus').value;
    
    console.log('Applying filters:', { search, school, status });
    alert('Filters applied! (This is a demo)');
}
</script>

<?php
// Include footer
include('../footer/footer.php');
?>