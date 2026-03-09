<?php

session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');
checkAdmin();

// Include header
include('../header/header.php');
include('../sidebar/sidebar.php');

// Get filter parameters
$adminFilter = isset($_GET['admin']) ? $_GET['admin'] : '';
$actionFilter = isset($_GET['action']) ? $_GET['action'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'recent';
$logType = isset($_GET['log_type']) ? $_GET['log_type'] : 'all';

// Pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$entriesPerPage = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$offset = ($page - 1) * $entriesPerPage;

// Build the base query for combined activity logs
$baseQuery = "
    SELECT * FROM (
        -- Student approvals (when admin approves a student)
        SELECT 
            CONCAT('APPROVED_', si.Id, '_', UNIX_TIMESTAMP(si.UpdatedAt)) as Id,
            'student_approved' as LogType,
            si.Id as TargetId,
            'Student Approved' as Action,
            CONCAT(
                'Student ', si.FirstName, ' ', si.LastName,
                ' (', si.Email, ') has been APPROVED'
            ) as Description,
            si.UpdatedAt as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Student Approval' as Category,
            'success' as Status,
            si.FirstName as StudentFirstName,
            si.LastName as StudentLastName,
            si.Email as StudentEmail,
            si.Status as StudentStatus
        FROM studentinfos si
        WHERE si.Status = 'Approved'
        AND si.UpdatedAt != si.EntryDate
        
        UNION ALL
        
        -- Student rejections (when admin rejects a student)
        SELECT 
            CONCAT('REJECTED_', si.Id, '_', UNIX_TIMESTAMP(si.UpdatedAt)) as Id,
            'student_rejected' as LogType,
            si.Id as TargetId,
            'Student Rejected' as Action,
            CONCAT(
                'Student ', si.FirstName, ' ', si.LastName,
                ' (', si.Email, ') has been REJECTED'
            ) as Description,
            si.UpdatedAt as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Student Rejection' as Category,
            'danger' as Status,
            si.FirstName as StudentFirstName,
            si.LastName as StudentLastName,
            si.Email as StudentEmail,
            si.Status as StudentStatus
        FROM studentinfos si
        WHERE si.Status = 'Rejected'
        AND si.UpdatedAt != si.EntryDate
        
        UNION ALL
        
        -- Student pending registrations (in queue)
        SELECT 
            CONCAT('PENDING_', si.Id, '_', UNIX_TIMESTAMP(si.EntryDate)) as Id,
            'student_pending' as LogType,
            si.Id as TargetId,
            'Student in Queue' as Action,
            CONCAT(
                'Student ', si.FirstName, ' ', si.LastName,
                ' (', si.Email, ') is waiting for approval'
            ) as Description,
            si.EntryDate as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Pending Registration' as Category,
            'warning' as Status,
            si.FirstName as StudentFirstName,
            si.LastName as StudentLastName,
            si.Email as StudentEmail,
            si.Status as StudentStatus
        FROM studentinfos si
        WHERE si.Status = 'Pending'
        
        UNION ALL
        
        -- Student registrations/creations (initial entry)
        SELECT 
            CONCAT('STUDENT_', si.Id, '_', UNIX_TIMESTAMP(si.EntryDate)) as Id,
            'student_creation' as LogType,
            si.Id as TargetId,
            'Student Registered' as Action,
            CONCAT(
                'New student registered: ', si.FirstName, ' ', si.LastName,
                ' (', si.Email, ')'
            ) as Description,
            si.EntryDate as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Student Registration' as Category,
            CASE 
                WHEN si.Status = 'Approved' THEN 'success'
                WHEN si.Status = 'Pending' THEN 'warning'
                ELSE 'secondary'
            END as Status,
            si.FirstName as StudentFirstName,
            si.LastName as StudentLastName,
            si.Email as StudentEmail,
            si.Status as StudentStatus
        FROM studentinfos si
        
        UNION ALL
        
        -- Document approvals (from documents table)
        SELECT 
            CONCAT('DOC_APPROVED_', d.Id, '_', UNIX_TIMESTAMP(NOW())) as Id,
            'document_approved' as LogType,
            d.StudentInfoId as TargetId,
            'Document Approved' as Action,
            CONCAT(
                CASE 
                    WHEN d.PSAStatus = 'approved' THEN 'PSA Birth Certificate'
                    WHEN d.TORStatus = 'approved' THEN 'Transcript of Records'
                    WHEN d.DiplomaStatus = 'approved' THEN 'Diploma'
                    WHEN d.Form137Status = 'approved' THEN 'Form 137'
                    WHEN d.ALSCertificateStatus = 'approved' THEN 'ALS Certificate'
                    WHEN d.MarriageCertificateStatus = 'approved' THEN 'Marriage Certificate'
                    WHEN d.BarangayIndigencyStatus = 'approved' THEN 'Barangay Indigency'
                    WHEN d.CertificateOfResidencyStatus = 'approved' THEN 'Certificate of Residency'
                END,
                ' approved for ', s.FirstName, ' ', s.LastName
            ) as Description,
            NOW() as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Document Approval' as Category,
            'success' as Status,
            s.FirstName as StudentFirstName,
            s.LastName as StudentLastName,
            s.Email as StudentEmail,
            s.Status as StudentStatus
        FROM documents d
        INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
        WHERE 
            d.PSAStatus = 'approved' OR
            d.TORStatus = 'approved' OR
            d.DiplomaStatus = 'approved' OR
            d.Form137Status = 'approved' OR
            d.ALSCertificateStatus = 'approved' OR
            d.MarriageCertificateStatus = 'approved' OR
            d.BarangayIndigencyStatus = 'approved' OR
            d.CertificateOfResidencyStatus = 'approved'
        
        UNION ALL
        
        -- Document rejections (from documents table)
        SELECT 
            CONCAT('DOC_REJECTED_', d.Id, '_', UNIX_TIMESTAMP(NOW())) as Id,
            'document_rejected' as LogType,
            d.StudentInfoId as TargetId,
            'Document Rejected' as Action,
            CONCAT(
                CASE 
                    WHEN d.PSAStatus = 'rejected' THEN 'PSA Birth Certificate'
                    WHEN d.TORStatus = 'rejected' THEN 'Transcript of Records'
                    WHEN d.DiplomaStatus = 'rejected' THEN 'Diploma'
                    WHEN d.Form137Status = 'rejected' THEN 'Form 137'
                    WHEN d.ALSCertificateStatus = 'rejected' THEN 'ALS Certificate'
                    WHEN d.MarriageCertificateStatus = 'rejected' THEN 'Marriage Certificate'
                    WHEN d.BarangayIndigencyStatus = 'rejected' THEN 'Barangay Indigency'
                    WHEN d.CertificateOfResidencyStatus = 'rejected' THEN 'Certificate of Residency'
                END,
                ' rejected for ', s.FirstName, ' ', s.LastName,
                IF(d.Remarks IS NOT NULL AND d.Remarks != '', CONCAT(' - Reason: ', d.Remarks), '')
            ) as Description,
            NOW() as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Document Rejection' as Category,
            'danger' as Status,
            s.FirstName as StudentFirstName,
            s.LastName as StudentLastName,
            s.Email as StudentEmail,
            s.Status as StudentStatus
        FROM documents d
        INNER JOIN studentinfos s ON d.StudentInfoId = s.Id
        WHERE 
            d.PSAStatus = 'rejected' OR
            d.TORStatus = 'rejected' OR
            d.DiplomaStatus = 'rejected' OR
            d.Form137Status = 'rejected' OR
            d.ALSCertificateStatus = 'rejected' OR
            d.MarriageCertificateStatus = 'rejected' OR
            d.BarangayIndigencyStatus = 'rejected' OR
            d.CertificateOfResidencyStatus = 'rejected'
        
        UNION ALL
        
        -- Admin account changes
        SELECT 
            CONCAT('ADMIN_', a.Id, '_', UNIX_TIMESTAMP(a.UpdatedAt)) as Id,
            'admin_change' as LogType,
            a.Id as TargetId,
            'Admin Updated' as Action,
            CONCAT(
                'Admin ', a.Username, ' (', a.FirstName, ' ', a.LastName, ') account updated'
            ) as Description,
            a.UpdatedAt as CreatedAt,
            a.Id as AdminId,
            a.Username as AdminName,
            'Admin Management' as Category,
            'primary' as Status,
            NULL as StudentFirstName,
            NULL as StudentLastName,
            NULL as StudentEmail,
            NULL as StudentStatus
        FROM admins a
        WHERE a.UpdatedAt IS NOT NULL
        AND a.UpdatedAt != a.CreatedAt
        
        UNION ALL
        
        -- Admin creations
        SELECT 
            CONCAT('ADMINCREATE_', a.Id, '_', UNIX_TIMESTAMP(a.CreatedAt)) as Id,
            'admin_creation' as LogType,
            a.Id as TargetId,
            'Admin Created' as Action,
            CONCAT(
                'New admin account: ', a.Username, ' (', a.FirstName, ' ', a.LastName, ')'
            ) as Description,
            a.CreatedAt as CreatedAt,
            a.Id as AdminId,
            a.Username as AdminName,
            'Admin Management' as Category,
            'success' as Status,
            NULL as StudentFirstName,
            NULL as StudentLastName,
            NULL as StudentEmail,
            NULL as StudentStatus
        FROM admins a
        WHERE a.CreatedAt IS NOT NULL
        
        UNION ALL
        
        -- Admin logins
        SELECT 
            CONCAT('ADMINLOGIN_', a.Id, '_', UNIX_TIMESTAMP(a.LastLogin)) as Id,
            'admin_login' as LogType,
            a.Id as TargetId,
            'Admin Login' as Action,
            CONCAT(
                'Admin ', a.Username, ' logged in'
            ) as Description,
            a.LastLogin as CreatedAt,
            a.Id as AdminId,
            a.Username as AdminName,
            'Admin Login' as Category,
            'info' as Status,
            NULL as StudentFirstName,
            NULL as StudentLastName,
            NULL as StudentEmail,
            NULL as StudentStatus
        FROM admins a
        WHERE a.LastLogin IS NOT NULL
        
        UNION ALL
        
        -- Course creations and updates
        SELECT 
            CONCAT('COURSE_', c.Id, '_', UNIX_TIMESTAMP(COALESCE(c.UpdatedAt, c.CreatedAt))) as Id,
            'course_change' as LogType,
            c.Id as TargetId,
            CASE 
                WHEN c.CreatedAt = COALESCE(c.UpdatedAt, c.CreatedAt) THEN 'Course Created'
                ELSE 'Course Updated'
            END as Action,
            CONCAT(
                CASE 
                    WHEN c.CreatedAt = COALESCE(c.UpdatedAt, c.CreatedAt) THEN 'New course created: '
                    ELSE 'Course updated: '
                END,
                c.CourseCode, ' - ', c.CourseName,
                ' (', c.School, ')'
            ) as Description,
            COALESCE(c.UpdatedAt, c.CreatedAt) as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Course Management' as Category,
            CASE 
                WHEN c.IsActive = 1 THEN 'success'
                ELSE 'secondary'
            END as Status,
            NULL as StudentFirstName,
            NULL as StudentLastName,
            NULL as StudentEmail,
            NULL as StudentStatus
        FROM courses c
        WHERE c.CreatedAt IS NOT NULL OR c.UpdatedAt IS NOT NULL
        
        UNION ALL
        
        -- Batch creations and updates
        SELECT 
            CONCAT('BATCH_', b.Id, '_', UNIX_TIMESTAMP(COALESCE(b.UpdatedAt, b.CreatedAt))) as Id,
            'batch_change' as LogType,
            b.Id as TargetId,
            CASE 
                WHEN b.CreatedAt = COALESCE(b.UpdatedAt, b.CreatedAt) THEN 'Batch Created'
                ELSE 'Batch Updated'
            END as Action,
            CONCAT(
                CASE 
                    WHEN b.CreatedAt = COALESCE(b.UpdatedAt, b.CreatedAt) THEN 'New batch created: '
                    ELSE 'Batch updated: '
                END,
                b.BatchName, ' (', b.BatchCode, ')'
            ) as Description,
            COALESCE(b.UpdatedAt, b.CreatedAt) as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Batch Management' as Category,
            CASE 
                WHEN b.Status = 'Active' THEN 'success'
                WHEN b.Status = 'Completed' THEN 'info'
                ELSE 'warning'
            END as Status,
            NULL as StudentFirstName,
            NULL as StudentLastName,
            NULL as StudentEmail,
            NULL as StudentStatus
        FROM batches b
        WHERE (b.CreatedAt IS NOT NULL OR b.UpdatedAt IS NOT NULL)
    ) combined_logs
    WHERE 1=1
";

// Build filter conditions
$filterConditions = "";
$params = [];

// Apply search filter
if (!empty($searchTerm)) {
    $filterConditions .= " AND (Description LIKE :search1 
                OR StudentFirstName LIKE :search2 
                OR StudentLastName LIKE :search3 
                OR StudentEmail LIKE :search4
                OR AdminName LIKE :search5)";
    $searchParam = "%$searchTerm%";
    $params[':search1'] = $searchParam;
    $params[':search2'] = $searchParam;
    $params[':search3'] = $searchParam;
    $params[':search4'] = $searchParam;
    $params[':search5'] = $searchParam;
}

