<?php
// Include header
include('../header/header.php');
include('../sidebar/sidebar.php');

// Fix the path to db-connect.php - going up one level from admin-tb5/admin-tb5/logs/ to admin-tb5/
require_once('../../db-connect.php');

// Get filter parameters
$adminFilter = isset($_GET['admin']) ? $_GET['admin'] : '';
$actionFilter = isset($_GET['action']) ? $_GET['action'] : '';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'recent'; // 'recent' or 'archived'
$logType = isset($_GET['log_type']) ? $_GET['log_type'] : 'all'; // 'all', 'admin_changes', 'student_logins', 'student_changes', 'pending'

// Pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$entriesPerPage = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$offset = ($page - 1) * $entriesPerPage;

// Build the base query for combined activity logs using actual table columns
// REMOVED: document_approval, account_deletion, admin_deactivation
$baseQuery = "
    SELECT * FROM (
        -- Student login attempts (from userlogins)
        SELECT 
            CONCAT('LOGIN_', ul.LoginProvider, '_', ul.ProviderKey) as Id,
            'student_login' as LogType,
            ul.UserId as TargetId,
            'Student Login' as Action,
            CONCAT(
                'Student ',
                COALESCE(si.FirstName, 'Unknown'), ' ', COALESCE(si.LastName, ''),
                ' (ID: ', ul.UserId, ') logged in via ', ul.LoginProvider
            ) as Description,
            NOW() as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Student Activity' as Category,
            'info' as Status,
            si.FirstName as StudentFirstName,
            si.LastName as StudentLastName,
            si.Email as StudentEmail,
            si.Status as StudentStatus,
            NULL as IPAddress,
            NULL as SessionId,
            NULL as UserAgent
        FROM userlogins ul
        LEFT JOIN studentinfos si ON ul.UserId = si.Id
        
        UNION ALL
        
        -- Student approvals (when status changes to Approved)
        SELECT 
            CONCAT('APPROVED_', si.Id, '_', UNIX_TIMESTAMP(si.UpdatedAt)) as Id,
            'student_approved' as LogType,
            si.Id as TargetId,
            'Student Approved' as Action,
            CONCAT(
                'Student ', si.FirstName, ' ', si.LastName,
                ' (', si.Email, ') has been APPROVED and can now access the system'
            ) as Description,
            si.UpdatedAt as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Student Approval' as Category,
            'success' as Status,
            si.FirstName as StudentFirstName,
            si.LastName as StudentLastName,
            si.Email as StudentEmail,
            si.Status as StudentStatus,
            NULL as IPAddress,
            NULL as SessionId,
            NULL as UserAgent
        FROM studentinfos si
        WHERE si.Status = 'Approved'
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
                ' (', si.Email, ') is in queue waiting for approval'
            ) as Description,
            si.EntryDate as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Pending Registration' as Category,
            'warning' as Status,
            si.FirstName as StudentFirstName,
            si.LastName as StudentLastName,
            si.Email as StudentEmail,
            si.Status as StudentStatus,
            NULL as IPAddress,
            NULL as SessionId,
            NULL as UserAgent
        FROM studentinfos si
        WHERE si.Status = 'Pending'
        
        UNION ALL
        
        -- Student status changes (other than approval)
        SELECT 
            CONCAT('STATUS_', si.Id, '_', UNIX_TIMESTAMP(si.UpdatedAt)) as Id,
            'student_status' as LogType,
            si.Id as TargetId,
            CONCAT('Status: ', si.Status) as Action,
            CONCAT(
                'Student ', si.FirstName, ' ', si.LastName,
                ' (', si.Email, ') status changed to ', si.Status
            ) as Description,
            si.UpdatedAt as CreatedAt,
            NULL as AdminId,
            NULL as AdminName,
            'Student Status' as Category,
            CASE 
                WHEN si.Status = 'Approved' THEN 'success'
                WHEN si.Status = 'Rejected' THEN 'danger'
                WHEN si.Status = 'Suspended' THEN 'warning'
                ELSE 'secondary'
            END as Status,
            si.FirstName as StudentFirstName,
            si.LastName as StudentLastName,
            si.Email as StudentEmail,
            si.Status as StudentStatus,
            NULL as IPAddress,
            NULL as SessionId,
            NULL as UserAgent
        FROM studentinfos si
        WHERE si.UpdatedAt IS NOT NULL 
        AND si.UpdatedAt != si.EntryDate
        AND si.Status != 'Approved'  -- Exclude approvals (already handled above)
        
        UNION ALL
        
        -- Student registrations/creations (initial entry)
        SELECT 
            CONCAT('STUDENT_', si.Id, '_', UNIX_TIMESTAMP(si.EntryDate)) as Id,
            'student_creation' as LogType,
            si.Id as TargetId,
            'Student Registered' as Action,
            CONCAT(
                'New student registered: ', si.FirstName, ' ', si.LastName,
                ' (', si.Email, ') - Status: ', si.Status
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
            si.Status as StudentStatus,
            NULL as IPAddress,
            NULL as SessionId,
            NULL as UserAgent
        FROM studentinfos si
        
        UNION ALL
        
        -- Admin account changes
        SELECT 
            CONCAT('ADMIN_', a.Id, '_', UNIX_TIMESTAMP(a.UpdatedAt)) as Id,
            'admin_change' as LogType,
            a.Id as TargetId,
            CONCAT('Admin ', 
                CASE 
                    WHEN a.CreatedAt = a.UpdatedAt THEN 'Created'
                    ELSE 'Updated'
                END
            ) as Action,
            CONCAT(
                'Admin ', a.Username, ' (', a.FirstName, ' ', a.LastName, ') ',
                CASE 
                    WHEN a.CreatedAt = a.UpdatedAt THEN 'account created'
                    ELSE 'account updated'
                END,
                ' - Role: ', a.Role,
                ', Status: ', a.Status
            ) as Description,
            a.UpdatedAt as CreatedAt,
            a.Id as AdminId,
            a.Username as AdminName,
            'Admin Management' as Category,
            'primary' as Status,
            NULL as StudentFirstName,
            NULL as StudentLastName,
            NULL as StudentEmail,
            NULL as StudentStatus,
            NULL as IPAddress,
            NULL as SessionId,
            NULL as UserAgent
        FROM admins a
        WHERE a.UpdatedAt IS NOT NULL
        
        UNION ALL
        
        -- Admin creations
        SELECT 
            CONCAT('ADMINCREATE_', a.Id, '_', UNIX_TIMESTAMP(a.CreatedAt)) as Id,
            'admin_creation' as LogType,
            a.Id as TargetId,
            'Admin Created' as Action,
            CONCAT(
                'New admin account created: ', a.Username, ' (', a.FirstName, ' ', a.LastName, ')',
                ' - Role: ', a.Role
            ) as Description,
            a.CreatedAt as CreatedAt,
            a.Id as AdminId,
            a.Username as AdminName,
            'Admin Management' as Category,
            'success' as Status,
            NULL as StudentFirstName,
            NULL as StudentLastName,
            NULL as StudentEmail,
            NULL as StudentStatus,
            NULL as IPAddress,
            NULL as SessionId,
            NULL as UserAgent
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
                'Admin ', a.Username, ' (', a.FirstName, ' ', a.LastName, ') logged in'
            ) as Description,
            a.LastLogin as CreatedAt,
            a.Id as AdminId,
            a.Username as AdminName,
            'Admin Login' as Category,
            'info' as Status,
            NULL as StudentFirstName,
            NULL as StudentLastName,
            NULL as StudentEmail,
            NULL as StudentStatus,
            NULL as IPAddress,
            NULL as SessionId,
            NULL as UserAgent
        FROM admins a
        WHERE a.LastLogin IS NOT NULL
    ) combined_logs
    WHERE 1=1
