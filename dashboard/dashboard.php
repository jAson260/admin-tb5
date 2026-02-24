<?php 
session_start();
require_once('../includes/rbac-guard.php');


// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once('../db-connect.php');

// 1. Unified path detection for nested folders
$currentPath = $_SERVER['PHP_SELF']; 
$currentDir = basename(dirname($currentPath));
$subfolders = ['dashboard', 'enrollment', 'history', 'register', 'useraccount', 'forgot password', 'upload'];
$root = in_array($currentDir, $subfolders) ? '../' : '';

include '../includes/header.php'; 
include '../includes/sidebar.php'; 

// Fetch logged-in user's name
$trainee_name = "TRAINEE";
$account_status = "Pending";

try {
    $stmt = $pdo->prepare("
        SELECT FirstName, LastName, MiddleName, Status
        FROM studentinfos 
        WHERE Id = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch();
    
    if ($userData) {
        $trainee_name = strtoupper(trim($userData['FirstName'] . ' ' . ($userData['MiddleName'] ? substr($userData['MiddleName'], 0, 1) . '. ' : '') . $userData['LastName']));
        $account_status = $userData['Status'];
    }
} catch(PDOException $e) {
    // Keep default name if error occurs
}

// MOCK DATA for other stats (keep existing)
$current_course = "BREAD AND PASTRY PRODUCTION NC II";
$docs_uploaded = 3;
$docs_needed = 4;
$enrollment_progress = 75; // Percent
?>

<style>
    /* Trainee Dashboard Unique Styles */
    .welcome-card {
        background: linear-gradient(135deg, var(--royal-blue) 0%, #2e51b8 100%);
        color: white;
        border-radius: 1.5rem;
        padding: 30px;
        position: relative;
        overflow: hidden;
    }
    .welcome-card::after {
        content: '\f19d'; /* Graduation Cap Icon */
        font-family: "Font Awesome 5 Free"; font-weight: 900;
        position: absolute; right: -20px; bottom: -20px;
        font-size: 150px; color: rgba(255,255,255,0.1);
        transform: rotate(-15deg);
    }
    
    .status-widget { border: none; border-radius: 1rem; transition: 0.3s; }
    .status-widget:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important; }

    .step-dot { height: 10px; width: 10px; background-color: #ddd; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .step-active { background-color: var(--royal-blue); box-shadow: 0 0 0 3px rgba(65, 105, 225, 0.2); }
    
    .bg-success-light { background-color: #d4edda !important; }
</style>

<div class="main-content">
    <div class="container-fluid">
        
        <!-- SECTION 1: WELCOME & IDENTITY -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="welcome-card shadow-sm mb-4">
                    <h1 class="fw-bold mb-1 text-uppercase">Hello, <?php echo htmlspecialchars($trainee_name); ?>!</h1>
                    <p class="mb-3 opacity-75">Welcome back to your Training & Assessment portal.</p>
                    <div class="d-inline-flex align-items-center <?php echo ($account_status === 'Approved') ? 'bg-white bg-opacity-25' : 'bg-warning bg-opacity-50'; ?> p-2 px-3 rounded-pill">
                        <span class="small fw-bold">
                            <?php echo ($account_status === 'Approved') ? 'TRAINEE ACCOUNT VERIFIED' : 'ACCOUNT ' . strtoupper($account_status); ?>
                        </span>
                        <i class="fas <?php echo ($account_status === 'Approved') ? 'fa-check-circle' : 'fa-clock'; ?> ms-2"></i>
                    </div>
                </div>
            </div>
        </div>

   <div class="row mb-4">
            <!-- Course Summary Card -->
            <div class="col-lg-6 mb-3">
                <div class="card status-widget shadow-sm h-100 p-3">
                    <div class="card-body">
                        <h6 class="text-muted fw-bold small text-uppercase">Active Qualification</h6>
                        <h4 class="fw-bold text-dark mt-2 mb-3"><?php echo $current_course; ?></h4>
                        <div class="d-flex align-items-center mb-1">
                            <span class="small fw-bold text-royal">Enrollment Progress</span>
                            <span class="ms-auto small fw-bold text-royal"><?php echo $enrollment_progress; ?>%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-royal" role="progressbar" style="width: <?php echo $enrollment_progress; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Stat -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card status-widget shadow-sm h-100 p-3 border-start border-royal border-4">
                    <div class="card-body text-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                            <i class="fas fa-file-alt text-royal"></i>
                        </div>
                        <h3 class="fw-bold mb-0"><?php echo $docs_uploaded . "/" . $docs_needed; ?></h3>
                        <small class="text-muted">Docs Uploaded</small>
                    </div>
                </div>
            </div>

            <!-- UPDATED: USERS MANUAL / GUIDE BUTTON -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card status-widget clickable shadow-sm h-100 p-3 border-start border-info border-4" data-bs-toggle="modal" data-bs-target="#manualModal">
                    <div class="card-body text-center">
                        <div class="bg-info bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                            <i class="fas fa-book-open text-info"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">User Guide</h5>
                        <small class="text-muted">How to Upload?</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- ENROLLMENT PATHWAY -->
            <div class="col-lg-7 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold text-royal"><i class="fas fa-list-ol me-2"></i>Next Action Items</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center border-0 px-0">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <span>Account Setup Complete</span>
                                <span class="ms-auto badge bg-light text-success fw-bold">Done</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-0 px-0">
                                <div class="step-dot step-active"></div>
                                <span>Upload PSA, TOR, & Diploma</span>
                                <span class="ms-auto"><a href="<?php echo $root; ?>upload/upload.php" class="text-royal small fw-bold">Submit Files</a></span>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-0 px-0">
                                <div class="step-dot"></div>
                                <span class="text-muted">Pending Admin Review</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- RECENT SYSTEM ACTIVITY -->
            <div class="col-lg-5 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold text-royal"><i class="fas fa-stream me-2"></i>Status Updates</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="alert bg-light border-0 small text-dark p-2 mb-2 d-flex">
                            <i class="fas fa-clock me-2 mt-1 text-muted"></i>
                            <div><strong>Current:</strong> No document issues found.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MANUAL MODAL (The User Guide Window) -->
<div class="modal fade" id="manualModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 bg-info bg-opacity-10 rounded-top-4">
                <h5 class="modal-title fw-bold text-info"><i class="fas fa-book me-2"></i>Submission Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 d-flex">
                    <div class="fw-bold text-info me-3 h4">01</div>
                    <div>
                        <h6 class="fw-bold">Go to Upload Module</h6>
                        <p class="small text-muted">Select <strong>"Upload Documents"</strong> from the side navigation menu on the left.</p>
                    </div>
                </div>
                <div class="mb-4 d-flex">
                    <div class="fw-bold text-info me-3 h4">02</div>
                    <div>
                        <h6 class="fw-bold">Pick your File</h6>
                        <p class="small text-muted">Find the specific requirement (e.g., PSA Certificate) and click the **Sync/Change** icon.</p>
                    </div>
                </div>
                <div class="mb-0 d-flex">
                    <div class="fw-bold text-info me-3 h4">03</div>
                    <div>
                        <h6 class="fw-bold">Wait for Verification</h6>
                        <p class="small text-muted">After submission, the status will change to <strong>Pending</strong>. Admins typically review files within 24-48 hours.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-royal rounded-pill px-5 shadow-sm" onclick="window.location.href='<?php echo $root; ?>upload/upload.php'">Go to Upload <i class="fas fa-arrow-right ms-1"></i></button>
            </div>
        </div>
    </div>
</div>


<?php include '../includes/footer.php'; ?>