// Apply log type filter
if ($logType != 'all') {
    switch($logType) {
        case 'admin_changes':
            $filterConditions .= " AND LogType IN ('admin_change', 'admin_creation', 'admin_login')";
            break;
        case 'student_changes':
            $filterConditions .= " AND LogType IN ('student_creation', 'student_approved', 'student_rejected', 'student_pending')";
            break;
        case 'document_actions':
            $filterConditions .= " AND LogType IN ('document_approved', 'document_rejected')";
            break;
        case 'pending':
            $filterConditions .= " AND LogType IN ('student_pending')";
            break;
        case 'course_changes':
            $filterConditions .= " AND LogType IN ('course_change')";
            break;
        case 'batch_changes':
            $filterConditions .= " AND LogType IN ('batch_change')";
            break;
    }
}

// Apply view mode filter
if ($viewMode === 'recent') {
    $filterConditions .= " AND CreatedAt >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
} elseif ($viewMode === 'archived') {
    $filterConditions .= " AND CreatedAt < DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

// Apply action filter
if (!empty($actionFilter)) {
    $filterConditions .= " AND Action LIKE :action";
    $params[':action'] = "%$actionFilter%";
}

// Apply admin filter
if (!empty($adminFilter)) {
    $filterConditions .= " AND AdminId = :adminId";
    $params[':adminId'] = $adminFilter;
}

// Apply date filter
if (!empty($dateFilter)) {
    switch($dateFilter) {
        case 'today':
            $filterConditions .= " AND DATE(CreatedAt) = CURDATE()";
            break;
        case 'yesterday':
            $filterConditions .= " AND DATE(CreatedAt) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $filterConditions .= " AND CreatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $filterConditions .= " AND CreatedAt >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
    }
}

// Complete query with filters
$fullQuery = $baseQuery . $filterConditions;

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM ($fullQuery) as count_table";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalEntries = $countStmt->fetch()['total'];
$totalPages = ceil($totalEntries / $entriesPerPage);

// Add ordering and pagination
$fullQuery .= " ORDER BY CreatedAt DESC LIMIT :offset, :entriesPerPage";

// Prepare and execute main query with pagination
$stmt = $pdo->prepare($fullQuery);

// Bind parameters
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':entriesPerPage', $entriesPerPage, PDO::PARAM_INT);
$stmt->execute();