";

// Build filter conditions
$filterConditions = "";

// Apply search filter
if (!empty($searchTerm)) {
    $filterConditions .= " AND (Description LIKE '%$searchTerm%' 
                OR StudentFirstName LIKE '%$searchTerm%' 
                OR StudentLastName LIKE '%$searchTerm%' 
                OR StudentEmail LIKE '%$searchTerm%'
                OR AdminName LIKE '%$searchTerm%')";
}

// Apply log type filter - REMOVED approvals and deletions options
if ($logType != 'all') {
    switch($logType) {
        case 'admin_changes':
            $filterConditions .= " AND LogType IN ('admin_change', 'admin_creation', 'admin_login')";
            break;
        case 'student_logins':
            $filterConditions .= " AND LogType IN ('student_login')";
            break;
        case 'student_changes':
            $filterConditions .= " AND LogType IN ('student_status', 'student_creation', 'student_approved', 'student_pending')";
            break;
        case 'pending':
            $filterConditions .= " AND LogType IN ('student_pending')";
            break;
    }
}

// Apply view mode filter (recent = last 30 days, archived = older than 30 days)
if ($viewMode === 'recent') {
    $filterConditions .= " AND CreatedAt >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
} elseif ($viewMode === 'archived') {
    $filterConditions .= " AND CreatedAt < DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

// Apply action filter
if (!empty($actionFilter)) {
    $filterConditions .= " AND Action LIKE '%$actionFilter%'";
}

// Apply admin filter
if (!empty($adminFilter)) {
    $filterConditions .= " AND AdminId = '$adminFilter'";
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
$countStmt = $pdo->query($countQuery);
$totalEntries = $countStmt->fetch()['total'];
$totalPages = ceil($totalEntries / $entriesPerPage);

// Add ordering and pagination
$fullQuery .= " ORDER BY CreatedAt DESC LIMIT $offset, $entriesPerPage";

// Execute main query
$stmt = $pdo->query($fullQuery);
$logs = $stmt->fetchAll();

// Get counts for recent and archived
$recentCountQuery = $baseQuery . $filterConditions . " AND CreatedAt >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$recentCountStmt = $pdo->query("SELECT COUNT(*) as recent FROM ($recentCountQuery) as recent_table");
$recentCount = $recentCountStmt->fetch()['recent'];

$archivedCountQuery = $baseQuery . $filterConditions . " AND CreatedAt < DATE_SUB(NOW(), INTERVAL 30 DAY)";
$archivedCountStmt = $pdo->query("SELECT COUNT(*) as archived FROM ($archivedCountQuery) as archived_table");
$archivedCount = $archivedCountStmt->fetch()['archived'];

// Get admins for filter dropdown
$adminsStmt = $pdo->query("SELECT Id, Username, FirstName, LastName, Email FROM admins WHERE Status = 'Active' ORDER BY FirstName, LastName");
$admins = $adminsStmt->fetchAll();

// Get today's logs count
$todayQuery = $baseQuery . $filterConditions . " AND DATE(CreatedAt) = CURDATE()";
$todayCountStmt = $pdo->query("SELECT COUNT(*) as today FROM ($todayQuery) as today_table");
$todayCount = $todayCountStmt->fetch()['today'];

// Get statistics - REMOVED document_approvals and critical actions
$statsQuery = "
    SELECT 
        (SELECT COUNT(*) FROM studentinfos WHERE UpdatedAt >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND Status = 'Approved' AND UpdatedAt != EntryDate) as recent_approvals_count,
        (SELECT COUNT(*) FROM studentinfos WHERE Status = 'Approved' AND EntryDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as recent_approved_students,
        (SELECT COUNT(*) FROM studentinfos WHERE Status = 'Pending') as pending_students,
        (SELECT COUNT(*) FROM studentinfos WHERE Status = 'Pending' AND EntryDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as recent_pending_students,
        (SELECT COUNT(*) FROM studentinfos WHERE Status = 'Approved') as total_approved_students,
        (SELECT COUNT(*) FROM admins WHERE Status = 'Active') as active_admins,
        (SELECT COUNT(*) FROM studentinfos) as total_students
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
.view-toggle {
    display: flex;
    gap: 0.5rem;
}

.view-toggle .btn {
    padding: 0.4rem 1rem;
    font-size: 0.9rem;
}

.view-toggle .btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
}

.view-toggle .btn:not(.active) {
    background: white;
    color: #6c757d;
    border-color: #dee2e6;
}

.view-toggle .btn:not(.active):hover {
    background: #f8f9fa;
}

/* Pagination */
.pagination .page-link {
    color: #667eea;
    border: none;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 0.25rem;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background: transparent;
}

/* Modal */
.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
}

.modal-body pre {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    font-size: 0.85rem;
}
</style>

<div class="content-wrapper">
    <div class="main-content">
        <!-- Page Title Card -->
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold text-white mb-2">
                            <i class="bi bi-clock-history me-2"></i>Activity Logs
                            <span class="real-time-indicator ms-2"></span>
                        </h2>
                        <p class="text-white-50 mb-0">
                            Track and monitor student registrations, approvals, logins, and admin activities
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                            <button class="btn btn-light btn-sm" onclick="exportLogs()">
                                <i class="bi bi-download me-1"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards - UPDATED -->
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
                                <small class="text-muted">All time</small>
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
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-person-check text-info" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">New Students</h6>
                                <h3 class="mb-0 fw-bold"><?php echo number_format($stats['recent_approved_students'] ?? 0); ?></h3>
                                <small class="text-muted">Approved last 30 days</small>
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
                                <i class="bi bi-shield-check text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Active Admins</h6>
                                <h3 class="mb-0 fw-bold"><?php echo number_format($stats['active_admins'] ?? 0); ?></h3>
                                <small class="text-muted">Currently active</small>
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

        <!-- Filters Section -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="" id="filterForm">
                    <input type="hidden" name="view" value="<?php echo $viewMode; ?>">
                    <input type="hidden" name="page" value="1" id="pageInput">
                    <input type="hidden" name="entries" value="<?php echo $entriesPerPage; ?>" id="entriesInput">
                    
                    <div class="row g-3">
                        <!-- Search Bar -->
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" name="search" id="searchInput" placeholder="Search logs..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                            </div>
                        </div>
                        
                        <!-- Admin Filter -->
                        <div class="col-md-2">
                            <select class="form-select" name="admin" id="adminFilter">
                                <option value="">All Admins</option>
                                <?php foreach($admins as $admin): ?>
                                <option value="<?php echo $admin['Id']; ?>" <?php echo $adminFilter == $admin['Id'] ? 'selected' : ''; ?>>
                                    <?php echo $admin['FirstName'] . ' ' . $admin['LastName']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Log Type Filter - UPDATED: Removed approvals and deletions -->
                        <div class="col-md-2">
                            <select class="form-select" name="log_type" id="logTypeFilter">
                                <option value="all" <?php echo $logType == 'all' ? 'selected' : ''; ?>>All Activities</option>
                                <option value="student_logins" <?php echo $logType == 'student_logins' ? 'selected' : ''; ?>>Student Logins</option>
                                <option value="student_changes" <?php echo $logType == 'student_changes' ? 'selected' : ''; ?>>Student Changes</option>
                                <option value="admin_changes" <?php echo $logType == 'admin_changes' ? 'selected' : ''; ?>>Admin Actions</option>
                                <option value="pending" <?php echo $logType == 'pending' ? 'selected' : ''; ?>>Pending Queue</option>
                            </select>
                        </div>
                        
                        <!-- Date Filter -->
                        <div class="col-md-2">
                            <select class="form-select" name="date" id="dateFilter">
                                <option value="">All Time</option>
                                <option value="today" <?php echo $dateFilter == 'today' ? 'selected' : ''; ?>>Today</option>
                                <option value="yesterday" <?php echo $dateFilter == 'yesterday' ? 'selected' : ''; ?>>Yesterday</option>
                                <option value="week" <?php echo $dateFilter == 'week' ? 'selected' : ''; ?>>This Week</option>
                                <option value="month" <?php echo $dateFilter == 'month' ? 'selected' : ''; ?>>This Month</option>
                            </select>
                        </div>
                        
                        <!-- Reset Button -->
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Stats Row - UPDATED -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="badge bg-success p-2">
                        <i class="bi bi-person-check me-1"></i> Approved Students: <?php echo number_format($stats['total_approved_students'] ?? 0); ?>
                    </span>
                    <span class="badge bg-warning p-2">
                        <i class="bi bi-person-fill me-1"></i> Pending: <?php echo number_format($stats['pending_students'] ?? 0); ?>
                    </span>
                    <span class="badge bg-info p-2">
                        <i class="bi bi-people me-1"></i> Total Students: <?php echo number_format($stats['total_students'] ?? 0); ?>
                    </span>
                    <span class="badge bg-primary p-2">
                        <i class="bi bi-shield me-1"></i> Active Admins: <?php echo number_format($stats['active_admins'] ?? 0); ?>
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
                                    
                                    if (strpos($actionLower, 'approve') !== false || strpos($actionLower, 'approved') !== false) {
                                        $badgeClass = 'success';
                                        $icon = 'check-circle';
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
                                                <i class="bi bi-shield-fill-check text-danger"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">
                                                    <?php 
                                                    if ($log['AdminName']) {
                                                        echo htmlspecialchars($log['AdminName']);
                                                    } elseif ($log['StudentFirstName']) {
                                                        echo htmlspecialchars($log['StudentFirstName'] . ' ' . $log['StudentLastName']);
                                                    } else {
                                                        echo 'System';
                                                    }
                                                    ?>
                                                </div>
                                                <?php if($log['AdminName']): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($log['AdminName']); ?></small>
                                                <?php endif; ?>
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
                                        <button class="btn btn-sm btn-outline-info" onclick='viewLogDetails(<?php echo json_encode($log); ?>)'>
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
// Auto-submit form when filters change
document.getElementById('adminFilter').addEventListener('change', function() {
    document.getElementById('pageInput').value = '1';
    document.getElementById('filterForm').submit();
});

document.getElementById('logTypeFilter').addEventListener('change', function() {
    document.getElementById('pageInput').value = '1';
    document.getElementById('filterForm').submit();
});

document.getElementById('dateFilter').addEventListener('change', function() {
    document.getElementById('pageInput').value = '1';
    document.getElementById('filterForm').submit();
});

// Search with debounce
let searchTimeout;
document.getElementById('searchInput').addEventListener('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('pageInput').value = '1';
        document.getElementById('filterForm').submit();
    }, 500);
});

