<?php
// export_logs.php
require_once('../../db-connect.php');

// Get filter parameters
$adminFilter = isset($_POST['admin']) ? $_POST['admin'] : (isset($_GET['admin']) ? $_GET['admin'] : '');
$actionFilter = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$dateFilter = isset($_POST['date']) ? $_POST['date'] : (isset($_GET['date']) ? $_GET['date'] : '');
$viewMode = isset($_POST['view']) ? $_POST['view'] : (isset($_GET['view']) ? $_GET['view'] : 'recent');
$logType = isset($_POST['log_type']) ? $_POST['log_type'] : (isset($_GET['log_type']) ? $_GET['log_type'] : 'all');

// Build the query for combined activity logs
$query = "
    SELECT * FROM (
        -- Document approval logs
        SELECT 
            'Document Approval' as ActivityType,
            CASE 
                WHEN da.PSAApproved = 1 THEN 'PSA Approved'
                WHEN da.TORApproved = 1 THEN 'TOR Approved'
                WHEN da.DiplomaApproved = 1 THEN 'Diploma Approved'
                WHEN da.MarriageApproved = 1 THEN 'Marriage Approved'
                ELSE 'Document Approved'
            END as Action,
            CONCAT(
                'Document approval for ',
                COALESCE(si.FirstName, 'Student'), ' ', COALESCE(si.LastName, ''),
                ' (ID: ', da.StudentInfoId, ') - ',
                CASE 
                    WHEN da.PSAApproved = 1 THEN 'PSA '
                    WHEN da.TORApproved = 1 THEN 'TOR '
                    WHEN da.DiplomaApproved = 1 THEN 'Diploma '
                    WHEN da.MarriageApproved = 1 THEN 'Marriage '
                    ELSE ''
                END,
                'approved'
            ) as Description,
            da.ApprovedDate as DateTime,
            COALESCE(da.ApprovedBy, 'System') as PerformedBy,
            'Student' as TargetType,
            CONCAT(si.FirstName, ' ', si.LastName) as TargetName,
            si.Email as TargetEmail,
            si.Status as TargetStatus
        FROM document_approvals da
        LEFT JOIN studentinfos si ON da.StudentInfoId = si.Id
        WHERE da.ApprovedDate IS NOT NULL
        
        UNION ALL
        
        -- Student login attempts (from userlogins)
        SELECT 
            'Student Login' as ActivityType,
            'Login' as Action,
            CONCAT(
                'Student ',
                COALESCE(si.FirstName, 'Unknown'), ' ', COALESCE(si.LastName, ''),
                ' (ID: ', ul.UserId, ') logged in'
            ) as Description,
            NOW() as DateTime,
            'System' as PerformedBy,
            'Student' as TargetType,
            CONCAT(si.FirstName, ' ', si.LastName) as TargetName,
            si.Email as TargetEmail,
            si.Status as TargetStatus
        FROM userlogins ul
        LEFT JOIN studentinfos si ON ul.UserId = si.Id
        
        UNION ALL
        
        -- Student status changes
        SELECT 
            'Student Status Change' as ActivityType,
            CONCAT('Status: ', si.Status) as Action,
            CONCAT(
                'Student ', si.FirstName, ' ', si.LastName,
                ' (', si.Email, ') status changed to ', si.Status
            ) as Description,
            si.UpdatedAt as DateTime,
            'System' as PerformedBy,
            'Student' as TargetType,
            CONCAT(si.FirstName, ' ', si.LastName) as TargetName,
            si.Email as TargetEmail,
            si.Status as TargetStatus
        FROM studentinfos si
        WHERE si.UpdatedAt IS NOT NULL 
        AND si.UpdatedAt != si.EntryDate
        
        UNION ALL
        
        -- Student registrations
        SELECT 
            'Student Registration' as ActivityType,
            'Registered' as Action,
            CONCAT(
                'New student registered: ', si.FirstName, ' ', si.LastName,
                ' (', si.Email, ')'
            ) as Description,
            si.EntryDate as DateTime,
            'System' as PerformedBy,
            'Student' as TargetType,
            CONCAT(si.FirstName, ' ', si.LastName) as TargetName,
            si.Email as TargetEmail,
            si.Status as TargetStatus
        FROM studentinfos si
        
        UNION ALL
        
        -- Admin account changes
        SELECT 
            'Admin Action' as ActivityType,
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
            a.UpdatedAt as DateTime,
            a.Username as PerformedBy,
            'Admin' as TargetType,
            CONCAT(a.FirstName, ' ', a.LastName) as TargetName,
            a.Email as TargetEmail,
            a.Status as TargetStatus
        FROM admins a
        WHERE a.UpdatedAt IS NOT NULL
        
        UNION ALL
        
        -- Admin creations
        SELECT 
            'Admin Creation' as ActivityType,
            'Admin Created' as Action,
            CONCAT(
                'New admin account created: ', a.Username, ' (', a.FirstName, ' ', a.LastName, ')',
                ' - Role: ', a.Role
            ) as Description,
            a.CreatedAt as DateTime,
            a.Username as PerformedBy,
            'Admin' as TargetType,
            CONCAT(a.FirstName, ' ', a.LastName) as TargetName,
            a.Email as TargetEmail,
            a.Status as TargetStatus
        FROM admins a
        WHERE a.CreatedAt IS NOT NULL
        
        UNION ALL
        
        -- Admin logins
        SELECT 
            'Admin Login' as ActivityType,
            'Login' as Action,
            CONCAT(
                'Admin ', a.Username, ' (', a.FirstName, ' ', a.LastName, ') logged in'
            ) as Description,
            a.LastLogin as DateTime,
            a.Username as PerformedBy,
            'Admin' as TargetType,
            CONCAT(a.FirstName, ' ', a.LastName) as TargetName,
            a.Email as TargetEmail,
            a.Status as TargetStatus
        FROM admins a
        WHERE a.LastLogin IS NOT NULL
    ) combined_logs
    WHERE 1=1