$logs = $stmt->fetchAll();

// Get counts for recent and archived (with filters applied)
$recentCountQuery = $baseQuery . $filterConditions . " AND CreatedAt >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$recentCountStmt = $pdo->prepare("SELECT COUNT(*) as recent FROM ($recentCountQuery) as recent_table");
$recentCountStmt->execute($params);
$recentCount = $recentCountStmt->fetch()['recent'];

$archivedCountQuery = $baseQuery . $filterConditions . " AND CreatedAt < DATE_SUB(NOW(), INTERVAL 30 DAY)";
$archivedCountStmt = $pdo->prepare("SELECT COUNT(*) as archived FROM ($archivedCountQuery) as archived_table");
$archivedCountStmt->execute($params);
$archivedCount = $archivedCountStmt->fetch()['archived'];

// Get admins for filter dropdown
$adminsStmt = $pdo->query("SELECT Id, Username, FirstName, LastName, Email FROM admins WHERE Status = 'Active' ORDER BY FirstName, LastName");
$admins = $adminsStmt->fetchAll();

// Get today's logs count (with filters applied)
$todayQuery = $baseQuery . $filterConditions . " AND DATE(CreatedAt) = CURDATE()";
$todayCountStmt = $pdo->prepare("SELECT COUNT(*) as today FROM ($todayQuery) as today_table");
$todayCountStmt->execute($params);
$todayCount = $todayCountStmt->fetch()['today'];

