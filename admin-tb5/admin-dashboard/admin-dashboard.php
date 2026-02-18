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
                    <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                </h2>
                <p class="text-white-50 mb-0">
                    <i class="bi bi-calendar3 me-2"></i>
                    <?php echo date('l, F j, Y'); ?> | 
                    <i class="bi bi-clock ms-2 me-2"></i>
                    <span id="currentTime"></span>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex justify-content-md-end gap-2">
                    <button class="btn btn-light btn-sm">
                        <i class="bi bi-bell-fill me-1"></i>
                        <span class="badge bg-danger">3</span>
                    </button>
                    <button class="btn btn-light btn-sm">
                        <i class="bi bi-gear-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Courses Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-book text-primary" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Courses</h6>
                                <h3 class="mb-0 fw-bold">24</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Batches Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-people-fill text-success" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Active Batches</h6>
                                <h3 class="mb-0 fw-bold">12</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-file-earmark-check text-warning" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Pending Approvals</h6>
                                <h3 class="mb-0 fw-bold">8</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Students Card -->
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-person-fill text-info" style="font-size: 1.5rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Students</h6>
                                <h3 class="mb-0 fw-bold">342</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <a href="../course-creation/course-creation.php" class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-plus-circle" style="font-size: 1.3rem;"></i>
                                    <span class="fw-semibold">Create New Course</span>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="../create-batch/create-batch.php" class="btn btn-outline-success w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-plus-circle" style="font-size: 1.3rem;"></i>
                                    <span class="fw-semibold">Create New Batch</span>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="documents-approval.php" class="btn btn-outline-warning w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-check-circle" style="font-size: 1.3rem;"></i>
                                    <span class="fw-semibold">Review Documents</span>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="tor-grades.php" class="btn btn-outline-info w-100 py-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-clipboard-data" style="font-size: 1.3rem;"></i>
                                    <span class="fw-semibold">Manage TOR Grades</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-circle-fill text-primary me-2" style="font-size: 0.5rem; margin-top: 6px;"></i>
                                    <div>
                                        <p class="mb-0 small">New batch created</p>
                                        <small class="text-muted">2 hours ago</small>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-circle-fill text-success me-2" style="font-size: 0.5rem; margin-top: 6px;"></i>
                                    <div>
                                        <p class="mb-0 small">Document approved</p>
                                        <small class="text-muted">5 hours ago</small>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-circle-fill text-warning me-2" style="font-size: 0.5rem; margin-top: 6px;"></i>
                                    <div>
                                        <p class="mb-0 small">Course updated</p>
                                        <small class="text-muted">1 day ago</small>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-circle-fill text-info me-2" style="font-size: 0.5rem; margin-top: 6px;"></i>
                                    <div>
                                        <p class="mb-0 small">Grades submitted</p>
                                        <small class="text-muted">2 days ago</small>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Documents Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">Pending Document Approvals</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Document Type</th>
                                        <th>Submission Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                </div>
                                                <span class="fw-semibold">Juan Dela Cruz</span>
                                            </div>
                                        </td>
                                        <td>Birth Certificate</td>
                                        <td>Feb 10, 2026</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Review</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                </div>
                                                <span class="fw-semibold">Maria Santos</span>
                                            </div>
                                        </td>
                                        <td>ID Card</td>
                                        <td>Feb 11, 2026</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Review</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                </div>
                                                <span class="fw-semibold">Pedro Reyes</span>
                                            </div>
                                        </td>
                                        <td>Transcript</td>
                                        <td>Feb 12, 2026</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Review</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
// Update current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    document.getElementById('currentTime').textContent = timeString;
}
updateTime();
setInterval(updateTime, 1000);
</script>
    
</div>
<?php
    // Include footer
    include('../footer/footer.php');
    ?>