<?php

session_start();
require_once('../includes/rbac-guard.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once('../db-connect.php');

$currentPath = $_SERVER['PHP_SELF']; 
$currentDir = basename(dirname($currentPath));
$subfolders = ['dashboard', 'enrollment', 'history', 'register', 'useraccount', 'forgot password', 'upload'];
$root = in_array($currentDir, $subfolders) ? '../' : '';

include '../includes/header.php'; 
include '../includes/sidebar.php'; 

$trainee_name = "TRAINEE";
$account_status = "Pending";
$current_course = "No Active Course";
$course_code = "";
$school = "";
$batch_name = "";
$batch_code = "";
$enrollment_status = "";
$enrollment_progress = 0;
$docs_uploaded = 0;
$docs_needed = 4;

try {
    // Get user info
    $stmt = $pdo->prepare("
        SELECT FirstName, LastName, MiddleName, Status
        FROM studentinfos 
        WHERE Id = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userData) {
        $trainee_name = strtoupper(trim(
            $userData['FirstName'] . ' ' . 
            ($userData['MiddleName'] ? substr($userData['MiddleName'], 0, 1) . '. ' : '') . 
            $userData['LastName']
        ));
        $account_status = $userData['Status'];
    }

    // UPDATED: Get enrolled course from enrollments table
    $courseStmt = $pdo->prepare("
        SELECT 
            c.CourseName,
            c.CourseCode,
            c.Duration,
            c.DurationHours,
            b.BatchName,
            b.BatchCode,
            b.StartDate,
            b.EndDate,
            e.School,
            e.Status        AS EnrollmentStatus,
            e.EnrolledAt
        FROM enrollments e
        INNER JOIN courses c ON e.CourseId = c.Id
        INNER JOIN batches b ON e.BatchId = b.Id
        WHERE e.StudentId = ?
        ORDER BY e.EnrolledAt DESC
        LIMIT 1
    ");
    $courseStmt->execute([$_SESSION['user_id']]);
    $courseData = $courseStmt->fetch(PDO::FETCH_ASSOC);

    if ($courseData) {
        $current_course     = strtoupper($courseData['CourseName']);
        $course_code        = $courseData['CourseCode'];
        $batch_name         = $courseData['BatchName'];
        $batch_code         = $courseData['BatchCode'];
        $school             = $courseData['School'];
        $enrollment_status  = $courseData['EnrollmentStatus'];
        $start_date         = $courseData['StartDate'] ? date('M d, Y', strtotime($courseData['StartDate'])) : 'TBA';
        $end_date           = $courseData['EndDate']   ? date('M d, Y', strtotime($courseData['EndDate']))   : 'TBA';
        $enrolled_at        = $courseData['EnrolledAt'] ? date('M d, Y', strtotime($courseData['EnrolledAt'])) : 'N/A';
    }

    // ── Count uploaded documents — only student-uploaded docs ─────────────────
    $docsStmt = $pdo->prepare("
        SELECT 
            (CASE WHEN PSAPath                IS NOT NULL AND PSAPath                != '' THEN 1 ELSE 0 END) +
            (CASE WHEN DiplomaPath            IS NOT NULL AND DiplomaPath            != '' THEN 1 ELSE 0 END) +
            (CASE WHEN Form137Path            IS NOT NULL AND Form137Path            != '' THEN 1 ELSE 0 END) +
            (CASE WHEN MarriageCertificatePath IS NOT NULL AND MarriageCertificatePath != '' THEN 1 ELSE 0 END)
            AS uploaded_count
        FROM documents
        WHERE StudentInfoId = ?
        LIMIT 1
    ");
    $docsStmt->execute([$_SESSION['user_id']]);
    $docsData = $docsStmt->fetch(PDO::FETCH_ASSOC);

    if ($docsData !== false) {
        $docs_uploaded = (int)$docsData['uploaded_count'];
    }

    // Calculate enrollment progress
    if ($docs_needed > 0) {
        $enrollment_progress = round(($docs_uploaded / $docs_needed) * 100);
    }

    if ($account_status === 'Approved') {
        $enrollment_progress = min(100, $enrollment_progress + 20);
    }

} catch(PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
}
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
    .clickable { cursor: pointer; }

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

                        <?php if ($current_course !== 'No Active Course'): ?>
                            <h4 class="fw-bold text-dark mt-2 mb-1">
                                <?php echo htmlspecialchars($current_course); ?>
                            </h4>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <?php if ($course_code): ?>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-book me-1"></i><?php echo htmlspecialchars($course_code); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($school): ?>
                                    <span class="badge <?php echo $school === 'TB5' ? 'bg-info' : 'bg-warning'; ?>">
                                        <i class="bi bi-bank me-1"></i><?php echo htmlspecialchars($school); ?>
                                    </span>
                                <?php endif; ?>
                                <?php if ($enrollment_status): ?>
                                    <?php
                                        $statusColor = match($enrollment_status) {
                                            'Enrolled'  => 'bg-success',
                                            'Ongoing'   => 'bg-primary',
                                            'Completed' => 'bg-success',
                                            'Dropped'   => 'bg-danger',
                                            'Failed'    => 'bg-danger',
                                            default     => 'bg-secondary'
                                        };
                                    ?>
                                    <span class="badge <?php echo $statusColor; ?>">
                                        <i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i><?php echo htmlspecialchars($enrollment_status); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Batch Info -->
                            <?php if ($batch_name): ?>
                                <div class="card bg-light border-0 mb-3 p-2">
                                    <div class="row g-2 small">
                                        <div class="col-6">
                                            <span class="text-muted">Batch</span><br>
                                            <strong><?php echo htmlspecialchars($batch_code); ?> - <?php echo htmlspecialchars($batch_name); ?></strong>
                                        </div>
                                        <div class="col-3">
                                            <span class="text-muted">Start</span><br>
                                            <strong><?php echo $start_date; ?></strong>
                                        </div>
                                        <div class="col-3">
                                            <span class="text-muted">End</span><br>
                                            <strong><?php echo $end_date; ?></strong>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="text-center py-3">
                                <i class="bi bi-journal-x text-muted" style="font-size:2.5rem;"></i>
                                <p class="text-muted mt-2 mb-0">No active course enrollment</p>
                                <small class="text-muted">Please wait for admin to assign you a course</small>
                            </div>
                        <?php endif; ?>

                        <!-- Progress Bar -->
                        <div class="d-flex align-items-center mb-1">
                            <span class="small fw-bold text-royal">Enrollment Progress</span>
                            <span class="ms-auto small fw-bold text-royal"><?php echo $enrollment_progress; ?>%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar bg-royal" role="progressbar" style="width: <?php echo $enrollment_progress; ?>%"></div>
                        </div>
                        <?php if ($enrollment_progress == 100): ?>
                            <small class="text-success mt-2 d-block"><i class="bi bi-check-circle me-1"></i>All requirements completed!</small>
                        <?php else: ?>
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                <?php echo ($docs_needed - $docs_uploaded); ?> document(s) remaining
                            </small>
                        <?php endif; ?>
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
                        <?php if ($docs_uploaded == $docs_needed): ?>
                            <div class="mt-2">
                                <span class="badge bg-success">Complete</span>
                            </div>
                        <?php elseif ($docs_uploaded > 0): ?>
                            <div class="mt-2">
                                <span class="badge bg-warning">In Progress</span>
                            </div>
                        <?php else: ?>
                            <div class="mt-2">
                                <span class="badge bg-secondary">Not Started</span>
                            </div>
                        <?php endif; ?>
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
                                <?php if ($docs_uploaded >= $docs_needed): ?>
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Upload Required Documents</span>
                                    <span class="ms-auto badge bg-light text-success fw-bold">Done</span>
                                <?php else: ?>
                                    <div class="step-dot step-active"></div>
                                    <span>Upload PSA, Form 137, Diploma & Marriage Certificate</span>
                                    <span class="ms-auto">
                                        <a href="<?php echo $root; ?>upload/upload.php"
                                            class="text-royal small fw-bold">Submit Files</a>
                                    </span>
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-0 px-0">
                                <?php if ($account_status === 'Approved'): ?>
                                    <i class="fas fa-check-circle text-success me-3"></i>
                                    <span>Admin Review</span>
                                    <span class="ms-auto badge bg-light text-success fw-bold">Approved</span>
                                <?php else: ?>
                                    <div class="step-dot <?php echo ($docs_uploaded >= $docs_needed) ? 'step-active' : ''; ?>"></div>
                                    <span class="<?php echo ($docs_uploaded < $docs_needed) ? 'text-muted' : ''; ?>">Pending Admin Review</span>
                                <?php endif; ?>
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
                        <?php if ($docs_uploaded == 0): ?>
                            <div class="alert bg-warning bg-opacity-10 border-0 small text-dark p-2 mb-2 d-flex">
                                <i class="fas fa-exclamation-triangle me-2 mt-1 text-warning"></i>
                                <div><strong>Action Required:</strong> Please upload your required documents to continue.</div>
                            </div>
                        <?php elseif ($docs_uploaded < $docs_needed): ?>
                            <div class="alert bg-info bg-opacity-10 border-0 small text-dark p-2 mb-2 d-flex">
                                <i class="fas fa-info-circle me-2 mt-1 text-info"></i>
                                <div><strong>In Progress:</strong> You have uploaded <?php echo $docs_uploaded; ?> out of <?php echo $docs_needed; ?> documents.</div>
                            </div>
                        <?php elseif ($account_status === 'Approved'): ?>
                            <div class="alert bg-success bg-opacity-10 border-0 small text-dark p-2 mb-2 d-flex">
                                <i class="fas fa-check-circle me-2 mt-1 text-success"></i>
                                <div><strong>All Set!</strong> Your account and documents are verified.</div>
                            </div>
                        <?php else: ?>
                            <div class="alert bg-light border-0 small text-dark p-2 mb-2 d-flex">
                                <i class="fas fa-clock me-2 mt-1 text-muted"></i>
                                <div><strong>Awaiting Review:</strong> Your documents are under admin review.</div>
                            </div>
                        <?php endif; ?>
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