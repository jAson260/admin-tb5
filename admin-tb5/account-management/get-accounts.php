<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\get-accounts.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

header('Content-Type: application/json');

// Check admin access
checkAdmin();

try {
    // Get DataTable parameters
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    $orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
    $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';
    
    // Get filter values
    $roleFilter = isset($_POST['roleFilter']) ? $_POST['roleFilter'] : '';
    $statusFilter = isset($_POST['statusFilter']) ? $_POST['statusFilter'] : '';
    
    // Column mapping for ordering
    $columns = ['Id', 'FullName', 'Email', 'AccountType', 'Status', 'LastLogin'];
    $orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'Id';
    
    // Build WHERE conditions
    $whereConditions = [];
    $params = [];
    
    // Search filter
    if (!empty($searchValue)) {
        $whereConditions[] = "(CONCAT(FirstName, ' ', LastName) LIKE ? OR Email LIKE ?)";
        $params[] = "%$searchValue%";
        $params[] = "%$searchValue%";
    }
    
    // Status filter (only for students, admins don't have Status in the same enum)
    if (!empty($statusFilter) && $roleFilter !== 'admin') {
        $whereConditions[] = "Status = ?";
        $params[] = ucfirst($statusFilter);
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    // Determine which query to use based on role filter
    if ($roleFilter === 'admin') {
        // Only admins
        $dataQuery = "
            SELECT 
                Id,
                CONCAT(FirstName, ' ', LastName) as FullName,
                Email,
                'admin' as AccountType,
                Role,
                Status,
                LastLogin,
                CreatedAt
            FROM admins
            $whereClause
            ORDER BY $orderColumn $orderDir
            LIMIT $start, $length
        ";
        
        $countQuery = "
            SELECT COUNT(*) as total 
            FROM admins 
            $whereClause
        ";
        
        $totalQuery = "SELECT COUNT(*) as total FROM admins";
        
    } elseif ($roleFilter === 'student') {
        // Only students
        $dataQuery = "
            SELECT 
                Id,
                CONCAT(FirstName, ' ', LastName) as FullName,
                Email,
                'student' as AccountType,
                Role,
                Status,
                LastLogin,
                EntryDate as CreatedAt
            FROM studentinfos
            $whereClause
            ORDER BY $orderColumn $orderDir
            LIMIT $start, $length
        ";
        
        $countQuery = "
            SELECT COUNT(*) as total 
            FROM studentinfos 
            $whereClause
        ";
        
        $totalQuery = "SELECT COUNT(*) as total FROM studentinfos";
        
    } else {
        // Both admins and students (UNION)
        $dataQuery = "
            SELECT 
                Id,
                CONCAT(FirstName, ' ', LastName) as FullName,
                Email,
                'admin' as AccountType,
                Role,
                Status,
                LastLogin,
                CreatedAt
            FROM admins
            " . ($whereClause ? $whereClause : "") . "
            
            UNION ALL
            
            SELECT 
                Id,
                CONCAT(FirstName, ' ', LastName) as FullName,
                Email,
                'student' as AccountType,
                Role,
                Status,
                LastLogin,
                EntryDate as CreatedAt
            FROM studentinfos
            " . ($whereClause ? $whereClause : "") . "
            
            ORDER BY $orderColumn $orderDir
            LIMIT $start, $length
        ";
        
        // For UNION, we need to duplicate params for both queries
        $unionParams = array_merge($params, $params);
        
        $countQuery = "
            SELECT COUNT(*) as total FROM (
                SELECT Id FROM admins $whereClause
                UNION ALL
                SELECT Id FROM studentinfos $whereClause
            ) as combined
        ";
        
        $totalQuery = "
            SELECT 
                (SELECT COUNT(*) FROM admins) + 
                (SELECT COUNT(*) FROM studentinfos) as total
        ";
        
        $params = $unionParams; // Use duplicated params for UNION
    }
    
    // Execute queries
    $stmt = $pdo->prepare($dataQuery);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get filtered count
    $stmtCount = $pdo->prepare($countQuery);
    $stmtCount->execute($params);
    $recordsFiltered = $stmtCount->fetch()['total'];
    
    // Get total count
    $stmtTotal = $pdo->query($totalQuery);
    $recordsTotal = $stmtTotal->fetch()['total'];
    
    // Return DataTable response
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => intval($recordsTotal),
        'recordsFiltered' => intval($recordsFiltered),
        'data' => $data
    ]);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Error: ' . $e->getMessage()
    ]);
}
?>