// Get statistics
$statsQuery = "
    SELECT 
        (SELECT COUNT(*) FROM studentinfos WHERE Status = 'Pending') as pending_students,
        (SELECT COUNT(*) FROM studentinfos WHERE Status = 'Approved') as total_approved_students,
        (SELECT COUNT(*) FROM studentinfos WHERE Status = 'Rejected') as total_rejected_students,
        (SELECT COUNT(*) FROM admins WHERE Status = 'Active') as active_admins,
        (SELECT COUNT(*) FROM studentinfos) as total_students,
        (SELECT COUNT(*) FROM documents WHERE 
            PSAStatus = 'approved' OR
            TORStatus = 'approved' OR
            DiplomaStatus = 'approved' OR
            Form137Status = 'approved' OR
            ALSCertificateStatus = 'approved' OR
            MarriageCertificateStatus = 'approved' OR
            BarangayIndigencyStatus = 'approved' OR
            CertificateOfResidencyStatus = 'approved'
        ) as total_document_approvals,
        (SELECT COUNT(*) FROM courses) as total_courses,
        (SELECT COUNT(*) FROM batches) as total_batches
";
$statsStmt = $pdo->query($statsQuery);
$stats = $statsStmt->fetch();
?>

<style>
/* Table Styles */
.table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    color: #495057;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody tr:hover {
    background-color: #f5f5f5;
}

.table td {
    vertical-align: middle;
    font-size: 0.9rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.5rem;
    font-weight: 500;
}

/* Card Styles */
.card {
    border-radius: 0.5rem;
    border: none;
}

.card-header {
    background-color: transparent;
    border-bottom: 1px solid rgba(0,0,0,.125);
}

/* Real-time indicator */
.real-time-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #28a745;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
    }
}

/* Admin badge */
.admin-badge {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(220, 53, 69, 0.1);
    border-radius: 50%;
}

/* View Toggle Buttons */
.view-toggle .btn.active {
    background: #4169E1;
    color: white;
    border-color: transparent;
}

.view-toggle .btn:not(.active):hover {
    background: #f0f4ff;
    border-color: #4169E1;
    color: #4169E1;
}

/* Pagination */
.pagination .page-link {
    color: #4169E1;
    border: none;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 0.25rem;
}

.pagination .page-item.active .page-link {
    background: #4169E1;
    color: white;
}

/* Modal */
.modal-header {
    background: #4169E1;
    color: white;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
}

/* Active Filters Badge */
.active-filters .badge {
    background-color: #4169E1;
    color: white;
    margin-right: 0.5rem;
    padding: 0.35rem 0.65rem;
}

</style>

