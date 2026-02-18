<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<!-- Main content area -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">User Dashboard</h2>
                <p class="text-muted">Welcome back! Manage your training and assessments here.</p>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-check-circle text-primary fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Enrollment Status</h6>
                            <small class="text-success fw-bold">Active</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-file-alt text-warning fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Uploaded Docs</h6>
                            <small class="text-muted">5 Files Uploaded</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>