";

// Apply filters
if ($logType != 'all') {
    switch($logType) {
        case 'approvals':
            $query .= " AND ActivityType = 'Document Approval'";
            break;
        case 'admin_changes':
            $query .= " AND ActivityType IN ('Admin Action', 'Admin Creation', 'Admin Login')";
            break;
        case 'student_logins':
            $query .= " AND ActivityType = 'Student Login'";
            break;
        case 'student_changes':
            $query .= " AND ActivityType IN ('Student Status Change', 'Student Registration')";
            break;
    }
}

if ($viewMode === 'recent') {
    $query .= " AND DateTime >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
} elseif ($viewMode === 'archived') {
    $query .= " AND DateTime < DATE_SUB(NOW(), INTERVAL 30 DAY)";
}

if (!empty($actionFilter)) {
    $query .= " AND Action LIKE '%" . $actionFilter . "%'";
}

if (!empty($adminFilter)) {
    $query .= " AND PerformedBy IN (SELECT Username FROM admins WHERE Id = " . intval($adminFilter) . ")";
}

if (!empty($dateFilter)) {
    switch($dateFilter) {
        case 'today':
            $query .= " AND DATE(DateTime) = CURDATE()";
            break;
        case 'yesterday':
            $query .= " AND DATE(DateTime) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            break;
        case 'week':
            $query .= " AND DateTime >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $query .= " AND DateTime >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
    }
}

$query .= " ORDER BY DateTime DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$logs = $stmt->fetchAll();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="activity_logs_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');
header('Pragma: public');