<div class="content-wrapper">
    <div class="main-content">
        <!-- Page Title Card -->
        <div class="card border-0 shadow-sm mb-4" style="background: #4169E1;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold text-white mb-2">
                            <i class="bi bi-clock-history me-2"></i>Activity Logs
                            <span class="real-time-indicator ms-2"></span>
                        </h2>
                        <p class="text-white-50 mb-0">
                            Track and monitor all system activities including admin actions, student activities, course/batch management, and document approvals
                        </p>
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
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-list-ul text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Total Logs</h6>
                                <h3 class="mb-0 fw-bold"><?php echo number_format($totalEntries); ?></h3>
                                <small class="text-muted">Filtered results</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-calendar-day text-success" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Today</h6>
                                <h3 class="mb-0 fw-bold"><?php echo number_format($todayCount); ?></h3>
                                <small class="text-muted">Last 24 hours</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-file-earmark-check text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Document Actions</h6>
                                <h3 class="mb-0 fw-bold"><?php echo number_format($stats['total_document_approvals'] ?? 0); ?></h3>
                                <small class="text-muted">Total approved/rejected</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-shield-check text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Courses/Batches</h6>
                                <h3 class="mb-0 fw-bold"><?php echo number_format(($stats['total_courses'] ?? 0) + ($stats['total_batches'] ?? 0)); ?></h3>
                                <small class="text-muted">Total managed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="view-toggle">
                <a href="?view=recent<?php 
                    echo $logType != 'all' ? '&log_type='.$logType : ''; 
                    echo !empty($searchTerm) ? '&search='.urlencode($searchTerm) : '';
                    echo !empty($adminFilter) ? '&admin='.$adminFilter : '';
                    echo !empty($actionFilter) ? '&action='.$actionFilter : '';
                    echo !empty($dateFilter) ? '&date='.$dateFilter : '';
                ?>" class="btn <?php echo $viewMode == 'recent' ? 'active' : ''; ?>">
                    <i class="bi bi-clock me-1"></i> Recent (30 days)
                    <?php if($recentCount > 0): ?>
                    <span class="badge bg-light text-dark ms-1"><?php echo number_format($recentCount); ?></span>
                    <?php endif; ?>
                </a>
                <a href="?view=archived<?php 
                    echo $logType != 'all' ? '&log_type='.$logType : ''; 
                    echo !empty($searchTerm) ? '&search='.urlencode($searchTerm) : '';
                    echo !empty($adminFilter) ? '&admin='.$adminFilter : '';
                    echo !empty($actionFilter) ? '&action='.$actionFilter : '';
                    echo !empty($dateFilter) ? '&date='.$dateFilter : '';
                ?>" class="btn <?php echo $viewMode == 'archived' ? 'active' : ''; ?>">
                    <i class="bi bi-archive me-1"></i> Archived
                    <?php if($archivedCount > 0): ?>
                    <span class="badge bg-light text-dark ms-1"><?php echo number_format($archivedCount); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <div class="text-muted small">
                <i class="bi bi-info-circle me-1"></i>
                Showing <?php echo $viewMode == 'recent' ? 'logs from last 30 days' : 'logs older than 30 days'; ?>
            </div>
        </div>

        <!-- Active Filters Display -->
        <?php if(!empty($searchTerm) || !empty($adminFilter) || $logType != 'all' || !empty($dateFilter) || !empty($actionFilter)): ?>
        <div class="active-filters mb-3">
            <i class="bi bi-funnel-fill me-2"></i> Active Filters:
            <?php if(!empty($searchTerm)): ?>
                <span class="badge">
                    Search: "<?php echo htmlspecialchars($searchTerm); ?>"
                    <i class="bi bi-x" onclick="removeFilter('search')"></i>
                </span>
            <?php endif; ?>
            <?php if(!empty($adminFilter)): 
                $adminName = '';
                foreach($admins as $admin) {
                    if($admin['Id'] == $adminFilter) {
                        $adminName = $admin['FirstName'] . ' ' . $admin['LastName'];
                        break;
                    }
                }
            ?>
                <span class="badge">
                    Admin: <?php echo htmlspecialchars($adminName); ?>
                    <i class="bi bi-x" onclick="removeFilter('admin')"></i>
                </span>
            <?php endif; ?>
            <?php if($logType != 'all'): ?>
                <span class="badge">
                    Type: <?php echo ucwords(str_replace('_', ' ', $logType)); ?>
                    <i class="bi bi-x" onclick="removeFilter('log_type')"></i>
                </span>
            <?php endif; ?>
            <?php if(!empty($dateFilter)): ?>
                <span class="badge">
                    Date: <?php echo ucfirst($dateFilter); ?>
                    <i class="bi bi-x" onclick="removeFilter('date')"></i>
                </span>
            <?php endif; ?>
            <?php if(!empty($actionFilter)): ?>
                <span class="badge">
                    Action: <?php echo htmlspecialchars($actionFilter); ?>
                    <i class="bi bi-x" onclick="removeFilter('action')"></i>
                </span>
            <?php endif; ?>
            <span class="badge bg-secondary" onclick="resetAllFilters()" style="cursor: pointer;">
                <i class="bi bi-arrow-counterclockwise"></i> Clear All
            </span>
        </div>
        <?php endif; ?>

        <!-- Filters Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="" id="filterForm">
                    <input type="hidden" name="view" value="<?php echo $viewMode; ?>" id="viewInput">
                    <input type="hidden" name="page" value="1" id="pageInput">
                    <input type="hidden" name="entries" value="<?php echo $entriesPerPage; ?>" id="entriesInput">
                    
                    <div class="row g-3">
                        <!-- Search Bar -->
                        <div class="col-md-3">
                            <label class="filter-label">Search</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" name="search" id="searchInput" placeholder="Search logs..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                            </div>
                        </div>
                        
                        <!-- Admin Filter -->
                        <div class="col-md-2">
                            <label class="filter-label">Admin</label>
                            <select class="form-select" name="admin" id="adminFilter">
                                <option value="">All Admins</option>
                                <?php foreach($admins as $admin): ?>
                                <option value="<?php echo $admin['Id']; ?>" <?php echo $adminFilter == $admin['Id'] ? 'selected' : ''; ?>>
                                    <?php echo $admin['FirstName'] . ' ' . $admin['LastName']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Log Type Filter -->
                        <div class="col-md-2">
                            <label class="filter-label">Log Type</label>
                            <select class="form-select" name="log_type" id="logTypeFilter">
                                <option value="all" <?php echo $logType == 'all' ? 'selected' : ''; ?>>All Activities</option>
                                <option value="admin_changes" <?php echo $logType == 'admin_changes' ? 'selected' : ''; ?>>Admin Actions</option>
                                <option value="student_changes" <?php echo $logType == 'student_changes' ? 'selected' : ''; ?>>Student Changes</option>
                                <option value="document_actions" <?php echo $logType == 'document_actions' ? 'selected' : ''; ?>>Document Actions</option>
                                <option value="course_changes" <?php echo $logType == 'course_changes' ? 'selected' : ''; ?>>Course Changes</option>
                                <option value="batch_changes" <?php echo $logType == 'batch_changes' ? 'selected' : ''; ?>>Batch Changes</option>
                                <option value="pending" <?php echo $logType == 'pending' ? 'selected' : ''; ?>>Pending Queue</option>
                            </select>
                        </div>
                        
                        <!-- Date Filter -->
                        <div class="col-md-2">
                            <label class="filter-label">Date Range</label>
                            <select class="form-select" name="date" id="dateFilter">
                                <option value="">All Time</option>
                                <option value="today" <?php echo $dateFilter == 'today' ? 'selected' : ''; ?>>Today</option>
                                <option value="yesterday" <?php echo $dateFilter == 'yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                                <option value="week" <?php echo $dateFilter == 'week' ? 'selected' : ''; ?>>This Week</option>
                                <option value="month" <?php echo $dateFilter == 'month' ? 'selected' : ''; ?>>This Month</option>
                            </select>
                        </div>
                        
                        <!-- Action Filter (optional) -->
                        <div class="col-md-3">
                            <label class="filter-label">Action (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-tag"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" name="action" id="actionFilter" placeholder="e.g., Approved, Registered, Created..." value="<?php echo htmlspecialchars($actionFilter); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel me-1"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="badge bg-success p-2">
                        <i class="bi bi-person-check me-1"></i> Approved Students: <?php echo number_format($stats['total_approved_students'] ?? 0); ?>
                    </span>
                    <span class="badge bg-warning p-2">
                        <i class="bi bi-person-fill me-1"></i> Pending: <?php echo number_format($stats['pending_students'] ?? 0); ?>
                    </span>
                    <span class="badge bg-danger p-2">
                        <i class="bi bi-person-x me-1"></i> Rejected: <?php echo number_format($stats['total_rejected_students'] ?? 0); ?>
                    </span>
                    <span class="badge bg-info p-2">
                        <i class="bi bi-file-earmark-check me-1"></i> Document Actions: <?php echo number_format($stats['total_document_approvals'] ?? 0); ?>
                    </span>
                    <span class="badge bg-primary p-2">
                        <i class="bi bi-shield me-1"></i> Active Admins: <?php echo number_format($stats['active_admins'] ?? 0); ?>
                    </span>
                    <span class="badge bg-secondary p-2">
                        <i class="bi bi-book me-1"></i> Courses: <?php echo number_format($stats['total_courses'] ?? 0); ?>
                    </span>
                    <span class="badge bg-dark p-2">
                        <i class="bi bi-collection me-1"></i> Batches: <?php echo number_format($stats['total_batches'] ?? 0); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Activity Logs Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2"></i>Activity Timeline
                    <?php if($viewMode == 'recent'): ?>
                    <span class="badge bg-primary ms-2">Last 30 days</span>
                    <?php else: ?>
                    <span class="badge bg-secondary ms-2">Archived</span>
                    <?php endif; ?>
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshLogs()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                    </button>
                    <select class="form-select form-select-sm" id="entriesPerPage" style="width: auto;" onchange="changeEntries(this.value)">
                        <option value="10" <?php echo $entriesPerPage == 10 ? 'selected' : ''; ?>>10 per page</option>
                        <option value="25" <?php echo $entriesPerPage == 25 ? 'selected' : ''; ?>>25 per page</option>
                        <option value="50" <?php echo $entriesPerPage == 50 ? 'selected' : ''; ?>>50 per page</option>
                        <option value="100" <?php echo $entriesPerPage == 100 ? 'selected' : ''; ?>>100 per page</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="logsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">Timestamp</th>
                                <th>Admin/User</th>
                                <th>Action Type</th>
                                <th>Description</th>
                                <th>Target</th>
                                <th>Status</th>
                                <th class="text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php if (count($logs) > 0): ?>
                                <?php foreach($logs as $log): 
                                    // Determine badge color and icon based on action
                                    $actionLower = strtolower($log['Action']);
                                    $badgeClass = 'secondary';
                                    $icon = 'info-circle';
                                    
                                    if (strpos($actionLower, 'approve') !== false) {
                                        $badgeClass = 'success';
                                        $icon = 'check-circle';
                                    } elseif (strpos($actionLower, 'reject') !== false) {
                                        $badgeClass = 'danger';
                                        $icon = 'x-circle';
                                    } elseif (strpos($actionLower, 'create') !== false || strpos($actionLower, 'registered') !== false) {
                                        $badgeClass = 'primary';
                                        $icon = 'plus-circle';
                                    } elseif (strpos($actionLower, 'update') !== false || strpos($actionLower, 'status') !== false) {
                                        $badgeClass = 'warning';
                                        $icon = 'pencil';
                                    } elseif (strpos($actionLower, 'login') !== false) {
                                        $badgeClass = 'info';
                                        $icon = 'box-arrow-in-right';
                                    } elseif (strpos($actionLower, 'queue') !== false) {
                                        $badgeClass = 'warning';
                                        $icon = 'hourglass-split';
                                    } elseif (strpos($actionLower, 'document') !== false) {
                                        $badgeClass = 'primary';
                                        $icon = 'file-earmark-text';
                                    } elseif (strpos($actionLower, 'course') !== false) {
                                        $badgeClass = 'info';
                                        $icon = 'book';
                                    } elseif (strpos($actionLower, 'batch') !== false) {
                                        $badgeClass = 'secondary';
                                        $icon = 'collection';
                                    }
                                    
                                    // Determine target display
                                    $targetDisplay = '';
                                    if ($log['StudentFirstName'] || $log['StudentLastName']) {
                                        $targetDisplay = '<span class="text-primary">' . htmlspecialchars($log['StudentFirstName'] . ' ' . $log['StudentLastName']) . '</span>';
                                        if ($log['StudentEmail']) {
                                            $targetDisplay .= '<br><small class="text-muted">' . htmlspecialchars($log['StudentEmail']) . '</small>';
                                        }
                                    } elseif ($log['AdminName']) {
                                        $targetDisplay = '<span class="text-primary">' . htmlspecialchars($log['AdminName']) . '</span>';
                                    } else {
                                        $targetDisplay = '<span class="text-muted">System</span>';
                                    }
                                    
                                    // Determine status badge
                                    $statusText = ucfirst($log['Status']);
                                ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-semibold"><?php echo date('M d, Y', strtotime($log['CreatedAt'])); ?></div>
                                        <small class="text-muted"><?php echo date('h:i:s A', strtotime($log['CreatedAt'])); ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="admin-badge me-2">
                                                <?php if($log['AdminName']): ?>
                                                    <i class="bi bi-shield-fill-check text-danger"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">
                                                    <?php 
                                                    if ($log['AdminName']) {
                                                        echo htmlspecialchars($log['AdminName']);
                                                        echo ' <small class="text-muted">(Admin)</small>';
                                                    } elseif ($log['StudentFirstName']) {
                                                        echo htmlspecialchars($log['StudentFirstName'] . ' ' . $log['StudentLastName']);
                                                        echo ' <small class="text-muted">(Student)</small>';
                                                    } else {
                                                        echo 'System';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $badgeClass; ?>">
                                            <i class="bi bi-<?php echo $icon; ?> me-1"></i><?php echo htmlspecialchars($log['Action']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($log['Description']); ?></td>
                                    <td><?php echo $targetDisplay; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $log['Status'] == 'danger' ? 'danger' : ($log['Status'] == 'warning' ? 'warning' : ($log['Status'] == 'success' ? 'success' : 'secondary')); ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-info view-log-details" data-log='<?php echo json_encode($log); ?>'>
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                        <h5 class="text-muted">No activity logs found</h5>
                                        <p class="text-muted small">Try adjusting your filters or search criteria</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing <span id="showingStart"><?php echo $offset + 1; ?></span> to <span id="showingEnd"><?php echo min($offset + $entriesPerPage, $totalEntries); ?></span> of <span id="totalEntries"><?php echo number_format($totalEntries); ?></span> entries
                    </div>
                    <nav>
                        <ul class="pagination mb-0" id="pagination">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="#" onclick="changePage(<?php echo $page - 1; ?>)" tabindex="-1">Previous</a>
                            </li>
                            
                            <?php
                            // Calculate pagination range
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            if ($startPage > 1) {
                                echo '<li class="page-item"><a class="page-link" href="#" onclick="changePage(1)">1</a></li>';
                                if ($startPage > 2) {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                }
                            }
                            
                            for ($i = $startPage; $i <= $endPage; $i++) {
                                echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '">';
                                echo '<a class="page-link" href="#" onclick="changePage(' . $i . ')">' . $i . '</a>';
                                echo '</li>';
                            }
                            
                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="#" onclick="changePage(' . $totalPages . ')">' . $totalPages . '</a></li>';
                            }
                            ?>
                            
                            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="#" onclick="changePage(<?php echo $page + 1; ?>)">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Log Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activity Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="logDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variable for refresh interval
let autoRefreshInterval = null;

// View log details function
function viewLogDetails(log) {
    try {
        // Parse log if it's a string
        if (typeof log === 'string') {
            log = JSON.parse(log);
        }
        
        const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
        const modalBody = document.getElementById('logDetailsContent');
        
        let html = `
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Log ID</label>
                    <p class="fw-semibold">#${log.Id || 'N/A'}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Timestamp</label>
                    <p class="fw-semibold">${log.CreatedAt ? new Date(log.CreatedAt).toLocaleString() : 'N/A'}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">User Type</label>
                    <p class="fw-semibold">${log.AdminName ? 'Admin' : (log.StudentFirstName ? 'Student' : 'System')}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">${log.AdminName ? 'Admin Name' : (log.StudentFirstName ? 'Student Name' : 'User')}</label>
                    <p class="fw-semibold">${log.AdminName || (log.StudentFirstName ? log.StudentFirstName + ' ' + (log.StudentLastName || '') : 'System')}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Action Type</label>
                    <p><span class="badge bg-${log.Status || 'secondary'}">${log.Action || 'N/A'}</span></p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Category</label>
                    <p><span class="badge bg-${log.Status || 'secondary'}">${log.Category || 'N/A'}</span></p>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">Status</label>
                    <p><span class="badge bg-${log.Status == 'danger' ? 'danger' : (log.Status == 'warning' ? 'warning' : (log.Status == 'success' ? 'success' : 'secondary'))}">${log.Status || 'N/A'}</span></p>
                </div>
                <div class="col-12 mb-3">
                    <label class="text-muted small">Description</label>
                    <p class="fw-semibold">${log.Description || 'No description'}</p>
                </div>
        `;
        
        if (log.StudentFirstName || log.StudentLastName) {
            html += `
                <div class="col-12 mb-3">
                    <label class="text-muted small">Target Student</label>
                    <p>${log.StudentFirstName || ''} ${log.StudentLastName || ''} ${log.StudentEmail ? '(' + log.StudentEmail + ')' : ''}</p>
                </div>
            `;
        }
        
        html += `
                <div class="col-12 mb-3">
                    <label class="text-muted small">Additional Details</label>
                    <div class="bg-light p-3 rounded">
                        <pre class="mb-0"><code>${JSON.stringify({
                            id: log.Id,
                            type: log.LogType,
                            target_id: log.TargetId,
                            status: log.StudentStatus || log.Status
                        }, null, 2)}</code></pre>
                    </div>
                </div>
            </div>
        `;
        
        modalBody.innerHTML = html;
        modal.show();
    } catch (error) {
        console.error('Error viewing log details:', error);
        alert('Could not load log details. Please try again.');
    }
}

// Initialize all event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Attach click handlers to all view details buttons
    const viewButtons = document.querySelectorAll('.view-log-details');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const logData = this.getAttribute('data-log');
            if (logData) {
                try {
                    const log = JSON.parse(logData);
                    viewLogDetails(log);
                } catch (e) {
                    console.error('Error parsing log data:', e);
                }
            }
        });
    });

    // Admin filter change
    const adminFilter = document.getElementById('adminFilter');
    if (adminFilter) {
        adminFilter.addEventListener('change', function() {
            document.getElementById('pageInput').value = '1';
            document.getElementById('filterForm').submit();
        });
    }

    // Log type filter change
    const logTypeFilter = document.getElementById('logTypeFilter');
    if (logTypeFilter) {
        logTypeFilter.addEventListener('change', function() {
            document.getElementById('pageInput').value = '1';
            document.getElementById('filterForm').submit();
        });
    }

    // Date filter change
    const dateFilter = document.getElementById('dateFilter');
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            document.getElementById('pageInput').value = '1';
            document.getElementById('filterForm').submit();
        });
    }

    // Search with debounce
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('pageInput').value = '1';
                document.getElementById('filterForm').submit();
            } else {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('pageInput').value = '1';
                    document.getElementById('filterForm').submit();
                }, 1000);
            }
        });
    }

    // Action filter with debounce
    const actionFilter = document.getElementById('actionFilter');
    if (actionFilter) {
        let actionTimeout;
        actionFilter.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('pageInput').value = '1';
                document.getElementById('filterForm').submit();
            } else {
                clearTimeout(actionTimeout);
                actionTimeout = setTimeout(() => {
                    document.getElementById('pageInput').value = '1';
                    document.getElementById('filterForm').submit();
                }, 1000);
            }
        });
    }

    // Setup auto-refresh for recent view
    <?php if($viewMode == 'recent'): ?>
    // Clear any existing interval
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    
    // Set up new interval
    autoRefreshInterval = setInterval(function() {
        if (!document.hidden) {
            refreshLogs();
        }
    }, 60000);
    <?php endif; ?>
});

// Reset filters
function resetFilters() {
    window.location.href = 'logs.php?view=<?php echo $viewMode; ?>';
}

// Remove specific filter
function removeFilter(filter) {
    let url = new URL(window.location.href);
    url.searchParams.delete(filter);
    if (filter === 'search') {
        url.searchParams.delete('search');
    }
    url.searchParams.set('page', '1');
    window.location.href = url.href;
}

// Reset all filters
function resetAllFilters() {
    window.location.href = 'logs.php?view=<?php echo $viewMode; ?>';
}

// Change page
function changePage(page) {
    document.getElementById('pageInput').value = page;
    document.getElementById('filterForm').submit();
}

// Change entries per page
function changeEntries(entries) {
    document.getElementById('entriesInput').value = entries;
    document.getElementById('pageInput').value = '1';
    document.getElementById('filterForm').submit();
}

// Refresh logs
function refreshLogs() {
    location.reload();
}

// Clear interval when navigating away
window.addEventListener('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>

<?php
    // Include footer
    include('../footer/footer.php');
?>