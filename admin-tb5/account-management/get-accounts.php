<?php
// filepath: c:\laragon\www\admin-tb5\admin-tb5\account-management\get-accounts.php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');

checkAdmin();

header('Content-Type: application/json');

try {
    // DataTables parameters
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    $orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
    $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';
    
    // Custom filters
    $roleFilter = isset($_POST['roleFilter']) ? $_POST['roleFilter'] : '';
    $statusFilter = isset($_POST['statusFilter']) ? $_POST['statusFilter'] : '';
    
    // Column mapping
    $columns = ['Id', 'FullName', 'Email', 'AccountType', 'Status', 'LastLogin'];
    $orderColumn = $columns[$orderColumnIndex] ?? 'Id';
    
    // Build WHERE conditions
    $whereConditions = [];
    $params = [];
    
    // Search filter
    if (!empty($searchValue)) {
        $whereConditions[] = "(CONCAT(FirstName, ' ', LastName) LIKE ? OR Email LIKE ? OR Id LIKE ?)";
        $searchTerm = "%$searchValue%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    // Status filter
    if (!empty($statusFilter)) {
        $whereConditions[] = "Status = ?";
        $params[] = ucfirst($statusFilter);
    }
    
    $whereClause = !empty($whereConditions) ? " WHERE " . implode(" AND ", $whereConditions) : "";
    
    // Admins query
    $adminSql = "SELECT 
        Id, 
        CONCAT(FirstName, ' ', LastName) as FullName,
        Email,
        Role,
        Status,
        LastLogin,
        CreatedAt,
        'admin' as AccountType
    FROM admins" . $whereClause;
    
    // Skip admins if filtering for students only
    if ($roleFilter === 'student') {
        $adminSql = "SELECT * FROM (SELECT NULL as Id) as dummy WHERE 1=0";
        $params = [];
    }
    
    // Students query
    $studentParams = $params;
    $studentSql = "SELECT 
        Id,
        CONCAT(FirstName, ' ', LastName) as FullName,
        Email,
        Role,
        Status,
        LastLogin,
        EntryDate as CreatedAt,
        'student' as AccountType
    FROM studentinfos" . $whereClause;
    
    // Skip students if filtering for admins only
    if ($roleFilter === 'admin') {
        $studentSql = "SELECT * FROM (SELECT NULL as Id) as dummy WHERE 1=0";
        $studentParams = [];
    }
    
    // Combined query for total count
    $countSql = "SELECT COUNT(*) as total FROM (
        ($adminSql) UNION ALL ($studentSql)
    ) as combined";
    
    $countParams = array_merge($params, $studentParams);
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Combined query with pagination
    $dataSql = "SELECT * FROM (
        ($adminSql) UNION ALL ($studentSql)
    ) as combined 
    ORDER BY $orderColumn $orderDir 
    LIMIT ? OFFSET ?";
    
    $dataParams = array_merge($params, $studentParams, [$length, $start]);
    $dataStmt = $pdo->prepare($dataSql);
    $dataStmt->execute($dataParams);
    $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Response
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalRecords,
        'data' => $data
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>