// Create Excel file with HTML table format
echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo '<style>';
echo 'body { font-family: Arial, sans-serif; }';
echo 'table { border-collapse: collapse; width: 100%; }';
echo 'th { background-color: #4CAF50; color: white; font-weight: bold; text-align: center; padding: 8px; border: 1px solid #ddd; }';
echo 'td { padding: 6px; border: 1px solid #ddd; vertical-align: top; }';
echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
echo 'tr:hover { background-color: #e9e9e9; }';
echo '.header { background-color: #333; color: white; font-size: 16px; font-weight: bold; text-align: center; padding: 10px; border: 1px solid #555; }';
echo '.subheader { background-color: #666; color: white; font-size: 12px; padding: 5px; border: 1px solid #777; }';
echo '.approval-row { background-color: #e8f5e8; }';
echo '.login-row { background-color: #e3f2fd; }';
echo '.admin-row { background-color: #ffebee; }';
echo '.status-row { background-color: #fff3e0; }';
echo '.creation-row { background-color: #f3e5f5; }';
echo '</style>';
echo '</head>';
echo '<body>';

// Title and summary
echo '<table border="1" cellspacing="0" cellpadding="5">';

// Title row
echo '<tr><td colspan="9" class="header">ACTIVITY LOGS EXPORT REPORT</td></tr>';

// Summary row
echo '<tr><td colspan="9" class="subheader">';
echo 'Export Date: ' . date('F d, Y h:i:s A') . '<br>';
echo 'Total Records: ' . count($logs) . '<br>';
echo 'View Mode: ' . ucfirst($viewMode) . '<br>';
echo 'Log Type: ' . ($logType == 'all' ? 'All Activities' : ucfirst(str_replace('_', ' ', $logType))) . '<br>';
if (!empty($dateFilter)) {
    echo 'Date Filter: ' . ucfirst($dateFilter) . '<br>';
}
if (!empty($actionFilter)) {
    echo 'Action Filter: ' . $actionFilter . '<br>';
}
echo '</td></tr>';

// Column headers
echo '<tr>';
echo '<th>No.</th>';
echo '<th>Date & Time</th>';
echo '<th>Activity Type</th>';
echo '<th>Action</th>';
echo '<th>Description</th>';
echo '<th>Performed By</th>';
echo '<th>Target Name</th>';
echo '<th>Target Email</th>';
echo '<th>Target Status</th>';
echo '</tr>';

// Data rows
if (count($logs) > 0) {
    $counter = 1;
    foreach ($logs as $log) {
        // Determine row class based on activity type
        $rowClass = '';
        if ($log['ActivityType'] == 'Document Approval') {
            $rowClass = 'approval-row';
        } elseif ($log['ActivityType'] == 'Student Login') {
            $rowClass = 'login-row';
        } elseif (strpos($log['ActivityType'], 'Admin') !== false) {
            $rowClass = 'admin-row';
        } elseif ($log['ActivityType'] == 'Student Status Change') {
            $rowClass = 'status-row';
        } elseif ($log['ActivityType'] == 'Student Registration') {
            $rowClass = 'creation-row';
        }
        
        echo '<tr class="' . $rowClass . '">';
        echo '<td align="center">' . $counter++ . '</td>';
        echo '<td>' . date('Y-m-d H:i:s', strtotime($log['DateTime'])) . '</td>';
        echo '<td>' . htmlspecialchars($log['ActivityType']) . '</td>';
        echo '<td>' . htmlspecialchars($log['Action']) . '</td>';
        echo '<td>' . htmlspecialchars($log['Description']) . '</td>';
        echo '<td>' . htmlspecialchars($log['PerformedBy']) . '</td>';
        echo '<td>' . htmlspecialchars($log['TargetName'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($log['TargetEmail'] ?? 'N/A') . '</td>';
        echo '<td>' . htmlspecialchars($log['TargetStatus'] ?? 'N/A') . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="9" align="center" style="padding: 20px; color: #999;">No activity logs found matching the criteria.</td></tr>';
}

// Summary footer
echo '<tr><td colspan="9" style="background-color: #f5f5f5; padding: 8px; text-align: center; font-size: 11px; color: #666;">';
echo 'Generated by Enrollment System Activity Monitor | ';
echo 'Report includes ' . count($logs) . ' activity records';
echo '</td></tr>';

echo '</table>';
echo '</body>';
echo '</html>';
exit;
?>