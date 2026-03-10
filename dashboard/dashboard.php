<?php
// filepath: c:\laragon\www\admin-tb5\dashboard\dashboard.php
session_start();

// Ensure path detection works from the dashboard subfolder
$currentPath = $_SERVER['PHP_SELF']; 
$currentDir = basename(dirname($currentPath));
$subfolders = ['dashboard', 'enrollment', 'history', 'register', 'useraccount', 'forgot password', 'upload'];
$root = in_array($currentDir, $subfolders) ? '../' : '';

require_once($root . 'includes/rbac-guard.php');

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ' . $root . 'index.php');
    exit;
}

require_once($root . 'db-connect.php'); // Path to your PDO connector
include $root . 'includes/header.php'; 
include $root . 'includes/sidebar.php'; 

// --- 1. INITIALIZE DATA PLACEHOLDERS ---
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
$docs_needed = 4; // PSA, TOR/Diploma, Form137, Marriage Cert (standard reqs)

try {
    // --- 2. FETCH TRAINEE BASIC INFO ---
    $stmt = $pdo->prepare("SELECT FirstName, LastName, MiddleName, Status FROM studentinfos WHERE Id = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userData) {
        $trainee_name = strtoupper(trim($userData['FirstName'] . ' ' . ($userData['MiddleName'] ? substr($userData['MiddleName'], 0, 1) . '. ' : '') . $userData['LastName']));
        $account_status = $userData['Status'];
    }

    // --- 3. FETCH ENROLLMENT, COURSE, AND BATCH DATA ---
    $courseStmt = $pdo->prepare("
        SELECT 
            c.CourseName, c.CourseCode, b.BatchName, b.BatchCode,
            b.StartDate, b.EndDate, e.School, e.Status AS EnrollmentStatus
        FROM enrollments e
        INNER JOIN courses c ON e.CourseId = c.Id
        INNER JOIN batches b ON e.BatchId = b.Id
        WHERE e.StudentId = ?
        ORDER BY e.EnrolledAt DESC LIMIT 1
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
        $start_date         = !empty($courseData['StartDate']) ? date('M d, Y', strtotime($courseData['StartDate'])) : 'TBA';
        $end_date           = !empty($courseData['EndDate']) ? date('M d, Y', strtotime($courseData['EndDate'])) : 'TBA';
    }

    // --- 4. CALCULATE DOCUMENT PROGRESS ---
    $docsStmt = $pdo->prepare("
        SELECT 
            (CASE WHEN PSAPath IS NOT NULL AND PSAPath != '' THEN 1 ELSE 0 END) +
            (CASE WHEN TORPath IS NOT NULL AND TORPath != '' THEN 1 ELSE 0 END) +
            (CASE WHEN DiplomaPath IS NOT NULL AND DiplomaPath != '' THEN 1 ELSE 0 END) +
            (CASE WHEN MarriageCertificatePath IS NOT NULL AND MarriageCertificatePath != '' THEN 1 ELSE 0 END)
            AS uploaded_count
        FROM documents WHERE StudentInfoId = ? LIMIT 1
    ");
    $docsStmt->execute([$_SESSION['user_id']]);
    $docsData = $docsStmt->fetch(PDO::FETCH_ASSOC);

    $docs_uploaded = ($docsData) ? (int)$docsData['uploaded_count'] : 0;

    // --- 5. PROGRESS CALCULATION (Weightage logic) ---
    $enrollment_progress = round(($docs_uploaded / $docs_needed) * 80); // Max 80% from docs
    if ($account_status === 'Approved') {
        $enrollment_progress = 100; // Total 100% only if approved
    }

} catch(PDOException $e) { error_log("DB Link Error: " . $e->getMessage()); }
?>

<style>
    /* Styling Polish */
    .welcome-card { background: linear-gradient(135deg, var(--royal-blue) 0%, #2e51b8 100%); color: white; border-radius: 1.5rem; padding: 30px; position: relative; overflow: hidden; }
    .welcome-card::after { content: '\f19d'; font-family: "Font Awesome 5 Free"; font-weight: 900; position: absolute; right: -20px; bottom: -20px; font-size: 150px; color: rgba(255,255,255,0.1); transform: rotate(-15deg); }
    .status-widget { border: none; border-radius: 1rem; transition: 0.3s; }
    .status-widget:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important; }
    .step-dot { height: 10px; width: 10px; background-color: #ddd; border-radius: 50%; display: inline-block; margin-right: 5px; }
    .step-active { background-color: var(--royal-blue); box-shadow: 0 0 0 3px rgba(65, 105, 225, 0.2); }
</style>

<div class="main-content">
    <div class="container-fluid">
        <!-- WELCOME CARD -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="welcome-card shadow-sm">
                    <h1 class="fw-bold mb-1">HELLO, <?php echo htmlspecialchars($trainee_name); ?>!</h1>
                    <p class="mb-3 opacity-75">Your unified portal for Big Five Learning & Assessment.</p>
                    <div class="d-inline-flex align-items-center <?php echo ($account_status === 'Approved') ? 'bg-white bg-opacity-25' : 'bg-warning bg-opacity-50 text-dark'; ?> p-2 px-3 rounded-pill shadow-sm">
                        <span class="small fw-bold">STATUS: <?php echo strtoupper($account_status); ?></span>
                        <i class="fas <?php echo ($account_status === 'Approved') ? 'fa-check-circle' : 'fa-clock'; ?> ms-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- ACTIVE QUALIFICATION MODULE -->
            <div class="col-lg-6 mb-3">
                <div class="card status-widget shadow-sm h-100 p-3">
                    <div class="card-body">
                        <h6 class="text-muted fw-bold small text-uppercase">Enrollment Details</h6>
                        <?php if ($current_course !== 'No Active Course'): ?>
                            <h4 class="fw-bold text-dark mt-2"><?php echo htmlspecialchars($current_course); ?></h4>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-primary px-3 rounded-pill small"><?php echo $course_code; ?></span>
                                <span class="badge <?php echo ($school=='TB5')?'bg-info':'bg-warning text-dark'; ?> rounded-pill px-3"><?php echo $school; ?> Official</span>
                            </div>
                            <div class="p-2 bg-light rounded-3 mb-4 small border">
                                <i class="fas fa-calendar-alt text-primary me-2"></i><b>Batch:</b> <?php echo $batch_code; ?> | <b>Schedule:</b> <?php echo $start_date; ?> - <?php echo $end_date; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-book-reader text-muted opacity-25 mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted small">Your assigned course will appear here after Admin verification.</p>
                            </div>
                        <?php endif; ?>

                        <!-- PROGRESS BAR -->
                        
                    </div>
                </div>
            </div>

            <!-- DOCUMENTS STATUS -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card status-widget shadow-sm h-100 p-3 border-start border-primary border-5">
                    <div class="card-body text-center pt-4">
                        <div class="bg-primary bg-opacity-10 text-royal p-3 rounded-circle mx-auto mb-3" style="width: 60px;"><i class="fas fa-file-invoice"></i></div>
                        <h3 class="fw-bold mb-0"><?php echo $docs_uploaded; ?> / <?php echo $docs_needed; ?></h3>
                        <small class="text-muted text-uppercase fw-bold">Requirements Uploaded</small>
                    </div>
                </div>
            </div>

            <!-- GUIDE BUTTON -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card status-widget shadow-sm h-100 p-3 text-center border-start border-info border-5" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#manualModal">
                    <div class="card-body pt-4">
                        <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle mx-auto mb-3" style="width: 60px;"><i class="fas fa-lightbulb"></i></div>
                        <h5 class="fw-bold mb-0">Help Center</h5>
                        <small class="text-muted">Need help with docs?</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- STEP INDICATOR -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                    <div class="card-header bg-white py-3 border-0"><h6 class="mb-0 fw-bold text-royal"><i class="fas fa-tasks me-2"></i>My Onboarding Track</h6></div>
                    <div class="card-body pt-0 px-4 pb-4">
                        <div class="d-flex align-items-start mb-3"><i class="fas fa-check-circle text-success me-3 mt-1"></i><div><b>Registered Profile</b><br><small class="text-muted">Account was established and identity confirmed.</small></div></div>
                        <div class="d-flex align-items-start mb-3">
                            <div class="step-dot <?php echo ($docs_uploaded < $docs_needed)?'step-active':'bg-success'; ?>"></div>
                            <div>
                                <b>Document Review</b><br>
                                <small class="text-muted">Submit all requirements via the Enrollment section.</small>
                            </div>
                            <a href="<?php echo $root; ?>enrollment/enrollment.php" class="ms-auto btn btn-sm btn-outline-primary border-0 rounded-pill">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                        <div class="d-flex align-items-start mb-0">
                            <div class="step-dot <?php echo ($current_course !== 'No Active Course' && $batch_name && $batch_code) ? 'bg-success' : ''; ?>"></div>
                            <div>
                                <b>Institutional Assignment</b><br>
                                <small class="text-muted text-truncate d-block">
                                    Admin assignment to TB5 or BBI official training classes.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SYSTEM FEEDBACK -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white py-3 border-0"><h6 class="mb-0 fw-bold text-royal"><i class="fas fa-info-circle me-2"></i>Status Feed</h6></div>
                    <div class="card-body pt-0">
                        <?php if ($docs_uploaded < $docs_needed): ?>
                            <div class="alert border-0 bg-warning bg-opacity-10 text-dark small p-3 d-flex shadow-sm">
                                <i class="fas fa-exclamation-triangle me-2 mt-1 text-warning"></i>
                                <div><b>Warning:</b> Complete your 4 required document uploads to be eligible for Admin approval.</div>
                            </div>
                        <?php else: ?>
                            <div class="alert border-0 bg-success bg-opacity-10 text-dark small p-3 d-flex shadow-sm">
                                <i class="fas fa-clipboard-check me-2 mt-1 text-success"></i>
                                <div><b>Documents Locked:</b> Records are secured. Your files are in the official verification queue.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- GUIDE MODAL (Instruction Manual) -->
<div class="modal fade" id="manualModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4 border-0 p-3"><div class="modal-header border-0"><h5 class="fw-bold mb-0">Doc-Upload Guide</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="small text-muted mb-4 text-center">Follow our institutional verification steps for 2026 academic standards.</p><div class="d-flex mb-3"><div class="fw-bold me-3 text-info h5">01</div><p class="small"><b>Formats:</b> Scans must be in clear PDF, JPG or PNG formats only.</p></div><div class="d-flex mb-3"><div class="fw-bold me-3 text-info h5">02</div><p class="small"><b>Naming:</b> Use standard labels like <i>(ULI_Lastname_TOR.pdf)</i> to speed up Admin checking.</p></div><div class="d-flex mb-3"><div class="fw-bold me-3 text-info h5">03</div><p class="small"><b>Limit:</b> Each individual file must be smaller than <b>5.0MB</b>.</p></div><button class="btn btn-royal w-100 rounded-pill py-2 shadow-sm" onclick="location.href='<?php echo $root; ?>upload/upload.php'">Go to Uploads <i class="fas fa-door-open ms-2"></i></button></div></div></div></div>

<?php include $root . 'includes/footer.php'; ?>