// Reset filters
document.getElementById('resetFilters').addEventListener('click', function() {
    window.location.href = 'logs.php?view=<?php echo $viewMode; ?>';
});

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

// View log details
function viewLogDetails(log) {
    const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
    const modalBody = document.getElementById('logDetailsContent');
    
    let html = `
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Log ID</label>
                <p class="fw-semibold">#${log.Id}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Timestamp</label>
                <p class="fw-semibold">${new Date(log.CreatedAt).toLocaleString()}</p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small">${log.AdminName ? 'Admin' : 'User'}</label>
                <p class="fw-semibold">${log.AdminName || (log.StudentFirstName + ' ' + log.StudentLastName) || 'System'}</p>
                ${log.AdminName ? '<small class="text-muted">' + log.AdminName + '</small>' : ''}
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Action Type</label>
                <p><span class="badge bg-${log.Status}">${log.Action}</span></p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Category</label>
                <p><span class="badge bg-${log.Status}">${log.Category}</span></p>
            </div>
            <div class="col-md-6 mb-3">
                <label class="text-muted small">Status</label>
                <p><span class="badge bg-${log.Status == 'danger' ? 'danger' : (log.Status == 'warning' ? 'warning' : (log.Status == 'success' ? 'success' : 'secondary'))}">${log.Status}</span></p>
            </div>
            <div class="col-12 mb-3">
                <label class="text-muted small">Description</label>
                <p class="fw-semibold">${log.Description}</p>
            </div>
    `;
    
    if (log.StudentFirstName || log.StudentLastName) {
        html += `
            <div class="col-12 mb-3">
                <label class="text-muted small">Target Student</label>
                <p>${log.StudentFirstName || ''} ${log.StudentLastName || ''} (${log.StudentEmail || 'No email'})</p>
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
}

// Export logs
function exportLogs() {
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'excel');
    window.location.href = 'export_logs.php?' + params.toString();
}

// Auto-refresh every 60 seconds for real-time updates
<?php if($viewMode == 'recent'): ?>
let refreshInterval = setInterval(function() {
    if (!document.hidden) {
        refreshLogs();
    }
}, 60000);

// Clear interval when navigating away
window.addEventListener('beforeunload', function() {
    clearInterval(refreshInterval);
});
<?php endif; ?>
</script>

<?php
    // Include footer
    include('../footer/footer.php');
?>